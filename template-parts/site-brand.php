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
<a class="font-heading font-medium gap-2.5 inline-flex items-center no-underline text-lg <?php echo esc_attr( $args['class'] ?? '' ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
	<span class="bg-secondary rounded-full shrink-0 size-6.5" aria-hidden="true"></span>
	<?php bloginfo( 'name' ); ?>
</a>
