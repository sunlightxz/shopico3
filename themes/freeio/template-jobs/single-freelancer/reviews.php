<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post;

if ( ! comments_open() ) {
	return;
}

$total_rating = WP_Freeio_Review::get_ratings_average( $post->ID );
$comment_ratings = WP_Freeio_Review::get_detail_ratings( $post->ID );
$total = WP_Freeio_Review::get_total_reviews( $post->ID );

?>
<div id="reviews">
	<div class="box-info-white max-780">
		<h3 class="title"><?php comments_number( esc_html__('0 Reviews', 'freeio'), esc_html__('1 Review', 'freeio'), esc_html__('% Reviews', 'freeio') ); ?></h3>

		<div class="d-md-flex align-items-center">
			<div class="detail-average-rating flex-column d-flex align-items-center justify-content-center">
				<div class="average-value"><?php echo number_format((float)$total_rating, 1, '.', ''); ?></div>
				<div class="average-star">
					<?php WP_Freeio_Review::print_review( $total_rating ); ?>
				</div>
				<div class="total-rating">
					<?php $total ? printf( _n( '%1$s rating', '%1$s ratings', $total, 'freeio' ), number_format_i18n( $total ) ) : esc_html_e( '0 rating', 'freeio' ); ?>
				</div>
			</div>

			<div class="detail-rating">
				<?php for ( $i = 5; $i >= 1; $i -- ) : ?>
					<div class="item-rating">
						<div class="list-rating">
							
							<div class="value-content">
								<div class="list-number">
									<?php echo sprintf(esc_html__('%s Star','freeio'), $i); ?>
								</div>
								<div class="progress">
									<div class="progress-bar progress-bar-success" style="<?php echo esc_attr(( $total && !empty( $comment_ratings[$i]->quantity ) ) ? esc_attr( 'width: ' . ( $comment_ratings[$i]->quantity / $total * 100 ) . '%' ) : 'width: 0%'); ?>">
									</div>
								</div>
								<div class="value">
									<?php echo trim( ( $total && !empty( $comment_ratings[$i]->quantity ) ) ?  number_format(( $comment_ratings[$i]->quantity / $total * 100 ), 0) . '%' : '0%' ); ?>
								</div>
							</div>
						</div>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	</div>
	
	<?php if ( have_comments() ) : ?>
		<div id="comments">

		<ol class="comment-list">
			<?php wp_list_comments( array( 'callback' => array( 'WP_Freeio_Review', 'freelancer_comments' ) ) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			echo '<nav class="apus-pagination">';
			paginate_comments_links( apply_filters( 'apus_comment_pagination_args', array(
				'prev_text' => '&larr;',
				'next_text' => '&rarr;',
				'type'      => 'list',
			) ) );
			echo '</nav>';
		endif; ?>

		</div>

	<?php endif; ?>
	
	<?php if ( get_current_user_id() != $post->post_author ) { ?>
		<?php $commenter = wp_get_current_commenter(); ?>
		<div id="review_form_wrapper" class="commentform <?php echo trim( (have_comments())?'':'no-comment' ) ?>">
			<div id="review_form">
				<?php
					$comment_form = array(
						'title_reply'          => have_comments() ? esc_html__( 'Add a review', 'freeio' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'freeio' ), get_the_title() ),
						'title_reply_to'       => esc_html__( 'Leave a Reply to %s', 'freeio' ),
						'comment_notes_before' => '',
						'comment_notes_after'  => '',
						'fields'               => array(
							'author' => '<div class="row"><div class="col-12 col-sm-6"><div class="form-group"><label>'.esc_html__( 'Your Name', 'freeio' ).'</label>'.
							            '<input id="author" placeholder="'.esc_attr__( 'Name', 'freeio' ).'" class="form-control" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" aria-required="true" /></div></div>',
							'email'  => '<div class="col-12 col-sm-6"><div class="form-group"><label>'.esc_html__( 'Your Email', 'freeio' ).'</label>' .
							            '<input id="email" placeholder="'.esc_attr__( 'Email', 'freeio' ).'" class="form-control" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-required="true" /></div></div></div>',
						),
						'label_submit'  => esc_html__( 'Submit Review', 'freeio' ),
						'logged_in_as'  => '',
						'comment_field' => '',
						'title_reply_before' => '<h4 class="title comment-reply-title">',
						'title_reply_after'  => '</h4>',
						'class_submit' => 'btn btn-theme btn-inverse'
					);

					$comment_form['must_log_in'] = '<div class="must-log-in">' .__( 'You must be <a href="">logged in</a> to post a review.', 'freeio' ) . '</div>';
					
					$comment_form['comment_field'] .= '<div class="form-group space-30"><label>'.esc_html__( 'Your Comment', 'freeio' ).'</label><textarea id="comment" class="form-control" placeholder="'.esc_attr__( 'Comment', 'freeio' ).'" name="comment" cols="45" rows="5" aria-required="true" required></textarea></div>';
					
					freeio_comment_form($comment_form);
				?>
			</div>
		</div>
	<?php } ?>
</div>