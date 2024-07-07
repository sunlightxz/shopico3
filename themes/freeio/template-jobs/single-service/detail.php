<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
?>
<div class="service-detail-detail">
	<?php
    $meta_obj = WP_Freeio_Service_Meta::get_instance($post->ID);
    ?>
    <ul class="list-service-detail d-flex align-items-center flex-wrap">
        <?php if ( $meta_obj->check_post_meta_exist('delivery_time') && ($delivery_time = $meta_obj->get_post_meta('delivery_time')) ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-calendar"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('delivery_time')); ?></div>
                    <div class="value">
                        <?php
                            if ( is_array($delivery_time) ) {
                                echo implode(', ', $delivery_time);
                            } else {
                                echo esc_html($delivery_time);
                            }
                        ?>
                    </div>
                </div>
            </li>
        <?php } ?>
        <?php if ( $meta_obj->check_post_meta_exist('english_level') && ($english_level = $meta_obj->get_post_meta('english_level')) ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-goal"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('english_level')); ?></div>
                    <div class="value">
                        <?php
                            if ( is_array($english_level) ) {
                                echo implode(', ', $english_level);
                            } else {
                                echo esc_html($english_level);
                            }
                        ?>
                    </div>
                </div>
            </li>
        <?php } ?>

        <?php
        $location = freeio_service_display_short_location($post, '', false);
        if ( $location ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-tracking"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('location')); ?></div>
                    <div class="value">
                        <?php echo trim($location); ?>
                    </div>
                </div>
            </li>
        <?php } ?>

        <?php do_action('wp-freeio-single-service-details', $post); ?>
    </ul>
</div>