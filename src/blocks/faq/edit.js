import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';

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
	const { title } = attributes;
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'noortemplates-faq__accordion' },
		{
			allowedBlocks: [ 'noortemplates/accordion' ],
			template: TEMPLATE,
			templateInsertUpdatesSelection: false,
		}
	);

	return (
		<div { ...blockProps }>
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
