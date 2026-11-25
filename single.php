<?php
/**
 * Article seul (et tout type de contenu sans gabarit single-{type}.php).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'container py-12' ); ?>>
		<header class="mx-auto mb-8 max-w-content">
			<p class="mb-2 text-sm text-gray-500">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			</p>
			<h1 class="text-4xl font-bold"><?php the_title(); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="mx-auto mb-10 max-w-wide">
				<?php the_post_thumbnail( 'large', array( 'class' => 'w-full rounded-lg' ) ); ?>
			</figure>
		<?php endif; ?>

		<div class="entry-content prose mx-auto max-w-content">
			<?php the_content(); ?>
		</div>

		<?php
		the_post_navigation(
			array(
				'class'     => 'post-navigation mx-auto mt-12 max-w-content border-t border-light pt-6',
				'prev_text' => '← %title',
				'next_text' => '%title →',
			)
		);
		?>
	</article>
	<?php
endwhile;

get_footer();
