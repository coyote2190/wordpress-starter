<?php
/**
 * Types de contenu (CPT) et taxonomies.
 *
 * Exemple fourni : « Réalisations » (reference) + « Catégories de réalisation ».
 * À adapter ou supprimer selon le client.
 *
 * Après toute modification d'un slug (rewrite), régénérer les permaliens :
 * Réglages > Permaliens > Enregistrer (ou `wp rewrite flush`).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'starter_register_post_types' );

/**
 * Enregistre les types de contenu.
 */
function starter_register_post_types(): void {
	register_post_type(
		'reference',
		array(
			'labels'        => array(
				'name'               => __( 'Réalisations', 'starter' ),
				'singular_name'      => __( 'Réalisation', 'starter' ),
				'add_new_item'       => __( 'Ajouter une réalisation', 'starter' ),
				'edit_item'          => __( 'Modifier la réalisation', 'starter' ),
				'all_items'          => __( 'Toutes les réalisations', 'starter' ),
				'search_items'       => __( 'Rechercher', 'starter' ),
				'not_found'          => __( 'Aucune réalisation', 'starter' ),
				'not_found_in_trash' => __( 'Aucune réalisation dans la corbeille', 'starter' ),
			),
			'public'        => true,
			'show_in_rest'  => true,               // Obligatoire pour l'éditeur Gutenberg.
			'menu_icon'     => 'dashicons-portfolio',
			'menu_position' => 20,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'has_archive'   => 'realisations',     // Liste sur /realisations/ (archive.php).
			'rewrite'       => array(
				'slug'       => 'realisations',     // Fiche sur /realisations/mon-projet/ (single.php).
				'with_front' => false,
			),
		)
	);
}

add_action( 'init', 'starter_register_taxonomies' );

/**
 * Enregistre les taxonomies.
 */
function starter_register_taxonomies(): void {
	register_taxonomy(
		'reference_category',
		array( 'reference' ),
		array(
			'labels'            => array(
				'name'          => __( 'Catégories de réalisation', 'starter' ),
				'singular_name' => __( 'Catégorie', 'starter' ),
				'add_new_item'  => __( 'Ajouter une catégorie', 'starter' ),
			),
			'hierarchical'      => true,       // Comme les catégories (cases à cocher), pas comme les étiquettes.
			'show_in_rest'      => true,
			'show_admin_column' => true,       // Colonne dans la liste des réalisations.
			'rewrite'           => array(
				'slug'       => 'realisations/categorie',
				'with_front' => false,
			),
		)
	);
}
