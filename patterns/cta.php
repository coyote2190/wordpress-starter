<?php
/**
 * Title: Appel à l'action
 * Slug: starter/cta
 * Categories: starter, call-to-action
 * Description: Titre, texte et bouton sur fond sombre, pleine largeur.
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem","left":"1rem","right":"1rem"}}},"backgroundColor":"ink","textColor":"canvas","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-canvas-color has-ink-background-color has-text-color has-background" style="padding-top:4rem;padding-right:1rem;padding-bottom:4rem;padding-left:1rem"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Un projet en tête ?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Parlons-en : nous vous répondons sous 24 h.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url(home_url('/contact/')); ?>">Demander un devis</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
