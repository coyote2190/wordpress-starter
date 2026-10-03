import '@vitejs/plugin-react/preamble'; // HMR React en dev (vide en build)
import '../scss/main.scss';

/**
 * Menu mobile
 */
function initMobileNav() {
  const toggle = document.querySelector('.site-header__toggle');
  const nav = document.querySelector('.site-nav');

  if (!toggle || !nav) return;

  const close = () => {
    toggle.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('nav-open');
  };

  toggle.addEventListener('click', () => {
    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!isOpen));
    document.body.classList.toggle('nav-open', !isOpen);
  });

  // Fermeture à la touche Échap
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });

  // Fermeture au redimensionnement vers desktop
  const mq = window.matchMedia('(min-width: 768px)');
  mq.addEventListener('change', (e) => {
    if (e.matches) close();
  });
}

/**
 * Îlots React — chargés uniquement si la page en contient
 */
function initIslands() {
  const islands = document.querySelectorAll('[data-island]');
  if (!islands.length) return;

  import('./islands/index.jsx').then(({ mountIslands }) => mountIslands(islands));
}

document.addEventListener('DOMContentLoaded', () => {
  console.log('WordPress Starter theme loaded');
  initMobileNav();
  initIslands();
});
