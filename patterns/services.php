<?php
/**
 * Title: Grille de services
 * Slug: starter/services
 * Categories: starter
 * Description: Les 3 premiers services (CPT Service, triés par ordre) avec image, titre et extrait.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem","left":"1rem","right":"1rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:4rem;padding-right:1rem;padding-bottom:4rem;padding-left:1rem"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Nos services</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":10,"query":{"perPage":3,"pages":0,"offset":0,"postType":"service","order":"asc","orderBy":"menu_order","author":"","search":"","exclude":[],"sticky":"","inherit":false},"align":"wide"} -->
<div class="wp-block-query alignwide"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"textColor":"muted"} /-->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Aucun service publié pour le moment.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(get_post_type_archive_link('service') ?: home_url('/services/')); ?>">Voir tous les services</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
