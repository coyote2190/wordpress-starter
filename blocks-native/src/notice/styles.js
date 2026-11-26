/**
 * Classes Tailwind de chaque variante, partagées par l'éditeur (edit.js).
 * render.php a sa propre copie : les deux doivent rester identiques.
 *
 * `className` : couleur de l'étiquette à gauche du message.
 * `icon`      : affichée dans l'étiquette quand son texte est vide.
 *
 * Les classes sont écrites EN ENTIER pour que Tailwind les détecte.
 */
export const VARIANTS = {
  info: { label: 'Information', icon: 'ℹ️', className: 'bg-secondary text-dark' },
  success: { label: 'Succès', icon: '✅', className: 'bg-green-100 text-green-900' },
  warning: { label: 'Attention', icon: '⚠️', className: 'bg-amber-100 text-amber-900' },
};

/** Classes communes (pilule blanche centrée), aussi recopiées dans render.php. */
export const WRAPPER = 'flex justify-center my-8 not-prose';
export const INNER =
  'bg-white border border-light flex gap-3 items-center leading-snug pl-2 pr-5 py-2 rounded-3xl sm:rounded-full text-base';
export const TAG = 'font-semibold px-3 py-1.5 rounded-full shrink-0 text-[13px]';
