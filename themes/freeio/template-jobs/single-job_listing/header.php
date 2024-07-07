<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$bg_image_url = freeio_get_config('job_header_bg_image');
$style = '';
if ( $bg_image_url ) {
    $style = 'style="background-image: url(\''.esc_url($bg_image_url).'\')"';
}
?>
<div class="job-detail-header">
    <div class="service-detail-breadcrumbs">
        <div class="container d-md-flex align-items-center">
            <div class="left-column">
                <?php freeio_render_breadcrumbs_simple(); ?>
            </div>
            <div class="right-column ms-auto">
                <?php get_template_part('template-parts/sharebox-listing'); ?>
                <?php echo freeio_job_display_favorite_btn($post->ID); ?>
                <?php freeio_job_display_report_icon($post); ?>
            </div>
        </div>
    </div>
    <div class="header-detail-job top-detail-job" <?php echo trim($style); ?>>
        <div class="container">
            <div class="max-930">
                <div class="d-md-flex align-items-end">
                    <div class="d-sm-flex align-items-center w-100">
                        <div class="job-thumbnail flex-shrink-0 position-relative">
                            <?php freeio_job_display_employer_logo($post, true, true); ?>
                        </div>
                        <div class="job-information flex-grow-1">
                            <div class="title-wrapper">
                                <?php the_title( '<h1 class="job-detail-title">', '</h1>' ); ?>
                                <?php freeio_job_display_featured_icon($post,'icon'); ?>
                                <?php freeio_job_display_filled_label($post); ?>
                            </div>
                            <?php freeio_job_display_employer_title($post); ?>

                            <div class="service-metas-detail d-flex align-items-start flex-wrap">
                                <?php freeio_job_display_salary($post); ?>
                                <?php freeio_job_display_job_category($post); ?>
                                <?php freeio_job_display_job_type($post); ?>
                            </div>
                        </div>
                    </div>
                    <div class="action ms-auto">
                        <?php WP_Freeio_Job_Listing::display_apply_job_btn($post->ID); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>