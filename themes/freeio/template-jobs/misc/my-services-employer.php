<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
freeio_load_select2();
?>
<div class="widget-service_orders box-dashboard-wrapper">
	<h3 class="widget-title"><?php echo esc_html__('My Services','freeio') ?></h3>
	<div class="inner-list">
		
		<?php
		$user_id = WP_Freeio_User::get_user_id();
		if ( !WP_Freeio_User::is_employer($user_id) ) {
			?>
			<div class="not-found"><?php esc_html_e('You have not permission to view this page.', 'freeio'); ?></div>
			<?php
		} else {
			?>
			<div class="search-orderby-wrapper d-sm-flex align-items-center">
				<div class="search-employer-form search-applicants-form widget_search">
					<form action="" method="get">
						<input type="text" placeholder="<?php esc_attr_e( 'Search ...', 'freeio' ); ?>" class="form-control" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
						<button class="search-submit btn btn-search" name="submit">
							<i class="flaticon-loupe"></i>
						</button>
						<input type="hidden" name="paged" value="1" />
					</form>
				</div>
				<div class="sort-employer-form sortby-form ms-auto">
					<?php
						$orderby_options = apply_filters( 'wp_freeio_my_jobs_orderby', array(
							'menu_order'	=> esc_html__( 'Default', 'freeio' ),
							'newest' 		=> esc_html__( 'Newest', 'freeio' ),
							'oldest'     	=> esc_html__( 'Oldest', 'freeio' ),
						) );

						$orderby = isset( $_GET['orderby'] ) ? wp_unslash( $_GET['orderby'] ) : 'newest'; 
					?>

					<div class="orderby-wrapper d-flex align-items-center">
						<span class="text-sort">
							<?php echo esc_html__('Sort by: ','freeio'); ?>
						</span>
						<form class="my-jobs-ordering" method="get">
							<select name="orderby" class="orderby">
								<?php foreach ( $orderby_options as $id => $name ) : ?>
									<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
								<?php endforeach; ?>
							</select>
							<input type="hidden" name="paged" value="1" />
							<?php WP_Freeio_Mixes::query_string_form_fields( null, array( 'orderby', 'submit', 'paged' ) ); ?>
						</form>
					</div>
				</div>
			</div>
			<?php
			$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
			$query_vars = array(
				'post_type'     => 'service_order',
				'post_status'   => apply_filters('wp-freeio-my-services-employer-post-statuses', array( 'publish', 'hired', 'completed', 'cancelled' )),
				'paged'         => $paged,
				'order' => 'DESC',
		        'orderby' => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
					'ID'         => 'DESC',
				),
				'author'        => $user_id,
			);
			if ( isset($_GET['search']) ) {
				$query_vars['s'] = $_GET['search'];
			}
			if ( isset($_GET['orderby']) ) {
				switch ($_GET['orderby']) {
					case 'menu_order':
						$query_vars['orderby'] = array(
							'menu_order' => 'ASC',
							'date'       => 'DESC',
							'ID'         => 'DESC',
						);
						break;
					case 'newest':
						$query_vars['orderby'] = 'date';
						$query_vars['order'] = 'DESC';
						break;
					case 'oldest':
						$query_vars['orderby'] = 'date';
						$query_vars['order'] = 'ASC';
						break;
				}
			}
			$service_orders = new WP_Query($query_vars);

			if ( !empty($service_orders) && !empty($service_orders->have_posts()) ) { ?>
				<div class="table-responsive">
					<table class="job-table">
						<thead>
							<tr>
								<th class="job-title-td"><?php esc_html_e('Title', 'freeio'); ?></th>
								<th class="job-applicants"><?php esc_html_e('Order Price', 'freeio'); ?></th>
								<th class="job-status"><?php esc_html_e('Status', 'freeio'); ?></th>
								<th class="job-actions"><?php esc_html_e('Actions', 'freeio'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php
						
						$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);

						$zoom_email = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_email');
						$zoom_client_id = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_client_id');
						$zoom_client_secret = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_client_secret');
						$access_token = WP_Freeio_Meeting_Zoom::user_zoom_access_token($user_id);

						while ( $service_orders->have_posts() ) : $service_orders->the_post(); global $post;
							$service_package_content = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_package_content', true);
							
							$status = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'status', true);
							$service_addons = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'addons', true);
							$service_id = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
							$service = get_post($service_id);
							if ( empty($service) ) {
								?>
								<tr class="my-item-wrapper">
									<td class="job-table-info">
										<div class="title-wrapper">
											<h3 class="job-table-info-content-title">
												<?php the_title(); ?>
											</h3>
										</div>
									</td>
									<td class="job-table-info" colspan="3">
										<?php esc_html_e('This service is not available', 'freeio'); ?>
									</td>
								</tr>
								<?php
								continue;
							}

							$freelancer_user_id = get_post_field('post_author', $service_id);
							$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
							$freelancer_obj = get_post($freelancer_id);
						?>
							<tr class="my-item-wrapper">
								<td class="job-table-info">
									
									<?php if ( $freelancer_obj ) { ?>
										<div class="title-wrapper d-flex align-items-center">
											<div class="flex-shrink-0">
												<?php freeio_freelancer_display_logo($freelancer_obj, true, '40px'); ?>
											</div>
											<h3 class="job-table-info-content-title">
												<a href="<?php echo esc_url(get_permalink($freelancer_obj)); ?>">
													<?php echo get_the_title($freelancer_id); ?>
												</a>
											</h3>
										</div>
									<?php } ?>

									<div class="job-service-title">
										<strong><a href="<?php echo esc_url(get_permalink($service_id)); ?>">
											<?php echo get_the_title($service_id); ?>
											<?php
											if ( $service_package_content ) {
												?>
												(<?php echo trim($service_package_content['name']); ?>)
												<?php
											}
											?>
										</a></strong>
									</div>
									<div class="listing-metas d-flex flex-wrap align-items-start">
										<?php freeio_service_display_short_location($service, 'icon'); ?>
										<div class="date">
											<i class="flaticon-30-days"></i><?php the_time(get_option('date_format')); ?>
										</div>
									</div>
								</td>
								<td class="job-table-cost">
									<div class="price-wrapper">
						                <?php
						                // $meta_obj = WP_Freeio_Service_Meta::get_instance($service_id);
										// if ( $meta_obj->check_post_meta_exist('price') ) {
											
										// 	$service_price = $meta_obj->get_post_meta( 'price' );

										// 	if ( !empty($service_addons) ) {
										// 		foreach ($service_addons as $addon_id) {
										// 			$addon_price = get_post_meta($addon_id, WP_FREEIO_SERVICE_ADDON_PREFIX.'price', true);
										// 			$service_price += $addon_price;
										// 		}
										// 	}
							            //     echo WP_Freeio_Price::format_price($service_price);
							            // }

							            $order_id = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'order_id', true);
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
								</td>
								<td class="job-table-status">
									<?php
									$post_status = get_post_status_object( $post->post_status );
									if ( $post->post_status == 'pending' ) {
					        			$classes = 'bg-pending';
					        		} elseif( $post->post_status == 'cancelled' ) {
					        			$classes = 'bg-cancelled';
					        		} else {
					        			$classes = 'bg-success';
					        		}
					        		?>
									<div class="badge <?php echo esc_attr($classes);?>">
										<?php
											if ( !empty($post_status->label) ) {
												echo esc_html($post_status->label);
											} else {
												echo esc_html($post_status->post_status);
											}
										?>
									</div>
								</td>
								<td class="job-table-action">
									<?php if ( !empty($zoom_email) && !empty($zoom_client_id) && !empty($zoom_client_secret) && !empty($access_token) ) { ?>
										<a href="#job-apply-create-meeting-form-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-create-meeting-job-applied btn btn-sm btn-theme"><?php echo esc_attr_e('Create Meeting', 'freeio'); ?></a>
	                    				<?php echo WP_Freeio_Template_Loader::get_template_part('misc/meeting-form'); ?>
	                    			<?php } ?>


									<?php
									$my_services_page_id = wp_freeio_get_option('my_bought_services_page_id');
									$my_services_url = get_permalink( $my_services_page_id );

									$my_services_url = add_query_arg( 'service_id', $service_id, remove_query_arg( 'service_id', $my_services_url ) );
									$my_services_url = add_query_arg( 'service_order_id', $post->ID, remove_query_arg( 'service_order_id', $my_services_url ) );
									$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_services_url ) );
									?>
									<a class="btn btn-sm btn-theme" href="<?php echo esc_url($view_history_url); ?>" title="<?php esc_attr_e('View history', 'freeio'); ?>">
										<?php esc_html_e('View history', 'freeio'); ?>
									</a>
								</td>
							</tr>
						<?php endwhile;
						wp_reset_postdata();
						?>
						</tbody>
					</table>
				</div>
				<?php
				WP_Freeio_Mixes::custom_pagination( array(
					'wp_query' => $service_orders,
					'max_num_pages' => $service_orders->max_num_pages,
					'prev_text'     => '<i class="ti-angle-left"></i>',
					'next_text'     => '<i class="ti-angle-right"></i>',
				));
			?>

			<?php } else { ?>
				<div class="not-found"><?php esc_html_e('No service order found.', 'freeio'); ?></div>
			<?php } ?>
		<?php } ?>
	</div>
</div>