# Gabarits — quel fichier affiche quelle page ?

## La hiérarchie des templates

Pour chaque URL, WordPress cherche le gabarit **le plus précis** qui existe, puis descend jusqu'à `index.php`.

| URL visitée                             | Fichiers cherchés, dans l'ordre (✅ = présent dans le thème) |
| --------------------------------------- | ------------------------------------------------------------ |
| Accueil                                 | ✅ `front-page.php` → `home.php` → `index.php`               |
| Page des articles (si accueil statique) | `home.php` → ✅ `index.php`                                  |
| Page                                    | `page-{slug}.php` → `page-{id}.php` → ✅ `page.php`          |
| Article                                 | `single-post.php` → ✅ `single.php`                          |
| Contenu personnalisé (ex. `reference`)  | `single-reference.php` → ✅ `single.php`                     |
| Catégorie, étiquette, auteur, date      | `category-{slug}.php` → `category.php` → ✅ `archive.php`    |
| Archive d'un CPT (ex. `/references/`)   | `archive-reference.php` → ✅ `archive.php`                   |
| Recherche                               | ✅ `search.php`                                              |
| Page introuvable                        | ✅ `404.php`                                                 |

> Pour une page avec une mise en page spéciale (ex. contact), créer `page-contact.php` : WordPress l'utilise automatiquement pour la page dont le slug est `contact`.

## Les fichiers

| Fichier          | Rôle                                                                                                                                                          |
| ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `header.php`     | `<head>` (dont `wp_head()`), ouverture du `<body>`, lien d'évitement, logo, menu principal, ouverture du `<main>`                                             |
| `footer.php`     | Fermeture du `<main>`, pied de page, menu footer, `wp_footer()`                                                                                               |
| `index.php`      | Liste des articles (blog) et gabarit de secours                                                                                                               |
| `front-page.php` | Accueil : contenu Gutenberg de la page d'accueil, sans titre (les sections sont des blocs)                                                                    |
| `page.php`       | Page : en-tête (`page-header`) + contenu Gutenberg dans `prose`. Si la page commence par un Hero ou un Hero slider : pas d'en-tête (le bloc porte le `<h1>`). |
| `single.php`     | Article : date, titre, image mise en avant, contenu, article précédent/suivant                                                                                |
| `archive.php`    | Titre + description de l'archive, puis la boucle                                                                                                              |
| `search.php`     | Titre « Résultats pour… », formulaire, puis la boucle                                                                                                         |
| `404.php`        | Message, recherche, lien vers l'accueil                                                                                                                       |
| `searchform.php` | Formulaire de recherche, utilisé partout via `get_search_form()`                                                                                              |

### `template-parts/` : morceaux réutilisables

Appelés avec `get_template_part( 'template-parts/content', 'card' )` → charge `template-parts/content-card.php`.

| Fichier            | Utilisé par            | Rôle                                                                   |
| ------------------ | ---------------------- | ---------------------------------------------------------------------- |
| `loop.php`         | index, archive, search | Grille de cartes + pagination, ou `content-none` si vide               |
| `content-card.php` | `loop.php`             | Une carte : image, date, titre (toute la carte est cliquable), extrait |
| `content-none.php` | `loop.php`             | Message « aucun contenu / aucun résultat »                             |
| `site-contact.php` | `footer.php`           | Téléphone, e-mail, adresse, réseaux (page « Infos du site »)           |

**Règle** : dès qu'un bout de HTML sert à deux endroits, il va dans `template-parts/`.

## Les fonctions WordPress à ne jamais oublier

| Fonction                        | Où                    | Pourquoi                                                         |
| ------------------------------- | --------------------- | ---------------------------------------------------------------- |
| `wp_head()`                     | juste avant `</head>` | WordPress et les extensions y injectent CSS, metas SEO…          |
| `wp_body_open()`                | juste après `<body>`  | Certaines extensions (ex. Google Tag Manager) y ajoutent du code |
| `wp_footer()`                   | juste avant `</body>` | Scripts, barre d'admin                                           |
| `body_class()` / `post_class()` | `<body>`, `<article>` | Classes utiles (`home`, `single-post`, `logged-in`…)             |

## Sécurité : toujours échapper à l'affichage

| Contexte                      | Fonction                      | Exemple                                         |
| ----------------------------- | ----------------------------- | ----------------------------------------------- |
| Texte                         | `esc_html()` / `esc_html_e()` | `<p><?php echo esc_html( $nom ); ?></p>`        |
| Attribut HTML                 | `esc_attr()`                  | `<input value="<?php echo esc_attr( $v ); ?>">` |
| URL                           | `esc_url()`                   | `<a href="<?php echo esc_url( $lien ); ?>">`    |
| HTML autorisé (contenu riche) | `wp_kses_post()`              | `<?php echo wp_kses_post( $texte ); ?>`         |

Les fonctions `the_title()`, `the_content()`, `the_permalink()`… échappent déjà elles-mêmes.

## Styles du HTML généré par WordPress

Les menus, la pagination et la navigation entre articles sont générés par WordPress : on ne peut pas mettre de classes Tailwind sur chaque `<a>`. Ils sont stylés dans `assets/src/css/components.css`.

## Largeur du contenu Gutenberg

Dans `page.php`, `single.php` et `front-page.php`, le contenu est dans `.entry-content.prose.max-w-content` : une colonne de texte lisible. Les blocs réglés sur **Large** (`alignwide`) ou **Pleine largeur** (`alignfull`) en sortent grâce à `components.css`. Les blocs maison ajoutent `not-prose` pour ne pas hériter de la mise en forme du texte (voir `docs/blocks.md`).

## Menus

Deux emplacements déclarés dans `inc/setup.php` : `primary` (header) et `footer`. Ils se remplissent dans **Apparence > Menus**. S'ils sont vides, rien ne s'affiche (pas de liste de pages automatique).

Le menu mobile (bouton burger) est géré en JS : voir `docs/js.md`.

## Éléments communs des gabarits

| Élément               | Fichier                                               | Rôle                                                                                                                                              |
| --------------------- | ----------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------- |
| En-tête de page       | `template-parts/page-header.php`                      | Grand titre (`**mot**` surligné) + texte à droite, pastille facultative, variante centrée (404). Arguments : `title`, `text`, `eyebrow`, `center` |
| Filtres par catégorie | `template-parts/term-filter.php`                      | Pastilles « Tout » + une par catégorie, au-dessus des listes (masqué s'il y a moins de 2 catégories)                                              |
| Carte                 | `template-parts/content-card.php`                     | Image arrondie + flèche, catégorie, date, titre, extrait. `featured` = carte large (1er contenu d'une liste). Sans image : carte blanche bordée   |
| Logo                  | `template-parts/site-brand.php`                       | Logo personnalisé, sinon pastille lavande + nom du site                                                                                           |
| Coordonnées / réseaux | `template-parts/site-contact.php`, `site-socials.php` | Footer                                                                                                                                            |
| Fonctions             | `inc/template-tags.php`                               | `starter_primary_term()`, `starter_post_type_list()`, `starter_reading_time()`, titres d'archive sans « Catégorie : »                             |

Introductions des listes : **Actualités** = contenu de la page des articles (Pages > Actualités) ; **Réalisations** = `description` du type de contenu (mu-plugin) ; **catégories** = leur description.

Article seul (`single.php`, aussi pour les réalisations) : retour vers la liste, titre centré, catégorie, date et temps de lecture, grande image, navigation précédent / suivant en cartes, puis « À lire aussi » (3 contenus récents du même type).

> `the_post_navigation()` n'accepte qu'**une** classe dans `class` (WordPress la passe dans `sanitize_html_class()`, qui retire les espaces) : la mise en page se fait sur un conteneur autour.
