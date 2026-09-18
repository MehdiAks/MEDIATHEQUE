<?php
/** CRUD d'administration de la médiathèque. Les identifiants SQL sont exclusivement définis ici. */

function backend_entities()
{
    return array(
        'groupes' => array('table'=>'GROUPE','title'=>'Groupes','pk'=>array('idGp'),'fields'=>array(
            'idGp'=>array('label'=>'Identifiant','type'=>'integer','generated'=>true),
            'nomGp'=>array('label'=>'Nom','type'=>'text','max'=>50,'required'=>true),
            'dtCreaGp'=>array('label'=>'Date de création','type'=>'date','required'=>true)),
            'list'=>'SELECT idGp, nomGp, dtCreaGp FROM GROUPE ORDER BY nomGp'),
        'artistes' => array('table'=>'ARTISTE','title'=>'Artistes','pk'=>array('idArt'),'fields'=>array(
            'idArt'=>array('label'=>'Identifiant','type'=>'integer','generated'=>true),
            'idGp'=>array('label'=>'Groupe','type'=>'integer','required'=>true,'fk'=>array('GROUPE','idGp','nomGp')),
            'nomArt'=>array('label'=>'Nom','type'=>'text','max'=>50,'required'=>true),
            'prenomArt'=>array('label'=>'Prénom','type'=>'text','max'=>50,'required'=>true)),
            'list'=>'SELECT a.idArt, g.nomGp AS groupe, a.nomArt, a.prenomArt FROM ARTISTE a JOIN GROUPE g ON g.idGp=a.idGp ORDER BY a.nomArt, a.prenomArt'),
        'albums' => array('table'=>'ALBUM','title'=>'Albums','pk'=>array('idAlb'),'fields'=>array(
            'idAlb'=>array('label'=>'Identifiant','type'=>'integer','generated'=>true),
            'idArt'=>array('label'=>'Artiste','type'=>'integer','required'=>true,'fk'=>array('ARTISTE','idArt',"CONCAT(prenomArt, ' ', nomArt)")),
            'idGp'=>array('label'=>'Groupe','type'=>'integer','required'=>true,'fk'=>array('GROUPE','idGp','nomGp')),
            'nomA'=>array('label'=>'Nom','type'=>'text','max'=>70,'required'=>true),
            'dtSortieA'=>array('label'=>'Date de sortie','type'=>'date','required'=>true),
            'nomLabelA'=>array('label'=>'Label','type'=>'text','max'=>90,'required'=>true)),
            'list'=>'SELECT a.idAlb, CONCAT(ar.prenomArt, \' \', ar.nomArt) AS artiste, g.nomGp AS groupe, a.nomA, a.dtSortieA, a.nomLabelA FROM ALBUM a JOIN ARTISTE ar ON ar.idArt=a.idArt JOIN GROUPE g ON g.idGp=a.idGp ORDER BY a.nomA'),
        'titres' => array('table'=>'TITRE','title'=>'Titres','pk'=>array('idTit'),'fields'=>array(
            'idTit'=>array('label'=>'Identifiant','type'=>'integer','generated'=>true),
            'idAlb'=>array('label'=>'Album','type'=>'integer','required'=>true,'fk'=>array('ALBUM','idAlb','nomA')),
            'nomTit'=>array('label'=>'Titre','type'=>'text','max'=>70,'required'=>true),
            'dureeTit'=>array('label'=>'Durée (minutes)','type'=>'number','min'=>0.01,'required'=>true)),
            'list'=>'SELECT t.idTit, a.nomA AS album, t.nomTit, t.dureeTit FROM TITRE t JOIN ALBUM a ON a.idAlb=t.idAlb ORDER BY a.nomA, t.nomTit'),
        'users' => array('table'=>'USER','title'=>'Utilisateurs','pk'=>array('eMailUser'),'fields'=>array(
            'eMailUser'=>array('label'=>'E-mail','type'=>'email','max'=>50,'required'=>true),
            'nomEUser'=>array('label'=>'Nom','type'=>'text','max'=>50,'required'=>true),
            'prenomUser'=>array('label'=>'Prénom','type'=>'text','max'=>50,'required'=>true)),
            'list'=>'SELECT eMailUser, nomEUser, prenomUser FROM USER ORDER BY nomEUser, prenomUser'),
        'likes' => array('table'=>'LIKES','title'=>'Favoris','pk'=>array('eMailUser','idAlb'),'fields'=>array(
            'eMailUser'=>array('label'=>'Utilisateur','type'=>'email','required'=>true,'fk'=>array('USER','eMailUser',"CONCAT(prenomUser, ' ', nomEUser, ' — ', eMailUser)")),
            'idAlb'=>array('label'=>'Album','type'=>'integer','required'=>true,'fk'=>array('ALBUM','idAlb','nomA'))),
            'list'=>'SELECT l.eMailUser, CONCAT(u.prenomUser, \' \', u.nomEUser) AS utilisateur, l.idAlb, a.nomA AS album FROM LIKES l JOIN USER u ON u.eMailUser=l.eMailUser JOIN ALBUM a ON a.idAlb=l.idAlb ORDER BY u.nomEUser, a.nomA')
    );
}

function backend_h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function backend_db() { global $DB; if (!isset($DB) || !$DB) sql_connect(); return $DB; }
function backend_csrf()
{
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function backend_authenticated()
{
    return !empty($_SESSION['USER_ID']) || !empty($_SESSION['id_user']) || !empty($_SESSION['eMailUser']);
}
function backend_require_auth()
{
    if (!backend_authenticated()) { http_response_code(403); throw new RuntimeException('Vous devez être authentifié pour effectuer cette opération.'); }
}
function backend_verify_csrf()
{
    if (!isset($_POST['csrf_token']) || !hash_equals(backend_csrf(), (string)$_POST['csrf_token']))
        throw new RuntimeException('Le jeton de sécurité est invalide. Rechargez la page.');
}
function backend_key(array $entity, array $source)
{
    $result=array();
    foreach ($entity['pk'] as $key) {
        if (!isset($source[$key]) || $source[$key] === '') throw new InvalidArgumentException('Identifiant manquant ou invalide.');
        $value=trim((string)$source[$key]);
        if ($entity['fields'][$key]['type']==='integer' && filter_var($value,FILTER_VALIDATE_INT)===false) throw new InvalidArgumentException('Identifiant invalide.');
        if ($entity['fields'][$key]['type']==='email' && !filter_var($value,FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Adresse e-mail invalide.');
        $result[$key]=$value;
    }
    return $result;
}
function backend_where(array $key, array &$params, $prefix='key_')
{
    $parts=array(); foreach($key as $column=>$value){$parts[]="$column = :$prefix$column";$params[$prefix.$column]=$value;} return implode(' AND ',$parts);
}
function backend_validate(array $entity, array $source)
{
    $values=array(); $errors=array();
    foreach($entity['fields'] as $name=>$field){
        if(!empty($field['generated'])) continue;
        $value=trim((string)($source[$name]??''));
        if(!empty($field['required']) && $value===''){$errors[]=$field['label'].' est obligatoire.';continue;}
        if(isset($field['max']) && mb_strlen($value,'UTF-8')>$field['max'])$errors[]=$field['label'].' est trop long.';
        if($field['type']==='integer' && filter_var($value,FILTER_VALIDATE_INT)===false)$errors[]=$field['label'].' doit être un identifiant valide.';
        if($field['type']==='number' && (!is_numeric($value)||$value<($field['min']??0)))$errors[]=$field['label'].' est invalide.';
        if($field['type']==='email' && !filter_var($value,FILTER_VALIDATE_EMAIL))$errors[]='L’adresse e-mail est invalide.';
        if($field['type']==='date' && (!DateTime::createFromFormat('Y-m-d',$value) || DateTime::createFromFormat('Y-m-d',$value)->format('Y-m-d')!==$value))$errors[]=$field['label'].' est invalide.';
        $values[$name]=$value;
    }
    if($errors) throw new InvalidArgumentException(implode(' ',array_unique($errors)));
    foreach($entity['fields'] as $name=>$field) if(isset($field['fk'])){
        $fk=$field['fk']; $stmt=backend_db()->prepare("SELECT 1 FROM {$fk[0]} WHERE {$fk[1]} = :value"); $stmt->execute(array('value'=>$values[$name]));
        if(!$stmt->fetchColumn()) throw new InvalidArgumentException($field['label'].' sélectionné(e) n’existe pas.');
    }
    return $values;
}
function backend_error(PDOException $e)
{
    $state=$e->errorInfo[0]??$e->getCode(); $driver=(int)($e->errorInfo[1]??0);
    if($state==='23000' && $driver===1062) return 'Cet enregistrement existe déjà.';
    if($state==='23000') return 'Opération impossible : cet enregistrement est lié à d’autres données.';
    return 'Une erreur de base de données est survenue.';
}
function backend_options(array $field)
{
    $fk=$field['fk']; // Les trois éléments viennent de backend_entities(), jamais de la requête HTTP.
    return backend_db()->query("SELECT {$fk[1]} AS value, {$fk[2]} AS label FROM {$fk[0]} ORDER BY label")->fetchAll();
}
function backend_duplicate(array $entity, array $values, array $excludedKey = array())
{
    // Un doublon métier est une ligne portant exactement les mêmes valeurs éditables.
    $parts=array(); $params=array();
    foreach($values as $column=>$value){$parts[]="$column = :dup_$column";$params['dup_'.$column]=$value;}
    if($excludedKey){$excluded=array();foreach($excludedKey as $column=>$value){$excluded[]="$column = :excluded_$column";$params['excluded_'.$column]=$value;}$parts[]='NOT ('.implode(' AND ',$excluded).')';}
    $statement=backend_db()->prepare('SELECT 1 FROM '.$entity['table'].' WHERE '.implode(' AND ',$parts).' LIMIT 1');
    $statement->execute($params);
    if($statement->fetchColumn())throw new InvalidArgumentException('Cet enregistrement existe déjà.');
}
function backend_redirect($resource){header('Location: list.php');exit;}

function backend_run($resource,$action)
{
    if(session_status()!==PHP_SESSION_ACTIVE)session_start();
    $entities=backend_entities(); if(!isset($entities[$resource])){http_response_code(404);exit;}$entity=$entities[$resource];
    $error='';$values=array();$oldKey=array();
    try {
        if($action==='list') $rows=backend_db()->query($entity['list'])->fetchAll();
        elseif($action==='create'){
            if($_SERVER['REQUEST_METHOD']==='POST'){backend_require_auth();backend_verify_csrf();$values=backend_validate($entity,$_POST);backend_duplicate($entity,$values);$cols=array_keys($values);$marks=array_map(function($c){return ':'.$c;},$cols);$stmt=backend_db()->prepare('INSERT INTO '.$entity['table'].' ('.implode(',',$cols).') VALUES ('.implode(',',$marks).')');$stmt->execute($values);backend_redirect($resource);}
        } elseif($action==='edit'){
            $oldKey=backend_key($entity,$_SERVER['REQUEST_METHOD']==='POST'?array_combine($entity['pk'],array_map(function($k){return $_POST['original_'.$k]??'';},$entity['pk'])):$_GET);
            if($_SERVER['REQUEST_METHOD']==='POST'){backend_require_auth();backend_verify_csrf();$values=backend_validate($entity,$_POST);backend_duplicate($entity,$values,$oldKey);$params=$values;$where=backend_where($oldKey,$params);$sets=array();foreach($values as $c=>$v)$sets[]="$c = :$c";$stmt=backend_db()->prepare('UPDATE '.$entity['table'].' SET '.implode(', ',$sets).' WHERE '.$where);$stmt->execute($params);backend_redirect($resource);}
            $params=array();$where=backend_where($oldKey,$params);$stmt=backend_db()->prepare('SELECT * FROM '.$entity['table'].' WHERE '.$where);$stmt->execute($params);$values=$stmt->fetch();if(!$values)throw new InvalidArgumentException('Enregistrement introuvable.');
        } elseif($action==='delete'){
            if($_SERVER['REQUEST_METHOD']!=='POST')throw new RuntimeException('La suppression doit être envoyée en POST.');
            backend_require_auth();backend_verify_csrf();$oldKey=backend_key($entity,$_POST);$params=array();$where=backend_where($oldKey,$params);$stmt=backend_db()->prepare('DELETE FROM '.$entity['table'].' WHERE '.$where);$stmt->execute($params);backend_redirect($resource);
        }
    } catch(PDOException $e){$error=backend_error($e);} catch(Exception $e){$error=$e->getMessage();}
    require dirname(__DIR__).'/header.php';
    echo '<main class="container py-4"><h1>'.backend_h($entity['title']).'</h1>';
    if($error!=='')echo '<div class="alert alert-danger" role="alert">'.backend_h($error).'</div>';
    if($action==='list') backend_render_list($resource,$entity,$rows??array());
    elseif($action==='create'||$action==='edit') backend_render_form($resource,$entity,$action,$values,$oldKey);
    elseif($action==='delete') echo '<p><a class="btn btn-secondary" href="list.php">Retour à la liste</a></p>';
    echo '</main>'; require dirname(__DIR__).'/footer.php';
}

function backend_render_list($resource,array $entity,array $rows)
{
    echo '<p><a class="btn btn-success" href="create.php">Ajouter</a></p><div class="table-responsive"><table class="table table-striped"><thead><tr>';
    $headers=$rows?array_keys($rows[0]):array_keys($entity['fields']);foreach($headers as $h)echo '<th>'.backend_h($entity['fields'][$h]['label']??ucfirst($h)).'</th>';echo '<th>Actions</th></tr></thead><tbody>';
    foreach($rows as $row){echo '<tr>';foreach($headers as $h)echo '<td>'.backend_h($row[$h]).'</td>';echo '<td class="d-flex gap-2">';$query=http_build_query(array_intersect_key($row,array_flip($entity['pk'])));echo '<a class="btn btn-sm btn-primary" href="edit.php?'.backend_h($query).'">Modifier</a><form method="post" action="delete.php" onsubmit="return confirm(\'Confirmer la suppression ?\');"><input type="hidden" name="csrf_token" value="'.backend_h(backend_csrf()).'">';foreach($entity['pk'] as $key)echo '<input type="hidden" name="'.backend_h($key).'" value="'.backend_h($row[$key]).'">';echo '<button class="btn btn-sm btn-danger" type="submit">Supprimer</button></form></td></tr>';}
    echo '</tbody></table></div>';
}
function backend_render_form($resource,array $entity,$action,array $values,array $oldKey)
{
    echo '<form method="post"><input type="hidden" name="csrf_token" value="'.backend_h(backend_csrf()).'">';
    if($action==='edit')foreach($oldKey as $k=>$v)echo '<input type="hidden" name="original_'.backend_h($k).'" value="'.backend_h($v).'">';
    foreach($entity['fields'] as $name=>$field){if(!empty($field['generated']))continue;$value=$values[$name]??'';echo '<div class="mb-3"><label class="form-label" for="'.backend_h($name).'">'.backend_h($field['label']).'</label>';
        if(isset($field['fk'])){echo '<select class="form-select" id="'.backend_h($name).'" name="'.backend_h($name).'" required><option value="">Sélectionner…</option>';foreach(backend_options($field) as $option)echo '<option value="'.backend_h($option['value']).'"'.((string)$value===(string)$option['value']?' selected':'').'>'.backend_h($option['label']).'</option>';echo '</select>';}
        else {echo '<input class="form-control" id="'.backend_h($name).'" name="'.backend_h($name).'" type="'.backend_h($field['type']).'" value="'.backend_h($value).'"'.(!empty($field['required'])?' required':'').(isset($field['max'])?' maxlength="'.backend_h($field['max']).'"':'').(isset($field['min'])?' min="'.backend_h($field['min']).'" step="any"':'').'>';}
        echo '</div>';}
    echo '<a class="btn btn-secondary" href="list.php">Annuler</a> <button class="btn btn-success" type="submit">'.($action==='create'?'Ajouter':'Enregistrer').'</button></form>';
}
