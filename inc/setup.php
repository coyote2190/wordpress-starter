<?php

/**
 * Configuration de base du thème
 */

if (!defined('ABSPATH')) {
    exit;
}

function starter_theme_setup()
{
    // Traductions
    load_theme_textdomain('starter', STARTER_THEME_DIR . '/languages');

    // Support des fonctionnalités WordPress courantes
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
    ]);
    add_theme_support('automatic-feed-links');
    add_theme_support('customize-selective-refresh-widgets');

    // Styles du thème dans l'éditeur (aperçu fidèle des blocs)
    // ⚠️ Utilise le build : lancer `npm run build` pour mettre à jour
    add_theme_support('editor-styles');
    add_editor_style('assets/dist/main.css');

    // Menus réutilisables — à ajuster par client si besoin
    register_nav_menus([
        'primary' => __('Menu principal', 'starter'),
        'footer'  => __('Menu footer', 'starter'),
    ]);
}
add_action('after_setup_theme', 'starter_theme_setup');
