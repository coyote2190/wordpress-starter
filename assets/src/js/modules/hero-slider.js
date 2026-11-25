/**
 * Hero slider : flèches, points, défilement automatique, clavier, glisser au doigt.
 *
 * HTML attendu : blocks/hero-slider/render.php.
 * - Diapositives : [data-slide]. Celle affichée porte `data-active` (le CSS gère le fondu),
 *   les autres sont `inert` + aria-hidden (ni focus ni lecture d'écran).
 * - Commandes : [data-slider-prev], [data-slider-next], [data-slider-dot], [data-slider-pause].
 * - Réglage : data-autoplay="6" (secondes, 0 = désactivé) sur l'élément racine.
 *
 * Accessibilité :
 * - bouton pause obligatoire quand ça défile seul (WCAG 2.2.2) ;
 * - pause tant que la souris survole ou que le focus est dans le slider ;
 * - pas de défilement automatique si le visiteur a demandé à réduire les animations.
 *
 * @param {HTMLElement} root Élément qui porte data-module="hero-slider".
 */
export default function heroSlider(root) {
  const slides = [...root.querySelectorAll('[data-slide]')];
  if (slides.length < 2) return;

  const track = root.querySelector('[data-slider-track]');
  const dots = [...root.querySelectorAll('[data-slider-dot]')];
  const pauseButton = root.querySelector('[data-slider-pause]');
  const delay = Number(root.dataset.autoplay || 0) * 1000;
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  let index = 0;
  let timer = null;
  let pausedByUser = reducedMotion || !delay; // pause choisie (bouton) ou imposée
  let pausedByHover = false; // pause temporaire (survol / focus)

  /** Affiche la diapositive n (boucle aux extrémités). */
  const goTo = (n) => {
    index = (n + slides.length) % slides.length;

    slides.forEach((slide, i) => {
      const active = i === index;
      slide.toggleAttribute('data-active', active);
      slide.inert = !active;
      slide.setAttribute('aria-hidden', String(!active));
    });

    dots.forEach((dot, i) => dot.setAttribute('aria-current', String(i === index)));
  };

  const stop = () => {
    clearInterval(timer);
    timer = null;
  };

  const start = () => {
    stop();
    if (delay && !pausedByUser && !pausedByHover) {
      timer = setInterval(() => goTo(index + 1), delay);
    }
  };

  /** Navigation manuelle : change de diapositive et relance le minuteur. */
  const navigate = (n) => {
    goTo(n);
    start();
  };

  // --- Commandes ---------------------------------------------------------
  root.querySelector('[data-slider-prev]')?.addEventListener('click', () => navigate(index - 1));
  root.querySelector('[data-slider-next]')?.addEventListener('click', () => navigate(index + 1));
  dots.forEach((dot, i) => dot.addEventListener('click', () => navigate(i)));

  // Bouton pause / lecture.
  const updatePauseButton = () => {
    if (!pauseButton) return;
    pauseButton.setAttribute('aria-pressed', String(pausedByUser));
    pauseButton.querySelector('[data-slider-pause-icon]')?.toggleAttribute('hidden', pausedByUser);
    pauseButton.querySelector('[data-slider-play-icon]')?.toggleAttribute('hidden', !pausedByUser);
  };
  pauseButton?.addEventListener('click', () => {
    pausedByUser = !pausedByUser;
    updatePauseButton();
    start();
  });

  // Les lecteurs d'écran annoncent le changement seulement quand ça ne défile pas seul.
  const updateLiveRegion = () => track?.setAttribute('aria-live', timer ? 'off' : 'polite');

  // --- Pause temporaire : survol et focus clavier -------------------------
  const holdOn = () => {
    pausedByHover = true;
    stop();
    updateLiveRegion();
  };
  const holdOff = () => {
    pausedByHover = false;
    start();
    updateLiveRegion();
  };
  root.addEventListener('mouseenter', holdOn);
  root.addEventListener('mouseleave', holdOff);
  root.addEventListener('focusin', holdOn);
  root.addEventListener('focusout', (event) => {
    if (!root.contains(event.relatedTarget)) holdOff();
  });

  // --- Clavier : flèches gauche / droite quand le focus est dans le slider ---
  root.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') navigate(index - 1);
    if (event.key === 'ArrowRight') navigate(index + 1);
  });

  // --- Glisser au doigt (mobile) ------------------------------------------
  let startX = null;
  root.addEventListener('pointerdown', (event) => {
    if (event.pointerType !== 'mouse') startX = event.clientX;
  });
  root.addEventListener('pointerup', (event) => {
    if (startX === null) return;
    const distance = event.clientX - startX;
    startX = null;
    if (Math.abs(distance) > 50) navigate(distance < 0 ? index + 1 : index - 1);
  });

  // --- Démarrage -----------------------------------------------------------
  updatePauseButton();
  start();
  updateLiveRegion();
}
