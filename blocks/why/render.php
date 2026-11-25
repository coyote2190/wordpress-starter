<?php
/**
 * Rendu du bloc « Pourquoi nous » : intro à gauche, cartes défilantes à droite.
 *
 * En pleine largeur, les cartes défilent jusqu'au bord droit de l'écran pendant
 * que l'intro reste alignée sur la grille du site.
 * Défilement : natif (scroll-snap) + boutons via assets/src/js/modules/scroller.js.
 *
 * @var array $block      Réglages du bloc.
 * @var bool  $is_preview true dans l'éditeur.
 *
 * Champs : scf-json/group_starter_block_why.json
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$title  = (string) starter_field( 'title' );
$text   = starter_field( 'text' );
$action = (string) starter_field( 'action', false, 'none' );
$button = starter_field( 'button', false, array() );
$button = is_array( $button ) ? $button : array();
$hint   = (string) starter_field( 'placeholder', false, __( 'votre@email.fr', 'starter' ) );
$cards  = array_values( array_filter( (array) starter_field( 'cards', false, array() ), static fn( $c ) => ! empty( $c['title'] ) ) );

if ( '' === $title && ! $cards ) {
	if ( $is_preview ) {
		echo '<p class="rounded border border-dashed border-gray-300 p-8 text-center text-gray-500">'
			. esc_html__( 'Pourquoi nous : remplissez le titre et ajoutez des cartes dans le panneau de droite.', 'starter' )
			. '</p>';
	}
	return;
}

$palette = starter_palette();
$is_full = 'full' === ( $block['align'] ?? 'full' );
$uid     = $block['id'] ?? wp_unique_id( 'why-' );

// Titre : une ligne par retour à la ligne, avec **surligné** / __souligné__.
$title_lines = array_filter( array_map( 'trim', explode( "\n", $title ) ) );

$attrs = array( 'class' => 'not-prose my-20 overflow-hidden' );
// Le JS des boutons n'est chargé que s'il y a plusieurs cartes, et jamais dans l'éditeur.
if ( count( $cards ) > 1 && ! $is_preview ) {
	$attrs['data-module'] = 'scroller';
}
$wrapper = get_block_wrapper_attributes( $attrs );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<?php
	// Pleine largeur : marge gauche = celle du conteneur du site, pas de marge à droite
	// (les cartes vont jusqu'au bord de l'écran).
	$grid_style = $is_full ? 'padding-left: max(1.25rem, calc((100vw - var(--container-wide)) / 2 + 1.25rem));' : '';
	?>
	<div class="grid gap-10 lg:grid-cols-[minmax(16rem,24rem)_minmax(0,1fr)] lg:items-center lg:gap-16" style="<?php echo esc_attr( $grid_style ); ?>">

		<?php // ------- Intro ------- ?>
		<div class="flex flex-col gap-6 <?php echo $is_full ? 'pr-5 lg:pr-0' : ''; ?>">
			<?php if ( $title_lines ) : ?>
				<h2 class="text-5xl leading-[1.08] font-bold tracking-tight md:text-6xl">
					<?php foreach ( $title_lines as $line ) : ?>
						<span class="block">
							<?php
							echo starter_format_title( // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_format_title().
								$line,
								'rounded-xl bg-secondary px-2 text-dark box-decoration-clone',
								'underline decoration-secondary decoration-wavy decoration-4 underline-offset-8'
							);
							?>
						</span>
					<?php endforeach; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="max-w-sm text-lg text-gray-600"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>

			<?php if ( 'email' === $action && ! empty( $button['url'] ) ) : ?>
				<?php // Comme le bloc Appel à l'action : GET vers la page cible avec ?email=… ?>
				<form class="mt-2 flex max-w-sm flex-col gap-3" method="get" action="<?php echo esc_url( $button['url'] ); ?>" <?php echo $is_preview ? 'inert' : ''; ?>>
					<label class="sr-only" for="<?php echo esc_attr( $uid ); ?>-email"><?php esc_html_e( 'Votre adresse e-mail', 'starter' ); ?></label>
					<input id="<?php echo esc_attr( $uid ); ?>-email" class="border-b-2 border-dark bg-transparent py-2 placeholder:text-gray-500 focus:border-primary focus:outline-none" type="email" name="email" autocomplete="email" required placeholder="<?php echo esc_attr( $hint ); ?>">
					<button class="inline-flex items-center justify-center gap-2 rounded-xl bg-dark px-6 py-3.5 font-semibold text-white transition hover:bg-dark/90" type="submit">
						<?php echo esc_html( ( $button['title'] ?? '' ) ?: __( 'Envoyer', 'starter' ) ); ?>
						<?php echo starter_icon( 'mail', 'size-5' ); ?>
					</button>
				</form>
			<?php elseif ( 'button' === $action && ! empty( $button['url'] ) ) : ?>
				<a class="mt-2 inline-flex w-fit items-center gap-2 rounded-xl bg-dark px-6 py-3.5 font-semibold text-white no-underline transition hover:bg-dark/90" href="<?php echo esc_url( $button['url'] ); ?>" <?php echo '_blank' === ( $button['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>>
					<?php echo esc_html( ( $button['title'] ?? '' ) ?: __( 'En savoir plus', 'starter' ) ); ?>
					<?php echo starter_icon( 'arrow-right', 'size-4' ); ?>
				</a>
			<?php endif; ?>

			<?php if ( count( $cards ) > 1 ) : ?>
				<?php // Boutons de défilement (souris). Au doigt / trackpad, on fait défiler directement. ?>
				<div class="mt-2 hidden gap-2 lg:flex">
					<button class="inline-flex size-12 items-center justify-center rounded-full border border-gray-300 transition hover:bg-dark hover:text-white disabled:pointer-events-none disabled:opacity-30" type="button" data-scroller-prev>
						<?php echo starter_icon( 'arrow-left', 'size-5', __( 'Cartes précédentes', 'starter' ) ); ?>
					</button>
					<button class="inline-flex size-12 items-center justify-center rounded-full border border-gray-300 transition hover:bg-dark hover:text-white disabled:pointer-events-none disabled:opacity-30" type="button" data-scroller-next>
						<?php echo starter_icon( 'arrow-right', 'size-5', __( 'Cartes suivantes', 'starter' ) ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>

		<?php // ------- Cartes défilantes ------- ?>
		<?php if ( $cards ) : ?>
			<div
				class="flex snap-x snap-mandatory gap-4 overflow-x-auto pr-5 pb-2 [scrollbar-width:none] focus-visible:outline-2 focus-visible:outline-primary [&::-webkit-scrollbar]:hidden"
				data-scroller-track
				tabindex="0"
				role="region"
				aria-label="<?php echo esc_attr( wp_strip_all_tags( str_replace( array( '**', '__' ), '', $title_lines[0] ?? __( 'Cartes', 'starter' ) ) ) ); ?>"
			>
				<?php foreach ( $cards as $card ) : ?>
					<?php
					$color = isset( $palette[ $card['color'] ?? '' ] ) ? $card['color'] : 'primary';
					$link  = is_array( $card['link'] ?? null ) ? $card['link'] : array();
					?>
					<article class="group relative flex w-72 shrink-0 snap-start flex-col overflow-hidden rounded-3xl bg-light md:w-80" style="<?php echo esc_attr( "--card-color: var(--wp--preset--color--{$color});" ); ?>">
						<div class="flex flex-1 flex-col gap-4 p-6 md:p-8">
							<div class="flex items-center justify-between gap-4">
								<?php if ( ! empty( $card['badge'] ) ) : ?>
									<span class="rounded-full bg-gray-200 px-3 py-1 text-sm font-medium"><?php echo esc_html( $card['badge'] ); ?></span>
								<?php else : ?>
									<span></span>
								<?php endif; ?>

								<?php if ( ! empty( $link['url'] ) ) : ?>
									<a class="inline-flex size-9 items-center justify-center rounded-full bg-white text-dark no-underline transition after:absolute after:inset-0 group-hover:rotate-45" href="<?php echo esc_url( $link['url'] ); ?>" <?php echo '_blank' === ( $link['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>>
										<?php echo starter_icon( 'arrow-up-right', 'size-4' ); ?>
										<span class="sr-only"><?php echo esc_html( ( $link['title'] ?? '' ) ?: $card['title'] ); ?></span>
									</a>
								<?php endif; ?>
							</div>

							<h3 class="text-3xl leading-tight font-bold tracking-tight"><?php echo esc_html( $card['title'] ); ?></h3>

							<?php if ( ! empty( $card['text'] ) ) : ?>
								<p class="text-gray-600"><?php echo esc_html( $card['text'] ); ?></p>
							<?php endif; ?>
						</div>

						<?php // Panneau du bas : photo, sinon grande icône sur fond pastel de la couleur choisie. ?>
						<div class="relative flex aspect-square items-center justify-center overflow-hidden rounded-3xl bg-(--card-color)/20 text-(--card-color)">
							<?php if ( ! empty( $card['image'] ) ) : ?>
								<?php echo wp_get_attachment_image( (int) $card['image'], 'medium_large', false, array( 'class' => 'absolute inset-0 size-full object-cover transition duration-500 group-hover:scale-105' ) ); ?>
							<?php elseif ( ! empty( $card['icon'] ) && starter_icon_exists( $card['icon'] ) ) : ?>
								<?php echo starter_icon( $card['icon'], 'size-28 [stroke-width:1.25] transition duration-500 group-hover:scale-110 group-hover:-rotate-6' ); ?>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
