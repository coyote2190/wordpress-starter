<?php
/**
 * Page introuvable.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="container pb-32">
	<?php
	get_template_part(
		'template-parts/page-header',
		null,
		array(
			'eyebrow' => __( 'Erreur 404', 'starter' ),
			'title'   => __( 'Cette page s\'est **égarée**', 'starter' ),
			'text'    => __( 'Elle n\'existe pas ou a été déplacée. Essayez une recherche, ou repartez de l\'accueil.', 'starter' ),
			'center'  => true,
		)
	);
	?>

	<div class="mx-auto flex max-w-xl flex-col items-center gap-6">
		<div class="w-full"><?php get_search_form(); ?></div>
		<?php
		echo starter_button( // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_button().
			array(
				'url'   => home_url( '/' ),
				'title' => __( 'Retour à l\'accueil', 'starter' ),
			),
			'icon',
			'arrow-left'
		);
		?>
	</div>
</div>

<?php
get_footer();
