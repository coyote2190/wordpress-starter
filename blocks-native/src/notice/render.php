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
		'class' => 'not-prose my-8 flex justify-center',
		'role'  => 'note',
	)
);
?>
<div <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<div class="flex justify-center <?php echo esc_attr( starter_block_inner_class( $attributes['align'] ?? null ) ); ?>">
		<div class="flex items-center gap-3 rounded-3xl border border-light bg-white py-2 pr-5 pl-2 text-base leading-snug sm:rounded-full">
			<?php if ( '' !== $badge ) : ?>
				<span class="shrink-0 rounded-full px-3 py-1.5 text-[13px] font-semibold <?php echo esc_attr( $tag_classes ); ?>"><?php echo esc_html( $badge ); ?></span>
			<?php else : ?>
				<span class="shrink-0 rounded-full px-3 py-1.5 text-[13px] font-semibold <?php echo esc_attr( $tag_classes ); ?>" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
			<?php endif; ?>
			<?php // RichText : HTML limité au gras, italique, liens → wp_kses_post. ?>
			<p class="m-0 [&_strong]:font-semibold"><?php echo wp_kses_post( $content ); ?></p>
		</div>
	</div>
</div>
