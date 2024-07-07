<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
?>
<?php if ( freeio_get_config('show_freelancer_description', true) && $post->post_content ) { ?>
	<div id="job-freelancer-description" class="job-detail-description box-detail">
		<h3 class="title"><?php esc_html_e('About Freelancer', 'freeio'); ?></h3>
		<div class="inner">
			<?php the_content(); ?>

			<?php do_action('wp-freeio-single-freelancer-description', $post); ?>
		</div>
	</div>
<?php } ?>