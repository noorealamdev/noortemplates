<?php
/**
 * Stats section.
 *
 * @package NoorBlocks
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Stats', 'noorblocks' ),
	'description'   => __( 'A dark strip with three key numbers.', 'noorblocks' ),
	'keywords'      => array( 'stats', 'numbers', 'counters' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noorblocks/container {"align":"full","style":{"color":{"background":"#0f172a","text":"#f8fafc"},"spacing":{"padding":{"top":"4rem","right":"1.5rem","bottom":"4rem","left":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-noorblocks-container alignfull has-text-color has-background" style="background-color:#0f172a;color:#f8fafc;padding-top:4rem;padding-right:1.5rem;padding-bottom:4rem;padding-left:1.5rem"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":3,"textAlign":"center"} -->
<h3 class="wp-block-noorblocks-heading has-text-align-center">10,000+</h3>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Active installs</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":3,"textAlign":"center"} -->
<h3 class="wp-block-noorblocks-heading has-text-align-center">50+</h3>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Sections and templates</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":3,"textAlign":"center"} -->
<h3 class="wp-block-noorblocks-heading has-text-align-center">4.9/5</h3>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Average rating</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:noorblocks/container -->',
);
