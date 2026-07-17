<?php
/**
 * Fallback single-product template that renders a NoorTemplates Product
 * Layout in place of the theme's default, mirroring the structure of
 * WooCommerce's own templates/single-product.php so theme header, footer
 * and sidebar wrappers stay intact.
 *
 * @package NoorTemplates
 */

use NoorTemplates\Layouts\Resolver;

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own hook, kept so the theme's structural CSS still applies.

while ( have_posts() ) :
	the_post();

	global $product;
	$product = wc_get_product( get_the_ID() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- $product is WooCommerce's own global.

	// Mirrors WooCommerce's own content-single-product.php wrapping hooks
	// (empty by default) so plugins that hook onto them — structured data,
	// "recently viewed" trackers, page-builder compatibility layers — still
	// fire around a NoorTemplates layout exactly as they would around the
	// theme's default template.
	do_action( 'woocommerce_before_single_product' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own hook.

	$noortemplates_layout = Resolver::instance()->get_layout( get_the_ID() );

	if ( $noortemplates_layout ) {
		echo do_blocks( $noortemplates_layout->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block content is sanitized like any other post content.
	}

	do_action( 'woocommerce_after_single_product' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own hook.

endwhile;

do_action( 'woocommerce_after_main_content' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own hook.

do_action( 'woocommerce_sidebar' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own hook.

get_footer( 'shop' );
