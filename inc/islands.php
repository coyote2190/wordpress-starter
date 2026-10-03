<?php
/**
 * Îlots React — composants interactifs montés dans le thème PHP
 * Le montage côté JS est fait par assets/src/js/islands/index.jsx
 */

if (!defined('ABSPATH')) {
    exit();
}

/**
 * Affiche le conteneur d'un îlot React
 *
 * Le contenu de $fallback est affiché tant que React n'est pas monté
 * (sans JS, robots, chargement lent), puis remplacé par le composant.
 *
 * @param string $name     Clé du composant dans le registre JS (ex: 'simulator')
 * @param array  $props    Props passées au composant (sérialisées en JSON)
 * @param string $fallback HTML de repli, déjà échappé (optionnel)
 */
function starter_island($name, $props = [], $fallback = '') {
    printf(
        '<div class="island island--%1$s" data-island="%1$s" data-props="%2$s">%3$s</div>',
        esc_attr($name),
        esc_attr(wp_json_encode($props)),
        $fallback,
    );
}
