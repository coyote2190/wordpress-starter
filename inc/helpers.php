<?php

/**
 * Fonctions utilitaires réutilisables entre projets
 */

if (!defined('ABSPATH')) {
    exit();
}

/**
 * Wrapper SCF/ACF sécurisé — retourne null si aucun des deux plugins n'est actif
 */
function starter_field($name, $post_id = false) {
    return function_exists('get_field') ? get_field($name, $post_id) : null;
}
