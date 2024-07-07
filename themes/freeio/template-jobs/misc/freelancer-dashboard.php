<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$user_id = WP_Freeio_User::get_user_id();
if ( empty($user_id) ) {
	return;
}
$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);

$services = new WP_Query(array(
    'post_type' => 'service',
    'post_status' => array('publish', 'hired', 'completed', 'cancelled'),
    'author' => $user_id,
    'fields' => 'ids',
    'posts_per_page' => -1,
));
$count_services = $services->found_posts;

$ids = !empty($services->posts) ? $services->posts : array();
$service_ids = array(0);
if ( $ids ) {
	foreach ($ids as $id) {
		$service_ida = apply_filters( 'wp-freeio-translations-post-ids', $id );
		if ( !empty($service_ida) && is_array($service_ida) ) {
			$service_ids = array_merge($service_ids, $service_ida );
		} else {
			$service_ids = array_merge($service_ids, array($id) );
		}
	}
}
$query_vars = array(
	'post_type'         => 'service_order',
	'posts_per_page'    => 1,
	'paged'    			=> 1,
	'post_status'       => 'hired',
	'meta_query'       => array(
		array(
			'key' => WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id',
			'value'     => $service_ids,
			'compare'   => 'IN',
		)
	)
);
$service_orders = new WP_Query($query_vars);
$service_orders_count = $service_orders->found_posts;


$completed_services = new WP_Query(array(
    'post_type' => 'service_order',
    'post_status' => 'completed',
    'fields' => 'ids',
    'posts_per_page' => 1,
    'meta_query'       => array(
		array(
			'key' => WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id',
			'value'     => $service_ids,
			'compare'   => 'IN',
		)
	)
));
$count_completed_services = $completed_services->found_posts;

$total_reviews = WP_Freeio_Review::get_total_reviews($freelancer_id);
?>
<div class="box-dashboard-wrapper freelancer-dashboard-wrapper clearfix">
	<h3 class="title"><?php esc_html_e('Dashboard', 'freeio'); ?></h3>
	<div class="space-30">
		<div class="statistics row">
			<div class="col-12 col-xl-3 col-sm-6">
				<div class="inner-header">
					<div class="posted-services list-item d-flex align-items-center justify-content-between text-right">
						<div class="inner">
							<span><?php esc_html_e('Posted Services', 'freeio'); ?></span>
							<div class="number-count"><?php echo esc_html( $count_services ? WP_Freeio_Mixes::format_number($count_services) : 0); ?></div>
							
						</div>
						<div class="icon-wrapper">
							<div class="icon">
								<i class="flaticon-contract"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-xl-3 col-sm-6">
				<div class="inner-header">
				<div class="review-count-wrapper list-item d-flex align-items-center justify-content-between text-right">
					<div class="inner">
						<span><?php esc_html_e('Completed Services', 'freeio'); ?></span>
						<div class="number-count"><?php echo esc_html( $count_completed_services ? WP_Freeio_Mixes::format_number($count_completed_services) : 0 ); ?></div>
						
					</div>
					<div class="icon-wrapper">
					<div class="icon">
						<i class="flaticon-success"></i>
					</div>
					</div>
				</div>
				</div>
			</div>
			<div class="col-12 col-xl-3 col-sm-6">
				<div class="inner-header">
				<div class="views-count-wrapper list-item d-flex align-items-center justify-content-between text-right">
					
					<div class="inner">
						<span><?php esc_html_e('In Queue Services', 'freeio'); ?></span>
						<div class="number-count"><?php echo esc_html( $service_orders_count ? WP_Freeio_Mixes::format_number($service_orders_count) : 0 ); ?></div>
						
					</div>
					<div class="icon-wrapper">
					<div class="icon">
						<i class="flaticon-sandclock"></i>
					</div>
					</div>
				</div>
				</div>
			</div>
			<div class="col-12 col-xl-3 col-sm-6">
				<div class="inner-header">
				<div class="review-count-wrapper list-item d-flex align-items-center justify-content-between text-right">
					
					<div class="inner">
						<span><?php esc_html_e('Review', 'freeio'); ?></span>
						<div class="number-count"><?php echo esc_html( $total_reviews ? WP_Freeio_Mixes::format_number($total_reviews) : 0 ); ?></div>
						
					</div>
					<div class="icon-wrapper">
						<div class="icon">
							<i class="flaticon-review-1"></i>
						</div>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
	<?php
		wp_enqueue_script( 'chart', get_template_directory_uri() . '/js/chart.min.js', array( 'jquery' ), '1.0.0', true );

		$class_second_column = '';

	?>
		<div class="row">
			<div class="col-xl-8 col-12">
			<?php
				
				if ( !empty($services->posts) ) {
					freeio_load_select2();
					$class_second_column = 'with-freelancer';
			?>
				<div class="inner-list">
					<h3 class="title-small"><?php echo esc_html__( 'Page Views', 'freeio' ); ?></h3>
					<div class="page_views-wrapper">
						
						<div class="page_views-wrapper">
							<canvas id="dashboard_job_chart_wrapper" data-post_id="<?php echo esc_attr($services->posts[0]); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'freeio-job-chart-nonce' )); ?>"></canvas>
						</div>

						<div class="search-form-wrapper">
							<form class="stats-graph-search-form form-theme" method="post">
								<div class="row">
									<div class="col-12 col-sm-6">
										<div class="form-group m-0">
											<label><?php esc_html_e('Services', 'freeio'); ?></label>
											<select class="form-control" name="post_id">
												<?php foreach ($services->posts as $post_id) { ?>
													<option value="<?php echo esc_attr($post_id); ?>"><?php echo esc_html(get_the_title($post_id)); ?></option>
												<?php } ?>
											</select>
										</div>
									</div>

									<div class="col-12 col-sm-6 mt-3 mt-sm-0">
										<div class="form-group m-0">
											<label><?php esc_html_e('Number Days', 'freeio'); ?></label>
											<select class="form-control" name="nb_days">
												<option value="30"><?php esc_html_e('30 days', 'freeio'); ?></option>
												<option value="15" selected><?php esc_html_e('15 days', 'freeio'); ?></option>
												<option value="7"><?php esc_html_e('7 days', 'freeio'); ?></option>
											</select>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<div class="inner-list">
					<h3 class="title-small"><?php echo esc_html__( 'Your Profile Views', 'freeio' ); ?></h3>
					<div class="page_views-wrapper">
						<?php
						$number_days = 14;

						// label
					    $array_labels = array();
						for ($i=$number_days; $i >= 0; $i--) { 
							$date = strtotime(date("Y-m-d", strtotime("-".$i." day")));
							$array_labels[] = date_i18n(get_option('date_format'), $date);
						}

					    // values
					    $views_by_date = get_post_meta( $freelancer_id, '_views_by_date', true );
					    if ( !is_array( $views_by_date ) ) {
					        $views_by_date = array();
					    }
					    $array_values = array();
						for ($i=$number_days; $i >= 0; $i--) { 
							$date = date("Y-m-d", strtotime("-".$i." day"));
							if ( isset($views_by_date[$date]) ) {
								$array_values[] = $views_by_date[$date];
							} else {
								$array_values[] = 0;
							}
						}

						?>

						<canvas id="dashboard_chart_wrapper" data-labels="<?php echo esc_attr(json_encode($array_labels)); ?>" data-values="<?php echo esc_attr(json_encode($array_values)); ?>" data-label="<?php esc_attr_e('Views', 'freeio'); ?>" data-chart_type="line" data-bg_color="rgb(255, 99, 132)" data-border_color="rgb(255, 99, 132)"></canvas>
					</div>
				</div>
			<?php } ?>
			</div>
			<div class="col-xl-4 col-12">
				<div class="inner-list dashboard-notifications <?php echo esc_attr($class_second_column); ?>">
				<h3 class="title-small"><?php echo esc_html__( 'Notifications', 'freeio' ); ?></h3>
				<?php
				$notifications = WP_Freeio_User_Notification::get_notifications($freelancer_id, 'freelancer');
				if ( !empty($notifications) ) {
				?>
		            <div class="dashboard-notifications-wrapper">
		                <ul>
		                    <?php foreach ($notifications as $key => $notify) {
		                        $type = !empty($notify['type']) ? $notify['type'] : '';
		                        if ( $type ) {
		                    ?>
		                            <li>
		                            	<span class="icons">
			                            	<?php
			                            	switch ($type) {
												case 'email_apply':
												case 'internal_apply':
												case 'remove_apply':
													?>
													<i class="flaticon-flag"></i>
													<?php
													break;
												case 'create_meeting':
												case 'reschedule_meeting':
												case 'remove_meeting':
												case 'cancel_meeting':
													?>
													<i class="flaticon-customer-service"></i>
													<?php
													break;
												case 'reject_applied':
												case 'undo_reject_applied':
												case 'approve_applied':
												case 'undo_approve_applied':
													?>
													<i class="flaticon-delete"></i>
													<?php
													break;
												case 'new_private_message':
													?>
													<i class="flaticon-review-1"></i>
													<?php
													break;
												default:
													?>
													<i class="flaticon-review-1"></i>
													<?php
													break;
											}
			                            	?>
		                            	</span>
		                            	<span class="text">
		                            		<div>
				                                <?php echo trim(WP_Freeio_User_Notification::display_notify($notify)); ?>
				                            </div>
				                            <small class="time">
			                            		<?php
			                            			$time = $notify['time'];
			                            			echo human_time_diff( $time, current_time( 'timestamp' ) ).' '.esc_html__( 'ago', 'freeio' );
			                            		?>
		                            		</small>
			                            </span>
		                            </li>
		                        <?php } ?>
		                    <?php } ?>
		                </ul>      
		            </div>
		        <?php } ?>
			    </div>
		    </div>
		</div>
		
	<div class="inner-list">
		<h3 class="title-small"><?php esc_html_e('Recent Service Orders', 'freeio'); ?></h3>
		<div class="service_orders">
			<?php
				if ( !empty($service_ids) ) {
					$query_args = array(
						'post_type'         => 'service_order',
						'posts_per_page'    => 5,
						'post_status'       => array('publish', 'hired', 'completed', 'cancelled'),
						'meta_query'       => array(
							array(
								'key' => WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id',
								'value'     => $service_ids,
								'compare'   => 'IN',
							)
						)
					);

					$service_orders = new WP_Query($query_args);
					
					if ( $service_orders->have_posts() ) {
						?>
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
									while ( $service_orders->have_posts() ) : $service_orders->the_post();
										global $post;
										$status = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'status', true);
										$service_addons = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'addons', true);
										$service_id = get_post_meta($post->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
										$service = get_post($service_id);
									?>
										<tr class="my-item-wrapper">
											<td class="job-table-info">
												<div class="title-wrapper">
													<h3 class="job-table-info-content-title">
														<?php the_title(); ?>
													</h3>
												</div>
												<div class="pl-10">
													<div class="job-service-title">
														<a href="<?php echo esc_url(get_permalink($service_id)); ?>"><?php echo get_the_title($service_id); ?></a>
													</div>
													<div class="listing-metas d-flex align-items-start flex-wrap">
														<?php freeio_service_display_short_location($service, 'icon'); ?>
														<div class="service-date">
															<i class="flaticon-30-days"></i> <?php the_time(get_option('date_format')); ?>
														</div>
													</div>
												</div>
											</td>
											<td class="job-table-cost">
												<div class="price-wrapper">
									                <?php
									                $meta_obj = WP_Freeio_Service_Meta::get_instance($service_id);
													if ( $meta_obj->check_post_meta_exist('price') ) {
														
														$service_price = $meta_obj->get_post_meta( 'price' );

														if ( !empty($service_addons) ) {
															foreach ($service_addons as $addon_id) {
																$addon_price = get_post_meta($addon_id, WP_FREEIO_SERVICE_ADDON_PREFIX.'price', true);
																$service_price += $addon_price;
															}
														}
										                echo WP_Freeio_Price::format_price($service_price);
										            }
									                ?>
									            </div>
											</td>
											<td class="job-table-status">

												<?php
								        		$post_status = get_post_status_object( $post->post_status );
								        		if ( $post_status == 'pending' ) {
								        			$classes = 'bg-pending';
								        		} elseif( $post_status == 'cancelled' ) {
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
											<td class="job-table-status">
												<?php
												$my_services_page_id = wp_freeio_get_option('my_services_page_id');
												$my_services_url = get_permalink( $my_services_page_id );

												$my_services_url = add_query_arg( 'service_id', $service_id, remove_query_arg( 'service_id', $my_services_url ) );
												$my_services_url = add_query_arg( 'service_order_id', $post->ID, remove_query_arg( 'service_order_id', $my_services_url ) );
												$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_services_url ) );
												?>
												<a class="btn btn-sm btn-theme-rgba10" href="<?php echo esc_url($view_history_url); ?>" title="<?php esc_attr_e('View history', 'freeio'); ?>">
													<?php esc_html_e('View History', 'freeio'); ?>
												</a>
											</td>
										</tr>

					                    <?php

									endwhile;
									wp_reset_postdata();
									?>
							</tbody>
						</table>
					</div>
						<?php
					} else {
						?>
						<div class="no-found"><?php esc_html_e('No service orders found.', 'freeio'); ?></div>
						<?php
					}
				} else {
					?>
					<div class="no-found"><?php esc_html_e('No service orders found.', 'freeio'); ?></div>
					<?php
				}
			?>
		</div>
	</div>
</div>