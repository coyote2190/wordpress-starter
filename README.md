# WordPress Starter

Reusable WordPress starter theme — PHP/Vite/Tailwind, built for fast customizations across client projects and optimized for shared hosting (Apache/MySQL).

## Stack

- **WordPress** (classic PHP theme, no FSE)
- **Vite 8** (Rolldown) for asset builds
- **Tailwind CSS 4** (CSS-first config via `@theme`, `@tailwindcss/vite` plugin)
- **React** — Gutenberg blocks (`@wordpress/scripts`) and front-end islands (Vite)
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
npm run dev            # starts the Vite dev server with HMR (localhost:5173)
npm run dev:blocks     # watches Gutenberg blocks (blocks/src → blocks/build)
npm run build          # compiles assets into assets/dist/ and blocks into blocks/build/
npm run sort-classes   # reorders Tailwind classes in the .php templates (rustywind)
npm run format         # sort-classes + prettier on the whole theme
```

> Class sorting uses [rustywind](https://github.com/avencera/rustywind) rather
> than `prettier-plugin-tailwindcss` — the latter can't see class names inside
> the `@prettier/plugin-php` output, which is where nearly all of ours live.

The theme automatically detects whether the dev server is running via the `.vite-hot` file in the project root:

- **Dev server active** → assets load from `localhost:5173` with hot reload
- **Dev server stopped** → assets load from `assets/dist/`

> ⚠️ `assets/dist/` and `blocks/build/` are versioned in Git. Run `npm run build` before each commit to keep the build up to date.

## Structure

```text
wordpress-starter/
├─ assets/
│  ├─ src/
│  │  ├─ js/
│  │  │  └─ islands/   # React islands (front)
│  │  └─ css/        # main.css — @import "tailwindcss" + @theme tokens
│  └─ dist/
├─ acf-json/       # SCF/ACF field groups (JSON sync)
├─ blocks/
│  ├─ src/        # Gutenberg blocks (block.json, edit.js, render.php)
│  └─ build/
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
└─ docs/
```

The theme is organized around a lightweight PHP structure, with Vite handling
asset compilation and Tailwind utilities written directly in the PHP templates.

## Styling

Tailwind CSS 4 with a **CSS-first config** — no `tailwind.config.js`.
Everything lives in [`assets/src/css/main.css`](assets/src/css/main.css):

- `@theme` — design tokens to override per client (colors, containers).
  Colors are neutral by default: `ink`, `canvas`, `muted`, `line`
  (used as `text-ink`, `bg-canvas`, `border-line`, …).
- `@layer base` — defaults on raw HTML elements (Tailwind Preflight does the reset).
- `@layer components` — the few classes that can't be expressed as utilities in
  markup: `.wrap` / `.wrap-narrow` (centered containers), `.btn`, `.skip-link`,
  `.burger` (hamburger icon).

Tailwind auto-scans the theme's `.php` files for class names — no content config needed.

## Components

Components are rendered via the `starter_component()` helper:

```php
starter_component('hero', [
  'title' => 'Welcome',
  'subtitle' => 'A short subtitle',
  'button_text' => 'Discover',
  'button_url' => home_url('/contact'),
]);
```

## Custom fields (SCF)

The starter uses **[Secure Custom Fields](https://wordpress.org/plugins/secure-custom-fields/)** (SCF), the WordPress.org fork of ACF — free, including repeater, flexible content, options pages and blocks. ACF / ACF Pro also works (same API), but never activate both.

- Field groups are saved as JSON in `acf-json/` (versioned). On a new install: *Custom Fields → Field Groups → Sync available*.
- Read fields with `starter_field('name')`, which returns `null` if no plugin is active instead of crashing.
- Included: `group_starter_service.json` (price, duration, icon, link for the `service` CPT).

## Fonts

The starter uses **Ranade** (Fontshare, ITF Free Font License).

Font files are **not versioned** — the license prohibits redistribution.

1. Download Ranade from [fontshare.com/fonts/ranade](https://www.fontshare.com/fonts/ranade)
2. Place `Ranade-Regular.woff2`, `Ranade-Medium.woff2` and `Ranade-Bold.woff2` in `assets/fonts/`

To switch fonts: replace the files, then update the `@font-face` rules and the
`--font-sans` token in `assets/src/css/main.css`.

## Documentation

- [React: blocks & islands](docs/REACT.md) — Gutenberg blocks and React islands (quote simulator example)
- [Template hierarchy](docs/TEMPLATES.md) — which file handles which page type
