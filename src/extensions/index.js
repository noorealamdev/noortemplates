import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	SelectControl,
	RangeControl,
} from '@wordpress/components';
import { Fragment } from '@wordpress/element';

import { VISIBILITY_DEFAULT, ANIMATION_DEFAULT, isExtendable } from './shared';

/**
 * Adds the visibility/animation attributes to every NoorTemplates block.
 *
 * @param {Object} settings Block settings.
 * @param {string} name     Block name.
 * @return {Object} Filtered settings.
 */
function addExtensionAttributes( settings, name ) {
	if ( ! isExtendable( name ) ) {
		return settings;
	}

	return {
		...settings,
		attributes: {
			...settings.attributes,
			noortemplatesVisibility: {
				type: 'object',
				default: VISIBILITY_DEFAULT,
			},
			noortemplatesAnimation: {
				type: 'object',
				default: ANIMATION_DEFAULT,
			},
		},
	};
}

addFilter(
	'blocks.registerBlockType',
	'noortemplates/extensions/attributes',
	addExtensionAttributes
);

/**
 * Appends a "Visibility" and "Animation" Inspector panel to every
 * NoorTemplates block, after its own controls.
 */
const withExtensionControls = createHigherOrderComponent(
	( BlockEdit ) => ( props ) => {
		if ( ! isExtendable( props.name ) ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;
		const visibility = {
			...VISIBILITY_DEFAULT,
			...attributes.noortemplatesVisibility,
		};
		const animation = {
			...ANIMATION_DEFAULT,
			...attributes.noortemplatesAnimation,
		};

		const updateVisibility = ( key, value ) =>
			setAttributes( {
				noortemplatesVisibility: { ...visibility, [ key ]: value },
			} );

		const updateAnimation = ( key, value ) =>
			setAttributes( {
				noortemplatesAnimation: { ...animation, [ key ]: value },
			} );

		return (
			<Fragment>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody
						title={ __( 'Visibility', 'noortemplates' ) }
						initialOpen={ false }
					>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Hide on desktop', 'noortemplates' ) }
							checked={ visibility.hideOnDesktop }
							onChange={ ( value ) =>
								updateVisibility( 'hideOnDesktop', value )
							}
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Hide on tablet', 'noortemplates' ) }
							checked={ visibility.hideOnTablet }
							onChange={ ( value ) =>
								updateVisibility( 'hideOnTablet', value )
							}
						/>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Hide on mobile', 'noortemplates' ) }
							checked={ visibility.hideOnMobile }
							onChange={ ( value ) =>
								updateVisibility( 'hideOnMobile', value )
							}
						/>
					</PanelBody>
					<PanelBody
						title={ __( 'Animation', 'noortemplates' ) }
						initialOpen={ false }
					>
						<SelectControl
							__nextHasNoMarginBottom
							label={ __(
								'Entrance animation',
								'noortemplates'
							) }
							value={ animation.type }
							options={ [
								{
									label: __( 'None', 'noortemplates' ),
									value: 'none',
								},
								{
									label: __( 'Fade in', 'noortemplates' ),
									value: 'fade-in',
								},
								{
									label: __( 'Slide up', 'noortemplates' ),
									value: 'slide-up',
								},
								{
									label: __( 'Zoom in', 'noortemplates' ),
									value: 'zoom-in',
								},
							] }
							onChange={ ( value ) =>
								updateAnimation( 'type', value )
							}
						/>
						{ animation.type !== 'none' && (
							<>
								<RangeControl
									__nextHasNoMarginBottom
									label={ __(
										'Duration (ms)',
										'noortemplates'
									) }
									min={ 100 }
									max={ 3000 }
									step={ 50 }
									value={ animation.duration }
									onChange={ ( value ) =>
										updateAnimation( 'duration', value )
									}
								/>
								<RangeControl
									__nextHasNoMarginBottom
									label={ __(
										'Delay (ms)',
										'noortemplates'
									) }
									min={ 0 }
									max={ 3000 }
									step={ 50 }
									value={ animation.delay }
									onChange={ ( value ) =>
										updateAnimation( 'delay', value )
									}
								/>
							</>
						) }
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	},
	'withNoorTemplatesExtensionControls'
);

addFilter(
	'editor.BlockEdit',
	'noortemplates/extensions/inspector-controls',
	withExtensionControls
);

/**
 * Writes the resulting visibility classes and animation data attributes
 * onto the saved markup of static (save.js) NoorTemplates blocks.
 *
 * @param {Object} extraProps Block save element props.
 * @param {Object} blockType  Block type object.
 * @param {Object} attributes Block attributes.
 * @return {Object} Filtered props.
 */
function addExtensionSaveProps( extraProps, blockType, attributes ) {
	if ( ! isExtendable( blockType.name ) ) {
		return extraProps;
	}

	const visibility = {
		...VISIBILITY_DEFAULT,
		...attributes.noortemplatesVisibility,
	};
	const animation = {
		...ANIMATION_DEFAULT,
		...attributes.noortemplatesAnimation,
	};

	const classNames = [ extraProps.className ];

	if ( visibility.hideOnMobile ) {
		classNames.push( 'noortemplates-hide-mobile' );
	}

	if ( visibility.hideOnTablet ) {
		classNames.push( 'noortemplates-hide-tablet' );
	}

	if ( visibility.hideOnDesktop ) {
		classNames.push( 'noortemplates-hide-desktop' );
	}

	const nextProps = {
		...extraProps,
		className: classNames.filter( Boolean ).join( ' ' ) || undefined,
	};

	if ( animation.type && 'none' !== animation.type ) {
		nextProps[ 'data-noortemplates-animation' ] = animation.type;
		nextProps[ 'data-noortemplates-duration' ] = animation.duration;
		nextProps[ 'data-noortemplates-delay' ] = animation.delay;
	}

	return nextProps;
}

addFilter(
	'blocks.getSaveContent.extraProps',
	'noortemplates/extensions/save-props',
	addExtensionSaveProps
);
