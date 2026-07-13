import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

const TEMPLATE = [
	[ 'noorblocks/accordion-item' ],
	[ 'noorblocks/accordion-item' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { allowMultiple } = attributes;
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: [ 'noorblocks/accordion-item' ],
		template: TEMPLATE,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Accordion Settings', 'noorblocks' ) }>
					<ToggleControl
						label={ __(
							'Allow multiple open items',
							'noorblocks'
						) }
						help={ __(
							'When off, opening an item closes the others.',
							'noorblocks'
						) }
						checked={ allowMultiple }
						onChange={ ( value ) =>
							setAttributes( { allowMultiple: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...innerBlocksProps } />
		</>
	);
}
