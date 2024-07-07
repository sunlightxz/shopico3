<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$bg_image_url = freeio_get_config('employer_header_bg_image');
$style = '';
if ( $bg_image_url ) {
    $style = 'style="background-image: url(\''.esc_url($bg_image_url).'\')"';
}
?>


<div class="employer-detail-header">
    <div class="service-detail-breadcrumbs">
        <div class="container d-md-flex align-items-center">
            <div class="left-column">
                <?php freeio_render_breadcrumbs_simple(); ?>
            </div>
            <div class="right-column ms-auto">
                <?php get_template_part('template-parts/sharebox-listing'); ?>
                <?php echo freeio_employer_display_favorite_btn($post->ID); ?>
                <?php freeio_employer_display_report_icon($post); ?>
            </div>
        </div>
    </div>
    <div class="header-detail-employer" <?php echo trim($style); ?>>
        <div class="container">
            <div class="d-sm-flex align-items-center">
                <div class="d-flex align-items-center flex-grow-1">
                    <?php if ( has_post_thumbnail() ) { ?>
                        <div class="employer-thumbnail flex-shrink-0">
                            <?php freeio_employer_display_logo($post, false); ?>
                        </div>
                    <?php } ?>
                    <div class="employer-information flex-grow-1">
                        <?php
                        $title = freeio_employer_name($post);
                        ?>
                        <div class="d-flex employer-detail-title-wrapper">
                            <h1 class="employer-detail-title">
                                <?php echo trim($title) ?>
                            </h1>
                            <span class="flex-shrink-0"><?php freeio_employer_display_featured_icon($post,'icon'); ?></span>
                        </div>
                        <?php freeio_employer_display_tagline($post); ?>
                        <div class="service-metas-detail d-flex flex-wrap align-items-center">
                            <?php freeio_employer_display_rating_reviews($post); ?>
                            <?php freeio_employer_display_short_location($post, 'icon'); ?>
                            <?php freeio_employer_display_email($post, 'icon'); ?>
                        </div>
                    </div>
                </div>
                <div class="ms-auto right-action">
                    <div class="d-flex align-items-center">
                        <?php
                            if ( freeio_is_wp_private_message() ) {
                                $user_id = WP_Freeio_User::get_user_by_employer_id($post->ID);
                                freeio_private_message_form($post, $user_id);
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>