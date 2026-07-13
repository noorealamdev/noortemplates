<?php
/**
 * Team section.
 *
 * @package NoorBlocks
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Team', 'noorblocks' ),
	'description'   => __( 'A three-column team grid with names and roles.', 'noorblocks' ),
	'keywords'      => array( 'team', 'people', 'members' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noorblocks/container {"align":"full","style":{"color":{"background":"#f1f5f9"},"spacing":{"padding":{"top":"5rem","right":"1.5rem","bottom":"5rem","left":"1.5rem"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-noorblocks-container alignfull has-background" style="background-color:#f1f5f9;padding-top:5rem;padding-right:1.5rem;padding-bottom:5rem;padding-left:1.5rem"><!-- wp:noorblocks/heading {"textAlign":"center"} -->
<h2 class="wp-block-noorblocks-heading has-text-align-center">Meet the team</h2>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">The people behind NoorBlocks.</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"32px"} -->
<div style="height:32px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":4,"textAlign":"center"} -->
<h4 class="wp-block-noorblocks-heading has-text-align-center">Noore Alam</h4>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Founder &amp; Lead Developer</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":4,"textAlign":"center"} -->
<h4 class="wp-block-noorblocks-heading has-text-align-center">Amina Rahman</h4>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Product Designer</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:noorblocks/heading {"level":4,"textAlign":"center"} -->
<h4 class="wp-block-noorblocks-heading has-text-align-center">David Kim</h4>
<!-- /wp:noorblocks/heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Support Engineer</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:noorblocks/container -->',
);
