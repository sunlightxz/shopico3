<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
if ( $post->post_content ) {
?>
	<div class="project-detail-description">
		<h3 class="title"><?php esc_html_e('Project Description', 'freeio'); ?></h3>
		<?php the_content(); ?>

		<?php do_action('wp-freeio-single-project-description', $post); ?>
	</div>
<?php }