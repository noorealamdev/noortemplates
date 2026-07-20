import { __ } from '@wordpress/i18n';
import { ExternalLink } from '@wordpress/components';
import { isPro, getCheckoutUrl } from './licensing';

/**
 * Wraps a feature that is locked behind NoorTemplates Pro. Modeled
 * directly on NoorQuiz's ProFeatureGuard (see that plugin's
 * admin/src/components/ProFeatureGuard.js): renders children normally
 * once a Pro license is active, otherwise dims them and shows an upgrade
 * nudge. The `feature` prop is forward-compatible with per-feature
 * license tiers — today every gate simply mirrors the global Pro flag
 * (window.noorTemplatesLicensing.isPro).
 *
 * This is editor-side UX only. Always pair it with a matching
 * server-side check (NoorTemplates\Licensing\Gate::is_pro() or
 * ::has_feature()) at the point the attribute is actually
 * applied/rendered — a determined user can still set block attributes
 * directly (the REST API, an imported JSON template, etc.), and the
 * editor-side gate alone wouldn't stop that.
 *
 * Usage:
 *
 *     import ProControl from '../../utils/pro-control';
 *     import '../../utils/pro-control.scss'; // once per consuming block
 *
 *     <ProControl feature="background_image">
 *         <ColorPalette value={ background } onChange={ ... } />
 *     </ProControl>
 *
 * @param {Object}  props           Component props.
 * @param {string}  [props.feature] Feature key, for forward-compatible per-feature gating.
 * @param {string}  [props.message] Optional extra copy shown next to the "PRO" badge.
 * @param {Element} props.children  The control(s) to gate.
 */
export default function ProControl( { feature, message, children } ) {
	if ( isPro() ) {
		return children;
	}

	return (
		<div className="noortemplates-pro-control" data-feature={ feature }>
			<div className="noortemplates-pro-control__disabled">
				{ children }
			</div>
			<div className="noortemplates-pro-control__badge">
				<span className="noortemplates-pro-control__label">
					{ __( 'PRO', 'noortemplates' ) }
				</span>
				{ message && (
					<span className="noortemplates-pro-control__message">
						{ message }
					</span>
				) }
				<ExternalLink
					href={ getCheckoutUrl() || '#' }
					className="noortemplates-pro-control__link"
				>
					{ __( 'Upgrade', 'noortemplates' ) }
				</ExternalLink>
			</div>
		</div>
	);
}
