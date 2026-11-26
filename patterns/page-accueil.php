<?php
/**
 * Title: Page d'accueil
 * Slug: starter/page-accueil
 * Categories: starter-pages
 * Keywords: accueil, home, landing
 * Post Types: page
 * Block Types: core/post-content
 * Viewport Width: 1400
 * Description: Hero, chiffres clés en bento, arguments qui défilent et appel à l'action.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs (serialize_block).
	'hero',
	array(
		'title'  => __( 'Votre promesse en **une phrase**', 'starter' ),
		'text'   => __( 'Pour qui, quoi, où : ce que le visiteur doit comprendre en trois secondes.', 'starter' ),
		'button' => array(
			'title' => __( 'Demander un devis', 'starter' ),
			'url'   => '/contact/',
		),
	),
	array( 'align' => 'wide' )
);

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
	'bento',
	array(
		'title' => __( 'En quelques chiffres', 'starter' ),
		'intro' => __( 'Des preuves concrètes : années d\'expérience, clients, avis.', 'starter' ),
		'tiles' => array(
			array(
				'color'  => 'dark',
				'width'  => '2',
				'number' => '10 ans',
				'text'   => __( 'd\'expérience au service des entreprises de la région.', 'starter' ),
			),
			array(
				'color'  => 'secondary',
				'number' => '120+',
				'text'   => __( 'clients accompagnés.', 'starter' ),
			),
			array(
				'color'  => 'light',
				'number' => '4,9/5',
				'text'   => __( 'note moyenne de nos avis.', 'starter' ),
			),
		),
	),
	array( 'align' => 'wide' )
);

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
	'why',
	array(
		'title'  => __( 'Ce qui fait la différence', 'starter' ),
		'layout' => 'scroller',
		'cards'  => array(
			array(
				'badge' => __( 'Rapidité', 'starter' ),
				'color' => 'secondary',
				'icon'  => 'bolt',
				'title' => __( 'Premier argument', 'starter' ),
				'text'  => __( 'Le bénéfice concret pour le client.', 'starter' ),
			),
			array(
				'badge' => __( 'Simplicité', 'starter' ),
				'color' => 'secondary',
				'icon'  => 'layers',
				'title' => __( 'Deuxième argument', 'starter' ),
				'text'  => __( 'Le bénéfice concret pour le client.', 'starter' ),
			),
			array(
				'badge' => __( 'Suivi', 'starter' ),
				'color' => 'secondary',
				'icon'  => 'chart',
				'title' => __( 'Troisième argument', 'starter' ),
				'text'  => __( 'Le bénéfice concret pour le client.', 'starter' ),
			),
			array(
				'badge' => __( 'Sérénité', 'starter' ),
				'color' => 'secondary',
				'icon'  => 'shield',
				'title' => __( 'Quatrième argument', 'starter' ),
				'text'  => __( 'Le bénéfice concret pour le client.', 'starter' ),
			),
		),
	)
);

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
	'cta',
	array(
		'title'      => __( 'Prêt à vous lancer ?', 'starter' ),
		'text'       => __( 'Laissez votre e-mail, on vous rappelle sous 48 h.', 'starter' ),
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
