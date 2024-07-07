<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

?>
<div class="job-detail-map-location">
	<h4 class="title"><?php esc_html_e('Location', 'freeio'); ?></h4>
    <div id="jobs-google-maps" class="single-job-map" <?php freeio_job_item_map_meta($post); ?>></div>
</div>