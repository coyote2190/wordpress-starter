<?php
/**
 * Composant Section Title
 *
 * @param array $args {
 *   @type string $overline Surtitre (optionnel)
 *   @type string $title    Titre principal
 *   @type string $subtitle Sous-titre (optionnel)
 *   @type string $level    Niveau de titre : h1, h2, h3... (défaut: h2)
 *   @type string $align    'left' ou 'center' (défaut: left)
 * }
 */

if (!defined('ABSPATH')) {
    exit();
}

$overline = $args['overline'] ?? '';
$title = $args['title'] ?? '';
$subtitle = $args['subtitle'] ?? '';
$level = $args['level'] ?? 'h2';
$align = $args['align'] ?? 'left';

// Sécurité : on n'accepte que des niveaux de titre valides
$level = in_array($level, ['h1', 'h2', 'h3', 'h4'], true) ? $level : 'h2';

$is_center = $align === 'center';

if (!$title && !$overline && !$subtitle) {
    return;
}
?>

<div class="mb-8<?php echo $is_center ? ' text-center' : ''; ?>">

    <?php if ($overline): ?>
        <p class="mb-2 text-sm tracking-wider text-muted uppercase">
            <?php echo esc_html($overline); ?>
        </p>
    <?php endif; ?>

    <?php if ($title): ?>
        <<?php echo $level; ?> class="text-[1.75rem] md:text-[2rem]">
            <?php echo esc_html($title); ?>
        </<?php echo $level; ?>>
    <?php endif; ?>

    <?php if ($subtitle): ?>
        <p class="max-w-narrow text-muted<?php echo $is_center
            ? ' mx-auto'
            : ''; ?>"><?php echo esc_html($subtitle); ?></p>
    <?php endif; ?>

</div>
