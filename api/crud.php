<?php
require_once dirname(__DIR__).'/config.php';
$json = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') || str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
$input = $_POST;
try {
    if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
        $input = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($input)) throw new InvalidArgumentException('Objet JSON attendu.');
    }
    $resource = resources()[$entity];
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
            $columns = array_unique(array_merge($resource['keys'],array_keys($resource['fields'])));
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
        if ($action === 'delete') {
            $stmt = db()->prepare('DELETE FROM `'.$resource['table'].'` WHERE '.$where); $stmt->execute($params);
        } else {
            $data = validate_resource($resource,$input);
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
        db()->commit();
        if (isset($result['passwordHash'])) unset($result['passwordHash']);
    }
    if ($json || $action === 'read') {
        http_response_code($action === 'create' ? 201 : 200); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['success'=>true,'data'=>$result ?? null],JSON_UNESCAPED_UNICODE); exit;
    }
    $_SESSION['messages'] = ['Opération effectuée.'];
} catch (Throwable $e) {
    if (isset($DB) && $DB->inTransaction()) $DB->rollBack();
    $status = $e instanceof InvalidArgumentException || $e instanceof JsonException ? 422 : ($e instanceof PDOException ? 409 : ($e->getCode() >= 400 && $e->getCode() <= 599 ? $e->getCode() : 500));
    $message = $e instanceof PDOException ? 'Opération impossible : doublon ou données liées.' : ($status === 500 ? 'Erreur interne.' : $e->getMessage());
    if ($status === 500) error_log((string)$e);
    if ($json || $action === 'read') { http_response_code($status); header('Content-Type: application/json; charset=utf-8'); echo json_encode(['success'=>false,'error'=>$message],JSON_UNESCAPED_UNICODE); exit; }
    if (in_array($status,[401,403,405])) { http_response_code($status); exit(h($message)); }
    $_SESSION['messages'] = [$message];
    unset($input['password']);
    $_SESSION['old'][$entity] = $input;
    $page = ['create'=>'create','update'=>'edit','delete'=>'delete'][$action];
    $keys=[]; foreach ($resource['keys'] as $key) $keys[$key]=$input[($action==='update'?'original_':'').$key] ?? '';
    redirect_to('/views/backend/'.$entity.'/'.$page.'.php?'.http_build_query($keys));
}
redirect_to('/views/backend/'.$entity.'/list.php');
