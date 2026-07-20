<?php
/**
 * Product and product category Layout assignment UI.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates\Layouts;

use NoorTemplates\Traits\Singleton;
use NoorTemplates\Licensing\Gate;

defined( 'ABSPATH' ) || exit;

/**
 * Adds a "Product Page Template" picker to the Product edit screen and a
 * default-template field to the product category edit/add screens.
 */
class Meta_Box {

	use Singleton;

	/**
	 * Hooks the meta box and taxonomy field registration.
	 */
	protected function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
		add_action( 'save_post_product', array( $this, 'save_meta_box' ) );

		add_action( 'product_cat_edit_form_fields', array( $this, 'render_term_edit_field' ) );
		add_action( 'product_cat_add_form_fields', array( $this, 'render_term_add_field' ) );
		add_action( 'edited_product_cat', array( $this, 'save_term_field' ) );
		add_action( 'created_product_cat', array( $this, 'save_term_field' ) );
	}

	/**
	 * Registers the product edit screen meta box.
	 *
	 * @return void
	 */
	public function register_meta_box() {
		add_meta_box(
			'noortemplates-layout',
			__( 'Product Page Template', 'noortemplates' ),
			array( $this, 'render_meta_box' ),
			'product',
			'side',
			'default'
		);
	}

	/**
	 * Returns every published Product Layout, for use in the select fields.
	 *
	 * @return \WP_Post[]
	 */
	private function get_layouts() {
		return get_posts(
			array(
				'post_type'     => Post_Type::SLUG,
				'post_status'   => 'publish',
				'numberposts'   => -1,
				'orderby'       => 'title',
				'order'         => 'ASC',
				'no_found_rows' => true,
			)
		);
	}

	/**
	 * Renders the layout <select> markup shared by both screens.
	 *
	 * @param string $name     Field name/id.
	 * @param int    $selected Currently selected layout ID.
	 * @return void
	 */
	private function render_select( $name, $selected ) {
		?>
		<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" style="width:100%;">
			<option value="0"><?php esc_html_e( '— Default (theme/WooCommerce) —', 'noortemplates' ); ?></option>
			<?php foreach ( $this->get_layouts() as $layout ) : ?>
				<option value="<?php echo esc_attr( $layout->ID ); ?>" <?php selected( (int) $selected, $layout->ID ); ?>>
					<?php echo esc_html( $layout->post_title ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Renders the product edit screen meta box contents.
	 *
	 * @param \WP_Post $post Product post.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'noortemplates_layout_meta', 'noortemplates_layout_nonce' );

		$selected = get_post_meta( $post->ID, Resolver::PRODUCT_META_KEY, true );

		$this->render_select( 'noortemplates_layout_id', (int) $selected );
		?>
		<p class="description">
			<?php esc_html_e( 'Replace this product\'s page with a NoorTemplates layout.', 'noortemplates' ); ?>
		</p>
		<hr />
		<?php if ( Gate::has_feature( 'split_test' ) ) : ?>
			<?php
			$split_enabled = '1' === get_post_meta( $post->ID, Split_Test::ENABLED_META_KEY, true );
			$variant_b     = get_post_meta( $post->ID, Split_Test::VARIANT_B_META_KEY, true );
			$ratio         = get_post_meta( $post->ID, Split_Test::RATIO_META_KEY, true );
			?>
			<p>
				<label>
					<input type="checkbox" name="noortemplates_split_enabled" value="1" <?php checked( $split_enabled ); ?> />
					<?php esc_html_e( 'Split test against a second layout', 'noortemplates' ); ?>
				</label>
			</p>
			<p>
				<label for="noortemplates_layout_id_b"><?php esc_html_e( 'Variant B', 'noortemplates' ); ?></label>
				<?php $this->render_select( 'noortemplates_layout_id_b', (int) $variant_b ); ?>
			</p>
			<p>
				<label for="noortemplates_split_ratio"><?php esc_html_e( 'Traffic to Variant B (%)', 'noortemplates' ); ?></label>
				<input
					type="number"
					name="noortemplates_split_ratio"
					id="noortemplates_split_ratio"
					min="1"
					max="99"
					value="<?php echo esc_attr( $ratio ? (int) $ratio : 50 ); ?>"
					style="width:100%;"
				/>
			</p>
			<p class="description">
				<?php esc_html_e( 'Visitors are split between the layout above (Variant A) and Variant B, and stuck with a cookie. Results appear under NoorTemplates → Split Tests.', 'noortemplates' ); ?>
			</p>
		<?php else : ?>
			<p>
				<label>
					<input type="checkbox" disabled="disabled" />
					<?php esc_html_e( 'Split test against a second layout', 'noortemplates' ); ?>
					<span style="display:inline-block;padding:1px 6px;margin-left:4px;background:#1c1c1c;border-radius:999px;color:#fff;font-size:10px;font-weight:600;text-transform:uppercase;vertical-align:middle;">
						<?php esc_html_e( 'Pro', 'noortemplates' ); ?>
					</span>
				</label>
			</p>
			<p class="description">
				<?php
				printf(
					/* translators: %s: "Upgrade to Pro" link */
					esc_html__( 'Split testing requires NoorTemplates Pro. %s', 'noortemplates' ),
					'<a href="' . esc_url( NOORTEMPLATES_CHECKOUT_URL ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Upgrade to Pro', 'noortemplates' ) . '</a>'
				);
				?>
			</p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Persists the product's chosen layout.
	 *
	 * @param int $post_id Product post ID.
	 * @return void
	 */
	public function save_meta_box( $post_id ) {
		if (
			! isset( $_POST['noortemplates_layout_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noortemplates_layout_nonce'] ) ), 'noortemplates_layout_meta' )
		) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$layout_id = isset( $_POST['noortemplates_layout_id'] ) ? absint( $_POST['noortemplates_layout_id'] ) : 0;

		if ( $layout_id ) {
			update_post_meta( $post_id, Resolver::PRODUCT_META_KEY, $layout_id );
		} else {
			delete_post_meta( $post_id, Resolver::PRODUCT_META_KEY );
		}

		// Without the split_test feature the fields are rendered disabled
		// (so nothing is posted for them) — skip entirely rather than
		// treat "not posted" as "user unchecked it", which would silently
		// wipe an existing split-test configuration just because the
		// license lapsed before someone next saved the product.
		if ( ! Gate::has_feature( 'split_test' ) ) {
			return;
		}

		if ( ! empty( $_POST['noortemplates_split_enabled'] ) ) {
			update_post_meta( $post_id, Split_Test::ENABLED_META_KEY, '1' );
		} else {
			delete_post_meta( $post_id, Split_Test::ENABLED_META_KEY );
		}

		$variant_b = isset( $_POST['noortemplates_layout_id_b'] ) ? absint( $_POST['noortemplates_layout_id_b'] ) : 0;

		if ( $variant_b ) {
			update_post_meta( $post_id, Split_Test::VARIANT_B_META_KEY, $variant_b );
		} else {
			delete_post_meta( $post_id, Split_Test::VARIANT_B_META_KEY );
		}

		// Never trust the posted value to already be within range.
		$ratio = isset( $_POST['noortemplates_split_ratio'] ) ? absint( $_POST['noortemplates_split_ratio'] ) : 50;
		update_post_meta( $post_id, Split_Test::RATIO_META_KEY, max( 1, min( 99, $ratio ) ) );
	}

	/**
	 * Renders the default-layout field on the category edit screen.
	 *
	 * @param \WP_Term $term Product category term.
	 * @return void
	 */
	public function render_term_edit_field( $term ) {
		$selected = get_term_meta( $term->term_id, Resolver::CATEGORY_META_KEY, true );
		wp_nonce_field( 'noortemplates_term_layout_meta', 'noortemplates_term_layout_nonce' );
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="noortemplates_layout_id"><?php esc_html_e( 'Default Product Page Template', 'noortemplates' ); ?></label>
			</th>
			<td>
				<?php $this->render_select( 'noortemplates_layout_id', (int) $selected ); ?>
				<p class="description">
					<?php esc_html_e( 'Used for products in this category that do not have their own template selected.', 'noortemplates' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Renders the default-layout field on the add-category screen.
	 *
	 * @return void
	 */
	public function render_term_add_field() {
		wp_nonce_field( 'noortemplates_term_layout_meta', 'noortemplates_term_layout_nonce' );
		?>
		<div class="form-field">
			<label for="noortemplates_layout_id"><?php esc_html_e( 'Default Product Page Template', 'noortemplates' ); ?></label>
			<?php $this->render_select( 'noortemplates_layout_id', 0 ); ?>
		</div>
		<?php
	}

	/**
	 * Persists the category's default layout.
	 *
	 * @param int $term_id Product category term ID.
	 * @return void
	 */
	public function save_term_field( $term_id ) {
		if (
			! isset( $_POST['noortemplates_term_layout_nonce'], $_POST['noortemplates_layout_id'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['noortemplates_term_layout_nonce'] ) ), 'noortemplates_term_layout_meta' )
		) {
			return;
		}

		if ( ! current_user_can( 'manage_product_terms' ) ) {
			return;
		}

		$layout_id = absint( $_POST['noortemplates_layout_id'] );

		if ( $layout_id ) {
			update_term_meta( $term_id, Resolver::CATEGORY_META_KEY, $layout_id );
		} else {
			delete_term_meta( $term_id, Resolver::CATEGORY_META_KEY );
		}
	}
}
