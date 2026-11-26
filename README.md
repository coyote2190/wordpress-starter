# WordPress Starter

Thème de départ pour les sites clients : PHP classique, Tailwind CSS, Vite, JS natif, icônes SVG, SCF (Secure Custom Fields) et Gutenberg.

> Chaque sujet a sa page de doc dans [`docs/`](docs/). Commence par [`docs/architecture.md`](docs/architecture.md).

## Prérequis

- WordPress ≥ 6.6, PHP ≥ 8.1
- Extensions : **Secure Custom Fields** (champs) et **FluentSMTP** (envoi des mails)
- Le mu-plugin `mu-plugin/` installé dans `wp-content/mu-plugins/` (voir [docs/mu-plugin.md](docs/mu-plugin.md))

## Démarrer

```bash
npm install
composer install     # outils PHP : règles WordPress (PHPCS), autocomplétion
npm run dev          # pendant le dev, puis ouvrir http://wordpress-starter.local
npm run blocks:dev   # (2ᵉ terminal) seulement si on modifie un bloc natif React
npm run build        # avant de commiter / déployer (Vite + blocs natifs)

# Contenu de démo (pages, articles, réalisations, menus) — voir docs/demo.md
wp eval-file wp-content/themes/wordpress-starter/demo/demo-content.php
```

## Arborescence

```
wordpress-starter/
├── style.css          En-tête du thème (lu par WordPress). Aucun style dedans.
├── functions.php      Point d'entrée : charge les fichiers de inc/.
├── header.php         <head>, logo, menu principal, ouverture du <main>.
├── footer.php         Pied de page, wp_footer().
├── index.php          Liste des articles + gabarit de secours.
├── front-page.php     Accueil (contenu Gutenberg de la page d'accueil, sans titre).
├── page.php           Page standard.
├── single.php         Article seul.
├── archive.php        Catégories, étiquettes, archives de CPT.
├── search.php         Résultats de recherche.
├── 404.php            Page introuvable.
├── searchform.php     Formulaire de recherche.
├── template-parts/    Morceaux de HTML réutilisés (en-tête de page, filtres, boucle, carte, logo, coordonnées, formulaire).
├── theme.json         Couleurs, polices, largeurs (source unique pour le site et l'éditeur).
├── screenshot.png     Aperçu dans Apparence > Thèmes (1200×900).
├── inc/               Logique PHP, un fichier par sujet.
│   ├── setup.php      Fonctionnalités WordPress activées, menus.
│   ├── cleanup.php    Retire ce que WordPress charge inutilement (emojis, metas…).
│   ├── vite.php       Pont Vite ↔ WordPress : charge les fichiers en dev ou depuis dist/.
│   ├── assets.php     Liste des CSS/JS chargés sur le site.
│   ├── icons.php      starter_icon() : insère une icône SVG.
│   ├── scf.php        SCF : JSON, page « Infos du site », starter_field() / starter_option().
│   ├── blocks.php     Enregistre les blocs (SCF + natifs), catégorie « Starter », CSS de l'éditeur, boutons.
│   ├── template-tags.php  Petites fonctions des gabarits (catégorie, liste parente, temps de lecture).
│   └── performance.php  Préchargement de la police des titres, images en WebP.
├── blocks/            Blocs Gutenberg SCF (un dossier par bloc : block.json + render.php).
│   ├── hero/          En-tête de page : grand titre + image arrondie.
│   ├── hero-slider/   En-tête : grand titre + diaporama d'images arrondi.
│   ├── features/      Grille de cartes (icône, couleur, titre, texte, lien).
│   ├── cta/           Appel à l'action (titre mis en valeur, bouton ou champ e-mail).
│   ├── why/           « Pourquoi nous » : intro + cartes défilantes.
│   ├── bento/         Mosaïque de tuiles (photo / couleur, tailles variées, chiffres clés).
│   └── contact-form/  Place le formulaire de contact dans une page.
├── blocks-native/     Blocs Gutenberg natifs en React.
│   ├── src/notice/    Sources du bloc « Encadré » (block.json, edit.js, render.php).
│   └── build/         Compilé par `npm run blocks:build` (commité, ne pas éditer).
├── mu-plugin/         MU-plugin « Starter Core » (données indépendantes du thème) → à copier dans wp-content/mu-plugins/.
│   ├── starter-core.php     Chargeur.
│   └── starter-core/        CPT et taxonomies, commentaires désactivés, formulaire + messages, SEO, sécurité.
├── scf-json/          Groupes de champs SCF en JSON (écrits par SCF, versionnés).
├── assets/icons/      Icônes SVG (un fichier par icône).
├── assets/src/        Sources front (éditées à la main).
│   ├── css/
│   │   ├── main.css   Entrée CSS du site : Tailwind + imports.
│   │   ├── editor.css Entrée CSS de l'éditeur Gutenberg (Tailwind sans reset).
│   │   ├── sources.css Dossiers scannés par Tailwind (partagé site/éditeur).
│   │   ├── tokens.css Relie les couleurs/polices de theme.json à Tailwind.
│   │   ├── base.css   Styles de base + utilitaire `container`.
│   │   └── components.css  Styles du HTML généré par WP (menus, pagination).
│   └── js/
│       ├── main.js    Entrée JS : charge les modules présents dans la page.
│       └── modules/   Un fichier par fonctionnalité (menu mobile, slider, défilement de cartes, formulaire).
├── dist/              Fichiers compilés par `npm run build` (commités, à déployer).
├── demo/              Script de contenu de démo (WP-CLI).
├── docs/              Documentation (un fichier par sujet).
├── package.json       Outils front et commandes npm.
├── vite.config.js     Configuration du build.
├── .prettierrc        Règles de formatage (JS/CSS/MD).
├── bin/sort-classes.mjs  Trie les classes Tailwind de A à Z, PHP et JS (npm run format, et à l'enregistrement).
├── .vscode/           Réglages VS Code partagés : tri à l'enregistrement, Prettier, extensions recommandées.
├── .editorconfig      Indentation commune à tous les éditeurs.
└── .gitignore         Fichiers exclus de Git.
```

## Documentation

| Sujet                                                                              | Fichier                                        |
| ---------------------------------------------------------------------------------- | ---------------------------------------------- |
| Architecture générale                                                              | [docs/architecture.md](docs/architecture.md)   |
| Nettoyage de WordPress                                                             | [docs/cleanup.md](docs/cleanup.md)             |
| Build (Vite, Tailwind)                                                             | [docs/build.md](docs/build.md)                 |
| Couleurs, polices, largeurs                                                        | [docs/design-tokens.md](docs/design-tokens.md) |
| Gabarits (quel fichier pour quelle page)                                           | [docs/templates.md](docs/templates.md)         |
| Icônes SVG                                                                         | [docs/icons.md](docs/icons.md)                 |
| JavaScript                                                                         | [docs/js.md](docs/js.md)                       |
| Champs SCF, page « Infos du site »                                                 | [docs/scf.md](docs/scf.md)                     |
| Blocs Gutenberg SCF                                                                | [docs/blocks.md](docs/blocks.md)               |
| Blocs Gutenberg natifs (React)                                                     | [docs/native-blocks.md](docs/native-blocks.md) |
| MU-plugin (CPT, commentaires)                                                      | [docs/mu-plugin.md](docs/mu-plugin.md)         |
| Formulaire de contact + FluentSMTP                                                 | [docs/contact-form.md](docs/contact-form.md)   |
| Sécurité (+ wp-config, .htaccess)                                                  | [docs/security.md](docs/security.md)           |
| SEO (titres, descriptions, sitemap, robots.txt, llms.txt), performance, Lighthouse | [docs/seo.md](docs/seo.md)                     |
| Contenu de démo                                                                    | [docs/demo.md](docs/demo.md)                   |
| **Nouveau projet client + déploiement**                                            | [docs/new-project.md](docs/new-project.md)     |
