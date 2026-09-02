<?php
/**
 * Traitement du formulaire de contact
 */

if (!defined('ABSPATH')) {
    exit();
}

/**
 * Traite la soumission du formulaire
 */
function starter_handle_contact_form() {
    // Vérification du nonce (protection CSRF)
    if (
        !isset($_POST['starter_contact_nonce']) ||
        !wp_verify_nonce($_POST['starter_contact_nonce'], 'starter_contact')
    ) {
        starter_contact_redirect('error');
    }

    // Honeypot anti-spam : champ caché que seuls les bots remplissent
    if (!empty($_POST['starter_website'])) {
        starter_contact_redirect('sent'); // on fait croire au bot que ça a marché
    }

    // Récupération et nettoyage
    $name = sanitize_text_field($_POST['starter_name'] ?? '');
    $email = sanitize_email($_POST['starter_email'] ?? '');
    $message = sanitize_textarea_field($_POST['starter_message'] ?? '');

    // Validation
    if (empty($name) || empty($message) || !is_email($email)) {
        starter_contact_redirect('invalid');
    }

    // Envoi
    $to = get_option('admin_email');
    $subject = sprintf(__('[%s] Nouveau message de %s', 'starter'), get_bloginfo('name'), $name);

    $body = sprintf("Nom : %s\nEmail : %s\n\nMessage :\n%s", $name, $email, $message);

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        sprintf('Reply-To: %s <%s>', $name, $email),
    ];

    $sent = wp_mail($to, $subject, $body, $headers);

    starter_contact_redirect($sent ? 'sent' : 'error');
}
add_action('admin_post_nopriv_starter_contact', 'starter_handle_contact_form');
add_action('admin_post_starter_contact', 'starter_handle_contact_form');

/**
 * Redirige vers la page d'origine avec un statut
 */
function starter_contact_redirect($status) {
    $referer = wp_get_referer() ?: home_url('/');

    wp_safe_redirect(add_query_arg('contact', $status, $referer));
    exit();
}
