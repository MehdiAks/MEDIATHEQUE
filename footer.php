<?php require_once ROOT.'/includes/libs/cookie-consent.php'; ?>
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
