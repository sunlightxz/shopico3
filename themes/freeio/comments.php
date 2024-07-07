<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package WordPress
 * @subpackage Freeio
 * @since Freeio 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<div class="box-comment">
	        <h3 class="comments-title"><?php comments_number( esc_html__('0 Comments', 'freeio'), esc_html__('1 Comment', 'freeio'), esc_html__('% Comments', 'freeio') ); ?></h3>
			<ol class="comment-list">
				<?php wp_list_comments('callback=freeio_comment_item'); ?>
			</ol><!-- .comment-list -->

			<?php freeio_comment_nav(); ?>
		</div>	
	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'freeio' ); ?></p>
	<?php endif; ?>

	<?php
        $aria_req = ( $req ? " aria-required='true'" : '' );
        $comment_args = array(
                        'title_reply'=> esc_html__('Leave a Comment','freeio'),
                        'comment_field' => '<div class="form-group space-comment"><label>'.esc_html__('Comment', 'freeio').'</label>
                                                <textarea rows="7" id="comment" class="form-control" placeholder="'.esc_attr__('Enter Your Comments', 'freeio').'" name="comment"'.$aria_req.'></textarea>
                                            </div>',
                        'fields' => apply_filters(
                        	'comment_form_default_fields',
	                    		array(
	                                'author' => '<div class="row"><div class="col-12 col-md-6"><div class="form-group "><label>'.esc_html__('Name', 'freeio').'</label>
	                                            <input type="text" name="author" class="form-control" id="author" placeholder="'.esc_attr__('Name', 'freeio').'" value="' . esc_attr( $commenter['comment_author'] ) . '" ' . $aria_req . ' />
	                                            </div></div>',
	                                'email' => ' <div class="col-12 col-md-6"><div class="form-group "><label>'.esc_html__('Email', 'freeio').'</label>
	                                            <input id="email"  name="email" class="form-control" type="text" placeholder="'.esc_attr__('Email', 'freeio').'" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" ' . $aria_req . ' />
	                                            </div></div>',
	                                'Website' => ' <div class="col-12 col-sm-4 d-none"><div class="form-group ">
	                                            <input id="website" name="website" placeholder="'.esc_attr__('Website', 'freeio').'" class="form-control" type="text" value="' . esc_attr(  $commenter['comment_author_url'] ) . '" ' . $aria_req . ' />
	                                            </div></div></div>',
	                            )
							),
	                        'label_submit' => esc_html__('Submit Comment', 'freeio'),
							'comment_notes_before' => '',
							'comment_notes_after' => '',
							'title_reply_before' => '<h4 class="comment-reply-title">',
							'title_reply_after'  => '</h4>',
							'class_submit' => 'btn btn-theme'
                        );
    ?>

	<?php freeio_comment_form($comment_args); ?>
</div><!-- .comments-area -->