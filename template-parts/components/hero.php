<?php
/**
 * Composant Hero réutilisable
 *
 * @param array $args {
 *   @type string $title       Titre principal
 *   @type string $subtitle    Sous-titre (optionnel)
 *   @type string $button_text Texte du bouton (optionnel)
 *   @type string $button_url  URL du bouton (optionnel)
 *   @type string $image       URL de l'image de fond (optionnel)
 * }
 */

if (!defined('ABSPATH')) {
    exit;
}

$title       = $args['title'] ?? '';
$subtitle    = $args['subtitle'] ?? '';
$button_text = $args['button_text'] ?? '';
$button_url  = $args['button_url'] ?? '';
$image       = $args['image'] ?? '';
?>

<section class="hero"<?php echo $image ? ' style="background-image: url(' . esc_url($image) . ');"' : ''; ?>>
    <div class="hero__content">
        <?php if ($title) : ?>
            <h1 class="hero__title"><?php echo esc_html($title); ?></h1>
        <?php endif; ?>

        <?php if ($subtitle) : ?>
            <p class="hero__subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>

        <?php if ($button_text && $button_url) : ?>
            <a href="<?php echo esc_url($button_url); ?>" class="hero__button">
                <?php echo esc_html($button_text); ?>
            </a>
        <?php endif; ?>
    </div>
</section>