<?php
/**
 * Feature Cards block server render.
 *
 * @package NoorTemplates
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$noortemplates_items = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();

if ( empty( $noortemplates_items ) ) {
	return;
}

$noortemplates_columns = ! empty( $attributes['columns'] ) ? max( 1, min( 4, (int) $attributes['columns'] ) ) : 1;

/*
 * Dark-card reference defaults — same values as edit.js's DEFAULT_CARD_*
 * constants (PHP/JS can't share a module, so these are kept in sync by
 * hand). Applied as conditional inline styles per card, not a CSS class
 * default: once the merchant clears a value via the block's own "Card"
 * panel, there's no inline override left to beat a class rule with, so a
 * class-based default would keep showing regardless of intent (the exact
 * issue already found and fixed for Trust Badges this session).
 */
$noortemplates_card_background = ! empty( $attributes['cardBackground'] ) ? $attributes['cardBackground'] : '#1c1c1c';
$noortemplates_card_radius     = ! empty( $attributes['cardRadius'] ) ? (int) $attributes['cardRadius'] : 20;
$noortemplates_card_padding    = ! empty( $attributes['cardPadding'] ) ? (int) $attributes['cardPadding'] : 28;

$noortemplates_card_style = sprintf(
	'background-color:%s;border-radius:%dpx;padding:%dpx;',
	$noortemplates_card_background,
	$noortemplates_card_radius,
	$noortemplates_card_padding
);

/*
 * The dark card background needs a paired light text default — plain
 * `color: inherit` on the heading/text would otherwise pick up whatever
 * ambient color happens to be active on the page, which has no guaranteed
 * contrast against a dark card. Only applied when the merchant hasn't set
 * their own Text Color (their choice always wins once set, same
 * conditional-inline-default pattern as the card background above).
 */
$noortemplates_has_own_text_color = ! empty( $attributes['textColor'] ) || ! empty( $attributes['style']['color']['text'] );
$noortemplates_text_style         = $noortemplates_has_own_text_color ? '' : 'color:#fff;';

$noortemplates_wrapper = get_block_wrapper_attributes(
	array( 'style' => '--noortemplates-feature-cards-columns:' . $noortemplates_columns . ';' )
);
?>
<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<div class="noortemplates-feature-cards__list">
		<?php foreach ( $noortemplates_items as $noortemplates_item ) : ?>
			<div class="noortemplates-feature-cards__item" style="<?php echo esc_attr( $noortemplates_card_style ); ?>">
				<div class="noortemplates-feature-cards__heading" style="<?php echo esc_attr( $noortemplates_text_style ); ?>"><?php echo wp_kses_post( $noortemplates_item['heading'] ?? '' ); ?></div>
				<div class="noortemplates-feature-cards__text" style="<?php echo esc_attr( $noortemplates_text_style ); ?>"><?php echo wp_kses_post( $noortemplates_item['text'] ?? '' ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
