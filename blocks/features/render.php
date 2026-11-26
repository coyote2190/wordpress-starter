<?php
/**
 * Rendu du bloc « Cartes » : grille de cartes avec icône colorée.
 *
 * @var array $block      Réglages du bloc.
 * @var bool  $is_preview true dans l'éditeur.
 *
 * Champs : scf-json/group_starter_block_features.json
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$title   = starter_field( 'title' );
$intro   = starter_field( 'intro' );
$columns = (string) starter_field( 'columns', false, '3' );
$style   = (string) starter_field( 'style', false, 'border' );
$items   = starter_field( 'items', false, array() );

if ( ! $items ) {
	if ( $is_preview ) {
		echo '<p class="border border-dashed border-gray-300 p-8 rounded text-center text-gray-500">'
			. esc_html__( 'Cartes : ajoutez des cartes dans le panneau de droite.', 'starter' )
			. '</p>';
	}
	return;
}

// Classes écrites EN ENTIER (Tailwind ne détecte pas les classes construites).
$grid_classes = array(
	'2' => 'sm:grid-cols-2',
	'3' => 'sm:grid-cols-2 lg:grid-cols-3',
	'4' => 'sm:grid-cols-2 lg:grid-cols-4',
);
$card_classes = array(
	'border' => 'border border-light bg-white',
	'light'  => 'bg-white',
	'shadow' => 'bg-white shadow-[0_8px_30px_-12px_rgb(28_28_28/.15)]',
	'accent' => 'border border-light border-t-4 border-t-(--card-color) bg-white',
);

$palette = starter_palette();

$wrapper = get_block_wrapper_attributes( array( 'class' => 'md:my-32 my-24 not-prose' ) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
<div class="<?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
	<?php if ( $title || $intro ) : ?>
		<?php // Titre à gauche, introduction à droite (comme le bloc Bento). ?>
		<header class="flex flex-wrap gap-x-16 gap-y-6 items-end justify-between mb-12">
			<?php if ( $title ) : ?>
				<h2 class="leading-none text-[clamp(2.25rem,4.4vw,3.75rem)] tracking-[-.04em]"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="leading-normal max-w-[420px] text-lg text-muted"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<div class="gap-4 grid <?php echo esc_attr( $grid_classes[ $columns ] ?? $grid_classes['3'] ); ?>">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$link  = is_array( $item['link'] ?? null ) ? $item['link'] : array();
			$color = isset( $palette[ $item['color'] ?? '' ] ) ? $item['color'] : 'primary';
			// La couleur de la carte passe par une variable CSS : toute couleur de
			// theme.json fonctionne, sans liste de classes à maintenir.
			$color_var = sprintf( '--card-color: var(--wp--preset--color--%s);', $color );
			?>
			<article
				class="flex flex-col group p-7 relative rounded-3xl transition <?php echo esc_attr( $card_classes[ $style ] ?? $card_classes['border'] ); ?> <?php echo ! empty( $link['url'] ) ? 'hover:-translate-y-1 hover:border-dark' : ''; ?>"
				style="<?php echo esc_attr( $color_var ); ?>"
			>
				<?php if ( ! empty( $item['icon'] ) && starter_icon_exists( $item['icon'] ) ) : ?>
					<span class="bg-(--card-color)/45 inline-flex items-center justify-center rounded-full size-12 text-dark" style="--icon-accent: var(--card-color);">
						<?php echo starter_icon( $item['icon'], '[stroke-width:1.5] size-6' ); ?>
					</span>
				<?php endif; ?>

				<h3 class="leading-tight mt-8 text-[1.625rem] tracking-[-.02em]"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>

				<?php if ( ! empty( $item['text'] ) ) : ?>
					<p class="flex-1 leading-normal mt-2 text-muted"><?php echo esc_html( $item['text'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $link['url'] ) ) : ?>
					<?php // after:absolute after:inset-0 : toute la carte est cliquable. ?>
					<a
						class="after:absolute after:inset-0 font-medium gap-1.5 inline-flex items-center mt-6 text-dark underline underline-offset-4"
						href="<?php echo esc_url( $link['url'] ); ?>"
						<?php echo '_blank' === ( $link['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>
					>
						<?php echo esc_html( ( $link['title'] ?? '' ) ?: __( 'En savoir plus', 'starter' ) ); ?>
						<?php echo starter_icon( 'arrow-right', 'group-hover:translate-x-1 size-4 transition' ); ?>
					</a>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</div>
</section>
