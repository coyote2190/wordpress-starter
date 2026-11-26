<?php
/**
 * SEO — page Réglages > SEO : réglages globaux.
 *
 * - image de partage par défaut (pages sans image) ;
 * - texte d'introduction de /llms.txt ;
 * - lignes ajoutées au robots.txt ;
 * - blocage des robots d'entraînement des IA (case à cocher).
 *
 * Tout est stocké dans une seule option : `starter_seo`.
 *
 * Voir docs/seo.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

const STARTER_SEO_OPTION = 'starter_seo';

/**
 * Un réglage SEO global.
 *
 * @param string $key     default_image, llms_intro, robots_extra, block_ai_training.
 * @param mixed  $default Valeur si non réglé.
 * @return mixed
 */
function starter_seo_setting( string $key, mixed $default = '' ): mixed {
	$options = (array) get_option( STARTER_SEO_OPTION, array() );
	return ( isset( $options[ $key ] ) && '' !== $options[ $key ] ) ? $options[ $key ] : $default;
}

/**
 * Le site est-il ouvert aux moteurs ? Non si « Demander aux moteurs de ne pas
 * indexer » est coché, ou sur une préprod (WP_ENVIRONMENT_TYPE = 'staging') :
 * une préprod ne doit jamais apparaître dans Google.
 * (Un site local n'est pas joignable depuis Internet : inutile de le fermer,
 * et Lighthouse peut ainsi auditer le SEO en local.)
 */
function starter_seo_site_indexable(): bool {
	$indexable = (bool) get_option( 'blog_public' ) && 'staging' !== wp_get_environment_type();
	/**
	 * Forcer l'ouverture / la fermeture du site aux moteurs.
	 */
	return (bool) apply_filters( 'starter_seo_site_indexable', $indexable );
}

/**
 * Champ de « Infos du site » (SCF), sans dépendre du thème.
 *
 * @return mixed
 */
function starter_seo_site_info( string $name ): mixed {
	return function_exists( 'get_field' ) ? get_field( $name, 'option' ) : '';
}

add_action( 'admin_menu', 'starter_seo_settings_menu' );

/**
 * Réglages > SEO.
 */
function starter_seo_settings_menu(): void {
	add_options_page( __( 'SEO', 'starter' ), __( 'SEO', 'starter' ), 'manage_options', 'starter-seo', 'starter_seo_settings_page' );
}

// Sur `init` (et pas seulement dans l'admin) : le nettoyage s'applique à tout
// update_option(), y compris en WP-CLI.
add_action( 'init', 'starter_seo_register_settings' );

/**
 * Déclare l'option et son nettoyage.
 */
function starter_seo_register_settings(): void {
	register_setting(
		'starter_seo',
		STARTER_SEO_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => static fn( $input ): array => array(
				'default_image'     => absint( $input['default_image'] ?? 0 ),
				'llms_intro'        => sanitize_textarea_field( (string) ( $input['llms_intro'] ?? '' ) ),
				// Seules les directives robots.txt connues sont gardées.
				'robots_extra'      => implode(
					"\n",
					array_filter(
						array_map( 'trim', explode( "\n", sanitize_textarea_field( (string) ( $input['robots_extra'] ?? '' ) ) ) ),
						static fn( string $line ): bool => (bool) preg_match( '/^(User-agent|Disallow|Allow|Crawl-delay):\s*\S*/i', $line )
					)
				),
				'block_ai_training' => empty( $input['block_ai_training'] ) ? 0 : 1,
			),
			'default'           => array(),
		)
	);
}

/**
 * HTML de la page de réglages.
 */
function starter_seo_settings_page(): void {
	$name  = STARTER_SEO_OPTION;
	$image = (int) starter_seo_setting( 'default_image', 0 );
	wp_enqueue_media();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'SEO', 'starter' ); ?></h1>

		<?php if ( ! starter_seo_site_indexable() ) : ?>
			<div class="notice notice-warning inline"><p>
				<?php
				echo esc_html(
					get_option( 'blog_public' )
						/* translators: %s : type d'environnement. */
						? sprintf( __( 'Environnement « %s » : le site est volontairement fermé aux moteurs de recherche (robots.txt « Disallow: / » + noindex). Il s\'ouvrira tout seul en production.', 'starter' ), wp_get_environment_type() )
						: __( 'Réglages > Lecture : « Demander aux moteurs de recherche de ne pas indexer ce site » est coché. Le site n\'apparaîtra pas dans Google.', 'starter' )
				);
				?>
			</p></div>
		<?php endif; ?>

		<p>
			<?php esc_html_e( 'Titre, description et image de chaque page : encadré « SEO » sous l\'éditeur de la page.', 'starter' ); ?><br>
			<?php esc_html_e( 'Fichiers générés :', 'starter' ); ?>
			<a href="<?php echo esc_url( get_sitemap_url( 'index' ) ); ?>" target="_blank">sitemap</a> ·
			<a href="<?php echo esc_url( home_url( '/robots.txt' ) ); ?>" target="_blank">robots.txt</a> ·
			<a href="<?php echo esc_url( home_url( '/llms.txt' ) ); ?>" target="_blank">llms.txt</a>
		</p>

		<form method="post" action="options.php">
			<?php settings_fields( 'starter_seo' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Image de partage par défaut', 'starter' ); ?></th>
					<td>
						<div data-starter-seo-image>
							<input type="hidden" name="<?php echo esc_attr( $name ); ?>[default_image]" value="<?php echo esc_attr( (string) $image ); ?>" data-starter-seo-image-input>
							<div data-starter-seo-image-preview><?php echo $image ? wp_get_attachment_image( $image, 'medium' ) : ''; ?></div>
							<button type="button" class="button" data-starter-seo-image-choose><?php esc_html_e( 'Choisir une image', 'starter' ); ?></button>
							<button type="button" class="button-link" data-starter-seo-image-remove <?php echo $image ? '' : 'hidden'; ?>><?php esc_html_e( 'Retirer', 'starter' ); ?></button>
						</div>
						<p class="description"><?php esc_html_e( 'Utilisée quand une page n\'a ni image SEO, ni image mise en avant, ni Hero. Idéal : 1200 × 630 px.', 'starter' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="starter-seo-llms"><?php esc_html_e( 'Présentation pour les IA (llms.txt)', 'starter' ); ?></label></th>
					<td>
						<textarea id="starter-seo-llms" class="large-text" rows="5" name="<?php echo esc_attr( $name ); ?>[llms_intro]"><?php echo esc_textarea( (string) starter_seo_setting( 'llms_intro' ) ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Quelques phrases : qui vous êtes, ce que vous proposez, pour qui, où. Reprises par ChatGPT, Claude, Perplexity… quand ils parlent du site.', 'starter' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Robots des IA', 'starter' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="<?php echo esc_attr( $name ); ?>[block_ai_training]" value="1" <?php checked( (int) starter_seo_setting( 'block_ai_training', 0 ), 1 ); ?>>
							<?php esc_html_e( 'Interdire l\'utilisation du contenu pour entraîner des IA', 'starter' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Bloque GPTBot, Google-Extended, CCBot, ClaudeBot, Applebot-Extended… dans le robots.txt. Les moteurs de recherche et les assistants qui citent le site ne sont pas concernés.', 'starter' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="starter-seo-robots"><?php esc_html_e( 'Lignes robots.txt en plus', 'starter' ); ?></label></th>
					<td>
						<textarea id="starter-seo-robots" class="large-text code" rows="4" name="<?php echo esc_attr( $name ); ?>[robots_extra]" placeholder="Disallow: /page-privee/"><?php echo esc_textarea( (string) starter_seo_setting( 'robots_extra' ) ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Une directive par ligne (User-agent, Disallow, Allow). Pour retirer UNE page de Google, préférez la case « Ne pas indexer » de la page.', 'starter' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
	starter_seo_media_script();
}

/**
 * Petit script du sélecteur d'image (médiathèque WordPress), partagé avec l'encadré SEO.
 * Écrit dans la page (pas de fichier) : le mu-plugin n'a pas d'URL fiable quand il est lié en symlink.
 */
function starter_seo_media_script(): void {
	?>
	<script>
	document.querySelectorAll('[data-starter-seo-image]').forEach((box) => {
		const input = box.querySelector('[data-starter-seo-image-input]');
		const preview = box.querySelector('[data-starter-seo-image-preview]');
		const remove = box.querySelector('[data-starter-seo-image-remove]');
		let frame;
		box.querySelector('[data-starter-seo-image-choose]').addEventListener('click', () => {
			frame ??= wp.media({ title: <?php echo wp_json_encode( __( 'Image de partage', 'starter' ) ); ?>, library: { type: 'image' }, multiple: false });
			frame.off('select').on('select', () => {
				const image = frame.state().get('selection').first().toJSON();
				input.value = image.id;
				const url = (image.sizes?.medium ?? image.sizes?.full ?? image).url;
				preview.innerHTML = '<img src="' + url + '" alt="" style="max-width:300px;height:auto;border-radius:4px">';
				remove.hidden = false;
				input.dispatchEvent(new Event('change', { bubbles: true }));
			});
			frame.open();
		});
		remove.addEventListener('click', () => {
			input.value = '';
			preview.innerHTML = '';
			remove.hidden = true;
			input.dispatchEvent(new Event('change', { bubbles: true }));
		});
	});
	</script>
	<?php
}
