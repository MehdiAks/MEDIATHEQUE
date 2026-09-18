<?php
require_once dirname(__DIR__).'/config.php';
$json = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
$input = $_POST;
$newImage = null;
$newAudio = null;
$audioToRemove = [];
$imagesToRemove = [];
try {
    if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
        $input = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($input)) throw new InvalidArgumentException('Objet JSON attendu.');
    }
    $resource = resources()[$entity];
    $postLimit = trim(ini_get('post_max_size'));
    $postBytes = (float)$postLimit * (['g'=>1073741824,'m'=>1048576,'k'=>1024][strtolower(substr($postLimit,-1))] ?? 1);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $postBytes > 0 && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > $postBytes) {
        throw new RuntimeException('Envoi trop volumineux pour le serveur. Réduisez la taille des fichiers envoyés.', 413);
    }
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method !== ($action === 'read' ? 'GET' : 'POST')) { header('Allow: '.($action === 'read' ? 'GET' : 'POST')); throw new RuntimeException('Méthode non autorisée.', 405); }
    $user = current_user();
    if (!$user) throw new RuntimeException('Connexion requise.', 401);
    if (!$user['isAdmin']) throw new RuntimeException('Accès réservé à l’administration.', 403);
    if ($action !== 'read') {
        $token = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!is_string($token) || !verify_csrf_token($token)) throw new RuntimeException('Jeton CSRF invalide.', 403);
    }
    if ($action === 'read') {
        if (isset($_GET[$resource['keys'][0]])) {
            [$where,$params] = resource_key($resource, $_GET);
            $columns = array_unique(array_merge($resource['keys'],array_keys($resource['fields']), $resource['readOnly'] ?? []));
            $result = query_rows('SELECT `'.implode('`,`',$columns).'` FROM `'.$resource['table'].'` WHERE '.$where,$params);
            if (!$result) throw new RuntimeException('Élément introuvable.',404);
        } else $result = resource_rows($resource);
    } else {
        db()->beginTransaction();
        if ($action !== 'create') {
            [$where,$params] = resource_key($resource,$input, $action === 'update' ? 'original_' : '');
            $existing = query_rows('SELECT * FROM `'.$resource['table'].'` WHERE '.$where.' FOR UPDATE',$params)[0] ?? null;
            if (!$existing) throw new RuntimeException('Élément introuvable.',404);
            if ($entity === 'users' && $action === 'delete' && $existing['isAdmin']) throw new InvalidArgumentException('Un compte administrateur ne peut pas être supprimé ici.');
        }
        if ($action === 'delete' && in_array($entity, ['albums','artistes','groupes'])) {
            $imageSql = match ($entity) {
                'albums' => 'SELECT imageA FROM ALBUM WHERE idAlb = ?',
                'artistes' => 'SELECT imageA FROM ALBUM WHERE idArt = ?',
                'groupes' => 'SELECT imageA FROM ALBUM WHERE idGp = ? OR idArt IN (SELECT idArt FROM ARTISTE WHERE idGp = ?)',
            };
            $id = $existing[$resource['keys'][0]];
            $imagesToRemove = array_column(query_rows($imageSql, $entity === 'groupes' ? [$id,$id] : [$id]), 'imageA');
        }
        if ($action === 'delete' && in_array($entity,['titres','albums','artistes','groupes'])) {
            $audioSql = match ($entity) {
                'titres'=>'SELECT audioTit FROM TITRE WHERE idTit = ?',
                'albums'=>'SELECT audioTit FROM TITRE WHERE idAlb = ?',
                'artistes'=>'SELECT t.audioTit FROM TITRE t JOIN ALBUM a ON a.idAlb=t.idAlb WHERE a.idArt = ?',
                'groupes'=>'SELECT t.audioTit FROM TITRE t JOIN ALBUM a ON a.idAlb=t.idAlb WHERE a.idGp = ? OR a.idArt IN (SELECT idArt FROM ARTISTE WHERE idGp = ?)',
            };
            $id=$existing[$resource['keys'][0]];
            $audioToRemove=array_column(query_rows($audioSql,$entity==='groupes'?[$id,$id]:[$id]),'audioTit');
        }
        if ($action === 'delete') {
            $stmt = db()->prepare('DELETE FROM `'.$resource['table'].'` WHERE '.$where); $stmt->execute($params);
        } else {
            $data = validate_resource($resource,$input);
            if (in_array($entity,['albums','titres'])) {
                $newAudio=upload_audio();
                if ($entity==='titres') {
                    $data['audioTit']=$newAudio ?? (($input['remove_audio'] ?? '')==='1' ? null : ($existing['audioTit'] ?? null));
                    if (($existing['audioTit'] ?? null)!==$data['audioTit']) $audioToRemove[]=$existing['audioTit'] ?? null;
                }
            }
            if ($entity === 'albums') {
                $newImage = upload_album_image();
                $data['imageA'] = $newImage ?? (($input['remove_image'] ?? '') === '1' ? null : ($existing['imageA'] ?? null));
                if (($existing['imageA'] ?? null) !== $data['imageA']) $imagesToRemove[] = $existing['imageA'] ?? null;
            }
            if ($entity === 'users') {
                $password = $input['password'] ?? '';
                if (!is_string($password) || ($password !== '' && (strlen($password)<12 || strlen($password)>72))) throw new InvalidArgumentException('Le mot de passe doit contenir entre 12 et 72 octets.');
                if ($password !== '') $data['passwordHash'] = password_hash($password,PASSWORD_DEFAULT);
            }
            if ($action === 'create') {
                $stmt = db()->prepare('INSERT INTO `'.$resource['table'].'` (`'.implode('`,`',array_keys($data)).'`) VALUES ('.implode(',',array_fill(0,count($data),'?')).')');
                $stmt->execute(array_values($data));
                $result = $data;
                if (count($resource['keys']) === 1 && $resource['keys'][0] !== 'eMailUser') $result[$resource['keys'][0]] = (int)db()->lastInsertId();
            } else {
                $sets = []; foreach ($data as $key=>$value) $sets[] = '`'.$key.'` = :'.$key;
                $stmt = db()->prepare('UPDATE `'.$resource['table'].'` SET '.implode(',',$sets).' WHERE '.$where); $stmt->execute(array_merge($data,$params));
                if ($entity === 'users' && $existing['eMailUser'] === $user['eMailUser']) $_SESSION['eMailUser'] = $_SESSION['USER_ID'] = $data['eMailUser'];
                $result = $data;
            }
        }
        if ($entity==='albums' && $newAudio && $action!=='delete') {
            $trackData=validate_resource(resources()['titres'],[
                'nomTit'=>$input['audioTitle'] ?? '',
                'dureeTit'=>$input['audioDuration'] ?? '',
                'idAlb'=>$action==='create'?$result['idAlb']:$existing['idAlb'],
            ]);
            $stmt=db()->prepare('INSERT INTO TITRE (nomTit,dureeTit,idAlb,audioTit) VALUES (?,?,?,?)');
            $stmt->execute([$trackData['nomTit'],$trackData['dureeTit'],$trackData['idAlb'],$newAudio]);
        }
        db()->commit();
        $newAudio=null;
        foreach ($audioToRemove as $audioPath) remove_audio($audioPath);
        foreach ($imagesToRemove as $imagePath) remove_album_image($imagePath);
        $newImage = null;
        if (isset($result['passwordHash'])) unset($result['passwordHash']);
    }
    if ($json || $action === 'read') {
        http_response_code($action === 'create' ? 201 : 200); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['success'=>true,'data'=>$result ?? null],JSON_UNESCAPED_UNICODE); exit;
    }
    $_SESSION['messages'] = ['Opération effectuée.'];
} catch (Throwable $e) {
    if ($newAudio) remove_audio($newAudio);
    if ($newImage) remove_album_image($newImage);
    if (isset($DB) && $DB->inTransaction()) $DB->rollBack();
    $status = $e instanceof InvalidArgumentException || $e instanceof JsonException ? 422 : ($e instanceof PDOException ? 409 : ($e->getCode() >= 400 && $e->getCode() <= 599 ? $e->getCode() : 500));
    $message = $e instanceof PDOException ? 'Opération impossible : doublon ou données liées.' : ($status === 500 ? 'Erreur interne.' : $e->getMessage());
    if ($status === 500) error_log((string)$e);
    if ($json || $action === 'read') { http_response_code($status); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['success'=>false,'error'=>$message],JSON_UNESCAPED_UNICODE); exit; }
    if (in_array($status,[401,403,405,413])) { http_response_code($status); exit(h($message)); }
    $_SESSION['messages'] = [$message];
    unset($input['password']);
    $_SESSION['old'][$entity] = $input;
    $page = ['create'=>'create','update'=>'edit','delete'=>'delete'][$action];
    $keys=[]; foreach ($resource['keys'] as $key) $keys[$key]=$input[($action==='update'?'original_':'').$key] ?? '';
    redirect_to('/views/backend/'.$entity.'/'.$page.'.php?'.http_build_query($keys));
}
redirect_to('/views/backend/'.$entity.'/list.php');
