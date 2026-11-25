<?php
/**
 * Résultats de recherche (?s=…).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="container py-12">
	<header class="mb-10">
		<h1 class="mb-6 text-4xl font-bold">
			<?php
			/* translators: %s : termes recherchés. */
			printf( esc_html__( 'Résultats pour « %s »', 'starter' ), esc_html( get_search_query() ) );
			?>
		</h1>
		<?php get_search_form(); ?>
	</header>

	<?php get_template_part( 'template-parts/loop' ); ?>
</div>

<?php
get_footer();
