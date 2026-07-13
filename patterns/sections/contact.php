<?php
/**
 * Contact section.
 *
 * @package NoorBlocks
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Contact', 'noorblocks' ),
	'description'   => __( 'A centered contact section with email, phone and office details.', 'noorblocks' ),
	'keywords'      => array( 'contact', 'email', 'phone', 'address' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noorblocks/container {"align":"full","style":{"spacing":{"padding":{"top":"5rem","right":"1.5rem","bottom":"5rem","left":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-noorblocks-container alignfull" style="padding-top:5rem;padding-right:1.5rem;padding-bottom:5rem;padding-left:1.5rem"><!-- wp:noorblocks/heading {"textAlign":"center"} -->
<h2 class="wp-block-noorblocks-heading has-text-align-center">Get in touch</h2>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Questions, feedback or a project in mind? We would love to hear from you.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"32px"} -->
<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":4,"textAlign":"center"} -->
<h4 class="wp-block-noorblocks-heading has-text-align-center">Email</h4>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">hello@example.com</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":4,"textAlign":"center"} -->
<h4 class="wp-block-noorblocks-heading has-text-align-center">Phone</h4>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">+1 (555) 123-4567</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":4,"textAlign":"center"} -->
<h4 class="wp-block-noorblocks-heading has-text-align-center">Office</h4>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">123 Main Street, Springfield</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:noorblocks/container -->',
);
