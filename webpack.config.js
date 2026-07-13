/**
 * Extends the default @wordpress/scripts config with the template
 * library app, which is not a block and therefore not auto-detected.
 *
 * With --experimental-modules the default export is an array of two
 * configs: [ scripts, modules ]. The library app is a classic script,
 * so it is added to the scripts config only.
 */
const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const addLibraryEntry = ( config ) => ( {
	...config,
	entry: {
		...( typeof config.entry === 'function'
			? config.entry()
			: config.entry ),
		'library/index': path.resolve( process.cwd(), 'src/library/index.js' ),
	},
} );

module.exports = Array.isArray( defaultConfig )
	? defaultConfig.map( ( config, index ) =>
			index === 0 ? addLibraryEntry( config ) : config
	  )
	: addLibraryEntry( defaultConfig );
