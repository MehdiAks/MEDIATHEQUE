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
  LEFT JOIN groupe g ON a.idGp = g.idGp
  LEFT JOIN ARTISTE art ON a.idArt = art.idArt
  ORDER BY a.dtSortieA DESC
";

$stmt = $bdd->query($requete);
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

  <main id="projets" class="projects-section">
    <div class="section-orb section-orb-one" aria-hidden="true"></div>
    <div class="section-orb section-orb-two" aria-hidden="true"></div>

    <section class="story-intro" aria-labelledby="story-title">
      <div class="story-intro-inner">
        <p class="eyebrow dark">Bienvenue</p>
        <h2 id="story-title">Bienvenue sur la galerie des projets de la Semaine d’Intégration MMI !</h2>

        <div class="story-copy">
          <p>Pendant trois jours, l’ensemble de la promotion a participé à un défi créatif géant sous forme de relais, en changeant d’équipe (mélangeant des 1A, 2A et 3A) tous les jours. Le but ? Construire une campagne transmédia complète en passant par trois formats obligatoires : Vidéo, Affiche, et Site Web.</p>

          <h3>Le principe du relais créatif</h3>
          <p>Toute l’originalité de cette semaine d’intégration reposait sur la transmission “à l’aveugle”. Chaque soir, à 17h30 précises, les groupes devaient déposer leur projet du jour finalisé. Mais le travail ne s’arrêtait pas là !</p>
          <p>Une fois leur création déposée, chaque équipe devait aller visionner le projet d’un autre groupe. Ils n’avaient alors que 5 minutes chrono pour l’analyser et rédiger le meilleur résumé possible sur une feuille.</p>
          <p>Le lendemain matin, un tout nouveau groupe prenait le relais : les étudiants devaient réaliser la suite du projet dans un nouveau format en se basant uniquement sur ce petit résumé écrit la veille par leurs camarades. Ils n’avaient jamais accès au point de départ ou au thème d’origine ! Ce système de “téléphone arabe” a permis de faire évoluer l’histoire de chaque projet de manière unique, surprenante et parfois complètement décalée.</p>

          <h3>Le Jeu de Piste : À la recherche du fil rouge</h3>
          <p>Conséquence de ce relais à l’aveugle : au bout de 3 jours, les formats d’un même thème (la Vidéo, l’Affiche et le Site Web) avaient parfois pris des directions artistiques très différentes !</p>
          <p>Pour clôturer la semaine, lors du grand jeu de piste du vendredi, toutes les œuvres ont été exposées. Munis d’un questionnaire, les étudiants se sont transformés en enquêteurs. Leur mission ? Retrouver les “familles” de projets et relier les œuvres entre elles en se basant sur les indices visuels (les fameux “Easter Eggs” et éléments de transmission laissés par les groupes précédents). Ils devaient par exemple observer et deviner que l’Affiche n°4 allait avec le Site Web n°9 et la Vidéo n°19 !</p>

          <h3>Les Récompenses</h3>
          <p>À la fin de ce jeu de piste, après avoir reconstitué tous les puzzles, la promo a pu voter pour élire ses créations préférées, catégorie par catégorie. Voici les résultats finaux et les grands gagnants élus :</p>
          <ul>
            <li><strong>💻 Meilleur Site Web :</strong> Thème 15</li>
            <li><strong>🎬 Meilleure Vidéo :</strong> Égalité parfaite entre le Thème 13 et le Thème 04</li>
            <li><strong>🎨 Meilleure Affiche :</strong> Thème 12</li>
          </ul>
          <p>Un immense bravo à toutes celles et ceux qui ont travaillé sur ces projets au fil de la semaine ! Prenez le temps de parcourir le site pour (re)découvrir l’évolution de chaque œuvre, de la vidéo initiale jusqu’au site web final.</p>
        </div>
      </div>
    </section>

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
                $createur = htmlspecialchars($album['nomGp']) . " (Groupe)";
            } else {
                $createur = htmlspecialchars($album['prenomArt'] . ' ' . $album['nomArt']) . " (Artiste)";
            }
          ?>
          <article class="album-card">
            <h3><?= htmlspecialchars($album['nomA']) ?></h3>
            <p><strong>Créateur :</strong> <?= $createur ?></p>
            <p><strong>Sortie :</strong> <?= htmlspecialchars($album['dtSortieA']) ?></p>
            <p><strong>Label :</strong> <?= htmlspecialchars($album['nomLabelA']) ?></p>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Aucun album trouvé dans la médiathèque.</p>
      <?php endif; ?>
    </div>
  </main>

  <section class="backstage-section" aria-labelledby="backstage-title">
    <div class="backstage-inner">
      <p class="eyebrow dark backstage-label">Backstage</p>
      <h2 id="backstage-title">Backstage</h2>
      <div class="backstage-video-wrap">
        <iframe
          src="https://www.youtube.com/embed/5V5hi0cD7xs?rel=0"
          title="Backstage MMI 2026"
          loading="lazy"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin"
          allowfullscreen>
        </iframe>
      </div>
    </div>
  </section>

<?php require_once 'footer.php'; ?>