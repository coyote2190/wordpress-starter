<?php
/**
 * Réseaux sociaux (page « Infos du site »), en liens texte. Utilisé dans footer.php.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_socials = starter_option( 'socials', array() );

if ( ! $starter_socials ) {
	return;
}
?>
<ul class="flex flex-wrap gap-5 font-medium text-white">
	<?php foreach ( $starter_socials as $starter_social ) : ?>
		<?php
		$starter_network = $starter_social['network'] ?? array();
		if ( empty( $starter_social['url'] ) || empty( $starter_network['label'] ) ) {
			continue;
		}
		?>
		<li>
			<a class="transition hover:opacity-70" href="<?php echo esc_url( $starter_social['url'] ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( $starter_network['label'] ); ?>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
