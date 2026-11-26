<?php
/**
 * Petites fonctions utilisées par les gabarits (listes, articles, réalisations).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Taxonomie « principale » d'un type de contenu (pastille sur les cartes, filtres).
 *
 * @param string $post_type Type de contenu.
 * @return string Nom de la taxonomie, ou '' si aucune.
 */
function starter_post_taxonomy( string $post_type ): string {
	$map = array(
		'post'      => 'category',
		'reference' => 'reference_category',
	);
	return $map[ $post_type ] ?? '';
}

/**
 * Première catégorie d'un contenu (hors « Non classé »).
 *
 * @param int|null $post_id ID du contenu, null = contenu courant.
 */
function starter_primary_term( ?int $post_id = null ): ?WP_Term {
	$post_id  = $post_id ?? get_the_ID();
	$taxonomy = starter_post_taxonomy( (string) get_post_type( $post_id ) );
	if ( '' === $taxonomy ) {
		return null;
	}

	$terms = get_the_terms( $post_id, $taxonomy );
	if ( ! is_array( $terms ) ) {
		return null;
	}
	foreach ( $terms as $term ) {
		if ( 'category' !== $taxonomy || (int) get_option( 'default_category' ) !== $term->term_id ) {
			return $term;
		}
	}
	return null;
}

/**
 * Page « liste » d'un type de contenu : page des articles, ou archive du CPT.
 *
 * @param string $post_type Type de contenu.
 * @return array{url: string, label: string}|null
 */
function starter_post_type_list( string $post_type ): ?array {
	if ( 'post' === $post_type ) {
		$page = (int) get_option( 'page_for_posts' );
		return $page ? array(
			'url'   => (string) get_permalink( $page ),
			'label' => get_the_title( $page ),
		) : null;
	}

	$url = get_post_type_archive_link( $post_type );
	$obj = get_post_type_object( $post_type );
	return $url && $obj ? array(
		'url'   => $url,
		'label' => $obj->labels->name,
	) : null;
}

/**
 * Temps de lecture estimé (200 mots / minute), en minutes.
 *
 * @param int|null $post_id ID du contenu, null = contenu courant.
 */
function starter_reading_time( ?int $post_id = null ): int {
	$words = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ?? get_the_ID() ) ) );
	return max( 1, (int) round( $words / 200 ) );
}

/*
 * Titres d'archive sans préfixe : « Conseils » au lieu de « Catégorie : Conseils ».
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
