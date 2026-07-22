import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { allowMultiple, boxed, boxedWidth } = attributes;

	const blockProps = useBlockProps.save( {
		'data-wp-interactive': 'noortemplates/accordion',
		'data-wp-context': JSON.stringify( {
			open: { ids: [] },
			allowMultiple,
		} ),
		className: boxed ? 'is-boxed' : undefined,
		style: boxed ? { maxWidth: boxedWidth } : undefined,
	} );

	return (
		<div { ...blockProps }>
			<InnerBlocks.Content />
		</div>
	);
}
