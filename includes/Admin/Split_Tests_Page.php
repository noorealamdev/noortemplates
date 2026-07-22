<?php
/**
 * "Split Tests" admin page — results and winner declaration.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates\Admin;

use NoorTemplates\Traits\Singleton;
use NoorTemplates\Layouts\Split_Test;
use NoorTemplates\Layouts\Split_Test_Stats;
use NoorTemplates\Layouts\Resolver as Layouts_Resolver;
use NoorTemplates\Licensing\Gate;

defined( 'ABSPATH' ) || exit;

/**
 * Lists every product currently running a split test, with raw
 * impression/add-to-cart/purchase counts per variant and actions to
 * declare a winner or reset stats.
 */
class Split_Tests_Page {

	use Singleton;

	/**
	 * Admin page slug.
	 */
	const PAGE_SLUG = 'noortemplates-split-tests';

	/**
	 * Hooks the admin menu and action handling.
	 */
	protected function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );
	}

	/**
	 * Adds the "Split Tests" submenu page.
	 *
	 * @return void
	 */
	public function register_menu() {
		add_submenu_page(
			Dashboard::PAGE_SLUG,
			__( 'Split Tests', 'noortemplates' ),
			__( 'Split Tests', 'noortemplates' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Handles the declare-winner / reset-stats form submissions.
	 *
	 * @return void
	 */
	public function handle_actions() {
		if ( ! isset( $_POST['noortemplates_split_action'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! Gate::has_feature( 'split_test' ) ) {
			return;
		}

		check_admin_referer( 'noortemplates_split_tests' );

		$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
		$action     = sanitize_text_field( wp_unslash( $_POST['noortemplates_split_action'] ) );

		if ( ! $product_id ) {
			return;
		}

		if ( in_array( $action, array( 'declare_a', 'declare_b' ), true ) ) {
			$this->declare_winner( $product_id, 'declare_a' === $action ? 'a' : 'b' );
		} elseif ( 'reset' === $action ) {
			Split_Test_Stats::reset( $product_id );
		}

		add_settings_error(
			'noortemplates_split_tests',
			'noortemplates_split_tests_updated',
			__( 'Updated.', 'noortemplates' ),
			'success'
		);
	}

	/**
	 * Ends a product's split test, keeping the winning variant as its
	 * regular layout.
	 *
	 * @param int    $product_id Product post ID.
	 * @param string $winner     'a' or 'b'.
	 * @return void
	 */
	private function declare_winner( $product_id, $winner ) {
		if ( 'b' === $winner ) {
			$variant_b_id = (int) get_post_meta( $product_id, Split_Test::VARIANT_B_META_KEY, true );
			update_post_meta( $product_id, Layouts_Resolver::PRODUCT_META_KEY, $variant_b_id );
		}

		delete_post_meta( $product_id, Split_Test::VARIANT_B_META_KEY );
		delete_post_meta( $product_id, Split_Test::ENABLED_META_KEY );
		delete_post_meta( $product_id, Split_Test::RATIO_META_KEY );

		Split_Test_Stats::reset( $product_id );
	}

	/**
	 * Returns every product currently running a split test.
	 *
	 * @return \WP_Post[]
	 */
	private function get_tested_products() {
		return get_posts(
			array(
				'post_type'     => 'product',
				'post_status'   => 'any',
				'numberposts'   => -1,
				'no_found_rows' => true,
				'meta_key'      => Split_Test::ENABLED_META_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'    => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
	}

	/**
	 * Renders a single variant's stat cells.
	 *
	 * @param array $stats Stats for this variant, from Split_Test_Stats::get_stats().
	 * @return void
	 */
	private function render_variant_cells( $stats ) {
		$conversion = $stats['impression']
			? round( $stats['purchase'] / $stats['impression'] * 100, 1 )
			: 0;
		?>
		<td><?php echo esc_html( number_format_i18n( $stats['impression'] ) ); ?></td>
		<td><?php echo esc_html( number_format_i18n( $stats['add_to_cart'] ) ); ?></td>
		<td><?php echo esc_html( number_format_i18n( $stats['purchase'] ) ); ?></td>
		<td><?php echo esc_html( $conversion ); ?>%</td>
		<?php
	}

	/**
	 * Renders the admin page.
	 *
	 * @return void
	 */
	public function render_page() {
		$products = $this->get_tested_products();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Split Tests', 'noortemplates' ); ?></h1>
			<p><?php esc_html_e( 'Raw counts only — no statistical significance is calculated. Enable a split test from a product\'s "Product Page Template" panel.', 'noortemplates' ); ?></p>

			<?php if ( ! Gate::has_feature( 'split_test' ) ) : ?>
				<div class="notice notice-warning">
					<p>
						<?php
						printf(
							/* translators: %s: "Upgrade to Pro" link */
							esc_html__( 'Split testing requires NoorTemplates Pro — existing configurations are preserved but paused until you upgrade. %s', 'noortemplates' ),
							'<a href="' . esc_url( NOORTEMPLATES_CHECKOUT_URL ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Upgrade to Pro', 'noortemplates' ) . '</a>'
						);
						?>
					</p>
				</div>
			<?php endif; ?>

			<?php settings_errors( 'noortemplates_split_tests' ); ?>

			<?php if ( empty( $products ) ) : ?>
				<p><?php esc_html_e( 'No products are currently running a split test.', 'noortemplates' ); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Product', 'noortemplates' ); ?></th>
							<th><?php esc_html_e( 'Variant', 'noortemplates' ); ?></th>
							<th><?php esc_html_e( 'Impressions', 'noortemplates' ); ?></th>
							<th><?php esc_html_e( 'Add to Cart', 'noortemplates' ); ?></th>
							<th><?php esc_html_e( 'Purchases', 'noortemplates' ); ?></th>
							<th><?php esc_html_e( 'Conversion', 'noortemplates' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'noortemplates' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $products as $product ) : ?>
							<?php
							$stats    = Split_Test_Stats::get_stats( $product->ID );
							$layout_a = get_post( (int) get_post_meta( $product->ID, Layouts_Resolver::PRODUCT_META_KEY, true ) );
							$layout_b = get_post( (int) get_post_meta( $product->ID, Split_Test::VARIANT_B_META_KEY, true ) );
							?>
							<tr>
								<td rowspan="2">
									<strong><?php echo esc_html( $product->post_title ); ?></strong><br />
									<a href="<?php echo esc_url( get_edit_post_link( $product->ID ) ); ?>"><?php esc_html_e( 'Edit product', 'noortemplates' ); ?></a>
								</td>
								<td>
									<?php esc_html_e( 'A:', 'noortemplates' ); ?>
									<?php echo esc_html( $layout_a ? $layout_a->post_title : __( '(none)', 'noortemplates' ) ); ?>
								</td>
								<?php $this->render_variant_cells( $stats['a'] ); ?>
								<td rowspan="2">
									<form method="post" style="margin-bottom:6px;">
										<?php wp_nonce_field( 'noortemplates_split_tests' ); ?>
										<input type="hidden" name="product_id" value="<?php echo esc_attr( $product->ID ); ?>" />
										<button type="submit" name="noortemplates_split_action" value="declare_a" class="button">
											<?php esc_html_e( 'Declare A winner', 'noortemplates' ); ?>
										</button>
									</form>
									<form method="post" style="margin-bottom:6px;">
										<?php wp_nonce_field( 'noortemplates_split_tests' ); ?>
										<input type="hidden" name="product_id" value="<?php echo esc_attr( $product->ID ); ?>" />
										<button type="submit" name="noortemplates_split_action" value="declare_b" class="button">
											<?php esc_html_e( 'Declare B winner', 'noortemplates' ); ?>
										</button>
									</form>
									<form method="post">
										<?php wp_nonce_field( 'noortemplates_split_tests' ); ?>
										<input type="hidden" name="product_id" value="<?php echo esc_attr( $product->ID ); ?>" />
										<button type="submit" name="noortemplates_split_action" value="reset" class="button-link-delete">
											<?php esc_html_e( 'Reset stats', 'noortemplates' ); ?>
										</button>
									</form>
								</td>
							</tr>
							<tr>
								<td>
									<?php esc_html_e( 'B:', 'noortemplates' ); ?>
									<?php echo esc_html( $layout_b ? $layout_b->post_title : __( '(none)', 'noortemplates' ) ); ?>
								</td>
								<?php $this->render_variant_cells( $stats['b'] ); ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}
}
