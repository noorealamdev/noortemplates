<?php
/**
 * Template source contract.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks\Templates;

defined( 'ABSPATH' ) || exit;

/**
 * A provider of library templates.
 *
 * Implement this to add templates from any origin (bundled files, a remote
 * cloud API, a Pro add-on, …) and register the instance via the
 * `noorblocks/template_sources` filter.
 */
interface Source {

	/**
	 * Returns a unique identifier for this source.
	 *
	 * @return string
	 */
	public function get_id();

	/**
	 * Returns the templates provided by this source.
	 *
	 * Each template is an associative array:
	 * - name        (string) Unique slug.
	 * - title       (string) Human-readable title.
	 * - description (string) Short description.
	 * - type        (string) Either 'page' or 'section'.
	 * - content     (string) Serialized block markup.
	 *
	 * @return array[]
	 */
	public function get_templates();
}
