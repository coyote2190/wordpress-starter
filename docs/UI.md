# Kit UI — design system réutilisable

Objectif : **un seul endroit à modifier par client**, et un rendu identique
entre composants PHP et composants React.

```
@theme (main.css)  →  couleurs, police, largeurs, rayon, ombre
        │
        ├─ Classes partagées : .btn  .field  .card  (main.css, @layer components)
        │
        ├─ Composants PHP      template-parts/components/*.php
        └─ Kit React (Base UI) assets/src/js/ui/*.jsx
```

## Adapter à un client

1. Modifier les tokens dans `@theme` (`assets/src/css/main.css`) :

   | Token                                       | Classe                                  | Exemple                          |
   | ------------------------------------------- | --------------------------------------- | -------------------------------- |
   | `--color-ink` / `canvas` / `muted` / `line` | `text-ink`, `bg-canvas`, `border-line`… | couleurs de la charte            |
   | `--font-sans`                               | `font-sans`                             | police du client                 |
   | `--radius-ui`                               | `rounded-ui`                            | `0` (carré) → `0.5rem` (arrondi) |
   | `--shadow-ui`                               | `shadow-ui`                             | ombre des popups/modales         |

2. Reporter couleurs et largeurs dans `theme.json` (éditeur Gutenberg).
3. `npm run build`, puis ouvrir la page **Styleguide** pour tout vérifier d'un coup.

Page Styleguide : créer une page (hors menu) avec le modèle « Styleguide ».
Elle affiche couleurs, typographie, composants PHP et tout le kit React.

## Règles

- **Pas de couleur Tailwind brute** (`gray-200`, `blue-600`…) dans les composants :
  uniquement les tokens (`ink`, `canvas`, `muted`, `line`). Sinon le composant
  ne suit plus la charte du client.
- **Arrondis : `rounded-ui`**, ombres : `shadow-ui`.
- **Un style répété à plusieurs endroits** (PHP et/ou React) → une classe partagée
  dans `@layer components` de `main.css`, comme `.btn`, `.field`, `.card`.

## Kit React (Base UI)

[Base UI](https://base-ui.com) (`@base-ui/react`) fournit le **comportement**
(clavier, focus, ARIA, positionnement) mais **aucun style** : les composants
du kit l'habillent avec les tokens.

| Composant     | Usage                                                                                                   |
| ------------- | ------------------------------------------------------------------------------------------------------- |
| `Checkbox`    | `<Checkbox checked={v} onCheckedChange={setV}>Libellé</Checkbox>`                                       |
| `Slider`      | `<Slider label="Pages" min={1} max={30} value={n} onValueChange={setN} format={(v) => v + ' pages'} />` |
| `NumberField` | `<NumberField label="Quantité" min={1} value={q} onValueChange={setQ} />`                               |
| `Select` ⚠️   | `<Select label="Formule" items={[{ label, value }]} value={v} onValueChange={setV} />`                  |
| `Accordion`   | `<Accordion items={[{ title, content }]} />`                                                            |
| `Dialog` ⚠️   | `<Dialog trigger="Ouvrir" title="Titre">contenu</Dialog>`                                               |

```jsx
import { Checkbox, Slider } from '../ui';
```

⚠️ `Select` et `Dialog` s'ouvrent dans un portal (`document.body`) : ne pas les
utiliser dans un composant qui sert aussi d'aperçu dans l'éditeur Gutenberg
(l'éditeur est dans une iframe). C'est pour ça que le simulateur n'utilise
que `Checkbox` et `Slider`.

### Ajouter un composant au kit

1. Ouvrir la doc du composant — aussi disponible hors ligne dans
   `node_modules/@base-ui/react/docs/react/components/<nom>.md`.
2. Copier l'exemple **Tailwind** dans `assets/src/js/ui/<Nom>.jsx`.
3. Remplacer les couleurs brutes par les tokens :
   `neutral-950` → `ink`, `white` → `canvas`, `neutral-500` → `muted`,
   `neutral-200` → `line`, `hover:bg-neutral-100` → `hover:bg-line/50` ;
   supprimer les classes `dark:` ; ajouter `rounded-ui` / `shadow-ui`.
4. Exposer des props simples (`label`, `items`, `value`, `onValueChange`…).
5. L'exporter dans `assets/src/js/ui/index.js` et l'ajouter à `islands/UiDemo.jsx`.

### Poids

Base UI est découpé par composant : seuls ceux importés sont chargés,
et uniquement sur les pages qui contiennent un îlot. Le simulateur
(Checkbox + Slider) ajoute ~17 ko gzip à React.
