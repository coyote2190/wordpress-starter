<?php
/**
 * Message affiché quand une liste est vide.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="rounded-lg bg-light p-8 text-center">
	<p class="mb-6">
		<?php
		is_search()
			? esc_html_e( 'Aucun résultat. Essayez avec d\'autres mots.', 'starter' )
			: esc_html_e( 'Aucun contenu pour le moment.', 'starter' );
		?>
	</p>
	<?php if ( is_search() ) : ?>
		<div class="mx-auto max-w-md"><?php get_search_form(); ?></div>
	<?php endif; ?>
</div>
