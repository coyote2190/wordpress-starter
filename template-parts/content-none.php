<?php
/**
 * Message affiché quand une liste est vide.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="flex flex-col items-center gap-4 rounded-[2rem] border border-light bg-white px-6 py-16 text-center">
	<span class="inline-flex size-14 items-center justify-center rounded-full bg-secondary">
		<?php echo starter_icon( is_search() ? 'search' : 'lightbulb', 'size-6' ); ?>
	</span>
	<p class="max-w-md text-lg text-muted">
		<?php
		is_search()
			? esc_html_e( 'Aucun résultat. Essayez avec d\'autres mots.', 'starter' )
			: esc_html_e( 'Aucun contenu pour le moment.', 'starter' );
		?>
	</p>
</div>
