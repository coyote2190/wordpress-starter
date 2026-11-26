<?php
/**
 * Title: Page « Services »
 * Slug: starter/page-services
 * Categories: starter-pages
 * Keywords: services, offres, prestations, tarifs
 * Post Types: page
 * Block Types: core/post-content
 * Viewport Width: 1400
 * Description: Hero, cartes de services avec liens, arguments, questions fréquentes et appel à l'action.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs (serialize_block).
	'hero',
	array(
		'title'  => __( 'Nos **services**', 'starter' ),
		'text'   => __( 'Une phrase qui résume ce que vous proposez et le résultat obtenu par vos clients.', 'starter' ),
		'button' => array(
			'title' => __( 'Demander un devis', 'starter' ),
			'url'   => '/contact/',
		),
	),
	array( 'align' => 'wide' )
);

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
	'features',
	array(
		'title'   => __( 'Ce que nous faisons', 'starter' ),
		'intro'   => __( 'Un service par carte : un titre court, une phrase, un lien vers le détail.', 'starter' ),
		'columns' => '3',
		'style'   => 'border',
		'items'   => array(
			array(
				'icon'  => 'monitor',
				'color' => 'secondary',
				'title' => __( 'Premier service', 'starter' ),
				'text'  => __( 'Ce que le client obtient, en une ou deux phrases.', 'starter' ),
				'link'  => array(
					'title' => __( 'En savoir plus', 'starter' ),
					'url'   => '#',
				),
			),
			array(
				'icon'  => 'palette',
				'color' => 'secondary',
				'title' => __( 'Deuxième service', 'starter' ),
				'text'  => __( 'Ce que le client obtient, en une ou deux phrases.', 'starter' ),
				'link'  => array(
					'title' => __( 'En savoir plus', 'starter' ),
					'url'   => '#',
				),
			),
			array(
				'icon'  => 'rocket',
				'color' => 'secondary',
				'title' => __( 'Troisième service', 'starter' ),
				'text'  => __( 'Ce que le client obtient, en une ou deux phrases.', 'starter' ),
				'link'  => array(
					'title' => __( 'En savoir plus', 'starter' ),
					'url'   => '#',
				),
			),
		),
	),
	array( 'align' => 'wide' )
);

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
	'why',
	array(
		'title'  => __( 'Pourquoi nous choisir ?', 'starter' ),
		'text'   => __( 'Les trois raisons que vos clients citent le plus souvent.', 'starter' ),
		'layout' => 'list',
		'cards'  => array(
			array(
				'badge' => __( 'Délais', 'starter' ),
				'color' => 'secondary',
				'icon'  => 'clock',
				'title' => __( 'Livré à la date prévue', 'starter' ),
				'text'  => __( 'Un planning clair dès le départ, et tenu.', 'starter' ),
			),
			array(
				'badge' => __( 'Qualité', 'starter' ),
				'color' => 'secondary',
				'icon'  => 'shield',
				'title' => __( 'Un travail garanti', 'starter' ),
				'text'  => __( 'Vérifié avant livraison, suivi après.', 'starter' ),
			),
			array(
				'badge' => __( 'Contact', 'starter' ),
				'color' => 'secondary',
				'icon'  => 'users',
				'title' => __( 'Un seul interlocuteur', 'starter' ),
				'text'  => __( 'Quelqu\'un qui connaît votre dossier, du début à la fin.', 'starter' ),
			),
		),
	)
);
?>
<!-- wp:pattern {"slug":"starter/section-faq"} /-->

<?php
echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
	'cta',
	array(
		'title'      => __( 'Un projet ? Parlons-en', 'starter' ),
		'text'       => __( 'Devis gratuit sous 48 h.', 'starter' ),
		'action'     => 'button',
		'button'     => array(
			'title' => __( 'Nous contacter', 'starter' ),
			'url'   => '/contact/',
		),
		'background' => 'dark',
		'accent'     => 'secondary',
	),
	array( 'align' => 'full' )
);
