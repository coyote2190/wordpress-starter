<?php
/**
 * HTML du bloc « Encadré » sur le site.
 *
 * Variables fournies par WordPress :
 * @var array    $attributes Attributs du bloc (type, content).
 * @var string   $content    Contenu des blocs enfants (inutilisé).
 * @var WP_Block $block      Instance du bloc.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

// Même table que src/notice/styles.js (classes écrites en entier pour Tailwind).
$variants = array(
	'info'    => array( 'border-primary bg-primary/5', 'ℹ️' ),
	'success' => array( 'border-green-600 bg-green-50', '✅' ),
	'warning' => array( 'border-secondary bg-secondary/10', '⚠️' ),
);

$content = $attributes['content'] ?? '';
if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
	return;
}

[ $classes, $icon ] = $variants[ $attributes['type'] ?? 'info' ] ?? $variants['info'];

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'not-prose my-6 border-l-4 py-4 ' . $classes . ( 'full' === ( $attributes['align'] ?? '' ) ? '' : ' rounded-md px-4' ),
		'role'  => 'note',
	)
);
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<div class="flex gap-3 <?php echo esc_attr( starter_block_inner_class( $attributes['align'] ?? null ) ); ?>">
		<span aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
		<?php // RichText : HTML limité au gras, italique, liens → wp_kses_post. ?>
		<p class="m-0"><?php echo wp_kses_post( $content ); ?></p>
	</div>
</div>
