import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	ToggleControl,
	TextControl,
	QueryControls,
	Disabled,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
	const {
		postsToShow,
		columns,
		order,
		orderBy,
		showFeaturedImage,
		showDate,
		showExcerpt,
		excerptLength,
		showReadMore,
		readMoreText,
	} = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Query', 'noorblocks' ) }>
					<QueryControls
						numberOfItems={ postsToShow }
						onNumberOfItemsChange={ ( value ) =>
							setAttributes( { postsToShow: value } )
						}
						order={ order }
						onOrderChange={ ( value ) =>
							setAttributes( { order: value } )
						}
						orderBy={ orderBy }
						onOrderByChange={ ( value ) =>
							setAttributes( { orderBy: value } )
						}
						maxItems={ 24 }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Layout', 'noorblocks' ) }>
					<RangeControl
						label={ __( 'Columns', 'noorblocks' ) }
						value={ columns }
						onChange={ ( value ) =>
							setAttributes( { columns: value } )
						}
						min={ 1 }
						max={ 4 }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Content', 'noorblocks' ) }>
					<ToggleControl
						label={ __( 'Featured image', 'noorblocks' ) }
						checked={ showFeaturedImage }
						onChange={ ( value ) =>
							setAttributes( { showFeaturedImage: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Date', 'noorblocks' ) }
						checked={ showDate }
						onChange={ ( value ) =>
							setAttributes( { showDate: value } )
						}
					/>
					<ToggleControl
						label={ __( 'Excerpt', 'noorblocks' ) }
						checked={ showExcerpt }
						onChange={ ( value ) =>
							setAttributes( { showExcerpt: value } )
						}
					/>
					{ showExcerpt && (
						<RangeControl
							label={ __(
								'Excerpt length (words)',
								'noorblocks'
							) }
							value={ excerptLength }
							onChange={ ( value ) =>
								setAttributes( { excerptLength: value } )
							}
							min={ 5 }
							max={ 60 }
						/>
					) }
					<ToggleControl
						label={ __( 'Read more link', 'noorblocks' ) }
						checked={ showReadMore }
						onChange={ ( value ) =>
							setAttributes( { showReadMore: value } )
						}
					/>
					{ showReadMore && (
						<TextControl
							label={ __( 'Read more text', 'noorblocks' ) }
							value={ readMoreText }
							onChange={ ( value ) =>
								setAttributes( { readMoreText: value } )
							}
							placeholder={ __( 'Read more', 'noorblocks' ) }
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...useBlockProps() }>
				<Disabled>
					<ServerSideRender
						block="noorblocks/post-grid"
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		</>
	);
}
