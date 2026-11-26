<?php
/**
 * Rendu du bloc « Pourquoi nous » : intro à gauche, arguments à droite.
 *
 * Deux présentations (champ `layout`) :
 * - « Liste » : arguments les uns sous les autres, séparés par un filet
 *   (icône, surtitre, titre, texte) ;
 * - « Cartes défilantes » : en pleine largeur, les cartes défilent jusqu'au bord
 *   droit de l'écran pendant que l'intro reste alignée sur la grille du site.
 *   Défilement natif (scroll-snap) + boutons via assets/src/js/modules/scroller.js.
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
$layout = 'scroller' === starter_field( 'layout', false, 'list' ) ? 'scroller' : 'list';
$cards  = array_values( array_filter( (array) starter_field( 'cards', false, array() ), static fn( $c ) => ! empty( $c['title'] ) ) );

if ( '' === $title && ! $cards ) {
	if ( $is_preview ) {
		echo '<p class="border border-dashed border-gray-300 p-8 rounded text-center text-gray-500">'
			. esc_html__( 'Pourquoi nous : remplissez le titre et ajoutez des arguments dans le panneau de droite.', 'starter' )
			. '</p>';
	}
	return;
}

$palette  = starter_palette();
$is_full  = 'full' === ( $block['align'] ?? 'full' );
$uid      = $block['id'] ?? wp_unique_id( 'why-' );
$scroller = 'scroller' === $layout && count( $cards ) > 1;

// Titre : une ligne par retour à la ligne, avec **surligné** / __souligné__.
$title_lines = array_values( array_filter( array_map( 'trim', explode( "\n", $title ) ) ) );

$attrs = array( 'class' => 'md:my-32 my-24 not-prose' . ( 'scroller' === $layout ? ' overflow-hidden' : '' ) );
// Le JS des boutons n'est chargé qu'en cartes défilantes (2 cartes ou plus), et jamais dans l'éditeur.
if ( $scroller && ! $is_preview ) {
	$attrs['data-module'] = 'scroller';
}
$wrapper = get_block_wrapper_attributes( $attrs );

/**
 * Couleur d'une carte (palette theme.json), en variable CSS.
 */
$color_var = static function ( array $card ) use ( $palette ): string {
	$color = isset( $palette[ $card['color'] ?? '' ] ) ? $card['color'] : 'secondary';
	return "--card-color: var(--wp--preset--color--{$color});";
};

if ( 'scroller' === $layout ) {
	// Pleine largeur : marge gauche = celle du conteneur du site, pas de marge à droite
	// (les cartes vont jusqu'au bord de l'écran).
	$grid_class = 'grid gap-10 lg:grid-cols-[minmax(16rem,24rem)_minmax(0,1fr)] lg:items-center lg:gap-16';
	$grid_style = $is_full ? 'padding-left: max(var(--gutter), calc((100vw - var(--container-wide)) / 2 + var(--gutter)));' : '';
} else {
	$grid_class = 'grid gap-x-20 gap-y-14 lg:grid-cols-[minmax(20rem,1fr)_minmax(0,2fr)] ' . starter_block_inner_class( $block['align'] ?? null );
	$grid_style = '';
}
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<div class="<?php echo esc_attr( $grid_class ); ?>" style="<?php echo esc_attr( $grid_style ); ?>">

		<?php // ------- Intro ------- ?>
		<div class="flex flex-col gap-6 items-start <?php echo $is_full && 'scroller' === $layout ? 'pr-5 lg:pr-0' : ''; ?>">
			<?php if ( $title_lines ) : ?>
				<h2 class="leading-[.98] text-[clamp(3rem,5.4vw,4.75rem)] tracking-[-.045em]">
					<?php foreach ( $title_lines as $line ) : ?>
						<span class="block">
							<?php
							echo starter_format_title( // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_format_title().
								$line,
								'title-mark',
								'underline decoration-secondary decoration-wavy decoration-4 underline-offset-8'
							);
							?>
						</span>
					<?php endforeach; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $text ) : ?>
				<p class="leading-normal max-w-[400px] text-lg text-muted"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>

			<?php if ( 'email' === $action && ! empty( $button['url'] ) ) : ?>
				<?php // Comme le bloc Appel à l'action : POST vers la page cible (l'e-mail n'apparaît pas dans l'URL). ?>
				<form class="bg-white flex gap-2 items-center max-w-sm p-1.5 rounded-full w-full" method="post" action="<?php echo esc_url( $button['url'] ); ?>" <?php echo $is_preview ? 'inert' : ''; ?>>
					<label class="sr-only" for="<?php echo esc_attr( $uid ); ?>-email"><?php esc_html_e( 'Votre adresse e-mail', 'starter' ); ?></label>
					<input id="<?php echo esc_attr( $uid ); ?>-email" class="bg-transparent flex-1 focus:outline-none min-w-0 placeholder:text-soft px-4 text-dark" type="email" name="email" autocomplete="email" required placeholder="<?php echo esc_attr( $hint ); ?>">
					<button class="bg-dark font-medium gap-2 hover:bg-primary inline-flex items-center px-5 py-3 rounded-full shrink-0 text-white transition" type="submit">
						<?php echo esc_html( ( $button['title'] ?? '' ) ?: __( 'Envoyer', 'starter' ) ); ?>
						<?php echo starter_icon( 'arrow-right', 'size-4' ); ?>
					</button>
				</form>
			<?php elseif ( 'button' === $action ) : ?>
				<?php echo starter_button( $button, 'icon', 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput -- échappé dans starter_button(). ?>
			<?php endif; ?>

			<?php if ( $scroller ) : ?>
				<?php // Boutons de défilement (souris). Au doigt / trackpad, on fait défiler directement. ?>
				<div class="gap-2 hidden lg:flex mt-2">
					<button class="border border-line disabled:opacity-30 disabled:pointer-events-none hover:bg-dark hover:text-white inline-flex items-center justify-center rounded-full size-12 transition" type="button" data-scroller-prev>
						<?php echo starter_icon( 'arrow-left', 'size-5', __( 'Cartes précédentes', 'starter' ) ); ?>
					</button>
					<button class="border border-line disabled:opacity-30 disabled:pointer-events-none hover:bg-dark hover:text-white inline-flex items-center justify-center rounded-full size-12 transition" type="button" data-scroller-next>
						<?php echo starter_icon( 'arrow-right', 'size-5', __( 'Cartes suivantes', 'starter' ) ); ?>
					</button>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $cards && 'list' === $layout ) : ?>
			<?php // ------- Liste ------- ?>
			<ul class="border-b border-line flex flex-col self-start">
				<?php foreach ( $cards as $card ) : ?>
					<?php $link = is_array( $card['link'] ?? null ) ? $card['link'] : array(); ?>
					<li class="border-line border-t flex gap-6 items-start py-7 relative" style="<?php echo esc_attr( $color_var( $card ) . ' --icon-accent: var(--card-color);' ); ?>">
						<?php if ( ! empty( $card['icon'] ) && starter_icon_exists( $card['icon'] ) ) : ?>
							<?php echo starter_icon( $card['icon'], 'size-11 text-dark' ); ?>
						<?php endif; ?>

						<div class="flex flex-1 flex-wrap gap-x-8 gap-y-2">
							<div class="flex flex-[1_1_12.5rem] flex-col gap-1.5">
								<?php if ( ! empty( $card['badge'] ) ) : ?>
									<p class="font-medium text-[13px] text-soft tracking-[.06em] uppercase"><?php echo esc_html( $card['badge'] ); ?></p>
								<?php endif; ?>
								<h3 class="leading-tight text-[1.625rem] tracking-[-.02em]">
									<?php if ( ! empty( $link['url'] ) ) : ?>
										<?php // after:absolute after:inset-0 : toute la ligne est cliquable. ?>
										<a class="after:absolute after:inset-0 hover:underline no-underline" href="<?php echo esc_url( $link['url'] ); ?>" <?php echo starter_link_target( $link ); // phpcs:ignore WordPress.Security.EscapeOutput ?>><?php echo esc_html( $card['title'] ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $card['title'] ); ?>
									<?php endif; ?>
								</h3>
							</div>
							<?php if ( ! empty( $card['text'] ) ) : ?>
								<p class="flex-[1_1_13.75rem] leading-normal text-muted"><?php echo esc_html( $card['text'] ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( $cards ) : ?>
			<?php // ------- Cartes défilantes ------- ?>
			<div
				class="[&::-webkit-scrollbar]:hidden [scrollbar-width:none] flex focus-visible:outline-2 focus-visible:outline-primary gap-4 overflow-x-auto pb-2 pr-5 snap-mandatory snap-x"
				data-scroller-track
				tabindex="0"
				role="region"
				aria-label="<?php echo esc_attr( wp_strip_all_tags( str_replace( array( '**', '__' ), '', $title_lines[0] ?? __( 'Cartes', 'starter' ) ) ) ); ?>"
			>
				<?php foreach ( $cards as $card ) : ?>
					<?php $link = is_array( $card['link'] ?? null ) ? $card['link'] : array(); ?>
					<article class="bg-white flex flex-col group md:w-80 overflow-hidden relative rounded-3xl shrink-0 snap-start w-72" style="<?php echo esc_attr( $color_var( $card ) ); ?>">
						<div class="flex flex-1 flex-col gap-4 md:p-8 p-6">
							<div class="flex gap-4 items-center justify-between">
								<?php if ( ! empty( $card['badge'] ) ) : ?>
									<span class="bg-light font-medium px-3 py-1 rounded-full text-[13px]"><?php echo esc_html( $card['badge'] ); ?></span>
								<?php else : ?>
									<span></span>
								<?php endif; ?>

								<?php if ( ! empty( $link['url'] ) ) : ?>
									<a class="after:absolute after:inset-0 bg-light group-hover:rotate-45 inline-flex items-center justify-center no-underline rounded-full size-9 text-dark transition" href="<?php echo esc_url( $link['url'] ); ?>" <?php echo starter_link_target( $link ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
										<?php echo starter_icon( 'arrow-up-right', 'size-4' ); ?>
										<span class="sr-only"><?php echo esc_html( ( $link['title'] ?? '' ) ?: $card['title'] ); ?></span>
									</a>
								<?php endif; ?>
							</div>

							<h3 class="leading-tight text-[1.75rem] tracking-[-.02em]"><?php echo esc_html( $card['title'] ); ?></h3>

							<?php if ( ! empty( $card['text'] ) ) : ?>
								<p class="leading-normal text-muted"><?php echo esc_html( $card['text'] ); ?></p>
							<?php endif; ?>
						</div>

						<?php // Panneau du bas : photo, sinon grande icône sur fond pastel de la couleur choisie. ?>
						<div class="aspect-square bg-(--card-color)/30 flex items-center justify-center overflow-hidden relative rounded-3xl text-dark" style="--icon-accent: var(--card-color);">
							<?php if ( ! empty( $card['image'] ) ) : ?>
								<?php echo wp_get_attachment_image( (int) $card['image'], 'medium_large', false, array( 'class' => 'absolute duration-500 group-hover:scale-105 inset-0 object-cover size-full transition' ) ); ?>
							<?php elseif ( ! empty( $card['icon'] ) && starter_icon_exists( $card['icon'] ) ) : ?>
								<?php echo starter_icon( $card['icon'], '[stroke-width:1.25] duration-500 group-hover:-rotate-6 group-hover:scale-110 size-28 transition' ); ?>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
