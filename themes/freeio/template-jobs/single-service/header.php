<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$bg_image_url = freeio_get_config('service_header_bg_image');
$style = '';
if ( $bg_image_url ) {
    $style = 'style="background-image: url(\''.esc_url($bg_image_url).'\')"';
}
?>



<div class="service-detail-header v1">
    <div class="service-detail-breadcrumbs">
        <div class="container d-md-flex align-items-center">
            <div class="left-column">
                <?php freeio_render_breadcrumbs_simple(); ?>
            </div>
            <div class="right-column ms-auto">
                <?php get_template_part('template-parts/sharebox-listing'); ?>
                <?php echo freeio_service_display_favorite_btn($post->ID); ?>
                <?php freeio_service_display_report_icon($post); ?>
            </div>
        </div>
    </div>

    <div class="header-detail-service" <?php echo trim($style); ?>>
        <div class="container">
            
            <div class="info-detail-service">
                <div class="title-wrapper d-flex">
                    <?php the_title( '<h1 class="service-detail-title">', '</h1>' ); ?>
                    <span class="flex-shrink-0"><?php freeio_service_display_featured_icon($post); ?></span>
                </div>
                <div class="service-metas-detail d-flex align-items-center flex-wrap">
                    <?php freeio_service_display_author($post); ?>
                    <?php freeio_service_display_rating_reviews($post); ?>
                    <?php freeio_service_display_views($post); ?>
                </div>
            </div>

        </div>
    </div>
</div>