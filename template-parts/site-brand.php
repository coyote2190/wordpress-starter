<?php
/**
 * Logo du site : logo personnalisé (Apparence > Personnaliser) s'il existe,
 * sinon pastille de couleur + nom du site. Utilisé dans header.php et footer.php.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

if ( has_custom_logo() ) {
	the_custom_logo();
	return;
}
?>
<a class="inline-flex items-center gap-2.5 font-heading text-lg font-medium no-underline <?php echo esc_attr( $args['class'] ?? '' ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<span class="size-6.5 shrink-0 rounded-full bg-secondary" aria-hidden="true"></span>
	<?php bloginfo( 'name' ); ?>
</a>
