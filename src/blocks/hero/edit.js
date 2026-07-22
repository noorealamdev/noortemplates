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
	RangeControl,
	ColorPalette,
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import {
	getBackgroundStyle,
	hasBackgroundVideo,
	hasOverlay,
} from './background';
import ProControl from '../../utils/pro-control';
import '../../utils/pro-control.scss';

const ACTIONS_TEMPLATE = [ [ 'noortemplates/button' ] ];

export default function Edit( { attributes, setAttributes } ) {
	const {
		heading,
		subheading,
		backgroundType,
		backgroundImage,
		backgroundVideo,
		overlayColor,
		overlayOpacity,
		textAlign,
		headingFontSize,
		subheadingFontSize,
		boxed,
		boxedWidth,
	} = attributes;
	const [ fontSizes ] = useSettings( 'typography.fontSizes' );
	const [ colors = [] ] = useSettings( 'color.palette' );

	const blockProps = useBlockProps( {
		className: textAlign ? `has-text-align-${ textAlign }` : undefined,
		style: getBackgroundStyle(
			backgroundType,
			backgroundImage,
			backgroundVideo,
			overlayOpacity
		),
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

	const onSelectVideo = ( media ) =>
		setAttributes( {
			backgroundVideo: { url: media.url, id: media.id },
		} );

	const onRemoveVideo = () =>
		setAttributes( { backgroundVideo: { url: '', id: 0 } } );

	const showVideo = hasBackgroundVideo( backgroundType, backgroundVideo );
	const showOverlay = hasOverlay( overlayOpacity );

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
				<PanelBody title={ __( 'Background', 'noortemplates' ) }>
					<ToggleGroupControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Type', 'noortemplates' ) }
						value={ backgroundType }
						onChange={ ( value ) =>
							setAttributes( { backgroundType: value } )
						}
					>
						<ToggleGroupControlOption
							value="image"
							label={ __( 'Image', 'noortemplates' ) }
						/>
						<ToggleGroupControlOption
							value="video"
							label={ __( 'Video', 'noortemplates' ) }
						/>
					</ToggleGroupControl>

					{ 'video' === backgroundType ? (
						<ProControl
							feature="hero_video_background"
							message={ __(
								'Use a video background.',
								'noortemplates'
							) }
						>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ onSelectVideo }
									allowedTypes={ [ 'video' ] }
									value={ backgroundVideo?.id }
									render={ ( { open } ) => (
										<Button
											variant="secondary"
											onClick={ open }
											className="noortemplates-hero__select-image"
										>
											{ backgroundVideo?.url
												? __(
														'Replace video',
														'noortemplates'
												  )
												: __(
														'Select video',
														'noortemplates'
												  ) }
										</Button>
									) }
								/>
							</MediaUploadCheck>
							{ backgroundVideo?.url && (
								<Button
									variant="link"
									isDestructive
									onClick={ onRemoveVideo }
								>
									{ __( 'Remove video', 'noortemplates' ) }
								</Button>
							) }
						</ProControl>
					) : (
						<>
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
						</>
					) }
				</PanelBody>

				<PanelBody title={ __( 'Overlay', 'noortemplates' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Opacity', 'noortemplates' ) }
						help={ __(
							'Darkens the background so text stays readable.',
							'noortemplates'
						) }
						value={ overlayOpacity }
						onChange={ ( value ) =>
							setAttributes( { overlayOpacity: value ?? 0 } )
						}
						min={ 0 }
						max={ 100 }
					/>
					{ showOverlay && (
						<BaseControl
							__nextHasNoMarginBottom
							label={ __( 'Color', 'noortemplates' ) }
						>
							<ColorPalette
								colors={ colors }
								value={ overlayColor }
								onChange={ ( value ) =>
									setAttributes( {
										overlayColor: value || '#000000',
									} )
								}
							/>
						</BaseControl>
					) }
				</PanelBody>

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

				<PanelBody title={ __( 'Layout', 'noortemplates' ) } initialOpen={ false }>
					<ToggleControl
						label={ __( 'Boxed width', 'noortemplates' ) }
						checked={ boxed }
						onChange={ ( value ) => setAttributes( { boxed: value } ) }
						help={
							boxed
								? __(
										'Heading, subheading and buttons are constrained to a max width and centered.',
										'noortemplates'
								  )
								: __(
										'Heading, subheading and buttons stretch the full width of the hero.',
										'noortemplates'
								  )
						}
					/>
					{ boxed && (
						<RangeControl
							label={ __( 'Max width (px)', 'noortemplates' ) }
							value={ boxedWidth }
							onChange={ ( value ) =>
								setAttributes( { boxedWidth: value } )
							}
							min={ 480 }
							max={ 1800 }
							step={ 10 }
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ showVideo && (
					<video
						className="noortemplates-hero__bg-video"
						src={ backgroundVideo.url }
						autoPlay
						muted
						loop
						playsInline
					/>
				) }
				{ showOverlay && (
					<div
						className="noortemplates-hero__overlay"
						style={ {
							backgroundColor: overlayColor,
							opacity: overlayOpacity / 100,
						} }
					/>
				) }
				<div
					className={
						'noortemplates-hero__content' +
						( boxed ? ' is-boxed' : '' )
					}
					style={ boxed ? { maxWidth: boxedWidth } : undefined }
				>
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
			</div>
		</>
	);
}
