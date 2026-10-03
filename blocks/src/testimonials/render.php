<?php
/**
 * Rendu front du bloc starter/testimonials
 *
 * @var array $attributes Attributs du bloc
 */

if (!defined('ABSPATH')) {
    exit();
}

// Même table que columns.js (classes complètes pour Tailwind)
$columns_classes = [
    1 => 'md:grid-cols-1',
    2 => 'md:grid-cols-2',
    3 => 'md:grid-cols-3',
];

$title = $attributes['title'] ?? '';
$columns = $columns_classes[(int) ($attributes['columns'] ?? 3)] ?? $columns_classes[3];
$items = array_filter((array) ($attributes['items'] ?? []), fn($item) => !empty($item['quote']));

if (!$items) {
    return;
}
?>

<div <?php echo get_block_wrapper_attributes(); ?>>

    <?php if ($title): ?>
        <h2 class="mb-8 text-center text-[1.75rem] md:text-[2rem]"><?php echo wp_kses_post(
            $title,
        ); ?></h2>
    <?php endif; ?>

    <div class="grid gap-6 <?php echo esc_attr($columns); ?>">
        <?php foreach ($items as $item): ?>
            <figure class="card m-0 flex flex-col">
                <blockquote class="m-0 mb-4 flex-1 border-0 p-0 text-lg">
                    <?php echo wp_kses_post($item['quote']); ?>
                </blockquote>
                <figcaption>
                    <?php if (!empty($item['name'])): ?>
                        <p class="m-0 font-medium"><?php echo wp_kses_post($item['name']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($item['role'])): ?>
                        <p class="text-muted m-0 text-sm"><?php echo wp_kses_post(
                            $item['role'],
                        ); ?></p>
                    <?php endif; ?>
                </figcaption>
            </figure>
        <?php endforeach; ?>
    </div>

</div>
