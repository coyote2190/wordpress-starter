<?php
/**
 * Chargement des assets CSS/JS compilés par Vite
 */

if (!defined('ABSPATH')) {
    exit;
}

function starter_enqueue_assets() {
    $dist_path = STARTER_THEME_DIR . '/assets/dist';
    $dist_uri  = STARTER_THEME_URI . '/assets/dist';

    $css_file = $dist_path . '/main.css';
    $js_file  = $dist_path . '/main.js';

    if (file_exists($css_file)) {
        wp_enqueue_style(
            'starter-main',
            $dist_uri . '/main.css',
            [],
            filemtime($css_file)
        );
    }

    if (file_exists($js_file)) {
        wp_enqueue_script(
            'starter-main',
            $dist_uri . '/main.js',
            [],
            filemtime($js_file),
            true // charge en footer
        );
    }
}
add_action('wp_enqueue_scripts', 'starter_enqueue_assets');