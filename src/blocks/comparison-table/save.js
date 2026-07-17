import { useBlockProps, InnerBlocks, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { title } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div { ...blockProps }>
			<RichText.Content
				tagName="h2"
				className="noortemplates-comparison-table__title"
				value={ title }
			/>
			<div className="noortemplates-comparison-table__table">
				<InnerBlocks.Content />
			</div>
		</div>
	);
}
