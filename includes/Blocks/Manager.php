<?php
/**
 * Block registration manager.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks\Blocks;

use NoorBlocks\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

/**
 * Discovers and registers every block in the build directory.
 */
class Manager {

	use Singleton;

	/**
	 * Option name storing the list of disabled block names.
	 */
	const DISABLED_OPTION = 'noorblocks_disabled_blocks';

	/**
	 * Hooks block registration.
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_filter( 'block_categories_all', array( $this, 'register_category' ), 10, 1 );
	}

	/**
	 * Registers every block found in build/blocks, skipping disabled ones.
	 *
	 * @return void
	 */
	public function register_blocks() {
		$disabled = (array) get_option( self::DISABLED_OPTION, array() );

		foreach ( $this->get_block_dirs() as $dir ) {
			$metadata = $this->get_block_metadata( $dir );

			if ( empty( $metadata['name'] ) || in_array( $metadata['name'], $disabled, true ) ) {
				continue;
			}

			register_block_type( $dir );
		}
	}

	/**
	 * Adds the NoorBlocks category to the top of the block inserter.
	 *
	 * @param array $categories Existing block categories.
	 * @return array
	 */
	public function register_category( $categories ) {
		return array_merge(
			array(
				array(
					'slug'  => 'noorblocks',
					'title' => __( 'NoorBlocks', 'noorblocks' ),
					'icon'  => null,
				),
			),
			$categories
		);
	}

	/**
	 * Returns the absolute paths of every built block directory.
	 *
	 * @return string[]
	 */
	public function get_block_dirs() {
		$dirs = glob( NOORBLOCKS_DIR . 'build/blocks/*', GLOB_ONLYDIR );

		if ( ! is_array( $dirs ) ) {
			return array();
		}

		return array_filter(
			$dirs,
			static function ( $dir ) {
				return file_exists( $dir . '/block.json' );
			}
		);
	}

	/**
	 * Reads and decodes a block.json file.
	 *
	 * @param string $dir Block directory.
	 * @return array
	 */
	public function get_block_metadata( $dir ) {
		$file = trailingslashit( $dir ) . 'block.json';

		if ( ! is_readable( $file ) ) {
			return array();
		}

		$metadata = wp_json_file_decode( $file, array( 'associative' => true ) );

		return is_array( $metadata ) ? $metadata : array();
	}

	/**
	 * Returns metadata for every discovered block, keyed by block name.
	 *
	 * @return array[]
	 */
	public function get_all_blocks() {
		$blocks = array();

		foreach ( $this->get_block_dirs() as $dir ) {
			$metadata = $this->get_block_metadata( $dir );

			if ( ! empty( $metadata['name'] ) ) {
				$blocks[ $metadata['name'] ] = $metadata;
			}
		}

		return $blocks;
	}
}
