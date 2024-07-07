<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="project-rss-btn margin-left-15">
	<a class="rss-feed-url" href="<?php echo esc_url(WP_Freeio_Project::project_feed_url(null, array('submit', 'paged'), '', '', true )); ?>" target="_blank">
		<i class="fas fa-rss"></i>
		<?php esc_html_e('RSS Feed', 'wp-freeio'); ?>
	</a>
</div>