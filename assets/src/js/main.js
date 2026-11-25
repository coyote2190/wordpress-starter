/**
 * Script principal, chargé sur toutes les pages (module ES, exécuté après
 * le chargement du HTML : pas besoin d'attendre DOMContentLoaded).
 *
 * Fonctionnement : chaque élément HTML qui a besoin de JS porte un attribut
 * `data-module="nom"`. Le module correspondant n'est téléchargé QUE si un tel
 * élément existe dans la page (import dynamique = fichier séparé dans dist/).
 *
 * Ajouter un module :
 *   1. créer modules/mon-module.js avec `export default function (el) { … }`
 *   2. l'ajouter dans `modules` ci-dessous
 *   3. mettre data-module="mon-module" sur l'élément HTML
 *
 * Voir docs/js.md.
 */

const modules = {
  nav: () => import('./modules/nav.js'),
};

for (const el of document.querySelectorAll('[data-module]')) {
  const name = el.dataset.module;
  const load = modules[name];

  if (!load) {
    console.warn(`[starter] Module JS inconnu : "${name}"`);
    continue;
  }

  load()
    .then(({ default: init }) => init(el))
    .catch((error) => console.error(`[starter] Erreur dans le module "${name}"`, error));
}
