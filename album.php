<?php
require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$albumId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);

if ($albumId === false || $albumId === null) {
    http_response_code(400);
    exit('Identifiant d\'album invalide.');
}

$bdd = sql_connect();
$albumStatement = $bdd->prepare(
    'SELECT a.idAlb, a.idArt, a.idGp, a.nomA, a.dtSortieA, a.nomLabelA,
            g.nomGp, art.nomArt, art.prenomArt
       FROM ALBUM a
       LEFT JOIN GROUPE g ON g.idGp = a.idGp
       LEFT JOIN ARTISTE art ON art.idArt = a.idArt
      WHERE a.idAlb = :idAlb'
);
$albumStatement->execute(['idAlb' => $albumId]);
$album = $albumStatement->fetch();

if (!$album) {
    http_response_code(404);
    exit('Album introuvable.');
}

$userEmail = $_SESSION['eMailUser'] ?? $_SESSION['USER_ID'] ?? null;
$isLoggedIn = is_string($userEmail) && $userEmail !== '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!$isLoggedIn || !is_string($submittedToken)
        || !isset($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        http_response_code(403);
        exit('Requête non autorisée.');
    }

    // INSERT IGNORE rend le like idempotent lorsque la paire de la clé
    // primaire composite (eMailUser, idAlb) existe déjà.
    $likeStatement = $bdd->prepare(
        'INSERT IGNORE INTO LIKES (eMailUser, idAlb) VALUES (:eMailUser, :idAlb)'
    );
    $likeStatement->execute(['eMailUser' => $userEmail, 'idAlb' => $albumId]);

    header('Location: ' . BASE_URL . '/album.php?id=' . rawurlencode((string) $albumId), true, 303);
    exit;
}

$members = [];
if (!empty($album['nomGp'])) {
    $memberStatement = $bdd->prepare(
        'SELECT idArt, nomArt, prenomArt FROM ARTISTE WHERE idGp = :idGp ORDER BY nomArt ASC, prenomArt ASC, idArt ASC'
    );
    $memberStatement->execute(['idGp' => $album['idGp']]);
    $members = $memberStatement->fetchAll();
}

$trackStatement = $bdd->prepare(
    'SELECT idTit, nomTit, dureeTit FROM TITRE WHERE idAlb = :idAlb ORDER BY idTit ASC, nomTit ASC'
);
$trackStatement->execute(['idAlb' => $albumId]);
$tracks = $trackStatement->fetchAll();

$likeStatement = $bdd->prepare('SELECT COUNT(*) FROM LIKES WHERE idAlb = :idAlb');
$likeStatement->execute(['idAlb' => $albumId]);
$likeCount = (int) $likeStatement->fetchColumn();

if ($isLoggedIn && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatDuration = static function ($duration): string {
    $seconds = max(0, (int) round((float) $duration));
    return sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
};

require __DIR__ . '/header.php';
?>
<main class="projects-section">
  <div class="section-heading">
    <div>
      <p class="eyebrow dark">Album</p>
      <h1><?= $escape($album['nomA']) ?></h1>
    </div>
  </div>

  <section class="album-details">
    <p><strong>Créateur :</strong>
      <?php if (!empty($album['nomGp'])): ?>
        <?= $escape($album['nomGp']) ?> (Groupe)
      <?php else: ?>
        <?= $escape(trim($album['prenomArt'] . ' ' . $album['nomArt'])) ?> (Artiste)
      <?php endif; ?>
    </p>
    <p><strong>Sortie :</strong> <?= $escape($album['dtSortieA']) ?></p>
    <p><strong>Label :</strong> <?= $escape($album['nomLabelA']) ?></p>
    <p><strong>Likes :</strong> <?= $likeCount ?></p>

    <?php if ($members): ?>
      <h2>Membres</h2>
      <ul>
        <?php foreach ($members as $member): ?>
          <li><?= $escape(trim($member['prenomArt'] . ' ' . $member['nomArt'])) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <h2>Pistes</h2>
    <?php if ($tracks): ?>
      <ol>
        <?php foreach ($tracks as $track): ?>
          <li><?= $escape($track['nomTit']) ?> — <?= $escape($formatDuration($track['dureeTit'])) ?></li>
        <?php endforeach; ?>
      </ol>
    <?php else: ?>
      <p>Aucune piste disponible.</p>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
      <form method="post" action="<?= $escape(BASE_URL . '/album.php?id=' . $albumId) ?>">
        <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
        <button class="btn btn-primary" type="submit">J'aime cet album</button>
      </form>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/footer.php'; ?>
