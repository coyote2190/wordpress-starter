# React dans le starter

Le thème reste un thème PHP classique (compatible mutualisé, sans Node en prod).
React sert à deux choses :

|               | **A. Blocs Gutenberg**                 | **B. Îlots React**                          |
| ------------- | -------------------------------------- | ------------------------------------------- |
| Où            | Éditeur WordPress                      | Front du site                               |
| React utilisé | Celui de WordPress (`wp.element`)      | Bundlé par Vite, chargé à la demande        |
| Build         | `@wordpress/scripts` → `blocks/build/` | Vite → `assets/dist/chunks/`                |
| Rendu front   | `render.php` (PHP)                     | Composant monté dans un `<div data-island>` |

L'exemple **Simulateur de devis** combine les deux : le bloc `starter/simulator`
se configure dans l'éditeur (A) et son rendu front est un îlot React (B).
Le même composant `Simulator.jsx` sert d'aperçu dans l'éditeur et en front.

## Commandes

```bash
npm run dev          # Vite (front + îlots) avec HMR
npm run dev:blocks   # watch des blocs Gutenberg (à lancer en parallèle si besoin)
npm run build        # build complet : Vite + blocs
```

`assets/dist/` et `blocks/build/` sont versionnés (pas de Node en production).

## B. Îlots React (front)

### Utilisation dans un template PHP

```php
starter_island('simulator', [
  'title' => 'Estimez votre projet',
  'basePrice' => 1500,
  'vatRate' => 20,
  'options' => [['label' => 'Blog', 'price' => 400], ['label' => 'Multilingue', 'price' => 600]],
  'ctaText' => 'Demander un devis',
  'ctaUrl' => home_url('/contact'),
]);
```

Les props sont sérialisées en JSON dans `data-props`. Un 3ᵉ argument optionnel
permet de passer un HTML de repli (SEO / sans JS), remplacé au montage.

Les props peuvent venir de SCF/ACF, d'un CPT, d'une option… :

```php
starter_island('simulator', [
  'basePrice' => (float) starter_field('base_price'),
  // ...
]);
```

### Créer un nouvel îlot

1. Créer le composant : `assets/src/js/islands/MonComposant.jsx`
2. L'ajouter au registre dans `assets/src/js/islands/index.jsx` :
   ```js
   const registry = {
     simulator: () => import('./Simulator.jsx'),
     'mon-composant': () => import('./MonComposant.jsx'),
   };
   ```
3. L'afficher : `starter_island('mon-composant', [...])`
4. Styles : classes Tailwind directement dans le JSX (Tailwind scanne aussi les `.jsx`).
   Pas d'import CSS dans le JSX — le build produit un seul `main.css`.

React (~68 ko gzip) n'est téléchargé que sur les pages qui contiennent un îlot.

## A. Blocs Gutenberg

Un bloc = un dossier dans `blocks/src/` :

```text
blocks/src/simulator/
├─ block.json   # nom, attributs, valeurs par défaut
├─ index.js     # registerBlockType
├─ edit.js      # interface d'édition (React)
└─ render.php   # rendu front (PHP)
```

Tous les blocs compilés dans `blocks/build/` sont enregistrés automatiquement
par `inc/blocks.php`. Pour en créer un : dupliquer `simulator/`, changer `name`
dans `block.json`, puis `npm run build:blocks`.

Blocs **dynamiques** (`render.php`, pas de `save()`) : on peut changer le markup
sans invalider les blocs déjà enregistrés en base.

Les styles du thème sont chargés dans l'éditeur via `add_editor_style()`
(depuis le build : relancer `npm run build` pour les mettre à jour).

## Prérequis

- WordPress **6.6+** (les blocs utilisent les scripts `react` et `react-jsx-runtime` fournis par WP)
- Node.js 20+ en local
