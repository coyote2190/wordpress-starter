<?php

/**
 * Chargement des assets — dev server Vite (HMR) ou build de production
 */

if (!defined('ABSPATH')) {
    exit;
}

define('STARTER_HOT_FILE', STARTER_THEME_DIR . '/assets/dist/.vite-hot');

/**
 * Le dev server Vite tourne-t-il ?
 * Détecté via le fichier flag créé/supprimé par le plugin wpHotFile de vite.config.js
 */
function starter_vite_is_running()
{
    return file_exists(STARTER_HOT_FILE);
}

/**
 * URL du dev server Vite (ex: http://localhost:5173), vide si non lancé
 */
function starter_vite_dev_url()
{
    return starter_vite_is_running()
        ? trim(file_get_contents(STARTER_HOT_FILE))
        : '';
}

/**
 * Point d'entrée : bascule automatique dev / production
 */
function starter_enqueue_assets()
{
    if (starter_vite_is_running()) {
        starter_enqueue_dev_assets();
    } else {
        starter_enqueue_build_assets();
    }
}
add_action('wp_enqueue_scripts', 'starter_enqueue_assets');

/**
 * Mode dev : client Vite + entrée source (le SCSS est injecté par le JS)
 */
function starter_enqueue_dev_assets()
{
    $dev_url = starter_vite_dev_url();

    wp_enqueue_script(
        'starter-vite-client',
        $dev_url . '/@vite/client',
        [],
        null,
        false
    );

    wp_enqueue_script(
        'starter-main',
        $dev_url . '/assets/src/js/main.js',
        [],
        null,
        true
    );
}

/**
 * Mode production : fichiers compilés dans assets/dist/
 */
function starter_enqueue_build_assets()
{
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
            true
        );
    }
}

/**
 * Les scripts Vite doivent être servis en type="module"
 */
function starter_module_script_tag($tag, $handle, $src)
{
    if (!starter_vite_is_running()) {
        return $tag;
    }

    if (in_array($handle, ['starter-vite-client', 'starter-main'], true)) {
        return '<script type="module" src="' . esc_url($src) . '"></script>' . "\n";
    }

    return $tag;
}
add_filter('script_loader_tag', 'starter_module_script_tag', 10, 3);
