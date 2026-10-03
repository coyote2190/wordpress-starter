<?php
/**
 * Rendu front du bloc starter/faq
 *
 * <details> natif : repliable sans JavaScript, accessible au clavier,
 * contenu lisible par les moteurs de recherche.
 *
 * @var array $attributes Attributs du bloc
 */

if (!defined('ABSPATH')) {
    exit();
}

$title = $attributes['title'] ?? '';
$items = array_filter(
    (array) ($attributes['items'] ?? []),
    fn($item) => !empty($item['question']) && !empty($item['answer']),
);

if (!$items) {
    return;
}
?>

<div <?php echo get_block_wrapper_attributes(['class' => 'mx-auto max-w-narrow']); ?>>

    <?php if ($title): ?>
        <h2 class="mb-6 text-center text-[1.75rem] md:text-[2rem]"><?php echo wp_kses_post(
            $title,
        ); ?></h2>
    <?php endif; ?>

    <div class="rounded-ui border-line border">
        <?php foreach ($items as $item): ?>
            <details class="group border-line not-first:border-t">
                <summary class="hover:bg-line/50 flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3 font-medium [&::-webkit-details-marker]:hidden">
                    <?php echo wp_kses_post($item['question']); ?>
                    <svg class="shrink-0 transition-transform duration-150 group-open:rotate-45" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-linecap="square" aria-hidden="true">
                        <path d="M1.5 8h13M8 14.5v-13" />
                    </svg>
                </summary>
                <div class="text-muted px-4 pb-4">
                    <?php echo wp_kses_post($item['answer']); ?>
                </div>
            </details>
        <?php endforeach; ?>
    </div>

</div>

<?php if (!empty($attributes['schema'])):
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(
            fn($item) => [
                '@type' => 'Question',
                'name' => wp_strip_all_tags($item['question']),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => wp_strip_all_tags($item['answer']),
                ],
            ],
            array_values($items),
        ),
    ]; ?>
    <script type="application/ld+json"><?php echo wp_json_encode(
        $schema,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG,
    ); ?></script>
<?php
endif; ?>
