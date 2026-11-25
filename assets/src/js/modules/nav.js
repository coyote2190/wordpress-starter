/**
 * Menu mobile : ouvre / ferme le menu avec le bouton burger.
 *
 * HTML attendu (voir header.php) :
 *   <header data-module="nav">
 *     <button data-nav-toggle aria-expanded="false" aria-controls="primary-menu">
 *       <span data-nav-icon-open>…</span>
 *       <span data-nav-icon-close hidden>…</span>
 *     </button>
 *     <nav id="primary-menu" data-nav-menu>…</nav>
 *   </header>
 *
 * L'état ouvert est porté par l'attribut `data-open` sur <nav>. Le CSS
 * (classe Tailwind `max-md:data-open:block`) se charge de l'affichage.
 *
 * @param {HTMLElement} root Élément qui porte data-module="nav".
 */
export default function nav(root) {
  const toggle = root.querySelector('[data-nav-toggle]');
  const menu = root.querySelector('[data-nav-menu]');
  if (!toggle || !menu) return;

  const iconOpen = toggle.querySelector('[data-nav-icon-open]');
  const iconClose = toggle.querySelector('[data-nav-icon-close]');
  const desktop = window.matchMedia('(min-width: 48rem)'); // = breakpoint md de Tailwind

  const setOpen = (open) => {
    toggle.setAttribute('aria-expanded', String(open));
    menu.toggleAttribute('data-open', open);
    iconOpen?.toggleAttribute('hidden', open);
    iconClose?.toggleAttribute('hidden', !open);
  };

  const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';

  toggle.addEventListener('click', () => setOpen(!isOpen()));

  // Échap : ferme et rend le focus au bouton (accessibilité clavier).
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && isOpen()) {
      setOpen(false);
      toggle.focus();
    }
  });

  // Clic sur un lien (ex. ancre #contact) : on ferme le menu.
  menu.addEventListener('click', (event) => {
    if (event.target.closest('a')) setOpen(false);
  });

  // Passage en affichage desktop : on remet à zéro.
  desktop.addEventListener('change', (event) => {
    if (event.matches) setOpen(false);
  });
}
