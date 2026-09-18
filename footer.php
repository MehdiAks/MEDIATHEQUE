<?php
// footer.php
?>
  <footer>
    <span>SEMAINE D'INTÉGRATION MMI 2026</span>
    <span>MMI · BORDEAUX</span>
  </footer>

  <dialog id="mediaDialog" class="media-dialog" aria-labelledby="mediaTitle">
    <header class="media-dialog-header">
      <h2 id="mediaTitle"></h2>
      <button type="button" id="mediaClose" aria-label="Fermer la fenêtre">✕</button>
    </header>
    <div id="mediaContent" class="media-dialog-content" aria-live="polite"></div>
  </dialog>

  <dialog id="creditsDialog" class="media-dialog credits-dialog" aria-labelledby="creditsTitle">
    <header class="media-dialog-header">
      <h2 id="creditsTitle"></h2>
      <button type="button" id="creditsClose" aria-label="Fermer les crédits">✕</button>
    </header>
    <div id="creditsContent" class="credits-dialog-content" aria-live="polite"></div>
  </dialog>
  <script src="group-media.js?v=2026-09-12-1"></script>
  <script src="script.js?v=2026-09-12-1"></script>
</body>
</html>