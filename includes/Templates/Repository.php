<?php
/**
 * Template repository.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks\Templates;

use NoorBlocks\Traits\Singleton;
use NoorBlocks\Templates\Sources\Local_Source;
use NoorBlocks\Templates\Sources\Cloud_Source;

defined( 'ABSPATH' ) || exit;

/**
 * Aggregates every template source into the single collection the editor
 * template library consumes.
 */
class Repository {

	use Singleton;

	/**
	 * Returns all registered template sources.
	 *
	 * @return Source[]
	 */
	public function get_sources() {
		$sources = array(
			new Local_Source(),
			new Cloud_Source(),
		);

		/**
		 * Filters the registered template sources.
		 *
		 * Add-ons can append their own Source implementations.
		 *
		 * @param Source[] $sources Registered sources.
		 */
		$sources = apply_filters( 'noorblocks/template_sources', $sources );

		return array_filter(
			(array) $sources,
			static function ( $source ) {
				return $source instanceof Source;
			}
		);
	}

	/**
	 * Returns the merged templates of every source.
	 *
	 * Later sources cannot overwrite a name already provided by an earlier
	 * one, so bundled templates always win over remote ones.
	 *
	 * @return array[]
	 */
	public function get_templates() {
		$templates = array();

		foreach ( $this->get_sources() as $source ) {
			foreach ( $source->get_templates() as $template ) {
				if ( empty( $template['name'] ) || isset( $templates[ $template['name'] ] ) ) {
					continue;
				}

				$template['source']             = $source->get_id();
				$templates[ $template['name'] ] = $template;
			}
		}

		/**
		 * Filters the final template collection served to the editor.
		 *
		 * @param array[] $templates Templates keyed by name.
		 */
		$templates = apply_filters( 'noorblocks/library_templates', $templates );

		return array_values( $templates );
	}

	/**
	 * Flushes every source cache so the next request refetches.
	 *
	 * @return void
	 */
	public function flush_caches() {
		foreach ( $this->get_sources() as $source ) {
			if ( method_exists( $source, 'flush_cache' ) ) {
				$source->flush_cache();
			}
		}
	}
}
