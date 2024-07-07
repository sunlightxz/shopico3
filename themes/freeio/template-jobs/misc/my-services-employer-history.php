<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="widget-service-orders box-dashboard-wrapper">
	<h3 class="widget-title"><?php echo esc_html__('Service History','freeio') ?></h3>
	<div class="inner-list">
		
		<?php
		$service_id = isset($_GET['service_id']) ? $_GET['service_id'] : '';
		$service_order_id = isset($_GET['service_order_id']) ? $_GET['service_order_id'] : '';
		$user_id = WP_Freeio_User::get_user_id();
		$employer_user_id = get_post_field('post_author', $service_order_id);
		$service_order_service_id = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
		$addons = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'addons', true);

		if ( $user_id != $employer_user_id || $service_id != $service_order_service_id ) {
			?>
			<div class="not-found"><?php esc_html_e('You have not permission to view this page.', 'freeio'); ?></div>
			<?php
		} else {
			?>
			<div class="service-details-history">
				<h2 class="inner-title"><?php esc_html_e('Service Details', 'freeio'); ?></h2>
				<div class="inner-content">
					<?php
					$service = get_post($service_id);
					if ( $service ) {
					?>
						<div class="service-content">
							<div class="title-wrapper">
								<h3 class="service-tittle">
									<a href="<?php echo esc_url(get_permalink($service)); ?>"><?php echo get_the_title($service); ?></a>
								</h3>
								<?php freeio_service_display_featured_icon($service); ?>
							</div>
							<div class="listing-metas d-flex align-items-start flex-wrap">
								<?php freeio_service_display_category($service, 'icon'); ?>
								<?php freeio_service_display_short_location($service, 'icon'); ?>
								<?php freeio_service_display_postdate($service, 'icon'); ?>
							</div>
							
						</div>
						<div class="row">
							<?php
							$second_column = 12;
							$service_package_content = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_package_content', true);
							if ( $service_package_content ) {
								$second_column = 6;

				            	$package_price = isset($service_package_content['price']) ? $service_package_content['price'] : 0;
		                        $name = isset($service_package_content['name']) ? $service_package_content['name'] : '';
		                        $description = isset($service_package_content['description']) ? $service_package_content['description'] : '';
		                        $delivery_time = isset($service_package_content['delivery_time']) ? $service_package_content['delivery_time'] : '';
		                        $revisions = isset($service_package_content['revisions']) ? $service_package_content['revisions'] : '';
		                        $features = isset($service_package_content['features']) ? $service_package_content['features'] : '';
			            	?>
				            	<div class="service-package-content col-12 col-lg-6">
				            		<h5><?php esc_html_e('Package', 'freeio'); ?></h5>
				            		<div class="package-inner content-serive-package-tabs">
				            			<div class="service-price-inner-wrapper">
				            				<div class="name"><?php echo trim($name); ?></div>
		                                    <div class="price-inner"><?php echo WP_Freeio_Price::format_price( $package_price ); ?></div>
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
				            		</div>
				            	</div>
				            <?php } ?>

							<?php if ( $addons ) { ?>
								<div class="service-status col-12 col-lg-<?php echo esc_attr($second_column); ?>">
									
									<h5><?php esc_html_e('Addons', 'freeio'); ?></h5>
									<ul class="addons-list">
										<?php foreach ($addons as $addon) {
											if ( !empty($addon['id']) ) {
											$addon_post = get_post($addon['id']);
												if ( $addon_post ) {
											?>
													<li>
														
							                            <div class="addon-item">
							                                
						                                    <div class="content">
						                                        <h5 class="title"><?php echo trim($addon_post->post_title); ?></h5>
						                                        <div class="inner">
						                                            <?php echo trim($addon_post->post_content); ?>
						                                        </div>
						                                        <div class="price">
						                                            <?php
						                                                $price = !empty($addon['price']) ? $addon['price'] : '';
						                                                echo WP_Freeio_Price::format_price($price, true);
						                                            ?>
						                                        </div>
						                                    </div>
							                            </div>
								                        
													</li>
												<?php
												}
											}
										} ?>
									</ul>
									
								</div>
							<?php } ?>
						</div>
					<?php } else { ?>
						<div class="alert alert-danger" role="alert">
							<?php esc_html_e('This service is not available', 'freeio'); ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="freelancer-history-service mt-4 mb-4">
				<div class="freelancer-item">
					<div class="d-sm-flex">
						<?php
							$freelancer_user_id = get_post_field('post_author', $service_id);
							$freelancer_post_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
							$freelancer = get_post($freelancer_post_id);

							$status = get_post_status($service_order_id);
						?>
						<div class="flex-shrink-0">
	        				<?php freeio_freelancer_display_logo($freelancer); ?>
						</div>
						<div class="information-right d-lg-flex">
							<div class="inner-middle">

								<div class="d-flex freelancer-title-wrapper">
									<h3 class="freelancer-title">
										<a href="<?php echo esc_url(get_permalink($freelancer)); ?>"><?php echo get_the_title($freelancer); ?></a>
									</h3>
									<span class="flex-shrink-0">
										<?php freeio_freelancer_display_featured_icon($freelancer); ?>
									</span>
								</div>

								<div class="listing-metas d-flex align-items-start flex-wrap">
									<?php freeio_freelancer_display_job_title($freelancer); ?>
									<?php freeio_freelancer_display_short_location($freelancer, 'icon'); ?>
		                    		<?php freeio_freelancer_display_rating_reviews($freelancer); ?>
								</div>
								<div class="price-wrapper">
									<?php
					                // $meta_obj = WP_Freeio_Service_Meta::get_instance($service_id);
									// if ( $meta_obj->check_post_meta_exist('price') ) {
										
									// 	$service_price = $meta_obj->get_post_meta( 'price' );

									// 	if ( !empty($addons) ) {
									// 		foreach ($addons as $addon) {
									// 			$addon_price = !empty($addon['price']) ? $addon['price'] : 0;
									// 			$service_price += $addon_price;
									// 		}
									// 	}
						            //     echo WP_Freeio_Price::format_price($service_price);
						            // }

						            $order_id = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'order_id', true);
						            $order 		= new WC_Order( $order_id );
									$items 		= $order->get_items();
									$amount = 0;
									
									if( !empty( $items ) ) {
										$counter	= 0;
										foreach( $items as $key => $order_item ){
											$order_item_id = $order_item->get_id();
											$order_detail = wc_get_order_item_meta( $order_item_id, 'cus_woo_product_data', true );
											$amount = $order_detail['price'];
											
										}
									}
									echo WP_Freeio_Price::format_price($amount);
					                ?>
					            </div>
							</div>
							<div class="inner-right">
								<?php
								$statuses = WP_Freeio::post_statuses();
								if ( $status == 'hired' ) {
									?>
									<select id="service_order_status" class="service_order_status" name="service_order_status">
										<?php foreach ($statuses as $key => $title) { ?>
											<option value="<?php echo esc_attr($key); ?>" <?php selected($key, $status); ?>><?php echo trim($title); ?></option>
										<?php } ?>
									</select>
									<button type="button" class="btn btn-theme-rgba10 w-100 radius-sm update-service-order-status" data-service_order_id="<?php echo esc_attr($service_order_id); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-update-service-order-status-nonce' )); ?>"><?php esc_html_e('Update', 'freeio'); ?><i class="next flaticon-right-up"></i></button>
								<?php } else {
									$classes = 'bg-primary';
									if ( $status == 'cancelled' ) {
										$classes = 'bg-cancelled';
									}
								?>
									<span class="badge <?php echo esc_attr($classes); ?>">
										<?php if ( !empty($statuses[$status]) ) {
											echo trim($statuses[$status]);
										} else {
											echo trim($status); 
										}
										?>
									</span>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="messages" class="messages">
				<div id="messages-list" class="messages-list">
					<?php echo WP_Freeio_Service::list_service_order_messages($service_order_id); ?>
				</div>
				<div class="service-order-message-form-wrapper">
					<form id="service-order-message-form-<?php echo esc_attr($service_order_id); ?>" method="post" action="?" class="service-order-message-form form-theme" action="" enctype="multipart/form-data">
			            <div class="form-group">
			                <textarea class="form-control" name="message" placeholder="<?php esc_attr_e( 'Message', 'freeio' ); ?>" required="required"></textarea>
			            </div><!-- /.form-group -->

			            <?php
						$file_types = wp_freeio_get_option('image_file_types');
						$file_types = !empty($file_types) ? $file_types : array();
						$cv_types = wp_freeio_get_option('cv_file_types');
						$file_types = !empty($cv_types) ? array_merge($file_types, $cv_types) : $file_types;
						
						$file_types_str = !empty($file_types) ? implode(', ', $file_types) : '';
						?>
						<div class="col-xs-12">
					     	<div class="form-group upload-file-btn-wrapper">
					            <input type="file" name="attachments[]" data-file_types="<?php echo esc_attr(!empty($file_types) ? implode('|', $file_types) : ''); ?>" multiple="multiple">

					            <div class="label-can-drag">
									<div class="form-group group-upload">
								        <div class="upload-file-btn" data-text="<?php echo esc_attr(sprintf(esc_html__('Upload File (%s)', 'freeio'), $file_types_str)); ?>">
							            	<span class="text"><?php echo sprintf(esc_html__('Upload File (%s)', 'freeio'), $file_types_str); ?></span>
								        </div>
								    </div>
								</div>
					        </div>

				        </div><!-- /.form-group -->

			            <input type="hidden" name="service_id" value="<?php echo esc_attr($service_id); ?>">
			            <input type="hidden" name="service_order_id" value="<?php echo esc_attr($service_order_id); ?>">
			            <button class="button btn btn-theme btn-outline" name="contact-form"><?php echo esc_html__( 'Send Message', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
			        </form>
				</div>
			</div>
			<?php
			
		} ?>
	</div>
</div>