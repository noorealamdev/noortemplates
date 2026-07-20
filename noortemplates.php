<?php
/**
 * Plugin Name:       NoorTemplates
 * Description:       Build WooCommerce single product pages from premade templates, using blocks that wrap WooCommerce's own rendering.
 * Version:           1.1.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:            Noor E Alam
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       noortemplates
 *
 * @package NoorTemplates
 */

defined( 'ABSPATH' ) || exit;

// Plugin constants.
define( 'NOORTEMPLATES_VERSION', '1.1.0' );
define( 'NOORTEMPLATES_FILE', __FILE__ );
define( 'NOORTEMPLATES_DIR', plugin_dir_path( __FILE__ ) );
define( 'NOORTEMPLATES_URL', plugin_dir_url( __FILE__ ) );
define( 'NOORTEMPLATES_BASENAME', plugin_basename( __FILE__ ) );

// Where Pro-only upgrade messages send the user — replace with the real
define( 'NOORTEMPLATES_CHECKOUT_URL', 'https://example.com/replace-with-real-checkout-link' );

// Register the PSR-4 autoloader.
require_once NOORTEMPLATES_DIR . 'includes/Autoloader.php';
\NoorTemplates\Autoloader::register();

// Lifecycle hooks.
register_activation_hook( __FILE__, array( '\NoorTemplates\Install', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\NoorTemplates\Install', 'deactivate' ) );

/**
 * Returns the main plugin instance.
 *
 * @return \NoorTemplates\Plugin
 */
function noortemplates() {
	return \NoorTemplates\Plugin::instance();
}

// Boot the plugin once all active plugins (including WooCommerce) have loaded.
add_action( 'plugins_loaded', 'noortemplates' );
