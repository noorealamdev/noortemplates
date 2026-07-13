<?php
/**
 * Removes all plugin data on uninstall.
 *
 * @package NoorBlocks
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'noorblocks_disabled_blocks' );
delete_option( 'noorblocks_version' );
delete_transient( 'noorblocks_cloud_templates' );
