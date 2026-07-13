<?php
/**
 * Asset manager.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks\Assets;

use NoorBlocks\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

/**
 * Handles plugin-wide (non block-specific) assets.
 *
 * Individual block scripts and styles are declared in each block.json and
 * enqueued automatically by WordPress; this class handles shared assets
 * such as the editor template library app.
 */
class Manager {

	use Singleton;

	/**
	 * Hooks asset enqueuing.
	 */
	protected function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Enqueues the template library app in the block editor.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets() {
		$asset_file = NOORBLOCKS_DIR . 'build/library/index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = include $asset_file;

		wp_enqueue_script(
			'noorblocks-library',
			NOORBLOCKS_URL . 'build/library/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		$style = NOORBLOCKS_DIR . 'build/library/index.css';

		if ( file_exists( $style ) ) {
			wp_enqueue_style(
				'noorblocks-library',
				NOORBLOCKS_URL . 'build/library/index.css',
				array( 'wp-components' ),
				$asset['version']
			);
		}
	}
}
