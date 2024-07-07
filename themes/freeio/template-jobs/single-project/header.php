<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$bg_image_url = freeio_get_config('project_header_bg_image');
$style = '';
if ( $bg_image_url ) {
    $style = 'style="background-image: url(\''.esc_url($bg_image_url).'\')"';
}

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

    <div class="header-detail-project" <?php echo trim($style); ?>>
        <div class="container">
            <div class="title-wrapper d-flex">
                <?php the_title( '<h1 class="project-detail-title">', '</h1>' ); ?>
                <span class="flex-shrink-0"><?php freeio_project_display_featured_icon($post); ?></span>
            </div>
            <div class="service-metas-detail d-flex flex-wrap align-items-center">
                <?php freeio_project_display_short_location($post, 'icon'); ?>
                <?php freeio_project_display_postdate($post, 'icon', 'date'); ?>
                <?php freeio_project_display_views($post); ?>
            </div>
        </div>
    </div>
</div>