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
	'border' => 'border border-gray-200 bg-white',
	'light'  => 'bg-light',
	'shadow' => 'bg-white shadow-md',
	'accent' => 'border border-gray-200 border-t-4 border-t-(--card-color) bg-white',
);

$palette = starter_palette();

$wrapper = get_block_wrapper_attributes( array( 'class' => 'not-prose my-16' ) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
	<?php if ( $title || $intro ) : ?>
		<header class="mx-auto mb-10 max-w-2xl text-center">
			<?php if ( $title ) : ?>
				<h2 class="text-3xl font-bold"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="mt-3 text-lg text-gray-600"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<div class="grid gap-6 <?php echo esc_attr( $grid_classes[ $columns ] ?? $grid_classes['3'] ); ?>">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$link  = is_array( $item['link'] ?? null ) ? $item['link'] : array();
			$color = isset( $palette[ $item['color'] ?? '' ] ) ? $item['color'] : 'primary';
			// La couleur de la carte passe par une variable CSS : toute couleur de
			// theme.json fonctionne, sans liste de classes à maintenir.
			$color_var = sprintf( '--card-color: var(--wp--preset--color--%s);', $color );
			?>
			<article
				class="group relative flex flex-col rounded-xl p-6 transition <?php echo esc_attr( $card_classes[ $style ] ?? $card_classes['border'] ); ?> <?php echo ! empty( $link['url'] ) ? 'hover:-translate-y-1 hover:shadow-lg' : ''; ?>"
				style="<?php echo esc_attr( $color_var ); ?>"
			>
				<?php if ( ! empty( $item['icon'] ) && starter_icon_exists( $item['icon'] ) ) : ?>
					<span class="inline-flex size-12 items-center justify-center rounded-lg bg-(--card-color)/10 text-(--card-color)">
						<?php echo starter_icon( $item['icon'], 'size-6' ); ?>
					</span>
				<?php endif; ?>

				<h3 class="mt-5 text-lg font-bold"><?php echo esc_html( $item['title'] ?? '' ); ?></h3>

				<?php if ( ! empty( $item['text'] ) ) : ?>
					<p class="mt-2 flex-1 text-gray-600"><?php echo esc_html( $item['text'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $link['url'] ) ) : ?>
					<?php // after:absolute after:inset-0 : toute la carte est cliquable. ?>
					<a
						class="mt-4 inline-flex items-center gap-1 font-semibold text-dark after:absolute after:inset-0"
						href="<?php echo esc_url( $link['url'] ); ?>"
						<?php echo '_blank' === ( $link['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>
					>
						<?php echo esc_html( ( $link['title'] ?? '' ) ?: __( 'En savoir plus', 'starter' ) ); ?>
						<?php echo starter_icon( 'arrow-right', 'size-4 text-(--card-color) transition group-hover:translate-x-1' ); ?>
					</a>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</section>
