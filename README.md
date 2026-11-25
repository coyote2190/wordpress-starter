# WordPress Starter

Thème de départ pour les sites clients : PHP classique, Tailwind CSS, Vite, JS natif, icônes SVG, SCF (Secure Custom Fields) et Gutenberg.

> Chaque sujet a sa page de doc dans [`docs/`](docs/). Commence par [`docs/architecture.md`](docs/architecture.md).

## Prérequis

- WordPress ≥ 6.6, PHP ≥ 8.1
- Extensions : **Secure Custom Fields** (champs) et **FluentSMTP** (envoi des mails)

## Arborescence

```
wordpress-starter/
├── style.css          En-tête du thème (lu par WordPress). Aucun style dedans.
├── functions.php      Point d'entrée : charge les fichiers de inc/.
├── index.php          Gabarit de secours (utilisé si aucun autre ne correspond).
├── screenshot.png     Aperçu dans Apparence > Thèmes (1200×900).
├── inc/               Logique PHP, un fichier par sujet.
│   └── setup.php      Fonctionnalités WordPress activées, menus.
├── docs/              Documentation (un fichier par sujet).
├── .editorconfig      Indentation commune à tous les éditeurs.
└── .gitignore         Fichiers exclus de Git.
```

## Documentation

| Sujet | Fichier |
|---|---|
| Architecture générale | [docs/architecture.md](docs/architecture.md) |
