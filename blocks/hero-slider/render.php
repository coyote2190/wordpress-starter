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
		echo '<p class="border border-dashed border-gray-300 p-8 rounded text-center text-gray-500">'
			. esc_html__( 'Hero slider : remplissez le titre et ajoutez des images dans le panneau de droite.', 'starter' )
			. '</p>';
	}
	return;
}

$multiple = count( $slides ) > 1;

$slider_attrs = array(
	// touch-pan-y : le navigateur garde le défilement vertical, le glissement horizontal va au JS.
	'class="bg-secondary h-[clamp(20rem,52vw,40rem)] isolate overflow-hidden relative rounded-[2rem] touch-pan-y"',
	'aria-roledescription="' . esc_attr__( 'carrousel', 'starter' ) . '"',
	'aria-label="' . esc_attr__( 'Diaporama', 'starter' ) . '"',
);
// Le JS n'est chargé que s'il y a plusieurs images, et jamais dans l'éditeur.
if ( $multiple && ! $is_preview ) {
	$slider_attrs[] = 'data-module="hero-slider"';
	$slider_attrs[] = 'data-autoplay="' . esc_attr( (string) $autoplay ) . '"';
}

$wrapper = get_block_wrapper_attributes( array( 'class' => 'mb-8 mt-6 not-prose' ) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
<div class="<?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">

	<?php // ------- Titre, texte, boutons ------- ?>
	<div class="flex flex-wrap gap-x-16 gap-y-8 items-end mb-10">
		<?php if ( $title ) : ?>
			<h1 class="flex-[2_1_32rem] leading-[.98] text-[clamp(3rem,7vw,6.5rem)] text-balance tracking-[-.045em]">
				<?php echo starter_format_title( $title, 'title-mark', 'underline decoration-secondary decoration-wavy underline-offset-8' ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_format_title(). ?>
			</h1>
		<?php endif; ?>

		<?php if ( $text || ! empty( $primary['url'] ) || ! empty( $secondary['url'] ) ) : ?>
			<div class="flex flex-[1_1_18rem] flex-col gap-6">
				<?php if ( $text ) : ?>
					<p class="leading-normal text-[1.1875rem] text-muted"><?php echo esc_html( $text ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $primary['url'] ) || ! empty( $secondary['url'] ) ) : ?>
					<div class="flex flex-wrap gap-2.5 items-center" <?php echo $is_preview ? 'inert' : ''; ?>>
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
						class="absolute data-active:opacity-100 data-active:visible duration-1000 inset-0 invisible opacity-0 transition-[opacity,visibility]"
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
								'class'         => 'object-cover size-full',
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
				<div class="absolute bg-white bottom-5 flex gap-2 items-center pl-3 pr-1.5 py-1.5 right-5 rounded-full z-10">
					<?php // Zone cliquable de 24 px (WCAG 2.5.8), point visuel de 8 px à l'intérieur. ?>
					<div class="flex items-center">
						<?php foreach ( $slides as $i => $slide ) : ?>
							<button
								class="group h-6 inline-flex items-center justify-center min-w-6"
								type="button"
								aria-label="<?php /* translators: %d : numéro de l'image. */ echo esc_attr( sprintf( __( 'Aller à l\'image %d', 'starter' ), $i + 1 ) ); ?>"
								aria-current="<?php echo 0 === $i ? 'true' : 'false'; ?>"
								data-slider-dot
							><span class="bg-dark duration-400 group-aria-[current=true]:opacity-100 group-aria-[current=true]:w-7 h-2 opacity-30 rounded-full transition-all w-2"></span></button>
						<?php endforeach; ?>
					</div>

					<?php if ( $autoplay ) : ?>
						<?php // Obligatoire (WCAG 2.2.2) : tout contenu qui défile seul doit pouvoir être mis en pause. ?>
						<button class="hover:bg-light inline-flex items-center justify-center rounded-full size-8 text-dark transition" type="button" data-slider-pause aria-pressed="false">
							<span data-slider-pause-icon><?php echo starter_icon( 'pause', 'size-3.5', __( 'Mettre en pause le défilement', 'starter' ) ); ?></span>
							<span data-slider-play-icon hidden><?php echo starter_icon( 'play', 'size-3.5', __( 'Reprendre le défilement', 'starter' ) ); ?></span>
						</button>
					<?php endif; ?>

					<button class="bg-[#f2f2f2] hover:bg-light inline-flex items-center justify-center rounded-full size-10 text-dark transition" type="button" data-slider-prev>
						<?php echo starter_icon( 'arrow-left', 'size-4', __( 'Image précédente', 'starter' ) ); ?>
					</button>
					<button class="bg-dark hover:bg-primary inline-flex items-center justify-center rounded-full size-10 text-white transition" type="button" data-slider-next>
						<?php echo starter_icon( 'arrow-right', 'size-4', __( 'Image suivante', 'starter' ) ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
</section>
