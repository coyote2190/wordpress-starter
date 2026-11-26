# Build : Vite + Tailwind CSS

## Les commandes

| Commande                              | Quand                             | Ce qu'elle fait                                                                                                                 |
| ------------------------------------- | --------------------------------- | ------------------------------------------------------------------------------------------------------------------------------- |
| `npm install`                         | Une fois après le clone           | Installe Vite, Tailwind, Prettier dans `node_modules/`                                                                          |
| `npm run dev`                         | Pendant qu'on code                | Lance Vite sur `http://localhost:5173`. Le CSS/JS se met à jour **sans recharger**, la page se recharge quand un `.php` change. |
| `npm run build`                       | Avant chaque commit / déploiement | Compile et minifie dans `dist/`, puis compile les blocs natifs (`blocks-native/build/`)                                         |
| `npm run blocks:dev` / `blocks:build` | Si on travaille sur un bloc React | Voir `docs/native-blocks.md`                                                                                                    |
| `npm run format`                      | Quand on veut                     | Formate JS/CSS/JSON/MD avec Prettier **et trie les classes Tailwind de A à Z**, PHP compris (voir ci-dessous)                   |
| `npm run format:check`                | En CI                             | Même vérification, sans rien modifier (code de sortie 1 si un fichier n'est pas formaté)                                        |
| `composer install`                    | Une fois après le clone           | Installe PHPCS + règles WordPress et les stubs WordPress / ACF (autocomplétion) dans `vendor/`                                  |
| `npm run lint:php` / `lint:php:fix`   | Avant un commit                   | Vérifie le PHP avec les règles WordPress / corrige ce qui peut l'être automatiquement (voir ci-dessous)                         |

> On continue d'ouvrir le site sur **http://wordpress-starter.local** (et pas localhost:5173). Vite ne sert que les fichiers CSS/JS.

## Comment ça marche

```
                     npm run dev                                npm run build
                         │                                            │
             crée le fichier « hot »                     génère dist/assets/main-a1b2.css
             (contient http://localhost:5173)            + dist/.vite/manifest.json
                         │                                            │
                         ▼                                            ▼
inc/vite.php :  « hot » existe ET site en local ?  ── non ──►  lit le manifest
                         │ oui                                 et charge dist/…
                         ▼
          charge localhost:5173/assets/src/…
          + le client Vite (rechargement à chaud)
```

### Pourquoi un fichier `hot` ?

PHP ne peut pas savoir tout seul si Vite tourne. Le plugin maison dans `vite.config.js` écrit ce fichier au démarrage et le supprime à l'arrêt. Il est dans `.gitignore`.

Sécurité : `inc/vite.php` ignore `hot` si `WP_ENVIRONMENT_TYPE` n'est pas `local` ou `development`. Un `hot` envoyé par erreur en prod ne casse rien.

### Pourquoi un manifest ?

Les fichiers compilés ont un **hash** dans leur nom (`main-B6d3mmbV.css`). Quand le contenu change, le nom change : le navigateur ne peut pas garder une vieille version en cache. Le manifest fait la correspondance `assets/src/css/main.css → assets/main-B6d3mmbV.css`.

## Les fichiers

| Fichier                           | Rôle                                                                                       |
| --------------------------------- | ------------------------------------------------------------------------------------------ |
| `package.json`                    | Liste des outils (devDependencies) et des commandes `npm run …`                            |
| `package-lock.json`               | Versions exactes installées (à commiter, garantit la même install partout)                 |
| `vite.config.js`                  | Points d'entrée, dossier de sortie, serveur de dev, plugin `hot`                           |
| `inc/vite.php`                    | `starter_vite_enqueue()` : charge une entrée en dev ou en prod                             |
| `inc/assets.php`                  | Dit **quelles** entrées charger sur le site                                                |
| `assets/src/css/main.css`         | Entrée CSS du site : Tailwind + nos styles                                                 |
| `assets/src/css/editor.css`       | Entrée CSS de l'éditeur Gutenberg (voir `docs/blocks.md`)                                  |
| `assets/src/css/sources.css`      | Dossiers scannés par Tailwind (`@source`)                                                  |
| `assets/src/js/main.js`           | Entrée JS                                                                                  |
| `dist/`                           | Fichiers compilés. **Commité** : on peut déployer par simple FTP sans Node sur le serveur. |
| `.prettierrc` / `.prettierignore` | Règles de formatage                                                                        |

## Ajouter un point d'entrée

Exemple : un JS chargé seulement sur la page contact.

1. Créer `assets/src/js/contact.js`.
2. L'ajouter à `entries` dans `vite.config.js`.
3. Le charger en PHP :
   ```php
   if ( is_page( 'contact' ) ) {
       starter_vite_enqueue( 'starter-contact', 'assets/src/js/contact.js' );
   }
   ```
4. `npm run build`.

## Tailwind v4 en bref

- Pas de `tailwind.config.js` : la configuration se fait **dans le CSS** (`@theme`, `@source`…).
- `@source` (dans `sources.css`) liste les dossiers où Tailwind cherche les classes utilisées. **Un nouveau dossier qui contient du HTML (ex. `blocks/`) doit être ajouté**, sinon ses classes ne seront pas générées.
- Le JS est chargé en `type="module"` (via `wp_enqueue_script_module`) : `import`/`export` natifs, exécuté après le parsing du HTML.

## Problèmes courants

| Symptôme                           | Cause / solution                                                                                                                             |
| ---------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------- |
| Aucun style sur le site            | `npm run build` jamais lancé → pas de manifest. Un admin connecté voit un warning PHP.                                                       |
| Une classe Tailwind ne marche pas  | Le fichier n'est pas couvert par un `@source`, ou la classe est construite dynamiquement (`'text-' . $color`). Écrire les classes en entier. |
| En dev, les styles ne changent pas | Vite ne tourne plus mais `hot` est resté (crash) : supprimer le fichier `hot`.                                                               |
| Port 5173 déjà pris                | Un autre Vite tourne : l'arrêter (`strictPort` empêche de basculer sur un autre port).                                                       |

## Tri des classes Tailwind : ordre alphabétique

Choix du projet : les classes sont rangées **de A à Z** (`bg-white border flex gap-3 items-center px-4 rounded-full`), plus simple à lire et à retrouver que l'ordre « officiel » de Tailwind. L'ordre des classes ne change **jamais** le rendu : c'est Tailwind qui décide de l'ordre des règles dans le CSS.

Outil : `bin/sort-classes.mjs`, lancé :

- **à chaque enregistrement dans VS Code** (fichiers PHP et JS) : extension **Run on Save** (`emeraldwalk.RunOnSave`), configurée dans `.vscode/settings.json`. VS Code la propose à l'ouverture du projet (`.vscode/extensions.json`) ;
- par `npm run format` pour tout le thème (et `npm run format:check` pour vérifier sans modifier).

> VS Code ouvert sur le dossier WordPress (`app/public`) et non sur le thème : copier la config dans `app/public/.vscode/settings.json` avec les chemins depuis la racine (`${workspaceFolder}/wp-content/themes/nom-du-theme/bin/sort-classes.mjs`).

Les autres réglages de `.vscode/settings.json` : Prettier à l'enregistrement pour JS / CSS / JSON / Markdown, PHPCBF à l'enregistrement pour le PHP (voir ci-dessous), autocomplétion Tailwind dans le PHP.

| Fichiers | Ce qui est trié                                                                                                                           |
| -------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| PHP      | attributs `class="…"` (les morceaux `<?php … ?>` restent en place), tableaux `'class' => '…'`, 2e argument de `starter_icon()`            |
| JS       | `className="…"`, `className: '…'`, parties fixes de ``className={`…`}``, constantes de classes en MAJUSCULES (`export const INNER = '…'`) |

- Un `-` ou `!` au début est ignoré pour le tri : `-translate-x-1` se range avec `translate-…`.
- Pas triées : les classes construites par concaténation ou rangées dans des tableaux PHP (`$widths = array( … )`).
- **Garde-fou** : une liste n'est modifiée que si chaque mot ressemble à une classe Tailwind. Une concaténation PHP prise par erreur pour une liste (`class="' . esc_attr( $x ) . '"`) n'est jamais touchée.

> Pas de `prettier-plugin-tailwindcss` : il impose l'ordre officiel de Tailwind et défairait le tri alphabétique du JS.

## PHP : règles WordPress (PHPCS)

**PHPCS** vérifie le PHP avec les règles officielles WordPress (`WordPress Coding Standards`), **PHPCBF** corrige automatiquement ce qui peut l'être. Réglages : `phpcs.xml.dist`. Outils installés par `composer install` dans `vendor/` (rien n'est chargé par le site).

Ce qui est vérifié :

- **sécurité** : sortie non échappée (`echo $x` au lieu de `esc_html()`), `$_POST` / `$_SERVER` non nettoyés, nonce manquant, requête SQL non préparée ;
- **compatibilité** : PHP 8.1+ et WordPress 6.6+ (fonctions obsolètes) ;
- **style** : espaces, indentation du code PHP, alignement des `=` et `=>`, préfixe `starter_` des fonctions, text domain `starter`, commentaires `translators:`.

Dans VS Code (extension **PHP_CodeSniffer**, `obliviousharmony.vscode-php-codesniffer`) : erreurs soulignées pendant la frappe, PHPCBF à chaque enregistrement.

> **Le HTML des gabarits n'est pas réindenté** : aucun outil ne le fait proprement dans le style WordPress (testé : Intelephense + PHPCBF casse les lignes `<?php echo …; ?>`). Indentation à la main : sélectionner les lignes puis `Tab` / `Shift+Tab`.

Choix du projet (`phpcs.xml.dist`) :

| Règle désactivée / adaptée                                     | Pourquoi                                                                                     |
| -------------------------------------------------------------- | -------------------------------------------------------------------------------------------- |
| `starter_icon()`, `starter_button()`, `starter_format_title()` | Renvoient du HTML déjà échappé : pas besoin de `phpcs:ignore` à chaque `echo`                |
| Variables « globales » dans `render.php` et `template-parts/`  | Ces fichiers sont inclus **dans** une fonction : `$title`, `$link`… ne sont pas des globales |
| Noms de fichiers `class-…`                                     | Le thème n'a pas de classes                                                                  |
| Petits tableaux sur une ligne                                  | `array( 'class' => 'mb-8' )` reste lisible                                                   |
| Docblocks complets dans `demo/`                                | Outils WP-CLI internes                                                                       |

Ignorer une ligne précise (toujours avec la raison) :

```php
echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans ma_fonction().
```

## CI : vérification à chaque push

`.github/workflows/ci.yml` lance sur GitHub, à chaque push sur `main` et à chaque pull request :

1. `npm run format:check` (Prettier + tri des classes) puis `npm run build` ;
2. vérification de la syntaxe PHP (`php -l`) en PHP 8.1, puis `composer lint`.

Une croix rouge sur le commit = quelque chose à corriger. Les mêmes commandes se lancent en local.
