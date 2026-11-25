<?php
/**
 * Gabarit de secours : utilisé quand aucun gabarit plus précis n'existe.
 * En pratique : la page des articles (blog) si aucun home.php.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="container py-12">
	<?php if ( is_home() && ! is_front_page() ) : ?>
		<h1 class="mb-10 text-4xl font-bold"><?php single_post_title(); ?></h1>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/loop' ); ?>
</div>

<?php
get_footer();
