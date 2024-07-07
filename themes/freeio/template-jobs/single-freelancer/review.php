<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post;
$rating = get_comment_meta( $comment->comment_ID, '_rating', true );

?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

	<div id="comment-<?php comment_ID(); ?>" class="the-comment ">
		<div class="avatar">
			<?php
				if ( empty($comment->user_id) ) {
					echo get_avatar( $comment->user_id, '80', '' );
				} elseif ( WP_Freeio_User::is_freelancer($comment->user_id) ) {
					$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($comment->user_id);
					
					$freelancer_p = get_post($freelancer_id);
					freeio_freelancer_display_logo($freelancer_p, true, 'thumbnail', 'class=avatar');
				}  elseif ( WP_Freeio_User::is_employer($comment->user_id) ) {
					$employer_id = WP_Freeio_User::get_employer_by_user_id($comment->user_id);
					
					$employer_p = get_post($employer_id);
					freeio_employer_display_logo($employer_p, true, 'thumbnail', 'class=avatar');
				} else {
					echo get_avatar( $comment->user_id, '80', '' );
				}
			?>
		</div>
		<div class="comment-box">
			<div class="clearfix">
				<div class="name-comment">
					<?php
						if ( empty($comment->user_id) ) {
							comment_author();
						} elseif ( WP_Freeio_User::is_freelancer($comment->user_id) ) {
							$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($comment->user_id);
							echo get_the_title($freelancer_id);
						}  elseif ( WP_Freeio_User::is_employer($comment->user_id) ) {
							$employer_id = WP_Freeio_User::get_employer_by_user_id($comment->user_id);
							echo get_the_title($employer_id);
						} else {
							comment_author();
						}
					?>
				</div>
				<div class="meta d-flex align-items-center">
					<div class="star-rating" title="<?php echo sprintf( esc_attr__( 'Rated %d out of 5', 'freeio' ), $rating ) ?>">
						<span class="review-avg"><i class="fa fa-star"></i><?php echo number_format((float)$rating, 1, '.', ''); ?></span>
					</div>
					<?php if ( $comment->comment_approved == '0' ) : ?>
						<div class="date"><em><?php esc_html_e( 'Your comment is awaiting approval', 'freeio' ); ?></em></div>
					<?php else : ?>
						<div class="date">
							<?php echo get_comment_date( get_option('date_format', 'd M, Y') ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div itemprop="description" class="comment-text">
			<?php comment_text(); ?>
		</div>
	</div>