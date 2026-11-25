<?php
/**
 * Carte d'un contenu dans une liste. Utilisée par template-parts/loop.php.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'group relative flex flex-col overflow-hidden rounded-lg border border-light' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<?php the_post_thumbnail( 'medium_large', array( 'class' => 'aspect-video w-full object-cover' ) ); ?>
	<?php endif; ?>

	<div class="flex flex-1 flex-col gap-3 p-6">
		<time class="text-sm text-gray-500" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>

		<h2 class="text-xl font-bold group-hover:text-primary">
			<?php // Le lien couvre toute la carte grâce à after:absolute after:inset-0. ?>
			<a class="after:absolute after:inset-0" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="text-gray-600"><?php the_excerpt(); ?></div>
	</div>
</article>
