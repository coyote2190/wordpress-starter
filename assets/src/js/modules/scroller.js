/**
 * Défilement horizontal de cartes (générique, réutilisable par tout bloc).
 *
 * Le défilement lui-même est NATIF (overflow-x + scroll-snap) : il marche au doigt,
 * au trackpad et au clavier sans JS. Ce module ajoute seulement les boutons
 * précédent / suivant, désactivés aux extrémités.
 *
 * HTML attendu :
 *   <div data-module="scroller">
 *     <div data-scroller-track> …cartes… </div>
 *     <button data-scroller-prev>…</button>
 *     <button data-scroller-next>…</button>
 *   </div>
 *
 * @param {HTMLElement} root Élément qui porte data-module="scroller".
 */
export default function scroller(root) {
  const track = root.querySelector('[data-scroller-track]');
  const prev = root.querySelector('[data-scroller-prev]');
  const next = root.querySelector('[data-scroller-next]');
  if (!track) return;

  /** Distance d'un « cran » : largeur d'une carte + espacement. */
  const step = () => {
    const card = track.firstElementChild;
    if (!card) return track.clientWidth;
    const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
    return card.getBoundingClientRect().width + gap;
  };

  const update = () => {
    const max = track.scrollWidth - track.clientWidth - 1;
    prev?.toggleAttribute('disabled', track.scrollLeft <= 0);
    next?.toggleAttribute('disabled', track.scrollLeft >= max);
  };

  // Défilement animé, sauf si le visiteur a demandé à réduire les animations.
  const behavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';

  prev?.addEventListener('click', () => track.scrollBy({ left: -step(), behavior }));
  next?.addEventListener('click', () => track.scrollBy({ left: step(), behavior }));
  track.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update);

  update();
}
