<?php
/**
 * PSR-4 autoloader for the NoorBlocks namespace.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks;

defined( 'ABSPATH' ) || exit;

/**
 * Maps NoorBlocks\Sub\ClassName to includes/Sub/ClassName.php.
 */
class Autoloader {

	/**
	 * Namespace prefix handled by this autoloader.
	 *
	 * @var string
	 */
	const PREFIX = 'NoorBlocks\\';

	/**
	 * Registers the autoloader with SPL.
	 *
	 * @return void
	 */
	public static function register() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Loads the file for a given fully qualified class name.
	 *
	 * @param string $class Fully qualified class name.
	 * @return void
	 */
	public static function autoload( $class ) {
		if ( 0 !== strpos( $class, self::PREFIX ) ) {
			return;
		}

		$relative = substr( $class, strlen( self::PREFIX ) );
		$path     = NOORBLOCKS_DIR . 'includes/' . str_replace( '\\', '/', $relative ) . '.php';

		if ( is_readable( $path ) ) {
			require $path;
		}
	}
}
