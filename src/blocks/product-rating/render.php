<?php
/**
 * Product Rating block server render.
 *
 * @package NoorTemplates
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

global $product;
$product = wc_get_product( get_the_ID() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- $product is WooCommerce's own global.

if ( ! $product ) {
	return;
}

/*
 * Sits directly under Product Title in the starter layouts and wants to
 * sit close to it — but forcing that via a `!important` class rule would
 * also silently beat the user's OWN margin-top if they set one via the
 * block's Spacing panel (that panel applies as an inline style, which a
 * plain class rule loses to, so `!important` was the only way to win —
 * at the cost of always winning, even against the user's own choice).
 * Applying the zero margin as an inline style here, only when the user
 * hasn't set their own, means it disappears the instant they do.
 */
$noortemplates_has_own_margin_top = isset( $attributes['style']['spacing']['margin']['top'] ) && '' !== $attributes['style']['spacing']['margin']['top'];

$noortemplates_wrapper = get_block_wrapper_attributes(
	$noortemplates_has_own_margin_top ? array() : array( 'style' => 'margin-top:0;' )
);
?>
<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<?php woocommerce_template_single_rating(); ?>
</div>
