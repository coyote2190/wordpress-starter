<?php
/**
 * Page d'accueil (prioritaire sur tous les autres gabarits pour l'accueil).
 *
 * Réglages > Lecture :
 * - « Une page statique » → affiche le contenu Gutenberg de cette page, sans titre
 *   (les sections de l'accueil sont des blocs, souvent en pleine largeur) ;
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
	<div class="container">
		<?php // Texte en colonne lisible ; les blocs alignfull / alignwide en sortent (components.css). ?>
		<div class="entry-content prose mx-auto max-w-content">
			<?php the_content(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
