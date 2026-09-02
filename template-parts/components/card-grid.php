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
?>

<div class="card-grid card-grid--<?php echo esc_attr($columns); ?>">
    <?php foreach ($cards as $card): ?>
        <?php starter_component('card', $card); ?>
    <?php endforeach; ?>
</div>