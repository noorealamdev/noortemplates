<?php
/**
 * Product Gallery Carousel block server render.
 *
 * Renders the current product's own images (featured + gallery) as a
 * self-contained carousel, instead of wrapping WooCommerce's own
 * flexslider-based gallery output.
 *
 * @package NoorTemplates
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

global $product;
$product = wc_get_product( get_the_ID() ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- $product is WooCommerce's own global.

if ( ! $product ) {
	return;
}

$noortemplates_image_ids = array();
$noortemplates_featured  = $product->get_image_id();

if ( $noortemplates_featured ) {
	$noortemplates_image_ids[] = (int) $noortemplates_featured;
}

foreach ( $product->get_gallery_image_ids() as $noortemplates_gallery_id ) {
	$noortemplates_image_ids[] = (int) $noortemplates_gallery_id;
}

$noortemplates_image_ids = array_values( array_unique( array_filter( $noortemplates_image_ids ) ) );

$noortemplates_show_thumbs = ! isset( $attributes['showThumbnails'] ) || (bool) $attributes['showThumbnails'];
$noortemplates_multiple    = count( $noortemplates_image_ids ) > 1;
$noortemplates_thumb_size  = wc_get_image_size( 'gallery_thumbnail' );
$noortemplates_wrapper     = get_block_wrapper_attributes();
?>
<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<div class="noortemplates-product-gallery-carousel">
		<div class="noortemplates-product-gallery-carousel__stage">
			<div class="noortemplates-product-gallery-carousel__viewport">
				<div class="noortemplates-product-gallery-carousel__track">
					<?php if ( empty( $noortemplates_image_ids ) ) : ?>
						<div class="noortemplates-product-gallery-carousel__slide">
							<div class="noortemplates-product-gallery-carousel__image-wrap">
								<?php echo wc_placeholder_img( 'woocommerce_single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>
							</div>
						</div>
					<?php else : ?>
						<?php foreach ( $noortemplates_image_ids as $noortemplates_id ) : ?>
							<div class="noortemplates-product-gallery-carousel__slide" data-image-id="<?php echo esc_attr( $noortemplates_id ); ?>">
								<div class="noortemplates-product-gallery-carousel__image-wrap">
									<?php
									echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped.
										$noortemplates_id,
										'woocommerce_single',
										false,
										array(
											'class'     => 'noortemplates-product-gallery-carousel__image',
											// Otherwise the browser's native "drag this image out"
											// gesture competes with (and can override) our own
											// pointer-based carousel drag.
											'draggable' => 'false',
										)
									);
									?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $noortemplates_multiple ) : ?>
				<button type="button" class="noortemplates-product-gallery-carousel__arrow noortemplates-product-gallery-carousel__arrow--prev" aria-label="<?php echo esc_attr__( 'Previous image', 'noortemplates' ); ?>">
					<span aria-hidden="true">&#10094;</span>
				</button>
				<button type="button" class="noortemplates-product-gallery-carousel__arrow noortemplates-product-gallery-carousel__arrow--next" aria-label="<?php echo esc_attr__( 'Next image', 'noortemplates' ); ?>">
					<span aria-hidden="true">&#10095;</span>
				</button>
			<?php endif; ?>
		</div>

		<?php if ( $noortemplates_multiple && $noortemplates_show_thumbs ) : ?>
			<div class="noortemplates-product-gallery-carousel__thumbs">
				<?php foreach ( $noortemplates_image_ids as $noortemplates_index => $noortemplates_id ) : ?>
					<button
						type="button"
						class="noortemplates-product-gallery-carousel__thumb<?php echo 0 === $noortemplates_index ? ' is-active' : ''; ?>"
						data-slide-index="<?php echo esc_attr( $noortemplates_index ); ?>"
						aria-label="<?php echo esc_attr( sprintf( __( 'Show image %d', 'noortemplates' ), $noortemplates_index + 1 ) ); ?>"
					>
						<?php
						echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped.
							$noortemplates_id,
							array( $noortemplates_thumb_size['width'], $noortemplates_thumb_size['height'] ),
							false,
							array( 'class' => 'noortemplates-product-gallery-carousel__thumb-image' )
						);
						?>
					</button>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $noortemplates_multiple ) : ?>
			<div class="noortemplates-product-gallery-carousel__dots">
				<?php foreach ( $noortemplates_image_ids as $noortemplates_index => $noortemplates_id ) : ?>
					<button
						type="button"
						class="noortemplates-product-gallery-carousel__dot<?php echo 0 === $noortemplates_index ? ' is-active' : ''; ?>"
						data-slide-index="<?php echo esc_attr( $noortemplates_index ); ?>"
						aria-label="<?php echo esc_attr( sprintf( __( 'Show image %d', 'noortemplates' ), $noortemplates_index + 1 ) ); ?>"
					></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
