<?php
/**
 * SEO — encadré « SEO » sous l'éditeur, sur tous les contenus publics
 * (pages, articles, réalisations…).
 *
 * Champs (méta du contenu, préfixe « _ » = cachés de l'écran « Champs personnalisés ») :
 * - _starter_seo_title       Titre affiché dans Google et l'onglet du navigateur ;
 * - _starter_seo_description Description affichée dans Google ;
 * - _starter_seo_image       Image de partage (réseaux sociaux, messageries) ;
 * - _starter_seo_noindex     1 = pas dans Google, ni dans le sitemap, ni dans llms.txt.
 *
 * Champ vide = valeur automatique (affichée en gris dans le champ).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

const STARTER_SEO_FIELDS = array( 'title', 'description', 'image', 'noindex' );

/**
 * Valeur SEO d'un contenu.
 *
 * @param int    $post_id ID du contenu.
 * @param string $field   title, description, image, noindex.
 */
function starter_seo_meta( int $post_id, string $field ): string {
	return $post_id ? trim( (string) get_post_meta( $post_id, '_starter_seo_' . $field, true ) ) : '';
}

/**
 * Types de contenu qui ont l'encadré SEO : publics, hors médias.
 *
 * @return string[]
 */
function starter_seo_post_types(): array {
	return array_values( array_diff( get_post_types( array( 'public' => true ) ), array( 'attachment' ) ) );
}

add_action( 'add_meta_boxes', 'starter_seo_add_meta_box' );

/**
 * Ajoute l'encadré sous l'éditeur.
 */
function starter_seo_add_meta_box(): void {
	if ( starter_seo_plugin_active() ) {
		return; // L'extension SEO a son propre encadré.
	}
	add_meta_box( 'starter-seo', __( 'SEO', 'starter' ), 'starter_seo_meta_box', starter_seo_post_types(), 'normal', 'low' );
}

add_action(
	'admin_enqueue_scripts',
	static function ( string $hook ): void {
		if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && in_array( get_current_screen()->post_type ?? '', starter_seo_post_types(), true ) ) {
			wp_enqueue_media(); // Sélecteur d'image.
		}
	}
);

/**
 * HTML de l'encadré : aperçu Google, titre, description, image, « ne pas indexer ».
 */
function starter_seo_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'starter_seo_save', 'starter_seo_nonce' );

	$title       = starter_seo_meta( $post->ID, 'title' );
	$description = starter_seo_meta( $post->ID, 'description' );
	$image       = (int) starter_seo_meta( $post->ID, 'image' );
	$noindex     = '1' === starter_seo_meta( $post->ID, 'noindex' );

	// Valeurs automatiques, montrées en gris (placeholder) et dans l'aperçu.
	$auto_title       = starter_seo_default_title( $post );
	$auto_description = starter_seo_default_description( $post );
	$url              = 'publish' === $post->post_status ? (string) get_permalink( $post ) : home_url( '/' );
	?>
	<div class="starter-seo" style="display:grid;gap:16px;max-width:720px">
		<?php // Aperçu « résultat Google », mis à jour pendant la saisie. ?>
		<div style="padding:16px;border:1px solid #dcdcde;border-radius:8px;background:#fff;font-family:arial,sans-serif">
			<div style="font-size:12px;color:#4d5156;margin-bottom:4px"><?php echo esc_html( preg_replace( '#^https?://#', '', $url ) ); ?></div>
			<div data-starter-seo-preview-title style="font-size:20px;line-height:1.3;color:#1a0dab;margin-bottom:4px"></div>
			<div data-starter-seo-preview-description style="font-size:14px;line-height:1.58;color:#4d5156"></div>
		</div>

		<p style="margin:0">
			<label for="starter-seo-title"><strong><?php esc_html_e( 'Titre dans Google', 'starter' ); ?></strong></label>
			<span data-starter-seo-count="title" style="float:right"></span>
			<input id="starter-seo-title" class="widefat" type="text" name="starter_seo[title]" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php echo esc_attr( $auto_title ); ?>" data-starter-seo-field="title" data-max="60">
		</p>

		<p style="margin:0">
			<label for="starter-seo-description"><strong><?php esc_html_e( 'Description dans Google', 'starter' ); ?></strong></label>
			<span data-starter-seo-count="description" style="float:right"></span>
			<textarea id="starter-seo-description" class="widefat" rows="3" name="starter_seo[description]" placeholder="<?php echo esc_attr( $auto_description ); ?>" data-starter-seo-field="description" data-max="160"><?php echo esc_textarea( $description ); ?></textarea>
			<span class="description"><?php esc_html_e( 'Vide = extrait de la page, sinon texte du Hero, sinon début du contenu.', 'starter' ); ?></span>
		</p>

		<div>
			<strong><?php esc_html_e( 'Image de partage', 'starter' ); ?></strong>
			<div data-starter-seo-image style="margin-top:6px">
				<input type="hidden" name="starter_seo[image]" value="<?php echo esc_attr( $image ? (string) $image : '' ); ?>" data-starter-seo-image-input>
				<div data-starter-seo-image-preview><?php echo $image ? wp_get_attachment_image( $image, 'medium', false, array( 'style' => 'max-width:300px;height:auto;border-radius:4px' ) ) : ''; ?></div>
				<button type="button" class="button" data-starter-seo-image-choose><?php esc_html_e( 'Choisir une image', 'starter' ); ?></button>
				<button type="button" class="button-link" data-starter-seo-image-remove <?php echo $image ? '' : 'hidden'; ?>><?php esc_html_e( 'Retirer', 'starter' ); ?></button>
				<p class="description"><?php esc_html_e( 'Vide = image mise en avant, sinon image du Hero, sinon image par défaut (Réglages > SEO). Idéal : 1200 × 630 px.', 'starter' ); ?></p>
			</div>
		</div>

		<label>
			<input type="checkbox" name="starter_seo[noindex]" value="1" <?php checked( $noindex ); ?>>
			<strong><?php esc_html_e( 'Ne pas indexer cette page', 'starter' ); ?></strong>
			— <?php esc_html_e( 'absente de Google, du sitemap et de llms.txt (ex. page de remerciement, page en construction).', 'starter' ); ?>
		</label>
	</div>

	<script>
	(() => {
		const box = document.querySelector('.starter-seo');
		const field = (name) => box.querySelector(`[data-starter-seo-field="${name}"]`);
		const update = () => {
			for (const name of ['title', 'description']) {
				const el = field(name);
				const value = el.value.trim() || el.placeholder;
				const max = Number(el.dataset.max);
				const count = box.querySelector(`[data-starter-seo-count="${name}"]`);
				count.textContent = `${value.length} / ${max}`;
				count.style.color = value.length > max ? '#d63638' : value.length < max * 0.5 ? '#dba617' : '#00a32a';
				box.querySelector(`[data-starter-seo-preview-${name}]`).textContent =
					value.length > max ? value.slice(0, max - 1) + '…' : value;
			}
		};
		box.addEventListener('input', update);
		update();
	})();
	</script>
	<?php
	starter_seo_media_script();
}

add_action( 'save_post', 'starter_seo_save', 10, 2 );

/**
 * Enregistre les champs de l'encadré.
 */
function starter_seo_save( int $post_id, WP_Post $post ): void {
	if (
		! isset( $_POST['starter_seo_nonce'] )
		|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['starter_seo_nonce'] ) ), 'starter_seo_save' )
		|| wp_is_post_revision( $post_id )
		|| ! current_user_can( 'edit_post', $post_id )
		|| ! in_array( $post->post_type, starter_seo_post_types(), true )
	) {
		return;
	}

	$input  = (array) wp_unslash( $_POST['starter_seo'] ?? array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- nettoyé champ par champ ci-dessous.
	$values = array(
		'title'       => sanitize_text_field( (string) ( $input['title'] ?? '' ) ),
		'description' => sanitize_textarea_field( (string) ( $input['description'] ?? '' ) ),
		'image'       => absint( $input['image'] ?? 0 ) ?: '',
		'noindex'     => empty( $input['noindex'] ) ? '' : '1',
	);

	foreach ( $values as $field => $value ) {
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_starter_seo_' . $field );
		} else {
			update_post_meta( $post_id, '_starter_seo_' . $field, $value );
		}
	}
}

/*
 * Colonne « SEO » dans les listes (Pages, Articles…) : repère en un coup d'œil
 * les contenus sans description personnalisée ou exclus de Google.
 */
add_action(
	'admin_init',
	static function (): void {
		foreach ( starter_seo_post_types() as $type ) {
			add_filter(
				"manage_{$type}_posts_columns",
				static function ( array $columns ): array {
					$columns['starter_seo'] = __( 'SEO', 'starter' );
					return $columns;
				}
			);
			add_action(
				"manage_{$type}_posts_custom_column",
				static function ( string $column, int $post_id ): void {
					if ( 'starter_seo' !== $column ) {
						return;
					}
					if ( '1' === starter_seo_meta( $post_id, 'noindex' ) ) {
						echo '🚫 ' . esc_html__( 'Non indexée', 'starter' );
					} elseif ( starter_seo_meta( $post_id, 'description' ) || has_excerpt( $post_id ) ) {
						echo '✅ ' . esc_html__( 'Description', 'starter' );
					} else {
						echo '<span style="color:#787c82">' . esc_html__( 'Auto', 'starter' ) . '</span>';
					}
				},
				10,
				2
			);
		}
	}
);
