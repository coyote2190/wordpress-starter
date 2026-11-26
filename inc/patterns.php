<?php
/**
 * Compositions (patterns) : pages et sections toutes faites, à insérer en un clic.
 *
 * Les fichiers de patterns/ sont enregistrés automatiquement par WordPress
 * (en-tête « Title / Slug / Categories »). Ici : leurs catégories, et
 * starter_pattern_block() pour écrire un bloc SCF à partir des noms de champs.
 *
 * Voir docs/patterns.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'starter_register_pattern_categories' );

/**
 * Catégories affichées dans l'onglet « Compositions » de l'éditeur.
 */
function starter_register_pattern_categories(): void {
	register_block_pattern_category( 'starter-pages', array( 'label' => __( 'Pages du site', 'starter' ) ) );
	register_block_pattern_category( 'starter-sections', array( 'label' => __( 'Sections du site', 'starter' ) ) );
}

// Seulement nos compositions : pas celles de WordPress.org (sans rapport avec le design du thème).
add_filter( 'should_load_remote_block_patterns', '__return_false' );
add_action( 'after_setup_theme', static fn() => remove_theme_support( 'core-block-patterns' ) );

/**
 * Balisage d'un bloc SCF, prêt à mettre dans une composition.
 *
 * SCF range les données d'un bloc « à plat », avec la clé de chaque champ :
 * { title: "…", _title: "field_…", items: 2, items_0_title: "…", _items_0_title: "field_…" }.
 * Cette fonction retrouve les clés dans scf-json/ : on n'écrit que les noms.
 *
 * @param string               $name   Nom du bloc, sans « starter/ » (ex. 'hero').
 * @param array<string, mixed> $values Valeurs par nom de champ. Répéteur : liste de lignes.
 * @param array<string, mixed> $attrs  Attributs du bloc (align, className…).
 */
function starter_pattern_block( string $name, array $values = array(), array $attrs = array() ): string {
	$data = array();
	starter_pattern_flatten( $data, $values, starter_pattern_field_keys( 'starter/' . $name ), '' );

	return serialize_block(
		array(
			'blockName'    => 'starter/' . $name,
			'attrs'        => array(
				'name' => 'starter/' . $name,
				'mode' => 'preview',
			) + $attrs + ( $data ? array( 'data' => $data ) : array() ),
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		)
	) . "\n\n";
}

/**
 * Clés des champs SCF d'un bloc : [ nom => clé ], et [ nom => [ 'key', 'sub' ] ] pour un répéteur.
 *
 * @return array<string, mixed>
 */
function starter_pattern_field_keys( string $block ): array {
	static $cache = array();

	if ( ! isset( $cache[ $block ] ) ) {
		$cache[ $block ] = array();
		foreach ( glob( STARTER_DIR . '/scf-json/group_*.json' ) ?: array() as $file ) {
			$group = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- fichier local du thème.
			if ( ( $group['location'][0][0]['value'] ?? '' ) === $block ) {
				$cache[ $block ] = starter_pattern_map_fields( $group['fields'] ?? array() );
				break;
			}
		}
	}

	return $cache[ $block ];
}

/**
 * Liste de champs SCF → [ nom => clé ] (récursif pour les répéteurs, onglets ignorés).
 *
 * @param array<int, array<string, mixed>> $fields Champs du groupe.
 * @return array<string, mixed>
 */
function starter_pattern_map_fields( array $fields ): array {
	$map = array();
	foreach ( $fields as $field ) {
		if ( empty( $field['name'] ) ) {
			continue;
		}
		$map[ $field['name'] ] = empty( $field['sub_fields'] )
			? $field['key']
			: array(
				'key' => $field['key'],
				'sub' => starter_pattern_map_fields( $field['sub_fields'] ),
			);
	}
	return $map;
}

/**
 * Valeurs → données « à plat » de SCF.
 *
 * @param array<string, mixed> $data   Résultat (modifié).
 * @param array<string, mixed> $values Valeurs par nom de champ.
 * @param array<string, mixed> $keys   Clés (starter_pattern_field_keys()).
 * @param string               $prefix Préfixe des lignes de répéteur (ex. 'items_0_').
 */
function starter_pattern_flatten( array &$data, array $values, array $keys, string $prefix ): void {
	foreach ( $values as $field => $value ) {
		$key = $keys[ $field ] ?? null;
		if ( null === $key ) {
			_doing_it_wrong( __FUNCTION__, esc_html( "Champ « {$field} » inconnu." ), '1.0.0' );
			continue;
		}

		// Répéteur : nombre de lignes, puis chaque sous-champ.
		if ( is_array( $key ) ) {
			$rows                           = array_values( (array) $value );
			$data[ $prefix . $field ]       = count( $rows );
			$data[ '_' . $prefix . $field ] = $key['key'];
			foreach ( $rows as $i => $row ) {
				starter_pattern_flatten( $data, (array) $row, $key['sub'], "{$prefix}{$field}_{$i}_" );
			}
			continue;
		}

		// Champ Lien : toujours les trois clés.
		if ( is_array( $value ) && isset( $value['url'] ) ) {
			$value += array(
				'title'  => '',
				'target' => '',
			);
		}

		$data[ $prefix . $field ]       = $value;
		$data[ '_' . $prefix . $field ] = $key;
	}
}
