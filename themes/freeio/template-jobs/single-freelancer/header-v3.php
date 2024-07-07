<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

?>
<div class="freelancer-detail-header">
    <div class="header-detail-freelancer v3">
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
    </div>
</div>