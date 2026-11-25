# Blocs Gutenberg avec SCF — `blocks/` + `inc/blocks.php`

Un bloc SCF = un bloc Gutenberg dont **les champs sont des champs SCF** et dont **le rendu est un fichier PHP**. Pas de React, pas de build : le même `render.php` sert au site et à l'aperçu dans l'éditeur.

## Anatomie d'un bloc

```
blocks/hero/
├── block.json    Carte d'identité du bloc (nom, titre, icône, options…)
└── render.php    HTML du bloc (site + aperçu éditeur)

scf-json/group_starter_block_hero.json    Champs du bloc (emplacement : Bloc = Hero)
```

### `block.json`

```jsonc
{
  "apiVersion": 3,
  "name": "starter/hero",            // identifiant unique : starter/{nom-du-dossier}
  "title": "Hero",                    // nom affiché dans l'éditeur
  "category": "starter",              // catégorie créée dans inc/blocks.php
  "icon": "cover-image",              // un Dashicon : developer.wordpress.org/resource/dashicons
  "supports": {
    "align": ["full"],                // alignements proposés (wide, full)
    "anchor": true,                   // champ « Ancre HTML » (id) → liens #ancre
    "html": false                     // pas de « Modifier en HTML »
  },
  "attributes": { "align": { "type": "string", "default": "full" } },  // pleine largeur par défaut
  "acf": {
    "blockVersion": 3,
    "mode": "preview",                // affiche l'aperçu, les champs sont dans le panneau de droite
    "renderTemplate": "render.php"
  }
}
```

C'est le **même format** que pour un bloc natif React (voir `docs/native-blocks.md`). Seule la clé `acf` est propre à SCF.

### `render.php`

Variables disponibles : `$block` (réglages), `$is_preview` (true dans l'éditeur), `$post_id`, `$content`.

Les points importants, tous présents dans `blocks/hero/render.php` :

| Point | Pourquoi |
|---|---|
| `starter_field( 'title' )` | Lit le champ **de ce bloc** (dans un bloc, `get_field()` lit les données du bloc, pas de la page) |
| Message si vide et `$is_preview` | Le rédacteur sait quoi faire au lieu de voir un cadre vide |
| `get_block_wrapper_attributes()` | Ajoute ce que WordPress gère : classe `wp-block-starter-hero`, `alignfull`, ancre `id`, « Classes CSS additionnelles » |
| Classe `not-prose` | Le bloc ignore la mise en forme du texte (`prose`) des pages |
| `wp_get_attachment_image( $id, … )` | Image responsive (`srcset`) avec `alt`. Le champ Image doit retourner un **ID**. |
| Échappement (`esc_html`, `esc_url`) | Toujours |

## Blocs fournis

| Bloc | Dossier | Usage |
|---|---|---|
| **Hero** | `blocks/hero/` | Bannière d'en-tête : titre, texte, bouton, image de fond |
| **Cartes** | `blocks/features/` | Grille de cartes : services, avantages, valeurs… |
| **Formulaire de contact** | `blocks/contact-form/` | Place le formulaire (voir `docs/contact-form.md`) |

### Le bloc « Cartes » en détail

| Réglage | Champ | Choix |
|---|---|---|
| Titre, introduction | `title`, `intro` | Facultatifs, centrés au-dessus de la grille |
| Colonnes | `columns` | 2, 3 ou 4 sur grand écran (toujours 1 sur mobile, 2 sur tablette) |
| Style | `style` | Bordure · Fond clair · Ombre · Bordure colorée en haut |
| Cartes (répéteur, 1 à 12) | `items` | Pour chaque carte : **icône**, **couleur**, titre, texte, lien |

- **Icône** et **couleur** sont des champs nommés `icon` et `color` : leurs choix sont remplis automatiquement depuis `assets/icons/` et `theme.json` (voir `docs/scf.md`).
- La couleur est passée en **variable CSS** sur la carte (`style="--card-color: var(--wp--preset--color--primary)"`), utilisée par les classes Tailwind `bg-(--card-color)/10`, `text-(--card-color)`, `border-t-(--card-color)`. Toute couleur ajoutée dans `theme.json` fonctionne sans toucher au bloc.
- Avec un lien, **toute la carte est cliquable** (`after:absolute after:inset-0` sur le lien) et se soulève au survol.

**Ajouter un style de carte** : ajouter le choix dans le champ `style` (SCF), puis la ligne correspondante dans `$card_classes` de `render.php`.

## Ce que fait `inc/blocks.php`

| Fonction | Rôle |
|---|---|
| `starter_register_blocks()` | Enregistre automatiquement **chaque dossier** `blocks/*/block.json`. Créer le dossier suffit. |
| `starter_block_category()` | Catégorie « Starter » en tête de l'outil d'insertion |
| `starter_editor_styles()` | Charge `editor.css` (Tailwind sans reset) **dans l'iframe** de l'éditeur : l'aperçu ressemble au site |

## Créer un bloc (exemple : « Chiffres clés »)

1. Copier `blocks/hero/` en `blocks/key-figures/`.
2. `block.json` : changer `name` (`starter/key-figures`), `title`, `description`, `icon`, `supports`.
3. Admin > SCF > Groupes de champs > Ajouter. Emplacement : **Bloc** est égal à **Chiffres clés**. Enregistrer → JSON créé dans `scf-json/`.
4. Écrire `render.php` avec des classes Tailwind.
5. `npm run dev` est déjà configuré pour lire `blocks/` (voir `assets/src/css/sources.css`).
6. Commiter le dossier du bloc **et** le JSON.

## Le CSS dans l'éditeur

| Fichier | Chargé où | Contenu |
|---|---|---|
| `main.css` | Site | Tailwind complet (avec reset « preflight ») + composants |
| `editor.css` | Iframe de l'éditeur | Variables + classes Tailwind, **sans** le reset (qui casserait les blocs natifs) |
| `sources.css` | Importé par les deux | Liste des dossiers scannés par Tailwind |

## Aller plus loin

- **Blocs enfants** (le rédacteur ajoute des blocs à l'intérieur) : `<InnerBlocks />` dans `render.php` + `"jsx": true` dans `supports`. Voir la doc ACF « ACF Blocks with InnerBlocks ».
- **Limiter les blocs disponibles** pour le client : filtre `allowed_block_types_all`.
- **Bloc natif React** : voir `docs/native-blocks.md`.
