<?php
/**
 * Composant Card générique
 *
 * @param array $args {
 *   @type string $title    Titre
 *   @type string $text     Description (optionnel)
 *   @type int    $image_id ID de l'image (optionnel)
 *   @type string $url      Lien (optionnel)
 *   @type string $meta     Métadonnée affichée sous le titre (optionnel)
 *   @type string $size     Taille d'image WordPress (défaut: medium)
 * }
 */

if (!defined('ABSPATH')) {
    exit();
}

$title = $args['title'] ?? '';
$text = $args['text'] ?? '';
$image_id = $args['image_id'] ?? 0;
$url = $args['url'] ?? '';
$meta = $args['meta'] ?? '';
$size = $args['size'] ?? 'medium';

if (!$title && !$image_id) {
    return;
}
?>

<article class="card">

    <?php if ($image_id): ?>
        <div class="card__media">
            <?php if ($url): ?><a href="<?php echo esc_url(
    $url,
); ?>" tabindex="-1" aria-hidden="true"><?php endif; ?>
                <?php echo wp_get_attachment_image($image_id, $size, false, [
                    'class' => 'card__img',
                    'loading' => 'lazy',
                ]); ?>
            <?php if ($url): ?></a><?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card__body">

        <?php if ($meta): ?>
            <p class="card__meta"><?php echo esc_html($meta); ?></p>
        <?php endif; ?>

        <?php if ($title): ?>
            <h3 class="card__title">
                <?php if ($url): ?>
                    <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($title); ?></a>
                <?php else: ?>
                    <?php echo esc_html($title); ?>
                <?php endif; ?>
            </h3>
        <?php endif; ?>

        <?php if ($text): ?>
            <p class="card__text"><?php echo esc_html($text); ?></p>
        <?php endif; ?>

    </div>
</article>