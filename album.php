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
    'SELECT a.imageA, a.idAlb, a.idArt, a.idGp, a.nomA, a.dtSortieA, a.nomLabelA,
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
    require __DIR__.'/404.php'; exit;
}

$user = current_user();
$userEmail = $user['eMailUser'] ?? null;
$isLoggedIn = $user !== null;

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
        ($_POST['action'] ?? '') === 'unlike'
        ? 'DELETE FROM LIKES WHERE eMailUser = :eMailUser AND idAlb = :idAlb'
        : 'INSERT IGNORE INTO LIKES (eMailUser, idAlb) VALUES (:eMailUser, :idAlb)'
    );
    $likeStatement->execute(['eMailUser' => $userEmail, 'idAlb' => $albumId]);

    $_SESSION['messages'] = [($_POST['action'] ?? '') === 'unlike' ? 'Album retiré de vos favoris.' : 'Album ajouté à vos favoris.'];
    header('Location: ' . BASE_URL . '/album.php?id=' . rawurlencode((string) $albumId), true, 303);
    exit;
}

$hasLiked = $isLoggedIn && (bool)query_rows('SELECT 1 FROM LIKES WHERE eMailUser = ? AND idAlb = ?', [$userEmail,$albumId]);

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

$pageTitle = $album['nomA'].' — Médiathèque';
$pageDescription = 'Découvrez '.$album['nomA'].', ses artistes, ses pistes et ajoutez cet album à vos favoris.';
$pageImage = album_image_url($album['imageA']) ?: BASE_URL.'/src/images/Michel.png';
require __DIR__ . '/header.php';
?>
<main id="contenu" class="album-page" tabindex="-1">
  <nav aria-label="Fil d’Ariane"><a href="<?= h(BASE_URL.'/index.php') ?>">← Retour aux albums</a></nav>
  <?php flashes(); ?>
  <section class="album-hero" aria-labelledby="album-title">
    <div class="album-artwork">
      <?php if (album_image_url($album['imageA'])): ?>
        <img src="<?= h(album_image_url($album['imageA'])) ?>" alt="Pochette de l’album <?= h($album['nomA']) ?>" width="600" height="600">
      <?php else: ?>
        <img src="<?= h(BASE_URL.'/src/images/Michel.png') ?>" alt="Michel, image de remplacement pour cet album">
      <?php endif; ?>
    </div>
    <div class="album-summary">
      <p class="eyebrow dark">Album · <?= count($tracks) ?> piste<?= count($tracks)>1?'s':'' ?></p>
      <h1 id="album-title"><?= h($album['nomA']) ?></h1>
      <p class="album-byline"><?= h($album['nomGp'] ?: trim(($album['prenomArt'] ?? '').' '.($album['nomArt'] ?? ''))) ?></p>
      <dl class="album-facts">
        <div><dt>Date de sortie</dt><dd><?= $album['dtSortieA'] ? h(date('d/m/Y',strtotime($album['dtSortieA']))) : 'Non renseignée' ?></dd></div>
        <div><dt>Label</dt><dd><?= h($album['nomLabelA'] ?: 'Non renseigné') ?></dd></div>
        <div><dt>Durée totale</dt><dd><?= h($formatDuration(array_sum(array_column($tracks,'dureeTit')))) ?> (min:s)</dd></div>
        <div><dt>Favoris</dt><dd><?= $likeCount ?> personne<?= $likeCount>1?'s':'' ?></dd></div>
      </dl>
      <?php if ($isLoggedIn): ?>
        <form method="post" action="<?= h(BASE_URL.'/album.php?id='.$albumId) ?>">
          <input type="hidden" name="csrf_token" value="<?= h(generate_csrf_token()) ?>">
          <input type="hidden" name="action" value="<?= $hasLiked?'unlike':'like' ?>">
          <button class="btn btn-primary" type="submit" aria-pressed="<?= $hasLiked?'true':'false' ?>"><?= $hasLiked?'Retirer de mes favoris':'Ajouter à mes favoris' ?></button>
        </form>
      <?php else: ?>
        <a class="btn btn-primary" href="<?= h(BASE_URL.'/views/backend/security/login.php') ?>">Se connecter pour ajouter aux favoris</a>
      <?php endif; ?>
    </div>
  </section>
  <section class="album-tracks" aria-labelledby="tracks-title">
    <h2 id="tracks-title">Les pistes</h2>
    <?php if ($tracks): ?>
      <table class="track-table"><caption class="visually-hidden">Pistes de <?= h($album['nomA']) ?> et durées en minutes et secondes</caption>
        <thead><tr><th scope="col">N°</th><th scope="col">Titre</th><th scope="col">Durée</th></tr></thead>
        <tbody><?php foreach ($tracks as $i=>$track): ?><tr>
          <td><?= $i+1 ?></td><th scope="row"><?= h($track['nomTit']) ?></th>
          <td><span aria-hidden="true"><?= h($formatDuration($track['dureeTit'])) ?></span><span class="visually-hidden"><?= (int)$track['dureeTit'] / 60 >= 1 ? intdiv((int)round($track['dureeTit']),60).' minutes ' : '' ?><?= (int)round($track['dureeTit']) % 60 ?> secondes</span></td>
        </tr><?php endforeach; ?></tbody>
      </table>
    <?php else: ?><p>Aucune piste n’a encore été ajoutée à cet album.</p><?php endif; ?>
  </section>
  <?php if ($members): ?>
    <section class="album-members" aria-labelledby="members-title"><h2 id="members-title">Les membres du groupe</h2>
    <ul><?php foreach ($members as $member): ?><li><?= h(trim($member['prenomArt'].' '.$member['nomArt'])) ?></li><?php endforeach; ?></ul></section>
  <?php endif; ?>
</main>
<?php require __DIR__.'/footer.php'; ?>
