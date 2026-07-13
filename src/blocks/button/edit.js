import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	BlockControls,
	AlignmentControl,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
	const { text, url, opensInNewTab, textAlign } = attributes;
	const blockProps = useBlockProps( {
		className: textAlign ? `has-text-align-${ textAlign }` : undefined,
	} );

	return (
		<>
			<BlockControls>
				<AlignmentControl
					value={ textAlign }
					onChange={ ( value ) =>
						setAttributes( { textAlign: value } )
					}
				/>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ __( 'Link Settings', 'noorblocks' ) }>
					<TextControl
						label={ __( 'URL', 'noorblocks' ) }
						value={ url }
						onChange={ ( value ) =>
							setAttributes( { url: value } )
						}
						placeholder="https://"
					/>
					<ToggleControl
						label={ __( 'Open in new tab', 'noorblocks' ) }
						checked={ opensInNewTab }
						onChange={ ( value ) =>
							setAttributes( { opensInNewTab: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<RichText
					tagName="a"
					className="noorblocks-button__link"
					value={ text }
					onChange={ ( value ) => setAttributes( { text: value } ) }
					placeholder={ __( 'Add text…', 'noorblocks' ) }
					allowedFormats={ [ 'core/bold', 'core/italic' ] }
				/>
			</div>
		</>
	);
}
