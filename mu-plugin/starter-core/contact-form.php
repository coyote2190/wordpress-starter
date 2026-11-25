<?php
/**
 * Formulaire de contact : traitement côté serveur.
 *
 * Le HTML est dans le thème (template-parts/contact-form.php, bloc « Formulaire
 * de contact »). Ici : vérifications anti-spam, validation, envoi par wp_mail()
 * — FluentSMTP se charge de l'envoi réel par SMTP.
 *
 * Deux modes de réponse :
 * - requête JS (en-tête Accept: application/json) → réponse JSON ;
 * - sans JS → redirection vers la page avec ?contact=sent ou ?contact=error.
 *
 * Voir docs/contact-form.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

const STARTER_CONTACT_ACTION      = 'starter_contact';
const STARTER_CONTACT_MIN_SECONDS = 3;  // Un humain met plus de 3 s à remplir le formulaire.
const STARTER_CONTACT_MAX_PER_IP  = 5;  // Envois maximum par IP et par heure.

// admin-post.php?action=starter_contact, visiteur connecté ou non.
add_action( 'admin_post_nopriv_' . STARTER_CONTACT_ACTION, 'starter_contact_handle' );
add_action( 'admin_post_' . STARTER_CONTACT_ACTION, 'starter_contact_handle' );

/**
 * Jeton anti-robot : heure d'affichage du formulaire, signée.
 * Sert à vérifier que le formulaire n'a pas été envoyé trop vite.
 */
function starter_contact_token(): string {
	$time = (string) time();
	return $time . '.' . wp_hash( $time . '|' . STARTER_CONTACT_ACTION );
}

/**
 * Adresse qui reçoit les messages : champ « E-mail » de la page
 * « Infos du site », sinon l'e-mail d'administration.
 */
function starter_contact_recipient(): string {
	$email = function_exists( 'get_field' ) ? (string) get_field( 'email', 'option' ) : '';
	$email = is_email( $email ) ? $email : (string) get_option( 'admin_email' );

	/**
	 * Permet de changer le destinataire (ex. selon le sujet choisi).
	 */
	return (string) apply_filters( 'starter_contact_recipient', $email );
}

/**
 * Point d'entrée : reçoit le POST, traite, répond.
 */
function starter_contact_handle(): void {
	$result = starter_contact_process( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification -- vérifié dans starter_contact_process().

	// --- Requête JS : réponse JSON. ---
	if ( str_contains( (string) ( $_SERVER['HTTP_ACCEPT'] ?? '' ), 'application/json' ) ) {
		wp_send_json(
			array(
				'success' => $result['ok'],
				'message' => $result['message'],
				'errors'  => $result['errors'],
			),
			$result['ok'] ? 200 : 422
		);
	}

	// --- Sans JS : retour sur la page du formulaire. ---
	$back = wp_validate_redirect( (string) wp_unslash( $_POST['_redirect'] ?? '' ), home_url( '/' ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$back = remove_query_arg( array( 'contact', 'contact_key' ), $back );

	if ( $result['ok'] ) {
		$back = add_query_arg( 'contact', 'sent', $back );
	} else {
		// Erreurs et saisies gardées 10 min pour réafficher le formulaire rempli.
		$key = wp_generate_password( 16, false );
		set_transient( 'starter_contact_' . $key, $result, 10 * MINUTE_IN_SECONDS );
		$back = add_query_arg(
			array(
				'contact'     => 'error',
				'contact_key' => $key,
			),
			$back
		);
	}

	wp_safe_redirect( $back . '#contact-form' );
	exit;
}

/**
 * Vérifie, valide et envoie.
 *
 * @param array<string, mixed> $data Données POST (déjà unslashées).
 * @return array{ok: bool, message: string, errors: array<string, string>, values: array<string, string>}
 */
function starter_contact_process( array $data ): array {
	$fail = static fn( string $message, array $errors = array(), array $values = array() ): array => array(
		'ok'      => false,
		'message' => $message,
		'errors'  => $errors,
		'values'  => $values,
	);
	$success = array(
		'ok'      => true,
		'message' => __( 'Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.', 'starter' ),
		'errors'  => array(),
		'values'  => array(),
	);

	// Nettoyage des champs (gardés pour réafficher le formulaire en cas d'erreur).
	$values = array(
		'name'    => sanitize_text_field( (string) ( $data['name'] ?? '' ) ),
		'email'   => sanitize_email( (string) ( $data['email'] ?? '' ) ),
		'phone'   => sanitize_text_field( (string) ( $data['phone'] ?? '' ) ),
		'message' => sanitize_textarea_field( (string) ( $data['message'] ?? '' ) ),
		'consent' => empty( $data['consent'] ) ? '' : '1',
	);

	// 1. Nonce : le formulaire vient bien de notre site.
	if ( ! wp_verify_nonce( (string) ( $data['_wpnonce'] ?? '' ), STARTER_CONTACT_ACTION ) ) {
		return $fail( __( 'La session a expiré. Rechargez la page et réessayez.', 'starter' ), array(), $values );
	}

	// 2. Pot de miel : champ invisible pour un humain, rempli par les robots.
	//    On fait semblant que tout va bien pour ne pas renseigner le robot.
	if ( ! empty( $data['website'] ) ) {
		return $success;
	}

	// 3. Délai : jeton signé + envoi trop rapide = robot.
	[ $time, $hash ] = array_pad( explode( '.', (string) ( $data['_started'] ?? '' ), 2 ), 2, '' );
	if ( ! hash_equals( wp_hash( $time . '|' . STARTER_CONTACT_ACTION ), $hash ) ) {
		return $fail( __( 'La session a expiré. Rechargez la page et réessayez.', 'starter' ), array(), $values );
	}
	if ( time() - (int) $time < STARTER_CONTACT_MIN_SECONDS ) {
		return $success;
	}

	// 4. Limite d'envois par IP.
	$ip_key = 'starter_contact_rl_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= STARTER_CONTACT_MAX_PER_IP ) {
		return $fail( __( 'Trop de messages envoyés. Réessayez dans une heure.', 'starter' ), array(), $values );
	}

	// 5. Validation des champs.

	$errors = array();
	if ( '' === $values['name'] ) {
		$errors['name'] = __( 'Indiquez votre nom.', 'starter' );
	} elseif ( mb_strlen( $values['name'] ) > 100 ) {
		$errors['name'] = __( 'Nom trop long (100 caractères maximum).', 'starter' );
	}
	if ( ! is_email( $values['email'] ) ) {
		$errors['email'] = __( 'Indiquez une adresse e-mail valide.', 'starter' );
	}
	if ( '' !== $values['phone'] && ! preg_match( '/^[0-9 +().-]{6,20}$/', $values['phone'] ) ) {
		$errors['phone'] = __( 'Numéro de téléphone invalide.', 'starter' );
	}
	if ( mb_strlen( $values['message'] ) < 10 ) {
		$errors['message'] = __( 'Votre message doit faire au moins 10 caractères.', 'starter' );
	} elseif ( mb_strlen( $values['message'] ) > 5000 ) {
		$errors['message'] = __( 'Message trop long (5 000 caractères maximum).', 'starter' );
	}
	if ( '' === $values['consent'] ) {
		$errors['consent'] = __( 'Merci d\'accepter le traitement de vos données.', 'starter' );
	}

	if ( $errors ) {
		return $fail( __( 'Le formulaire contient des erreurs.', 'starter' ), $errors, $values );
	}

	// 6. Envoi.
	$site    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$subject = sprintf( '[%s] %s', $site, sprintf( __( 'Nouveau message de %s', 'starter' ), $values['name'] ) );
	$body    = implode(
		"\n",
		array(
			sprintf( '%s : %s', __( 'Nom', 'starter' ), $values['name'] ),
			sprintf( '%s : %s', __( 'E-mail', 'starter' ), $values['email'] ),
			sprintf( '%s : %s', __( 'Téléphone', 'starter' ), $values['phone'] ?: '—' ),
			'',
			$values['message'],
			'',
			'—',
			sprintf( __( 'Envoyé depuis %s', 'starter' ), home_url( '/' ) ),
		)
	);
	// Reply-To : « Répondre » dans la messagerie répond directement au visiteur.
	// Uniquement l'adresse (validée par is_email) : pas d'injection d'en-tête possible.
	$headers = array( 'Reply-To: ' . $values['email'] );

	if ( ! wp_mail( starter_contact_recipient(), $subject, $body, $headers ) ) {
		return $fail( __( 'L\'envoi a échoué. Réessayez plus tard ou contactez-nous par téléphone.', 'starter' ), array(), $values );
	}

	set_transient( $ip_key, $count + 1, HOUR_IN_SECONDS );

	/**
	 * Après un envoi réussi (ex. : enregistrer le message, l'envoyer à un CRM…).
	 *
	 * @param array<string, string> $values Champs nettoyés.
	 */
	do_action( 'starter_contact_sent', $values );

	return $success;
}
