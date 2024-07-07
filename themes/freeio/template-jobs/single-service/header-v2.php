<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;


?>



<div class="service-detail-header">
    
    <div class="header-detail-service v2">
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