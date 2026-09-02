# WordPress Starter

Reusable WordPress starter theme — PHP/Vite/SCSS, built for fast customizations across client projects and optimized for shared hosting (Apache/MySQL).

## Stack

- **WordPress** (classic PHP theme, no FSE)
- **Vite 8** (Rolldown) for asset builds
- **Sass** (Dart Sass, module system `@use`)
- Compatible with **shared hosting** (no Node required in production)

## Requirements

- Node.js 20+
- A local WordPress installation (Local, wp-env, MAMP, etc.)

## Installation

```bash
git clone https://github.com/coyote2190/wordpress-starter.git
cd wordpress-starter
npm install
composer install   # WordPress stubs for IDE autocompletion
```

Place the folder inside `wp-content/themes/`, then activate the theme from the WordPress admin.

## Development

```bash
npm run dev     # starts the Vite dev server with HMR (localhost:5173)
npm run build   # compiles assets into assets/dist/
```

The theme automatically detects whether the dev server is running via the `.vite-hot` file in the project root:

- **Dev server active** → assets load from `localhost:5173` with hot reload
- **Dev server stopped** → assets load from `assets/dist/`

> ⚠️ `assets/dist/` is versioned in Git. Run `npm run build` before each commit to keep the build up to date.

## Structure

```text
wordpress-starter/
├─ assets/
│  ├─ src/
│  │  ├─ js/
│  │  └─ scss/
│  └─ dist/
├─ inc/
├─ template-parts/
├─ footer.php
├─ functions.php
├─ header.php
├─ index.php
├─ package.json
├─ style.css
├─ vite.config.js
├─ README.md
└─ .vite-hot
```

The theme is organized around a lightweight PHP structure, with Vite handling frontend asset compilation and SCSS modules for styling and component-level organization.

## Components

Components are rendered via the `starter_component()` helper:

```php
starter_component('hero', [
    'title'       => 'Welcome',
    'subtitle'    => 'A short subtitle',
    'button_text' => 'Discover',
    'button_url'  => home_url('/contact'),
]);
```

## Fonts

The starter uses **Ranade** (Fontshare, ITF Free Font License).

Font files are **not versioned** — the license prohibits redistribution.

1. Download Ranade from [fontshare.com/fonts/ranade](https://www.fontshare.com/fonts/ranade)
2. Place `Ranade-Regular.woff2`, `Ranade-Medium.woff2` and `Ranade-Bold.woff2` in `assets/fonts/`

To switch fonts: replace the files, update `base/_fonts.scss` and `$font-base` in `abstracts/_variables.scss`.
