<?php 
require_once 'header.php';

// Connexion à la base de données
$bdd = sql_connect();

// Requête SQL pour récupérer les albums avec leur artiste ou leur groupe
// Selon la BDD : soit idArt est renseigné, soit idGp est renseigné
$requete = "
  SELECT 
    a.idAlb,
    a.imageA,
    a.nomA,
    a.dtSortieA,
    a.nomLabelA,
    g.nomGp,
    art.nomArt,
    art.prenomArt
  FROM ALBUM a
  LEFT JOIN GROUPE g ON a.idGp = g.idGp
  LEFT JOIN ARTISTE art ON a.idArt = art.idArt
  WHERE a.nomA LIKE ? OR g.nomGp LIKE ? OR art.nomArt LIKE ? OR art.prenomArt LIKE ?
  ORDER BY a.dtSortieA DESC
";

$stmt = $bdd->prepare($requete);
$search = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$stmt->execute(array_fill(0, 4, '%'.$search.'%'));
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

  <main id="projets" class="projects-section">
    <div class="section-orb section-orb-one" aria-hidden="true"></div>
    <div class="section-orb section-orb-two" aria-hidden="true"></div>

    <section class="catalogue-intro" aria-labelledby="catalogue-title"><p class="eyebrow dark">Votre prochaine découverte musicale</p><h1 id="catalogue-title">La musique se découvre ici.</h1><p>Parcourez les albums, rencontrez les artistes et retrouvez vos coups de cœur.</p><a class="btn btn-primary" href="#projectGrid">Découvrir les albums</a></section>
    <div class="section-heading">
      <div>
        <p class="eyebrow dark">Médiathèque</p>
        <h2>Les Albums</h2>
      </div>
      <p class="section-intro">Découvrez les albums de notre collection musicale.</p>
    </div>

    <?php if ($search !== ''): ?>
      <p class="catalogue-results"><?= count($albums) ?> résultat<?= count($albums) > 1 ? 's' : '' ?> pour « <?= h($search) ?> » · <a href="<?= h(BASE_URL.'/index.php') ?>">Tout afficher</a></p>
    <?php endif; ?>
    <div id="projectGrid" class="project-grid album-grid">
      <?php foreach ($albums as $album):
          $createur = !empty($album['nomGp']) ? $album['nomGp'] : trim(($album['prenomArt'] ?? '').' '.($album['nomArt'] ?? ''));
          $year = !empty($album['dtSortieA']) ? substr($album['dtSortieA'], 0, 4) : '';
      ?>
        <a class="album-card-link" href="<?= h(BASE_URL.'/album.php?id='.(int)$album['idAlb']) ?>">
          <article class="album-card">
            <div class="album-cover album-cover--<?= (int)$album['idAlb'] % 4 ?>">
              <span class="album-cover-category"><?= !empty($album['nomGp']) ? 'Groupe' : 'Artiste' ?></span>
              <?php if (album_image_url($album['imageA'])): ?>
              <img class="album-cover-image" src="<?= h(album_image_url($album['imageA'])) ?>" alt="Pochette de <?= h($album['nomA']) ?>" loading="lazy" width="600" height="600">
              <?php else: ?><img class="album-cover-image fallback-cover" src="<?= h(BASE_URL.'/src/images/Michel.png') ?>" alt="Michel, image de remplacement pour cet album" loading="lazy"><?php endif; ?>
              <span class="album-cover-name" <?= album_image_url($album['imageA']) ? 'hidden' : '' ?>><?= h($album['nomA']) ?></span>
              <span class="album-cover-action">Découvrir l’album <span>↗</span></span>
            </div>
            <div class="album-card-body">
              <h3><?= h($album['nomA']) ?></h3>
              <p class="album-creator"><?= h($createur ?: 'Artiste non renseigné') ?></p>
              <p class="album-meta"><?= h(implode(' · ', array_filter([$year, $album['nomLabelA']]))) ?></p>
            </div>
          </article>
        </a>
      <?php endforeach; ?>
      <?php if (!$albums): ?>
        <p class="catalogue-empty"><?= $search !== '' ? 'Aucun album ne correspond à votre recherche.' : 'Aucun album dans la collection pour le moment.' ?></p>
      <?php endif; ?>
    </div>
  </main>

<?php require_once 'footer.php'; ?>
