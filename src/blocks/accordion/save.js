import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { allowMultiple } = attributes;

	const blockProps = useBlockProps.save( {
		'data-wp-interactive': 'noortemplates/accordion',
		'data-wp-context': JSON.stringify( {
			open: { ids: [] },
			allowMultiple,
		} ),
	} );

	return (
		<div { ...blockProps }>
			<InnerBlocks.Content />
		</div>
	);
}
