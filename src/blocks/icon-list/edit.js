import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	ToggleControl,
	RangeControl,
} from '@wordpress/components';
import { closeSmall } from '@wordpress/icons';
import IconPicker from '../../utils/icon-picker';

export default function Edit( { attributes, setAttributes } ) {
	const { rows, boxed, boxedWidth } = attributes;
	const blockProps = useBlockProps();

	const updateRow = ( index, field, value ) => {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], [ field ]: value };
		setAttributes( { rows: next } );
	};

	const addRow = () =>
		setAttributes( {
			rows: [
				...rows,
				{ icon: 'shield', text: __( 'New line', 'noortemplates' ) },
			],
		} );

	const removeRow = ( index ) =>
		setAttributes( { rows: rows.filter( ( _row, i ) => i !== index ) } );

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
			<ul
				className={
					'noortemplates-icon-list__list' +
					( boxed ? ' is-boxed' : '' )
				}
				style={ boxed ? { maxWidth: boxedWidth } : undefined }
			>
				{ rows.map( ( row, index ) => (
					<li className="noortemplates-icon-list__row" key={ index }>
						<IconPicker
							value={ row.icon }
							onChange={ ( value ) =>
								updateRow( index, 'icon', value )
							}
						/>
						<RichText
							tagName="span"
							className="noortemplates-icon-list__text"
							value={ row.text }
							onChange={ ( value ) =>
								updateRow( index, 'text', value )
							}
							placeholder={ __( 'Add a line…', 'noortemplates' ) }
						/>
						<Button
							className="noortemplates-icon-list__remove"
							icon={ closeSmall }
							label={ __( 'Remove row', 'noortemplates' ) }
							onClick={ () => removeRow( index ) }
						/>
					</li>
				) ) }
			</ul>
			<Button variant="secondary" onClick={ addRow }>
				{ __( 'Add row', 'noortemplates' ) }
			</Button>
		</div>
	);
}
