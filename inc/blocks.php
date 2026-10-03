<?php
/**
 * Blocs Gutenberg du thème
 * Sources : blocks/src/<nom>/ — compilés par `npm run build:blocks` dans blocks/build/
 */

if (!defined('ABSPATH')) {
    exit();
}

/**
 * Enregistre automatiquement chaque bloc compilé (un dossier = un bloc)
 */
function starter_register_blocks() {
    foreach (glob(STARTER_THEME_DIR . '/blocks/build/*/block.json') as $block_json) {
        register_block_type(dirname($block_json));
    }
}
add_action('init', 'starter_register_blocks');
