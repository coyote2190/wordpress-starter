<?php
/**
 * Coordonnées du site (page « Infos du site ») : téléphone, e-mail, adresse.
 * Utilisé dans footer.php. Les réseaux sociaux : template-parts/site-socials.php.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_phone   = starter_option( 'phone' );
$starter_email   = starter_option( 'email' );
$starter_address = starter_option( 'address' );

if ( ! $starter_phone && ! $starter_email && ! $starter_address ) {
	return;
}
?>
<?php if ( $starter_phone ) : ?>
	<a class="w-fit transition hover:text-white" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $starter_phone ) ); ?>">
		<?php echo esc_html( $starter_phone ); ?>
	</a>
<?php endif; ?>

<?php if ( $starter_email ) : ?>
	<?php // antispambot() encode l'adresse pour gêner les robots collecteurs. ?>
	<a class="w-fit transition hover:text-white" href="mailto:<?php echo esc_attr( antispambot( $starter_email ) ); ?>">
		<?php echo esc_html( antispambot( $starter_email ) ); ?>
	</a>
<?php endif; ?>

<?php if ( $starter_address ) : ?>
	<?php // Adresse sur une ligne : « Quai des Chevillards, 59800 Lille ». ?>
	<address class="not-italic"><?php echo esc_html( implode( ', ', array_filter( array_map( 'trim', preg_split( '/\R|<br\s*\/?>/i', $starter_address ) ) ) ) ); ?></address>
<?php endif; ?>
