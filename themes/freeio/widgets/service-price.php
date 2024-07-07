<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $post, $preview_post;
if ( $preview_post ) {
    $post = $preview_post;
}
if ( empty($post->post_type) || $post->post_type !== 'service' ) {
    return;
}

$meta_obj = WP_Freeio_Service_Meta::get_instance($post->ID);

if ( $meta_obj->check_post_meta_exist('price_type') && $meta_obj->get_post_meta('price_type') === 'package' ) {
    
    if ( !$meta_obj->check_post_meta_exist('price_packages') ) {
        return false;
    }
    $price_packages = $meta_obj->get_post_meta( 'price_packages' );
    if ( $price_packages && is_array($price_packages) ) {
        $price = $price_packages[0]['price'];
        foreach ($price_packages as $package) {
            $t_price = $package['price'];
            if ( $t_price == '0' ) {
                $price = 0;
            } elseif ( empty( $t_price ) || ! is_numeric( $t_price ) ) {
                break;
            } else {
                $price = $package['price'] < $price ? $package['price'] : $price;
            }
        }
    } else {
        return false;
    }

} else {
    if ( !$meta_obj->check_post_meta_exist('price') ) {
        return false;
    }
    $price = $meta_obj->get_post_meta( 'price' );

    if ( empty( $price ) || ! is_numeric( $price ) ) {
        return;
    }
}


extract( $args );

extract( $args );
extract( $instance );


echo trim($before_widget);
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . $after_title;
}

$tab_rand = freeio_random_key();

?>
    <div class="service-price">
        
        <?php
            if ( $meta_obj->check_post_meta_exist('price_type') && $meta_obj->get_post_meta('price_type') === 'package' ) {
                $price_packages = $meta_obj->get_post_meta( 'price_packages' );
                if ( $price_packages && is_array($price_packages) ) {
                    ?>
                    <ul role="tablist" class="nav serive-package-tabs flex-nowrap position-relative w-100">
                        <?php $index = 0; foreach ($price_packages as $key => $package) {
                            $package_price = isset($package['price']) ? $package['price'] : '';
                            if ( !empty( $package_price ) && is_numeric( $package_price ) ) {
                        ?>
                                <li>
                                    <a href="#tab-<?php echo esc_attr($tab_rand);?>-<?php echo esc_attr($key); ?>" class="<?php echo esc_attr($index == 0 ? 'active' : '');?>" data-bs-toggle="tab">
                                        <?php echo trim($package['name']); ?>
                                    </a>
                                </li>
                            <?php $index++; } ?>
                        <?php } ?>
                    </ul>
                    <div class="tab-content content-serive-package-tabs">
                        <?php $index = 0; foreach ($price_packages as $key => $package) {
                            $form_rand = freeio_random_key();

                            $package_price = isset($package['price']) ? $package['price'] : '';
                            if ( !empty( $package_price ) && is_numeric( $package_price ) ) {

                                $name = isset($package['name']) ? $package['name'] : '';
                                $description = isset($package['description']) ? $package['description'] : '';
                                $delivery_time = isset($package['delivery_time']) ? $package['delivery_time'] : '';
                                $revisions = isset($package['revisions']) ? $package['revisions'] : '';
                                $features = isset($package['features']) ? $package['features'] : '';
                            ?>
                                <div id="tab-<?php echo esc_attr($tab_rand);?>-<?php echo esc_attr($key); ?>" class="tab-pane <?php echo esc_attr($index == 0 ? 'active' : ''); ?>">
                                    <div class="service-tab-inner">
                                        <form id="service-add-to-cart-<?php echo esc_attr($post->ID.'_'.$form_rand); ?>" class="service-add-to-cart" method="post">
                                            <input type="hidden" name="service_package" value="<?php echo esc_attr($key); ?>">
                                            <div class="service-price-inner-wrapper">
                                                <div class="service-price-inner"><?php echo WP_Freeio_Price::format_price( $package_price ); ?></div>
                                                <div class="description"><?php echo trim($description); ?></div>
                                                <div class="features">
                                                    <?php if ($delivery_time) { ?>
                                                        <div class="item">
                                                            <i class="flaticon-sand-clock"></i>
                                                            <span><?php echo trim($delivery_time); ?> <?php esc_html_e('Delivery', 'freeio'); ?></span>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if ($revisions) { ?>
                                                        <div class="item">
                                                            <i class="flaticon-recycle"></i>
                                                            <span><?php echo trim($revisions); ?> <?php esc_html_e('Revisions', 'freeio'); ?></span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <?php if ( $features ) {
                                                    $options = explode("\n", str_replace("\r", "", stripslashes($features)));
                                                ?>
                                                    <ul class="more-features list-border-check">
                                                        <?php
                                                        foreach ($options as $val) {
                                                            ?>
                                                            <li><?php echo trim($val); ?></li>
                                                            <?php
                                                        }
                                                        ?>
                                                    </ul>
                                                <?php } ?>
                                            </div>
                                            <?php if ( $meta_obj->check_post_meta_exist('addons') && ($addons = $meta_obj->get_post_meta( 'addons' )) ) { ?>
                                                <div class="service-price-addons">
                                                    <?php foreach ($addons as $addon_id) {
                                                        $addon_post = get_post($addon_id);
                                                        if ( $addon_post ) {
                                                    ?>
                                                            <div class="addon-item">
                                                                <label for="addon-item-<?php echo esc_attr($addon_id.'_'.$form_rand);?>">
                                                                    <input id="addon-item-<?php echo esc_attr($addon_id.'_'.$form_rand);?>" type="checkbox" name="service_addons[]" value="<?php echo esc_attr($addon_post->ID); ?>">

                                                                    <div class="content">
                                                                        <h5 class="title"><?php echo trim($addon_post->post_title); ?></h5>
                                                                        <div class="inner">
                                                                            <?php echo trim($addon_post->post_content); ?>
                                                                        </div>
                                                                        <div class="price">
                                                                            <?php
                                                                                $price = get_post_meta($addon_post->ID, WP_FREEIO_SERVICE_ADDON_PREFIX . 'price', true);
                                                                                echo WP_Freeio_Price::format_price($price, true);
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>

                                            <input type="hidden" name="service_id" value="<?php echo esc_attr($post->ID); ?>">
                                            <button type="submit" class="btn btn-theme btn-inverse w-100"><?php esc_html_e('Buy Now', 'freeio'); ?> 
                                                <span><?php echo WP_Freeio_Price::format_price_without_html( $package_price, true ); ?></span> <i class="flaticon-right-up next"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php $index++; } ?>
                        <?php } ?>
                    </div>
                    <?php
                }
            } else {
                $rand = freeio_random_key();
                ?>
                <form id="service-add-to-cart-<?php echo esc_attr($post->ID.'_'.$rand); ?>" class="service-add-to-cart" method="post">
                    <div class="service-price-inner">
                        <?php echo WP_Freeio_Service::get_price_html($post->ID); ?>
                    </div>
                    <?php if ( $meta_obj->check_post_meta_exist('addons') && ($addons = $meta_obj->get_post_meta( 'addons' )) ) { ?>
                        <div class="service-price-addons">
                            <?php foreach ($addons as $addon_id) {
                                $addon_post = get_post($addon_id);
                                if ( $addon_post ) {
                            ?>
                                    <div class="addon-item">
                                        <label for="addon-item-<?php echo esc_attr($addon_id.'_'.$rand);?>">
                                            <input id="addon-item-<?php echo esc_attr($addon_id.'_'.$rand);?>" type="checkbox" name="service_addons[]" value="<?php echo esc_attr($addon_post->ID); ?>">

                                            <div class="content">
                                                <h5 class="title"><?php echo trim($addon_post->post_title); ?></h5>
                                                <div class="inner">
                                                    <?php echo trim($addon_post->post_content); ?>
                                                </div>
                                                <div class="price">
                                                    <?php
                                                        $price = get_post_meta($addon_post->ID, WP_FREEIO_SERVICE_ADDON_PREFIX . 'price', true);
                                                        echo WP_Freeio_Price::format_price($price, true);
                                                    ?>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <input type="hidden" name="service_id" value="<?php echo esc_attr($post->ID); ?>">
                    <button type="submit" class="btn btn-theme btn-inverse w-100"><?php esc_html_e('Buy Now', 'freeio'); ?> <span><?php echo WP_Freeio_Service::get_price_html($post->ID, false); ?></span> <i class="flaticon-right-up next"></i></button>
                </form>
                <?php
            }
        ?>
            
            
    </div>
<?php

echo trim($after_widget);