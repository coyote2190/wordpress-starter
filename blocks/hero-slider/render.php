<?php
/**
 * Rendu du bloc « Hero slider ».
 *
 * Sans JS : la première diapositive s'affiche (les autres sont masquées).
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

$slides   = array_values( array_filter( (array) starter_field( 'slides', false, array() ), static fn( $s ) => ! empty( $s['title'] ) ) );
$height   = (string) starter_field( 'height', false, 'screen' );
$position = (string) starter_field( 'content_align', false, 'left' );
$overlay  = (string) starter_field( 'overlay', false, 'medium' );
// Dans l'éditeur : pas de défilement automatique (gênant pour rédiger).
$autoplay = $is_preview ? 0 : max( 0, (int) starter_field( 'autoplay', false, 6 ) );

if ( ! $slides ) {
	if ( $is_preview ) {
		echo '<p class="rounded border border-dashed border-gray-300 p-8 text-center text-gray-500">'
			. esc_html__( 'Hero slider : ajoutez des diapositives dans le panneau de droite.', 'starter' )
			. '</p>';
	}
	return;
}

$is_full  = 'full' === ( $block['align'] ?? 'full' );
$multiple = count( $slides ) > 1;

// Classes écrites EN ENTIER pour Tailwind.
$heights = array(
	// 100 % de l'écran, moins la barre d'admin WordPress quand on est connecté.
	'screen' => 'h-[calc(100svh-var(--wp-admin--admin-bar--height,0px))] min-h-[32rem]',
	'large'  => 'h-[70svh] min-h-[28rem]',
);
$overlays = array(
	'light'  => 'bg-dark/20',
	'medium' => 'bg-dark/45',
	'strong' => 'bg-dark/70',
);
$positions = array(
	'left'   => 'items-start text-left',
	'center' => 'items-center text-center',
);

$attrs = array(
	// touch-pan-y : le navigateur garde le défilement vertical, le glissement horizontal va au JS.
	'class'                => 'not-prose relative isolate touch-pan-y overflow-hidden bg-dark text-white' . ( $is_full ? '' : ' my-10 rounded-xl' ),
	'aria-roledescription' => __( 'carrousel', 'starter' ),
	'aria-label'           => __( 'Diaporama', 'starter' ),
);
// Le JS n'est chargé que s'il y a plusieurs diapositives, et jamais dans l'éditeur.
if ( $multiple && ! $is_preview ) {
	$attrs['data-module']   = 'hero-slider';
	$attrs['data-autoplay'] = (string) $autoplay;
}
$wrapper = get_block_wrapper_attributes( $attrs );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<div class="relative <?php echo esc_attr( $heights[ $height ] ?? $heights['screen'] ); ?>" data-slider-track aria-live="<?php echo $autoplay ? 'off' : 'polite'; ?>">
		<?php foreach ( $slides as $i => $slide ) : ?>
			<?php
			$first     = 0 === $i;
			$primary   = is_array( $slide['button_primary'] ?? null ) ? $slide['button_primary'] : array();
			$secondary = is_array( $slide['button_secondary'] ?? null ) ? $slide['button_secondary'] : array();
			// Un seul <h1> par page : le titre de la 1re diapositive.
			$tag = $first ? 'h1' : 'h2';
			?>
			<div
				class="invisible absolute inset-0 opacity-0 transition-[opacity,visibility] duration-700 data-active:visible data-active:opacity-100"
				role="group"
				aria-roledescription="<?php esc_attr_e( 'diapositive', 'starter' ); ?>"
				aria-label="<?php echo esc_attr( sprintf( '%d / %d', $i + 1, count( $slides ) ) ); ?>"
				data-slide
				<?php echo $first ? 'data-active' : 'inert aria-hidden="true"'; ?>
			>
				<?php if ( ! empty( $slide['image'] ) ) : ?>
					<?php
					echo wp_get_attachment_image(
						(int) $slide['image'],
						'full',
						false,
						array(
							'class'         => 'absolute inset-0 size-full object-cover',
							'sizes'         => '100vw',
							// 1re image : affichée immédiatement, priorité haute. Les suivantes : en différé.
							'loading'       => $first ? 'eager' : 'lazy',
							'fetchpriority' => $first ? 'high' : 'low',
						)
					);
					?>
				<?php endif; ?>
				<div class="absolute inset-0 <?php echo esc_attr( $overlays[ $overlay ] ?? $overlays['medium'] ); ?>"></div>

				<div class="relative flex h-full flex-col justify-center gap-6 <?php echo esc_attr( ( $positions[ $position ] ?? $positions['left'] ) . ' ' . ( $is_full ? starter_block_inner_class( 'full' ) : 'px-8 md:px-16' ) ); ?>">
					<<?php echo $tag; // phpcs:ignore -- h1 ou h2. ?> class="max-w-4xl text-5xl leading-none font-bold uppercase md:text-7xl">
						<?php echo esc_html( $slide['title'] ); ?>
						<?php if ( ! empty( $slide['title_highlight'] ) ) : ?>
							<span class="block font-accent font-normal italic"><?php echo esc_html( $slide['title_highlight'] ); ?></span>
						<?php endif; ?>
					</<?php echo $tag; // phpcs:ignore ?>>

					<?php if ( ! empty( $slide['text'] ) ) : ?>
						<p class="max-w-2xl text-lg md:text-xl"><?php echo esc_html( $slide['text'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $primary['url'] ) || ! empty( $secondary['url'] ) ) : ?>
						<div class="mt-4 flex flex-wrap gap-4">
							<?php if ( ! empty( $primary['url'] ) ) : ?>
								<a class="inline-flex min-w-44 justify-center bg-white px-8 py-4 font-accent text-lg text-dark italic no-underline transition hover:bg-light" href="<?php echo esc_url( $primary['url'] ); ?>" <?php echo '_blank' === ( $primary['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>>
									<?php echo esc_html( $primary['title'] ?: __( 'En savoir plus', 'starter' ) ); ?>
								</a>
							<?php endif; ?>
							<?php if ( ! empty( $secondary['url'] ) ) : ?>
								<a class="inline-flex min-w-44 justify-center border border-white px-8 py-4 font-accent text-lg text-white italic no-underline transition hover:bg-white hover:text-dark" href="<?php echo esc_url( $secondary['url'] ); ?>" <?php echo '_blank' === ( $secondary['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>>
									<?php echo esc_html( $secondary['title'] ?: __( 'Découvrir', 'starter' ) ); ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( $multiple ) : ?>
		<?php // Flèches (masquées sur mobile : on y glisse au doigt). ?>
		<button class="absolute top-1/2 left-4 z-10 hidden -translate-y-1/2 p-2 text-white transition hover:-translate-x-1 md:block lg:left-8" type="button" data-slider-prev>
			<?php echo starter_icon( 'arrow-left', 'size-10 stroke-1', __( 'Diapositive précédente', 'starter' ) ); ?>
		</button>
		<button class="absolute top-1/2 right-4 z-10 hidden -translate-y-1/2 p-2 text-white transition hover:translate-x-1 md:block lg:right-8" type="button" data-slider-next>
			<?php echo starter_icon( 'arrow-right', 'size-10 stroke-1', __( 'Diapositive suivante', 'starter' ) ); ?>
		</button>

		<div class="absolute inset-x-0 bottom-8 z-10 flex items-center justify-center gap-3">
			<?php foreach ( $slides as $i => $slide ) : ?>
				<button
					class="size-3 rounded-full border border-white transition aria-[current=true]:bg-white"
					type="button"
					aria-label="<?php echo esc_attr( sprintf( __( 'Aller à la diapositive %d', 'starter' ), $i + 1 ) ); ?>"
					aria-current="<?php echo 0 === $i ? 'true' : 'false'; ?>"
					data-slider-dot
				></button>
			<?php endforeach; ?>

			<?php if ( $autoplay ) : ?>
				<?php // Obligatoire (WCAG 2.2.2) : tout contenu qui défile seul doit pouvoir être mis en pause. ?>
				<button class="ml-3 p-1 text-white" type="button" data-slider-pause aria-pressed="false">
					<span data-slider-pause-icon><?php echo starter_icon( 'pause', 'size-4', __( 'Mettre en pause le défilement', 'starter' ) ); ?></span>
					<span data-slider-play-icon hidden><?php echo starter_icon( 'play', 'size-4', __( 'Reprendre le défilement', 'starter' ) ); ?></span>
				</button>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</section>
