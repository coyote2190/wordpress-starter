<?php
/**
 * Custom Post Types réutilisables entre projets
 */

if (!defined('ABSPATH')) {
    exit();
}

/**
 * CPT "Service"
 */
function starter_register_service_cpt() {
    register_post_type('service', [
        'labels' => [
            'name' => __('Services', 'starter'),
            'singular_name' => __('Service', 'starter'),
            'add_new' => __('Ajouter', 'starter'),
            'add_new_item' => __('Ajouter un service', 'starter'),
            'edit_item' => __('Modifier le service', 'starter'),
            'all_items' => __('Tous les services', 'starter'),
            'not_found' => __('Aucun service', 'starter'),
        ],
        'public' => true,
        'hierarchical' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-portfolio',
        'menu_position' => 20,
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
        'rewrite' => ['slug' => 'services'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'starter_register_service_cpt');
