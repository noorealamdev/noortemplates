<?php
/**
 * Product Grid block server render.
 *
 * @package NoorTemplates
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content (unused, dynamic block).
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$noortemplates_relation = in_array( $attributes['relation'], array( 'latest', 'related', 'upsells', 'cross-sells' ), true )
	? $attributes['relation']
	: 'latest';
$noortemplates_current  = wc_get_product( get_the_ID() );
$noortemplates_to_show  = absint( $attributes['productsToShow'] );

if ( 'related' === $noortemplates_relation && $noortemplates_current ) {
	$noortemplates_ids = wc_get_related_products( $noortemplates_current->get_id(), $noortemplates_to_show );
} elseif ( 'upsells' === $noortemplates_relation && $noortemplates_current ) {
	$noortemplates_ids = array_slice( $noortemplates_current->get_upsell_ids(), 0, $noortemplates_to_show );
} elseif ( 'cross-sells' === $noortemplates_relation && $noortemplates_current ) {
	$noortemplates_ids = array_slice( $noortemplates_current->get_cross_sell_ids(), 0, $noortemplates_to_show );
} else {
	$noortemplates_ids = array();
}

if ( 'latest' !== $noortemplates_relation && empty( $noortemplates_ids ) ) {
	// Related/upsell products requested but none exist for the current product.
	$noortemplates_query = new WP_Query();
} elseif ( 'latest' !== $noortemplates_relation ) {
	$noortemplates_query = new WP_Query(
		array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'post__in'            => $noortemplates_ids,
			'orderby'             => 'post__in',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
} else {
	$noortemplates_query = new WP_Query(
		array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'posts_per_page'      => $noortemplates_to_show,
			'order'               => 'asc' === strtolower( $attributes['order'] ) ? 'ASC' : 'DESC',
			'orderby'             => in_array( $attributes['orderBy'], array( 'date', 'title', 'rand' ), true ) ? $attributes['orderBy'] : 'date',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
}

// Self-contained max-width instead of relying on the theme/template to
// constrain block content — applied to this block's own wrapper (rather
// than an inner div, like other blocks in this plugin) because that
// wrapper is also the CSS grid container itself; an extra nested div would
// separate `display: grid` from the item children it needs to apply to.
$noortemplates_boxed       = ! isset( $attributes['boxed'] ) || (bool) $attributes['boxed'];
$noortemplates_boxed_width = isset( $attributes['boxedWidth'] ) ? absint( $attributes['boxedWidth'] ) : 1200;

$noortemplates_wrapper_args = array( 'class' => 'columns-' . absint( $attributes['columns'] ) );

if ( $noortemplates_boxed ) {
	$noortemplates_wrapper_args['class'] .= ' is-boxed';
	$noortemplates_wrapper_args['style']  = 'max-width:' . $noortemplates_boxed_width . 'px';
}

$noortemplates_wrapper = get_block_wrapper_attributes( $noortemplates_wrapper_args );
?>
<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<?php if ( ! $noortemplates_query->have_posts() ) : ?>
		<p class="noortemplates-product-grid__empty"><?php esc_html_e( 'No products found.', 'noortemplates' ); ?></p>
	<?php endif; ?>

	<?php
	while ( $noortemplates_query->have_posts() ) :
		$noortemplates_query->the_post();

		$noortemplates_product = wc_get_product( get_the_ID() );

		if ( ! $noortemplates_product ) {
			continue;
		}
		?>
		<div class="noortemplates-product-grid__item">
			<?php if ( $attributes['showImage'] ) : ?>
				<a class="noortemplates-product-grid__image" href="<?php the_permalink(); ?>">
					<?php echo wp_kses_post( $noortemplates_product->get_image( 'medium_large' ) ); ?>
				</a>
			<?php endif; ?>

			<h3 class="noortemplates-product-grid__title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>

			<?php if ( $attributes['showPrice'] ) : ?>
				<div class="noortemplates-product-grid__price">
					<?php echo wp_kses_post( $noortemplates_product->get_price_html() ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $attributes['showAddToCart'] ) : ?>
				<div class="noortemplates-product-grid__add-to-cart">
					<?php
					echo wp_kses_post(
						apply_filters(
							'woocommerce_loop_add_to_cart_link', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own filter.
							sprintf(
								'<a href="%s" data-quantity="1" class="button product_type_%s add_to_cart_button ajax_add_to_cart" data-product_id="%s" aria-label="%s" rel="nofollow">%s</a>',
								esc_url( $noortemplates_product->add_to_cart_url() ),
								esc_attr( $noortemplates_product->get_type() ),
								esc_attr( $noortemplates_product->get_id() ),
								esc_attr( $noortemplates_product->add_to_cart_description() ),
								esc_html( $noortemplates_product->add_to_cart_text() )
							),
							$noortemplates_product,
							array()
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</div>
<?php
wp_reset_postdata();
