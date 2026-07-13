import { useBlockProps, InnerBlocks, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { title, uid } = attributes;
	const panelId = `noorblocks-accordion-panel-${ uid }`;

	const blockProps = useBlockProps.save( {
		'data-wp-context': JSON.stringify( { uid } ),
	} );

	return (
		<div { ...blockProps }>
			<h3 className="noorblocks-accordion__heading">
				<button
					type="button"
					className="noorblocks-accordion__toggle"
					aria-controls={ panelId }
					aria-expanded="false"
					data-wp-on--click="actions.toggle"
					data-wp-bind--aria-expanded="state.isOpen"
					data-wp-class--is-open="state.isOpen"
				>
					<RichText.Content
						tagName="span"
						className="noorblocks-accordion__title"
						value={ title }
					/>
					<span
						className="noorblocks-accordion__icon"
						aria-hidden="true"
					></span>
				</button>
			</h3>
			<div
				id={ panelId }
				className="noorblocks-accordion__panel"
				hidden
				data-wp-bind--hidden="!state.isOpen"
			>
				<InnerBlocks.Content />
			</div>
		</div>
	);
}
