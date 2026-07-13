import { __ } from '@wordpress/i18n';
import { useState, useEffect, useMemo, createPortal } from '@wordpress/element';
import { Button, Modal, TabPanel } from '@wordpress/components';
import { parse, cloneBlock } from '@wordpress/blocks';
import { useDispatch, select } from '@wordpress/data';
import { BlockPreview } from '@wordpress/block-editor';
import { layout } from '@wordpress/icons';

const TEMPLATES =
	( window.noorBlocksLibrary && window.noorBlocksLibrary.templates ) || [];

// Renders children into a container appended to the editor header toolbar.
function ToolbarPortal( { children } ) {
	const [ container, setContainer ] = useState( null );

	useEffect( () => {
		const toolbar =
			document.querySelector( '.editor-header__toolbar' ) ||
			document.querySelector( '.edit-post-header__toolbar' );

		if ( ! toolbar ) {
			return;
		}

		const wrap = document.createElement( 'div' );
		wrap.className = 'noorblocks-library__toolbar-wrap';
		toolbar.appendChild( wrap );
		setContainer( wrap );

		return () => {
			toolbar.removeChild( wrap );
		};
	}, [] );

	return container ? createPortal( children, container ) : null;
}

// A single template card with a live block preview.
function TemplateCard( { template, onSelect } ) {
	const blocks = useMemo(
		() => parse( template.content ),
		[ template.content ]
	);

	return (
		<div className="noorblocks-library__card">
			<button
				type="button"
				className="noorblocks-library__card-preview"
				onClick={ () => onSelect( template, blocks ) }
				aria-label={ template.title }
			>
				<BlockPreview blocks={ blocks } viewportWidth={ 1400 } />
				<span className="noorblocks-library__card-action">
					{ template.type === 'page'
						? __( 'Use Template', 'noorblocks' )
						: __( 'Insert Section', 'noorblocks' ) }
				</span>
			</button>
			<div className="noorblocks-library__card-title">
				{ template.title }
			</div>
			{ template.description && (
				<div className="noorblocks-library__card-description">
					{ template.description }
				</div>
			) }
		</div>
	);
}

/**
 * Returns true when the post has no meaningful content yet.
 */
function isPostEmpty() {
	const blocks = select( 'core/block-editor' ).getBlocks();

	return blocks.every(
		( block ) =>
			block.name === 'core/paragraph' &&
			( ! block.attributes.content ||
				block.attributes.content.length === 0 )
	);
}

/**
 * The Templates button in the editor top bar plus the library modal.
 */
export default function Library() {
	const [ isOpen, setOpen ] = useState( false );
	const { resetBlocks, insertBlocks } = useDispatch( 'core/block-editor' );

	const applyTemplate = ( template, blocks ) => {
		if (
			template.type === 'page' &&
			! isPostEmpty() &&
			// eslint-disable-next-line no-alert
			! window.confirm(
				__(
					'This will replace the current page content. Continue?',
					'noorblocks'
				)
			)
		) {
			return;
		}

		// Clone so inserting the same template twice never reuses client ids.
		const toInsert = blocks.map( ( block ) => cloneBlock( block ) );

		if ( template.type === 'page' ) {
			resetBlocks( toInsert );
		} else {
			insertBlocks( toInsert );
		}

		setOpen( false );
	};

	const tabs = [
		{ name: 'page', title: __( 'Page Templates', 'noorblocks' ) },
		{ name: 'section', title: __( 'Sections', 'noorblocks' ) },
	];

	return (
		<>
			<ToolbarPortal>
				<Button
					variant="primary"
					size="compact"
					icon={ layout }
					onClick={ () => setOpen( true ) }
				>
					{ __( 'Templates', 'noorblocks' ) }
				</Button>
			</ToolbarPortal>

			{ isOpen && (
				<Modal
					title={ __( 'NoorBlocks Template Library', 'noorblocks' ) }
					onRequestClose={ () => setOpen( false ) }
					className="noorblocks-library__modal"
					isFullScreen
				>
					<TabPanel
						className="noorblocks-library__tabs"
						tabs={ tabs }
					>
						{ ( tab ) => (
							<div className="noorblocks-library__grid">
								{ TEMPLATES.filter(
									( template ) => template.type === tab.name
								).map( ( template ) => (
									<TemplateCard
										key={ template.name }
										template={ template }
										onSelect={ applyTemplate }
									/>
								) ) }
							</div>
						) }
					</TabPanel>
				</Modal>
			) }
		</>
	);
}
