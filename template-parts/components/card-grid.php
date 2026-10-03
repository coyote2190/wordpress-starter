<?php
/**
 * Composant Card Grid — conteneur de cartes
 *
 * @param array $args {
 *   @type array $cards    Tableau de tableaux d'args pour le composant card
 *   @type int   $columns  Nombre de colonnes sur desktop : 2, 3 ou 4 (défaut: 3)
 * }
 */

if (!defined('ABSPATH')) {
    exit();
}

$cards = $args['cards'] ?? [];
$columns = $args['columns'] ?? 3;
$columns = in_array($columns, [2, 3, 4], true) ? $columns : 3;

if (empty($cards)) {
    return;
}

$cols_class = [
    2 => 'md:grid-cols-2',
    3 => 'md:grid-cols-2 lg:grid-cols-3',
    4 => 'md:grid-cols-2 lg:grid-cols-4',
][$columns];
?>

<div class="wrap grid gap-8 <?php echo esc_attr($cols_class); ?>">
    <?php foreach ($cards as $card): ?>
        <?php starter_component('card', $card); ?>
    <?php endforeach; ?>
</div>
