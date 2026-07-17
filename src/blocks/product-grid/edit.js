import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	ToggleControl,
	SelectControl,
	Disabled,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

const RELATION_OPTIONS = [
	{ label: __( 'Latest products', 'noortemplates' ), value: 'latest' },
	{
		label: __( 'Related to current product', 'noortemplates' ),
		value: 'related',
	},
	{
		label: __( 'Upsells for current product', 'noortemplates' ),
		value: 'upsells',
	},
	{
		label: __( 'Cross-sells for current product', 'noortemplates' ),
		value: 'cross-sells',
	},
];

export default function Edit( { attributes, setAttributes } ) {
	const {
		relation,
		productsToShow,
		columns,
		order,
		orderBy,
		showImage,
		showPrice,
		showAddToCart,
	} = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Query', 'noortemplates' ) }>
					<SelectControl
						label={ __( 'Products', 'noortemplates' ) }
						value={ relation }
						options={ RELATION_OPTIONS }
						onChange={ ( value ) =>
							setAttributes( { relation: value } )
						}
					/>
					<RangeControl
						label={ __( 'Number of products', 'noortemplates' ) }
						value={ productsToShow }
						onChange={ ( value ) =>
							setAttributes( { productsToShow: value } )
						}
						min={ 1 }
						max={ 12 }
					/>
					{ 'latest' === relation && (
						<>
							<SelectControl
								label={ __( 'Order by', 'noortemplates' ) }
								value={ orderBy }
								options={ [
									{
										label: __( 'Date', 'noortemplates' ),
										value: 'date',
									},
									{
										label: __( 'Title', 'noortemplates' ),
										value: 'title',
									},
									{
										label: __( 'Random', 'noortemplates' ),
										value: 'rand',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { orderBy: value } )
								}
							/>
							<SelectControl
								label={ __( 'Order', 'noortemplates' ) }
								value={ order }
								options={ [
									{
										label: __(
											'Descending',
											'noortemplates'
										),
										value: 'desc',
									},
									{
										label: __(
											'Ascending',
											'noortemplates'
										),
										value: 'asc',
									},
								] }
								onChange={ ( value ) =>
									setAttributes( { order: value } )
								}
							/>
						</>
					) }
				</PanelBody>

				<PanelBody title={ __( 'Layout', 'noortemplates' ) }>
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

				<PanelBody title={ __( 'Content', 'noortemplates' ) }>
					<ToggleControl
						label={ __( 'Product image', 'noortemplates' ) }
						checked={ showImage }
						onChange={ ( value ) =>
							setAttributes( { showImage: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Price', 'noortemplates' ) }
						checked={ showPrice }
						onChange={ ( value ) =>
							setAttributes( { showPrice: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Add to cart button', 'noortemplates' ) }
						checked={ showAddToCart }
						onChange={ ( value ) =>
							setAttributes( { showAddToCart: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender
						block="noortemplates/product-grid"
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		</>
	);
}
