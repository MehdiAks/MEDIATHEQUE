<?php
/** Disposable MySQL database; never writes to the configured database. */
require dirname(__DIR__).'/config.php';
$database='mediatheque_test_'.bin2hex(random_bytes(5));
$connection=db(); $server=null;
function expect($condition,$message): void { if (!$condition) throw new RuntimeException($message); }
$cookie='';
function request(string $path,array $data=[],string $method='GET',bool $json=false, ?string $image=null, string $fileField="imageA"): array {
    global $cookie;
    $headers=['Cookie: '.$cookie];
    if ($json) $headers[]='Accept: application/json';
    $content=http_build_query($data);
    if ($image !== null) {
        $boundary='test'.bin2hex(random_bytes(12)); $content='';
        foreach ($data as $key=>$value) $content.='--'.$boundary."\r\nContent-Disposition: form-data; name=\"".$key."\"\r\n\r\n".$value."\r\n";
        $content.='--'.$boundary."\r\nContent-Disposition: form-data; name=\"".$fileField."\"; filename=\"cover.png\"\r\nContent-Type: image/png\r\n\r\n".$image."\r\n--".$boundary."--\r\n";
        $headers[]='Content-Type: multipart/form-data; boundary='.$boundary;
    } elseif ($method==='POST') $headers[]='Content-Type: application/x-www-form-urlencoded';
    $context=stream_context_create(['http'=>['method'=>$method,'header'=>implode("\r\n",$headers),'content'=>$content,'ignore_errors'=>true,'follow_location'=>0,'timeout'=>10]]);
    $body=file_get_contents('http://127.0.0.1:18976'.$path,false,$context);
    foreach ($http_response_header as $header) if (preg_match('/^Set-Cookie: ([^;]+)/i',$header,$match)) { $parts=explode('=', $match[1],2); $cookies=[]; foreach(explode('; ', $cookie) as $pair) { if(str_contains($pair,'=')) {[$k,$v]=explode('=',$pair,2); $cookies[$k]=$v;} } $cookies[$parts[0]]=$parts[1]; $cookie=implode('; ',array_map(static fn($k,$v)=>$k.'='.$v,array_keys($cookies),$cookies)); }
    preg_match('/\s(\d{3})\s/',$http_response_header[0],$match);
    return [(int)$match[1],$body];
}
function token(string $path): string { [$status,$body]=request($path); expect($status===200,'Page '.$path.' : '.$status); preg_match('/name="csrf_token" value="([^"]+)"/',$body,$match); expect(isset($match[1]),'CSRF absent'); return $match[1]; }
try {
    $connection->exec('CREATE DATABASE `'.$database.'` CHARACTER SET utf8');
    $connection->exec('USE `'.$database.'`');
    $schema=file_get_contents(ROOT.'/BDD/CreateDbMediatheq22.sql');
    $schema=preg_replace('/CREATE DATABASE MEDIATHEQ22.*?;/s','',$schema);
    $schema=str_replace('USE MEDIATHEQ22;','',$schema);
    $connection->exec($schema);
    $connection->exec(file_get_contents(ROOT.'/BDD/002_authentication.sql'));
    $connection->exec(file_get_contents(ROOT.'/BDD/003_album_cover.sql'));
    $connection->exec(file_get_contents(ROOT.'/BDD/004_antispam.sql'));
    $connection->exec(file_get_contents(ROOT.'/BDD/005_track_audio.sql'));
    $connection->prepare('INSERT INTO `USER` (eMailUser,nomEUser,prenomUser,passwordHash,isAdmin) VALUES (?,?,?,?,1)')->execute(['admin@example.test','Admin','Test',password_hash('PasswordTest123!',PASSWORD_DEFAULT)]);
    $env=getenv(); $env['DB_DATABASE']=$database; $env['BASE_URL']='http://127.0.0.1:18976';
    $log=tempnam(sys_get_temp_dir(),'mediatheque-http-');
    $server=proc_open([PHP_BINARY,'-S','127.0.0.1:18976','-t',ROOT,ROOT.'/router.php'],[0=>['pipe','r'],1=>['file',$log,'a'],2=>['file',$log,'a']],$pipes,ROOT,$env);
    for($i=0;$i<30;$i++) { $socket=@fsockopen('127.0.0.1',18976); if($socket){fclose($socket);break;} usleep(100000); }
    expect(request('/.env')[0]===403,'Configuration privée');
    expect(request('/BDD/CreateDbMediatheq22.sql')[0]===403,'Schéma privé');
    expect(request('/api/albums/read.php',[], 'GET',true)[0]===401,'Lecture anonyme refusée');
    $csrf=token('/views/backend/security/login.php');
    expect(request('/api/security/login.php',['csrf_token'=>$csrf,'eMailUser'=>'admin@example.test','password'=>'PasswordTest123!'],'POST')[0]===303,'Connexion admin');
    $csrf=token('/views/backend/groupes/create.php');
    expect(request('/api/groupes/create.php',['nomGp'=>'Test'],'POST',true)[0]===403,'CSRF obligatoire');
    expect(request('/api/groupes/create.php',[],'GET',true)[0]===405,'Méthode obligatoire');
    $fixtures=[
        'groupes'=>['nomGp'=>"Groupe d’essai <script>",'dtCreaGp'=>'2020-01-01'],
        'artistes'=>['nomArt'=>'Artiste','prenomArt'=>'Test','idGp'=>1],
        'albums'=>['nomA'=>'Album unique test','nomLabelA'=>'Label','dtSortieA'=>'2024-01-01','idArt'=>1,'idGp'=>''],
        'titres'=>['nomTit'=>'Piste','dureeTit'=>120,'idAlb'=>1],
        'users'=>['eMailUser'=>'reader@example.test','nomEUser'=>'Lecteur','prenomUser'=>'Test'],
        'likes'=>['eMailUser'=>'reader@example.test','idAlb'=>1],
    ];
    foreach ($fixtures as $entity=>$data) {
        [$status,$body]=request('/api/'.$entity.'/create.php',$data+['csrf_token'=>$csrf],'POST',true); expect($status===201,'Création '.$entity.': '.$body);
        $keys=[]; foreach(resources()[$entity]['keys'] as $key) $keys[$key]=$data[$key] ?? 1;
        expect(request('/api/'.$entity.'/read.php?'.http_build_query($keys),[],'GET',true)[0]===200,'Lecture '.$entity);
        foreach (['list','edit','delete'] as $page) expect(request('/views/backend/'.$entity.'/'.$page.'.php?'.http_build_query($keys))[0]===200,'Vue '.$entity.'/'.$page);
        $update=$data+['csrf_token'=>$csrf]; foreach($keys as $key=>$value) $update['original_'.$key]=$value;
        expect(request('/api/'.$entity.'/update.php',$update,'POST',true)[0]===200,'Modification '.$entity);
    }
    $update=$fixtures['albums']+['original_idAlb'=>1,'csrf_token'=>$csrf];
    $testImage=imagecreatetruecolor(10,10); ob_start(); imagepng($testImage); $imageBytes=ob_get_clean(); imagedestroy($testImage);
    [$status,$body]=request('/api/albums/update.php',$update,'POST',true,$imageBytes);
    expect($status===200,'Upload multipart : '.$body);
    $cover=json_decode($body,true)['data']['imageA'];
    expect(is_file(ROOT.'/'.$cover),'Pochette stockée');
    expect(request('/'.$cover)[0]===200,'Pochette publique');
    expect(str_contains(request('/album.php?id=1')[1],$cover),'Pochette sur fiche');
    expect(str_contains(request('/index.php')[1],$cover),'Pochette catalogue');
    expect(request('/api/albums/update.php',$update,'POST',true,'<?php echo "invalid"; ?>')[0]===422,'Fausse image refusée');
    expect(is_file(ROOT.'/'.$cover),'Ancienne pochette conservée après erreur');
    [$status,$body]=request('/api/albums/update.php',$update,'POST',true,$imageBytes);
    clearstatcache();
    expect($status===200 && !is_file(ROOT.'/'.$cover),'Remplacement nettoie ancienne pochette');
    $cover=json_decode($body,true)['data']['imageA'];
    expect(request('/api/albums/update.php',$update+['remove_image'=>'1'],'POST',true)[0]===200,'Retrait pochette');
    clearstatcache();
    expect(!is_file(ROOT.'/'.$cover),'Fichier retiré');
    $pcm=str_repeat("\x00\x00",8000);
    $wav='RIFF'.pack('V',36+strlen($pcm)).'WAVEfmt '.pack('VvvVVvv',16,1,1,8000,16000,2,16).'data'.pack('V',strlen($pcm)).$pcm;
    $audioData=$fixtures['albums']+['csrf_token'=>$csrf,'audioTitle'=>'Test audio','audioDuration'=>1];
    [$status,$body]=request('/api/albums/create.php',$audioData,'POST',true,$wav,'audioTit');
    expect($status===201,'Album avec audio : '.$body);
    $audioAlbum=json_decode($body,true)['data']['idAlb'];
    $audioRow=$connection->query('SELECT * FROM TITRE WHERE idAlb='.(int)$audioAlbum)->fetch();
    expect($audioRow && is_file(ROOT.'/'.$audioRow['audioTit']),'Piste et fichier créés');
    expect(request('/'.$audioRow['audioTit'])[0]===200,'Audio servi publiquement');
    expect(str_contains(request('/album.php?id='.$audioAlbum)[1],'<audio'),'Lecteur présent');
    $trackUpdate=['csrf_token'=>$csrf,'original_idTit'=>$audioRow['idTit'],'nomTit'=>'Test audio','dureeTit'=>1,'idAlb'=>$audioAlbum];
    expect(request('/api/titres/update.php',$trackUpdate,'POST',true,'<?php echo 1;','audioTit')[0]===422,'Faux audio refusé');
    expect(request('/api/titres/update.php',$trackUpdate+['remove_audio'=>'1'],'POST',true)[0]===200,'Retrait audio');
    clearstatcache(); expect(!is_file(ROOT.'/'.$audioRow['audioTit']),'Fichier retiré');
    [$status,$body]=request('/api/titres/update.php',$trackUpdate,'POST',true,$wav,'audioTit');
    expect($status===200,'Remplacement audio');
    $audioPath=json_decode($body,true)['data']['audioTit'];
    expect(request('/api/albums/delete.php',['csrf_token'=>$csrf,'idAlb'=>$audioAlbum],'POST',true)[0]===200,'Suppression album audio');
    clearstatcache(); expect(!is_file(ROOT.'/'.$audioPath),'Audio supprimé par cascade');
    $before=(int)$connection->query('SELECT COUNT(*) FROM ALBUM')->fetchColumn();
    expect(request('/api/albums/create.php',$fixtures['albums']+['csrf_token'=>$csrf],'POST',true,$wav,'audioTit')[0]===422,'Métadonnées audio obligatoires');
    expect((int)$connection->query('SELECT COUNT(*) FROM ALBUM')->fetchColumn()===$before,'Album annulé si piste invalide');
    expect(request('/api/likes/create.php',$fixtures['likes']+['csrf_token'=>$csrf],'POST',true)[0]===409,'Doublon favori');
    expect(request('/api/titres/create.php',['nomTit'=>'Invalide','dureeTit'=>-1,'idAlb'=>1,'csrf_token'=>$csrf],'POST',true)[0]===422,'Durée invalide');
    expect(request('/api/groupes/create.php',['nomGp'=>'Date','dtCreaGp'=>'2024-02-31','csrf_token'=>$csrf],'POST',true)[0]===422,'Date invalide');
    expect(str_contains(request('/index.php?q=unique')[1],'Album unique test'),'Recherche');
    expect(!str_contains(request('/index.php?q=inexistant')[1],'Album unique test'),'Filtre recherche');
    expect(request('/album.php?id=99999')[0]===404,'Album absent');
    $csrf=token('/album.php?id=1');
    expect(request('/album.php?id=1',['csrf_token'=>$csrf],'POST')[0]===303,'Ajout favori');
    expect(request('/album.php?id=1',['csrf_token'=>$csrf,'action'=>'unlike'],'POST')[0]===303,'Retrait favori');
    foreach (array_reverse($fixtures,true) as $entity=>$data) {
        $keys=['csrf_token'=>$csrf]; foreach(resources()[$entity]['keys'] as $key) $keys[$key]=$data[$key] ?? 1;
        expect(request('/api/'.$entity.'/delete.php',$keys,'POST',true)[0]===200,'Suppression '.$entity);
    }
    request('/api/security/disconnect.php',['csrf_token'=>$csrf],'POST');
    $csrf=token('/views/backend/security/signup.php');
    expect(request('/api/security/signup.php',['csrf_token'=>$csrf,'eMailUser'=>'new@example.test','nomEUser'=>'Nom','prenomUser'=>'Prénom','password'=>'PasswordTest123!','password_confirm'=>'PasswordTest123!','consent'=>'1','isAdmin'=>1],'POST')[0]===303,'Inscription');
    $home=request('/index.php')[1];
    expect(!str_contains($home,'>Admin</a>'),'Lien admin absent pour membre');
    expect(str_contains($home,'Michel.png'),'Michel présent');
    $csrf=token('/views/backend/security/signup.php');
    expect(request('/api/privacy/consent.php',['csrf_token'=>$csrf,'choice'=>'refused'],'POST')[0]===303,'Refus cookies');
    expect(request('/api/privacy/consent.php',['choice'=>'accepted'],'POST')[0]===403,'Consentement protégé');
    expect(!str_contains(request('/index.php')[1],'id="cookie-title"'),'Choix cookies mémorisé');
    expect(str_contains(request('/index.php?cookies=1')[1],'id="cookie-title"'),'Choix cookies modifiable');
    expect(!valid_password('abcdefghijklmnop'),'Mot de passe simple refusé');
    expect(valid_password('PasswordTest123!'),'Mot de passe complexe accepté');

    expect(request('/api/users/read.php',[],'GET',true)[0]===403,'Utilisateur standard sans droits admin');
    expect(request('/views/backend/dashboard.php')[0]===403,'Dashboard protégé');
    expect(!preg_match('/PHP (?:Warning|Fatal|Deprecated|Parse error)/',file_get_contents($log)), 'Aucune erreur PHP dans les journaux');
    [$status,$body]=request('/introuvable-test');
    expect($status===404 && str_contains($body,'Cette page est introuvable'),'404 personnalisée');
    expect(str_contains(request('/index.php')[1],'property="og:image"'),'Métadonnées de partage');
    expect(str_contains(request('/sitemap.php')[1],'<urlset'),'Sitemap XML');
    $csrf=token('/views/backend/security/signup.php');
    $spam=['csrf_token'=>$csrf,'website'=>'https://spam.example','eMailUser'=>'spam@example.test','password'=>'PasswordTest123!'];
    expect(request('/api/security/signup.php',$spam,'POST')[0]===303,'Piège antispam');
    expect(!$connection->query("SELECT 1 FROM USER WHERE eMailUser='spam@example.test'")->fetchColumn(),'Spam non enregistré');
    for($attempt=0;$attempt<6;$attempt++) $last=request('/api/security/signup.php',$spam,'POST');
    expect($last[0]===429,'Limitation des inscriptions répétées');
    echo "OK : CRUD des 6 ressources, formulaires, authentification, permissions, CSRF, méthodes HTTP, validation, recherche et favoris.\n";
} finally {
    foreach ($connection->query('SELECT audioTit FROM TITRE WHERE audioTit IS NOT NULL') as $row) remove_audio($row['audioTit']);
    foreach ($connection->query('SELECT imageA FROM ALBUM WHERE imageA IS NOT NULL') as $row) remove_album_image($row['imageA']);
    if (is_resource($server)) { proc_terminate($server); proc_close($server); }
    $connection->exec('DROP DATABASE IF EXISTS `'.$database.'`');
    if (isset($log)) unlink($log);
}
