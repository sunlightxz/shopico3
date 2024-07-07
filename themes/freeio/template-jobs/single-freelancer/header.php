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
    <div class="header-detail-freelancer" <?php echo trim($style); ?>>
        <div class="container">
            
            <div class="d-sm-flex align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <?php if ( has_post_thumbnail() ) { ?>
                        <div class="freelancer-thumbnail flex-shrink-0 position-relative">
                            <?php freeio_freelancer_display_logo($post, false); ?>
                        </div>
                    <?php } ?>
                    <div class="freelancer-information flex-grow-1">
                        <?php
                        $title = freeio_freelancer_name($post);
                        ?>
                        <div class="d-flex freelancer-detail-title-wrapper">
                            <h1 class="freelancer-detail-title">
                                <?php echo trim($title) ?>
                            </h1>
                            <span class="flex-shrink-0"><?php freeio_freelancer_display_featured_icon($post,'icon'); ?></span>
                        </div>
                        <?php freeio_freelancer_display_job_title($post); ?>
                        <div class="service-metas-detail d-flex flex-wrap align-items-center">
                            <?php freeio_freelancer_display_rating_reviews($post); ?>
                            <?php freeio_freelancer_display_short_location($post, 'icon'); ?>
                            
                            <?php freeio_freelancer_display_birthday($post, 'icon'); ?>
                        </div>
                    </div>
                </div>
                <div class="ms-auto right-action">
                    <div class="d-flex align-items-center">
                        <?php freeio_freelancer_show_invite($post->ID); ?>
                        <?php
                            if ( freeio_is_wp_private_message() ) {
                                $user_id = WP_Freeio_User::get_user_by_freelancer_id($post->ID);
                                freeio_private_message_form($post, $user_id);
                            }
                        ?>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>