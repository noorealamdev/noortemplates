/**
 * Extends the default @wordpress/scripts config with the template
 * library app, which is not a block and therefore not auto-detected.
 */
const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: {
		...( typeof defaultConfig.entry === 'function'
			? defaultConfig.entry()
			: defaultConfig.entry ),
		'library/index': path.resolve( process.cwd(), 'src/library/index.js' ),
	},
};
