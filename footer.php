<?php require_once ROOT.'/includes/libs/cookie-consent.php'; ?>
<img class="cursor-disk" id="cursorDisk" src="<?= h(BASE_URL.'/src/images/disque.png') ?>" alt="" aria-hidden="true">
<aside class="audio-player" id="siteAudioPlayer" aria-label="Lecteur audio" hidden>
  <img class="audio-player-disc" data-player-disc src="<?= h(BASE_URL.'/src/images/disque.png') ?>" alt="" aria-hidden="true">
  <div class="audio-player-info">
    <span class="audio-player-label">Lecture en cours</span>
    <strong data-player-title></strong>
    <small data-player-artist></small>
  </div>
  <div class="audio-player-controls">
    <button type="button" class="audio-player-button" data-player-previous aria-label="Musique précédente" title="Musique précédente">|◀</button>
    <button type="button" class="audio-player-button audio-player-play" data-player-play aria-label="Lire la musique" title="Lire la musique">▶</button>
    <button type="button" class="audio-player-button" data-player-next aria-label="Musique suivante" title="Musique suivante">▶|</button>
    <button type="button" class="audio-player-button" data-player-stop aria-label="Arrêter la lecture" title="Arrêter la lecture">■</button>
    <button type="button" class="audio-player-button audio-player-like" data-player-like aria-label="Ajouter aux favoris" title="Ajouter aux favoris">♡</button>
  </div>
  <div class="audio-player-progress">
    <span data-player-current>0:00</span>
    <input type="range" data-player-progress min="0" max="100" value="0" step="0.1" aria-label="Progression de la musique">
    <span data-player-duration>0:00</span>
  </div>
</aside>
<footer class="site-footer">
  <span>MÉDIATHÈQUE · MMI BORDEAUX</span>
  <nav aria-label="Informations légales">
    <a href="<?= h(BASE_URL.'/views/frontend/rgpd/rgpd.php') ?>">Confidentialité / RGPD</a>
    <a href="<?= h(BASE_URL.'/views/frontend/rgpd/cgu.php') ?>">CGU</a>
    <a href="<?= h(BASE_URL.'/index.php?cookies=1') ?>">Gérer mes cookies</a>
  </nav>
</footer>
<?php if (getCookieConsent() === null || isset($_GET['cookies'])): ?>
<aside class="cookie-banner" aria-labelledby="cookie-title">
  <h2 id="cookie-title">Vos cookies, votre choix</h2>
  <p>Nous utilisons un cookie de session pour la connexion et la sécurité. Aucun cookie publicitaire ni outil de mesure d’audience n’est actuellement activé. Votre choix est conservé pendant 6 mois.</p>
  <a href="<?= h(BASE_URL.'/views/frontend/rgpd/rgpd.php') ?>">En savoir plus sur vos données</a>
  <form method="post" action="<?= h(BASE_URL.'/api/privacy/consent.php') ?>">
    <input type="hidden" name="csrf_token" value="<?= h(generate_csrf_token()) ?>">
    <button class="btn btn-outline-dark" name="choice" value="refused">Refuser les cookies facultatifs</button>
    <button class="btn btn-outline-dark" name="choice" value="accepted">Accepter les cookies facultatifs</button>
  </form>
  <small>Les cookies strictement nécessaires restent actifs. Aucun nouveau traceur n’est autorisé par ce choix sans information préalable.</small>
</aside>
<?php endif; ?>
<script src="<?= h(BASE_URL.'/src/js/site-ui.js') ?>" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body></html>
