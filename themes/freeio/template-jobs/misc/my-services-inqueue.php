<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
freeio_load_select2();
?>
<div class="widget-service_orders box-dashboard-wrapper">
	<h3 class="widget-title"><?php echo esc_html__('My Services In Queue','freeio') ?></h3>
	<div class="inner-list">
		
		<?php
		$service_id = isset($_GET['service_id']) ? $_GET['service_id'] : '';

		$user_id = WP_Freeio_User::get_user_id();
		$freelancer_user_id = get_post_field('post_author', $service_id);
		if ( $user_id != $freelancer_user_id) {
			?>
			<div class="not-found"><?php esc_html_e('You have not permission to view this page.', 'freeio'); ?></div>
			<?php
		} else {
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
				'meta_query' => array(
		            array(
		                'key'     => WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id',
		                'value'   => intval( $service_id ),
		                'compare' => '=',
		            ),
		        ),
			);
			$service_orders = new WP_Query($query_vars);

			if ( !empty($service_orders) && !empty($service_orders->have_posts()) ) { ?>
				<div class="table-responsive">
					<table class="job-table">
						<thead>
							<tr>
								<th class="job-title-td"><?php esc_html_e('Title', 'freeio'); ?></th>
								<th class="job-applicants"><?php esc_html_e('Cost/Time', 'freeio'); ?></th>
								<th class="job-status"><?php esc_html_e('Status', 'freeio'); ?></th>
								<th class="job-actions"><?php esc_html_e('Actions', 'freeio'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php
						
						$service = get_post($service_id);

						while ( $service_orders->have_posts() ) : $service_orders->the_post(); global $post;
							$service_addons = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'addons', true);
							$service_package_content = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_package_content', true);
							$employer_id = WP_Freeio_User::get_employer_by_user_id($post->post_author);
							$employer_obj = get_post($employer_id);
						?>
							<tr class="my-item-wrapper">
								<td class="job-table-info">
									<?php if ( $employer_obj ) { ?>
										<div class="title-wrapper d-flex align-items-center">
											<div class="flex-shrink-0">
												<?php freeio_employer_display_logo($employer_obj, true, '40px'); ?>
											</div>
											<h3 class="job-table-info-content-title">
												<a href="<?php echo esc_url(get_permalink($employer_obj)); ?>">
													<?php echo get_the_title($employer_id); ?>
												</a>
											</h3>
										</div>
									<?php } ?>
									<div class="job-service-title">
										<a href="<?php echo esc_url(get_permalink($service_id)); ?>"><?php echo get_the_title($service_id); ?>
											<?php
											if ( $service_package_content ) {
												?>
												(<?php echo trim($service_package_content['name']); ?>)
												<?php
											}
											?>
										</a>
									</div>
									<div class="listing-metas d-flex flex-wrap align-items-start">
										<?php freeio_service_display_short_location($service, 'icon'); ?>
										<div class="service-date">
											<i class="flaticon-30-days"></i> <?php the_time(get_option('date_format')); ?>
										</div>
									</div>
								</td>
								<td class="job-table-cost">
									<div class="price-wrapper">
						                <?php
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
									<?php
									$my_services_page_id = wp_freeio_get_option('my_services_page_id');
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