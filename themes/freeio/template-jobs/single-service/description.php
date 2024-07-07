<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
if ( $post->post_content ) {
?>
	<div class="service-detail-description">
		<h3 class="title"><?php esc_html_e('Service Description', 'freeio'); ?></h3>
		<?php the_content(); ?>

		<?php do_action('wp-freeio-single-service-description', $post); ?>
	</div>
<?php }