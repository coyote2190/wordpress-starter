<?php
/**
 * Formulaire de recherche. Remplace celui de WordPress partout où
 * get_search_form() est appelé.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_search_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form flex" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="<?php echo esc_attr( $starter_search_id ); ?>"><?php esc_html_e( 'Rechercher', 'starter' ); ?></label>
	<input
		id="<?php echo esc_attr( $starter_search_id ); ?>"
		class="min-w-0 flex-1 rounded-l-md border border-gray-300 px-4 py-2 focus:border-primary focus:outline-none"
		type="search"
		name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( 'Rechercher…', 'starter' ); ?>"
	>
	<button class="rounded-r-md bg-primary px-4 text-white hover:bg-primary/90" type="submit">
		<?php echo starter_icon( 'search', 'size-5', __( 'Lancer la recherche', 'starter' ) ); ?>
	</button>
</form>
