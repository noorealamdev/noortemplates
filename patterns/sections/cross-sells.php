<?php
/**
 * Cross-sells section.
 *
 * @package NoorTemplates
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Cross-sells', 'noortemplates' ),
	'description'   => __( 'A grid of cross-sell products for the current product, always up to date.', 'noortemplates' ),
	'keywords'      => array( 'cross-sells', 'products', 'grid', 'upsell' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noortemplates/product-grid {"relation":"cross-sells"} /-->',
);
