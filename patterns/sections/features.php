<?php
/**
 * Features section.
 *
 * @package NoorBlocks
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Features', 'noorblocks' ),
	'description'   => __( 'A three-column feature grid with a centered intro.', 'noorblocks' ),
	'keywords'      => array( 'features', 'services', 'grid' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noorblocks/container {"align":"full","style":{"spacing":{"padding":{"top":"5rem","right":"1.5rem","bottom":"5rem","left":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-noorblocks-container alignfull" style="padding-top:5rem;padding-right:1.5rem;padding-bottom:5rem;padding-left:1.5rem"><!-- wp:noorblocks/heading {"textAlign":"center"} -->
<h2 class="wp-block-noorblocks-heading has-text-align-center">Everything you need to build</h2>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Powerful blocks, flexible sections and full page templates — all in one plugin.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"32px"} -->
<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":3} -->
<h3 class="wp-block-noorblocks-heading">Advanced Blocks</h3>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph -->
<p>Containers, headings, buttons and more — each with full color, spacing and typography controls.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":3} -->
<h3 class="wp-block-noorblocks-heading">Ready-Made Sections</h3>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph -->
<p>Drop in a hero, feature grid or testimonial wall in one click and simply replace the text.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":3} -->
<h3 class="wp-block-noorblocks-heading">Full Page Templates</h3>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph -->
<p>Start a new page from a complete Home, About or Contact layout and make it yours.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:noorblocks/container -->',
);
