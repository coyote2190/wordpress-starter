# Pages Gutenberg

Le thème reste un thème PHP classique, mais les pages peuvent être construites
avec l'éditeur de blocs — idéal pour une page d'accueil modifiable par le client.

## Page d'accueil

1. Créer une page « Accueil » → la fenêtre de démarrage propose
   **« Page d'accueil complète »** (bannière, services, simulateur, CTA).
2. Réglages → Lecture → « Une page statique » → choisir « Accueil ».

`front-page.php` affiche alors uniquement les blocs : pas de titre de page,
pas de conteneur étroit. Si l'accueil affiche « Les derniers articles »,
c'est `index.php` qui prend le relais.

Pour les autres pages construites en blocs : modèle **« Pleine largeur (blocs) »**
(`page-templates/page-blocks.php`). Les pages classiques gardent `page.php`.

## Largeurs

| Alignement du bloc | Largeur         | Réglage                                     |
| ------------------ | --------------- | ------------------------------------------- |
| Normal             | 40rem (lecture) | `--container-narrow` / `layout.contentSize` |
| Large              | 1200px          | `--container-wrap` / `layout.wideSize`      |
| Pleine largeur     | bord à bord     | —                                           |

⚠️ Les valeurs existent à deux endroits : `@theme` dans `assets/src/css/main.css`
(front) et `theme.json` (éditeur + groupes). **Les modifier ensemble.**
Idem pour la palette (`ink`, `canvas`, `muted`, `line`).

## Compositions (patterns)

Dans l'inserteur : onglet Compositions → catégorie **Starter**.

| Fichier                  | Contenu                                              |
| ------------------------ | ---------------------------------------------------- |
| `patterns/hero.php`      | Bannière pleine largeur : titre H1, accroche, bouton |
| `patterns/services.php`  | Les 3 premiers services (CPT, triés par ordre)       |
| `patterns/simulator.php` | Section avec le bloc Simulateur de devis             |
| `patterns/cta.php`       | Appel à l'action sur fond sombre                     |
| `patterns/home.php`      | Les 4 ci-dessus, proposée à la création d'une page   |

Un fichier PHP dans `patterns/` avec l'en-tête `Title` / `Slug` / `Categories`
est enregistré automatiquement par WordPress. Le plus simple pour en créer une :
construire la section dans l'éditeur, « Éditeur de code » (Ctrl+Maj+Alt+M),
copier le balisage dans un nouveau fichier.

Une composition est copiée dans la page à l'insertion : modifier le fichier
ensuite ne change pas les pages existantes.

⚠️ `patterns/` est exclu de Prettier : reformater le balisage des blocs
les rend invalides dans l'éditeur.

## Styles

- Les styles du thème (`assets/dist/main.css`) sont chargés dans l'éditeur → relancer
  `npm run build` pour voir les changements côté éditeur.
- Les styles qui surchargent WordPress (boutons, largeurs) sont **hors `@layer`**
  dans `main.css` : le CSS des blocs de WordPress n'est pas dans un layer et passerait devant.
