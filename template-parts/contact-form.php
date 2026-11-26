<?php
/**
 * Formulaire de contact (HTML). Le traitement est dans le mu-plugin :
 * mu-plugin/starter-core/contact-form.php.
 *
 * Fonctionne sans JS (envoi classique + redirection) ; avec JS, le module
 * assets/src/js/modules/contact-form.js envoie en arrière-plan.
 *
 * Voir docs/contact-form.md.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'starter_contact_token' ) ) {
	if ( current_user_can( 'manage_options' ) ) {
		echo '<p class="rounded bg-red-50 p-4 text-red-700">' . esc_html__( 'Formulaire indisponible : le mu-plugin « Starter Core » n\'est pas installé.', 'starter' ) . '</p>';
	}
	return;
}

// État après un envoi sans JS (?contact=sent / ?contact=error&contact_key=…).
// phpcs:disable WordPress.Security.NonceVerification.Recommended -- simple lecture d'un état d'affichage.
$starter_status = sanitize_key( $_GET['contact'] ?? '' );
$starter_state  = array(
	'message' => '',
	'errors'  => array(),
	'values'  => array(),
);
if ( 'sent' === $starter_status ) {
	$starter_state['message'] = __( 'Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.', 'starter' );
} elseif ( 'error' === $starter_status ) {
	$starter_key    = sanitize_key( $_GET['contact_key'] ?? '' );
	$starter_stored = $starter_key ? get_transient( 'starter_contact_' . $starter_key ) : false;
	if ( is_array( $starter_stored ) ) {
		$starter_state = array_merge( $starter_state, $starter_stored );
		delete_transient( 'starter_contact_' . $starter_key );
	}
}
// phpcs:enable

// E-mail pré-rempli quand on arrive depuis un bloc « Appel à l'action » (?email=…).
if ( empty( $starter_state['values']['email'] ) && ! empty( $_GET['email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	$starter_state['values']['email'] = sanitize_email( wp_unslash( $_GET['email'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
}

$starter_value = static fn( string $name ): string => (string) ( $starter_state['values'][ $name ] ?? '' );
$starter_error = static fn( string $name ): string => (string) ( $starter_state['errors'][ $name ] ?? '' );

/**
 * Attributs d'accessibilité d'un champ : lien vers son message d'erreur + état invalide.
 */
$starter_field_attrs = static function ( string $name ) use ( $starter_error ): string {
	$attrs = sprintf( 'id="contact-%1$s" name="%1$s" aria-describedby="contact-%1$s-error"', esc_attr( $name ) );
	return $starter_error( $name ) ? $attrs . ' aria-invalid="true"' : $attrs;
};

$starter_input = 'w-full rounded-2xl border border-light bg-paper px-4.5 py-3.5 placeholder:text-soft transition focus:border-dark focus:bg-white focus:outline-none aria-invalid:border-red-600';
$starter_label = 'mb-2 block text-[15px] font-medium';
$starter_hint  = 'mt-1 text-sm text-red-600';

$starter_redirect = remove_query_arg( array( 'contact', 'contact_key' ) );
?>
<form
	id="contact-form"
	class="contact-form grid gap-5 sm:grid-cols-2"
	method="post"
	action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
	data-module="contact-form"
>
	<input type="hidden" name="action" value="<?php echo esc_attr( STARTER_CONTACT_ACTION ); ?>">
	<input type="hidden" name="_redirect" value="<?php echo esc_attr( $starter_redirect ); ?>">
	<input type="hidden" name="_started" value="<?php echo esc_attr( starter_contact_token() ); ?>">
	<?php wp_nonce_field( STARTER_CONTACT_ACTION, '_wpnonce', false ); ?>

	<?php // Pot de miel : caché aux humains (et aux lecteurs d'écran), rempli par les robots. ?>
	<div class="absolute -left-[9999px]" aria-hidden="true">
		<label for="contact-website">Website</label>
		<input id="contact-website" type="text" name="website" tabindex="-1" autocomplete="off">
	</div>

	<?php // Message global (succès / erreur). role="status" : annoncé par les lecteurs d'écran. ?>
	<div
		class="rounded-2xl p-4 empty:hidden sm:col-span-2 <?php echo 'sent' === $starter_status ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-700'; ?>"
		role="status"
		data-contact-status
	><?php echo esc_html( $starter_state['message'] ); ?></div>

	<div>
		<label class="<?php echo esc_attr( $starter_label ); ?>" for="contact-name"><?php esc_html_e( 'Nom', 'starter' ); ?> *</label>
		<input class="<?php echo esc_attr( $starter_input ); ?>" <?php echo $starter_field_attrs( 'name' ); // phpcs:ignore ?> type="text" autocomplete="name" maxlength="100" required value="<?php echo esc_attr( $starter_value( 'name' ) ); ?>">
		<p class="<?php echo esc_attr( $starter_hint ); ?> empty:hidden" id="contact-name-error" data-error-for="name"><?php echo esc_html( $starter_error( 'name' ) ); ?></p>
	</div>

	<div>
		<label class="<?php echo esc_attr( $starter_label ); ?>" for="contact-email"><?php esc_html_e( 'E-mail', 'starter' ); ?> *</label>
		<input class="<?php echo esc_attr( $starter_input ); ?>" <?php echo $starter_field_attrs( 'email' ); // phpcs:ignore ?> type="email" autocomplete="email" required value="<?php echo esc_attr( $starter_value( 'email' ) ); ?>">
		<p class="<?php echo esc_attr( $starter_hint ); ?> empty:hidden" id="contact-email-error" data-error-for="email"><?php echo esc_html( $starter_error( 'email' ) ); ?></p>
	</div>

	<div class="sm:col-span-2">
		<label class="<?php echo esc_attr( $starter_label ); ?>" for="contact-phone"><?php esc_html_e( 'Téléphone', 'starter' ); ?></label>
		<input class="<?php echo esc_attr( $starter_input ); ?>" <?php echo $starter_field_attrs( 'phone' ); // phpcs:ignore ?> type="tel" autocomplete="tel" value="<?php echo esc_attr( $starter_value( 'phone' ) ); ?>">
		<p class="<?php echo esc_attr( $starter_hint ); ?> empty:hidden" id="contact-phone-error" data-error-for="phone"><?php echo esc_html( $starter_error( 'phone' ) ); ?></p>
	</div>

	<div class="sm:col-span-2">
		<label class="<?php echo esc_attr( $starter_label ); ?>" for="contact-message"><?php esc_html_e( 'Message', 'starter' ); ?> *</label>
		<textarea class="<?php echo esc_attr( $starter_input ); ?>" <?php echo $starter_field_attrs( 'message' ); // phpcs:ignore ?> rows="6" minlength="10" maxlength="5000" required><?php echo esc_textarea( $starter_value( 'message' ) ); ?></textarea>
		<p class="<?php echo esc_attr( $starter_hint ); ?> empty:hidden" id="contact-message-error" data-error-for="message"><?php echo esc_html( $starter_error( 'message' ) ); ?></p>
	</div>

	<div class="sm:col-span-2">
		<label class="flex items-start gap-3 text-sm text-muted" for="contact-consent">
			<input class="mt-0.5 size-4.5 shrink-0 accent-dark" <?php echo $starter_field_attrs( 'consent' ); // phpcs:ignore ?> type="checkbox" value="1" required <?php checked( $starter_value( 'consent' ), '1' ); ?>>
			<span>
				<?php esc_html_e( 'J\'accepte que mes données soient utilisées pour me recontacter.', 'starter' ); ?>
				<?php if ( get_privacy_policy_url() ) : ?>
					<a class="text-dark underline underline-offset-2" href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Politique de confidentialité', 'starter' ); ?></a>
				<?php endif; ?>
			</span>
		</label>
		<p class="<?php echo esc_attr( $starter_hint ); ?> empty:hidden" id="contact-consent-error" data-error-for="consent"><?php echo esc_html( $starter_error( 'consent' ) ); ?></p>
	</div>

	<div class="sm:col-span-2">
		<button
			class="inline-flex items-center gap-2.5 rounded-full bg-dark py-2 pr-5.5 pl-2 font-medium text-white transition hover:bg-primary disabled:opacity-60"
			type="submit"
			data-contact-submit
		>
			<span class="inline-flex size-9 items-center justify-center rounded-full border-[1.5px] border-white/50"><?php echo starter_icon( 'arrow-right', 'size-4' ); ?></span>
			<?php esc_html_e( 'Envoyer le message', 'starter' ); ?>
		</button>
	</div>
</form>
