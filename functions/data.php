<?php
/** Registry shared by the API and administration forms. */
function resources(): array {
    return [
        'groupes' => ['table'=>'GROUPE','label'=>'Groupes','keys'=>['idGp'],'fields'=>['nomGp'=>['Nom','text',50], 'dtCreaGp'=>['Date de création','date']]],
        'artistes' => ['table'=>'ARTISTE','label'=>'Artistes','keys'=>['idArt'],'fields'=>['nomArt'=>['Nom','text',50],'prenomArt'=>['Prénom','text',50],'idGp'=>['Groupe','relation','groupes',true]]],
        'albums' => ['table'=>'ALBUM','label'=>'Albums','keys'=>['idAlb'],'fields'=>['nomA'=>['Nom','text',70],'dtSortieA'=>['Date de sortie','date'],'nomLabelA'=>['Label','text',90],'idArt'=>['Artiste','relation','artistes',true],'idGp'=>['Groupe','relation','groupes',true]]],
        'titres' => ['table'=>'TITRE','label'=>'Titres','keys'=>['idTit'],'fields'=>['nomTit'=>['Nom','text',70],'dureeTit'=>['Durée (secondes)','number'],'idAlb'=>['Album','relation','albums']]],
        'users' => ['table'=>'USER','label'=>'Utilisateurs','keys'=>['eMailUser'],'fields'=>['eMailUser'=>['Adresse e-mail','email',50],'nomEUser'=>['Nom','text',50],'prenomUser'=>['Prénom','text',50]]],
        'likes' => ['table'=>'LIKES','label'=>'Favoris','keys'=>['eMailUser','idAlb'],'fields'=>['eMailUser'=>['Utilisateur','relation','users'],'idAlb'=>['Album','relation','albums']]],
    ];
}
function h($value): string { return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function db(): PDO { global $DB; return $DB ?: sql_connect(); }
function query_rows(string $sql, array $params = []): array {
    $statement = db()->prepare($sql); $statement->execute($params); return $statement->fetchAll();
}
function resource_rows(array $resource): array {
    $columns = array_unique(array_merge($resource['keys'], array_keys($resource['fields'])));
    return query_rows('SELECT `'.implode('`,`', $columns).'` FROM `'.$resource['table'].'` ORDER BY `'.$resource['keys'][0].'`');
}
function resource_key(array $resource, array $input, string $prefix = ''): array {
    $params = []; $where = [];
    foreach ($resource['keys'] as $key) {
        $value = $input[$prefix.$key] ?? null;
        if (!is_scalar($value) || (string)$value === '' || ($key !== 'eMailUser' && !filter_var($value, FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]))) {
            throw new InvalidArgumentException('Identifiant invalide.');
        }
        $params['key_'.$key] = $value; $where[] = '`'.$key.'` = :key_'.$key;
    }
    return [implode(' AND ', $where), $params];
}
function validate_resource(array $resource, array $input): array {
    $data = [];
    foreach ($resource['fields'] as $name => $field) {
        $raw = $input[$name] ?? '';
        if (!is_scalar($raw)) throw new InvalidArgumentException($field[0].' : valeur invalide.');
        $value = trim((string)$raw);
        if ($value === '' && !empty($field[3])) { $data[$name] = null; continue; }
        if ($value === '') throw new InvalidArgumentException($field[0].' est obligatoire.');
        if (isset($field[2]) && is_int($field[2]) && mb_strlen($value) > $field[2]) throw new InvalidArgumentException($field[0].' est trop long.');
        if ($field[1] === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Adresse e-mail invalide.');
        if ($field[1] === 'date') {
            $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
            if (!$date || $date->format('Y-m-d') !== $value) throw new InvalidArgumentException('Date invalide.');
        }
        if ($field[1] === 'number' && (!is_numeric($value) || !is_finite((float)$value) || (float)$value <= 0)) throw new InvalidArgumentException('La durée doit être positive.');
        if ($field[1] === 'relation') {
            $target = resources()[$field[2]];
            [$where, $params] = resource_key($target, [$target['keys'][0]=>$value]);
            if (!query_rows('SELECT 1 FROM `'.$target['table'].'` WHERE '.$where, $params)) throw new InvalidArgumentException($field[0].' introuvable.');
        }
        $data[$name] = $value;
    }
    if ($resource['table'] === 'ALBUM' && empty($data['idArt']) && empty($data['idGp'])) throw new InvalidArgumentException('Sélectionnez un artiste ou un groupe.');
    return $data;
}
function redirect_to(string $path): never { header('Location: '.BASE_URL.$path, true, 303); exit; }
function flashes(): void {
    foreach ($_SESSION['messages'] ?? [] as $message) echo '<div class="alert alert-info" role="status">'.h($message).'</div>';
    unset($_SESSION['messages']);
}
