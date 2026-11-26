<?php
/**
 * Filtres en pastilles (« Tout », puis une pastille par catégorie) au-dessus d'une liste.
 *
 * Arguments :
 * - taxonomy (string) Taxonomie à lister (ex. 'category', 'reference_category').
 * - all_url  (string) Lien de la pastille « Tout » (page des articles, archive du CPT…).
 *
 * N'affiche rien s'il y a moins de 2 catégories non vides.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_taxonomy = (string) ( $args['taxonomy'] ?? 'category' );
$starter_all_url  = (string) ( $args['all_url'] ?? '' );
$starter_terms    = get_terms(
	array(
		'taxonomy'   => $starter_taxonomy,
		'hide_empty' => true,
		'exclude'    => 'category' === $starter_taxonomy ? array( (int) get_option( 'default_category' ) ) : array(),
	)
);

if ( is_wp_error( $starter_terms ) || count( $starter_terms ) < 2 ) {
	return;
}

$starter_current = is_tax( $starter_taxonomy ) || is_category() ? get_queried_object_id() : 0;
$starter_pill    = 'inline-flex rounded-full border px-4 py-2 text-[15px] font-medium transition';
$starter_on      = 'border-dark bg-dark text-white';
$starter_off     = 'border-light bg-white hover:border-dark';
?>
<nav class="mb-10" aria-label="<?php esc_attr_e( 'Filtrer par catégorie', 'starter' ); ?>">
	<ul class="flex flex-wrap gap-2">
		<?php if ( $starter_all_url ) : ?>
			<li>
				<a class="<?php echo esc_attr( $starter_pill . ' ' . ( $starter_current ? $starter_off : $starter_on ) ); ?>" href="<?php echo esc_url( $starter_all_url ); ?>" <?php echo $starter_current ? '' : 'aria-current="page"'; ?>>
					<?php esc_html_e( 'Tout', 'starter' ); ?>
				</a>
			</li>
		<?php endif; ?>
		<?php foreach ( $starter_terms as $starter_term ) : ?>
			<?php $starter_active = $starter_current === $starter_term->term_id; ?>
			<li>
				<a class="<?php echo esc_attr( $starter_pill . ' ' . ( $starter_active ? $starter_on : $starter_off ) ); ?>" href="<?php echo esc_url( get_term_link( $starter_term ) ); ?>" <?php echo $starter_active ? 'aria-current="page"' : ''; ?>>
					<?php echo esc_html( $starter_term->name ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
