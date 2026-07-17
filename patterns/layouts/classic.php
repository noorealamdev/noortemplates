<?php
/**
 * Classic single product layout.
 *
 * @package NoorTemplates
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Classic', 'noortemplates' ),
	'description'   => __( 'Gallery and details side by side, with tabs and related products below.', 'noortemplates' ),
	'keywords'      => array( 'classic', 'product', 'layout' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noortemplates/product-breadcrumbs /-->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:noortemplates/product-gallery-carousel /--></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:noortemplates/product-category /-->

<!-- wp:noortemplates/product-title /-->

<!-- wp:noortemplates/product-rating /-->

<!-- wp:noortemplates/product-price /-->

<!-- wp:noortemplates/product-short-description /-->

<!-- wp:noortemplates/product-add-to-cart /-->

<!-- wp:noortemplates/product-meta /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:noortemplates/product-tabs /-->

<!-- wp:noortemplates/related-products /-->',
);
