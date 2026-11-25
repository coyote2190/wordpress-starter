<?php
/**
 * Page standard (Pages > Ajouter).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<?php // Page qui commence par un hero pleine largeur : le hero porte le <h1>, pas de titre ici. ?>
	<?php $starter_hero_first = starter_starts_with_hero(); ?>
	<article <?php post_class( $starter_hero_first ? 'container pb-12' : 'container py-12' ); ?>>
		<?php if ( ! $starter_hero_first ) : ?>
			<h1 class="mx-auto mb-8 max-w-content text-4xl font-bold"><?php the_title(); ?></h1>
		<?php endif; ?>

		<div class="entry-content prose mx-auto max-w-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
