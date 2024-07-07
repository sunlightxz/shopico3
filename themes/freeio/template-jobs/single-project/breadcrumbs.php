<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

?>



<div class="project-detail-header">
    <div class="service-detail-breadcrumbs">
        <div class="container d-md-flex align-items-center">
            <div class="left-column">
                <?php freeio_render_breadcrumbs_simple(); ?>
            </div>
            <div class="right-column ms-auto">
                <?php get_template_part('template-parts/sharebox-listing'); ?>
                <?php echo freeio_project_display_favorite_btn($post->ID); ?>
                <?php freeio_project_display_report_icon($post); ?>
            </div>
        </div>
    </div>
</div>