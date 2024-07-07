<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$user_id = WP_Freeio_User::get_user_id();
if ( empty($user_id) ) {
	return;
}
$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);

$projects = new WP_Query(array(
    'post_type' => 'project',
    'post_status' => array('publish', 'hired', 'completed', 'cancelled'),
    'author' => $user_id,
    'fields' => 'ids',
    'posts_per_page' => -1,
));
$count_projects = $projects->found_posts;

$ids = !empty($projects->posts) ? $projects->posts : array();
$project_ids = array(0);
if ( $ids ) {
	foreach ($ids as $id) {
		$project_ida = apply_filters( 'wp-freeio-translations-post-ids', $id );
		if ( !empty($project_ida) && is_array($project_ida) ) {
			$project_ids = array_merge($project_ids, $project_ida );
		} else {
			$project_ids = array_merge($project_ids, array($id) );
		}
	}
}
$query_vars = array(
	'post_type'         => 'project_proposal',
	'posts_per_page'    => 1,
	'paged'    			=> 1,
	'post_status'       => array('publish', 'hired', 'completed', 'cancelled'),
	'meta_query'       => array(
		array(
			'key' => WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id',
			'value'     => $project_ids,
			'compare'   => 'IN',
		)
	)
);
$proposals = new WP_Query($query_vars);
$proposals_count = $proposals->found_posts;


$completed_projects = new WP_Query(array(
    'post_type' => 'project',
    'post_status' => 'completed',
    'author' => $user_id,
    'fields' => 'ids',
    'posts_per_page' => 1,
));
$count_completed_projects = $completed_projects->found_posts;

$total_reviews = WP_Freeio_Review::get_total_reviews($employer_id);
?>
<div class="box-dashboard-wrapper employer-dashboard-wrapper">
	<h3 class="title"><?php esc_html_e('Dashboard', 'freeio'); ?></h3>
	<div class="space-30">
		<div class="statistics row">
			<div class="col-12 col-xl-3 col-sm-6">
				<div class="inner-header">
					<div class="posted-projects list-item d-flex align-items-center justify-content-between text-right">
						<div class="inner">
							<span><?php esc_html_e('Posted Projects', 'freeio'); ?></span>
							<div class="number-count"><?php echo esc_html( $count_projects ? WP_Freeio_Mixes::format_number($count_projects) : 0); ?></div>
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
						<span><?php esc_html_e('Completed Projects', 'freeio'); ?></span>
						<div class="number-count"><?php echo esc_html( $count_completed_projects ? WP_Freeio_Mixes::format_number($count_completed_projects) : 0 ); ?></div>
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
						<span><?php esc_html_e('Proposals', 'freeio'); ?></span>
						<div class="number-count"><?php echo esc_html( $proposals_count ? WP_Freeio_Mixes::format_number($proposals_count) : 0 ); ?></div>
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
			<div class="col-sm-8">
			<?php
				if ( !empty($projects->posts) ) {
					freeio_load_select2();
					$class_second_column = 'with-employer';
			?>
				<div class="inner-list">
					<h3 class="title-small"><?php echo esc_html__( 'Page Views', 'freeio' ); ?></h3>
					<div class="page_views-wrapper">
						
						<div class="page_views-wrapper">
							<canvas id="dashboard_job_chart_wrapper" data-post_id="<?php echo esc_attr($projects->posts[0]); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'freeio-job-chart-nonce' )); ?>"></canvas>
						</div>

						<div class="search-form-wrapper">
							<form class="stats-graph-search-form form-theme" method="post">
								<div class="row">
									<div class="col-12 col-sm-6">
										<div class="form-group m-0">
											<label><?php esc_html_e('Projects', 'freeio'); ?></label>
											<select class="form-control" name="post_id">
												<?php foreach ($projects->posts as $post_id) { ?>
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
					    $views_by_date = get_post_meta( $employer_id, '_views_by_date', true );
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
			<div class="col-sm-4">
				<div class="inner-list dashboard-notifications <?php echo esc_attr($class_second_column); ?>">
				<h3 class="title-small"><?php echo esc_html__( 'Notifications', 'freeio' ); ?></h3>
				<?php
				$notifications = WP_Freeio_User_Notification::get_notifications($employer_id, 'employer');
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
													<i class="flaticon-briefcase"></i>
													<?php
													break;
												case 'create_meeting':
												case 'reschedule_meeting':
												case 'remove_meeting':
												case 'cancel_meeting':
													?>
													<i class="flaticon-user"></i>
													<?php
													break;
												case 'reject_applied':
												case 'undo_reject_applied':
												case 'approve_applied':
												case 'undo_approve_applied':
													?>
													<i class="flaticon-briefcase"></i>
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
		<h3 class="title-small"><?php esc_html_e('Recent Proposals', 'freeio'); ?></h3>
		<div class="proposals">
			<?php
				if ( !empty($project_ids) ) {
					$query_args = array(
						'post_type'         => 'project_proposal',
						'posts_per_page'    => 5,
						'post_status'       => array('publish', 'hired', 'completed', 'cancelled'),
						'meta_query'       => array(
							array(
								'key' => WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id',
								'value'     => $project_ids,
								'compare'   => 'IN',
							)
						)
					);

					$proposals = new WP_Query($query_args);
					
					if ( $proposals->have_posts() ) {
						?>
						<div class="table-responsive">
							<table class="job-table">
								<thead>
									<tr>
										<th class="job-title-td"><?php esc_html_e('Title', 'freeio'); ?></th>
										<th class="job-applicants"><?php esc_html_e('Cost/Time', 'freeio'); ?></th>
										<th class="job-status"><?php esc_html_e('Status', 'freeio'); ?></th>
										<th class="job-actions"><?php esc_html_e('Actions', 'freeio'); ?></th>
										<th class="job-actions"></th>
									</tr>
								</thead>
								<tbody>
									<?php
									while ( $proposals->have_posts() ) : $proposals->the_post();
										global $post;
										$proposed_amount = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'amount', true);
										$estimeted_time = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'estimeted_time', true);
										$project_id = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
										$project = get_post($project_id);
									?>
										<tr class="my-item-wrapper">
											<td class="job-table-info">
												<div class="title-wrapper">
													<h3 class="job-table-info-content-title">
														<?php the_title(); ?>
													</h3>
												</div>
												<div class="pl-10">
													<div class="job-project-title">
														<a href="<?php echo esc_url(get_permalink($project_id)); ?>"><?php echo get_the_title($project_id); ?></a>
													</div>
													<div class="listing-metas d-flex align-items-start flex-wrap">
														<?php freeio_project_display_short_location($project, 'icon'); ?>
														<div class="date-project">
															<i class="flaticon-30-days"></i> <?php the_time(get_option('date_format')); ?>
														</div>
													</div>
												</div>
											</td>
											<td class="job-table-cost">
												<div class="price-wrapper">
									                <?php echo WP_Freeio_Price::format_price($proposed_amount); ?>
									            </div>
									            <div class="time"><?php echo sprintf(esc_html__('in %d hours', 'freeio'), $estimeted_time); ?></div>
											</td>
											<td class="job-table-status">
												<?php
												$post_status = get_post_status_object( $post->post_status );
												if ( $post->post_status == 'pending' || $post->post_status == 'publish' ) {
								        			$classes = 'bg-pending';
								        		} elseif( $post->post_status == 'cancelled' ) {
								        			$classes = 'bg-cancelled';
								        		} else {
								        			$classes = 'bg-success';
								        		}
								        		?>
												<div class="badge <?php echo esc_attr($classes);?>">
													<?php
														if ( $post->post_status == 'publish' ) {
															esc_html_e('Pending', 'freeio');
														} elseif ( !empty($post_status->label) ) {
															echo esc_html($post_status->label);
														} else {
															echo esc_html($post_status->post_status);
														}
													?>
												</div>
												
											</td>
											<td class="job-table-action">
												<?php
												if ( $post->post_status == 'publish' && get_post_status($project_id) == 'publish' ) {
													?>
													<a class="btn btn-sm btn-theme proposal-button-hire-now" href="javascript:void(0)" data-proposal_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-hire-proposal-nonce' )); ?>" title="<?php esc_attr_e('Hire Now', 'freeio'); ?>">
														<?php esc_html_e('Hire Now', 'freeio'); ?>
													</a>
													<?php
												} elseif ( $post->post_status == 'hired' ) {
													$my_projects_page_id = wp_freeio_get_option('my_projects_page_id');
													$my_projects_url = get_permalink( $my_projects_page_id );

													$my_projects_url = add_query_arg( 'project_id', $project_id, remove_query_arg( 'project_id', $my_projects_url ) );
													$my_projects_url = add_query_arg( 'proposal_id', $post->ID, remove_query_arg( 'proposal_id', $my_projects_url ) );
													$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_projects_url ) );
													?>
													<a class="btn btn-sm btn-theme" href="<?php echo esc_url($view_history_url); ?>" title="<?php esc_attr_e('View history', 'freeio'); ?>">
														<?php esc_html_e('View history', 'freeio'); ?>
													</a>
													<?php
												} else {
													echo '--';
												}
												?>
											</td>
											<td class="job-table-action">
												
												<a data-toggle="tooltip" href="#view-proposal-description-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-show-popup btn-view-proposal-description btn-action-icon" title="<?php echo esc_attr_e('Cover Letter', 'freeio'); ?>"><i class="flaticon-mail"></i></a>
												<div id="view-proposal-description-wrapper-<?php echo esc_attr($post->ID); ?>" class="view-proposal-description-wrapper mfp-hide">
													<div class="inner">
														<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>

														<h2 class="widget-title"><span><?php esc_html_e('Cover Letter', 'freeio'); ?></span></h2>
														<div class="content">
															<?php echo wpautop($post->post_content); ?>
														</div>
													</div>
												</div>

												<a data-toggle="tooltip" class="remove-btn btn-action-icon deleted job-table-action proposal-button-delete" href="javascript:void(0)" data-proposal_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-delete-proposal-nonce' )); ?>" title="<?php esc_attr_e('Remove', 'freeio'); ?>">
													<i class="flaticon-delete"></i>
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
						<div class="no-found"><?php esc_html_e('No proposals found.', 'freeio'); ?></div>
						<?php
					}
				} else {
					?>
					<div class="no-found"><?php esc_html_e('No proposals found.', 'freeio'); ?></div>
					<?php
				}
			?>
		</div>
	</div>
</div>