(() => {
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
