<?php
/**
 * Templates REST controller.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates\Rest;

use NoorTemplates\Traits\Singleton;
use NoorTemplates\Templates\Repository;

defined( 'ABSPATH' ) || exit;

/**
 * Serves the template library to the editor.
 */
class Templates_Controller {

	use Singleton;

	/**
	 * REST namespace.
	 */
	const REST_NAMESPACE = 'noortemplates/v1';

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
					'args'                => array(
						'type'     => array(
							'type' => 'string',
							'enum' => array( 'layout', 'section' ),
						),
						'category' => array(
							'type' => 'string',
						),
						'search'   => array(
							'type' => 'string',
						),
					),
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

		register_rest_route(
			self::REST_NAMESPACE,
			'/templates/(?P<name>[a-z0-9_-]+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_template' ),
					'permission_callback' => array( $this, 'can_use_library' ),
				),
			)
		);
	}

	/**
	 * Returns library templates as lightweight metadata, optionally
	 * filtered by type, category and/or search term.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response
	 */
	public function get_templates( $request ) {
		$args = array_intersect_key(
			$request->get_params(),
			array_flip( array( 'type', 'category', 'search' ) )
		);

		return rest_ensure_response( Repository::instance()->get_templates( $args ) );
	}

	/**
	 * Returns a single template with its full content.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_template( $request ) {
		$template = Repository::instance()->get_template( $request['name'] );

		if ( ! $template ) {
			return new \WP_Error(
				'noortemplates_template_not_found',
				__( 'Template not found.', 'noortemplates' ),
				array( 'status' => 404 )
			);
		}

		return rest_ensure_response( $template );
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
