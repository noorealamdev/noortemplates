<?php
/**
 * Minimal single product layout.
 *
 * @package NoorTemplates
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Minimal', 'noortemplates' ),
	'description'   => __( 'A simple single-column layout: title, gallery, price and add to cart, then tabs.', 'noortemplates' ),
	'keywords'      => array( 'minimal', 'product', 'layout' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noortemplates/product-category /-->

<!-- wp:noortemplates/product-title /-->

<!-- wp:noortemplates/product-rating /-->

<!-- wp:noortemplates/product-gallery-carousel /-->

<!-- wp:noortemplates/product-price /-->

<!-- wp:noortemplates/product-short-description /-->

<!-- wp:noortemplates/product-add-to-cart /-->

<!-- wp:noortemplates/product-tabs /-->

<!-- wp:noortemplates/related-products /-->',
);
