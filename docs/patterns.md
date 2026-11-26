# Compositions (patterns) et traductions

## Compositions

Des pages et sections toutes faites, construites avec les blocs du thème : le rédacteur insère une composition puis remplace les textes et ajoute ses images.

| Fichier                      | Composition                                                     | Catégorie        |
| ---------------------------- | --------------------------------------------------------------- | ---------------- |
| `patterns/page-accueil.php`  | Hero, chiffres clés (bento), arguments qui défilent, CTA e-mail | Pages du site    |
| `patterns/page-a-propos.php` | Hero, histoire, valeurs (cartes), CTA e-mail                    | Pages du site    |
| `patterns/page-services.php` | Hero, services (cartes avec liens), « Pourquoi nous », FAQ, CTA | Pages du site    |
| `patterns/page-contact.php`  | Hero sans image, formulaire de contact                          | Pages du site    |
| `patterns/section-faq.php`   | Questions dépliables (blocs « Détails »)                        | Sections du site |

**Où les trouver** : à la création d'une page, WordPress propose « Choisir une composition » (grâce à `Block Types: core/post-content`). Sinon : bouton **+** > onglet **Compositions**.

Les compositions de WordPress.org sont désactivées (`inc/patterns.php`) : elles ne suivent pas le design du thème.

### Ajouter une composition

Un fichier PHP dans `patterns/`, enregistré automatiquement grâce à son en-tête :

```php
<?php
/**
 * Title: Page « Tarifs »
 * Slug: starter/page-tarifs
 * Categories: starter-pages
 * Post Types: page
 * Block Types: core/post-content
 */

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
	'features',
	array(
		'title' => __( 'Nos formules', 'starter' ),
		'items' => array(
			array( 'icon' => 'star', 'title' => __( 'Essentiel', 'starter' ) ),
		),
	),
	array( 'align' => 'wide' )
);
?>
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Un paragraphe classique.', 'starter' ); ?></p>
<!-- /wp:paragraph -->
```

- `starter_pattern_block( 'nom', $valeurs, $attributs )` écrit un bloc SCF à partir des **noms** de champs : les clés `field_…` sont retrouvées dans `scf-json/`. Un répéteur = une liste de lignes. Un nom inconnu est signalé (avec `WP_DEBUG`).
- Les blocs WordPress (titre, paragraphe…) s'écrivent en HTML commenté, comme l'éditeur les enregistre. Le plus simple : construire la section dans l'éditeur, puis **⋮ > Copier** et coller dans le fichier.
- Une composition peut en inclure une autre : `<!-- wp:pattern {"slug":"starter/section-faq"} /-->`.
- Pas d'ID d'image : ils changent d'un site à l'autre. Le rédacteur ajoute ses images.
- Textes dans `__()` : ils sont traduisibles.

> **Nouvelle composition invisible ?** En dehors du mode développement, WordPress garde la liste en cache. Dans `wp-config.php` du site local : `define( 'WP_DEVELOPMENT_MODE', 'theme' );`, ou une fois : `wp eval 'wp_get_theme()->delete_pattern_cache();'`.

## Traductions

Tous les textes du thème et du mu-plugin sont écrits en français dans `__( '…', 'starter' )`.

`npm run pot` (WP-CLI) regénère `languages/starter.pot`, le modèle à donner à un traducteur ou à ouvrir dans **Poedit** pour créer `languages/starter-en_US.po` / `.mo`. WordPress charge la traduction de la langue du site (`load_theme_textdomain()` dans `inc/setup.php`).

Le dossier `demo/` est exclu : outil interne.
