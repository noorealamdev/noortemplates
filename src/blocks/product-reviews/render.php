<?php
/**
 * Product Reviews block server render.
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

$noortemplates_ratings_enabled = wc_review_ratings_enabled();
$noortemplates_average         = (float) $product->get_average_rating();
$noortemplates_rating_counts   = $product->get_rating_counts();

/*
 * get_review_count() counts every approved comment on the product, which
 * can be higher than the number that actually carry a rating (e.g. legacy
 * comments imported without one) — using it as the percentage denominator
 * would make the breakdown bars never add up to 100%. The sum of
 * get_rating_counts() is the real total of *rated* reviews, so every
 * number shown (count, bars, percentages) stays internally consistent.
 */
$noortemplates_review_count = array_sum( $noortemplates_rating_counts );

$noortemplates_breakdown = array();

for ( $noortemplates_star = 5; $noortemplates_star >= 1; $noortemplates_star-- ) {
	$noortemplates_star_count               = isset( $noortemplates_rating_counts[ $noortemplates_star ] ) ? (int) $noortemplates_rating_counts[ $noortemplates_star ] : 0;
	$noortemplates_breakdown[ $noortemplates_star ] = array(
		'count'      => $noortemplates_star_count,
		'percentage' => $noortemplates_review_count ? round( ( $noortemplates_star_count / $noortemplates_review_count ) * 100 ) : 0,
	);
}

$noortemplates_per_page = max( 1, absint( $attributes['reviewsToShow'] ) );

// Fetches one extra review to detect whether a "Load more" button is
// needed, without a second query.
$noortemplates_reviews  = \NoorTemplates\Blocks\Product_Reviews_Renderer::get_reviews( $product->get_id(), 0, $noortemplates_per_page + 1 );
$noortemplates_has_more = count( $noortemplates_reviews ) > $noortemplates_per_page;
$noortemplates_reviews  = array_slice( $noortemplates_reviews, 0, $noortemplates_per_page );

$noortemplates_wrapper = get_block_wrapper_attributes();

// Self-contained max-width instead of relying on the theme/template to
// constrain block content — this block can land in templates that render
// blocks without any width-limiting ancestor. The modal further below sits
// outside this wrapper since it's position:fixed and already ignores
// ancestor width.
$noortemplates_boxed       = ! isset( $attributes['boxed'] ) || (bool) $attributes['boxed'];
$noortemplates_boxed_width = isset( $attributes['boxedWidth'] ) ? absint( $attributes['boxedWidth'] ) : 1200;
?>
<div <?php echo $noortemplates_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>

	<div
		class="noortemplates-product-reviews__inner<?php echo $noortemplates_boxed ? ' is-boxed' : ''; ?>"
		<?php if ( $noortemplates_boxed ) : ?>
			style="max-width:<?php echo esc_attr( $noortemplates_boxed_width ); ?>px"
		<?php endif; ?>
	>
		<div class="noortemplates-product-reviews__summary">
			<div class="noortemplates-product-reviews__summary-main">
				<?php if ( $noortemplates_ratings_enabled ) : ?>
					<div class="noortemplates-product-reviews__average"><?php echo esc_html( number_format_i18n( $noortemplates_average, 1 ) ); ?></div>
					<?php echo wc_get_rating_html( $noortemplates_average, $noortemplates_review_count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>
				<?php endif; ?>
				<p class="noortemplates-product-reviews__count">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: number of reviews. */
							_n( 'Based on %s review', 'Based on %s reviews', $noortemplates_review_count, 'noortemplates' ),
							number_format_i18n( $noortemplates_review_count )
						)
					);
					?>
				</p>
				<?php if ( comments_open() ) : ?>
					<button
						type="button"
						class="noortemplates-product-reviews__write-link"
						data-review-modal-target="noortemplates-review-modal-<?php echo esc_attr( $product->get_id() ); ?>"
					>
						<?php esc_html_e( 'Write a Review', 'noortemplates' ); ?>
					</button>
				<?php endif; ?>
			</div>

			<?php if ( $noortemplates_ratings_enabled && $noortemplates_review_count ) : ?>
				<div class="noortemplates-product-reviews__breakdown">
					<?php foreach ( $noortemplates_breakdown as $noortemplates_star => $noortemplates_row ) : ?>
						<div class="noortemplates-product-reviews__breakdown-row">
							<span class="noortemplates-product-reviews__breakdown-label">
								<?php
								echo esc_html(
									sprintf(
										/* translators: %d: star rating, 1-5. */
										_n( '%d star', '%d stars', $noortemplates_star, 'noortemplates' ),
										$noortemplates_star
									)
								);
								?>
							</span>
							<span class="noortemplates-product-reviews__breakdown-track">
								<span class="noortemplates-product-reviews__breakdown-fill" style="width:<?php echo esc_attr( $noortemplates_row['percentage'] ); ?>%"></span>
							</span>
							<span class="noortemplates-product-reviews__breakdown-percentage"><?php echo esc_html( $noortemplates_row['percentage'] ); ?>%</span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $noortemplates_reviews ) ) : ?>
			<div class="noortemplates-product-reviews__grid columns-<?php echo esc_attr( absint( $attributes['columns'] ) ); ?>">
				<?php foreach ( $noortemplates_reviews as $noortemplates_review ) : ?>
					<?php
					echo \NoorTemplates\Blocks\Product_Reviews_Renderer::render_card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- self-escaping.
						$noortemplates_review['comment'],
						$noortemplates_review['rating'],
						$noortemplates_ratings_enabled
					);
					?>
				<?php endforeach; ?>
			</div>
			<?php if ( $noortemplates_has_more ) : ?>
				<div class="noortemplates-product-reviews__load-more-wrap">
					<button
						type="button"
						class="noortemplates-product-reviews__load-more"
						data-rest-url="<?php echo esc_url( rest_url( 'noortemplates/v1/product-reviews/' . $product->get_id() ) ); ?>"
						data-offset="<?php echo esc_attr( $noortemplates_per_page ); ?>"
						data-per-page="<?php echo esc_attr( $noortemplates_per_page ); ?>"
						data-loading-text="<?php echo esc_attr__( 'Loading…', 'noortemplates' ); ?>"
					>
						<?php esc_html_e( 'Load more reviews', 'noortemplates' ); ?>
					</button>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<p class="noortemplates-product-reviews__empty"><?php esc_html_e( 'There are no reviews yet.', 'noortemplates' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( comments_open() ) : ?>
		<div
			id="noortemplates-review-modal-<?php echo esc_attr( $product->get_id() ); ?>"
			class="noortemplates-product-reviews__modal"
			hidden
			aria-hidden="true"
		>
			<div class="noortemplates-product-reviews__modal-backdrop" data-review-modal-close></div>
			<div class="noortemplates-product-reviews__modal-panel" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__( 'Write a review', 'noortemplates' ); ?>">
				<button type="button" class="noortemplates-product-reviews__modal-close" data-review-modal-close aria-label="<?php echo esc_attr__( 'Close', 'noortemplates' ); ?>">
					&times;
				</button>
			<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
				<div id="review_form_wrapper">
					<div id="review_form">
						<?php
						$noortemplates_commenter    = wp_get_current_commenter();
						$noortemplates_comment_form = array(
							/* translators: %s is product title */
							'title_reply'         => $noortemplates_review_count ? esc_html__( 'Add a review', 'noortemplates' ) : sprintf( esc_html__( 'Be the first to review "%s"', 'noortemplates' ), get_the_title() ),
							/* translators: %s is product title */
							'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'noortemplates' ),
							'title_reply_before'  => '<span id="reply-title" class="comment-reply-title" role="heading" aria-level="3">',
							'title_reply_after'   => '</span>',
							'comment_notes_after' => '',
							'label_submit'        => esc_html__( 'Submit', 'noortemplates' ),
							'logged_in_as'        => '',
							'comment_field'       => '',
						);

						$noortemplates_name_email_required = (bool) get_option( 'require_name_email', 1 );
						$noortemplates_fields              = array(
							'author' => array(
								'label'        => __( 'Name', 'noortemplates' ),
								'type'         => 'text',
								'value'        => $noortemplates_commenter['comment_author'],
								'required'     => $noortemplates_name_email_required,
								'autocomplete' => 'name',
							),
							'email'  => array(
								'label'        => __( 'Email', 'noortemplates' ),
								'type'         => 'email',
								'value'        => $noortemplates_commenter['comment_author_email'],
								'required'     => $noortemplates_name_email_required,
								'autocomplete' => 'email',
							),
						);

						$noortemplates_comment_form['fields'] = array();

						foreach ( $noortemplates_fields as $noortemplates_key => $noortemplates_field ) {
							$noortemplates_field_html  = '<p class="comment-form-' . esc_attr( $noortemplates_key ) . '">';
							$noortemplates_field_html .= '<label for="' . esc_attr( $noortemplates_key ) . '">' . esc_html( $noortemplates_field['label'] );

							if ( $noortemplates_field['required'] ) {
								$noortemplates_field_html .= '&nbsp;<span class="required">*</span>';
							}

							$noortemplates_field_html .= '</label><input id="' . esc_attr( $noortemplates_key ) . '" name="' . esc_attr( $noortemplates_key ) . '" type="' . esc_attr( $noortemplates_field['type'] ) . '" autocomplete="' . esc_attr( $noortemplates_field['autocomplete'] ) . '" value="' . esc_attr( $noortemplates_field['value'] ) . '" size="30" ' . ( $noortemplates_field['required'] ? 'required' : '' ) . ' /></p>';

							$noortemplates_comment_form['fields'][ $noortemplates_key ] = $noortemplates_field_html;
						}

						$noortemplates_account_page_url = wc_get_page_permalink( 'myaccount' );
						if ( $noortemplates_account_page_url ) {
							/* translators: %1$s/%2$s: opening/closing link tags */
							$noortemplates_comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'noortemplates' ), '<a href="' . esc_url( $noortemplates_account_page_url ) . '">', '</a>' ) . '</p>';
						}

						if ( $noortemplates_ratings_enabled ) {
							$noortemplates_comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating" id="comment-form-rating-label">' . esc_html__( 'Your rating', 'noortemplates' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" ' . ( wc_review_ratings_required() ? 'required' : '' ) . '>
								<option value="">' . esc_html__( 'Rate…', 'noortemplates' ) . '</option>
								<option value="5">' . esc_html__( 'Perfect', 'noortemplates' ) . '</option>
								<option value="4">' . esc_html__( 'Good', 'noortemplates' ) . '</option>
								<option value="3">' . esc_html__( 'Average', 'noortemplates' ) . '</option>
								<option value="2">' . esc_html__( 'Not that bad', 'noortemplates' ) . '</option>
								<option value="1">' . esc_html__( 'Very poor', 'noortemplates' ) . '</option>
							</select></div>';
						}

						$noortemplates_comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'noortemplates' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

						comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $noortemplates_comment_form ) );
						?>
					</div>
				</div>
			<?php else : ?>
				<p class="noortemplates-product-reviews__verification-required">
					<?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'noortemplates' ); ?>
				</p>
			<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
