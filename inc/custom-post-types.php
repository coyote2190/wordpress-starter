<?php
/**
 * Custom Post Types réutilisables entre projets
 *
 * - CPT « Plat » classé par la catégorie « Type de plat »
 *   (Entrées, Plats, Desserts, Boissons).
 * - CPT « Formule » : menu à prix fixe (entrée + plat + dessert) qui pioche dans les plats.
 * - Les champs sont dans acf-json/ (group_starter_plat.json, group_starter_formule.json).
 */

if (!defined('ABSPATH')) {
    exit();
}

/**
 * CPT « Plat » : une fiche par plat, liste publique sur /menu
 */
function starter_register_plat_cpt() {
    register_post_type('plat', [
        'labels' => [
            'name' => __('Plats', 'starter'),
            'singular_name' => __('Plat', 'starter'),
            'add_new' => __('Ajouter', 'starter'),
            'add_new_item' => __('Ajouter un plat', 'starter'),
            'edit_item' => __('Modifier le plat', 'starter'),
            'all_items' => __('Tous les plats', 'starter'),
            'not_found' => __('Aucun plat', 'starter'),
        ],
        'public' => true,
        'has_archive' => false, // la liste est la page « La carte » (page-templates/page-carte.php)
        'menu_icon' => 'dashicons-food',
        'menu_position' => 20,
        'supports' => ['title', 'editor', 'thumbnail', 'page-attributes'],
        'rewrite' => ['slug' => 'plat'],
        'show_in_rest' => true,
    ]);

    register_taxonomy('type_plat', 'plat', [
        'labels' => [
            'name' => __('Types de plat', 'starter'),
            'singular_name' => __('Type de plat', 'starter'),
            'add_new_item' => __('Ajouter un type', 'starter'),
            'edit_item' => __('Modifier le type', 'starter'),
        ],
        'hierarchical' => true,
        'public' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'type-de-plat'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'starter_register_plat_cpt');

/**
 * CPT « Formule » : affichée sur la page « La carte », pas de page à elle
 */
function starter_register_formule_cpt() {
    register_post_type('formule', [
        'labels' => [
            'name' => __('Formules', 'starter'),
            'singular_name' => __('Formule', 'starter'),
            'add_new' => __('Ajouter', 'starter'),
            'add_new_item' => __('Ajouter une formule', 'starter'),
            'edit_item' => __('Modifier la formule', 'starter'),
            'all_items' => __('Toutes les formules', 'starter'),
            'not_found' => __('Aucune formule', 'starter'),
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-list-view',
        'menu_position' => 21,
        'supports' => ['title', 'editor', 'page-attributes'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'starter_register_formule_cpt');

/**
 * Crée une seule fois les types par défaut (dans cet ordre = ordre d'affichage)
 */
function starter_create_default_plat_types() {
    if (get_option('starter_plat_types_created')) {
        return;
    }

    foreach (['Entrées', 'Plats', 'Desserts', 'Boissons'] as $name) {
        if (!term_exists($name, 'type_plat')) {
            wp_insert_term($name, 'type_plat');
        }
    }

    update_option('starter_plat_types_created', 1);
}
add_action('init', 'starter_create_default_plat_types', 20);
