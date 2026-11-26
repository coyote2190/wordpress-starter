<?php
/**
 * HTML du bloc « Encadré » sur le site : pilule blanche centrée, étiquette à gauche.
 *
 * Variables fournies par WordPress :
 * @var array    $attributes Attributs du bloc (type, badge, content).
 * @var string   $content    Contenu des blocs enfants (inutilisé).
 * @var WP_Block $block      Instance du bloc.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

// Même table que src/notice/styles.js (classes écrites en entier pour Tailwind).
$variants = array(
	'info'    => array( 'bg-secondary text-dark', 'ℹ️' ),
	'success' => array( 'bg-green-100 text-green-900', '✅' ),
	'warning' => array( 'bg-amber-100 text-amber-900', '⚠️' ),
);

$content = $attributes['content'] ?? '';
if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
	return;
}

[ $tag_classes, $icon ] = $variants[ $attributes['type'] ?? 'info' ] ?? $variants['info'];
$badge                  = trim( (string) ( $attributes['badge'] ?? '' ) );

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'flex justify-center my-8 not-prose',
		'role'  => 'note',
	)
);
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<div class="flex justify-center <?php echo esc_attr( starter_block_inner_class( $attributes['align'] ?? null ) ); ?>">
		<div class="bg-white border border-light flex gap-3 items-center leading-snug pl-2 pr-5 py-2 rounded-3xl sm:rounded-full text-base">
			<?php if ( '' !== $badge ) : ?>
				<span class="font-semibold px-3 py-1.5 rounded-full shrink-0 text-[13px] <?php echo esc_attr( $tag_classes ); ?>"><?php echo esc_html( $badge ); ?></span>
			<?php else : ?>
				<span class="font-semibold px-3 py-1.5 rounded-full shrink-0 text-[13px] <?php echo esc_attr( $tag_classes ); ?>" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
			<?php endif; ?>
			<?php // RichText : HTML limité au gras, italique, liens → wp_kses_post. ?>
			<p class="[&_strong]:font-semibold m-0"><?php echo wp_kses_post( $content ); ?></p>
		</div>
	</div>
</div>
