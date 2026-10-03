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
    'quantityLabel' => sanitize_text_field($attributes['quantityLabel'] ?? ''),
    'quantityUnit' => sanitize_text_field($attributes['quantityUnit'] ?? ''),
    'quantityMin' => (int) ($attributes['quantityMin'] ?? 1),
    'quantityMax' => (int) ($attributes['quantityMax'] ?? 1),
    'quantityPrice' => (float) ($attributes['quantityPrice'] ?? 0),
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
<div class="card mx-auto max-w-narrow">
    <?php if ($props['title']): ?>
        <h2 class="mb-4 text-2xl"><?php echo esc_html($props['title']); ?></h2>
    <?php endif; ?>

    <p class="mb-6 text-muted">
        <?php printf(
            esc_html__('À partir de %s € HT', 'starter'),
            esc_html(number_format_i18n($props['basePrice'])),
        ); ?>
    </p>

    <?php if (
        $props['quantityLabel'] &&
        $props['quantityPrice'] > 0 &&
        $props['quantityMax'] > $props['quantityMin']
    ): ?>
        <p class="mb-6">
            <?php printf(
                esc_html__('%1$s : prix de base pour %2$s %3$s, puis %4$s € HT par unité supplémentaire', 'starter'),
                esc_html($props['quantityLabel']),
                esc_html($props['quantityMin']),
                esc_html($props['quantityUnit']),
                esc_html(number_format_i18n($props['quantityPrice']))
            ); ?>
        </p>
    <?php endif; ?>

    <?php if ($props['options']): ?>
        <ul class="m-0 mb-6 list-none p-0">
            <?php foreach ($props['options'] as $option): ?>
                <li class="flex justify-between border-b border-line py-2">
                    <?php echo esc_html($option['label']); ?>
                    <span class="whitespace-nowrap text-muted">
                        + <?php echo esc_html(number_format_i18n($option['price'])); ?> €
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($props['ctaText'] && $props['ctaUrl']): ?>
        <a class="btn" href="<?php echo esc_url($props['ctaUrl']); ?>">
            <?php echo esc_html($props['ctaText']); ?>
        </a>
    <?php endif; ?>
</div>
<?php $fallback = ob_get_clean(); ?>

<div <?php echo get_block_wrapper_attributes(); ?>>
    <?php starter_island('simulator', $props, $fallback); ?>
</div>
