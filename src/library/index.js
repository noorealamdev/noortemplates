import { registerPlugin } from '@wordpress/plugins';

import Library from './library';
import './editor.scss';

registerPlugin( 'noortemplates-library', {
	render: Library,
} );
