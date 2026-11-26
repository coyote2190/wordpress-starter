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
		<header class="mx-auto flex max-w-4xl flex-col items-center gap-6 pt-10 pb-12 text-center md:pt-16">
			<?php if ( $starter_list ) : ?>
				<a class="inline-flex items-center gap-2 text-[15px] font-medium text-muted transition hover:text-dark" href="<?php echo esc_url( $starter_list['url'] ); ?>">
					<?php echo starter_icon( 'arrow-left', 'size-4' ); ?>
					<?php echo esc_html( $starter_list['label'] ); ?>
				</a>
			<?php endif; ?>

			<h1 class="text-[clamp(2.5rem,5.5vw,4.75rem)] leading-[1] tracking-[-.045em] text-balance"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<p class="max-w-2xl text-[1.1875rem] leading-normal text-muted"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>

			<div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-sm text-muted">
				<?php if ( $starter_term ) : ?>
					<a class="rounded-full bg-secondary px-3 py-1 text-[13px] font-semibold text-dark" href="<?php echo esc_url( get_term_link( $starter_term ) ); ?>"><?php echo esc_html( $starter_term->name ); ?></a>
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
				<?php the_post_thumbnail( 'full', array( 'class' => 'h-[clamp(16rem,42vw,34rem)] w-full rounded-[2rem] bg-secondary object-cover', 'sizes' => '(min-width: 80rem) 80rem, 100vw' ) ); ?>
			</figure>
		<?php endif; ?>

		<div class="entry-content prose prose-lg mx-auto max-w-content">
			<?php the_content(); ?>
		</div>

		<?php // Mise en page sur un conteneur : WordPress n'accepte qu'UNE classe dans 'class' (sanitize_html_class). ?>
		<div class="mx-auto mt-20 max-w-content">
		<?php
		the_post_navigation(
			array(
				'class'     => 'post-navigation',
				'prev_text' => '<span class="font-sans text-[13px] font-medium tracking-[.06em] text-soft uppercase">' . esc_html__( 'Précédent', 'starter' ) . '</span>%title',
				'next_text' => '<span class="font-sans text-[13px] font-medium tracking-[.06em] text-soft uppercase">' . esc_html__( 'Suivant', 'starter' ) . '</span>%title',
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
		<section class="border-t border-line py-24">
			<div class="container">
				<div class="mb-12 flex flex-wrap items-end justify-between gap-6">
					<h2 class="text-[clamp(2.25rem,4.4vw,3.75rem)] leading-none tracking-[-.04em]">
						<?php echo $starter_is_post ? esc_html__( 'À lire aussi', 'starter' ) : esc_html__( 'Autres projets', 'starter' ); ?>
					</h2>
					<?php if ( $starter_list ) : ?>
						<?php echo starter_button( array( 'url' => $starter_list['url'], 'title' => __( 'Tout voir', 'starter' ) ), 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<?php endif; ?>
				</div>
				<div class="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
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
