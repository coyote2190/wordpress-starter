<?php
/**
 * Formulaire de recherche (pilule blanche). Remplace celui de WordPress partout
 * où get_search_form() est appelé.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_search_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form flex items-center gap-2 rounded-full border border-light bg-white p-1.5 transition focus-within:border-dark" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="<?php echo esc_attr( $starter_search_id ); ?>"><?php esc_html_e( 'Rechercher', 'starter' ); ?></label>
	<?php echo starter_icon( 'search', 'ml-3 size-5 text-soft' ); ?>
	<input
		id="<?php echo esc_attr( $starter_search_id ); ?>"
		class="min-w-0 flex-1 bg-transparent py-2 placeholder:text-soft focus:outline-none"
		type="search"
		name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'Rechercher…', 'starter' ); ?>"
	>
	<button class="shrink-0 rounded-full bg-dark px-5 py-2.5 font-medium text-white transition hover:bg-primary" type="submit">
		<?php esc_html_e( 'Rechercher', 'starter' ); ?>
	</button>
</form>
