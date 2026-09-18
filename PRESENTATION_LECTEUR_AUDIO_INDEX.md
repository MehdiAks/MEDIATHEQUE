# Présentation : le lecteur audio de la page d'accueil

> Ce fichier est un **support de présentation** : il regroupe uniquement les
> morceaux de PHP, HTML et JavaScript nécessaires au lecteur de `index.php`.
> Il n'est pas chargé par le site et ne remplace pas les fichiers d'origine.

## L'idée générale en 30 secondes

Le lecteur fonctionne en trois étapes :

1. **PHP récupère les morceaux** dans la base de données et vérifie leur chemin.
2. **HTML crée un élément `<audio>` caché** pour chaque morceau et place les
   informations du titre dans des attributs `data-*`.
3. **JavaScript relie les clics au son** et met à jour le lecteur visible :
   lecture, pause, précédent, suivant, arrêt et barre de progression.

Le son est donc réellement joué par l'élément HTML `<audio>`. Le grand lecteur
fixé en bas de l'écran est une interface personnalisée qui pilote cet élément.

---

## 1. PHP : récupérer les morceaux

Extrait simplifié de `index.php` :

```php
<?php
// On demande les informations utiles à l'affichage et à la lecture :
// - le titre et sa durée ;
// - le chemin du fichier audio ;
// - l'album et son créateur.
$topLikedStatement = $bdd->query(
  "SELECT t.idTit, t.nomTit, t.dureeTit, t.audioTit,
          a.idAlb, a.nomA,
          COALESCE(
            g.nomGp,
            TRIM(CONCAT(COALESCE(art.prenomArt, ''), ' ', COALESCE(art.nomArt, '')))
          ) AS createur
     FROM TITRE t
     INNER JOIN ALBUM a ON a.idAlb = t.idAlb
     LEFT JOIN GROUPE g ON g.idGp = a.idGp
     LEFT JOIN ARTISTE art ON art.idArt = a.idArt
     LIMIT 5"
);

// fetchAll transforme les résultats SQL en tableau PHP.
$tracks = $topLikedStatement->fetchAll(PDO::FETCH_ASSOC);
?>
```

À dire au tableau : **la jointure permet de récupérer en une requête le morceau,
son album et le nom de l'artiste ou du groupe.** La vraie requête ajoute aussi le
nombre de likes et le tri du classement.

Avant d'utiliser le chemin reçu depuis la base, le projet le valide avec cette
fonction de `functions/audio.php` :

```php
<?php
function audio_url(?string $path): ?string {
    // La regex n'accepte que : uploads/audio/ + 32 caractères hexadécimaux
    // + une extension autorisée. Exemple : uploads/audio/abc...123.mp3
    $isValid = $path
        && preg_match('~^uploads/audio/[a-f0-9]{32}\.(mp3|wav|ogg)$~D', $path);

    // Si le chemin est valide, on fabrique son URL publique.
    // Sinon, on renvoie null : aucun lecteur ne sera créé pour ce titre.
    return $isValid ? BASE_URL.'/'.$path : null;
}
?>
```

Point important : **on ne place pas directement n'importe quel chemin de la base
dans la page**. On accepte seulement le dossier et les formats prévus.

---

## 2. HTML/PHP : créer une piste audio sur la page index

Cet extrait correspond à une ligne de morceau dans `index.php` :

```php
<?php foreach ($tracks as $track): ?>
  <?php
  // La fonction renvoie une URL sûre, ou null si le chemin est invalide.
  $trackAudio = audio_url($track['audioTit']);
  ?>

  <a
    class="music-ranking-link"
    href="<?= h(BASE_URL.'/album.php?id='.(int) $track['idAlb']) ?>"
    <?= $trackAudio ? 'data-home-track="true"' : '' ?>
  >
    <strong><?= h($track['nomTit']) ?></strong>

    <?php if ($trackAudio): ?>
      <!--
        C'est CET élément qui lit réellement le fichier son.
        Il est caché visuellement, car on utilise notre propre interface.

        preload="metadata" demande seulement les informations du fichier
        (par exemple sa durée), pas le téléchargement complet dès l'ouverture.

        Les attributs data-* transportent les informations de PHP vers JS.
        h() échappe les valeurs avant de les écrire dans le HTML.
      -->
      <audio
        class="track-audio home-track-audio"
        preload="metadata"
        src="<?= h($trackAudio) ?>"
        data-track-title="<?= h($track['nomTit']) ?>"
        data-album-title="<?= h($track['nomA']) ?>"
        data-track-artist="<?= h($track['createur']) ?>"
        data-player-like-url="<?= h(BASE_URL.'/album.php?id='.(int) $track['idAlb']) ?>"
        data-player-auth="false"
        data-player-liked="false"
      >
        Votre navigateur ne prend pas en charge le lecteur audio.
      </audio>
    <?php endif; ?>
  </a>
<?php endforeach; ?>
```

La classe `home-track-audio` cache le lecteur natif sur l'accueil :

```css
/* On garde l'élément <audio> dans la page, mais on affiche notre lecteur. */
.home-track-audio {
  display: none;
}
```

---

## 3. HTML : l'interface du lecteur visible

Le lecteur est écrit une seule fois dans `footer.php`, donc il est disponible en
bas des pages du site. Les attributs `data-player-*` servent de points de repère
au JavaScript.

```html
<!-- hidden évite d'afficher le lecteur avant le choix d'un morceau. -->
<aside id="siteAudioPlayer" class="audio-player" aria-label="Lecteur audio" hidden>
  <div class="audio-player-info">
    <span>Lecture en cours</span>
    <strong data-player-title></strong>
    <small data-player-artist></small>
  </div>

  <div class="audio-player-controls">
    <button type="button" data-player-previous aria-label="Musique précédente">⏮</button>
    <button type="button" data-player-play aria-label="Lire la musique">▶</button>
    <button type="button" data-player-next aria-label="Musique suivante">⏭</button>
    <button type="button" data-player-stop aria-label="Arrêter la lecture">⏹</button>
  </div>

  <div class="audio-player-progress">
    <span data-player-current>0:00</span>
    <input
      type="range"
      data-player-progress
      min="0"
      max="100"
      value="0"
      step="0.1"
      aria-label="Progression de la musique"
    >
    <span data-player-duration>0:00</span>
  </div>
</aside>
```

Dans le vrai fichier, les symboles sont remplacés par les images du projet et un
bouton de favoris est également présent.

---

## 4. JavaScript : connecter les pistes au lecteur

Version extraite et commentée de `src/js/site-ui.js` :

```js
// On récupère l'interface visible et toutes les balises <audio> de la page.
const audioPlayer = document.getElementById('siteAudioPlayer');
const audioTracks = [...document.querySelectorAll('.track-audio')];

// Le code ne se lance que si l'interface et au moins une piste existent.
if (audioPlayer && audioTracks.length) {
  // On récupère les éléments que le script devra modifier.
  const playerTitle = audioPlayer.querySelector('[data-player-title]');
  const playerArtist = audioPlayer.querySelector('[data-player-artist]');
  const playerPlay = audioPlayer.querySelector('[data-player-play]');
  const playerProgress = audioPlayer.querySelector('[data-player-progress]');
  const playerCurrent = audioPlayer.querySelector('[data-player-current]');
  const playerDuration = audioPlayer.querySelector('[data-player-duration]');

  // Cette variable mémorise la piste actuellement contrôlée.
  let activeTrack = null;

  // Transforme un temps en secondes (ex. 83) en minutes:secondes (1:23).
  const formatTime = seconds => {
    if (!Number.isFinite(seconds)) return '0:00';
    const total = Math.max(0, Math.floor(seconds));
    return `${Math.floor(total / 60)}:${String(total % 60).padStart(2, '0')}`;
  };

  // Affiche le lecteur et copie les data-* de la piste dans l'interface.
  const showPlayer = track => {
    activeTrack = track;
    playerTitle.textContent = track.dataset.trackTitle || 'Titre sans nom';
    playerArtist.textContent =
      `${track.dataset.trackArtist || 'Artiste non renseigné'} · ` +
      `${track.dataset.albumTitle || 'Album non renseigné'}`;
    playerCurrent.textContent = formatTime(track.currentTime);
    playerDuration.textContent = formatTime(track.duration);
    audioPlayer.hidden = false;
  };

  // Lance une piste après avoir mis les autres en pause.
  // Cela empêche deux morceaux de jouer en même temps.
  const playTrack = track => {
    audioTracks.forEach(otherTrack => {
      if (otherTrack !== track) otherTrack.pause();
    });
    activeTrack = track;
    track.play();
  };

  // Chaque élément <audio> envoie des événements pendant sa lecture.
  audioTracks.forEach(track => {
    track.addEventListener('play', () => showPlayer(track));

    // timeupdate est envoyé régulièrement par le navigateur.
    // On s'en sert pour actualiser le temps et la barre de progression.
    track.addEventListener('timeupdate', () => {
      if (activeTrack !== track) return;
      playerCurrent.textContent = formatTime(track.currentTime);
      playerProgress.value = track.duration
        ? (track.currentTime / track.duration) * 100
        : 0;
    });

    // Quand un morceau se termine, on cherche le suivant dans le tableau.
    track.addEventListener('ended', () => {
      const position = audioTracks.indexOf(track);
      const nextTrack = audioTracks[position + 1];
      if (nextTrack) playTrack(nextTrack);
      else audioPlayer.hidden = true;
    });
  });

  // Sur la page index, cliquer sur une ligne joue ou met en pause son audio.
  document.querySelectorAll('[data-home-track]').forEach(trackLink => {
    const track = trackLink.querySelector('.home-track-audio');
    if (!track) return;

    trackLink.addEventListener('click', event => {
      // Sans cela, le lien ouvrirait directement la page de l'album.
      event.preventDefault();

      if (activeTrack === track && !track.paused) track.pause();
      else playTrack(track);
    });
  });

  // Le même bouton sert à lire et à mettre en pause.
  playerPlay.addEventListener('click', () => {
    if (!activeTrack) return;
    if (activeTrack.paused) activeTrack.play();
    else activeTrack.pause();
  });

  audioPlayer.querySelector('[data-player-previous]').addEventListener('click', () => {
    if (!activeTrack) return;
    const previousTrack = audioTracks[audioTracks.indexOf(activeTrack) - 1];
    if (previousTrack) playTrack(previousTrack);
  });

  audioPlayer.querySelector('[data-player-next]').addEventListener('click', () => {
    if (!activeTrack) return;
    const nextTrack = audioTracks[audioTracks.indexOf(activeTrack) + 1];
    if (nextTrack) playTrack(nextTrack);
  });

  audioPlayer.querySelector('[data-player-stop]').addEventListener('click', () => {
    if (!activeTrack) return;
    activeTrack.pause();       // Arrête temporairement le son.
    activeTrack.currentTime = 0; // Replace la piste au début.
    audioPlayer.hidden = true; // Cache l'interface.
    activeTrack = null;
  });

  // Déplacer le curseur change le moment joué dans le morceau.
  playerProgress.addEventListener('input', () => {
    if (!activeTrack?.duration) return;
    activeTrack.currentTime =
      (Number(playerProgress.value) / 100) * activeTrack.duration;
  });
}
```

---

## Déroulé simple à raconter au tableau

1. « La base de données me donne le titre et le chemin du fichier audio. »
2. « PHP vérifie le chemin puis crée une balise `<audio>` cachée par morceau. »
3. « Les attributs `data-*` font le lien entre les données PHP et JavaScript. »
4. « Au clic, JavaScript empêche l'ouverture du lien et appelle `play()` sur la
   bonne balise audio. »
5. « La variable `activeTrack` retient le morceau courant. »
6. « Les événements `play`, `timeupdate` et `ended` gardent l'interface
   synchronisée avec le vrai son. »
7. « Les boutons ne lisent pas eux-mêmes de musique : ils appellent les méthodes
   `play()`, `pause()` et modifient `currentTime` sur l'élément `<audio>`. »

## Mots-clés à connaître si on te pose une question

- **`data-*`** : attribut HTML personnalisé, lisible en JavaScript avec
  `element.dataset`.
- **`event.preventDefault()`** : bloque le comportement normal du lien.
- **`play()` / `pause()`** : méthodes natives de l'API audio du navigateur.
- **`currentTime`** : position actuelle dans le son, en secondes.
- **`duration`** : durée totale du son, en secondes.
- **`timeupdate`** : événement envoyé quand la position de lecture avance.
- **`activeTrack`** : référence vers le morceau actuellement sélectionné.
- **`textContent`** : ajoute du texte sans interpréter du HTML, ce qui est plus
  sûr pour les valeurs provenant des données.

### Phrase de conclusion possible

> « J'aime cette partie parce qu'elle relie les trois couches du projet : PHP
> récupère et sécurise les données, HTML fournit le lecteur natif, et JavaScript
> transforme ce lecteur en une interface interactive et synchronisée. »
