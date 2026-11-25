<?php
/**
 * Coordonnées et réseaux sociaux (page « Infos du site »). Utilisé dans footer.php.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_phone   = starter_option( 'phone' );
$starter_email   = starter_option( 'email' );
$starter_address = starter_option( 'address' );
$starter_socials = starter_option( 'socials', array() );

if ( ! $starter_phone && ! $starter_email && ! $starter_address && ! $starter_socials ) {
	return;
}
?>
<div class="flex flex-col gap-3 text-sm">
	<?php if ( $starter_phone ) : ?>
		<a class="inline-flex items-center gap-2 hover:text-white" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $starter_phone ) ); ?>">
			<?php echo starter_icon( 'phone', 'size-4' ); ?>
			<?php echo esc_html( $starter_phone ); ?>
		</a>
	<?php endif; ?>

	<?php if ( $starter_email ) : ?>
		<?php // antispambot() encode l'adresse pour gêner les robots collecteurs. ?>
		<a class="inline-flex items-center gap-2 hover:text-white" href="mailto:<?php echo esc_attr( antispambot( $starter_email ) ); ?>">
			<?php echo starter_icon( 'mail', 'size-4' ); ?>
			<?php echo esc_html( antispambot( $starter_email ) ); ?>
		</a>
	<?php endif; ?>

	<?php if ( $starter_address ) : ?>
		<p class="inline-flex items-start gap-2">
			<?php echo starter_icon( 'map-pin', 'size-4 mt-0.5' ); ?>
			<span><?php echo wp_kses( $starter_address, array( 'br' => array() ) ); ?></span>
		</p>
	<?php endif; ?>

	<?php if ( $starter_socials ) : ?>
		<ul class="flex flex-wrap gap-4">
			<?php foreach ( $starter_socials as $starter_social ) : ?>
				<?php
				$starter_network = $starter_social['network'] ?? array();
				if ( empty( $starter_social['url'] ) || empty( $starter_network['value'] ) ) {
					continue;
				}
				// Icône si assets/icons/{réseau}.svg existe, sinon le nom du réseau en texte.
				$starter_icon = starter_icon_exists( $starter_network['value'] )
					? starter_icon( $starter_network['value'], 'size-5', $starter_network['label'] )
					: '';
				?>
				<li>
					<a class="hover:text-white" href="<?php echo esc_url( $starter_social['url'] ); ?>" target="_blank" rel="noopener">
						<?php echo $starter_icon ? $starter_icon : esc_html( $starter_network['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput -- SVG généré par starter_icon(). ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>
