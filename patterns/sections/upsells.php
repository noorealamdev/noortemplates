<?php
/**
 * Upsells section.
 *
 * @package NoorTemplates
 */

defined( 'ABSPATH' ) || exit;

return array(
	'title'         => __( 'Upsells', 'noortemplates' ),
	'description'   => __( 'A grid of upsell products for the current product, always up to date.', 'noortemplates' ),
	'keywords'      => array( 'upsells', 'products', 'grid', 'cross-sell' ),
	'viewportWidth' => 1400,
	'content'       => '<!-- wp:noortemplates/product-grid {"relation":"upsells"} /-->',
);
