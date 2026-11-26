<?php
/**
 * Boucle commune aux listes (index, archive, search) : grille de cartes + pagination.
 *
 * Sur la 1re page d'une liste d'articles ou de réalisations, le 1er contenu est
 * mis en avant (carte large, image à gauche).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

if ( ! have_posts() ) {
	get_template_part( 'template-parts/content', 'none' );
	return;
}

$starter_feature_first = ! is_paged() && ! is_search() && ( is_home() || is_archive() );
?>
<div class="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'card', array( 'featured' => $starter_feature_first && 0 === $GLOBALS['wp_query']->current_post ) );
	endwhile;
	?>
</div>

<?php
the_posts_pagination(
	array(
		'class'     => 'pagination mt-16',
		'prev_text' => __( 'Précédent', 'starter' ),
		'next_text' => __( 'Suivant', 'starter' ),
	)
);
