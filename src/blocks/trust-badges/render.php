<?php
/**
 * Trust Badges block server render.
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

/*
 * No forced default background/text-color/radius here — same approach as
 * Icon List. WordPress can't distinguish "cleared" from "never set" for a
 * color attribute, so any baked-in default can never be fully suppressed
 * by the user; plain color/border supports with nothing hardcoded avoids
 * the problem entirely. The soft rounded card look ships as starter
 * *content* instead (see the `items` default in block.json) — pick this
 * block from the inserter and its own default color/border/spacing
 * values (set once via Inspector, no code needed) give the same look.
 */
$noortemplates_wrapper = get_block_wrapper_attributes();

$noortemplates_boxed       = ! isset( $attributes['boxed'] ) || (bool) $attributes['boxed'];
$noortemplates_boxed_width = isset( $attributes['boxedWidth'] ) ? absint( $attributes['boxedWidth'] ) : 1200;
?>
<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<ul
		class="noortemplates-trust-badges__list<?php echo $noortemplates_boxed ? ' is-boxed' : ''; ?>"
		<?php if ( $noortemplates_boxed ) : ?>
			style="max-width:<?php echo esc_attr( $noortemplates_boxed_width ); ?>px"
		<?php endif; ?>
	>
		<?php foreach ( $noortemplates_items as $noortemplates_item ) : ?>
			<li class="noortemplates-trust-badges__item">
				<?php if ( ! empty( $noortemplates_item['icon'] ) ) : ?>
					<span class="noortemplates-trust-badges__icon">
						<?php echo \NoorTemplates\Blocks\WP_Icon_Registry::render( $noortemplates_item['icon'], 'nt-shieldCheck' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- self-escaping, fixed internal registry. ?>
					</span>
				<?php endif; ?>
				<span class="noortemplates-trust-badges__label"><?php echo wp_kses_post( $noortemplates_item['label'] ?? '' ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
