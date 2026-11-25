<?php
/**
 * Page d'accueil (prioritaire sur tous les autres gabarits pour l'accueil).
 *
 * Réglages > Lecture :
 * - « Une page statique » → affiche le contenu Gutenberg de cette page, en pleine largeur
 *   (les sections de l'accueil sont des blocs) ;
 * - « Les derniers articles » → on retombe sur la liste d'articles (index.php).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

if ( 'posts' === get_option( 'show_on_front' ) ) {
	get_template_part( 'index' );
	return;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;

get_footer();
