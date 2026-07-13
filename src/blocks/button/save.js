import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { text, url, opensInNewTab, textAlign } = attributes;
	const blockProps = useBlockProps.save( {
		className: textAlign ? `has-text-align-${ textAlign }` : undefined,
	} );

	return (
		<div { ...blockProps }>
			<RichText.Content
				tagName="a"
				className="noorblocks-button__link"
				value={ text }
				href={ url || undefined }
				target={ opensInNewTab ? '_blank' : undefined }
				rel={ opensInNewTab ? 'noopener noreferrer' : undefined }
			/>
		</div>
	);
}
