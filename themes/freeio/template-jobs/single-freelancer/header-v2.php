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
<div class="freelancer-detail-header mt-0">
    <div class="header-detail-freelancer v2" <?php echo trim($style); ?>>
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
                    <div class="d-none d-sm-block"><?php freeio_freelancer_display_rating_reviews($post); ?></div>
                    <?php freeio_freelancer_display_short_location($post, 'icon'); ?>
                    <?php freeio_freelancer_display_birthday($post, 'icon'); ?>
                </div>
            </div>
        </div>
    </div>
</div>