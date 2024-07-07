<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

$gallery = array();
if ( $meta_obj->check_post_meta_exist('gallery') && ($gallery = $meta_obj->get_post_meta( 'gallery' )) ) {
    $gallery = $meta_obj->get_post_meta( 'gallery' );
}
?>

<?php
if ( $gallery && is_array($gallery) ) {
    $gallery_size = !empty($gallery_size) ? $gallery_size : 'freeio-gallery';
    $gallery_second_size = !empty($gallery_second_size) ? $gallery_second_size : 'thumbnail';
?>
    <div class="freelancer-detail-gallery gallery-listing">
        <div class="slick-carousel slick-carousel-gallery-main" <?php echo trim(empty($elementor) ? 'data-carousel="slick"' : ''); ?> data-items="1" data-large="1" data-medium="1" data-small="1" data-smallest="1" data-pagination="false" data-nav="true" data-autoplay="false" data-slickparent="true">
            
            <?php
            if ( $gallery && is_array($gallery) ) {
                foreach ( $gallery as $id => $src ) { ?>
                    <div class="item">
                        <a href="<?php echo esc_url( $src ); ?>" data-elementor-lightbox-slideshow="freeio-gallery" class="p-popup-image">
                            <?php echo freeio_get_attachment_thumbnail( $id, $gallery_size );?>
                        </a>
                    </div>
                <?php }
            } ?>
        </div>
        
        <div class="slick-carousel slick-carousel-gallery-thumbs d-none d-md-block" <?php echo trim(empty($elementor) ? 'data-carousel="slick"' : ''); ?> data-items="6" data-large="5" data-medium="4" data-small="3" data-smallest="2" data-pagination="false" data-nav="false" data-autoplay="false" data-asnavfor=".slick-carousel-gallery-main" data-slidestoscroll="1" data-focusonselect="true">
            
            <?php
            if ( $gallery && is_array($gallery) ) {
                foreach ( $gallery as $id => $src ) { ?>
                    <div class="item">
                       <?php echo freeio_get_attachment_thumbnail( $id, $gallery_second_size ); ?>
                    </div>
                <?php }
            } ?>
        </div>
    </div>
<?php } ?>