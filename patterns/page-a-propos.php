<?php
/**
 * Title: Page « À propos »
 * Slug: starter/page-a-propos
 * Categories: starter-pages
 * Keywords: équipe, histoire, valeurs, about
 * Post Types: page
 * Block Types: core/post-content
 * Viewport Width: 1400
 * Description: Hero, histoire, valeurs en cartes et appel à l'action avec e-mail.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs (serialize_block).
	'hero',
	array(
		'title'  => __( 'Une équipe à **taille humaine**', 'starter' ),
		'text'   => __( 'Présentez en deux phrases qui vous êtes, ce que vous faites et pour qui. Ajoutez une photo de l\'équipe ou de vos locaux.', 'starter' ),
		'button' => array(
			'title' => __( 'Nous rencontrer', 'starter' ),
			'url'   => '/contact/',
		),
	),
	array( 'align' => 'wide' )
);
?>
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Notre histoire', 'starter' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Racontez comment tout a commencé, ce qui vous rend différent et ce qui vous motive chaque jour. Trois ou quatre phrases suffisent : les visiteurs veulent savoir à qui ils parlent.', 'starter' ); ?></p>
<!-- /wp:paragraph -->

<?php
echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
	'features',
	array(
		'title'   => __( 'Nos valeurs', 'starter' ),
		'intro'   => __( 'Trois engagements qui guident chacun de nos projets.', 'starter' ),
		'columns' => '3',
		'style'   => 'light',
		'items'   => array(
			array(
				'icon'  => 'lightbulb',
				'color' => 'secondary',
				'title' => __( 'Clarté', 'starter' ),
				'text'  => __( 'Des devis lisibles, des délais tenus, pas de jargon.', 'starter' ),
			),
			array(
				'icon'  => 'star',
				'color' => 'secondary',
				'title' => __( 'Exigence', 'starter' ),
				'text'  => __( 'Un travail soigné, vérifié, livré quand prévu.', 'starter' ),
			),
			array(
				'icon'  => 'heart',
				'color' => 'secondary',
				'title' => __( 'Proximité', 'starter' ),
				'text'  => __( 'Un seul interlocuteur, joignable par téléphone.', 'starter' ),
			),
		),
	),
	array( 'align' => 'wide' )
);

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
	'cta',
	array(
		'title'      => __( 'Parlons de votre projet', 'starter' ),
		'text'       => __( 'Laissez-nous votre e-mail : on vous rappelle sous 48 h, sans engagement.', 'starter' ),
		'action'     => 'email',
		'button'     => array(
			'title' => __( 'C\'est parti', 'starter' ),
			'url'   => '/contact/',
		),
		'background' => 'secondary',
		'accent'     => 'white',
	),
	array( 'align' => 'full' )
);
