<?php
/**
 * About intro section.
 *
 * @package NoorBlocks
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'About Intro', 'noorblocks' ),
	'description'   => __( 'A two-column intro with heading on the left and story text on the right.', 'noorblocks' ),
	'keywords'      => array( 'about', 'intro', 'story' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noorblocks/container {"align":"full","style":{"spacing":{"padding":{"top":"5rem","right":"1.5rem","bottom":"5rem","left":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-noorblocks-container alignfull" style="padding-top:5rem;padding-right:1.5rem;padding-bottom:5rem;padding-left:1.5rem"><!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:noorblocks/heading -->
<h2 class="wp-block-noorblocks-heading">We build tools that make the web beautiful</h2>
<!-- /wp:noorblocks/heading --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:paragraph -->
<p>What started as a small side project has grown into a full library of blocks and templates used by thousands of site builders around the world.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>We believe great design should be accessible to everyone — no code, no compromises. Every block we ship is fast, accessible and built to work with any theme.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:noorblocks/container -->',
);
