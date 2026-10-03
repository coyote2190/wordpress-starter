<?php
/**
 * Rendu du bloc starter/contact-form
 * Réutilise le composant PHP (template-parts/components/contact-form.php)
 * et son traitement (inc/contact-form.php).
 *
 * @var array $attributes Attributs du bloc
 */

if (!defined('ABSPATH')) {
    exit();
} ?>

<div <?php echo get_block_wrapper_attributes(); ?>>
    <?php starter_component('contact-form', [
        'title' => $attributes['title'] ?? '',
        'button_text' => $attributes['buttonText'] ?? '' ?: __('Envoyer', 'starter'),
    ]); ?>
</div>
