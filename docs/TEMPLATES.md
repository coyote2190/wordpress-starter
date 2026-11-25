# Les fichiers templates

Les templates sont les fichiers PHP à la racine du thème. WordPress choisit lequel afficher selon la page demandée : c'est la **hiérarchie des templates**. La logique métier est dans [inc/](inc.md), ici on ne trouve que l'affichage (HTML + appels aux fonctions WordPress).

## Structure

```
wordpress-starter/
├── header.php        ← début du HTML, <head>, en-tête du site, navigation
├── footer.php        ← pied de page, wp_footer(), fermeture </body></html>
├── front-page.php    ← page d'accueil
├── page.php          ← pages statiques
├── single.php        ← article seul
├── archive.php       ← catégories, tags, archives, CPT
├── search.php        ← résultats de recherche
├── 404.php           ← page introuvable
├── index.php         ← template de secours (obligatoire pour WordPress)
├── style.css         ← métadonnées du thème (le CSS réel est compilé par Vite)
└── theme.json        ← réglages de l'éditeur de blocs
```

## Quel template pour quelle page

| Fichier          | Affiché pour                                 | Contenu                                                                                        |
| ---------------- | -------------------------------------------- | ---------------------------------------------------------------------------------------------- |
| `front-page.php` | La page d'accueil du site                    | Aiguillage selon Réglages > Lecture (voir plus bas).                                           |
| `page.php`       | Une page statique                            | Titre, image à la une, `the_content()`.                                                        |
| `single.php`     | Un article                                   | Titre, date, catégories, image, contenu, tags, article précédent/suivant, commentaires.        |
| `archive.php`    | Archive de catégorie, tag, auteur, date, CPT | Titre et description d'archive, grille d'articles (2 colonnes en `md`, 3 en `lg`), pagination. |
| `search.php`     | Une recherche                                | Terme cherché, nombre de résultats, formulaire, grille de résultats, pagination.               |
| `404.php`        | Une URL inexistante                          | Message, formulaire de recherche, lien vers l'accueil.                                         |
| `index.php`      | Tout ce qu'aucun autre template ne couvre    | Liste simple des articles. Fichier de secours exigé par WordPress.                             |

## Les deux templates partagés

- **`header.php`** : ouvre le document (`<!DOCTYPE html>`, `<head>` avec `wp_head()`, `<body>` avec `body_class()` et `wp_body_open()`), affiche un lien d'évitement « Aller au contenu » (accessibilité), le logo (ou le nom du site), le bouton burger et le menu `primary` (profondeur 2). Le bouton et la nav portent les attributs `data-nav-toggle` et `data-nav`, utilisés par le JS pour l'ouverture du menu mobile.
- **`footer.php`** : affiche le menu `footer`, le copyright, puis `wp_footer()` et ferme `</body></html>`.

Chaque template de page appelle `get_header()` au début et `get_footer()` à la fin, et place son contenu dans `<main id="main">`. L'id `main` est la cible du lien d'évitement.

## `front-page.php` : l'aiguillage de l'accueil

WordPress utilise `front-page.php` en priorité pour l'accueil, quel que soit le réglage. Le fichier regarde donc l'option `show_on_front` :

- **« Une page statique »** (Réglages > Lecture) : il charge `page.php`, donc la page choisie s'affiche normalement.
- **« Les derniers articles »** : il charge `home.php` s'il existe, sinon `index.php`, c'est-à-dire la hiérarchie classique.

## Conventions

- **Textes traduisibles.** Les chaînes passent par `esc_html_e()` / `esc_html__()` avec le text domain `starter`.
- **Échappement.** Toute sortie dynamique est échappée (`esc_html`, `esc_url`, `esc_attr`).
- **Classes Tailwind.** Le style est fait avec des classes utilitaires dans le HTML. Les classes `wrap` et `wrap-narrow` sont les conteneurs de largeur normale et réduite, définis dans `assets/src/css/main.css`.
- **`post_class()`** est appliqué sur les `<article>` pour que WordPress ajoute ses classes (type de contenu, catégories…).
- **Menus.** Les emplacements `primary` et `footer` sont à enregistrer dans `inc/setup.php`. Sans menu assigné, rien ne s'affiche (`fallback_cb` est à `false`).
- **Doublon connu.** `archive.php` et `search.php` ont la même grille d'articles et la même pagination. Une modification de l'un doit être répercutée dans l'autre, ou extraite dans un `template-parts/`.

## Ajouter un template

WordPress cherche des noms de fichiers précis. Quelques exemples :

| Besoin                                          | Fichier à créer           |
| ----------------------------------------------- | ------------------------- |
| Page d'article d'un CPT (ex. `service`)         | `single-service.php`      |
| Archive d'un CPT                                | `archive-service.php`     |
| Une page précise (par slug)                     | `page-contact.php`        |
| Une catégorie précise                           | `category-actualites.php` |
| Page blog quand l'accueil est une page statique | `home.php`                |

Le fichier le plus spécifique gagne. S'il n'existe pas, WordPress retombe sur `single.php`, `page.php` ou `archive.php`, puis sur `index.php`.
