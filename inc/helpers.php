<?php
/**
 * Fonctions utilitaires réutilisables entre projets
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Affiche un composant depuis template-parts/components/
 *
 * @param string $name Nom du composant (ex: 'hero')
 * @param array  $args Données passées au composant
 */
function starter_component($name, $args = []) {
    get_template_part('template-parts/components/' . $name, null, $args);
}

/**
 * Retourne le HTML d'un composant au lieu de l'afficher
 * Utile pour composer des blocs ou passer du markup en argument
 *
 * @param string $name
 * @param array  $args
 * @return string
 */
function starter_get_component($name, $args = []) {
    ob_start();
    starter_component($name, $args);
    return ob_get_clean();
}