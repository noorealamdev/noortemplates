import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';

import useUniqueId from '../../utils/use-unique-id';

const TEMPLATE = [ [ 'core/paragraph' ] ];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, uid } = attributes;

	useUniqueId( clientId, uid, setAttributes );

	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'noorblocks-accordion__panel' },
		{ template: TEMPLATE }
	);

	return (
		<div { ...blockProps }>
			<div className="noorblocks-accordion__heading">
				<div className="noorblocks-accordion__toggle">
					<RichText
						tagName="span"
						className="noorblocks-accordion__title"
						value={ title }
						onChange={ ( value ) =>
							setAttributes( { title: value } )
						}
						placeholder={ __( 'Add a title…', 'noorblocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					<span
						className="noorblocks-accordion__icon"
						aria-hidden="true"
					></span>
				</div>
			</div>
			<div { ...innerBlocksProps } />
		</div>
	);
}
