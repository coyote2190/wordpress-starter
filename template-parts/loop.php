<?php
/**
 * Boucle commune aux listes (index, archive, search) : grille de cartes + pagination.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

if ( ! have_posts() ) {
	get_template_part( 'template-parts/content', 'none' );
	return;
}
?>
<div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'card' );
	endwhile;
	?>
</div>

<?php
the_posts_pagination(
	array(
		'class'     => 'pagination mt-12',
		'prev_text' => __( 'Précédent', 'starter' ),
		'next_text' => __( 'Suivant', 'starter' ),
	)
);
