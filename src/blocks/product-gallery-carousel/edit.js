import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import WooPlaceholder from '../../utils/woo-placeholder';

export default function Edit( { attributes, setAttributes } ) {
	const { showThumbnails } = attributes;

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Gallery', 'noortemplates' ) } initialOpen>
					<ToggleControl
						label={ __( 'Show thumbnail strip', 'noortemplates' ) }
						checked={ showThumbnails }
						onChange={ ( value ) =>
							setAttributes( { showThumbnails: value } )
						}
						help={
							showThumbnails
								? __(
										'A row of clickable thumbnails appears below the main image.',
										'noortemplates'
								  )
								: __(
										'Only dots indicate the current image.',
										'noortemplates'
								  )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<WooPlaceholder
				icon="images-alt2"
				label={ __( 'Product Gallery Carousel', 'noortemplates' ) }
				instructions={ __(
					"A swipeable carousel of the current product's own images. Preview appears on the product page.",
					'noortemplates'
				) }
			/>
		</div>
	);
}
