<?php
/**
 * Page d'accueil
 *
 * - Réglages > Lecture > « Une page statique » : la page choisie est rendue
 *   en pleine largeur avec ses blocs Gutenberg (voir page-blocks.php).
 * - « Les derniers articles » : on retombe sur la hiérarchie classique (home.php / index.php).
 */

if (get_option('show_on_front') !== 'page') {
    require get_home_template() ?: get_index_template();
    return;
}

require get_theme_file_path('page-templates/page-blocks.php');
