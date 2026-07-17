import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	useSettings,
	RichText,
	InspectorControls,
	BlockControls,
	AlignmentControl,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	Button,
	BaseControl,
	FontSizePicker,
} from '@wordpress/components';

const ACTIONS_TEMPLATE = [ [ 'noortemplates/button' ] ];

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

export default function Edit( { attributes, setAttributes } ) {
	const {
		heading,
		subheading,
		backgroundImage,
		textAlign,
		headingFontSize,
		subheadingFontSize,
	} = attributes;
	const [ fontSizes ] = useSettings( 'typography.fontSizes' );

	const blockProps = useBlockProps( {
		className: textAlign ? `has-text-align-${ textAlign }` : undefined,
		style: getBackgroundStyle( backgroundImage ),
	} );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'noortemplates-hero__actions' },
		{
			allowedBlocks: [ 'noortemplates/button', 'core/buttons' ],
			template: ACTIONS_TEMPLATE,
			templateInsertUpdatesSelection: false,
		}
	);

	const onSelectImage = ( media ) =>
		setAttributes( {
			backgroundImage: { url: media.url, id: media.id },
		} );

	const onRemoveImage = () =>
		setAttributes( { backgroundImage: { url: '', id: 0 } } );

	return (
		<>
			<BlockControls>
				<AlignmentControl
					value={ textAlign }
					onChange={ ( value ) =>
						setAttributes( { textAlign: value || 'center' } )
					}
				/>
			</BlockControls>

			<InspectorControls>
				<PanelBody title={ __( 'Typography', 'noortemplates' ) }>
					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Heading', 'noortemplates' ) }
					>
						<FontSizePicker
							__nextHasNoMarginBottom
							value={ headingFontSize }
							fontSizes={ fontSizes }
							onChange={ ( value ) =>
								setAttributes( {
									headingFontSize: value || '',
								} )
							}
						/>
					</BaseControl>
					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Subheading', 'noortemplates' ) }
					>
						<FontSizePicker
							__nextHasNoMarginBottom
							value={ subheadingFontSize }
							fontSizes={ fontSizes }
							onChange={ ( value ) =>
								setAttributes( {
									subheadingFontSize: value || '',
								} )
							}
						/>
					</BaseControl>
				</PanelBody>

				<PanelBody title={ __( 'Background', 'noortemplates' ) }>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ onSelectImage }
							allowedTypes={ [ 'image' ] }
							value={ backgroundImage?.id }
							render={ ( { open } ) => (
								<Button
									variant="secondary"
									onClick={ open }
									className="noortemplates-hero__select-image"
								>
									{ backgroundImage?.url
										? __(
												'Replace image',
												'noortemplates'
										  )
										: __(
												'Select image',
												'noortemplates'
										  ) }
								</Button>
							) }
						/>
					</MediaUploadCheck>
					{ backgroundImage?.url && (
						<Button
							variant="link"
							isDestructive
							onClick={ onRemoveImage }
						>
							{ __( 'Remove image', 'noortemplates' ) }
						</Button>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<RichText
					tagName="h1"
					className="noortemplates-hero__heading"
					style={
						headingFontSize
							? { fontSize: headingFontSize }
							: undefined
					}
					value={ heading }
					onChange={ ( value ) =>
						setAttributes( { heading: value } )
					}
					placeholder={ __( 'Your Hero Heading', 'noortemplates' ) }
				/>
				<RichText
					tagName="p"
					className="noortemplates-hero__subheading"
					style={
						subheadingFontSize
							? { fontSize: subheadingFontSize }
							: undefined
					}
					value={ subheading }
					onChange={ ( value ) =>
						setAttributes( { subheading: value } )
					}
					placeholder={ __(
						'Add a supporting subheading…',
						'noortemplates'
					) }
				/>
				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
