<?php
/**
 * Admin dashboard page.
 *
 * @package NoorBlocks
 */

namespace NoorBlocks\Admin;

use NoorBlocks\Traits\Singleton;
use NoorBlocks\Blocks\Manager as Blocks_Manager;

defined( 'ABSPATH' ) || exit;

/**
 * Settings page where individual blocks can be toggled on or off.
 */
class Dashboard {

	use Singleton;

	/**
	 * Admin page slug.
	 */
	const PAGE_SLUG = 'noorblocks';

	/**
	 * Hooks the admin menu and settings handling.
	 */
	protected function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_save' ) );
	}

	/**
	 * Adds the NoorBlocks top-level admin menu.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__( 'NoorBlocks', 'noorblocks' ),
			__( 'NoorBlocks', 'noorblocks' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' ),
			'dashicons-layout',
			59
		);
	}

	/**
	 * Persists the block toggles when the settings form is submitted.
	 *
	 * @return void
	 */
	public function handle_save() {
		if ( ! isset( $_POST['noorblocks_save_settings'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_admin_referer( 'noorblocks_settings' );

		$all_blocks = array_keys( Blocks_Manager::instance()->get_all_blocks() );
		$enabled    = isset( $_POST['noorblocks_enabled'] )
			? array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['noorblocks_enabled'] ) )
			: array();

		// Everything not checked is disabled.
		$disabled = array_values( array_diff( $all_blocks, $enabled ) );

		update_option( Blocks_Manager::DISABLED_OPTION, $disabled );

		add_settings_error(
			'noorblocks',
			'settings_saved',
			__( 'Settings saved.', 'noorblocks' ),
			'success'
		);
	}

	/**
	 * Renders the dashboard page.
	 *
	 * @return void
	 */
	public function render_page() {
		$blocks   = Blocks_Manager::instance()->get_all_blocks();
		$disabled = (array) get_option( Blocks_Manager::DISABLED_OPTION, array() );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'NoorBlocks', 'noorblocks' ); ?></h1>
			<p><?php esc_html_e( 'Enable or disable individual blocks. Disabled blocks are removed from the editor but existing content is not deleted.', 'noorblocks' ); ?></p>

			<?php settings_errors( 'noorblocks' ); ?>

			<form method="post">
				<?php wp_nonce_field( 'noorblocks_settings' ); ?>
				<table class="widefat striped" style="max-width: 720px;">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Block', 'noorblocks' ); ?></th>
							<th><?php esc_html_e( 'Description', 'noorblocks' ); ?></th>
							<th style="width: 100px;"><?php esc_html_e( 'Enabled', 'noorblocks' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $blocks ) ) : ?>
							<tr>
								<td colspan="3"><?php esc_html_e( 'No blocks found. Run the build first (npm run build).', 'noorblocks' ); ?></td>
							</tr>
						<?php endif; ?>
						<?php foreach ( $blocks as $name => $metadata ) : ?>
							<tr>
								<td><strong><?php echo esc_html( isset( $metadata['title'] ) ? $metadata['title'] : $name ); ?></strong></td>
								<td><?php echo esc_html( isset( $metadata['description'] ) ? $metadata['description'] : '' ); ?></td>
								<td>
									<input
										type="checkbox"
										name="noorblocks_enabled[]"
										value="<?php echo esc_attr( $name ); ?>"
										<?php checked( ! in_array( $name, $disabled, true ) ); ?>
									/>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<p>
					<button type="submit" name="noorblocks_save_settings" value="1" class="button button-primary">
						<?php esc_html_e( 'Save Settings', 'noorblocks' ); ?>
					</button>
				</p>
			</form>
		</div>
		<?php
	}
}
