# Blocs Gutenberg natifs (React) — `blocks-native/`

Les blocs SCF (`blocks/`) suffisent pour 90 % des besoins. Les blocs natifs servent quand on veut **éditer directement dans le bloc** (taper le texte dedans, comme un paragraphe), ou pour des interactions riches dans l'éditeur. C'est aussi ce que WordPress utilise pour tous ses blocs : c'est le savoir-faire à acquérir.

## SCF ou natif ?

|            | Bloc SCF                                                | Bloc natif                                                                |
| ---------- | ------------------------------------------------------- | ------------------------------------------------------------------------- |
| Langage    | PHP                                                     | React (JSX) pour l'éditeur + PHP pour le site                             |
| Édition    | Champs dans le panneau de droite                        | **Directement dans le bloc** + panneau de droite                          |
| Build      | Aucun                                                   | `@wordpress/scripts` (webpack)                                            |
| Dépend de  | Extension SCF                                           | Rien (cœur de WordPress)                                                  |
| Idéal pour | Sections de mise en page (hero, chiffres clés, équipe…) | Éléments de texte enrichi (encadré, citation stylée…), outils interactifs |

## Exemple fourni : « Encadré » (`starter/notice`)

```
blocks-native/
├── src/notice/            SOURCES (éditées à la main)
│   ├── block.json         Carte d'identité : nom, attributs, scripts, render
│   ├── index.js           Enregistre le bloc côté éditeur
│   ├── edit.js            Composant React affiché dans l'éditeur
│   ├── styles.js          Classes Tailwind des variantes (info, succès, attention)
│   └── render.php         HTML sur le site (PHP)
└── build/notice/          COMPILÉ par `npm run blocks:build` (commité, ne pas éditer)
    ├── block.json
    ├── index.js           edit.js + index.js compilés
    ├── index.asset.php    Dépendances WordPress (wp-blocks, wp-block-editor…) + version
    └── render.php         Copié tel quel
```

`inc/blocks.php` enregistre automatiquement chaque `blocks-native/build/*/block.json`.

### Le cycle de vie

```
Éditeur (React)                                   Site (PHP)
───────────────                                   ──────────
edit.js affiche le bloc                           render.php reçoit $attributes
  │ le rédacteur tape / choisit                     et génère le HTML
  ▼                                                  ▲
setAttributes({ content, type })                    │
  │                                                  │
  ▼                                                  │
WordPress enregistre dans le contenu de la page :   │
<!-- wp:starter/notice {"type":"warning","content":"…"} /-->
```

### `block.json` : les attributs

```jsonc
"attributes": {
  "type":    { "type": "string", "enum": ["info", "success", "warning"], "default": "info" },
  "content": { "type": "string", "default": "" }
}
```

Les **attributs** sont les données du bloc. Ils sont lus par `edit.js` (`attributes.type`) et par `render.php` (`$attributes['type']`).

> ⚠️ **Piège classique** : dans un bloc dynamique (`save: () => null`), **ne jamais mettre `"source"`** sur un attribut (`"source": "html"`, `"rich-text"`, `"attribute"`…). `source` dit à Gutenberg de relire la valeur **dans le HTML enregistré**, or un bloc dynamique n'enregistre pas de HTML → la valeur est perdue au rechargement. Sans `source`, l'attribut est stocké dans le commentaire `<!-- wp:starter/notice {...} /-->`.

### `edit.js` : les composants à connaître

| Composant / fonction                                          | Paquet                    | Rôle                                                                                                                  |
| ------------------------------------------------------------- | ------------------------- | --------------------------------------------------------------------------------------------------------------------- |
| `useBlockProps()`                                             | `@wordpress/block-editor` | Attributs de la racine du bloc (obligatoire avec `apiVersion: 3`). Équivalent PHP : `get_block_wrapper_attributes()`. |
| `RichText`                                                    | `@wordpress/block-editor` | Zone de texte éditable avec gras, italique, liens                                                                     |
| `InspectorControls`                                           | `@wordpress/block-editor` | Contenu du panneau de droite                                                                                          |
| `PanelBody`, `SelectControl`, `ToggleControl`, `TextControl`… | `@wordpress/components`   | Champs du panneau. Catalogue : https://wordpress.github.io/gutenberg/                                                 |
| `__()`                                                        | `@wordpress/i18n`         | Texte traduisible                                                                                                     |

Ces paquets ne sont **pas** inclus dans le fichier compilé : WordPress les fournit déjà (`wp.blocks`, `wp.blockEditor`…). `index.asset.php` liste ces dépendances pour que WordPress les charge avant notre script.

### Bloc dynamique (`render.php`) ou statique (`save.js`) ?

|                            | Dynamique (choisi ici)                            | Statique                                                                         |
| -------------------------- | ------------------------------------------------- | -------------------------------------------------------------------------------- |
| HTML du site               | Généré par PHP **à chaque affichage**             | Généré par React **à l'enregistrement**, stocké dans la base                     |
| Modifier le HTML plus tard | On modifie `render.php`, toutes les pages suivent | Les blocs existants deviennent « invalides » → il faut écrire des _deprecations_ |
| Utiliser des fonctions PHP | Oui (`starter_icon()`, requêtes…)                 | Non                                                                              |

**Alignements** : comme les blocs SCF, l'encadré propose Aucun / Grande / Pleine largeur. `edit.js` et `render.php` appliquent la même règle (conteneur `container` en pleine largeur, voir `docs/blocks.md`).

**Règle du starter : toujours dynamique** (`"render": "file:./render.php"` + `save: () => null`).

## Commandes

| Commande               | Rôle                                                                                         |
| ---------------------- | -------------------------------------------------------------------------------------------- |
| `npm run blocks:dev`   | Recompile à chaque modification (à lancer **en plus** de `npm run dev`, dans un 2ᵉ terminal) |
| `npm run blocks:build` | Compilation de production                                                                    |
| `npm run build`        | Vite **puis** blocs natifs : tout le projet                                                  |

Après une modification d'`edit.js`, **recharger l'éditeur** (pas de rechargement à chaud).

## Créer un bloc natif

1. Copier `blocks-native/src/notice/` en `blocks-native/src/mon-bloc/`.
2. `block.json` : changer `name` (`starter/mon-bloc`), `title`, `attributes`…
3. Écrire `edit.js` et `render.php`.
4. `npm run blocks:build` → `blocks-native/build/mon-bloc/` est créé et enregistré automatiquement.
5. Commiter `src/` **et** `build/`.

> Les imports entre fichiers doivent avoir leur extension (`import Edit from './edit.js'`) : le projet est en modules ES (`"type": "module"` dans `package.json`).

## Pour progresser

- Doc officielle : https://developer.wordpress.org/block-editor/
- Prochaines étapes : un bloc avec **blocs enfants** (`InnerBlocks`), un bloc qui **affiche des contenus** (`useSelect` + `core-data`), la **Interactivity API** pour l'interactivité côté site.
