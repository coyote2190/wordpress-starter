<?php
/**
 * Composant Service Card
 *
 * @param array $args {
 *   @type string $title       Titre du service
 *   @type string $description Description courte
 *   @type int    $image_id    ID de l'image (optionnel)
 *   @type string $url         Lien vers la page détail (optionnel)
 *   @type array  $icon        Tableau image ACF (optionnel)
 *   @type string $price       Prix formaté (optionnel)
 *   @type string $duration    Durée (optionnel)
 *   @type string $cta_url     Lien externe (optionnel)
 * }
 */

if (!defined('ABSPATH')) {
    exit();
}

$title = $args['title'] ?? '';
$description = $args['description'] ?? '';
$image_id = $args['image_id'] ?? 0;
$url = $args['url'] ?? '';
$icon = $args['icon'] ?? null;
$price = $args['price'] ?? '';
$duration = $args['duration'] ?? '';
$cta_url = $args['cta_url'] ?? '';
?>

<article class="service-card">

    <?php if ($icon): ?>
        <div class="service-card__icon">
            <?php echo wp_get_attachment_image($icon['ID'], 'thumbnail'); ?>
        </div>
    <?php elseif ($image_id): ?>
        <div class="service-card__image">
            <?php echo wp_get_attachment_image($image_id, 'medium', false, [
                'class' => 'service-card__img',
                'loading' => 'lazy',
            ]); ?>
        </div>
    <?php endif; ?>

    <?php if ($title): ?>
        <h2 class="service-card__title">
            <?php if ($url): ?>
                <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($title); ?></a>
            <?php else: ?>
                <?php echo esc_html($title); ?>
            <?php endif; ?>
        </h2>
    <?php endif; ?>

    <?php if ($price || $duration): ?>
        <ul class="service-card__meta">
            <?php if ($price): ?>
                <li><?php echo esc_html($price); ?></li>
            <?php endif; ?>
            <?php if ($duration): ?>
                <li><?php echo esc_html($duration); ?></li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>

    <?php if ($description): ?>
        <p class="service-card__description"><?php echo esc_html($description); ?></p>
    <?php endif; ?>

    <?php if ($cta_url): ?>
        <a href="<?php echo esc_url($cta_url); ?>" class="btn service-card__cta">
            <?php esc_html_e('En savoir plus', 'starter'); ?>
        </a>
    <?php endif; ?>

</article>