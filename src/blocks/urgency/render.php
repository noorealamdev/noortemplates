<?php
/**
 * Urgency & Countdown block server render.
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

$noortemplates_type        = ! empty( $attributes['type'] ) ? $attributes['type'] : 'stock';
$noortemplates_boxed       = ! isset( $attributes['boxed'] ) || (bool) $attributes['boxed'];
$noortemplates_boxed_width = isset( $attributes['boxedWidth'] ) ? absint( $attributes['boxedWidth'] ) : 800;

/*
 * Background/text/border/padding all come from the block's own supports
 * (Color, Border, Dimensions panels) via get_block_wrapper_attributes() —
 * nothing here hardcodes a look. `is-boxed`/max-width is merged into that
 * same single wrapper (not a separately-styled inner div) so the merchant's
 * Inspector choices always land on the one visible surface.
 */
$noortemplates_wrapper = get_block_wrapper_attributes(
	array(
		'class' => $noortemplates_boxed ? 'is-boxed' : '',
		'style' => $noortemplates_boxed ? 'max-width:' . $noortemplates_boxed_width . 'px;' : '',
	)
);

if ( 'stock' === $noortemplates_type ) {
	// Honest scarcity only: relies entirely on the product's own real
	// stock-management data, never a fabricated/estimated number.
	if ( ! $product->managing_stock() || ! $product->is_in_stock() ) {
		return;
	}

	$noortemplates_stock     = $product->get_stock_quantity();
	$noortemplates_threshold = isset( $attributes['stockThreshold'] ) ? absint( $attributes['stockThreshold'] ) : 10;

	if ( null === $noortemplates_stock || $noortemplates_stock > $noortemplates_threshold ) {
		return;
	}

	$noortemplates_template  = ! empty( $attributes['stockMessage'] )
		? $attributes['stockMessage']
		: __( 'Only {stock} left in stock — order soon!', 'noortemplates' );
	$noortemplates_message   = str_replace( '{stock}', $noortemplates_stock, $noortemplates_template );
	$noortemplates_show_icon = ! isset( $attributes['showIcon'] ) || (bool) $attributes['showIcon'];
	?>
	<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
		<?php if ( $noortemplates_show_icon ) : ?>
			<svg class="noortemplates-urgency__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2 1 21h22L12 2zm0 5.5 7.53 12.5H4.47L12 7.5zM11 10v5h2v-5h-2zm0 6.5v2h2v-2h-2z"/></svg>
		<?php endif; ?>
		<span class="noortemplates-urgency__message"><?php echo esc_html( $noortemplates_message ); ?></span>
	</div>
	<?php
	return;
}

// Countdown type: only ever counts down to a real, merchant-controlled
// deadline (the product's own scheduled sale end date, or a date the
// merchant explicitly sets) — never a fake/rolling "limited time" timer.
$noortemplates_source   = ! empty( $attributes['countdownSource'] ) ? $attributes['countdownSource'] : 'sale';
$noortemplates_deadline = null;

if ( 'sale' === $noortemplates_source ) {
	$noortemplates_sale_to = $product->get_date_on_sale_to();

	if ( $noortemplates_sale_to && $product->is_on_sale() ) {
		$noortemplates_deadline = $noortemplates_sale_to->getTimestamp();
	}
} elseif ( ! empty( $attributes['customDate'] ) ) {
	try {
		$noortemplates_custom   = new DateTime( $attributes['customDate'], wp_timezone() );
		$noortemplates_deadline = $noortemplates_custom->getTimestamp();
	} catch ( Exception $e ) {
		$noortemplates_deadline = null;
	}
}

if ( null === $noortemplates_deadline ) {
	return;
}

$noortemplates_hide_when_expired = ! isset( $attributes['hideWhenExpired'] ) || (bool) $attributes['hideWhenExpired'];
$noortemplates_is_expired        = $noortemplates_deadline <= time();

if ( $noortemplates_is_expired && $noortemplates_hide_when_expired ) {
	return;
}

$noortemplates_label           = ! empty( $attributes['countdownLabel'] ) ? $attributes['countdownLabel'] : __( 'Sale ends in:', 'noortemplates' );
$noortemplates_expired_message = ! empty( $attributes['expiredMessage'] ) ? $attributes['expiredMessage'] : __( 'This offer has ended.', 'noortemplates' );
?>
<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<?php if ( $noortemplates_is_expired ) : ?>
		<span class="noortemplates-urgency__message"><?php echo esc_html( $noortemplates_expired_message ); ?></span>
	<?php else : ?>
		<span class="noortemplates-urgency__label"><?php echo esc_html( $noortemplates_label ); ?></span>
		<div
			class="noortemplates-urgency__timer"
			data-deadline="<?php echo esc_attr( $noortemplates_deadline * 1000 ); ?>"
			data-expired-message="<?php echo esc_attr( $noortemplates_expired_message ); ?>"
			data-hide-when-expired="<?php echo $noortemplates_hide_when_expired ? '1' : '0'; ?>"
		>
			<div class="noortemplates-urgency__unit">
				<span class="noortemplates-urgency__value" data-unit="days">00</span>
				<span class="noortemplates-urgency__unit-label"><?php esc_html_e( 'Days', 'noortemplates' ); ?></span>
			</div>
			<div class="noortemplates-urgency__unit">
				<span class="noortemplates-urgency__value" data-unit="hours">00</span>
				<span class="noortemplates-urgency__unit-label"><?php esc_html_e( 'Hrs', 'noortemplates' ); ?></span>
			</div>
			<div class="noortemplates-urgency__unit">
				<span class="noortemplates-urgency__value" data-unit="minutes">00</span>
				<span class="noortemplates-urgency__unit-label"><?php esc_html_e( 'Min', 'noortemplates' ); ?></span>
			</div>
			<div class="noortemplates-urgency__unit">
				<span class="noortemplates-urgency__value" data-unit="seconds">00</span>
				<span class="noortemplates-urgency__unit-label"><?php esc_html_e( 'Sec', 'noortemplates' ); ?></span>
			</div>
		</div>
	<?php endif; ?>
</div>
