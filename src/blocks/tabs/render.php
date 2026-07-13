<?php
/**
 * Tabs block server render.
 *
 * Builds the tab list from the inner tab blocks' attributes and wraps the
 * pre-rendered panels ($content) with the Interactivity API context.
 *
 * @package NoorBlocks
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Rendered inner blocks (the panels).
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$noorblocks_tabs = array();

foreach ( $block->parsed_block['innerBlocks'] as $noorblocks_inner ) {
	$noorblocks_tabs[] = array(
		'uid'   => isset( $noorblocks_inner['attrs']['uid'] ) ? (string) $noorblocks_inner['attrs']['uid'] : '',
		'title' => isset( $noorblocks_inner['attrs']['title'] ) ? (string) $noorblocks_inner['attrs']['title'] : '',
	);
}

if ( empty( $noorblocks_tabs ) ) {
	return;
}

$noorblocks_context = array(
	'active' => array( 'id' => $noorblocks_tabs[0]['uid'] ),
);

// The initial state is rendered server-side (first tab active); the
// Interactivity API directives keep it in sync after hydration.
$noorblocks_panels = new WP_HTML_Tag_Processor( $content );
$noorblocks_index  = 0;

while ( $noorblocks_panels->next_tag( array( 'class_name' => 'noorblocks-tabs__panel' ) ) ) {
	if ( 0 !== $noorblocks_index ) {
		$noorblocks_panels->set_attribute( 'hidden', true );
	}
	++$noorblocks_index;
}

$noorblocks_content = $noorblocks_panels->get_updated_html();
?>
<div
	<?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>
	data-wp-interactive="noorblocks/tabs"
	data-wp-context="<?php echo esc_attr( (string) wp_json_encode( $noorblocks_context ) ); ?>"
>
	<div
		class="noorblocks-tabs__list"
		role="tablist"
		data-wp-on--keydown="actions.handleKeydown"
	>
		<?php foreach ( $noorblocks_tabs as $noorblocks_i => $noorblocks_tab ) : ?>
			<button
				type="button"
				id="noorblocks-tab-<?php echo esc_attr( $noorblocks_tab['uid'] ); ?>"
				class="noorblocks-tabs__tab<?php echo 0 === $noorblocks_i ? ' is-active' : ''; ?>"
				role="tab"
				aria-selected="<?php echo 0 === $noorblocks_i ? 'true' : 'false'; ?>"
				tabindex="<?php echo 0 === $noorblocks_i ? '0' : '-1'; ?>"
				aria-controls="noorblocks-tab-panel-<?php echo esc_attr( $noorblocks_tab['uid'] ); ?>"
				data-wp-context="<?php echo esc_attr( (string) wp_json_encode( array( 'tabId' => $noorblocks_tab['uid'] ) ) ); ?>"
				data-wp-on--click="actions.activate"
				data-wp-bind--aria-selected="state.isSelected"
				data-wp-bind--tabindex="state.tabIndex"
				data-wp-class--is-active="state.isSelected"
			>
				<?php echo esc_html( $noorblocks_tab['title'] ); ?>
			</button>
		<?php endforeach; ?>
	</div>
	<div class="noorblocks-tabs__panels">
		<?php echo $noorblocks_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
	</div>
</div>
