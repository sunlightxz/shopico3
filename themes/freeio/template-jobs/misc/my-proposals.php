<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
freeio_load_select2();
?>
<div class="widget-proposals box-dashboard-wrapper">
	<h3 class="widget-title"><?php echo esc_html__('My Proposal','freeio') ?></h3>
	<div class="inner-list">
		
		<?php
		$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';
		$user_id = WP_Freeio_User::get_user_id();
		$project_user_id = get_post_field('post_author', $project_id);
		if ( $user_id != $project_user_id ) {
			?>
			<div class="not-found"><?php esc_html_e('You have not permission to view this page.', 'freeio'); ?></div>
			<?php
		} else {
			$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
			$query_vars = array(
				'post_type'     => 'project_proposal',
				'post_status'   => array('publish', 'hired', 'completed', 'cancelled'),
				'paged'         => $paged,
				'order' => 'DESC',
		        'orderby' => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
					'ID'         => 'DESC',
				),
				'meta_query' => array(
		            array(
		                'key'     => WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id',
		                'value'   => intval( $project_id ),
		                'compare' => '=',
		            ),
		        ),
			);
			$proposals = new WP_Query($query_vars);

			$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);

			$zoom_email = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_email');
			$zoom_client_id = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_client_id');
			$zoom_client_secret = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_client_secret');
			$access_token = WP_Freeio_Meeting_Zoom::user_zoom_access_token($user_id);

			if ( !empty($proposals) && !empty($proposals->have_posts()) ) { ?>
				<div class="table-responsive">
					<table class="job-table">
						<thead>
							<tr>
								<th class="job-title-td"><?php esc_html_e('Title', 'freeio'); ?></th>
								<th class="job-applicants"><?php esc_html_e('Cost/Time', 'freeio'); ?></th>
								<th class="job-status"><?php esc_html_e('Status', 'freeio'); ?></th>
								<th class="job-history"><?php esc_html_e('Actions', 'freeio'); ?></th>
								<th class="job-actions"></th>
							</tr>
						</thead>
						<tbody>
						<?php
						
						$project = get_post($project_id);

						while ( $proposals->have_posts() ) : $proposals->the_post(); global $post;
							$proposed_amount = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'amount', true);
							$estimeted_time = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'estimeted_time', true);
							
							$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($post->post_author);
							$freelancer_obj = get_post($freelancer_id);

						?>
							<tr class="my-item-wrapper">
								<td class="job-table-info">
									<?php if ( $freelancer_obj ) { ?>
										<div class="title-wrapper d-flex align-items-center">
											<div class="flex-shrink-0">
												<?php freeio_freelancer_display_logo($freelancer_obj, true, '40x40'); ?>
											</div>
											<h3 class="job-table-info-content-title">
												<a href="<?php echo esc_url(get_permalink($freelancer_obj)); ?>">
													<?php echo get_the_title($freelancer_id); ?>
												</a>
											</h3>
										</div>
									<?php } ?>
									<div class="job-project-title">
										<strong><a href="<?php echo esc_url(get_permalink($project_id)); ?>"><?php echo get_the_title($project_id); ?></a></strong>
									</div>
									<div class="listing-metas d-flex flex-wrap align-items-start">
										<?php freeio_project_display_short_location($project, 'icon'); ?>
										<div class="date"><i class="flaticon-30-days"></i><?php the_time(get_option('date_format')); ?></div>
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
									$status_label = '';
					        		$post_status = get_post_status_object( $post->post_status );
									if ( $post->post_status == 'pending' || $post->post_status == 'publish' ) {
					        			$classes = 'bg-pending';
					        			$status_label = esc_html__('Pending', 'freeio');
					        		} elseif( $post->post_status == 'cancelled' ) {
					        			$classes = 'bg-cancelled';
					        		} else {
					        			$classes = 'bg-success';
					        		}
									?>
									<div class="badge <?php echo esc_attr($classes);?>">
										<?php
											if ( !empty($status_label) ) {
												echo trim($status_label);
											} elseif ( !empty($post_status->label) ) {
												echo esc_html($post_status->label);
											}
										?>
									</div>
								</td>

								<td class="job-table-history">
									<?php
									$status = get_post_status($post);
									if ( $status == 'publish' && get_post_status($project_id) == 'publish' ) {
										?>
										<a class="btn btn-sm btn-theme proposal-button-hire-now" href="javascript:void(0)" data-proposal_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-hire-proposal-nonce' )); ?>" title="<?php esc_attr_e('Hire Now', 'freeio'); ?>">
											<?php esc_html_e('Hire Now', 'freeio'); ?>
										</a>
										<?php
									} elseif ( $status == 'hired' ) {
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
									}
									?>
								</td>

								<td class="job-table-action">
									<?php if ( !empty($zoom_email) && !empty($zoom_client_id) && !empty($zoom_client_secret) && !empty($access_token) ) { ?>
										<a data-bs-toggle="tooltip" href="#job-apply-create-meeting-form-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-create-meeting-job-applied btn-action-icon" title="<?php echo esc_attr_e('Create Meeting', 'freeio'); ?>"><i class="ti-plus"></i></a>
	                    				<?php echo WP_Freeio_Template_Loader::get_template_part('misc/meeting-form'); ?>
	                    			<?php } ?>


									<a data-bs-toggle="tooltip" href="#view-proposal-description-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-show-popup btn-view-proposal-description btn-action-icon" title="<?php echo esc_attr_e('Cover Letter', 'freeio'); ?>"><i class="flaticon-mail"></i></a>
									<div id="view-proposal-description-wrapper-<?php echo esc_attr($post->ID); ?>" class="view-proposal-description-wrapper mfp-hide">
										<div class="inner">
											<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>

											<h2 class="widget-title"><span><?php esc_html_e('Cover Letter', 'freeio'); ?></span></h2>
											<div class="content">
												<?php echo wpautop($post->post_content); ?>
											</div>
										</div>
									</div>

									<!-- <a data-bs-toggle="tooltip" class="remove-btn btn-action-icon deleted job-table-action proposal-button-delete" href="javascript:void(0)" data-proposal_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-delete-proposal-nonce' )); ?>" title="<?php esc_attr_e('Remove', 'freeio'); ?>">
										<i class="flaticon-delete"></i>
									</a> -->
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
					'wp_query' => $proposals,
					'max_num_pages' => $proposals->max_num_pages,
					'prev_text'     => '<i class="ti-angle-left"></i>',
					'next_text'     => '<i class="ti-angle-right"></i>',
				));
			?>

			<?php } else { ?>
				<div class="not-found"><?php esc_html_e('No proposal found.', 'freeio'); ?></div>
			<?php } ?>
		<?php } ?>
	</div>
</div>