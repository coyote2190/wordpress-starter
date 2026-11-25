<?php
/**
 * Listes : catégorie, étiquette, auteur, date, archive d'un type de contenu.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="container py-12">
	<header class="mb-10">
		<h1 class="text-4xl font-bold"><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="prose mt-4">', '</div>' ); ?>
	</header>

	<?php get_template_part( 'template-parts/loop' ); ?>
</div>

<?php
get_footer();
