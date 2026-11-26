<?php
/**
 * Messages du formulaire de contact enregistrés dans l'admin (menu « Messages »).
 *
 * Pourquoi : si l'e-mail n'arrive pas (SMTP en panne, filtre anti-spam…), le
 * message n'est pas perdu. Il est enregistré AVANT l'envoi de l'e-mail ; la
 * colonne « E-mail » indique s'il a bien été envoyé.
 *
 * RGPD :
 * - suppression automatique après STARTER_CONTACT_RETENTION_DAYS jours (tâche quotidienne) ;
 * - export et effacement par adresse e-mail dans Outils > Exporter / Effacer
 *   les données personnelles.
 *
 * Voir docs/contact-form.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

const STARTER_CONTACT_POST_TYPE      = 'contact_message';
const STARTER_CONTACT_RETENTION_DAYS = 3 * 365; // À aligner sur la politique de confidentialité.

add_action( 'init', 'starter_contact_register_post_type' );

/**
 * Type de contenu privé : visible seulement dans l'admin, pas de création à la main.
 */
function starter_contact_register_post_type(): void {
	register_post_type(
		STARTER_CONTACT_POST_TYPE,
		array(
			'labels'          => array(
				'name'          => __( 'Messages', 'starter' ),
				'singular_name' => __( 'Message', 'starter' ),
				'all_items'     => __( 'Tous les messages', 'starter' ),
				'edit_item'     => __( 'Message', 'starter' ),
				'search_items'  => __( 'Rechercher', 'starter' ),
				'not_found'     => __( 'Aucun message', 'starter' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false, // Écran classique : lecture seule, pas d'éditeur de blocs.
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 25,
			'supports'        => array( 'title' ),
			'capability_type' => 'page', // Éditeurs et administrateurs.
			'map_meta_cap'    => true,
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ), // Pas de bouton « Ajouter ».
		)
	);
}

/**
 * Enregistre un message. Appelé par starter_contact_process() avant l'envoi de l'e-mail.
 *
 * @param array<string, string> $values Champs nettoyés (name, email, phone, message).
 * @return int ID du message, 0 en cas d'échec.
 */
function starter_contact_save( array $values ): int {
	$id = wp_insert_post(
		wp_slash(
			array(
				'post_type'    => STARTER_CONTACT_POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => $values['name'],
				'post_content' => $values['message'],
				'meta_input'   => array(
					'_starter_email'     => $values['email'],
					'_starter_phone'     => $values['phone'],
					'_starter_mail_sent' => 0,
				),
			)
		)
	);
	return is_wp_error( $id ) ? 0 : (int) $id;
}

/*
 * ---------------------------------------------------------------------------
 * Liste et lecture dans l'admin
 * ---------------------------------------------------------------------------
 */

add_filter( 'manage_' . STARTER_CONTACT_POST_TYPE . '_posts_columns', 'starter_contact_columns' );

/**
 * Colonnes de la liste : nom, e-mail, téléphone, envoi de l'e-mail, date.
 *
 * @param array<string, string> $columns Colonnes par défaut.
 * @return array<string, string>
 */
function starter_contact_columns( array $columns ): array {
	return array(
		'cb'                => $columns['cb'],
		'title'             => __( 'Nom', 'starter' ),
		'starter_email'     => __( 'E-mail', 'starter' ),
		'starter_phone'     => __( 'Téléphone', 'starter' ),
		'starter_mail_sent' => __( 'Transmis par e-mail', 'starter' ),
		'date'              => $columns['date'],
	);
}

add_action( 'manage_' . STARTER_CONTACT_POST_TYPE . '_posts_custom_column', 'starter_contact_column', 10, 2 );

/**
 * Contenu des colonnes personnalisées.
 *
 * @param string $column  Colonne.
 * @param int    $post_id Message.
 */
function starter_contact_column( string $column, int $post_id ): void {
	if ( 'starter_email' === $column ) {
		$email = (string) get_post_meta( $post_id, '_starter_email', true );
		printf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $email ), esc_html( $email ) );
	} elseif ( 'starter_phone' === $column ) {
		echo esc_html( (string) get_post_meta( $post_id, '_starter_phone', true ) ?: '—' );
	} elseif ( 'starter_mail_sent' === $column ) {
		echo get_post_meta( $post_id, '_starter_mail_sent', true )
			? '✅ ' . esc_html__( 'Oui', 'starter' )
			: '⚠️ ' . esc_html__( 'Non — à traiter depuis ici', 'starter' );
	}
}

add_action( 'add_meta_boxes_' . STARTER_CONTACT_POST_TYPE, 'starter_contact_meta_box' );

/**
 * Encadré « Message » sur l'écran d'un message.
 */
function starter_contact_meta_box(): void {
	add_meta_box( 'starter-contact-message', __( 'Message', 'starter' ), 'starter_contact_meta_box_html', null, 'normal', 'high' );
}

/**
 * Affichage du message (lecture seule) + bouton Répondre.
 *
 * @param WP_Post $post Message.
 */
function starter_contact_meta_box_html( WP_Post $post ): void {
	$email = (string) get_post_meta( $post->ID, '_starter_email', true );
	$phone = (string) get_post_meta( $post->ID, '_starter_phone', true );
	?>
	<p>
		<strong><?php esc_html_e( 'E-mail', 'starter' ); ?> :</strong> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a><br>
		<strong><?php esc_html_e( 'Téléphone', 'starter' ); ?> :</strong> <?php echo esc_html( $phone ?: '—' ); ?><br>
		<strong><?php esc_html_e( 'Reçu le', 'starter' ); ?> :</strong> <?php echo esc_html( get_the_date( 'j F Y, H:i', $post ) ); ?>
	</p>
	<div style="padding: 12px 16px; background: #f6f7f7; border-radius: 4px;"><?php echo nl2br( esc_html( $post->post_content ) ); ?></div>
	<p><a class="button button-primary" href="mailto:<?php echo esc_attr( $email ); ?>?subject=<?php echo rawurlencode( 'Re: ' . get_bloginfo( 'name' ) ); ?>"><?php esc_html_e( 'Répondre par e-mail', 'starter' ); ?></a></p>
	<?php
}

/*
 * ---------------------------------------------------------------------------
 * RGPD : durée de conservation, export, effacement
 * ---------------------------------------------------------------------------
 */

add_action( 'init', 'starter_contact_schedule_purge' );

/**
 * Programme la purge quotidienne (une seule fois).
 */
function starter_contact_schedule_purge(): void {
	if ( ! wp_next_scheduled( 'starter_contact_purge' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'starter_contact_purge' );
	}
}

add_action( 'starter_contact_purge', 'starter_contact_purge' );

/**
 * Supprime les messages plus vieux que la durée de conservation.
 */
function starter_contact_purge(): void {
	/**
	 * Durée de conservation des messages, en jours.
	 */
	$days = (int) apply_filters( 'starter_contact_retention_days', STARTER_CONTACT_RETENTION_DAYS );

	$old = get_posts(
		array(
			'post_type'      => STARTER_CONTACT_POST_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'date_query'     => array( array( 'before' => "{$days} days ago" ) ),
		)
	);
	foreach ( $old as $id ) {
		wp_delete_post( $id, true );
	}
}

/**
 * Messages envoyés par une adresse e-mail.
 *
 * @return int[]
 */
function starter_contact_ids_by_email( string $email ): array {
	return get_posts(
		array(
			'post_type'      => STARTER_CONTACT_POST_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_starter_email', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $email,           // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
}

add_filter(
	'wp_privacy_personal_data_exporters',
	static function ( array $exporters ): array {
		$exporters['starter-contact'] = array(
			'exporter_friendly_name' => __( 'Messages du formulaire de contact', 'starter' ),
			'callback'               => static function ( string $email ): array {
				$data = array();
				foreach ( starter_contact_ids_by_email( $email ) as $id ) {
					$data[] = array(
						'group_id'    => 'starter-contact',
						'group_label' => __( 'Messages du formulaire de contact', 'starter' ),
						'item_id'     => 'starter-contact-' . $id,
						'data'        => array(
							array( 'name' => __( 'Date', 'starter' ), 'value' => get_the_date( 'c', $id ) ),
							array( 'name' => __( 'Nom', 'starter' ), 'value' => get_the_title( $id ) ),
							array( 'name' => __( 'Téléphone', 'starter' ), 'value' => (string) get_post_meta( $id, '_starter_phone', true ) ),
							array( 'name' => __( 'Message', 'starter' ), 'value' => (string) get_post_field( 'post_content', $id ) ),
						),
					);
				}
				return array( 'data' => $data, 'done' => true );
			},
		);
		return $exporters;
	}
);

add_filter(
	'wp_privacy_personal_data_erasers',
	static function ( array $erasers ): array {
		$erasers['starter-contact'] = array(
			'eraser_friendly_name' => __( 'Messages du formulaire de contact', 'starter' ),
			'callback'             => static function ( string $email ): array {
				$ids = starter_contact_ids_by_email( $email );
				foreach ( $ids as $id ) {
					wp_delete_post( $id, true );
				}
				return array(
					'items_removed'  => count( $ids ) > 0,
					'items_retained' => false,
					'messages'       => array(),
					'done'           => true,
				);
			},
		);
		return $erasers;
	}
);
