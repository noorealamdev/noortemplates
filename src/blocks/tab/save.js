import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { uid } = attributes;

	const blockProps = useBlockProps.save( {
		id: `noorblocks-tab-panel-${ uid }`,
		className: 'noorblocks-tabs__panel',
		role: 'tabpanel',
		'aria-labelledby': `noorblocks-tab-${ uid }`,
		'data-wp-context': JSON.stringify( { tabId: uid } ),
		'data-wp-bind--hidden': '!state.isSelected',
	} );

	return (
		<div { ...blockProps }>
			<InnerBlocks.Content />
		</div>
	);
}
