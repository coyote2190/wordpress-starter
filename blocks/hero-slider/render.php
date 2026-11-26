<?php
/**
 * Rendu du bloc « Hero slider » : grand titre + texte + boutons, puis diaporama d'images.
 *
 * Sans JS : la première image s'affiche (les autres sont masquées).
 * Avec JS : assets/src/js/modules/hero-slider.js gère flèches, points,
 * défilement automatique, clavier et glisser au doigt.
 *
 * @var array $block      Réglages du bloc.
 * @var bool  $is_preview true dans l'éditeur.
 *
 * Champs : scf-json/group_starter_block_hero_slider.json
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$title     = (string) starter_field( 'title' );
$text      = starter_field( 'text' );
$primary   = starter_field( 'button_primary', false, array() );
$secondary = starter_field( 'button_secondary', false, array() );
$slides    = array_values( array_filter( (array) starter_field( 'slides', false, array() ), static fn( $s ) => ! empty( $s['image'] ) ) );
// Dans l'éditeur : pas de défilement automatique (gênant pour rédiger).
$autoplay = $is_preview ? 0 : max( 0, (int) starter_field( 'autoplay', false, 6 ) );

if ( '' === $title && ! $slides ) {
	if ( $is_preview ) {
		echo '<p class="rounded border border-dashed border-gray-300 p-8 text-center text-gray-500">'
			. esc_html__( 'Hero slider : remplissez le titre et ajoutez des images dans le panneau de droite.', 'starter' )
			. '</p>';
	}
	return;
}

$multiple = count( $slides ) > 1;

$slider_attrs = array(
	// touch-pan-y : le navigateur garde le défilement vertical, le glissement horizontal va au JS.
	'class="relative isolate h-[clamp(20rem,52vw,40rem)] touch-pan-y overflow-hidden rounded-[2rem] bg-secondary"',
	'aria-roledescription="' . esc_attr__( 'carrousel', 'starter' ) . '"',
	'aria-label="' . esc_attr__( 'Diaporama', 'starter' ) . '"',
);
// Le JS n'est chargé que s'il y a plusieurs images, et jamais dans l'éditeur.
if ( $multiple && ! $is_preview ) {
	$slider_attrs[] = 'data-module="hero-slider"';
	$slider_attrs[] = 'data-autoplay="' . esc_attr( (string) $autoplay ) . '"';
}

$wrapper = get_block_wrapper_attributes( array( 'class' => 'not-prose mt-6 mb-8' ) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
<div class="<?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">

	<?php // ------- Titre, texte, boutons ------- ?>
	<div class="mb-10 flex flex-wrap items-end gap-x-16 gap-y-8">
		<?php if ( $title ) : ?>
			<h1 class="flex-[2_1_32rem] text-[clamp(3rem,7vw,6.5rem)] leading-[.98] tracking-[-.045em] text-balance">
				<?php echo starter_format_title( $title, 'title-mark', 'underline decoration-secondary decoration-wavy underline-offset-8' ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_format_title(). ?>
			</h1>
		<?php endif; ?>

		<?php if ( $text || ! empty( $primary['url'] ) || ! empty( $secondary['url'] ) ) : ?>
			<div class="flex flex-[1_1_18rem] flex-col gap-6">
				<?php if ( $text ) : ?>
					<p class="text-[1.1875rem] leading-normal text-muted"><?php echo esc_html( $text ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $primary['url'] ) || ! empty( $secondary['url'] ) ) : ?>
					<div class="flex flex-wrap items-center gap-2.5" <?php echo $is_preview ? 'inert' : ''; ?>>
						<?php echo starter_button( is_array( $primary ) ? $primary : array(), 'icon', 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_button(). ?>
						<?php echo starter_button( is_array( $secondary ) ? $secondary : array(), 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_button(). ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php // ------- Diaporama ------- ?>
	<?php if ( $slides ) : ?>
		<div <?php echo implode( ' ', $slider_attrs ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé ci-dessus. ?>>
			<div class="absolute inset-0" data-slider-track aria-live="<?php echo $autoplay ? 'off' : 'polite'; ?>">
				<?php foreach ( $slides as $i => $slide ) : ?>
					<?php $first = 0 === $i; ?>
					<div
						class="invisible absolute inset-0 opacity-0 transition-[opacity,visibility] duration-1000 data-active:visible data-active:opacity-100"
						role="group"
						aria-roledescription="<?php esc_attr_e( 'diapositive', 'starter' ); ?>"
						aria-label="<?php echo esc_attr( sprintf( '%d / %d', $i + 1, count( $slides ) ) ); ?>"
						data-slide
						<?php echo $first ? 'data-active' : 'inert aria-hidden="true"'; ?>
					>
						<?php
						echo wp_get_attachment_image(
							(int) $slide['image'],
							'full',
							false,
							array(
								'class'         => 'size-full object-cover',
								'sizes'         => '(min-width: 80rem) 80rem, 100vw',
								// 1re image : affichée immédiatement, priorité haute. Les suivantes : en différé.
								'loading'       => $first ? 'eager' : 'lazy',
								'fetchpriority' => $first ? 'high' : 'low',
							)
						);
						?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( $multiple ) : ?>
				<?php // Pilule de commandes en bas à droite : points, pause, flèches. ?>
				<div class="absolute right-5 bottom-5 z-10 flex items-center gap-3 rounded-full bg-white py-1.5 pr-1.5 pl-4.5">
					<div class="flex items-center gap-1.5">
						<?php foreach ( $slides as $i => $slide ) : ?>
							<button
								class="h-2 w-2 rounded-full bg-dark opacity-30 transition-all duration-400 aria-[current=true]:w-7 aria-[current=true]:opacity-100"
								type="button"
								aria-label="<?php echo esc_attr( sprintf( __( 'Aller à l\'image %d', 'starter' ), $i + 1 ) ); ?>"
								aria-current="<?php echo 0 === $i ? 'true' : 'false'; ?>"
								data-slider-dot
							></button>
						<?php endforeach; ?>
					</div>

					<?php if ( $autoplay ) : ?>
						<?php // Obligatoire (WCAG 2.2.2) : tout contenu qui défile seul doit pouvoir être mis en pause. ?>
						<button class="inline-flex size-8 items-center justify-center rounded-full text-dark transition hover:bg-light" type="button" data-slider-pause aria-pressed="false">
							<span data-slider-pause-icon><?php echo starter_icon( 'pause', 'size-3.5', __( 'Mettre en pause le défilement', 'starter' ) ); ?></span>
							<span data-slider-play-icon hidden><?php echo starter_icon( 'play', 'size-3.5', __( 'Reprendre le défilement', 'starter' ) ); ?></span>
						</button>
					<?php endif; ?>

					<button class="inline-flex size-10 items-center justify-center rounded-full bg-[#f2f2f2] text-dark transition hover:bg-light" type="button" data-slider-prev>
						<?php echo starter_icon( 'arrow-left', 'size-4', __( 'Image précédente', 'starter' ) ); ?>
					</button>
					<button class="inline-flex size-10 items-center justify-center rounded-full bg-dark text-white transition hover:bg-primary" type="button" data-slider-next>
						<?php echo starter_icon( 'arrow-right', 'size-4', __( 'Image suivante', 'starter' ) ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
</section>
