import { useBlockProps, InnerBlocks, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { title } = attributes;
	const blockProps = useBlockProps.save();

	return (
		<div { ...blockProps }>
			<RichText.Content
				tagName="h2"
				className="noortemplates-faq__title"
				value={ title }
			/>
			<div className="noortemplates-faq__accordion">
				<InnerBlocks.Content />
			</div>
		</div>
	);
}
