<?php
/**
 * Listes : catégorie, étiquette, auteur, date, archive d'un type de contenu.
 *
 * Introduction : description de la catégorie, ou du type de contenu
 * (`description` dans register_post_type()).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Type de contenu listé : filtres par catégorie de ce type, pastille « Tout » vers sa liste.
$starter_post_type = is_post_type_archive() ? (string) get_query_var( 'post_type' ) : ( is_tax() ? (string) ( get_taxonomy( get_queried_object()->taxonomy )->object_type[0] ?? 'post' ) : 'post' );
$starter_list      = starter_post_type_list( $starter_post_type );
?>

<div class="container pb-24">
	<?php
	get_template_part(
		'template-parts/page-header',
		null,
		array(
			'title'   => wp_strip_all_tags( get_the_archive_title() ),
			'text'    => wp_strip_all_tags( get_the_archive_description() ),
			// Sur une catégorie : rappel de la liste parente (« Actualités », « Réalisations »).
			'eyebrow' => ( is_category() || is_tax() ) && $starter_list ? $starter_list['label'] : '',
		)
	);

	if ( is_category() || is_tax() || is_post_type_archive() ) {
		get_template_part(
			'template-parts/term-filter',
			null,
			array(
				'taxonomy' => starter_post_taxonomy( $starter_post_type ),
				'all_url'  => $starter_list['url'] ?? '',
			)
		);
	}

	get_template_part( 'template-parts/loop' );
	?>
</div>

<?php
get_footer();
