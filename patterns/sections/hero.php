<?php
/**
 * Hero section.
 *
 * @package NoorBlocks
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Hero', 'noorblocks' ),
	'description'   => __( 'A dark full-width hero with heading, intro text and a call-to-action button.', 'noorblocks' ),
	'keywords'      => array( 'hero', 'banner', 'header' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noorblocks/container {"align":"full","style":{"color":{"background":"#0f172a","text":"#f8fafc"},"spacing":{"padding":{"top":"7rem","right":"1.5rem","bottom":"7rem","left":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-noorblocks-container alignfull has-text-color has-background" style="background-color:#0f172a;color:#f8fafc;padding-top:7rem;padding-right:1.5rem;padding-bottom:7rem;padding-left:1.5rem"><!-- wp:noorblocks/heading {"level":1,"textAlign":"center"} -->
<h1 class="wp-block-noorblocks-heading has-text-align-center">Craft stunning pages in minutes</h1>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">NoorBlocks gives you advanced blocks, ready-made sections and full page templates so you can launch faster than ever.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:noorblocks/button {"url":"#","textAlign":"center"} -->
<div class="wp-block-noorblocks-button has-text-align-center"><a class="noorblocks-button__link" href="#">Get Started Free</a></div>
<!-- /wp:noorblocks/button --></div>
<!-- /wp:noorblocks/container -->',
);
