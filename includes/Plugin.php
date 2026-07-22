<?php
/**
 * Main plugin class.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates;

use NoorTemplates\Traits\Singleton;
use NoorTemplates\Blocks\Manager as Blocks_Manager;
use NoorTemplates\Blocks\Interactivity;
use NoorTemplates\Blocks\Extensions as Blocks_Extensions;
use NoorTemplates\Blocks\Buy_Now;
use NoorTemplates\Patterns\Manager as Patterns_Manager;
use NoorTemplates\Assets\Manager as Assets_Manager;
use NoorTemplates\Rest\Templates_Controller;
use NoorTemplates\Rest\Product_Reviews_Controller;
use NoorTemplates\Admin\Dashboard;
use NoorTemplates\Layouts\Post_Type as Layouts_Post_Type;
use NoorTemplates\Layouts\Meta_Box as Layouts_Meta_Box;
use NoorTemplates\Layouts\Template_Override as Layouts_Template_Override;
use NoorTemplates\Layouts\Split_Test;
use NoorTemplates\Layouts\Duplicate as Layouts_Duplicate;
use NoorTemplates\Layouts\Preview as Layouts_Preview;
use NoorTemplates\Admin\Split_Tests_Page;

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
		if ( ! $this->has_woocommerce() ) {
			add_action( 'admin_notices', array( $this, 'render_missing_woocommerce_notice' ) );
			return;
		}

		$this->init_services();
	}

	/**
	 * Whether WooCommerce is active.
	 *
	 * @return bool
	 */
	private function has_woocommerce() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Warns the site admin that WooCommerce is required.
	 *
	 * @return void
	 */
	public function render_missing_woocommerce_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		?>
		<div class="notice notice-error">
			<p>
				<?php
				esc_html_e(
					'NoorTemplates requires WooCommerce to be installed and active.',
					'noortemplates'
				);
				?>
			</p>
		</div>
		<?php
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
		Blocks_Extensions::instance();
		Buy_Now::instance();
		Patterns_Manager::instance();
		Assets_Manager::instance();
		Templates_Controller::instance();
		Product_Reviews_Controller::instance();

		Layouts_Post_Type::instance();
		Layouts_Meta_Box::instance();
		Layouts_Template_Override::instance();
		Layouts_Duplicate::instance();
		Layouts_Preview::instance();
		Split_Test::instance();

		if ( is_admin() ) {
			Dashboard::instance();
			Split_Tests_Page::instance();
		}

		/**
		 * Fires after every core service booted.
		 *
		 * Add-ons can hook here to register their own services.
		 *
		 * @param Plugin $plugin The plugin instance.
		 */
		do_action( 'noortemplates/loaded', $this );
	}
}
