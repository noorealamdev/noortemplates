<?php
/**
 * Product Category block server render.
 *
 * @package NoorTemplates
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$noortemplates_product_id = get_the_ID();
$noortemplates_terms      = get_the_terms( $noortemplates_product_id, 'product_cat' );

if ( empty( $noortemplates_terms ) || is_wp_error( $noortemplates_terms ) ) {
	return;
}

$noortemplates_term = $noortemplates_terms[0];

/*
 * This block ships with an opinionated "kicker label" default look (small,
 * uppercase, letter-spaced, muted) — applied as a conditional inline style
 * rather than a plain CSS class, since a class-based default can never be
 * fully "cleared": once the user changes a value via the Typography/Color
 * panel, that panel's own inline style naturally wins over ours; skipping
 * each default the moment the user has set their own keeps every one of
 * these controls genuinely editable instead of silently dead.
 */
$noortemplates_has_own_font_size      = ! empty( $attributes['fontSize'] ) || ! empty( $attributes['style']['typography']['fontSize'] );
$noortemplates_has_own_letter_spacing = ! empty( $attributes['style']['typography']['letterSpacing'] );
$noortemplates_has_own_text_transform = ! empty( $attributes['style']['typography']['textTransform'] );
$noortemplates_has_own_font_weight    = ! empty( $attributes['style']['typography']['fontWeight'] );
$noortemplates_has_own_text_color     = ! empty( $attributes['textColor'] ) || ! empty( $attributes['style']['color']['text'] );

$noortemplates_default_style = '';

if ( ! $noortemplates_has_own_font_size ) {
	$noortemplates_default_style .= 'font-size:0.8rem;';
}

if ( ! $noortemplates_has_own_letter_spacing ) {
	$noortemplates_default_style .= 'letter-spacing:0.06em;';
}

if ( ! $noortemplates_has_own_text_transform ) {
	$noortemplates_default_style .= 'text-transform:uppercase;';
}

if ( ! $noortemplates_has_own_font_weight ) {
	$noortemplates_default_style .= 'font-weight:600;';
}

if ( ! $noortemplates_has_own_text_color ) {
	$noortemplates_default_style .= 'color:#6b7280;';
}

$noortemplates_wrapper = get_block_wrapper_attributes(
	$noortemplates_default_style ? array( 'style' => $noortemplates_default_style ) : array()
);
?>
<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<a href="<?php echo esc_url( get_term_link( $noortemplates_term ) ); ?>">
		<?php echo esc_html( $noortemplates_term->name ); ?>
	</a>
</div>
