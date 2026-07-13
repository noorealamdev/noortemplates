import { registerPlugin } from '@wordpress/plugins';

import Library from './library';
import './editor.scss';

registerPlugin( 'noorblocks-library', {
	render: Library,
} );
