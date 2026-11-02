// Motion is progressive enhancement: content stays readable if this module fails.
const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
const finePointer = matchMedia('(hover: hover) and (pointer: fine)');
const storageKey = 'nexacore-motion-paused';
let userPaused = false;
try { userPaused = localStorage.getItem(storageKey) === 'true'; } catch {}
let revealObserver, sceneObserver, scanFrame, scrollFrame, pointerFrame;
const seen = new WeakSet();
const scenes = new WeakSet();
const pendingReveals = new Set();
const observedScenes = new Set();
const enabled = () => !userPaused && !reducedMotion.matches;
const revealSelectors = [
  '.hero-copy > *', '.core-art', '.hero-summary > div',
  '.section-heading > *', '.category-card', '.feature-visual',
  '.feature-copy > *', '.step', '.faq-grid > div:first-child',
  '.faq-list details', '.cta-inner > div', '.cta-inner > .button',
  '.footer-top > div', '.page-intro', '.service-row',
  '.service-detail-grid > article > *', '.order-panel',
  '.contact-grid > div', '.legal-page > *', '.not-found > *',
].join(',');

const motionCopy = {
  en: { pause: 'Pause animations', resume: 'Resume animations', disabled: 'Animations disabled by your device preference' },
  ru: { pause: 'Приостановить анимацию', resume: 'Возобновить анимацию', disabled: 'Анимация отключена настройками устройства' },
  ky: { pause: 'Анимацияны токтотуу', resume: 'Анимацияны улантуу', disabled: 'Анимация түзүлүш жөндөөлөрү менен өчүрүлгөн' }
};
function motionLabels() {
  return motionCopy[document.documentElement.lang] || motionCopy.en;
}
function updateControls() {
  const paused = !enabled();
  const copy = motionLabels();
  document.querySelectorAll('[data-motion-toggle]').forEach(button => {
    const label = paused ? copy.resume : copy.pause;
    if (button.getAttribute('aria-label') !== label) {
      button.setAttribute('aria-label', label);
      button.title = label;
      button.innerHTML = `<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="${paused ? 'm9 5 11 7-11 7V5Z' : 'M9 5v14M15 5v14'}"/></svg>`;
    }
    button.setAttribute('aria-pressed', String(paused));
    button.disabled = reducedMotion.matches;
    if (reducedMotion.matches) button.title = copy.disabled;
  });
}

function updateState() {
  document.body.classList.toggle('motion-paused', !enabled());
  document.body.classList.toggle('motion-running', enabled() && !document.hidden);
  if (!enabled()) {
    document.querySelectorAll('.motion-reveal').forEach(element => element.classList.add('is-visible'));
    document.querySelectorAll('.core-art').forEach(element => {
      element.style.removeProperty('--pointer-x');
      element.style.removeProperty('--pointer-y');
    });
  }
  updateControls();
}

export function refreshMotion() {
  updateControls();
  for (const element of pendingReveals) {
    if (element.isConnected) continue;
    revealObserver.unobserve(element);
    pendingReveals.delete(element);
    seen.delete(element);
  }
  for (const element of observedScenes) {
    if (element.isConnected) continue;
    sceneObserver.unobserve(element);
    observedScenes.delete(element);
    scenes.delete(element);
  }
  document.querySelectorAll(revealSelectors).forEach(element => {
    if (seen.has(element)) return;
    seen.add(element);
    element.classList.add('motion-reveal');
    // Stagger siblings only, so a long catalogue never accumulates seconds of delay.
    const index = Array.prototype.indexOf.call(element.parentElement.children, element);
    element.classList.add(`motion-delay-${index % 4}`);
    if (!enabled() || !revealObserver) element.classList.add('is-visible');
    else { revealObserver.observe(element); pendingReveals.add(element); }
  });
  document.querySelectorAll('.core-art, .feature-visual').forEach(element => {
    if (scenes.has(element)) return;
    scenes.add(element);
    if (sceneObserver) { sceneObserver.observe(element); observedScenes.add(element); }
    else element.classList.add('motion-in-view');
  });
  scheduleScroll();
}

function scheduleScan() {
  if (scanFrame) return;
  scanFrame = requestAnimationFrame(() => { scanFrame = null; refreshMotion(); });
}

function scheduleScroll() {
  if (scrollFrame) return;
  scrollFrame = requestAnimationFrame(() => {
    scrollFrame = null;
    const distance = document.documentElement.scrollHeight - innerHeight;
    const progress = distance > 0 ? Math.max(0, Math.min(1, scrollY / distance)) : 0;
    document.documentElement.style.setProperty('--scroll-progress', String(progress));
    document.querySelector('.site-header')?.classList.toggle('is-scrolled', scrollY > 16);
  });
}

export function initMotion() {
  if ('IntersectionObserver' in window) {
    revealObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
        pendingReveals.delete(entry.target);
      });
    }, { threshold: 0, rootMargin: '0px 0px -24px 0px' });
    sceneObserver = new IntersectionObserver(entries => {
      entries.forEach(entry => entry.target.classList.toggle('motion-in-view', entry.isIntersecting));
    }, { threshold: 0 });
    document.documentElement.classList.add('motion-ready');
  }
  new MutationObserver(scheduleScan).observe(document.querySelector('main'), { childList: true, subtree: true });
  document.addEventListener('click', event => {
    if (!event.target.closest('[data-motion-toggle]') || reducedMotion.matches) return;
    userPaused = !userPaused;
    try { localStorage.setItem(storageKey, String(userPaused)); } catch {}
    updateState();
  });
  document.addEventListener('visibilitychange', updateState);
  reducedMotion.addEventListener('change', updateState);
  window.addEventListener('scroll', scheduleScroll, { passive: true });
  window.addEventListener('resize', scheduleScroll, { passive: true });
  document.addEventListener('pointermove', event => {
    if (!enabled() || !finePointer.matches || pointerFrame) return;
    const target = event.target.closest('.core-art, .category-card');
    if (!target) return;
    const { clientX, clientY } = event;
    pointerFrame = requestAnimationFrame(() => {
      pointerFrame = null;
      if (!target.isConnected || !enabled()) return;
      const box = target.getBoundingClientRect();
      const x = (clientX - box.left) / box.width;
      const y = (clientY - box.top) / box.height;
      target.style.setProperty('--glow-x', `${Math.round(x * 100)}%`);
      target.style.setProperty('--glow-y', `${Math.round(y * 100)}%`);
      target.style.setProperty('--pointer-x', `${((x - .5) * 14).toFixed(1)}px`);
      target.style.setProperty('--pointer-y', `${((y - .5) * 10).toFixed(1)}px`);
    });
  }, { passive: true });
  document.addEventListener('pointerout', event => {
    const target = event.target.closest('.core-art, .category-card');
    if (target && !target.contains(event.relatedTarget)) {
      target.style.removeProperty('--pointer-x');
      target.style.removeProperty('--pointer-y');
    }
  }, { passive: true });
  updateState();
  refreshMotion();
}
