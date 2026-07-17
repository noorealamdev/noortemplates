import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

const TEMPLATE = [
	[ 'noortemplates/accordion-item' ],
	[ 'noortemplates/accordion-item' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { allowMultiple } = attributes;
	const blockProps = useBlockProps();
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
			</InspectorControls>

			<div { ...innerBlocksProps } />
		</>
	);
}
