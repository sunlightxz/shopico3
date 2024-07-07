<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

?>

<?php do_action( 'wp_freeio_before_service_content', $post->ID ); ?>

<article <?php post_class('map-item service-item service-favorite-wrapper'); ?> <?php freeio_service_item_map_meta($post); ?>>
    <div class="position-relative">
        <?php freeio_service_display_featured_icon($post, 'text'); ?>
        <?php freeio_service_display_image($post); ?>
        
        <a href="javascript:void(0);" class="btn-remove-service-favorite" data-service_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-remove-service-favorite-nonce' )); ?>"><i class="flaticon-delete"></i></a>

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