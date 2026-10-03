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

Dans l'inserteur : onglet Compositions → catégorie **Sea Click**.

| Fichier                     | Contenu                                                                                   |
| --------------------------- | ----------------------------------------------------------------------------------------- |
| `patterns/hero.php`         | Bannière pleine largeur : titre H1, accroche, bouton                                      |
| `patterns/services.php`     | Les 3 premiers services (CPT, triés par ordre)                                            |
| `patterns/simulator.php`    | Section avec le bloc Simulateur de devis                                                  |
| `patterns/cta.php`          | Appel à l'action sur fond sombre                                                          |
| `patterns/testimonials.php` | Section avec le bloc Témoignages                                                          |
| `patterns/faq.php`          | Section avec le bloc FAQ                                                                  |
| `patterns/contact.php`      | Section avec le bloc Formulaire de contact                                                |
| `patterns/home.php`         | Bannière, services, simulateur, témoignages, FAQ, CTA — proposée à la création d'une page |

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

## Blocs Sea Click

Catégorie **Sea Click** dans l'inserteur (blocs dynamiques : rendu PHP, modifiables sans casser les pages existantes).

| Bloc                  | Rendu                        | Particularités                                  |
| --------------------- | ---------------------------- | ----------------------------------------------- |
| Simulateur de devis   | Îlot React (kit UI)          | Prix de base, curseur de quantité, options      |
| FAQ                   | `<details>` natif, sans JS   | Balisage FAQ JSON-LD pour Google (désactivable) |
| Témoignages           | Grille de `.card`            | 1 à 3 colonnes                                  |
| Formulaire de contact | Composant PHP `contact-form` | Anti-spam (nonce + honeypot), un seul par page  |

FAQ et Témoignages s'éditent directement dans la page (texte cliquable),
avec boutons monter / descendre / supprimer — composant partagé
`blocks/shared/ItemList.js`, à réutiliser pour tout bloc « liste d'éléments ».
