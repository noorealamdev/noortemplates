<?php
/**
 * Tabs block server render.
 *
 * Builds the tab list from the inner tab blocks' attributes and wraps the
 * pre-rendered panels ($content) with the Interactivity API context.
 *
 * @package NoorTemplates
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Rendered inner blocks (the panels).
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$noortemplates_tabs = array();

foreach ( $block->parsed_block['innerBlocks'] as $noortemplates_inner ) {
	$noortemplates_tabs[] = array(
		'uid'   => isset( $noortemplates_inner['attrs']['uid'] ) ? (string) $noortemplates_inner['attrs']['uid'] : '',
		'title' => isset( $noortemplates_inner['attrs']['title'] ) ? (string) $noortemplates_inner['attrs']['title'] : '',
	);
}

if ( empty( $noortemplates_tabs ) ) {
	return;
}

$noortemplates_context = array(
	'active' => array( 'id' => $noortemplates_tabs[0]['uid'] ),
);

// The initial state is rendered server-side (first tab active); the
// Interactivity API directives keep it in sync after hydration.
$noortemplates_panels = new WP_HTML_Tag_Processor( $content );
$noortemplates_index  = 0;

while ( $noortemplates_panels->next_tag( array( 'class_name' => 'noortemplates-tabs__panel' ) ) ) {
	if ( 0 !== $noortemplates_index ) {
		$noortemplates_panels->set_attribute( 'hidden', true );
	}
	++$noortemplates_index;
}

$noortemplates_content = $noortemplates_panels->get_updated_html();
?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>
	data-wp-interactive="noortemplates/tabs"
	data-wp-context="<?php echo esc_attr( (string) wp_json_encode( $noortemplates_context ) ); ?>"
>
	<div
		class="noortemplates-tabs__list"
		role="tablist"
		data-wp-on--keydown="actions.handleKeydown"
	>
		<?php foreach ( $noortemplates_tabs as $noortemplates_i => $noortemplates_tab ) : ?>
			<button
				type="button"
				id="noortemplates-tab-<?php echo esc_attr( $noortemplates_tab['uid'] ); ?>"
				class="noortemplates-tabs__tab<?php echo 0 === $noortemplates_i ? ' is-active' : ''; ?>"
				role="tab"
				aria-selected="<?php echo 0 === $noortemplates_i ? 'true' : 'false'; ?>"
				tabindex="<?php echo 0 === $noortemplates_i ? '0' : '-1'; ?>"
				aria-controls="noortemplates-tab-panel-<?php echo esc_attr( $noortemplates_tab['uid'] ); ?>"
				data-wp-context="<?php echo esc_attr( (string) wp_json_encode( array( 'tabId' => $noortemplates_tab['uid'] ) ) ); ?>"
				data-wp-on--click="actions.activate"
				data-wp-bind--aria-selected="state.isSelected"
				data-wp-bind--tabindex="state.tabIndex"
				data-wp-class--is-active="state.isSelected"
			>
				<?php echo esc_html( $noortemplates_tab['title'] ); ?>
			</button>
		<?php endforeach; ?>
	</div>
	<div class="noortemplates-tabs__panels">
		<?php echo $noortemplates_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
	</div>
</div>
