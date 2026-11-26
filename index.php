<?php
/**
 * Gabarit de secours : utilisé quand aucun gabarit plus précis n'existe.
 * En pratique : la page des articles (blog) si aucun home.php.
 *
 * Le texte d'introduction de la page des articles = le contenu de cette page
 * (Pages > Actualités), que WordPress n'affiche pas lui-même.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();

$starter_blog_page = (int) get_option( 'page_for_posts' );
?>

<div class="container pb-24">
	<?php
	if ( is_home() && $starter_blog_page && ! is_front_page() ) {
		get_template_part(
			'template-parts/page-header',
			null,
			array(
				'title' => get_the_title( $starter_blog_page ),
				'text'  => wp_strip_all_tags( (string) get_post_field( 'post_content', $starter_blog_page ) ),
			)
		);
		get_template_part(
			'template-parts/term-filter',
			null,
			array(
				'taxonomy' => 'category',
				'all_url'  => get_permalink( $starter_blog_page ),
			)
		);
	} else {
		get_template_part( 'template-parts/page-header', null, array( 'title' => get_bloginfo( 'name' ) ) );
	}

	get_template_part( 'template-parts/loop' );
	?>
</div>

<?php
get_footer();
