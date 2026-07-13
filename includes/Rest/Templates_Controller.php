<?php
/**
 * Templates REST controller.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks\Rest;

use NoorBlocks\Traits\Singleton;
use NoorBlocks\Templates\Repository;

defined( 'ABSPATH' ) || exit;

/**
 * Serves the template library to the editor.
 */
class Templates_Controller {

	use Singleton;

	/**
	 * REST namespace.
	 */
	const REST_NAMESPACE = 'noorblocks/v1';

	/**
	 * Hooks route registration.
	 */
	protected function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Registers the template library routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			self::REST_NAMESPACE,
			'/templates',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_templates' ),
					'permission_callback' => array( $this, 'can_use_library' ),
				),
			)
		);

		register_rest_route(
			self::REST_NAMESPACE,
			'/templates/sync',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'sync_templates' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
			)
		);
	}

	/**
	 * Returns every library template.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_templates() {
		return rest_ensure_response( Repository::instance()->get_templates() );
	}

	/**
	 * Flushes source caches and returns the refetched templates.
	 *
	 * @return \WP_REST_Response
	 */
	public function sync_templates() {
		$repository = Repository::instance();
		$repository->flush_caches();

		return rest_ensure_response( $repository->get_templates() );
	}

	/**
	 * Whether the current user may read the template library.
	 *
	 * @return bool
	 */
	public function can_use_library() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Whether the current user may flush template caches.
	 *
	 * @return bool
	 */
	public function can_manage() {
		return current_user_can( 'manage_options' );
	}
}
