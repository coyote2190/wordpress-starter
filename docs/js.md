# JavaScript — `assets/src/js/`

## Principes

- **JS natif uniquement**, pas de jQuery ni de framework côté front.
- Chargé en **module ES** (`type="module"`) : `import` / `export` natifs, exécuté une fois le HTML lu (inutile d'attendre `DOMContentLoaded`).
- **Un module = une fonctionnalité = un fichier** dans `modules/`.
- Un module n'est **téléchargé que si la page en a besoin**.

## Comment ça marche

```
HTML (PHP)                              assets/src/js/main.js
──────────                              ─────────────────────
<header data-module="nav">   ──────►    cherche tous les [data-module]
                                        "nav" → import('./modules/nav.js')
                                                    │
                                                    ▼
                                        modules/nav.js : export default function (el) { … }
                                        reçoit l'élément <header> et s'y branche
```

Au build, Vite crée un fichier séparé par module (`dist/assets/nav-xxxx.js`). Une page sans `data-module="nav"` ne le télécharge jamais.

## Les fichiers

| Fichier | Rôle |
|---|---|
| `main.js` | Point d'entrée. Contient la liste `modules` (nom → fichier) et lance ceux présents dans la page. |
| `modules/nav.js` | Menu mobile : bouton burger, `aria-expanded`, Échap pour fermer, fermeture au clic sur un lien ou au passage en desktop. |
| `modules/hero-slider.js` | Hero slider : fondu, flèches, points, autoplay avec pause, clavier, glisser au doigt (voir `docs/blocks.md`). |
| `modules/contact-form.js` | Envoi du formulaire de contact en `fetch` (voir `docs/contact-form.md`). |

## Écrire un module

```js
// assets/src/js/modules/accordion.js
/**
 * @param {HTMLElement} root Élément qui porte data-module="accordion".
 */
export default function accordion(root) {
  root.querySelectorAll('[data-accordion-trigger]').forEach((button) => {
    button.addEventListener('click', () => { /* … */ });
  });
}
```

1. Créer le fichier dans `modules/`.
2. L'ajouter dans `main.js` : `accordion: () => import('./modules/accordion.js'),`
3. Dans le HTML : `<div data-module="accordion">…</div>`.

### Conventions

| Règle | Pourquoi |
|---|---|
| Le module ne cherche des éléments **qu'à l'intérieur de `root`** | Plusieurs instances sur la même page ne se marchent pas dessus |
| Sélecteurs JS en `data-*` (`data-nav-toggle`), jamais en classes | On peut changer les classes Tailwind sans casser le JS |
| L'état est porté par un attribut (`data-open`, `aria-expanded`, `hidden`) | Le CSS réagit avec `data-open:block` ; les lecteurs d'écran suivent l'état |
| Toujours gérer le clavier (Échap, focus) | Accessibilité |

## Passer des données de PHP à JS

Via des attributs `data-*` sur l'élément :

```php
<div data-module="map" data-lat="<?php echo esc_attr( $lat ); ?>" data-lng="<?php echo esc_attr( $lng ); ?>"></div>
```
```js
export default function map(root) {
  const { lat, lng } = root.dataset; // chaînes de caractères
}
```

## Ajouter une librairie (slider, lightbox…)

```bash
npm install swiper
```
Puis l'importer **dans le module qui l'utilise** (pas dans `main.js`) : elle ne sera téléchargée que sur les pages concernées.
