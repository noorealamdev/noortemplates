<?php
/**
 * Admin dashboard page.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates\Admin;

use NoorTemplates\Traits\Singleton;
use NoorTemplates\Blocks\Manager as Blocks_Manager;
use NoorTemplates\Layouts\Post_Type as Layouts_Post_Type;
use NoorTemplates\Layouts\Resolver as Layouts_Resolver;

defined( 'ABSPATH' ) || exit;

/**
 * Settings page where individual blocks can be toggled on or off.
 */
class Dashboard {

	use Singleton;

	/**
	 * Admin page slug.
	 */
	const PAGE_SLUG = 'noortemplates';

	/**
	 * Hooks the admin menu and settings handling.
	 */
	protected function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_save' ) );
	}

	/**
	 * Adds the NoorTemplates top-level admin menu.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_menu_page(
			__( 'NoorTemplates', 'noortemplates' ),
			__( 'NoorTemplates', 'noortemplates' ),
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
		if ( ! isset( $_POST['noortemplates_save_settings'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		check_admin_referer( 'noortemplates_settings' );

		$all_blocks = array_keys( Blocks_Manager::instance()->get_all_blocks() );
		$enabled    = isset( $_POST['noortemplates_enabled'] )
			? array_map( 'sanitize_text_field', wp_unslash( (array) $_POST['noortemplates_enabled'] ) )
			: array();

		// Everything not checked is disabled.
		$disabled = array_values( array_diff( $all_blocks, $enabled ) );

		update_option( Blocks_Manager::DISABLED_OPTION, $disabled );

		$default_layout_id = isset( $_POST['noortemplates_default_layout_id'] )
			? absint( $_POST['noortemplates_default_layout_id'] )
			: 0;

		if ( $default_layout_id ) {
			update_option( Layouts_Resolver::DEFAULT_OPTION, $default_layout_id );
		} else {
			delete_option( Layouts_Resolver::DEFAULT_OPTION );
		}

		add_settings_error(
			'noortemplates',
			'settings_saved',
			__( 'Settings saved.', 'noortemplates' ),
			'success'
		);
	}

	/**
	 * Renders the dashboard page.
	 *
	 * @return void
	 */
	public function render_page() {
		$blocks         = Blocks_Manager::instance()->get_all_blocks();
		$disabled       = (array) get_option( Blocks_Manager::DISABLED_OPTION, array() );
		$default_layout = (int) get_option( Layouts_Resolver::DEFAULT_OPTION, 0 );
		$layouts        = get_posts(
			array(
				'post_type'     => Layouts_Post_Type::SLUG,
				'post_status'   => 'publish',
				'numberposts'   => -1,
				'orderby'       => 'title',
				'order'         => 'ASC',
				'no_found_rows' => true,
			)
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'NoorTemplates', 'noortemplates' ); ?></h1>
			<p><?php esc_html_e( 'Enable or disable individual blocks. Disabled blocks are removed from the editor but existing content is not deleted.', 'noortemplates' ); ?></p>

			<?php settings_errors( 'noortemplates' ); ?>

			<form method="post">
				<?php wp_nonce_field( 'noortemplates_settings' ); ?>

				<h2><?php esc_html_e( 'Default Product Page Template', 'noortemplates' ); ?></h2>
				<p><?php esc_html_e( 'Applied to every product that does not have its own template or a category default.', 'noortemplates' ); ?></p>
				<p>
					<select name="noortemplates_default_layout_id">
						<option value="0"><?php esc_html_e( '— Default (theme/WooCommerce) —', 'noortemplates' ); ?></option>
						<?php foreach ( $layouts as $layout ) : ?>
							<option value="<?php echo esc_attr( $layout->ID ); ?>" <?php selected( $default_layout, $layout->ID ); ?>>
								<?php echo esc_html( $layout->post_title ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</p>

				<table class="widefat striped" style="max-width: 720px;">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Block', 'noortemplates' ); ?></th>
							<th><?php esc_html_e( 'Description', 'noortemplates' ); ?></th>
							<th style="width: 100px;"><?php esc_html_e( 'Enabled', 'noortemplates' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $blocks ) ) : ?>
							<tr>
								<td colspan="3"><?php esc_html_e( 'No blocks found. Run the build first (npm run build).', 'noortemplates' ); ?></td>
							</tr>
						<?php endif; ?>
						<?php foreach ( $blocks as $name => $metadata ) : ?>
							<tr>
								<td><strong><?php echo esc_html( isset( $metadata['title'] ) ? $metadata['title'] : $name ); ?></strong></td>
								<td><?php echo esc_html( isset( $metadata['description'] ) ? $metadata['description'] : '' ); ?></td>
								<td>
									<input
										type="checkbox"
										name="noortemplates_enabled[]"
										value="<?php echo esc_attr( $name ); ?>"
										<?php checked( ! in_array( $name, $disabled, true ) ); ?>
									/>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<p>
					<button type="submit" name="noortemplates_save_settings" value="1" class="button button-primary">
						<?php esc_html_e( 'Save Settings', 'noortemplates' ); ?>
					</button>
				</p>
			</form>
		</div>
		<?php
	}
}
