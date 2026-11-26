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
	<article <?php post_class( 'container pb-24' ); ?>>
		<?php
		// Page qui commence par un Hero / Hero slider : le bloc porte le <h1>, pas d'en-tête ici.
		if ( ! starter_starts_with_hero() ) {
			// Page de texte : en-tête aligné sur la colonne de lecture.
			echo '<div class="max-w-content mx-auto">';
			get_template_part(
				'template-parts/page-header',
				null,
				array(
					'title' => get_the_title(),
					'text'  => has_excerpt() ? get_the_excerpt() : '',
				)
			);
			echo '</div>';
		}
		?>

		<div class="entry-content max-w-content mx-auto prose prose-lg">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
