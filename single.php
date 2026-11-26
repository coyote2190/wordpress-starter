<?php
/**
 * Article seul (et tout type de contenu sans gabarit single-{type}.php, ex. réalisations).
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$starter_list    = starter_post_type_list( (string) get_post_type() );
	$starter_term    = starter_primary_term();
	$starter_is_post = 'post' === get_post_type();
	?>
	<article <?php post_class( 'container pb-24' ); ?>>
		<header class="flex flex-col gap-6 items-center max-w-4xl md:pt-16 mx-auto pb-12 pt-10 text-center">
			<?php if ( $starter_list ) : ?>
				<a class="font-medium gap-2 hover:text-dark inline-flex items-center text-[15px] text-muted transition" href="<?php echo esc_url( $starter_list['url'] ); ?>">
					<?php echo starter_icon( 'arrow-left', 'size-4' ); ?>
					<?php echo esc_html( $starter_list['label'] ); ?>
				</a>
			<?php endif; ?>

			<h1 class="leading-[1] text-[clamp(2.5rem,5.5vw,4.75rem)] text-balance tracking-[-.045em]"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<p class="leading-normal max-w-2xl text-[1.1875rem] text-muted"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>

			<div class="flex flex-wrap gap-x-3 gap-y-2 items-center justify-center text-muted text-sm">
				<?php if ( $starter_term ) : ?>
					<a class="bg-secondary font-semibold px-3 py-1 rounded-full text-[13px] text-dark" href="<?php echo esc_url( get_term_link( $starter_term ) ); ?>"><?php echo esc_html( $starter_term->name ); ?></a>
				<?php endif; ?>
				<?php if ( $starter_is_post ) : ?>
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<span aria-hidden="true">·</span>
					<?php /* translators: %d : minutes. */ ?>
					<span><?php echo esc_html( sprintf( _n( '%d min de lecture', '%d min de lecture', starter_reading_time(), 'starter' ), starter_reading_time() ) ); ?></span>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="mb-16">
				<?php the_post_thumbnail( 'full', array( 'class' => 'bg-secondary h-[clamp(16rem,42vw,34rem)] object-cover rounded-[2rem] w-full', 'sizes' => '(min-width: 80rem) 80rem, 100vw' ) ); ?>
			</figure>
		<?php endif; ?>

		<div class="entry-content max-w-content mx-auto prose prose-lg">
			<?php the_content(); ?>
		</div>

		<?php // Mise en page sur un conteneur : WordPress n'accepte qu'UNE classe dans 'class' (sanitize_html_class). ?>
		<div class="max-w-content mt-20 mx-auto">
		<?php
		the_post_navigation(
			array(
				'class'     => 'post-navigation',
				'prev_text' => '<span class="font-medium font-sans text-[13px] text-soft tracking-[.06em] uppercase">' . esc_html__( 'Précédent', 'starter' ) . '</span>%title',
				'next_text' => '<span class="font-medium font-sans text-[13px] text-soft tracking-[.06em] uppercase">' . esc_html__( 'Suivant', 'starter' ) . '</span>%title',
			)
		);
		?>
		</div>
	</article>

	<?php
	// À lire aussi : les 3 contenus les plus récents du même type.
	$starter_related = new WP_Query(
		array(
			'post_type'           => get_post_type(),
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $starter_related->have_posts() ) :
		?>
		<section class="border-line border-t py-24">
			<div class="container">
				<div class="flex flex-wrap gap-6 items-end justify-between mb-12">
					<h2 class="leading-none text-[clamp(2.25rem,4.4vw,3.75rem)] tracking-[-.04em]">
						<?php echo $starter_is_post ? esc_html__( 'À lire aussi', 'starter' ) : esc_html__( 'Autres projets', 'starter' ); ?>
					</h2>
					<?php if ( $starter_list ) : ?>
						<?php echo starter_button( array( 'url' => $starter_list['url'], 'title' => __( 'Tout voir', 'starter' ) ), 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
				</div>
				<div class="gap-x-6 gap-y-12 grid lg:grid-cols-3 sm:grid-cols-2">
					<?php
					while ( $starter_related->have_posts() ) :
						$starter_related->the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
