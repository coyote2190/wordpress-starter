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
    exit();
}

$title = $args['title'] ?? '';
$subtitle = $args['subtitle'] ?? '';
$button_text = $args['button_text'] ?? '';
$button_url = $args['button_url'] ?? '';
$image = $args['image'] ?? '';
?>

<section class="bg-cover bg-center py-16 text-center"<?php echo $image
    ? ' style="background-image: url(' . esc_url($image) . ');"'
    : ''; ?>>
    <div class="wrap-narrow">
        <?php if ($title): ?>
            <h1 class="text-[2rem] md:text-[2.5rem]"><?php echo esc_html($title); ?></h1>
        <?php endif; ?>

        <?php if ($subtitle): ?>
            <p class="text-muted mb-6"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>

        <?php if ($button_text && $button_url): ?>
            <a href="<?php echo esc_url($button_url); ?>" class="btn">
                <?php echo esc_html($button_text); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
