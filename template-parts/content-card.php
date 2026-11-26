<?php
/**
 * Carte d'un contenu dans une liste (articles, réalisations, résultats de recherche).
 * Utilisée par template-parts/loop.php et single.php (« À lire aussi »).
 *
 * Arguments :
 * - featured (bool) Carte mise en avant : toute la largeur, image à gauche (grand écran).
 *
 * Sans image mise en avant (ex. une page dans les résultats de recherche) : carte
 * blanche bordée, avec le type de contenu en pastille.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_featured = ! empty( $args['featured'] );
$starter_term     = starter_primary_term();
$starter_is_post  = 'post' === get_post_type();
$starter_has_img  = has_post_thumbnail();
$starter_featured = $starter_featured && $starter_has_img;
?>
<?php
$starter_classes = 'group relative flex flex-col gap-5';
if ( $starter_featured ) {
	$starter_classes .= ' sm:col-span-full lg:grid lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)] lg:items-center lg:gap-12';
} elseif ( ! $starter_has_img ) {
	$starter_classes .= ' self-start rounded-3xl border border-light bg-white p-6 transition hover:border-dark';
}
$starter_arrow = '<span class="absolute top-4 right-4 inline-flex size-10 items-center justify-center rounded-full bg-white transition group-hover:rotate-45" aria-hidden="true">' . starter_icon( 'arrow-up-right', 'size-4' ) . '</span>';
?>
<article <?php post_class( $starter_classes ); ?>>
	<?php if ( $starter_has_img ) : ?>
		<div class="relative overflow-hidden rounded-3xl bg-secondary <?php echo $starter_featured ? 'aspect-[4/3] lg:aspect-[16/10]' : 'aspect-[4/3]'; ?>">
			<?php the_post_thumbnail( $starter_featured ? 'large' : 'medium_large', array( 'class' => 'size-full object-cover transition duration-500 group-hover:scale-105' ) ); ?>
			<?php echo $starter_arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML fixe + SVG. ?>
		</div>
	<?php else : ?>
		<?php echo str_replace( 'bg-white', 'bg-secondary', $starter_arrow ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	<?php endif; ?>

	<div class="flex flex-col items-start gap-3">
		<div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-muted">
			<?php if ( ! $starter_term && is_search() ) : ?>
				<?php // Recherche : type de contenu (Page, Réalisation…) quand il n'y a pas de catégorie. ?>
				<span class="rounded-full bg-light px-3 py-1 text-[13px] font-medium text-dark"><?php echo esc_html( get_post_type_object( (string) get_post_type() )->labels->singular_name ); ?></span>
			<?php endif; ?>
			<?php if ( $starter_term ) : ?>
				<?php // Pastille relative (z-10) : cliquable par-dessus le lien qui couvre la carte. ?>
				<a class="relative z-10 rounded-full border border-light bg-white px-3 py-1 text-[13px] font-medium text-dark transition hover:border-dark" href="<?php echo esc_url( get_term_link( $starter_term ) ); ?>"><?php echo esc_html( $starter_term->name ); ?></a>
			<?php endif; ?>
			<?php if ( $starter_is_post ) : ?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<?php endif; ?>
		</div>

		<h2 class="<?php echo $starter_featured ? 'text-[clamp(1.75rem,3vw,2.75rem)] leading-[1.05] tracking-[-.035em]' : 'text-2xl leading-tight tracking-[-.02em]'; ?>">
			<?php // Le lien couvre toute la carte grâce à after:absolute after:inset-0. ?>
			<a class="after:absolute after:inset-0" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<?php if ( has_excerpt() || $starter_featured ) : ?>
			<p class="leading-normal text-muted <?php echo $starter_featured ? 'text-lg' : 'line-clamp-2'; ?>"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</article>
