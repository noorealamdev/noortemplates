import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl, RangeControl } from '@wordpress/components';

const TEMPLATE = [
	[
		'noortemplates/accordion',
		{},
		[
			[
				'noortemplates/accordion-item',
				{
					title: __(
						'How long does shipping take?',
						'noortemplates'
					),
					uid: 'faq-shipping',
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'Most orders ship within 1-2 business days and arrive within 5-7 business days depending on your location.',
								'noortemplates'
							),
						},
					],
				],
			],
			[
				'noortemplates/accordion-item',
				{
					title: __(
						'What is your return policy?',
						'noortemplates'
					),
					uid: 'faq-returns',
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'We offer a 30-day return window on unused items in their original packaging. Contact us to start a return.',
								'noortemplates'
							),
						},
					],
				],
			],
			[
				'noortemplates/accordion-item',
				{
					title: __(
						'Do you ship internationally?',
						'noortemplates'
					),
					uid: 'faq-international',
				},
				[
					[
						'core/paragraph',
						{
							content: __(
								'Yes, we ship to most countries worldwide. International shipping rates are calculated at checkout.',
								'noortemplates'
							),
						},
					],
				],
			],
		],
	],
];

export default function Edit( { attributes, setAttributes } ) {
	const { title, boxed, boxedWidth } = attributes;
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		{
			className:
				'noortemplates-faq__accordion' +
				( boxed ? ' is-boxed' : '' ),
			style: boxed ? { maxWidth: boxedWidth } : undefined,
		},
		{
			allowedBlocks: [ 'noortemplates/accordion' ],
			template: TEMPLATE,
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
				className="noortemplates-faq__title"
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				placeholder={ __(
					'Frequently Asked Questions',
					'noortemplates'
				) }
			/>
			<div { ...innerBlocksProps } />
		</div>
	);
}
