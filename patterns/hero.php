<?php
/**
 * Title: Bannière d'accueil
 * Slug: starter/hero
 * Categories: starter, banner
 * Description: Grand titre, accroche et bouton sur fond gris, pleine largeur.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"6rem","bottom":"6rem","left":"1rem","right":"1rem"}}},"backgroundColor":"line","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-line-background-color has-background" style="padding-top:6rem;padding-right:1rem;padding-bottom:6rem;padding-left:1rem"><!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="wp-block-heading has-text-align-center">Un titre clair sur ce que vous faites</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"muted"} -->
<p class="has-text-align-center has-muted-color has-text-color">Une phrase d'accroche qui explique en quelques mots ce que vous apportez à vos clients.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(home_url('/contact/')); ?>">Nous contacter</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
