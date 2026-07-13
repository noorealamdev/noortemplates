<?php
/**
 * Post Grid block server render.
 *
 * @package NoorBlocks
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content (unused, dynamic block).
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$noorblocks_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => absint( $attributes['postsToShow'] ),
		'order'               => 'asc' === strtolower( $attributes['order'] ) ? 'ASC' : 'DESC',
		'orderby'             => in_array( $attributes['orderBy'], array( 'date', 'title', 'rand' ), true ) ? $attributes['orderBy'] : 'date',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

$noorblocks_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'columns-' . absint( $attributes['columns'] ) )
);

$noorblocks_read_more = '' !== trim( $attributes['readMoreText'] )
	? $attributes['readMoreText']
	: __( 'Read more', 'noorblocks' );
?>
<div <?php echo $noorblocks_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core-escaped. ?>>
	<?php if ( ! $noorblocks_query->have_posts() ) : ?>
		<p class="noorblocks-post-grid__empty"><?php esc_html_e( 'No posts found.', 'noorblocks' ); ?></p>
	<?php endif; ?>

	<?php
	while ( $noorblocks_query->have_posts() ) :
		$noorblocks_query->the_post();
		?>
		<article class="noorblocks-post-grid__item">
			<?php if ( $attributes['showFeaturedImage'] && has_post_thumbnail() ) : ?>
				<a class="noorblocks-post-grid__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
					<?php the_post_thumbnail( 'medium_large' ); ?>
				</a>
			<?php endif; ?>

			<h3 class="noorblocks-post-grid__title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>

			<?php if ( $attributes['showDate'] ) : ?>
				<time class="noorblocks-post-grid__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
			<?php endif; ?>

			<?php if ( $attributes['showExcerpt'] ) : ?>
				<p class="noorblocks-post-grid__excerpt">
					<?php echo esc_html( wp_trim_words( get_the_excerpt(), absint( $attributes['excerptLength'] ) ) ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $attributes['showReadMore'] ) : ?>
				<a class="noorblocks-post-grid__more" href="<?php the_permalink(); ?>">
					<?php echo esc_html( $noorblocks_read_more ); ?>
					<span class="screen-reader-text">: <?php the_title(); ?></span>
				</a>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</div>
<?php
wp_reset_postdata();
