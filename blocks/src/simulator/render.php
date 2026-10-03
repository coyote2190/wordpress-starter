<?php
/**
 * Rendu front du bloc starter/simulator
 *
 * Le HTML de repli (prix de base + liste des options) est lisible sans JS
 * et par les moteurs de recherche, puis remplacé par l'îlot React.
 *
 * @var array $attributes Attributs du bloc
 */

if (!defined('ABSPATH')) {
    exit();
}

$props = [
    'title' => sanitize_text_field($attributes['title'] ?? ''),
    'basePrice' => (float) ($attributes['basePrice'] ?? 0),
    'vatRate' => (float) ($attributes['vatRate'] ?? 0),
    'ctaText' => sanitize_text_field($attributes['ctaText'] ?? ''),
    'ctaUrl' => esc_url_raw($attributes['ctaUrl'] ?? ''),
    'options' => array_values(
        array_map(
            fn($option) => [
                'label' => sanitize_text_field($option['label'] ?? ''),
                'price' => (float) ($option['price'] ?? 0),
            ],
            (array) ($attributes['options'] ?? []),
        ),
    ),
];

ob_start();
?>
<div class="simulator">
    <?php if ($props['title']): ?>
        <h2 class="simulator__title"><?php echo esc_html($props['title']); ?></h2>
    <?php endif; ?>

    <p class="simulator__base">
        <?php printf(
            esc_html__('À partir de %s € HT', 'starter'),
            esc_html(number_format_i18n($props['basePrice'])),
        ); ?>
    </p>

    <?php if ($props['options']): ?>
        <ul class="simulator__options">
            <?php foreach ($props['options'] as $option): ?>
                <li>
                    <?php echo esc_html($option['label']); ?>
                    <span class="simulator__price">
                        + <?php echo esc_html(number_format_i18n($option['price'])); ?> €
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($props['ctaText'] && $props['ctaUrl']): ?>
        <a class="btn simulator__cta" href="<?php echo esc_url($props['ctaUrl']); ?>">
            <?php echo esc_html($props['ctaText']); ?>
        </a>
    <?php endif; ?>
</div>
<?php $fallback = ob_get_clean(); ?>

<div <?php echo get_block_wrapper_attributes(); ?>>
    <?php starter_island('simulator', $props, $fallback); ?>
</div>
