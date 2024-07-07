<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

?>

<div class="project-detail-header">
    <div class="header-detail-project v3">
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