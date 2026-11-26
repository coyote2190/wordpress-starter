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
<form role="search" method="get" class="bg-white border border-light flex focus-within:border-dark gap-2 items-center p-1.5 rounded-full search-form transition" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="<?php echo esc_attr( $starter_search_id ); ?>"><?php esc_html_e( 'Rechercher', 'starter' ); ?></label>
	<?php echo starter_icon( 'search', 'ml-3 size-5 text-soft' ); ?>
	<input
		id="<?php echo esc_attr( $starter_search_id ); ?>"
		class="bg-transparent flex-1 focus:outline-none min-w-0 placeholder:text-soft py-2"
		type="search"
		name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'Rechercher…', 'starter' ); ?>"
	>
	<button class="bg-dark font-medium hover:bg-primary px-5 py-2.5 rounded-full shrink-0 text-white transition" type="submit">
		<?php esc_html_e( 'Rechercher', 'starter' ); ?>
	</button>
</form>
