<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

?>

<?php do_action( 'wp_freeio_before_service_content', $post->ID ); ?>

<article <?php post_class('map-item service-item v2'); ?> <?php freeio_service_item_map_meta($post); ?>>
    <div class="position-relative">
        <?php freeio_service_display_featured_icon($post, 'text'); ?>
        <?php freeio_service_display_image($post); ?>
        <?php echo freeio_service_display_favorite_btn($post->ID); ?>
    </div>

    <div class="service-information">
        <?php freeio_service_display_first_category($post); ?>
        
        <?php the_title( sprintf( '<h2 class="service-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
        <?php freeio_service_display_rating_reviews($post); ?>
        <div class="info-bottom d-flex align-items-center">
            <?php freeio_service_display_author($post); ?>
            <div class="ms-auto">
                <?php freeio_service_display_price($post, 'text'); ?>
            </div>
        </div>
	</div>
</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_service_content', $post->ID ); ?>