<?php
/**
 * Data access for split-test counters.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates\Layouts;

defined( 'ABSPATH' ) || exit;

/**
 * Stores raw impression/add-to-cart/purchase counts per product+variant.
 *
 * Stateless by design (no singleton) — every method takes the data it needs
 * and talks to $wpdb directly, since there is no per-request state worth
 * caching here.
 */
class Split_Test_Stats {

	/**
	 * Returns the counters table name, including the site's table prefix.
	 *
	 * @return string
	 */
	public static function table_name() {
		global $wpdb;

		return $wpdb->prefix . 'noortemplates_split_stats';
	}

	/**
	 * Creates (or updates) the counters table.
	 *
	 * Safe to call repeatedly — dbDelta() only applies the schema diff.
	 * ENUM columns are deliberately avoided; dbDelta has long-standing
	 * trouble reliably detecting/altering them on repeated calls.
	 *
	 * @return void
	 */
	public static function create_table() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_name      = self::table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			product_id BIGINT UNSIGNED NOT NULL,
			variant VARCHAR(1) NOT NULL,
			metric VARCHAR(20) NOT NULL,
			count BIGINT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY  (id),
			UNIQUE KEY product_variant_metric (product_id, variant, metric)
		) {$charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Atomically increments one counter by 1.
	 *
	 * @param int    $product_id Product post ID.
	 * @param string $variant    'a' or 'b'.
	 * @param string $metric     'impression', 'add_to_cart', or 'purchase'.
	 * @return void
	 */
	public static function increment( $product_id, $variant, $metric ) {
		global $wpdb;

		$table_name = self::table_name();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		// $table_name is our own prefixed table identifier, not user input; every value placeholder is passed through prepare().
		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$table_name} (product_id, variant, metric, count) VALUES (%d, %s, %s, 1)
				ON DUPLICATE KEY UPDATE count = count + 1",
				$product_id,
				$variant,
				$metric
			)
		);
		// phpcs:enable
	}

	/**
	 * Returns both variants' counts, zero-filled when no rows exist yet.
	 *
	 * @param int $product_id Product post ID.
	 * @return array{a: array{impression:int,add_to_cart:int,purchase:int}, b: array{impression:int,add_to_cart:int,purchase:int}}
	 */
	public static function get_stats( $product_id ) {
		global $wpdb;

		$table_name = self::table_name();
		$metrics    = array( 'impression', 'add_to_cart', 'purchase' );

		$stats = array(
			'a' => array_fill_keys( $metrics, 0 ),
			'b' => array_fill_keys( $metrics, 0 ),
		);

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		// $table_name is our own prefixed table identifier, not user input; the product_id value is passed through prepare().
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT variant, metric, count FROM {$table_name} WHERE product_id = %d",
				$product_id
			)
		);
		// phpcs:enable

		foreach ( (array) $rows as $row ) {
			if ( isset( $stats[ $row->variant ][ $row->metric ] ) ) {
				$stats[ $row->variant ][ $row->metric ] = (int) $row->count;
			}
		}

		return $stats;
	}

	/**
	 * Clears every counter for a product (used when a test ends).
	 *
	 * @param int $product_id Product post ID.
	 * @return void
	 */
	public static function reset( $product_id ) {
		global $wpdb;

		$table_name = self::table_name();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
		// $table_name is our own prefixed table identifier, not user input; the product_id value is passed through prepare().
		$wpdb->query(
			$wpdb->prepare( "DELETE FROM {$table_name} WHERE product_id = %d", $product_id )
		);
		// phpcs:enable
	}
}
