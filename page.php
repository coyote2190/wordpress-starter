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
	<article <?php post_class( 'container py-12' ); ?>>
		<h1 class="mx-auto mb-8 max-w-content text-4xl font-bold"><?php the_title(); ?></h1>

		<div class="entry-content prose mx-auto max-w-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
