<?php
/**
 * Title: Questions fréquentes
 * Slug: starter/section-faq
 * Categories: starter-sections
 * Keywords: faq, questions, réponses, accordéon
 * Viewport Width: 1000
 * Description: Titre et questions dépliables (blocs « Détails »), lisibles par Google.
 *
 * @package Starter
 */

defined( 'ABSPATH' ) || exit;

$starter_faq = array(
	__( 'Combien de temps faut-il compter ?', 'starter' ) => __( 'Donnez un ordre de grandeur et ce dont il dépend. Les visiteurs préfèrent une fourchette honnête à « ça dépend ».', 'starter' ),
	__( 'Combien ça coûte ?', 'starter' )                 => __( 'Un prix de départ ou une fourchette, et ce qui est compris. Proposez un devis gratuit pour le reste.', 'starter' ),
	__( 'Comment se passe le premier rendez-vous ?', 'starter' ) => __( 'Expliquez le déroulé : durée, sur place ou en visio, ce qu\'il faut préparer.', 'starter' ),
	__( 'Que se passe-t-il après la livraison ?', 'starter' ) => __( 'Suivi, garantie, maintenance : rassurez sur l\'après.', 'starter' ),
);
?>
<!-- wp:group {"className":"faq","layout":{"type":"constrained"}} -->
<div class="wp-block-group faq">
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Questions fréquentes', 'starter' ); ?></h2>
<!-- /wp:heading -->
<?php foreach ( $starter_faq as $starter_question => $starter_answer ) : ?>

<!-- wp:details -->
<details class="wp-block-details"><summary><?php echo esc_html( $starter_question ); ?></summary>
<!-- wp:paragraph -->
<p><?php echo esc_html( $starter_answer ); ?></p>
<!-- /wp:paragraph -->
</details>
<!-- /wp:details -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
