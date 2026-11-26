<?php
/**
 * Résultats de recherche (?s=…).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();

global $wp_query;
?>

<div class="container pb-24">
	<?php
	get_template_part(
		'template-parts/page-header',
		null,
		array(
			'eyebrow' => __( 'Recherche', 'starter' ),
			/* translators: %s : termes recherchés. */
			'title'   => sprintf( __( 'Résultats pour **%s**', 'starter' ), get_search_query( false ) ),
			/* translators: %d : nombre de résultats. */
			'text'    => sprintf( _n( '%d contenu trouvé.', '%d contenus trouvés.', (int) $wp_query->found_posts, 'starter' ), (int) $wp_query->found_posts ),
		)
	);
	?>

	<div class="max-w-xl mb-12"><?php get_search_form(); ?></div>

	<?php get_template_part( 'template-parts/loop' ); ?>
</div>

<?php
get_footer();
