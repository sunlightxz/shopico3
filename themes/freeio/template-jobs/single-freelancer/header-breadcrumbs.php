<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$bg_image_url = freeio_get_config('freelancer_header_bg_image');
$style = '';
if ( $bg_image_url ) {
    $style = 'style="background-image: url(\''.esc_url($bg_image_url).'\')"';
}

?>
<div class="freelancer-detail-header">
    <div class="service-detail-breadcrumbs">
        <div class="container d-md-flex align-items-center">
            <div class="left-column">
                <?php freeio_render_breadcrumbs_simple(); ?>
            </div>
            <div class="right-column ms-auto">
                <?php get_template_part('template-parts/sharebox-listing'); ?>
                <?php echo freeio_freelancer_display_favorite_btn($post->ID); ?>
                <?php freeio_freelancer_display_report_icon($post); ?>
            </div>
        </div>
    </div>
</div>