import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	useSettings,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	RangeControl,
	ColorPalette,
	BaseControl,
	ToggleControl,
} from '@wordpress/components';
import { closeSmall } from '@wordpress/icons';

/*
 * Defaults for the dark-card reference look — shared here (editor preview)
 * and in render.php (front-end), so the two stay in sync. Applied as
 * conditional inline styles rather than a CSS class default: once the
 * merchant clears a value in the Inspector, there'd be no inline override
 * left to beat a class rule with, so the class default would keep showing
 * regardless of intent (same issue already fixed for Trust Badges).
 */
export const DEFAULT_CARD_BACKGROUND = '#1c1c1c';
export const DEFAULT_CARD_RADIUS = 20;
export const DEFAULT_CARD_PADDING = 28;

export default function Edit( { attributes, setAttributes } ) {
	const {
		items,
		columns,
		cardBackground,
		cardRadius,
		cardPadding,
		boxed,
		boxedWidth,
	} = attributes;
	const [ colors = [] ] = useSettings( 'color.palette' );
	const blockProps = useBlockProps( {
		style: { '--noortemplates-feature-cards-columns': columns },
	} );

	// Same pairing as render.php: the dark card default needs a paired
	// light text default, since plain inheritance has no guaranteed
	// contrast against it. Only applied when the merchant hasn't set their
	// own Text Color via the block's Color panel.
	const hasOwnTextColor =
		!! attributes.textColor || !! attributes.style?.color?.text;
	const textStyle = hasOwnTextColor ? undefined : { color: '#fff' };

	const updateItem = ( index, field, value ) => {
		const next = items.slice();
		next[ index ] = { ...next[ index ], [ field ]: value };
		setAttributes( { items: next } );
	};

	const addItem = () =>
		setAttributes( {
			items: [
				...items,
				{
					heading: __( 'New heading', 'noortemplates' ),
					text: __( 'Supporting text goes here.', 'noortemplates' ),
				},
			],
		} );

	const removeItem = ( index ) =>
		setAttributes( { items: items.filter( ( _item, i ) => i !== index ) } );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'noortemplates' ) }>
					<RangeControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Columns', 'noortemplates' ) }
						help={ __(
							'1 column reads as wide, horizontal cards. More columns make each card narrower and taller.',
							'noortemplates'
						) }
						value={ columns }
						onChange={ ( value ) =>
							setAttributes( { columns: value || 1 } )
						}
						min={ 1 }
						max={ 4 }
					/>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Boxed width', 'noortemplates' ) }
						checked={ boxed }
						onChange={ ( value ) => setAttributes( { boxed: value } ) }
						help={
							boxed
								? __(
										'Constrained to a max width and centered.',
										'noortemplates'
								  )
								: __(
										'Stretches the full width of its container.',
										'noortemplates'
								  )
						}
					/>
					{ boxed && (
						<RangeControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
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

				<PanelBody title={ __( 'Card', 'noortemplates' ) }>
					<BaseControl
						__nextHasNoMarginBottom
						label={ __( 'Background', 'noortemplates' ) }
					>
						<ColorPalette
							colors={ colors }
							value={ cardBackground }
							onChange={ ( value ) =>
								setAttributes( { cardBackground: value || '' } )
							}
						/>
					</BaseControl>
					<RangeControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Corner radius', 'noortemplates' ) }
						value={ cardRadius }
						onChange={ ( value ) =>
							setAttributes( { cardRadius: value || 0 } )
						}
						min={ 0 }
						max={ 40 }
					/>
					<RangeControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Padding', 'noortemplates' ) }
						value={ cardPadding }
						onChange={ ( value ) =>
							setAttributes( { cardPadding: value || 0 } )
						}
						min={ 0 }
						max={ 60 }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div
					className={
						'noortemplates-feature-cards__list' +
						( boxed ? ' is-boxed' : '' )
					}
					style={ boxed ? { maxWidth: boxedWidth } : undefined }
				>
					{ items.map( ( item, index ) => (
						<div
							className="noortemplates-feature-cards__item"
							key={ index }
							style={ {
								backgroundColor:
									cardBackground || DEFAULT_CARD_BACKGROUND,
								borderRadius:
									( cardRadius || DEFAULT_CARD_RADIUS ) + 'px',
								padding:
									( cardPadding || DEFAULT_CARD_PADDING ) + 'px',
							} }
						>
							<Button
								className="noortemplates-feature-cards__remove"
								icon={ closeSmall }
								label={ __( 'Remove card', 'noortemplates' ) }
								onClick={ () => removeItem( index ) }
							/>
							<RichText
								tagName="div"
								className="noortemplates-feature-cards__heading"
								style={ textStyle }
								value={ item.heading }
								onChange={ ( value ) =>
									updateItem( index, 'heading', value )
								}
								placeholder={ __( 'Heading', 'noortemplates' ) }
							/>
							<RichText
								tagName="div"
								className="noortemplates-feature-cards__text"
								style={ textStyle }
								value={ item.text }
								onChange={ ( value ) =>
									updateItem( index, 'text', value )
								}
								placeholder={ __( 'Supporting text', 'noortemplates' ) }
							/>
						</div>
					) ) }
				</div>
				<Button variant="secondary" onClick={ addItem }>
					{ __( 'Add card', 'noortemplates' ) }
				</Button>
			</div>
		</>
	);
}
