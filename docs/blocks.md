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
    "align": ["wide", "full"],        // alignements proposés (voir « Alignements » ci-dessous)
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
| `starter_block_inner_class( $block['align'] )` | Conteneur intérieur en pleine largeur (règle commune des alignements) |
| `get_block_wrapper_attributes()` | Ajoute ce que WordPress gère : classe `wp-block-starter-hero`, `alignfull`, ancre `id`, « Classes CSS additionnelles » |
| Classe `not-prose` | Le bloc ignore la mise en forme du texte (`prose`) des pages |
| `no-underline` sur les liens stylés (boutons, liens de carte) | L'éditeur souligne tous les liens par défaut : sans ça, l'aperçu diffère du site |
| `wp_get_attachment_image( $id, … )` | Image responsive (`srcset`) avec `alt`. Le champ Image doit retourner un **ID**. |
| Échappement (`esc_html`, `esc_url`) | Toujours |

## Alignements : la règle commune

Tous les blocs du thème proposent **Aucun · Grande largeur · Pleine largeur** (`"supports": { "align": ["wide", "full"] }`).

| Alignement | Largeur du bloc | Contenu du bloc |
|---|---|---|
| Aucun | Colonne de texte (`theme.json` → `contentSize`, 48rem) | Pleine largeur du bloc |
| Grande largeur | `wideSize` (80rem) | Pleine largeur du bloc |
| Pleine largeur | Tout l'écran | Dans un `container` : **le fond couvre l'écran, le contenu reste aligné sur la grille du site** |

Dans `render.php`, une seule ligne applique cette règle :

```php
<div class="<?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
    … contenu du bloc …
</div>
```

`starter_block_inner_class()` (dans `inc/blocks.php`) renvoie `container` en pleine largeur, rien sinon. Côté React : `attributes.align === 'full'` (voir `blocks-native/src/notice/edit.js`).

| Réglage | Où |
|---|---|
| Alignements proposés | `supports.align` dans `block.json` (« Aucun » est toujours proposé) |
| Alignement à l'insertion | `attributes.align.default` dans `block.json` (Hero : `full`, Cartes : `wide`) |
| Valeurs 48rem / 80rem | `settings.layout` dans `theme.json` |
| Sortie de la colonne sur le site | `.entry-content > .alignwide / .alignfull` dans `components.css` |

> Un bloc qui a un alignement par défaut et qu'on passe sur « Aucun » reçoit `align: ""` (WordPress ajoute alors une classe `align` vide, sans effet).

## Blocs fournis

| Bloc | Dossier | Usage |
|---|---|---|
| **Hero** | `blocks/hero/` | En-tête de page : grand titre (`**mot**` surligné), texte, bouton, puis grande image arrondie (facultative) |
| **Hero slider** | `blocks/hero-slider/` | En-tête de page : grand titre, texte, boutons, puis diaporama d'images arrondi (un seul par page) |
| **Cartes** | `blocks/features/` | Grille de cartes : services, avantages, valeurs… |
| **Appel à l'action** | `blocks/cta/` | Grand titre, bouton ou champ e-mail, avatars en pile ou flottants |
| **Pourquoi nous** | `blocks/why/` | Grand titre + texte à gauche, arguments à droite : liste à filets ou cartes défilantes |
| **Bento** | `blocks/bento/` | Mosaïque de tuiles de tailles variées : photos, aplats de couleur, chiffres clés |
| **Formulaire de contact** | `blocks/contact-form/` | Coordonnées de « Infos du site » en cartes + formulaire dans une carte blanche (voir `docs/contact-form.md`) |

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

### Le bloc « Appel à l'action » en détail

| Réglage | Champ | Détail |
|---|---|---|
| Titre | `title` | Syntaxe de mise en valeur : `**mot**` = **surligné** (pastille couleur d'accent), `__mot__` = souligné ondulé. Ex. `Lancez **votre projet** __aujourd'hui__` |
| Accroche | `text` | |
| Action | `action` | **Bouton** (lien) ou **Champ e-mail + bouton** |
| Bouton | `button` | Texte du bouton et page de destination |
| Couleurs | `background`, `accent` | Palette `theme.json`. Contraste du texte automatique (`starter_color_is_light()`) |
| Personnes | `people` (0 à 4) | Photo + prénom, décoratives (`aria-hidden`) |
| Présentation des personnes | `people_layout` | **En pile** au-dessus du titre (avatars qui se chevauchent) · **Flottantes** autour du titre sur grand écran (sans animation si « réduire les animations » est activé) |

**Le champ e-mail sans service de newsletter** : c'est un formulaire `GET` vers la page du bouton (ex. `/contact/?email=jean@exemple.fr`). Le formulaire de contact reprend l'adresse (`template-parts/contact-form.php`). Pour une vraie newsletter (Brevo, Mailchimp…), remplacer l'`action` du formulaire par l'URL d'inscription du service.

**Sécurité de la syntaxe** : `starter_format_title()` (`inc/blocks.php`) échappe tout le texte **avant** d'ajouter ses balises `<mark>` / `<span>` : un client ne peut pas injecter de HTML.

### Le bloc « Pourquoi nous » en détail

Intro à gauche, arguments à droite, en deux présentations (champ `layout`) :

- **Liste** (par défaut) : arguments les uns sous les autres, séparés par un filet — icône, surtitre (`badge`), titre, texte. Les icônes bicolores (`bolt`, `layers`, `circles`) prennent la couleur de l'argument ;
- **Cartes défilantes** : en pleine largeur, l'intro est alignée sur la grille du site et les cartes défilent **jusqu'au bord droit de l'écran**.

| Réglage | Champ | Détail |
|---|---|---|
| Titre | `title` | **Une ligne par retour à la ligne** (ex. « Rapides. / Simples. / Sereins. »), avec `**surligné**` / `__souligné__` |
| Texte | `text` | |
| Action | `action` | Aucune · Bouton · Champ e-mail + bouton (même principe que l'Appel à l'action : `?email=` vers la page cible) |
| Cartes (1 à 10) | `cards` | Pastille, couleur, titre, texte, **icône** (grande, sur fond pastel de la couleur) ou **photo / illustration**, lien (bouton ↗, carte cliquable) |

Défilement (cartes défilantes) :
- **natif** (`overflow-x-auto` + `scroll-snap`) : au doigt, au trackpad, au clavier (la piste est focusable) — fonctionne sans JS ;
- boutons ← → sous l'intro sur grand écran, via le module générique **`scroller`** (`assets/src/js/modules/scroller.js`), réutilisable pour tout futur carrousel ;
- barre de défilement masquée, aimantation sur chaque carte.

### Le bloc « Bento » en détail

Grille de **4 colonnes** sur grand écran, 2 sur tablette, 1 sur mobile. Les tuiles se placent dans l'ordre et `grid-flow-dense` comble les trous.

| Réglage de chaque tuile | Choix |
|---|---|
| Type | **Couleur** (aplat) · **Photo** (avec dégradé de la couleur choisie en bas, pour la lisibilité) |
| Largeur | ¼ · ½ · ¾ · toute la ligne |
| Hauteur | Normale · Double (à partir de la tablette) |
| Couleur | Palette `theme.json` (champ `color`) : fond de la tuile ou teinte du dégradé |
| Pastille, chiffre clé, titre, texte | Tous facultatifs (une tuile « chiffre » n'a souvent pas de titre) |
| Lien | Bouton rond ↗ en haut à droite, **toute la tuile cliquable** |

**Contraste automatique** : sur une tuile Couleur, le texte est foncé si la couleur est claire, blanc sinon. `starter_color_is_light()` (`inc/scf.php`) calcule la luminance de la couleur dans `theme.json` : si le client change sa palette, le texte reste lisible sans rien toucher.

Exemple de composition (la démo) : `½ double` photo + `¼` chiffre + `¼` couleur, puis `½` photo sous les deux petites.

### Le bloc « Hero slider » en détail

Grand titre (le `<h1>` de la page) à gauche, texte et boutons à droite, puis un diaporama d'images aux coins arrondis avec une pilule de commandes (points, pause, flèches) en bas à droite.

| Réglage | Champ | Détail |
|---|---|---|
| Titre | `title` | `**mot**` = mot surligné (pastille lavande, classe `title-mark`) |
| Texte | `text` | |
| Bouton principal | `button_primary` | Pilule foncée avec « + » (`starter_button( $link, 'icon', 'plus' )`) |
| Lien secondaire | `button_secondary` | Lien souligné |
| Images (1 à 8) | `slides` | Texte alternatif réglé dans la médiathèque |
| Défilement auto | `autoplay` | En secondes, 0 = désactivé |

Comportement (module `assets/src/js/modules/hero-slider.js`, chargé seulement s'il y a 2 images ou plus) :

- fondu entre images : l'attribut `data-active` + classes `data-active:visible data-active:opacity-100` ;
- points, flèches, flèches du clavier, glisser au doigt (`touch-pan-y`) ;
- **accessibilité** : images cachées en `inert`, bouton **pause** obligatoire quand ça défile seul (WCAG 2.2.2), pause au survol et au focus clavier, **pas de défilement auto** si « réduire les animations » est activé ni dans l'éditeur ;
- **performance** : 1re image en `fetchpriority="high"` + `loading="eager"`, les suivantes en différé ;
- sans JS : la 1re image s'affiche normalement.

## Boutons : `starter_button()`

`starter_button( $link, $variant, $icon, $default )` (`inc/blocks.php`) affiche un champ Lien SCF en bouton du thème :

| Variante | Rendu |
|---|---|
| `icon` | Pilule foncée, icône dans un rond à gauche (« + Démarrer un projet ») |
| `simple` | Pilule foncée, texte + petite icône à droite (« C'est parti → ») |
| `link` | Lien souligné sans fond |

`starter_link_target( $link )` renvoie `target="_blank" rel="noopener"` si le lien s'ouvre dans un nouvel onglet.

## Page qui commence par un hero

Quand une **page** commence par un **Hero** ou un **Hero slider**, `page.php` n'affiche pas son propre en-tête : le bloc porte déjà le `<h1>`. Détection : `starter_starts_with_hero()` (`inc/blocks.php`), qui lit le 1er bloc de la page.

## Menu : bouton d'appel

Ajouter la classe `menu-cta` à un élément de menu (Apparence > Menus > Options de l'écran > Classes CSS) l'affiche en pilule foncée (« Contact » dans la démo). La page courante est signalée par un petit point devant le lien.

## Ce que fait `inc/blocks.php`

| Fonction | Rôle |
|---|---|
| `starter_register_blocks()` | Enregistre automatiquement **chaque dossier** `blocks/*/block.json`. Créer le dossier suffit. |
| `starter_block_category()` | Catégorie « Starter » en tête de l'outil d'insertion |
| `starter_block_inner_class()` | Conteneur intérieur en pleine largeur (voir « Alignements ») |
| `starter_starts_with_hero()` | La page commence-t-elle par un Hero / Hero slider ? (voir ci-dessus) |
| `starter_format_title()` | Titre avec `**surligné**` et `__souligné__` (Hero slider, Pourquoi nous, Appel à l'action) |
| `starter_button()` / `starter_link_target()` | Boutons du thème à partir d'un champ Lien (voir « Boutons ») |
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
