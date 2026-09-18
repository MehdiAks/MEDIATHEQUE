<?php 
require_once 'header.php';

// Connexion à la base de données
$bdd = sql_connect();

// Requête SQL pour récupérer les albums avec leur artiste ou leur groupe
// Selon la BDD : soit idArt est renseigné, soit idGp est renseigné
$requete = "
  SELECT 
    a.idAlb,
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

    <div class="section-heading">
      <div>
        <p class="eyebrow dark">Médiathèque</p>
        <h2>Les Albums</h2>
      </div>
      <p class="section-intro">Découvrez les albums de notre collection musicale.</p>
    </div>

    <!-- Affichage dynamique des albums de la BDD -->
    <div id="projectGrid" class="project-grid">
      <?php if (!empty($albums)): ?>
        <?php foreach ($albums as $album): ?>
          <?php 
            // Détermination du créateur (Groupe ou Artiste solo)
            if (!empty($album['nomGp'])) {
                $createur = h($album['nomGp']) . " (Groupe)";
            } else {
                $createur = h($album['prenomArt'] . ' ' . $album['nomArt']) . " (Artiste)";
            }
          ?>
          <a class="album-card-link" href="<?= htmlspecialchars(BASE_URL . '/album.php?id=' . rawurlencode((string) $album['idAlb']), ENT_QUOTES, 'UTF-8') ?>">
          <article class="album-card">
            <h3><?= h($album['nomA']) ?></h3>
            <p><strong>Créateur :</strong> <?= $createur ?></p>
            <p><strong>Sortie :</strong> <?= h($album['dtSortieA']) ?></p>
            <p><strong>Label :</strong> <?= h($album['nomLabelA']) ?></p>
          </article>
          </a>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Aucun album trouvé dans la médiathèque.</p>
      <?php endif; ?>
    </div>
  </main>

<?php require_once 'footer.php'; ?>
