<?php
/**
 * Composant Formulaire de contact
 *
 * @param array $args {
 *   @type string $title       Titre du formulaire (optionnel)
 *   @type string $button_text Texte du bouton (optionnel)
 * }
 */

if (!defined('ABSPATH')) {
    exit();
}

$title = $args['title'] ?? '';
$button_text = $args['button_text'] ?? __('Envoyer', 'starter');
$status = isset($_GET['contact']) ? sanitize_key($_GET['contact']) : '';
?>

<div class="contact-form">

    <?php if ($title): ?>
        <h2 class="contact-form__title"><?php echo esc_html($title); ?></h2>
    <?php endif; ?>

    <?php if ($status === 'sent'): ?>
        <p class="contact-form__message contact-form__message--success" role="status">
            <?php esc_html_e('Votre message a bien été envoyé. Merci !', 'starter'); ?>
        </p>
    <?php elseif ($status === 'invalid'): ?>
        <p class="contact-form__message contact-form__message--error" role="alert">
            <?php esc_html_e('Merci de remplir tous les champs correctement.', 'starter'); ?>
        </p>
    <?php elseif ($status === 'error'): ?>
        <p class="contact-form__message contact-form__message--error" role="alert">
            <?php esc_html_e('Une erreur est survenue. Merci de réessayer.', 'starter'); ?>
        </p>
    <?php endif; ?>

    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">

        <input type="hidden" name="action" value="starter_contact">
        <?php wp_nonce_field('starter_contact', 'starter_contact_nonce'); ?>

        <!-- Honeypot : caché aux humains, rempli par les bots -->
        <div class="contact-form__honeypot" aria-hidden="true">
            <label for="starter_website"><?php esc_html_e('Site web', 'starter'); ?></label>
            <input type="text" id="starter_website" name="starter_website" tabindex="-1" autocomplete="off">
        </div>

        <div class="contact-form__field">
            <label for="starter_name"><?php esc_html_e('Nom', 'starter'); ?></label>
            <input type="text" id="starter_name" name="starter_name" required autocomplete="name">
        </div>

        <div class="contact-form__field">
            <label for="starter_email"><?php esc_html_e('Email', 'starter'); ?></label>
            <input type="email" id="starter_email" name="starter_email" required autocomplete="email">
        </div>

        <div class="contact-form__field">
            <label for="starter_message"><?php esc_html_e('Message', 'starter'); ?></label>
            <textarea id="starter_message" name="starter_message" required></textarea>
        </div>

        <button type="submit" class="btn"><?php echo esc_html($button_text); ?></button>

    </form>
</div>