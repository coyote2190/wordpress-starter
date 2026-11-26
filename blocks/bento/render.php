<?php
/**
 * Rendu du bloc « Bento » : grille de tuiles de tailles variées.
 *
 * Grille de 4 colonnes sur grand écran (2 sur tablette, 1 sur mobile). Chaque
 * tuile choisit sa largeur (1 à 4 colonnes) et sa hauteur (1 ou 2 rangées) ;
 * `grid-flow-dense` bouche automatiquement les trous.
 *
 * @var array $block      Réglages du bloc.
 * @var bool  $is_preview true dans l'éditeur.
 *
 * Champs : scf-json/group_starter_block_bento.json
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$title = starter_field( 'title' );
$intro = starter_field( 'intro' );
$tiles = array_values( array_filter( (array) starter_field( 'tiles', false, array() ), static fn( $t ) => ! empty( $t['title'] ) || ! empty( $t['number'] ) ) );

if ( ! $tiles ) {
	if ( $is_preview ) {
		echo '<p class="rounded border border-dashed border-gray-300 p-8 text-center text-gray-500">'
			. esc_html__( 'Bento : ajoutez des tuiles dans le panneau de droite.', 'starter' )
			. '</p>';
	}
	return;
}

// Classes écrites EN ENTIER pour Tailwind.
$widths = array(
	'1' => 'lg:col-span-1',
	'2' => 'md:col-span-2 lg:col-span-2',
	'3' => 'md:col-span-2 lg:col-span-3',
	'4' => 'md:col-span-2 lg:col-span-4',
);
$heights = array(
	'normal' => '',
	'tall'   => 'md:row-span-2', // Sur mobile, les tuiles s'empilent : pas de double hauteur.
);

$palette = starter_palette();

$wrapper = get_block_wrapper_attributes( array( 'class' => 'not-prose my-24 md:my-32' ) );
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput -- échappé par WordPress. ?>>
<div class="<?php echo esc_attr( starter_block_inner_class( $block['align'] ?? null ) ); ?>">
	<?php if ( $title || $intro ) : ?>
		<header class="mb-12 flex flex-wrap items-end justify-between gap-x-16 gap-y-6">
			<?php if ( $title ) : ?>
				<h2 class="text-[clamp(2.25rem,4.4vw,3.75rem)] leading-none tracking-[-.04em]"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="max-w-[420px] text-lg leading-normal text-muted"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</header>
	<?php endif; ?>

	<div class="grid grid-flow-dense auto-rows-[minmax(17rem,auto)] gap-4 md:grid-cols-2 lg:grid-cols-4">
		<?php foreach ( $tiles as $tile ) : ?>
			<?php
			$type     = 'image' === ( $tile['type'] ?? '' ) && ! empty( $tile['image'] ) ? 'image' : 'color';
			$color    = isset( $palette[ $tile['color'] ?? '' ] ) ? $tile['color'] : 'primary';
			$link     = is_array( $tile['link'] ?? null ) ? $tile['link'] : array();
			$has_link = ! empty( $link['url'] );
			// Texte foncé sur une couleur claire, blanc sinon (photo : toujours blanc, sur le dégradé).
			$dark_text = 'color' === $type && starter_color_is_light( $color );

			$classes = implode(
				' ',
				array_filter(
					array(
						'group relative isolate flex flex-col justify-between gap-4 overflow-hidden rounded-3xl p-6',
						$widths[ (string) ( $tile['width'] ?? '1' ) ] ?? $widths['1'],
						$heights[ $tile['height'] ?? 'normal' ] ?? '',
						'color' === $type ? 'bg-(--tile-color)' : 'bg-dark',
						$dark_text ? 'text-dark' : 'text-white',
					)
				)
			);
			?>
			<article class="<?php echo esc_attr( $classes ); ?>" style="<?php echo esc_attr( "--tile-color: var(--wp--preset--color--{$color});" ); ?>">
				<?php if ( 'image' === $type ) : ?>
					<?php
					echo wp_get_attachment_image(
						(int) $tile['image'],
						'large',
						false,
						array( 'class' => 'absolute inset-0 -z-20 size-full object-cover transition duration-500 group-hover:scale-105' )
					);
					?>
					<?php // Dégradé de la couleur choisie, du bas vers le haut : le texte reste lisible sur la photo. ?>
					<div class="absolute inset-0 -z-10 bg-linear-to-t from-(--tile-color)/75 to-transparent to-60%"></div>
				<?php endif; ?>

				<?php // Haut : chiffre clé + bouton flèche. ?>
				<div class="flex items-start justify-between gap-4">
					<?php if ( ! empty( $tile['number'] ) ) : ?>
						<p class="font-heading text-[clamp(3.5rem,5.5vw,5rem)] leading-none font-medium tracking-[-.05em]"><?php echo esc_html( $tile['number'] ); ?></p>
					<?php else : ?>
						<span></span>
					<?php endif; ?>

					<?php if ( $has_link ) : ?>
						<?php // after:absolute after:inset-0 : toute la tuile est cliquable. ?>
						<a
							class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-white text-dark no-underline transition group-hover:rotate-45 after:absolute after:inset-0"
							href="<?php echo esc_url( $link['url'] ); ?>"
							<?php echo '_blank' === ( $link['target'] ?? '' ) ? 'target="_blank" rel="noopener"' : ''; ?>
						>
							<?php echo starter_icon( 'arrow-up-right', 'size-4' ); ?>
							<span class="sr-only"><?php echo esc_html( ( $link['title'] ?? '' ) ?: ( $tile['title'] ?? '' ) ); ?></span>
						</a>
					<?php endif; ?>
				</div>

				<?php // Bas : pastille, titre, texte. ?>
				<div class="flex flex-col items-start gap-2">
					<?php if ( ! empty( $tile['badge'] ) ) : ?>
						<span class="rounded-full bg-white px-3 py-1 text-[13px] font-medium text-dark"><?php echo esc_html( $tile['badge'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $tile['title'] ) ) : ?>
						<h3 class="text-[1.75rem] leading-tight tracking-[-.02em]"><?php echo esc_html( $tile['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $tile['text'] ) ) : ?>
						<p class="max-w-[420px] leading-normal <?php echo $dark_text ? 'text-primary' : 'opacity-90'; ?>"><?php echo esc_html( $tile['text'] ); ?></p>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</div>
</section>
