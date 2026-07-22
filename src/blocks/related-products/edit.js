import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, ToggleControl } from '@wordpress/components';
import WooPlaceholder from '../../utils/woo-placeholder';

export default function Edit( { attributes, setAttributes } ) {
	const { productsToShow, columns, boxed, boxedWidth } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Query', 'noortemplates' ) }>
					<RangeControl
						label={ __( 'Number of products', 'noortemplates' ) }
						value={ productsToShow }
						onChange={ ( value ) =>
							setAttributes( { productsToShow: value } )
						}
						min={ 1 }
						max={ 12 }
					/>
					<RangeControl
						label={ __( 'Columns', 'noortemplates' ) }
						value={ columns }
						onChange={ ( value ) =>
							setAttributes( { columns: value } )
						}
						min={ 1 }
						max={ 4 }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Layout', 'noortemplates' ) } initialOpen={ false }>
					<ToggleControl
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

			<div { ...useBlockProps() }>
				<WooPlaceholder
					icon="networking"
					label={ __( 'Related Products', 'noortemplates' ) }
					instructions={ __(
						"Displays WooCommerce's related products for the current product. Preview appears on the product page.",
						'noortemplates'
					) }
				/>
			</div>
		</>
	);
}
