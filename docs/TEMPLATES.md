# WordPress template hierarchy

Which file WordPress uses for which page type, and what each one does in this theme.

## How WordPress picks a template

For every URL, WordPress looks for the most specific template available and falls back down the chain until it finds one. `index.php` is the last resort — it always exists and catches everything.

That's why a theme without `page.php` renders pages with the post-list layout: WordPress had nothing better to fall back on.

## Template files

### `index.php`

**Fallback for everything.** Used when no more specific template exists.
Currently renders the blog home (list of posts) with the hero component on top.

### `page.php`

**Static pages** — About, Contact, Legal notice, etc.
Uses `the_content()` so Gutenberg blocks render fully. Single `<h1>` title, optional featured image.

### `single.php`

**Individual blog posts.**
Same idea as `page.php` but with post metadata (date, author, categories) and post navigation.

### `archive.php`

**Any list of posts**: category, tag, author, date, or custom post type archive.
Shows the archive title (e.g. "Category: News") and loops through matching posts.

### `search.php`

**Search results page.**
Displays the query, the result count, and the matching posts — or an empty state.

### `404.php`

**Page not found.**
Static content only: no loop, since there's nothing to display.

## Partials

### `header.php` / `footer.php`

Included by every template via `get_header()` and `get_footer()`.
Contain `wp_head()` and `wp_footer()` — required hooks, never remove them (plugins and the admin bar depend on them).

## Fallback chain (simplified)

| URL type                 | WordPress looks for                                                             |
| ------------------------ | ------------------------------------------------------------------------------- |
| Static page              | `page-{slug}.php` → `page-{id}.php` → `page.php` → `singular.php` → `index.php` |
| Blog post                | `single-post.php` → `single.php` → `singular.php` → `index.php`                 |
| Category                 | `category-{slug}.php` → `category.php` → `archive.php` → `index.php`            |
| Custom post type archive | `archive-{cpt}.php` → `archive.php` → `index.php`                               |
| Search                   | `search.php` → `index.php`                                                      |
| 404                      | `404.php` → `index.php`                                                         |

Full reference: [Template Hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/)

## Per-client customization

Two ways to override without touching the shared templates:

**Page templates** — a file with a header comment, selectable in the page editor:

```php
<?php
/* Template Name: Landing page */
```

Place these in `page-templates/`.

**Specific templates** — `page-contact.php` targets the page with slug `contact`, `single-realisation.php` targets the `realisation` CPT.

Prefer these over editing `page.php` directly, so the starter stays mergeable across projects.
