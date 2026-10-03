import '@vitejs/plugin-react/preamble'; // HMR React en dev (vide en build)
import '../css/main.css';

/**
 * Menu mobile
 *
 * Le <nav> porte `hidden md:block` : masqué en mobile, visible dès `md`.
 * En mobile, on ajoute/retire `hidden` au clic. En desktop, `md:block`
 * l'emporte donc le nav reste visible quoi qu'il arrive.
 */
function initMobileNav() {
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');

  if (!toggle || !nav) return;

  const close = () => {
    toggle.setAttribute('aria-expanded', 'false');
    nav.classList.add('hidden');
  };

  const open = () => {
    toggle.setAttribute('aria-expanded', 'true');
    nav.classList.remove('hidden');
  };

  toggle.addEventListener('click', () => {
    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
    isOpen ? close() : open();
  });

  // Fermeture à la touche Échap
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });

  // Retour vers desktop : on remet l'état fermé (md:block garde le nav visible)
  const mq = window.matchMedia('(min-width: 48rem)');
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
