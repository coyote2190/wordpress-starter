<?php
/**
 * Title: Page « Contact »
 * Slug: starter/page-contact
 * Categories: starter-pages
 * Keywords: contact, formulaire, devis
 * Post Types: page
 * Block Types: core/post-content
 * Viewport Width: 1400
 * Description: Titre et formulaire de contact (coordonnées tirées de « Infos du site »).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

echo starter_pattern_block( // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs (serialize_block).
	'hero',
	array(
		'title' => __( '**Parlons** de votre projet', 'starter' ),
		'text'  => __( 'Une question, un devis ? Écrivez-nous : réponse sous 24 h ouvrées.', 'starter' ),
	),
	array( 'align' => 'wide' )
);

echo starter_pattern_block( 'contact-form', array(), array( 'align' => 'wide' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- balisage de blocs.
