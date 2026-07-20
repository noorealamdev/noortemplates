<?php
/**
 * Asset manager.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates\Assets;

use NoorTemplates\Traits\Singleton;
use NoorTemplates\Layouts\Resolver;
use NoorTemplates\Licensing\Gate;

defined( 'ABSPATH' ) || exit;

/**
 * Handles plugin-wide (non block-specific) assets.
 *
 * Individual block scripts and styles are declared in each block.json and
 * enqueued automatically by WordPress; this class handles shared assets
 * such as the editor template library app and the block-extensions engine.
 */
class Manager {

	use Singleton;

	/**
	 * Hooks asset enqueuing.
	 */
	protected function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_end_assets' ) );
	}

	/**
	 * Enqueues the template library app and the block-extensions engine
	 * in the block editor.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets() {
		$this->enqueue_built_script( 'noortemplates-library', 'library/index', true );
		$this->enqueue_built_script( 'noortemplates-extensions', 'extensions/index', false );

		// wp_localize_script() stringifies every value (false becomes '',
		// true becomes '1'), which is a footgun for a boolean callers check
		// with strict-ish truthiness — wp_add_inline_script() with
		// wp_json_encode() keeps isPro a real JS boolean instead.
		wp_add_inline_script(
			'noortemplates-library',
			'window.noorTemplatesLicensing = ' . wp_json_encode(
				array(
					'isPro'       => Gate::is_pro(),
					'checkoutUrl' => NOORTEMPLATES_CHECKOUT_URL,
				)
			) . ';',
			'before'
		);
	}

	/**
	 * Enqueues the block-extensions front-end script and styles, only on
	 * product pages that actually resolve to a NoorTemplates layout.
	 *
	 * @return void
	 */
	public function enqueue_front_end_assets() {
		if ( ! function_exists( 'is_product' ) || ! is_product() ) {
			return;
		}

		if ( ! Resolver::instance()->get_layout( get_the_ID() ) ) {
			return;
		}

		$this->enqueue_built_script( 'noortemplates-extensions-view', 'extensions/view', false );
	}

	/**
	 * Enqueues a built script (and its CSS counterpart, if one exists)
	 * from build/{$entry}.js, using the dependencies/version recorded in
	 * its generated asset.php.
	 *
	 * @param string $handle       Script/style handle.
	 * @param string $entry        Entry name relative to build/, e.g. 'library/index'.
	 * @param bool   $needs_wp_components Whether the stylesheet depends on wp-components.
	 * @return void
	 */
	private function enqueue_built_script( $handle, $entry, $needs_wp_components ) {
		$asset_file = NOORTEMPLATES_DIR . 'build/' . $entry . '.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = include $asset_file;

		wp_enqueue_script(
			$handle,
			NOORTEMPLATES_URL . 'build/' . $entry . '.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		$style_path = $this->get_style_path( $entry );

		if ( $style_path ) {
			wp_enqueue_style(
				$handle,
				NOORTEMPLATES_URL . 'build/' . $style_path,
				$needs_wp_components ? array( 'wp-components' ) : array(),
				$asset['version']
			);
		}
	}

	/**
	 * Resolves the compiled CSS path for a built entry, relative to
	 * build/. wp-scripts names the file `style-{base}.css` when the
	 * entry imports `style.scss` (front-end + editor styles) and
	 * `{base}.css` when it imports `editor.scss` (editor-only styles).
	 *
	 * @param string $entry Entry name relative to build/, e.g. 'extensions/view'.
	 * @return string|null Path relative to build/, or null when no CSS was emitted.
	 */
	private function get_style_path( $entry ) {
		$dir  = trailingslashit( dirname( $entry ) );
		$dir  = '.' === $dir ? '' : $dir;
		$base = basename( $entry );

		foreach ( array( $dir . 'style-' . $base . '.css', $dir . $base . '.css' ) as $candidate ) {
			if ( file_exists( NOORTEMPLATES_DIR . 'build/' . $candidate ) ) {
				return $candidate;
			}
		}

		return null;
	}
}
