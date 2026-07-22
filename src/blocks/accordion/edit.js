import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl, RangeControl } from '@wordpress/components';

const TEMPLATE = [
	[ 'noortemplates/accordion-item' ],
	[ 'noortemplates/accordion-item' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { allowMultiple, boxed, boxedWidth } = attributes;
	const blockProps = useBlockProps( {
		className: boxed ? 'is-boxed' : undefined,
		style: boxed ? { maxWidth: boxedWidth } : undefined,
	} );
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: [ 'noortemplates/accordion-item' ],
		template: TEMPLATE,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Accordion Settings', 'noortemplates' ) }
				>
					<ToggleControl
						label={ __(
							'Allow multiple open items',
							'noortemplates'
						) }
						help={ __(
							'When off, opening an item closes the others.',
							'noortemplates'
						) }
						checked={ allowMultiple }
						onChange={ ( value ) =>
							setAttributes( { allowMultiple: value } )
						}
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

			<div { ...innerBlocksProps } />
		</>
	);
}
