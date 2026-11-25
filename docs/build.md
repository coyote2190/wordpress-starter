# Build : Vite + Tailwind CSS

## Les commandes

| Commande | Quand | Ce qu'elle fait |
|---|---|---|
| `npm install` | Une fois après le clone | Installe Vite, Tailwind, Prettier dans `node_modules/` |
| `npm run dev` | Pendant qu'on code | Lance Vite sur `http://localhost:5173`. Le CSS/JS se met à jour **sans recharger**, la page se recharge quand un `.php` change. |
| `npm run build` | Avant chaque commit / déploiement | Compile et minifie dans `dist/`, puis compile les blocs natifs (`blocks-native/build/`) |
| `npm run blocks:dev` / `blocks:build` | Si on travaille sur un bloc React | Voir `docs/native-blocks.md` |
| `npm run format` | Quand on veut | Formate JS/CSS/JSON/MD avec Prettier (et trie les classes Tailwind) |

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

| Fichier | Rôle |
|---|---|
| `package.json` | Liste des outils (devDependencies) et des commandes `npm run …` |
| `package-lock.json` | Versions exactes installées (à commiter, garantit la même install partout) |
| `vite.config.js` | Points d'entrée, dossier de sortie, serveur de dev, plugin `hot` |
| `inc/vite.php` | `starter_vite_enqueue()` : charge une entrée en dev ou en prod |
| `inc/assets.php` | Dit **quelles** entrées charger sur le site |
| `assets/src/css/main.css` | Entrée CSS du site : Tailwind + nos styles |
| `assets/src/css/editor.css` | Entrée CSS de l'éditeur Gutenberg (voir `docs/blocks.md`) |
| `assets/src/css/sources.css` | Dossiers scannés par Tailwind (`@source`) |
| `assets/src/js/main.js` | Entrée JS |
| `dist/` | Fichiers compilés. **Commité** : on peut déployer par simple FTP sans Node sur le serveur. |
| `.prettierrc` / `.prettierignore` | Règles de formatage |

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

| Symptôme | Cause / solution |
|---|---|
| Aucun style sur le site | `npm run build` jamais lancé → pas de manifest. Un admin connecté voit un warning PHP. |
| Une classe Tailwind ne marche pas | Le fichier n'est pas couvert par un `@source`, ou la classe est construite dynamiquement (`'text-' . $color`). Écrire les classes en entier. |
| En dev, les styles ne changent pas | Vite ne tourne plus mais `hot` est resté (crash) : supprimer le fichier `hot`. |
| Port 5173 déjà pris | Un autre Vite tourne : l'arrêter (`strictPort` empêche de basculer sur un autre port). |
