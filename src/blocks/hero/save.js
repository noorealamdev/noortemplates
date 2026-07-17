import { useBlockProps, InnerBlocks, RichText } from '@wordpress/block-editor';

function getBackgroundStyle( backgroundImage ) {
	if ( ! backgroundImage || ! backgroundImage.url ) {
		return {};
	}

	return {
		backgroundImage: `url(${ backgroundImage.url })`,
		backgroundSize: 'cover',
		backgroundPosition: 'center',
	};
}

export default function save( { attributes } ) {
	const {
		heading,
		subheading,
		backgroundImage,
		textAlign,
		headingFontSize,
		subheadingFontSize,
	} = attributes;
	const blockProps = useBlockProps.save( {
		className: textAlign ? `has-text-align-${ textAlign }` : undefined,
		style: getBackgroundStyle( backgroundImage ),
	} );

	return (
		<div { ...blockProps }>
			<RichText.Content
				tagName="h1"
				className="noortemplates-hero__heading"
				style={
					headingFontSize
						? { fontSize: headingFontSize }
						: undefined
				}
				value={ heading }
			/>
			<RichText.Content
				tagName="p"
				className="noortemplates-hero__subheading"
				style={
					subheadingFontSize
						? { fontSize: subheadingFontSize }
						: undefined
				}
				value={ subheading }
			/>
			<div className="noortemplates-hero__actions">
				<InnerBlocks.Content />
			</div>
		</div>
	);
}
