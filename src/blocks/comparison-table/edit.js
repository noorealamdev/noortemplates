import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';

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
	const { title } = attributes;
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'noortemplates-comparison-table__table' },
		{
			allowedBlocks: [ 'core/table' ],
			template: TABLE_TEMPLATE,
			templateInsertUpdatesSelection: false,
		}
	);

	return (
		<div { ...blockProps }>
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
