<?php
/**
 * Icon List block server render.
 *
 * @package NoorTemplates
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$noortemplates_rows = isset( $attributes['rows'] ) && is_array( $attributes['rows'] ) ? $attributes['rows'] : array();

if ( empty( $noortemplates_rows ) ) {
	return;
}

$noortemplates_boxed       = ! isset( $attributes['boxed'] ) || (bool) $attributes['boxed'];
$noortemplates_boxed_width = isset( $attributes['boxedWidth'] ) ? absint( $attributes['boxedWidth'] ) : 800;
?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<ul
		class="noortemplates-icon-list__list<?php echo $noortemplates_boxed ? ' is-boxed' : ''; ?>"
		<?php if ( $noortemplates_boxed ) : ?>
			style="max-width:<?php echo esc_attr( $noortemplates_boxed_width ); ?>px"
		<?php endif; ?>
	>
		<?php foreach ( $noortemplates_rows as $noortemplates_row ) : ?>
			<li class="noortemplates-icon-list__row">
				<span class="noortemplates-icon-list__icon">
					<?php echo \NoorTemplates\Blocks\WP_Icon_Registry::render( $noortemplates_row['icon'] ?? 'shield' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- self-escaping, fixed internal registry. ?>
				</span>
				<span class="noortemplates-icon-list__text"><?php echo wp_kses_post( $noortemplates_row['text'] ?? '' ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
