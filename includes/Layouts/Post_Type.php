<?php
/**
 * Product Layout custom post type.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates\Layouts;

use NoorTemplates\Traits\Singleton;
use NoorTemplates\Admin\Dashboard;

defined( 'ABSPATH' ) || exit;

/**
 * Each Product Layout post holds the block markup for one alternate
 * single-product page arrangement. Layouts are edited in the standard
 * block editor (works on any theme) and applied to products via the
 * Resolver.
 */
class Post_Type {

	use Singleton;

	/**
	 * Post type slug.
	 */
	const SLUG = 'noortemplates_layout';

	/**
	 * Hooks post type registration.
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Registers the Product Layout post type.
	 *
	 * @return void
	 */
	public function register() {
		register_post_type(
			self::SLUG,
			array(
				'labels'          => array(
					'name'          => __( 'Product Layouts', 'noortemplates' ),
					'singular_name' => __( 'Product Layout', 'noortemplates' ),
					'add_new_item'  => __( 'Add New Product Layout', 'noortemplates' ),
					'edit_item'     => __( 'Edit Product Layout', 'noortemplates' ),
					'all_items'     => __( 'Product Layouts', 'noortemplates' ),
					'search_items'  => __( 'Search Product Layouts', 'noortemplates' ),
					'not_found'     => __( 'No product layouts found.', 'noortemplates' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => Dashboard::PAGE_SLUG,
				'show_in_rest'    => true,
				'supports'        => array( 'title', 'editor' ),
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);
	}
}
