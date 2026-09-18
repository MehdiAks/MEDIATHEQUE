(() => {
  const introLayer = document.getElementById('siteIntroLayer');
  const introButton = document.getElementById('siteIntroButton');
  if (introLayer && introButton) {
    const introStorageKey = 'mmi-intro-seen';
    const cameFromThisSite = (() => {
      if (!document.referrer) return false;
      try {
        return new URL(document.referrer).origin === window.location.origin;
      } catch {
        return false;
      }
    })();
    let introWasSeen = false;
    try {
      introWasSeen = sessionStorage.getItem(introStorageKey) === 'true';
      if (!introWasSeen && !cameFromThisSite) sessionStorage.setItem(introStorageKey, 'true');
    } catch {
      introWasSeen = cameFromThisSite;
    }

    if (introWasSeen || cameFromThisSite) {
      introLayer.remove();
    } else {
      document.body.classList.add('intro-layer-open');
      introLayer.classList.remove('is-pending');
      introButton.addEventListener('click', () => {
        introLayer.classList.add('is-closing');
        document.body.classList.remove('intro-layer-open');
        introLayer.addEventListener('transitionend', () => introLayer.remove(), { once: true });
      });
    }
  }
  const fallback = document.body.dataset.imageFallback;
  function replaceImage(img) {
    if (!fallback || img.dataset.fallbackApplied) return;
    img.dataset.fallbackApplied = 'true';
    img.removeAttribute('srcset');
    img.src = fallback;
    if (img.alt) img.alt = 'Michel, image de remplacement';
  }
  document.addEventListener('error', event => { if (event.target instanceof HTMLImageElement) replaceImage(event.target); }, true);
  document.querySelectorAll('img').forEach(img => { if (img.complete && !img.naturalWidth) replaceImage(img); });
  const audioInput = document.getElementById('audioTit');
  audioInput?.addEventListener('change', () => {
    const hasFile = audioInput.files.length > 0;
    for (const id of ['audioTitle','audioDuration']) {
      const input = document.getElementById(id);
      if (input) input.required = hasFile;
    }
    audioInput.setCustomValidity(hasFile && audioInput.files[0].size > 20*1024*1024 ? '20 Mo maximum par fichier audio.' : '');
  });
  document.addEventListener('play', event => {
    if (event.target instanceof HTMLAudioElement) document.querySelectorAll('audio').forEach(player => { if (player !== event.target) player.pause(); });
  }, true);
  document.querySelectorAll('[data-password-toggle]').forEach(toggle => {
    const passwordInput = document.getElementById(toggle.dataset.passwordToggle);
    if (!passwordInput) return;
    toggle.addEventListener('click', () => {
      const isVisible = passwordInput.type === 'text';
      passwordInput.type = isVisible ? 'password' : 'text';
      toggle.textContent = isVisible ? 'Afficher' : 'Masquer';
      toggle.setAttribute('aria-pressed', String(!isVisible));
    });
  });
  const audioPlayer = document.getElementById('siteAudioPlayer');
  const cursorDisk = document.getElementById('cursorDisk');
  if (cursorDisk && window.matchMedia('(pointer: fine)').matches) {
    let cursorFrame = null;
    document.addEventListener('mousemove', event => {
      if (cursorFrame) cancelAnimationFrame(cursorFrame);
      cursorFrame = requestAnimationFrame(() => {
        cursorDisk.style.left = `${event.clientX}px`;
        cursorDisk.style.top = `${event.clientY}px`;
        cursorDisk.classList.add('is-visible');
      });
    });
  }
  const audioTracks = [...document.querySelectorAll('.track-audio')];
  if (audioPlayer && audioTracks.length) {
    const playerTitle = audioPlayer.querySelector('[data-player-title]');
    const playerArtist = audioPlayer.querySelector('[data-player-artist]');
    const playerPlay = audioPlayer.querySelector('[data-player-play]');
    const playerLike = audioPlayer.querySelector('[data-player-like]');
    const playerProgress = audioPlayer.querySelector('[data-player-progress]');
    const playerCurrent = audioPlayer.querySelector('[data-player-current]');
    const playerDuration = audioPlayer.querySelector('[data-player-duration]');
    const playerDisc = audioPlayer.querySelector('[data-player-disc]');
    let activeTrack = null;

    const formatTime = seconds => {
      if (!Number.isFinite(seconds)) return '0:00';
      const totalSeconds = Math.max(0, Math.floor(seconds));
      return `${Math.floor(totalSeconds / 60)}:${String(totalSeconds % 60).padStart(2, '0')}`;
    };
    const updateLikeButton = () => {
      const liked = activeTrack?.dataset.playerLiked === 'true';
      playerLike.textContent = liked ? '♥' : '♡';
      playerLike.setAttribute('aria-label', liked ? 'Retirer des favoris' : 'Ajouter aux favoris');
      playerLike.setAttribute('title', liked ? 'Retirer des favoris' : 'Ajouter aux favoris');
      playerLike.setAttribute('aria-pressed', String(liked));
    };
    const showPlayer = track => {
      activeTrack = track;
      playerTitle.textContent = track.dataset.trackTitle || 'Titre sans nom';
      playerArtist.textContent = `${track.dataset.trackArtist || 'Artiste non renseigné'} · ${track.dataset.albumTitle || 'Album non renseigné'}`;
      playerDuration.textContent = formatTime(track.duration);
      playerProgress.value = track.duration ? (track.currentTime / track.duration) * 100 : 0;
      playerCurrent.textContent = formatTime(track.currentTime);
      updateLikeButton();
      audioPlayer.hidden = false;
      audioPlayer.classList.add('is-visible');
    };
    const hidePlayer = () => {
      audioPlayer.classList.remove('is-visible');
      audioPlayer.hidden = true;
      playerDisc.classList.remove('is-playing');
      activeTrack = null;
    };
    const playTrack = track => {
      audioTracks.forEach(candidate => { if (candidate !== track) candidate.pause(); });
      activeTrack = track;
      track.play();
    };

    audioTracks.forEach(track => {
      track.addEventListener('play', () => { showPlayer(track); playerDisc.classList.add('is-playing'); playerPlay.textContent = 'Ⅱ'; playerPlay.setAttribute('aria-label', 'Mettre en pause'); });
      track.addEventListener('pause', () => { if (activeTrack === track) { playerDisc.classList.remove('is-playing'); playerPlay.textContent = '▶'; } });
      track.addEventListener('loadedmetadata', () => { if (activeTrack === track) playerDuration.textContent = formatTime(track.duration); });
      track.addEventListener('timeupdate', () => {
        if (activeTrack !== track) return;
        playerProgress.value = track.duration ? (track.currentTime / track.duration) * 100 : 0;
        playerCurrent.textContent = formatTime(track.currentTime);
      });
      track.addEventListener('ended', () => {
        const nextTrack = audioTracks[audioTracks.indexOf(track) + 1];
        if (nextTrack) playTrack(nextTrack);
        else hidePlayer();
      });
    });
    document.querySelectorAll('[data-home-track]').forEach(trackLink => {
      const homeTrack = trackLink.querySelector('.home-track-audio');
      if (!homeTrack) return;
      trackLink.addEventListener('click', event => {
        event.preventDefault();
        if (activeTrack === homeTrack && !homeTrack.paused) homeTrack.pause();
        else playTrack(homeTrack);
      });
    });
    playerPlay.addEventListener('click', () => {
      if (!activeTrack) return;
      if (activeTrack.paused) activeTrack.play(); else activeTrack.pause();
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
      activeTrack.pause();
      activeTrack.currentTime = 0;
      playerDisc.classList.remove('is-playing');
      hidePlayer();
    });
    playerProgress.addEventListener('input', () => {
      if (activeTrack?.duration) activeTrack.currentTime = (Number(playerProgress.value) / 100) * activeTrack.duration;
    });
    playerLike.addEventListener('click', async () => {
      if (!activeTrack) return;
      if (activeTrack.dataset.playerAuth !== 'true') {
        window.location.href = `${activeTrack.dataset.playerLikeUrl.replace('/album.php?', '/views/backend/security/login.php?')}`;
        return;
      }
      const wasLiked = activeTrack.dataset.playerLiked === 'true';
      const formData = new FormData();
      formData.append('csrf_token', activeTrack.dataset.playerCsrf);
      formData.append('action', wasLiked ? 'unlike' : 'like');
      playerLike.disabled = true;
      try {
        const response = await fetch(activeTrack.dataset.playerLikeUrl, { method: 'POST', body: formData, credentials: 'same-origin' });
        if (!response.ok) throw new Error('Like impossible');
        activeTrack.dataset.playerLiked = String(!wasLiked);
        updateLikeButton();
      } catch (error) {
        playerLike.title = 'Impossible de modifier le favori';
      } finally {
        playerLike.disabled = false;
      }
    });
  }
  const form = document.querySelector('[data-auth]');
  if (!form) return;
  const email = form.elements.eMailUser;
  const emailHelp = document.getElementById('email-help');
  function checkEmail() {
    const valid = /^[^\s@]+@[^\s@.]+(?:\.[^\s@.]+)+$/.test(email.value) && email.validity.typeMismatch === false;
    email.setCustomValidity(valid ? '' : 'Saisissez une adresse au format texte@texte.texte.');
    email.setAttribute('aria-invalid', String(!valid && email.value !== ''));
    emailHelp.textContent = !email.value ? 'Format attendu : texte@texte.texte' : valid ? '✓ Format de l’adresse valide' : '✕ Format attendu : texte@texte.texte';
  }
  email.addEventListener('input', checkEmail);
  const password = form.elements.password;
  const confirm = form.elements.password_confirm;
  function checkPassword() {
    if (!confirm) return;
    const value = password.value;
    const rules = {length:[...value].length>=12, upper:/[A-Z]/.test(value), lower:/[a-z]/.test(value), digit:/[0-9]/.test(value), special:/[^a-zA-Z0-9\s]/u.test(value), bytes:new TextEncoder().encode(value).length<=72};
    Object.entries(rules).forEach(([key,valid]) => {
      const row = document.querySelector(`[data-rule="${key}"]`);
      row.classList.toggle('rule-valid',valid); row.querySelector('span').textContent=valid?'✓':'○';
    });
    const valid = Object.values(rules).every(Boolean);
    password.setCustomValidity(valid?'':'Le mot de passe doit respecter tous les critères indiqués.');
    password.setAttribute('aria-invalid',String(!valid && value!==''));
    const matches = confirm.value === value;
    confirm.setCustomValidity(matches?'':'Les mots de passe doivent être identiques.');
    document.getElementById('confirm-help').textContent = !confirm.value ? '' : matches?'✓ Les mots de passe correspondent.':'✕ Les mots de passe ne correspondent pas.';
  }
  password.addEventListener('input',checkPassword);
  confirm?.addEventListener('input',checkPassword);
  form.addEventListener('submit',event => { checkEmail(); checkPassword(); if (!form.reportValidity()) event.preventDefault(); });
})();
