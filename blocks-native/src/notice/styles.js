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
export const WRAPPER = 'not-prose my-8 flex justify-center';
export const INNER =
  'flex items-center gap-3 rounded-3xl border border-light bg-white py-2 pr-5 pl-2 text-base leading-snug sm:rounded-full';
export const TAG = 'shrink-0 rounded-full px-3 py-1.5 text-[13px] font-semibold';
