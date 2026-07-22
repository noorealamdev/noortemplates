import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	RangeControl,
	TextControl,
	ToggleControl,
	BaseControl,
} from '@wordpress/components';
import WooPlaceholder from '../../utils/woo-placeholder';

export default function Edit( { attributes, setAttributes } ) {
	const {
		type,
		stockThreshold,
		stockMessage,
		showIcon,
		countdownSource,
		customDate,
		countdownLabel,
		hideWhenExpired,
		expiredMessage,
		boxed,
		boxedWidth,
	} = attributes;

	const instructions =
		type === 'stock'
			? __(
					'Shows a low-stock warning based on the live stock level once it drops to or below the threshold. Preview appears on the product page.',
					'noortemplates'
			  )
			: __(
					'Counts down to the sale end date or a custom deadline. Preview appears on the product page.',
					'noortemplates'
			  );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Urgency type', 'noortemplates' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Type', 'noortemplates' ) }
						value={ type }
						options={ [
							{
								label: __( 'Low stock warning', 'noortemplates' ),
								value: 'stock',
							},
							{
								label: __( 'Countdown timer', 'noortemplates' ),
								value: 'countdown',
							},
						] }
						onChange={ ( value ) => setAttributes( { type: value } ) }
					/>
				</PanelBody>

				{ type === 'stock' && (
					<PanelBody title={ __( 'Low stock settings', 'noortemplates' ) }>
						<RangeControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __(
								'Show when stock is at or below',
								'noortemplates'
							) }
							value={ stockThreshold }
							onChange={ ( value ) =>
								setAttributes( { stockThreshold: value || 1 } )
							}
							min={ 1 }
							max={ 50 }
						/>
						<TextControl
							__nextHasNoMarginBottom
							label={ __( 'Message', 'noortemplates' ) }
							help={ __(
								'Use {stock} where the remaining quantity should appear.',
								'noortemplates'
							) }
							value={ stockMessage }
							onChange={ ( value ) =>
								setAttributes( { stockMessage: value } )
							}
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Show icon', 'noortemplates' ) }
							checked={ showIcon }
							onChange={ ( value ) =>
								setAttributes( { showIcon: value } )
							}
						/>
					</PanelBody>
				) }

				{ type === 'countdown' && (
					<PanelBody title={ __( 'Countdown settings', 'noortemplates' ) }>
						<SelectControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Count down to', 'noortemplates' ) }
							value={ countdownSource }
							options={ [
								{
									label: __(
										"Product's scheduled sale end date",
										'noortemplates'
									),
									value: 'sale',
								},
								{
									label: __( 'Custom date & time', 'noortemplates' ),
									value: 'custom',
								},
							] }
							onChange={ ( value ) =>
								setAttributes( { countdownSource: value } )
							}
						/>
						{ countdownSource === 'sale' && (
							<p className="components-base-control__help">
								{ __(
									'Only shows while the product has an active sale with a scheduled end date set in Product data → General.',
									'noortemplates'
								) }
							</p>
						) }
						{ countdownSource === 'custom' && (
							<BaseControl
								__nextHasNoMarginBottom
								label={ __( 'Deadline', 'noortemplates' ) }
								help={ __( "Uses your site's timezone.", 'noortemplates' ) }
							>
								<input
									type="datetime-local"
									className="components-text-control__input"
									value={ customDate }
									onChange={ ( event ) =>
										setAttributes( { customDate: event.target.value } )
									}
								/>
							</BaseControl>
						) }
						<TextControl
							__nextHasNoMarginBottom
							label={ __( 'Label', 'noortemplates' ) }
							value={ countdownLabel }
							onChange={ ( value ) =>
								setAttributes( { countdownLabel: value } )
							}
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Hide once expired', 'noortemplates' ) }
							checked={ hideWhenExpired }
							onChange={ ( value ) =>
								setAttributes( { hideWhenExpired: value } )
							}
							help={
								hideWhenExpired
									? __(
											'The block disappears once the deadline passes.',
											'noortemplates'
									  )
									: __(
											'Shows the message below once the deadline passes.',
											'noortemplates'
									  )
							}
						/>
						{ ! hideWhenExpired && (
							<TextControl
								__nextHasNoMarginBottom
								label={ __( 'Expired message', 'noortemplates' ) }
								value={ expiredMessage }
								onChange={ ( value ) =>
									setAttributes( { expiredMessage: value } )
								}
							/>
						) }
					</PanelBody>
				) }

				<PanelBody title={ __( 'Layout', 'noortemplates' ) } initialOpen={ false }>
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
							min={ 320 }
							max={ 1800 }
							step={ 10 }
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div
				{ ...useBlockProps( {
					className: boxed ? 'is-boxed' : undefined,
					style: boxed ? { maxWidth: boxedWidth } : undefined,
				} ) }
			>
				<WooPlaceholder
					icon="warning"
					label={ __( 'Urgency & Countdown', 'noortemplates' ) }
					instructions={ instructions }
				/>
			</div>
		</>
	);
}
