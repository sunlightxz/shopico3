<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( $packages ) : ?>
	<div class="widget widget-packages widget-subwoo m-0">
		<h2 class="widget-title"><?php esc_html_e( 'Buy any cv package to get started', 'wp-freeio-wc-paid-listings' ); ?></h2>
		<div class="row">
			<?php foreach ( $packages as $key => $package ) :
				$post_object = get_post($package);
				setup_postdata( $GLOBALS['post'] =& $post_object );
				$product = wc_get_product( $package );
				if ( ! $product->is_type( array( 'cv_package', 'cv_package_subscription' ) ) || ! $product->is_purchasable() ) {
					continue;
				}
				?>

				<div class="col-xl-3 col-sm-6 col-12">
					<div class="subwoo-inner text-center <?php echo ($product->is_featured())?'highlight':''; ?>">
						<div class="item">
							<div class="header-sub">
								<div class="price">
									<?php echo (!empty($product->get_price())) ? $product->get_price_html() : esc_html__('Free', 'wp-freeio-wc-paid-listings'); ?>
								</div>
								<h3 class="title"><?php echo trim($product->get_title()); ?></h3>
								<?php 
									$product;
								?>
								<?php if ( has_excerpt($product->get_id()) ) { ?>
	                                <div class="short-des">
	                                	<?php echo apply_filters( 'the_excerpt', get_post_field('post_excerpt', $product->get_id()) ) ?>
	                                </div>
	                            <?php } ?>
							</div>
							<div class="bottom-sub">
                                <div class="content">
                                    <?php echo apply_filters( 'the_content', get_post_field('post_content', $product->get_id()) ) ?>
                                </div>
                                <div class="button-action">
                                	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
                                </div>
                            </div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>