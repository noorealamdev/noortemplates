<?php
/**
 * Removes all plugin data on uninstall.
 *
 * @package NoorTemplates
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'noortemplates_disabled_blocks' );
delete_option( 'noortemplates_version' );
delete_option( 'noortemplates_default_layout_id' );
delete_transient( 'noortemplates_cloud_templates' );

// Remove every Product Layout post and its per-product/per-category assignments.
$noortemplates_layouts = get_posts(
	array(
		'post_type'     => 'noortemplates_layout',
		'post_status'   => 'any',
		'numberposts'   => -1,
		'fields'        => 'ids',
		'no_found_rows' => true,
	)
);

foreach ( $noortemplates_layouts as $noortemplates_layout_id ) {
	wp_delete_post( $noortemplates_layout_id, true );
}

delete_post_meta_by_key( '_noortemplates_layout_id' );
delete_post_meta_by_key( '_noortemplates_layout_id_b' );
delete_post_meta_by_key( '_noortemplates_split_enabled' );
delete_post_meta_by_key( '_noortemplates_split_ratio' );

global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.NotPrepared -- one-time cleanup of this plugin's own table on uninstall; $wpdb->prefix is a trusted constant, not user input.
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'noortemplates_split_stats' );

$noortemplates_terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'fields'     => 'ids',
	)
);

if ( is_array( $noortemplates_terms ) ) {
	foreach ( $noortemplates_terms as $noortemplates_term_id ) {
		delete_term_meta( $noortemplates_term_id, 'noortemplates_layout_id' );
	}
}
