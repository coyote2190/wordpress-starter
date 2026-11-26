# Architecture

## Vue d'ensemble

```
                         ┌──────────────── wp-content/ ────────────────┐
                         │                                             │
 mu-plugins/starter-core │  DONNÉES & COMPORTEMENT (indépendant du thème)
   ├─ post-types.php     │  CPT « Réalisations » + taxonomie
   ├─ comments.php       │  commentaires désactivés
   ├─ contact-form.php   │  traitement du formulaire → wp_mail()
   └─ security.php       │  XML-RPC, en-têtes, énumération des auteurs
                         │
 plugins/                │  SCF (champs)  ·  FluentSMTP (envoi des mails par SMTP)
                         │
 themes/wordpress-starter│  AFFICHAGE
   ├─ functions.php ─────┼─► inc/*.php (un fichier par sujet)
   ├─ theme.json ────────┼─► couleurs / polices → variables CSS → Tailwind + éditeur
   ├─ *.php, template-parts/  gabarits (hiérarchie WordPress)
   ├─ blocks/            │  blocs SCF (PHP)
   ├─ blocks-native/     │  blocs React (@wordpress/scripts)
   ├─ assets/src/ ───────┼─► Vite ─► dist/  (CSS Tailwind + JS en modules)
   └─ scf-json/          │  définitions des champs, versionnées
                         └─────────────────────────────────────────────┘
```

## Les grands principes

1. **Un fichier = un sujet.** `functions.php` ne fait que charger des fichiers de `inc/`. Quand on cherche « où sont déclarés les menus ? », la réponse est dans le nom du fichier.
2. **Le thème affiche, le mu-plugin stocke et traite.** Types de contenu, traitement de formulaire, sécurité : dans `mu-plugin/`. Si le client change de thème, rien ne se perd.
3. **Une seule source de vérité pour le design** : `theme.json`. Tailwind et l'éditeur en découlent.
4. **Pas de dépendance cachée.** Pas de jQuery, pas de framework côté site : du JS natif en modules, chargés seulement si la page en a besoin.
5. **WordPress allégé.** On retire ce que WordPress charge sans qu'on s'en serve (emojis, metas…).
6. **Tout est versionné**, y compris les champs SCF (JSON) et les fichiers compilés (`dist/`), pour déployer par simple FTP.

## Comment WordPress charge le site

```
Requête d'une page
  │
  ├─ 1. wp-content/mu-plugins/*.php   → starter-core.php (toujours chargé)
  ├─ 2. wp-content/plugins/…          → SCF, FluentSMTP
  ├─ 3. functions.php du thème        → inc/*.php
  │      └─ chaque fichier s'accroche à des « hooks » (add_action / add_filter)
  └─ 4. gabarit choisi selon la hiérarchie (front-page.php, single.php…) → docs/templates.md
```

## Les hooks en deux phrases

- `add_action( 'nom', 'ma_fonction' )` : « quand WordPress arrive à l'étape _nom_, exécute ma fonction ».
- `add_filter( 'nom', 'ma_fonction' )` : « quand WordPress calcule la valeur _nom_, laisse ma fonction la modifier et la renvoyer ».

Exemple dans `inc/setup.php` : `after_setup_theme` est l'étape où le thème déclare ce qu'il supporte.

## Les fichiers de `inc/`

| Fichier        | Sujet                                                                    | Doc           |
| -------------- | ------------------------------------------------------------------------ | ------------- |
| `setup.php`    | Fonctionnalités du thème (logo, images, menus…)                          | —             |
| `cleanup.php`  | Retire emojis, metas inutiles…                                           | `cleanup.md`  |
| `vite.php`     | `starter_vite_enqueue()` : fichiers Vite en dev / prod                   | `build.md`    |
| `assets.php`   | CSS/JS chargés sur le site                                               | `build.md`    |
| `icons.php`    | `starter_icon()`                                                         | `icons.md`    |
| `scf.php`      | JSON SCF, page « Infos du site », `starter_field()` / `starter_option()` | `scf.md`      |
| `blocks.php`   | Enregistrement des blocs, catégorie, CSS de l'éditeur                    | `blocks.md`   |
| `patterns.php` | Catégories des compositions, `starter_pattern_block()`                   | `patterns.md` |

## Conventions

| Quoi                      | Convention                                   | Exemple                   |
| ------------------------- | -------------------------------------------- | ------------------------- |
| Fonctions PHP             | préfixe `starter_`                           | `starter_setup()`         |
| Constantes                | préfixe `STARTER_`                           | `STARTER_DIR`             |
| Text domain (traductions) | `starter`                                    | `__( 'Menu', 'starter' )` |
| Noms de blocs             | `starter/{nom}`                              | `starter/hero`            |
| Sélecteurs JS             | attributs `data-*`                           | `data-module="nav"`       |
| Indentation               | tabulations en PHP, 2 espaces en JS/CSS/JSON | voir `.editorconfig`      |
| Commits                   | gitmoji + message en anglais                 | `✨ Add …`                |

## Constantes disponibles

| Constante         | Valeur                                                   |
| ----------------- | -------------------------------------------------------- |
| `STARTER_VERSION` | Version lue dans `style.css`                             |
| `STARTER_DIR`     | Chemin disque du thème (pour `require`, lire un fichier) |
| `STARTER_URI`     | URL du thème (pour pointer vers une image, un script)    |
