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
		echo '<p class="rounded border border-dashed border-gray-300 p-8 text-center text-gray-500">'
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

$wrapper = get_block_wrapper_attributes( array( 'class' => 'not-prose my-24 md:my-32' ) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
<div class="<?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
	<?php if ( $title || $intro ) : ?>
		<?php // Titre à gauche, introduction à droite (comme le bloc Bento). ?>
		<header class="mb-12 flex flex-wrap items-end justify-between gap-x-16 gap-y-6">
			<?php if ( $title ) : ?>
				<h2 class="text-[clamp(2.25rem,4.4vw,3.75rem)] leading-none tracking-[-.04em]"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="max-w-[420px] text-lg leading-normal text-muted"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<div class="grid gap-4 <?php echo esc_attr( $grid_classes[ $columns ] ?? $grid_classes['3'] ); ?>">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$link  = is_array( $item['link'] ?? null ) ? $item['link'] : array();
			$color = isset( $palette[ $item['color'] ?? '' ] ) ? $item['color'] : 'primary';
			// La couleur de la carte passe par une variable CSS : toute couleur de
			// theme.json fonctionne, sans liste de classes à maintenir.
			$color_var = sprintf( '--card-color: var(--wp--preset--color--%s);', $color );
			?>
			<article
				class="group relative flex flex-col rounded-3xl p-7 transition <?php echo esc_attr( $card_classes[ $style ] ?? $card_classes['border'] ); ?> <?php echo ! empty( $link['url'] ) ? 'hover:-translate-y-1 hover:border-dark' : ''; ?>"
				style="<?php echo esc_attr( $color_var ); ?>"
			>
				<?php if ( ! empty( $item['icon'] ) && starter_icon_exists( $item['icon'] ) ) : ?>
					<span class="inline-flex size-12 items-center justify-center rounded-full bg-(--card-color)/45 text-dark" style="--icon-accent: var(--card-color);">
						<?php echo starter_icon( $item['icon'], 'size-6 [stroke-width:1.5]' ); ?>
					</span>
				<?php endif; ?>

				<h3 class="mt-8 text-[1.625rem] leading-tight tracking-[-.02em]"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>

				<?php if ( ! empty( $item['text'] ) ) : ?>
					<p class="mt-2 flex-1 leading-normal text-muted"><?php echo esc_html( $item['text'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $link['url'] ) ) : ?>
					<?php // after:absolute after:inset-0 : toute la carte est cliquable. ?>
					<a
						class="mt-6 inline-flex items-center gap-1.5 font-medium text-dark underline underline-offset-4 after:absolute after:inset-0"
						href="<?php echo esc_url( $link['url'] ); ?>"
						<?php echo '_blank' === ( $link['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>
					>
						<?php echo esc_html( ( $link['title'] ?? '' ) ?: __( 'En savoir plus', 'starter' ) ); ?>
						<?php echo starter_icon( 'arrow-right', 'size-4 transition group-hover:translate-x-1' ); ?>
					</a>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</div>
</section>
