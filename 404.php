<?php
/**
 * Page introuvable.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="container py-24 text-center">
	<p class="text-6xl font-bold text-primary">404</p>
	<h1 class="mt-4 text-2xl font-bold"><?php esc_html_e( 'Page introuvable', 'starter' ); ?></h1>
	<p class="mt-2 text-gray-600"><?php esc_html_e( 'Cette page n\'existe pas ou a été déplacée.', 'starter' ); ?></p>

	<div class="mx-auto mt-8 max-w-md"><?php get_search_form(); ?></div>

	<a class="mt-8 inline-flex items-center gap-2 font-semibold text-primary hover:underline" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php esc_html_e( 'Retour à l\'accueil', 'starter' ); ?>
		<?php echo starter_icon( 'arrow-right', 'size-4' ); ?>
	</a>
</div>

<?php
get_footer();
