<?php
/**
 * Cloud template source.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks\Templates\Sources;

use NoorBlocks\Templates\Source;

defined( 'ABSPATH' ) || exit;

/**
 * Fetches predesigned templates as JSON from a remote cloud API.
 *
 * The API must return a JSON array of template objects:
 *
 *     [
 *         {
 *             "name": "agency-home",
 *             "title": "Agency Home",
 *             "description": "…",
 *             "type": "page",
 *             "content": "<!-- wp:noorblocks/container -->…"
 *         }
 *     ]
 *
 * The endpoint URL is configured with the NOORBLOCKS_CLOUD_URL constant or
 * the `noorblocks/cloud_api_url` filter; the source is inactive until one
 * of them provides a URL. Responses are cached in a transient.
 */
class Cloud_Source implements Source {

	/**
	 * Transient name for the cached cloud response.
	 */
	const CACHE_KEY = 'noorblocks_cloud_templates';

	/**
	 * How long successful responses are cached.
	 */
	const CACHE_TTL = 12 * HOUR_IN_SECONDS;

	/**
	 * How long failures are cached, so an unreachable API cannot slow
	 * down every editor load.
	 */
	const FAILURE_CACHE_TTL = 5 * MINUTE_IN_SECONDS;

	/**
	 * Valid template types.
	 *
	 * @var string[]
	 */
	private static $types = array( 'page', 'section' );

	/**
	 * Returns the source identifier.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'cloud';
	}

	/**
	 * Returns the cloud templates, from cache when available.
	 *
	 * @return array[]
	 */
	public function get_templates() {
		$url = $this->get_api_url();

		if ( ! $url ) {
			return array();
		}

		$cached = get_transient( self::CACHE_KEY );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$templates = $this->fetch( $url );

		set_transient(
			self::CACHE_KEY,
			$templates,
			empty( $templates ) ? self::FAILURE_CACHE_TTL : self::CACHE_TTL
		);

		return $templates;
	}

	/**
	 * Empties the cached cloud response.
	 *
	 * @return void
	 */
	public function flush_cache() {
		delete_transient( self::CACHE_KEY );
	}

	/**
	 * Returns the configured cloud API URL, or an empty string when the
	 * cloud source is disabled.
	 *
	 * @return string
	 */
	private function get_api_url() {
		$url = defined( 'NOORBLOCKS_CLOUD_URL' ) ? NOORBLOCKS_CLOUD_URL : '';

		/**
		 * Filters the cloud template API URL.
		 *
		 * @param string $url The API URL; empty string disables the source.
		 */
		return (string) apply_filters( 'noorblocks/cloud_api_url', $url );
	}

	/**
	 * Requests and sanitizes the templates from the API.
	 *
	 * @param string $url API endpoint.
	 * @return array[]
	 */
	private function fetch( $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 15,
				'headers' => array( 'Accept' => 'application/json' ),
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return array();
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $data ) ) {
			return array();
		}

		return $this->sanitize( $data );
	}

	/**
	 * Keeps only well-formed template items and normalizes their fields.
	 *
	 * @param array $data Decoded API response.
	 * @return array[]
	 */
	private function sanitize( $data ) {
		$templates = array();

		foreach ( $data as $item ) {
			if (
				! is_array( $item )
				|| empty( $item['name'] ) || ! is_string( $item['name'] )
				|| empty( $item['title'] ) || ! is_string( $item['title'] )
				|| empty( $item['content'] ) || ! is_string( $item['content'] )
				|| empty( $item['type'] ) || ! in_array( $item['type'], self::$types, true )
			) {
				continue;
			}

			$templates[] = array(
				'name'        => sanitize_key( $item['name'] ),
				'title'       => sanitize_text_field( $item['title'] ),
				'description' => isset( $item['description'] ) && is_string( $item['description'] )
					? sanitize_text_field( $item['description'] )
					: '',
				'type'        => $item['type'],
				'content'     => $item['content'],
			);
		}

		return $templates;
	}
}
