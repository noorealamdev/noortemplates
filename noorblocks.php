<?php
/**
 * Plugin Name:       NoorBlocks
 * Plugin URI:        https://noorblocks.com
 * Description:       A powerful Gutenberg blocks and patterns library built with a modern OOP architecture.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Noore Alam
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       noorblocks
 * Domain Path:       /languages
 *
 * @package NoorBlocks
 */

defined( 'ABSPATH' ) || exit;

// Plugin constants.
define( 'NOORBLOCKS_VERSION', '1.0.0' );
define( 'NOORBLOCKS_FILE', __FILE__ );
define( 'NOORBLOCKS_DIR', plugin_dir_path( __FILE__ ) );
define( 'NOORBLOCKS_URL', plugin_dir_url( __FILE__ ) );
define( 'NOORBLOCKS_BASENAME', plugin_basename( __FILE__ ) );

// Register the PSR-4 autoloader.
require_once NOORBLOCKS_DIR . 'includes/Autoloader.php';
\NoorBlocks\Autoloader::register();

/**
 * Returns the main plugin instance.
 *
 * @return \NoorBlocks\Plugin
 */
function noorblocks() {
	return \NoorBlocks\Plugin::instance();
}

// Boot the plugin.
noorblocks();
