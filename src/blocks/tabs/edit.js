import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { createBlock } from '@wordpress/blocks';
import { useSelect, useDispatch } from '@wordpress/data';
import { plus } from '@wordpress/icons';

const TEMPLATE = [
	[ 'noorblocks/tab', { title: 'Tab 1' } ],
	[ 'noorblocks/tab', { title: 'Tab 2' } ],
];

export default function Edit( { clientId } ) {
	const tabs = useSelect(
		( select ) => select( 'core/block-editor' ).getBlocks( clientId ),
		[ clientId ]
	);
	const activeTabId = useSelect(
		( select ) => {
			const { isBlockSelected, hasSelectedInnerBlock } =
				select( 'core/block-editor' );
			const selected = select( 'core/block-editor' )
				.getBlocks( clientId )
				.find(
					( tab ) =>
						isBlockSelected( tab.clientId ) ||
						hasSelectedInnerBlock( tab.clientId, true )
				);

			return selected ? selected.clientId : null;
		},
		[ clientId ]
	);
	const { selectBlock, updateBlockAttributes, insertBlock } =
		useDispatch( 'core/block-editor' );

	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'noorblocks-tabs__panels' },
		{
			allowedBlocks: [ 'noorblocks/tab' ],
			template: TEMPLATE,
			renderAppender: false,
		}
	);

	const addTab = () => {
		const tab = createBlock( 'noorblocks/tab', {
			title: `Tab ${ tabs.length + 1 }`,
		} );
		insertBlock( tab, tabs.length, clientId );
		selectBlock( tab.clientId );
	};

	// With no selection inside the block, the first tab acts as active.
	const currentId =
		activeTabId || ( tabs.length ? tabs[ 0 ].clientId : null );

	return (
		<div { ...blockProps }>
			<div className="noorblocks-tabs__list">
				{ tabs.map( ( tab ) => (
					/* eslint-disable-next-line jsx-a11y/click-events-have-key-events, jsx-a11y/no-static-element-interactions */
					<div
						key={ tab.clientId }
						className={
							'noorblocks-tabs__tab' +
							( tab.clientId === currentId ? ' is-active' : '' )
						}
						onClick={ () => selectBlock( tab.clientId ) }
					>
						<RichText
							tagName="span"
							value={ tab.attributes.title }
							onChange={ ( value ) =>
								updateBlockAttributes( tab.clientId, {
									title: value,
								} )
							}
							placeholder={ __( 'Tab', 'noorblocks' ) }
							allowedFormats={ [] }
							withoutInteractiveFormatting
						/>
					</div>
				) ) }
				<Button
					className="noorblocks-tabs__add"
					icon={ plus }
					label={ __( 'Add tab', 'noorblocks' ) }
					onClick={ addTab }
				/>
			</div>
			<div { ...innerBlocksProps } />
		</div>
	);
}
