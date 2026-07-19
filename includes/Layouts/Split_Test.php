<?php
/**
 * A/B split testing between two Product Layouts on the same product.
 *
 * @package NoorTemplates
 */

namespace NoorTemplates\Layouts;

use NoorTemplates\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

/**
 * Buckets visitors into Variant A/B via a sticky cookie and logs raw
 * impression/add-to-cart/purchase counters per variant.
 *
 * Deliberately lightweight: no statistical-significance math, no
 * date-bucketed trends, no unique-visitor dedup beyond the cookie itself,
 * and no full-page-cache compatibility (assumes product pages aren't
 * cached — a caching plugin/CDN would serve the same cached variant to
 * every visitor regardless of their cookie).
 */
class Split_Test {

	use Singleton;

	/**
	 * Post meta key storing Variant B's layout ID. Variant A reuses
	 * Resolver::PRODUCT_META_KEY — there is no separate key for it.
	 */
	const VARIANT_B_META_KEY = '_noortemplates_layout_id_b';

	/**
	 * Post meta key storing whether a split test is currently running.
	 */
	const ENABLED_META_KEY = '_noortemplates_split_enabled';

	/**
	 * Post meta key storing the percentage of traffic sent to Variant B.
	 */
	const RATIO_META_KEY = '_noortemplates_split_ratio';

	/**
	 * Order meta key guarding against double-logging a purchase — the
	 * thank-you page fires on every reload, not just the first one.
	 */
	const ORDER_LOGGED_META_KEY = '_noortemplates_split_logged';

	/**
	 * Cookie name prefix; the product ID is appended.
	 */
	const COOKIE_PREFIX = 'noortemplates_split_';

	/**
	 * How long a visitor's variant assignment sticks, in seconds.
	 */
	const COOKIE_TTL = 30 * DAY_IN_SECONDS;

	/**
	 * Per-request cache of resolved variants, keyed by product ID.
	 *
	 * Resolver::get_layout_id() is called more than once per pageview
	 * (once from Template_Override, once from the layout template itself);
	 * without this, the second call would re-roll a fresh random
	 * assignment (since setcookie() doesn't update $_COOKIE mid-request)
	 * and double-log an impression.
	 *
	 * @var array<int, string>
	 */
	private $resolved = array();

	/**
	 * Hooks WooCommerce's cart/order events.
	 */
	protected function __construct() {
		add_action( 'woocommerce_add_to_cart', array( $this, 'log_add_to_cart' ), 10, 2 );
		add_action( 'woocommerce_thankyou', array( $this, 'log_purchase' ) );
	}

	/**
	 * Whether a product has a valid, currently-running split test.
	 *
	 * @param int $product_id Product post ID.
	 * @return bool
	 */
	public function has_active_test( $product_id ) {
		if ( '1' !== get_post_meta( $product_id, self::ENABLED_META_KEY, true ) ) {
			return false;
		}

		$variant_a = (int) get_post_meta( $product_id, Resolver::PRODUCT_META_KEY, true );
		$variant_b = (int) get_post_meta( $product_id, self::VARIANT_B_META_KEY, true );

		return $this->is_published_layout( $variant_a ) && $this->is_published_layout( $variant_b );
	}

	/**
	 * Resolves (bucketing + logging as needed) which layout a visitor
	 * should see for a product under an active split test.
	 *
	 * @param int $product_id Product post ID.
	 * @return int Layout post ID for the visitor's assigned variant.
	 */
	public function resolve_variant( $product_id ) {
		if ( isset( $this->resolved[ $product_id ] ) ) {
			return $this->variant_layout_id( $product_id, $this->resolved[ $product_id ] );
		}

		$variant = $this->get_cookie_variant( $product_id );

		if ( ! $variant ) {
			$ratio   = $this->get_ratio( $product_id );
			$variant = wp_rand( 1, 100 ) <= $ratio ? 'b' : 'a';

			wc_setcookie( self::COOKIE_PREFIX . $product_id, $variant, time() + self::COOKIE_TTL );

			Split_Test_Stats::increment( $product_id, $variant, 'impression' );
		}

		$this->resolved[ $product_id ] = $variant;

		return $this->variant_layout_id( $product_id, $variant );
	}

	/**
	 * Logs an add-to-cart conversion for the visitor's assigned variant.
	 *
	 * Skips silently when no variant cookie exists yet — e.g. a product
	 * added via a shop-grid AJAX button without ever visiting its page.
	 * We do not force-bucket a visitor just because they added to cart.
	 *
	 * @param string $cart_item_key Cart item key (unused).
	 * @param int    $product_id    Product post ID.
	 * @return void
	 */
	public function log_add_to_cart( $cart_item_key, $product_id ) {
		if ( ! $this->has_active_test( $product_id ) ) {
			return;
		}

		$variant = $this->get_cookie_variant( $product_id );

		if ( $variant ) {
			Split_Test_Stats::increment( $product_id, $variant, 'add_to_cart' );
		}
	}

	/**
	 * Logs a purchase conversion for each order line under an active test.
	 *
	 * Guarded by order meta since `woocommerce_thankyou` fires on every
	 * pageview of the order-received screen (reloads, gateway
	 * double-redirects), not just once.
	 *
	 * @param int $order_id Order post ID.
	 * @return void
	 */
	public function log_purchase( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order || 'yes' === $order->get_meta( self::ORDER_LOGGED_META_KEY ) ) {
			return;
		}

		foreach ( $order->get_items() as $item ) {
			$product_id = $item->get_product_id();

			if ( ! $this->has_active_test( $product_id ) ) {
				continue;
			}

			$variant = $this->get_cookie_variant( $product_id );

			if ( $variant ) {
				Split_Test_Stats::increment( $product_id, $variant, 'purchase' );
			}
		}

		$order->update_meta_data( self::ORDER_LOGGED_META_KEY, 'yes' );
		$order->save();
	}

	/**
	 * Reads a product's variant assignment from its cookie, if any.
	 *
	 * @param int $product_id Product post ID.
	 * @return string 'a', 'b', or '' when no valid cookie is set.
	 */
	private function get_cookie_variant( $product_id ) {
		$name = self::COOKIE_PREFIX . $product_id;

		if ( isset( $_COOKIE[ $name ] ) ) {
			$value = sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) );

			if ( in_array( $value, array( 'a', 'b' ), true ) ) {
				return $value;
			}
		}

		return '';
	}

	/**
	 * Returns the layout post ID for a resolved variant.
	 *
	 * @param int    $product_id Product post ID.
	 * @param string $variant    'a' or 'b'.
	 * @return int
	 */
	private function variant_layout_id( $product_id, $variant ) {
		return 'b' === $variant
			? (int) get_post_meta( $product_id, self::VARIANT_B_META_KEY, true )
			: (int) get_post_meta( $product_id, Resolver::PRODUCT_META_KEY, true );
	}

	/**
	 * Returns the configured Variant B traffic percentage (1-99).
	 *
	 * @param int $product_id Product post ID.
	 * @return int
	 */
	private function get_ratio( $product_id ) {
		$ratio = (int) get_post_meta( $product_id, self::RATIO_META_KEY, true );

		return $ratio ? max( 1, min( 99, $ratio ) ) : 50;
	}

	/**
	 * Whether a layout ID points to a published Product Layout post.
	 *
	 * Mirrors Resolver::is_published_layout() — kept separate rather than
	 * exposed publicly there, consistent with this codebase's existing
	 * tolerance for this kind of small duplication (e.g. get_layouts()
	 * appears near-identically in both Meta_Box and Dashboard).
	 *
	 * @param int $layout_id Layout post ID.
	 * @return bool
	 */
	private function is_published_layout( $layout_id ) {
		return $layout_id
			&& Post_Type::SLUG === get_post_type( $layout_id )
			&& 'publish' === get_post_status( $layout_id );
	}
}
