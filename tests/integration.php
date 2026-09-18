<?php
/** Disposable MySQL database; never writes to the configured database. */
require dirname(__DIR__).'/config.php';
$database='mediatheque_test_'.bin2hex(random_bytes(5));
$connection=db(); $server=null;
function expect($condition,$message): void { if (!$condition) throw new RuntimeException($message); }
$cookie='';
function request(string $path,array $data=[],string $method='GET',bool $json=false): array {
    global $cookie;
    $headers=['Cookie: '.$cookie];
    if ($json) $headers[]='Accept: application/json';
    if ($method==='POST') $headers[]='Content-Type: application/x-www-form-urlencoded';
    $context=stream_context_create(['http'=>['method'=>$method,'header'=>implode("\r\n",$headers),'content'=>http_build_query($data),'ignore_errors'=>true,'follow_location'=>0,'timeout'=>10]]);
    $body=file_get_contents('http://127.0.0.1:18976'.$path,false,$context);
    foreach ($http_response_header as $header) if (preg_match('/^Set-Cookie: ([^;]+)/i',$header,$match)) $cookie=$match[1];
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
    expect(request('/api/users/read.php',[],'GET',true)[0]===403,'Utilisateur standard sans droits admin');
    expect(request('/views/backend/dashboard.php')[0]===403,'Dashboard protégé');
    expect(!preg_match('/PHP (?:Warning|Fatal|Deprecated|Parse error)/',file_get_contents($log)), 'Aucune erreur PHP dans les journaux');
    echo "OK : CRUD des 6 ressources, formulaires, authentification, permissions, CSRF, méthodes HTTP, validation, recherche et favoris.\n";
} finally {
    if (is_resource($server)) { proc_terminate($server); proc_close($server); }
    $connection->exec('DROP DATABASE IF EXISTS `'.$database.'`');
    if (isset($log)) unlink($log);
}
