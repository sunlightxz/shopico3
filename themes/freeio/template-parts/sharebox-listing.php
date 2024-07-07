<?php
global $post;

if ( $post->post_type == 'job_listing' ) {
	$author_id = freeio_get_post_author($post->ID);
	$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
	$img = '';
	if ( has_post_thumbnail($employer_id) ) {
		$img = get_the_post_thumbnail_url($employer_id, 'full');
	}
} else {
	$img = '';
	if ( has_post_thumbnail($post->ID) ) {
		$img = get_the_post_thumbnail_url($post->ID, 'full');
	}
}

?>
<div class="apus-social-share share-listing position-relative">
	<div class="d-flex align-items-center">
		<span class="icon-share"><i class="flaticon-share"></i></span>
		<h6 class="share-title">
			<?php esc_html_e('Share', 'freeio'); ?>
		</h6>
	</div>
	<div class="bo-social-icons">
		
		<?php if ( freeio_get_config('facebook_share', 1) ): ?>
 
			<a class="facebook" href="https://www.facebook.com/sharer.php?s=100&u=<?php the_permalink(); ?>&i=<?php echo urlencode($img); ?>" target="_blank" title="<?php echo esc_attr__('Share on facebook', 'freeio'); ?>">
				<i class="fab fa-facebook-f"></i>
			</a>
 
		<?php endif; ?>
		<?php if ( freeio_get_config('twitter_share', 1) ): ?>
			<a class="twitter" href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>" target="_blank" title="<?php echo esc_attr__('Share on Twitter', 'freeio'); ?>">
				<i class="fab fa-x-twitter"></i>
			</a>
 
		<?php endif; ?>
		<?php if ( freeio_get_config('linkedin_share', 1) ): ?>
 
			<a class="linkedin" href="https://linkedin.com/shareArticle?mini=true&amp;url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" target="_blank" title="<?php echo esc_attr__('Share on LinkedIn', 'freeio'); ?>">
				<i class="fab fa-linkedin-in"></i>
			</a>
 
		<?php endif; ?>

		<?php if ( freeio_get_config('pinterest_share', 1) ): ?>
 
			<a class="pinterest" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?>&amp;media=<?php echo urlencode($img); ?>" target="_blank" title="<?php echo esc_attr__('Share on Pinterest', 'freeio'); ?>">
				<i class="fab fa-pinterest-p"></i>
			</a>
 
		<?php endif; ?>
	</div>
</div>