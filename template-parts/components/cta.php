<?php
/**
 * Composant CTA (appel à l'action)
 *
 * @param array $args {
 *   @type string $title       Titre
 *   @type string $text        Texte descriptif (optionnel)
 *   @type string $button_text Texte du bouton
 *   @type string $button_url  URL du bouton
 *   @type bool   $external    Ouvre dans un nouvel onglet (défaut: false)
 * }
 */

if (!defined('ABSPATH')) {
    exit();
}

$title = $args['title'] ?? '';
$text = $args['text'] ?? '';
$button_text = $args['button_text'] ?? '';
$button_url = $args['button_url'] ?? '';
$external = $args['external'] ?? false;

if (!$title && !$button_text) {
    return;
}
?>

<section class="cta">
    <div class="cta__inner">

        <?php if ($title): ?>
            <h2 class="cta__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <?php if ($text): ?>
            <p class="cta__text"><?php echo esc_html($text); ?></p>
        <?php endif; ?>

        <?php if ($button_text && $button_url): ?>
            
                href="<?php echo esc_url($button_url); ?>"
                class="btn cta__button"
                <?php if ($external): ?>target="_blank" rel="noopener"<?php endif; ?>
            >
                <?php echo esc_html($button_text); ?>
            </a>
        <?php endif; ?>

    </div>
</section>