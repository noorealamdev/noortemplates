<?php
/**
 * Main plugin class.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks;

use NoorBlocks\Traits\Singleton;
use NoorBlocks\Blocks\Manager as Blocks_Manager;
use NoorBlocks\Patterns\Manager as Patterns_Manager;
use NoorBlocks\Assets\Manager as Assets_Manager;
use NoorBlocks\Admin\Dashboard;

defined( 'ABSPATH' ) || exit;

/**
 * Boots every plugin service.
 */
class Plugin {

	use Singleton;

	/**
	 * Wires up all services.
	 */
	protected function __construct() {
		$this->init_services();

		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Instantiates the service managers.
	 *
	 * @return void
	 */
	private function init_services() {
		Blocks_Manager::instance();
		Patterns_Manager::instance();
		Assets_Manager::instance();

		if ( is_admin() ) {
			Dashboard::instance();
		}
	}

	/**
	 * Loads the plugin text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'noorblocks', false, dirname( NOORBLOCKS_BASENAME ) . '/languages' );
	}
}
