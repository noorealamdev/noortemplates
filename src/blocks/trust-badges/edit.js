import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';
import { closeSmall } from '@wordpress/icons';
import IconPicker from '../../utils/icon-picker';

export default function Edit( { attributes, setAttributes } ) {
	const { items } = attributes;
	const blockProps = useBlockProps();

	const updateItem = ( index, field, value ) => {
		const next = items.slice();
		next[ index ] = { ...next[ index ], [ field ]: value };
		setAttributes( { items: next } );
	};

	const addItem = () =>
		setAttributes( {
			items: [
				...items,
				{ icon: 'nt-shieldCheck', label: __( 'New badge', 'noortemplates' ) },
			],
		} );

	const removeItem = ( index ) =>
		setAttributes( { items: items.filter( ( _item, i ) => i !== index ) } );

	return (
		<div { ...blockProps }>
			<ul className="noortemplates-trust-badges__list">
				{ items.map( ( item, index ) => (
					<li className="noortemplates-trust-badges__item" key={ index }>
						<Button
							className="noortemplates-trust-badges__remove"
							icon={ closeSmall }
							label={ __( 'Remove badge', 'noortemplates' ) }
							onClick={ () => removeItem( index ) }
						/>
						<IconPicker
							value={ item.icon }
							allowNone
							onChange={ ( value ) =>
								updateItem( index, 'icon', value )
							}
						/>
						<RichText
							tagName="span"
							className="noortemplates-trust-badges__label"
							value={ item.label }
							onChange={ ( value ) =>
								updateItem( index, 'label', value )
							}
							placeholder={ __( 'Label', 'noortemplates' ) }
						/>
					</li>
				) ) }
			</ul>
			<Button variant="secondary" onClick={ addItem }>
				{ __( 'Add badge', 'noortemplates' ) }
			</Button>
		</div>
	);
}
