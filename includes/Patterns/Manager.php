<?php
/**
 * Block pattern registration manager.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates\Patterns;

use NoorTemplates\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the NoorTemplates pattern categories and every pattern file.
 *
 * Patterns are organised by folder:
 * - patterns/sections/ — reusable product page sections (FAQ, trust badges,
 *                        feature table, …) meant to be inserted inside a
 *                        Product Layout.
 * - patterns/layouts/  — full single-product page layouts composed from
 *                        the WooCommerce wrapper blocks + sections; these
 *                        feed the Templates library on the Product Layout
 *                        editor.
 * - patterns/          — uncategorised one-off patterns.
 */
class Manager {

	use Singleton;

	/**
	 * Transient key the parsed pattern collection is cached under.
	 */
	const CACHE_KEY = 'noortemplates_patterns';

	/**
	 * Per-request memoized patterns, keyed by name.
	 *
	 * @var array[]|null
	 */
	private $patterns = null;

	/**
	 * Hooks pattern registration.
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'register_patterns' ) );
	}

	/**
	 * Returns the pattern categories, keyed by slug.
	 *
	 * @return array<string, string>
	 */
	public function get_categories() {
		return array(
			'noortemplates'          => __( 'NoorTemplates', 'noortemplates' ),
			'noortemplates-sections' => __( 'NoorTemplates Sections', 'noortemplates' ),
			'noortemplates-layouts'  => __( 'NoorTemplates Layouts', 'noortemplates' ),
		);
	}

	/**
	 * Returns the pattern directories mapped to their default category and
	 * library type.
	 *
	 * @return array<string, array{pattern_category: string, type: ?string}>
	 */
	private function get_pattern_dirs() {
		$dirs = array(
			NOORTEMPLATES_DIR . 'patterns'          => array(
				'pattern_category' => 'noortemplates',
				'type'             => null,
			),
			NOORTEMPLATES_DIR . 'patterns/sections' => array(
				'pattern_category' => 'noortemplates-sections',
				'type'             => 'section',
			),
			NOORTEMPLATES_DIR . 'patterns/layouts'  => array(
				'pattern_category' => 'noortemplates-layouts',
				'type'             => 'layout',
			),
		);

		/**
		 * Filters the pattern directories to register.
		 *
		 * Keys are absolute directory paths, values an array with a
		 * `pattern_category` slug and a library `type` ('layout', 'section'
		 * or null for uncategorised one-off patterns).
		 *
		 * @param array $dirs Pattern directories.
		 */
		return (array) apply_filters( 'noortemplates/pattern_dirs', $dirs );
	}

	/**
	 * Whether the current request can actually make use of registered
	 * block patterns.
	 *
	 * Registering patterns requires globbing and including every pattern
	 * file, so this is skipped on front-end requests (e.g. WooCommerce
	 * product pages) where the pattern registry is never consulted.
	 *
	 * @return bool
	 */
	private function patterns_are_needed() {
		if ( is_admin() || wp_doing_ajax() ) {
			return true;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		/**
		 * Filters whether block patterns should be registered on requests
		 * that are neither admin, ajax, nor REST.
		 *
		 * @param bool $needed Whether patterns are needed.
		 */
		return (bool) apply_filters( 'noortemplates/patterns_are_needed', false );
	}

	/**
	 * Registers the pattern categories and all pattern files.
	 *
	 * @return void
	 */
	public function register_patterns() {
		if ( ! $this->patterns_are_needed() ) {
			return;
		}

		foreach ( $this->get_categories() as $slug => $label ) {
			register_block_pattern_category( $slug, array( 'label' => $label ) );
		}

		foreach ( $this->get_patterns() as $name => $pattern ) {
			register_block_pattern( 'noortemplates/' . $name, $pattern );
		}
	}

	/**
	 * Returns every template for the editor template library, as
	 * lightweight metadata (no `content`).
	 *
	 * @return array[] Each item: name, title, description, type
	 *                 (layout|section), category, thumbnail.
	 */
	public function get_library_templates() {
		$templates = array();

		foreach ( $this->get_patterns() as $name => $pattern ) {
			if ( empty( $pattern['type'] ) ) {
				continue;
			}

			$templates[] = array(
				'name'        => $name,
				'title'       => $pattern['title'],
				'description' => $pattern['description'],
				'type'        => $pattern['type'],
				'category'    => $pattern['category'],
				'thumbnail'   => $pattern['thumbnail'],
			);
		}

		return $templates;
	}

	/**
	 * Returns a single bundled template with its full content, or null.
	 *
	 * @param string $name Pattern file name without extension.
	 * @return array|null
	 */
	public function get_template( $name ) {
		$patterns = $this->get_patterns();

		if ( ! isset( $patterns[ $name ] ) || empty( $patterns[ $name ]['type'] ) ) {
			return null;
		}

		$pattern = $patterns[ $name ];

		return array(
			'name'        => $name,
			'title'       => $pattern['title'],
			'description' => $pattern['description'],
			'type'        => $pattern['type'],
			'category'    => $pattern['category'],
			'thumbnail'   => $pattern['thumbnail'],
			'content'     => $pattern['content'],
		);
	}

	/**
	 * Returns the raw block markup of a section pattern.
	 *
	 * Lets page templates be composed from section files without
	 * duplicating markup.
	 *
	 * @param string $name Section file name without extension.
	 * @return string
	 */
	public static function get_section_content( $name ) {
		$patterns = self::instance()->get_patterns();

		return isset( $patterns[ $name ]['content'] ) ? $patterns[ $name ]['content'] : '';
	}

	/**
	 * Empties the cached pattern collection so the next request rescans
	 * every pattern file.
	 *
	 * @return void
	 */
	public function flush_cache() {
		$this->patterns = null;
		delete_transient( self::CACHE_KEY );
	}

	/**
	 * Returns every pattern, keyed by name, normalized with fallbacks for
	 * `category` and `thumbnail` and ready for register_block_pattern().
	 *
	 * Cached per-request in $this->patterns and across requests in a
	 * transient, invalidated whenever any pattern file's mtime changes.
	 *
	 * @return array[]
	 */
	private function get_patterns() {
		if ( null !== $this->patterns ) {
			return $this->patterns;
		}

		$fingerprint = $this->get_fingerprint();
		$cached      = get_transient( self::CACHE_KEY );

		if ( is_array( $cached ) && isset( $cached['fingerprint'], $cached['patterns'] ) && $cached['fingerprint'] === $fingerprint ) {
			return $this->patterns = $cached['patterns'];
		}

		$patterns = $this->scan_patterns();

		set_transient(
			self::CACHE_KEY,
			array(
				'fingerprint' => $fingerprint,
				'patterns'    => $patterns,
			),
			DAY_IN_SECONDS
		);

		return $this->patterns = $patterns;
	}

	/**
	 * Builds a fingerprint from every pattern file's path and mtime, so
	 * editing a file's content invalidates the cache immediately (not just
	 * adding/removing/renaming a file, which is all a directory mtime
	 * would reliably reflect).
	 *
	 * @return string
	 */
	private function get_fingerprint() {
		$stamp = '';

		foreach ( array_keys( $this->get_pattern_dirs() ) as $dir ) {
			foreach ( $this->glob_pattern_files( $dir ) as $file ) {
				$stamp .= $file . filemtime( $file );
			}
		}

		return md5( $stamp );
	}

	/**
	 * Globs the *.php files directly inside a pattern directory.
	 *
	 * @param string $dir Absolute directory path.
	 * @return string[]
	 */
	private function glob_pattern_files( $dir ) {
		$files = glob( $dir . '/*.php' );

		return is_array( $files ) ? $files : array();
	}

	/**
	 * Includes and normalizes every pattern file.
	 *
	 * @return array[]
	 */
	private function scan_patterns() {
		$patterns = array();

		foreach ( $this->get_pattern_dirs() as $dir => $context ) {
			foreach ( $this->glob_pattern_files( $dir ) as $file ) {
				$pattern = include $file;

				if ( ! is_array( $pattern ) || empty( $pattern['content'] ) ) {
					continue;
				}

				$name = basename( $file, '.php' );

				$pattern['title']       = isset( $pattern['title'] ) ? $pattern['title'] : $name;
				$pattern['description'] = isset( $pattern['description'] ) ? $pattern['description'] : '';
				$pattern['type']        = isset( $pattern['type'] ) ? $pattern['type'] : $context['type'];
				$pattern['category']    = isset( $pattern['category'] ) ? $pattern['category'] : $pattern['type'];
				$pattern['thumbnail']   = isset( $pattern['thumbnail'] ) ? $pattern['thumbnail'] : $this->guess_thumbnail( $dir, $name );

				if ( empty( $pattern['categories'] ) ) {
					$pattern['categories'] = array( $context['pattern_category'] );
				}

				$patterns[ $name ] = $pattern;
			}
		}

		return $patterns;
	}

	/**
	 * Auto-detects a thumbnail image co-located with a pattern file, in
	 * `{dir}/thumbnails/{name}.{png,jpg,webp}`.
	 *
	 * @param string $dir  Absolute pattern directory.
	 * @param string $name Pattern file name without extension.
	 * @return string Absolute URL, or '' when no thumbnail exists.
	 */
	private function guess_thumbnail( $dir, $name ) {
		foreach ( array( 'png', 'jpg', 'webp' ) as $ext ) {
			$path = $dir . '/thumbnails/' . $name . '.' . $ext;

			if ( is_readable( $path ) ) {
				return str_replace( NOORTEMPLATES_DIR, NOORTEMPLATES_URL, $path );
			}
		}

		return '';
	}
}
