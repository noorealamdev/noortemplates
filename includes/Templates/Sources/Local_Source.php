<?php
/**
 * Bundled template source.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks\Templates\Sources;

use NoorBlocks\Templates\Source;
use NoorBlocks\Patterns\Manager as Patterns_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Provides the templates bundled with the plugin in the patterns directory.
 */
class Local_Source implements Source {

	/**
	 * Returns the source identifier.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'local';
	}

	/**
	 * Returns the bundled templates.
	 *
	 * @return array[]
	 */
	public function get_templates() {
		return Patterns_Manager::instance()->get_library_templates();
	}
}
