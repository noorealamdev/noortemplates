<?php
/**
 * Testimonials section.
 *
 * @package NoorBlocks
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Testimonials', 'noorblocks' ),
	'description'   => __( 'A two-column testimonial section on a soft background.', 'noorblocks' ),
	'keywords'      => array( 'testimonials', 'reviews', 'quotes' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noorblocks/container {"align":"full","style":{"color":{"background":"#f1f5f9"},"spacing":{"padding":{"top":"5rem","right":"1.5rem","bottom":"5rem","left":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-noorblocks-container alignfull has-background" style="background-color:#f1f5f9;padding-top:5rem;padding-right:1.5rem;padding-bottom:5rem;padding-left:1.5rem"><!-- wp:noorblocks/heading {"textAlign":"center"} -->
<h2 class="wp-block-noorblocks-heading has-text-align-center">Loved by site builders</h2>
<!-- /wp:noorblocks/heading -->

<!-- wp:spacer {"height":"32px"} -->
<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p>"NoorBlocks cut our page-building time in half. The sections look great out of the box and every detail is easy to customize."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Sarah Mitchell</strong> — Freelance Designer</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p>"We rebuilt our entire marketing site with the page templates in a single afternoon. Clients love the results."</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>James Carter</strong> — Agency Owner</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:noorblocks/container -->',
);
