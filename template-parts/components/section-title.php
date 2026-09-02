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

if (!$title && !$overline && !$subtitle) {
    return;
}
?>

<div class="section-title section-title--<?php echo esc_attr($align); ?>">

    <?php if ($overline): ?>
        <p class="section-title__overline"><?php echo esc_html($overline); ?></p>
    <?php endif; ?>

    <?php if ($title): ?>
        <<?php echo $level; ?> class="section-title__heading">
            <?php echo esc_html($title); ?>
        </<?php echo $level; ?>>
    <?php endif; ?>

    <?php if ($subtitle): ?>
        <p class="section-title__subtitle"><?php echo esc_html($subtitle); ?></p>
    <?php endif; ?>

</div>