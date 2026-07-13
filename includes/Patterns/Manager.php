<?php
/**
 * Block pattern registration manager.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks\Patterns;

use NoorBlocks\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the NoorBlocks pattern categories and every pattern file.
 *
 * Patterns are organised by folder:
 * - patterns/sections/ — reusable page sections (hero, features, CTA, …)
 * - patterns/pages/    — full page templates composed from sections; these
 *                        also appear in the "start with a pattern" modal
 *                        when creating a new page.
 * - patterns/          — uncategorised one-off patterns.
 */
class Manager {

	use Singleton;

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
			'noorblocks'          => __( 'NoorBlocks', 'noorblocks' ),
			'noorblocks-sections' => __( 'NoorBlocks Sections', 'noorblocks' ),
			'noorblocks-pages'    => __( 'NoorBlocks Pages', 'noorblocks' ),
		);
	}

	/**
	 * Returns the pattern directories mapped to their default category.
	 *
	 * @return array<string, string>
	 */
	private function get_pattern_dirs() {
		return array(
			NOORBLOCKS_DIR . 'patterns'          => 'noorblocks',
			NOORBLOCKS_DIR . 'patterns/sections' => 'noorblocks-sections',
			NOORBLOCKS_DIR . 'patterns/pages'    => 'noorblocks-pages',
		);
	}

	/**
	 * Registers the pattern categories and all pattern files.
	 *
	 * Each pattern file must return an array accepted by
	 * register_block_pattern(); the file name becomes the pattern slug and
	 * the folder provides the default category when none is set.
	 *
	 * @return void
	 */
	public function register_patterns() {
		foreach ( $this->get_categories() as $slug => $label ) {
			register_block_pattern_category( $slug, array( 'label' => $label ) );
		}

		foreach ( $this->get_pattern_dirs() as $dir => $default_category ) {
			$files = glob( $dir . '/*.php' );

			if ( ! is_array( $files ) ) {
				continue;
			}

			foreach ( $files as $file ) {
				$pattern = include $file;

				if ( ! is_array( $pattern ) || empty( $pattern['content'] ) ) {
					continue;
				}

				if ( empty( $pattern['categories'] ) ) {
					$pattern['categories'] = array( $default_category );
				}

				register_block_pattern( 'noorblocks/' . basename( $file, '.php' ), $pattern );
			}
		}
	}

	/**
	 * Returns every template for the editor template library.
	 *
	 * @return array[] Each item: name, title, description, type (page|section), content.
	 */
	public function get_library_templates() {
		$templates = array();

		$folders = array(
			'pages'    => 'page',
			'sections' => 'section',
		);

		foreach ( $folders as $folder => $type ) {
			$files = glob( NOORBLOCKS_DIR . 'patterns/' . $folder . '/*.php' );

			if ( ! is_array( $files ) ) {
				continue;
			}

			foreach ( $files as $file ) {
				$pattern = include $file;

				if ( ! is_array( $pattern ) || empty( $pattern['content'] ) ) {
					continue;
				}

				$templates[] = array(
					'name'        => basename( $file, '.php' ),
					'title'       => isset( $pattern['title'] ) ? $pattern['title'] : basename( $file, '.php' ),
					'description' => isset( $pattern['description'] ) ? $pattern['description'] : '',
					'type'        => $type,
					'content'     => $pattern['content'],
				);
			}
		}

		return $templates;
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
		$file = NOORBLOCKS_DIR . 'patterns/sections/' . $name . '.php';

		if ( ! is_readable( $file ) ) {
			return '';
		}

		$pattern = include $file;

		return is_array( $pattern ) && ! empty( $pattern['content'] ) ? $pattern['content'] : '';
	}
}
