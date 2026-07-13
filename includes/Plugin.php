<?php
/**
 * Main plugin class.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks;

use NoorBlocks\Traits\Singleton;
use NoorBlocks\Blocks\Manager as Blocks_Manager;
use NoorBlocks\Blocks\Interactivity;
use NoorBlocks\Patterns\Manager as Patterns_Manager;
use NoorBlocks\Assets\Manager as Assets_Manager;
use NoorBlocks\Rest\Templates_Controller;
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
		Install::init();

		Blocks_Manager::instance();
		Interactivity::instance();
		Patterns_Manager::instance();
		Assets_Manager::instance();
		Templates_Controller::instance();

		if ( is_admin() ) {
			Dashboard::instance();
		}

		/**
		 * Fires after every core service booted.
		 *
		 * Add-ons can hook here to register their own services.
		 *
		 * @param Plugin $plugin The plugin instance.
		 */
		do_action( 'noorblocks/loaded', $this );
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
