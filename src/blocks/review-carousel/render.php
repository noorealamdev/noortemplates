<?php
/**
 * Review Carousel block server render.
 *
 * @package NoorTemplates
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

$noortemplates_product_id = absint( $attributes['productId'] );
$noortemplates_selected   = array_map( 'absint', (array) $attributes['selectedReviews'] );

if ( ! $noortemplates_product_id || empty( $noortemplates_selected ) ) {
	return;
}

$noortemplates_product = wc_get_product( $noortemplates_product_id );

if ( ! $noortemplates_product ) {
	return;
}

$noortemplates_comments = get_comments(
	array(
		'post_id'     => $noortemplates_product_id,
		'status'      => 'approve',
		'comment__in' => $noortemplates_selected,
	)
);

// Reorders results to match the curated selection order (get_comments()
// doesn't preserve comment__in order), dropping any picked review that's
// since been unapproved or deleted.
$noortemplates_comments_by_id = array();

foreach ( $noortemplates_comments as $noortemplates_comment ) {
	$noortemplates_comments_by_id[ (int) $noortemplates_comment->comment_ID ] = $noortemplates_comment;
}

$noortemplates_ordered = array();

foreach ( $noortemplates_selected as $noortemplates_id ) {
	if ( isset( $noortemplates_comments_by_id[ $noortemplates_id ] ) ) {
		$noortemplates_ordered[] = $noortemplates_comments_by_id[ $noortemplates_id ];
	}
}

if ( empty( $noortemplates_ordered ) ) {
	return;
}

$noortemplates_ratings_enabled = wc_review_ratings_enabled();
$noortemplates_wrapper         = get_block_wrapper_attributes();
$noortemplates_multiple        = count( $noortemplates_ordered ) > 1;

// Self-contained max-width instead of relying on the theme/template to
// constrain block content — this block can land in templates that render
// blocks without any width-limiting ancestor.
$noortemplates_boxed       = ! isset( $attributes['boxed'] ) || (bool) $attributes['boxed'];
$noortemplates_boxed_width = isset( $attributes['boxedWidth'] ) ? absint( $attributes['boxedWidth'] ) : 1200;
?>
<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<div
		class="noortemplates-review-carousel<?php echo $noortemplates_boxed ? ' is-boxed' : ''; ?>"
		<?php if ( $noortemplates_boxed ) : ?>
			style="max-width:<?php echo esc_attr( $noortemplates_boxed_width ); ?>px"
		<?php endif; ?>
	>
		<div class="noortemplates-review-carousel__stage">
			<div class="noortemplates-review-carousel__viewport">
				<div class="noortemplates-review-carousel__track">
					<?php foreach ( $noortemplates_ordered as $noortemplates_comment ) : ?>
						<?php
						$noortemplates_rating   = (float) get_comment_meta( $noortemplates_comment->comment_ID, 'rating', true );
						$noortemplates_verified = wc_review_is_from_verified_owner( $noortemplates_comment->comment_ID );
						?>
						<div class="noortemplates-review-carousel__slide">
							<div class="noortemplates-review-carousel__card">
								<?php if ( $noortemplates_ratings_enabled ) : ?>
									<?php echo wc_get_rating_html( $noortemplates_rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>
								<?php endif; ?>
								<p class="noortemplates-review-carousel__content">
									<?php echo wp_kses_post( get_comment_text( $noortemplates_comment ) ); ?>
								</p>
								<div class="noortemplates-review-carousel__footer">
									<?php if ( $noortemplates_verified ) : ?>
										<span class="noortemplates-review-carousel__verified">
											<?php esc_html_e( 'Verified Purchase', 'noortemplates' ); ?>
										</span>
									<?php endif; ?>
									<span class="noortemplates-review-carousel__name">
										&#8211;<?php echo esc_html( $noortemplates_comment->comment_author ); ?>
									</span>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php if ( $noortemplates_multiple ) : ?>
				<button type="button" class="noortemplates-review-carousel__arrow noortemplates-review-carousel__arrow--prev" aria-label="<?php echo esc_attr__( 'Previous review', 'noortemplates' ); ?>">
					<span aria-hidden="true">&#10094;</span>
				</button>
				<button type="button" class="noortemplates-review-carousel__arrow noortemplates-review-carousel__arrow--next" aria-label="<?php echo esc_attr__( 'Next review', 'noortemplates' ); ?>">
					<span aria-hidden="true">&#10095;</span>
				</button>
			<?php endif; ?>
		</div>

		<?php if ( $noortemplates_multiple ) : ?>
			<div class="noortemplates-review-carousel__dots">
				<?php foreach ( $noortemplates_ordered as $noortemplates_index => $noortemplates_comment ) : ?>
					<?php
					/* translators: %d: review number in the carousel. */
					$noortemplates_dot_label = sprintf( __( 'Go to review %d', 'noortemplates' ), $noortemplates_index + 1 );
					?>
					<button
						type="button"
						class="noortemplates-review-carousel__dot<?php echo 0 === $noortemplates_index ? ' is-active' : ''; ?>"
						data-slide-index="<?php echo esc_attr( $noortemplates_index ); ?>"
						aria-label="<?php echo esc_attr( $noortemplates_dot_label ); ?>"
					></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
