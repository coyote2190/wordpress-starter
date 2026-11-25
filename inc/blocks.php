<?php
/**
 * Blocs Gutenberg du thème.
 *
 * - enregistre automatiquement chaque dossier de blocks/ qui contient un block.json ;
 * - ajoute une catégorie « Starter » dans l'outil d'insertion de blocs ;
 * - charge les styles Tailwind dans l'éditeur pour que les blocs y ressemblent au site.
 *
 * Voir docs/blocks.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'starter_register_blocks' );

/**
 * Enregistre tous les blocs :
 * - blocks/{nom}/block.json               : blocs SCF (PHP) ;
 * - blocks-native/build/{nom}/block.json  : blocs natifs React, compilés par `npm run blocks:build`.
 */
function starter_register_blocks(): void {
	$block_jsons = array_merge(
		glob( STARTER_DIR . '/blocks/*/block.json' ) ?: array(),
		glob( STARTER_DIR . '/blocks-native/build/*/block.json' ) ?: array()
	);

	foreach ( $block_jsons as $block_json ) {
		register_block_type( dirname( $block_json ) );
	}
}

/**
 * Classe du conteneur intérieur d'un bloc selon son alignement.
 *
 * Règle commune à tous les blocs du thème :
 * - « Pleine largeur » : le fond du bloc couvre tout l'écran, mais le contenu
 *   reste aligné sur la grille du site → conteneur `container` à l'intérieur ;
 * - « Aucun » / « Grande largeur » : le bloc a déjà la bonne largeur → rien.
 *
 * @param string|null $align Valeur de l'attribut align ($block['align'] en SCF, $attributes['align'] en natif).
 */
function starter_block_inner_class( ?string $align ): string {
	return 'full' === $align ? 'container' : '';
}

add_filter( 'block_categories_all', 'starter_block_category' );

/**
 * Ajoute la catégorie « Starter » en tête de liste dans l'éditeur.
 *
 * @param array<int, array<string, string>> $categories Catégories existantes.
 * @return array<int, array<string, string>>
 */
function starter_block_category( array $categories ): array {
	array_unshift(
		$categories,
		array(
			'slug'  => 'starter',
			'title' => __( 'Starter', 'starter' ),
		)
	);
	return $categories;
}

add_action( 'enqueue_block_assets', 'starter_editor_styles' );

/**
 * Styles Tailwind dans l'éditeur Gutenberg.
 *
 * `enqueue_block_assets` charge le CSS DANS l'iframe de l'éditeur (là où le
 * contenu est affiché), sans toucher à l'interface de WordPress autour.
 * Ce hook est aussi appelé côté visiteur : on se limite à l'admin.
 */
function starter_editor_styles(): void {
	if ( is_admin() ) {
		starter_vite_enqueue( 'starter-editor', 'assets/src/css/editor.css' );
	}
}
