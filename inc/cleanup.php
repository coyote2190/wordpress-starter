<?php
/**
 * Nettoyage et optimisation de WordPress
 * Retire ce qui alourdit le front sans être utilisé sur un site vitrine
 */

if (!defined('ABSPATH')) {
    exit();
}

/**
 * Nettoyage du <head>
 */
function starter_clean_head() {
    // Version de WordPress (info exploitable par un attaquant)
    remove_action('wp_head', 'wp_generator');

    // Liens RSD / wlwmanifest (Windows Live Writer, obsolète)
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');

    // Liens de navigation entre articles (rarement utiles, requêtes en plus)
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    remove_action('wp_head', 'start_post_rel_link', 10);
    remove_action('wp_head', 'parent_post_rel_link', 10);

    // Shortlink (?p=123)
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
}
add_action('init', 'starter_clean_head');

/**
 * Désactivation des emojis
 * Charge ~15 Ko de JS + une requête pour un usage quasi nul
 */
function starter_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    add_filter('tiny_mce_plugins', function ($plugins) {
        return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
    });
}
add_action('init', 'starter_disable_emojis');

/**
 * Désactivation des embeds oEmbed
 * Charge wp-embed.js sur toutes les pages
 * ⚠️ Commenter si le client colle des URLs YouTube/Twitter dans l'éditeur
 */
function starter_disable_embeds() {
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');

    add_action('wp_footer', function () {
        wp_deregister_script('wp-embed');
    });
}
add_action('init', 'starter_disable_embeds');

/**
 * Retire le CSS des blocs si Gutenberg n'est pas utilisé sur le front
 * ⚠️ Commenter si le client utilise les blocs Gutenberg (souvent le cas)
 */
// function starter_remove_block_css()
// {
//
