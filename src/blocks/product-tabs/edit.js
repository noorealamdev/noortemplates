import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import WooPlaceholder from '../../utils/woo-placeholder';

export default function Edit() {
	return (
		<div { ...useBlockProps() }>
			<WooPlaceholder
				icon="editor-table"
				label={ __( 'Product Tabs', 'noortemplates' ) }
				instructions={ __(
					'Displays the description, additional information and reviews tabs. Preview appears on the product page.',
					'noortemplates'
				) }
			/>
		</div>
	);
}
