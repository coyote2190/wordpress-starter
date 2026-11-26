<?php
/**
 * Message affiché quand une liste est vide.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="bg-white border border-light flex flex-col gap-4 items-center px-6 py-16 rounded-[2rem] text-center">
	<span class="bg-secondary inline-flex items-center justify-center rounded-full size-14">
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
