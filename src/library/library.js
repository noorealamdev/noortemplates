import { __ } from '@wordpress/i18n';
import { useState, useEffect, createPortal } from '@wordpress/element';
import {
	Button,
	Modal,
	TabPanel,
	Spinner,
	Notice,
	TextControl,
	SelectControl,
} from '@wordpress/components';
import { parse, cloneBlock } from '@wordpress/blocks';
import { useDispatch, useSelect, select } from '@wordpress/data';
import { BlockPreview } from '@wordpress/block-editor';
import { layout, image as imagePlaceholder, seen } from '@wordpress/icons';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

const LAYOUT_POST_TYPE = 'noortemplates_layout';

// Delay, in ms, before a search keystroke triggers a new request.
const SEARCH_DEBOUNCE = 300;

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
		wrap.className = 'noortemplates-library__toolbar-wrap';
		toolbar.appendChild( wrap );
		setContainer( wrap );

		return () => {
			toolbar.removeChild( wrap );
		};
	}, [] );

	return container ? createPortal( children, container ) : null;
}

// A single template card. Shows a static thumbnail (or a placeholder) so
// the grid stays cheap to render even with hundreds of templates; the full
// block content is only fetched when the card is applied or previewed.
function TemplateCard( { template, isBusy, onSelect, onPreview } ) {
	return (
		<div className="noortemplates-library__card">
			<button
				type="button"
				className="noortemplates-library__card-preview"
				onClick={ () => onSelect( template ) }
				aria-label={ template.title }
				disabled={ isBusy }
			>
				{ template.thumbnail ? (
					<img
						src={ template.thumbnail }
						alt=""
						loading="lazy"
					/>
				) : (
					<span className="noortemplates-library__card-placeholder">
						{ imagePlaceholder }
					</span>
				) }
				{ isBusy && (
					<span className="noortemplates-library__card-spinner">
						<Spinner />
					</span>
				) }
				<span className="noortemplates-library__card-action">
					{ template.type === 'layout'
						? __( 'Use Layout', 'noortemplates' )
						: __( 'Insert Section', 'noortemplates' ) }
				</span>
			</button>
			<Button
				className="noortemplates-library__card-preview-button"
				icon={ seen }
				label={ __( 'Preview', 'noortemplates' ) }
				onClick={ () => onPreview( template ) }
			/>
			<div className="noortemplates-library__card-title">
				{ template.title }
			</div>
			{ template.description && (
				<div className="noortemplates-library__card-description">
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
 *
 * Only rendered while editing a Product Layout, since that is the only
 * post type NoorTemplates' premade layouts and sections apply to.
 */
export default function Library() {
	const [ isOpen, setOpen ] = useState( false );
	const [ type, setType ] = useState( 'layout' );
	const [ category, setCategory ] = useState( '' );
	const [ search, setSearch ] = useState( '' );
	const [ debouncedSearch, setDebouncedSearch ] = useState( '' );
	const [ templates, setTemplates ] = useState( null );
	const [ categoryOptions, setCategoryOptions ] = useState( [] );
	const [ hasError, setHasError ] = useState( false );
	const [ applyingName, setApplyingName ] = useState( null );
	const [ preview, setPreview ] = useState( null );
	const [ previewLoading, setPreviewLoading ] = useState( false );
	const { resetBlocks, insertBlocks } = useDispatch( 'core/block-editor' );

	const isLayoutEditor = useSelect(
		( selectStore ) =>
			selectStore( 'core/editor' )?.getCurrentPostType() ===
			LAYOUT_POST_TYPE,
		[]
	);

	// Debounce the search input before it drives a request.
	useEffect( () => {
		const timeout = setTimeout( () => {
			setDebouncedSearch( search );
		}, SEARCH_DEBOUNCE );

		return () => clearTimeout( timeout );
	}, [ search ] );

	// Fetch the filtered template list whenever the modal is open and the
	// active filters change. Payloads are metadata-only (no block content),
	// so this stays cheap even with hundreds of templates.
	useEffect( () => {
		if ( ! isOpen ) {
			return;
		}

		setHasError( false );

		const args = { type };

		if ( category ) {
			args.category = category;
		}

		if ( debouncedSearch ) {
			args.search = debouncedSearch;
		}

		apiFetch( { path: addQueryArgs( '/noortemplates/v1/templates', args ) } )
			.then( ( result ) =>
				setTemplates( Array.isArray( result ) ? result : [] )
			)
			.catch( () => setHasError( true ) );
	}, [ isOpen, type, category, debouncedSearch ] );

	// Separately fetch the unfiltered templates for the active type, purely
	// to populate the category dropdown's options.
	useEffect( () => {
		if ( ! isOpen ) {
			return;
		}

		apiFetch( { path: addQueryArgs( '/noortemplates/v1/templates', { type } ) } )
			.then( ( result ) => {
				const found = new Set();

				( Array.isArray( result ) ? result : [] ).forEach( ( template ) => {
					if ( template.category ) {
						found.add( template.category );
					}
				} );

				setCategoryOptions( Array.from( found ).sort() );
			} )
			.catch( () => {} );
	}, [ isOpen, type ] );

	if ( ! isLayoutEditor ) {
		return null;
	}

	const applyTemplate = ( template, blocks ) => {
		if (
			template.type === 'layout' &&
			! isPostEmpty() &&
			// eslint-disable-next-line no-alert
			! window.confirm(
				__(
					'This will replace the current layout content. Continue?',
					'noortemplates'
				)
			)
		) {
			return;
		}

		// Clone so inserting the same template twice never reuses client ids.
		const toInsert = blocks.map( ( block ) => cloneBlock( block ) );

		if ( template.type === 'layout' ) {
			resetBlocks( toInsert );
		} else {
			insertBlocks( toInsert );
		}

		setOpen( false );
	};

	// Fetches a template's full content on demand, since the list payload
	// intentionally omits it to stay lightweight at scale.
	const fetchFullTemplate = ( name ) =>
		apiFetch( { path: `/noortemplates/v1/templates/${ name }` } );

	const handleSelect = ( template ) => {
		setApplyingName( template.name );

		fetchFullTemplate( template.name )
			.then( ( full ) => {
				applyTemplate( full, parse( full.content ) );
			} )
			.catch( () => setHasError( true ) )
			.finally( () => setApplyingName( null ) );
	};

	const handlePreview = ( template ) => {
		setPreviewLoading( true );

		fetchFullTemplate( template.name )
			.then( ( full ) => {
				setPreview( { template: full, blocks: parse( full.content ) } );
			} )
			.catch( () => setHasError( true ) )
			.finally( () => setPreviewLoading( false ) );
	};

	const tabs = [
		{ name: 'layout', title: __( 'Full Layouts', 'noortemplates' ) },
		{ name: 'section', title: __( 'Sections', 'noortemplates' ) },
	];

	const renderContent = () => {
		if ( hasError ) {
			return (
				<Notice status="error" isDismissible={ false }>
					{ __(
						'The template library could not be loaded. Please try again.',
						'noortemplates'
					) }
				</Notice>
			);
		}

		return (
			<>
				<div className="noortemplates-library__toolbar">
					<TextControl
						__nextHasNoMarginBottom
						className="noortemplates-library__search"
						value={ search }
						onChange={ setSearch }
						placeholder={ __( 'Search templates…', 'noortemplates' ) }
					/>
					<SelectControl
						__nextHasNoMarginBottom
						className="noortemplates-library__category"
						value={ category }
						onChange={ setCategory }
						options={ [
							{ value: '', label: __( 'All categories', 'noortemplates' ) },
							...categoryOptions.map( ( slug ) => ( {
								value: slug,
								label: slug,
							} ) ),
						] }
					/>
				</div>

				{ ! templates ? (
					<div className="noortemplates-library__loading">
						<Spinner />
					</div>
				) : (
					<div className="noortemplates-library__grid">
						{ templates.map( ( template ) => (
							<TemplateCard
								key={ template.name }
								template={ template }
								isBusy={ applyingName === template.name }
								onSelect={ handleSelect }
								onPreview={ handlePreview }
							/>
						) ) }
					</div>
				) }
			</>
		);
	};

	return (
		<>
			<ToolbarPortal>
				<Button
					variant="primary"
					size="compact"
					icon={ layout }
					onClick={ () => setOpen( true ) }
				>
					{ __( 'NoorTemplates', 'noortemplates' ) }
				</Button>
			</ToolbarPortal>

			{ isOpen && (
				<Modal
					title={ __(
						'NoorTemplates Layout Library',
						'noortemplates'
					) }
					onRequestClose={ () => setOpen( false ) }
					className="noortemplates-library__modal"
					isFullScreen
				>
					<TabPanel
						className="noortemplates-library__tabs"
						tabs={ tabs }
						onSelect={ ( tabName ) => {
							setType( tabName );
							setCategory( '' );
						} }
					>
						{ () => renderContent() }
					</TabPanel>
				</Modal>
			) }

			{ preview && (
				<Modal
					title={ preview.template.title }
					onRequestClose={ () => setPreview( null ) }
					className="noortemplates-library__preview-modal"
				>
					<BlockPreview blocks={ preview.blocks } viewportWidth={ 1400 } />
				</Modal>
			) }

			{ previewLoading && ! preview && (
				<div className="noortemplates-library__preview-loading">
					<Spinner />
				</div>
			) }
		</>
	);
}
