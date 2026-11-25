# Couleurs, polices, largeurs — `theme.json` + Tailwind

## Le principe : une seule source de vérité

```
theme.json  (palette, polices, tailles, largeurs)
   │
   │  WordPress génère des variables CSS, dans le front ET dans l'éditeur :
   │  --wp--preset--color--primary: #0f4c81;
   ▼
assets/src/css/tokens.css
   │  @theme inline { --color-primary: var(--wp--preset--color--primary); }
   ▼
Classes Tailwind : bg-primary, text-primary/80, border-primary, font-heading…
```

On change une couleur **dans `theme.json` uniquement**. Le site, Tailwind et la palette de l'éditeur Gutenberg suivent.

## `theme.json` : ce qu'il fait ici

| Réglage | Valeur | Effet |
|---|---|---|
| `color.palette` | 5 couleurs du client | Les seules couleurs proposées dans l'éditeur |
| `color.custom: false` | — | Le client ne peut pas choisir une couleur libre (respect de la charte) |
| `defaultPalette`, `defaultGradients`, `defaultDuotone: false` | — | Retire les couleurs WordPress par défaut (rose pâle, violet vif…) |
| `typography.fontFamilies` | `sans`, `heading` | Polices du texte et des titres |
| `typography.fontSizes` | 4 tailles | Tailles proposées dans l'éditeur, pas de taille libre |
| `layout.contentSize` / `wideSize` | 48rem / 80rem | Largeur du texte et des blocs « large ». Utilisées aussi par la classe `container`. |
| `appearanceTools: false` | — | Masque bordures, marges, etc. dans l'éditeur : le client ne peut pas casser la mise en page |
| `styles` | fond blanc, texte foncé | Styles appliqués au `<body>` (front et éditeur) |

> Les variables des couleurs WordPress par défaut restent dans le CSS : WordPress les garde pour la compatibilité. Elles ne sont plus proposées dans l'éditeur et aucune classe ne les utilise.

## Fichiers CSS

| Fichier | Rôle |
|---|---|
| `assets/src/css/main.css` | Entrée : importe Tailwind, déclare les `@source`, importe le reste |
| `assets/src/css/tokens.css` | Fait le lien entre les variables `theme.json` et Tailwind |
| `assets/src/css/base.css` | Styles de base (titres, lien d'évitement) + utilitaire `container` |

## Classes disponibles

| Type | Classes |
|---|---|
| Couleurs | `primary`, `secondary`, `dark`, `light`, `white` → `bg-primary`, `text-dark`, `border-light`… Transparence possible : `bg-primary/10` |
| Polices | `font-sans`, `font-heading` |
| Largeurs | `max-w-content`, `max-w-wide` |
| Conteneur | `container` (centré, marges latérales, largeur `wide`) |
| Contenu Gutenberg | `prose` (plugin `@tailwindcss/typography`) : met en forme titres, listes, liens… du contenu saisi dans l'éditeur |

Les couleurs Tailwind par défaut (`gray-500`, `red-600`…) restent disponibles pour les détails (bordures, états d'erreur…).

## Recettes

### Changer les couleurs pour un nouveau client
Modifier les valeurs `color` dans `theme.json > settings.color.palette`. Rien d'autre.

### Ajouter une couleur
1. `theme.json` : `{ "slug": "accent", "name": "Accent", "color": "#e11d48" }`
2. `tokens.css` : `--color-accent: var(--wp--preset--color--accent);`
3. `npm run build`

### Utiliser une police Google (hébergée sur le site, conforme RGPD)
1. Télécharger les fichiers `.woff2` (ex. sur google-webfonts-helper) dans `assets/fonts/`.
2. Dans `theme.json`, sur la police concernée, ajouter `fontFace` :
   ```json
   {
     "slug": "heading",
     "name": "Titres",
     "fontFamily": "'Outfit', sans-serif",
     "fontFace": [
       {
         "fontFamily": "Outfit",
         "fontWeight": "400 700",
         "fontStyle": "normal",
         "fontDisplay": "swap",
         "src": ["file:./assets/fonts/outfit-variable.woff2"]
       }
     ]
   }
   ```
   WordPress génère le `@font-face` lui-même, dans le front et l'éditeur.
