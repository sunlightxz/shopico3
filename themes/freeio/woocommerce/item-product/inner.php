<?php 
global $product;
$product_id = $product->get_id();
?>
<div class="product-block grid" data-product-id="<?php echo esc_attr($product_id); ?>">
    <div class="grid-inner">
        <div class="block-inner position-relative text-center">
            <figure class="image">
                <a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>" class="product-image">
                    <?php
                        /**
                        * woocommerce_before_shop_loop_item_title hook
                        *
                        * @hooked woocommerce_show_product_loop_sale_flash - 10
                        * @hooked woocommerce_template_loop_product_thumbnail - 10
                        */
                        remove_action('woocommerce_before_shop_loop_item_title','woocommerce_show_product_loop_sale_flash', 10);
                        do_action( 'woocommerce_before_shop_loop_item_title' );
                    ?>
                </a>
                <?php do_action('freeio_woocommerce_before_shop_loop_item'); ?>
            </figure>
        </div>
        <div class="metas">
            <div class="top-metas text-center">
                <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<div class="categories">', '</div>' ); ?>
                <h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <?php
                    /**
                    * woocommerce_after_shop_loop_item_title hook
                    *
                    * @hooked woocommerce_template_loop_rating - 5
                    * @hooked woocommerce_template_loop_price - 10
                    */
                    remove_action('woocommerce_after_shop_loop_item_title','woocommerce_template_loop_rating', 5);
                    do_action( 'woocommerce_after_shop_loop_item_title');
                ?>
            </div>
            <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
        </div>
    </div>
</div>