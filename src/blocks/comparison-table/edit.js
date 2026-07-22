import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl, RangeControl } from '@wordpress/components';

const TABLE_TEMPLATE = [
	[
		'core/table',
		{
			hasFixedLayout: true,
			head: [
				{
					cells: [
						{
							content: __( 'Feature', 'noortemplates' ),
							tag: 'th',
						},
						{
							content: __( 'Basic', 'noortemplates' ),
							tag: 'th',
						},
						{
							content: __( 'Premium', 'noortemplates' ),
							tag: 'th',
						},
					],
				},
			],
			body: [
				{
					cells: [
						{
							content: __( 'Free shipping', 'noortemplates' ),
							tag: 'td',
						},
						{ content: '✕', tag: 'td' },
						{ content: '✓', tag: 'td' },
					],
				},
				{
					cells: [
						{
							content: __( 'Priority support', 'noortemplates' ),
							tag: 'td',
						},
						{ content: '✕', tag: 'td' },
						{ content: '✓', tag: 'td' },
					],
				},
				{
					cells: [
						{
							content: __(
								'Extended warranty',
								'noortemplates'
							),
							tag: 'td',
						},
						{ content: '✕', tag: 'td' },
						{ content: '✓', tag: 'td' },
					],
				},
			],
		},
	],
];

export default function Edit( { attributes, setAttributes } ) {
	const { title, boxed, boxedWidth } = attributes;
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		{
			className:
				'noortemplates-comparison-table__table' +
				( boxed ? ' is-boxed' : '' ),
			style: boxed ? { maxWidth: boxedWidth } : undefined,
		},
		{
			allowedBlocks: [ 'core/table' ],
			template: TABLE_TEMPLATE,
			templateInsertUpdatesSelection: false,
		}
	);

	return (
		<div { ...blockProps }>
			<InspectorControls>
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
			<RichText
				tagName="h2"
				className="noortemplates-comparison-table__title"
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				placeholder={ __( 'Compare Options', 'noortemplates' ) }
			/>
			<div { ...innerBlocksProps } />
		</div>
	);
}
