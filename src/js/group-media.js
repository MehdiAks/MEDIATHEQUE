/* Médias communs : chemins relatifs au sous-site, même depuis dist/client. v2026-09-12 */
(() => {
  const match = location.pathname.match(/^(.*\/G\d{2}\/[^/]+\/)/i);
  const root = match ? new URL(match[1], location.href) : new URL('./', location.href);
  const group = match?.[1].match(/\/(G\d{2})\//i)?.[1].toUpperCase() || document.documentElement.dataset.group;
  const cache = new Map();
  function probeMedia(url) {
    return new Promise(resolve => {
      const extension = url.split('.').pop().toLowerCase();
      if (extension === 'pdf') return resolve(false);
      const media = document.createElement(['mp4', 'mov'].includes(extension) ? 'video' : 'img');
      let timer;
      const finish = success => {
        clearTimeout(timer);
        media.onload = media.onerror = media.onloadedmetadata = null;
        if (media.tagName === 'VIDEO') { media.removeAttribute('src'); media.load(); }
        resolve(success);
      };
      media.onload = media.onloadedmetadata = () => finish(true);
      media.onerror = () => finish(false);
      timer = setTimeout(() => finish(false), 5000);
      media.preload = 'metadata';
      media.src = url;
    });
  }
  async function findCandidates(candidates) {
    const key = candidates.join('|');
    if (!cache.has(key)) cache.set(key, (async () => {
      for (const url of candidates) {
        if (location.protocol === 'file:') {
          if (await probeMedia(url)) return url;
          continue;
        }
        try {
          let response = await fetch(url, { method: 'HEAD' });
          if (response.status === 405 || response.status === 501) {
            response = await fetch(url, { headers: { Range: 'bytes=0-0' } });
            response.body?.cancel().catch(() => {});
          }
          if (response.ok && !response.headers.get('content-type')?.includes('text/html')) return url;
        } catch {
          if (await probeMedia(url)) return url;
        }
      }
      return null;
    })());
    const result = await cache.get(key);
    if (!result) cache.delete(key); // Un fichier ajouté ensuite doit pouvoir être retrouvé.
    return result;
  }
  function find(base, extensions) {
    return findCandidates(extensions.map(extension => `${base}.${extension}`));
  }
  window.GroupMedia = { root: root.href, group, find, findCandidates };
  if (!group) return;
  const poster = find(new URL(`assets/images/affiche_${group}`, root).href, ['png', 'jpg', 'jpeg', 'pdf']);
  const video = find(new URL(`assets/videos/video_${group}`, root).href, ['mp4', 'mov']);
  window.GroupMedia.ready = Promise.all([poster, video]).then(([poster, video]) => ({ poster, video }));

  function update(element) {
    for (const attr of ['src', 'poster', 'data', 'href', 'data-src', 'data-trailer']) {
      const value = element.getAttribute(attr);
      if (!value) continue;
      const isPoster = new RegExp(`affiche_${group}\\.(png|jpe?g|pdf)$`, 'i').test(value);
      const isVideo = new RegExp(`video_${group}\\.(mp4|mov)$`, 'i').test(value);
      if (!isPoster && !isVideo) continue;
      (isPoster ? poster : video).then(url => {
        if (!url || !element.isConnected || element.getAttribute(attr) !== value) return;
        const pdf = url.endsWith('.pdf');
        if (isPoster && attr === 'poster' && pdf) { element.removeAttribute('poster'); return; }
        if (isPoster && element.tagName === 'IMG' && pdf) {
          if (element.nextElementSibling?.dataset.groupPdf) return;
          const frame = document.createElement('object');
          frame.dataset.groupPdf = 'true';
          frame.type = 'application/pdf'; frame.data = url;
          frame.className = element.className;
          frame.style.cssText = 'width:100%;height:100%;min-height:350px;';
          frame.setAttribute('aria-label', element.alt || `Affiche ${group}`);
          const link = document.createElement('a');
          link.href = url; link.textContent = `Ouvrir l’affiche ${group} (PDF)`;
          frame.append(link); element.after(frame); element.hidden = true; element.style.display = 'none';
          return;
        }
        if (element.tagName === 'OBJECT' && !pdf) element.type = url.endsWith('.png') ? 'image/png' : 'image/jpeg';
        if (value === url) return;
        element.setAttribute(attr, url);
        if (element.tagName === 'SOURCE') {
          element.removeAttribute('type');
          if (attr === 'src') element.parentElement?.load?.();
        }
      });
    }
  }
  function scan(node) {
    if (node.nodeType !== 1) return;
    update(node);
    node.querySelectorAll('[src],[poster],[data],[href],[data-src],[data-trailer]').forEach(update);
  }
  const observer = new MutationObserver(records => {
    for (const record of records) {
      if (record.type === 'attributes') update(record.target);
      else record.addedNodes.forEach(scan);
    }
  });
  observer.observe(document.documentElement, { subtree: true, childList: true, attributes: true,
    attributeFilter: ['src', 'poster', 'data', 'href', 'data-src', 'data-trailer'] });
  scan(document.documentElement);
})();
