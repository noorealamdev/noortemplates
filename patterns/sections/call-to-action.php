<?php
/**
 * Call to Action section.
 *
 * @package NoorBlocks
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Call to Action', 'noorblocks' ),
	'description'   => __( 'A dark call-to-action strip with heading, supporting text and button.', 'noorblocks' ),
	'keywords'      => array( 'cta', 'call to action', 'signup' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noorblocks/container {"align":"full","style":{"color":{"background":"#0f172a","text":"#f8fafc"},"spacing":{"padding":{"top":"4rem","right":"1.5rem","bottom":"4rem","left":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-noorblocks-container alignfull has-text-color has-background" style="background-color:#0f172a;color:#f8fafc;padding-top:4rem;padding-right:1.5rem;padding-bottom:4rem;padding-left:1.5rem"><!-- wp:noorblocks/heading {"level":3,"textAlign":"center"} -->
<h3 class="wp-block-noorblocks-heading has-text-align-center">Ready to build something great?</h3>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Join thousands of site builders already using NoorBlocks.</p>
<!-- /wp:paragraph -->

<!-- wp:noorblocks/button {"url":"#","textAlign":"center"} -->
<div class="wp-block-noorblocks-button has-text-align-center"><a class="noorblocks-button__link" href="#">Get Started Now</a></div>
<!-- /wp:noorblocks/button --></div>
<!-- /wp:noorblocks/container -->',
);
