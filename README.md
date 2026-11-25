# WordPress Starter

Thème de départ pour les sites clients : PHP classique, Tailwind CSS, Vite, JS natif, icônes SVG, SCF (Secure Custom Fields) et Gutenberg.

> Chaque sujet a sa page de doc dans [`docs/`](docs/). Commence par [`docs/architecture.md`](docs/architecture.md).

## Prérequis

- WordPress ≥ 6.6, PHP ≥ 8.1
- Extensions : **Secure Custom Fields** (champs) et **FluentSMTP** (envoi des mails)

## Démarrer

```bash
npm install
npm run dev     # pendant le dev, puis ouvrir http://wordpress-starter.local
npm run build   # avant de commiter / déployer
```

## Arborescence

```
wordpress-starter/
├── style.css          En-tête du thème (lu par WordPress). Aucun style dedans.
├── functions.php      Point d'entrée : charge les fichiers de inc/.
├── header.php         <head>, logo, menu principal, ouverture du <main>.
├── footer.php         Pied de page, wp_footer().
├── index.php          Liste des articles + gabarit de secours.
├── front-page.php     Accueil (contenu Gutenberg pleine largeur).
├── page.php           Page standard.
├── single.php         Article seul.
├── archive.php        Catégories, étiquettes, archives de CPT.
├── search.php         Résultats de recherche.
├── 404.php            Page introuvable.
├── searchform.php     Formulaire de recherche.
├── template-parts/    Morceaux de HTML réutilisés (boucle, carte, liste vide).
├── theme.json         Couleurs, polices, largeurs (source unique pour le site et l'éditeur).
├── screenshot.png     Aperçu dans Apparence > Thèmes (1200×900).
├── inc/               Logique PHP, un fichier par sujet.
│   ├── setup.php      Fonctionnalités WordPress activées, menus.
│   ├── cleanup.php    Retire ce que WordPress charge inutilement (emojis, metas…).
│   ├── vite.php       Pont Vite ↔ WordPress : charge les fichiers en dev ou depuis dist/.
│   ├── assets.php     Liste des CSS/JS chargés sur le site.
│   └── icons.php      starter_icon() : insère une icône SVG.
├── assets/icons/      Icônes SVG (un fichier par icône).
├── assets/src/        Sources front (éditées à la main).
│   ├── css/
│   │   ├── main.css   Entrée CSS : Tailwind, @source, imports.
│   │   ├── tokens.css Relie les couleurs/polices de theme.json à Tailwind.
│   │   ├── base.css   Styles de base + utilitaire `container`.
│   │   └── components.css  Styles du HTML généré par WP (menus, pagination).
│   └── js/main.js     Entrée JS (module ES).
├── dist/              Fichiers compilés par `npm run build` (commités, à déployer).
├── docs/              Documentation (un fichier par sujet).
├── package.json       Outils front et commandes npm.
├── vite.config.js     Configuration du build.
├── .prettierrc        Règles de formatage (JS/CSS/MD).
├── .editorconfig      Indentation commune à tous les éditeurs.
└── .gitignore         Fichiers exclus de Git.
```

## Documentation

| Sujet | Fichier |
|---|---|
| Architecture générale | [docs/architecture.md](docs/architecture.md) |
| Nettoyage de WordPress | [docs/cleanup.md](docs/cleanup.md) |
| Build (Vite, Tailwind) | [docs/build.md](docs/build.md) |
| Couleurs, polices, largeurs | [docs/design-tokens.md](docs/design-tokens.md) |
| Gabarits (quel fichier pour quelle page) | [docs/templates.md](docs/templates.md) |
| Icônes SVG | [docs/icons.md](docs/icons.md) |
