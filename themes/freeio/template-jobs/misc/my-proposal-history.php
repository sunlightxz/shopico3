<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="widget-proposals box-dashboard-wrapper">
	<h3 class="widget-title"><?php echo esc_html__('Proposal History','freeio') ?></h3>
	<div class="inner-list">
		
		<?php
		$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : '';
		$proposal_id = isset($_GET['proposal_id']) ? $_GET['proposal_id'] : '';
		$user_id = WP_Freeio_User::get_user_id();
		$project_user_id = get_post_field('post_author', $project_id);
		$freelancer_user_id = get_post_field('post_author', $proposal_id);
		$proposal_project_id = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);

		if ( $user_id != $project_user_id || $project_id != $proposal_project_id ) {
			?>
			<div class="not-found"><?php esc_html_e('You have not permission to view this page.', 'freeio'); ?></div>
			<?php
		} else {
			?>
			<div class="project-details-history">
				<h2 class="inner-title"><?php esc_html_e('Project Details', 'freeio'); ?></h2>
				<div class="inner-content">
					<?php
					$project = get_post($project_id);
					?>
					<div class="project-content">
						<div class="title-wrapper">
							<h3 class="project-tittle">
								<a href="<?php echo esc_url(get_permalink($project)); ?>"><?php echo get_the_title($project); ?></a>
							</h3>
							<?php freeio_project_display_featured_icon($project); ?>
						</div>
						<div class="listing-metas d-flex flex-wrap align-items-start">
							<?php freeio_project_display_category($project, 'icon'); ?>
							<?php freeio_project_display_short_location($project, 'icon'); ?>
							<?php freeio_project_display_postdate($project, 'icon'); ?>
							<?php freeio_project_display_proposals_count($project, 'icon'); ?>
						</div>
						
					</div>

					<div class="project-status">
						<?php echo esc_html__('Status : ','freeio') ?>
						<?php
						$post_status = get_post_status_object( $project->post_status );
						if ( $project->post_status == 'pending' ) {
		        			$classes = 'bg-pending';
		        		} elseif( $project->post_status == 'cancelled' ) {
		        			$classes = 'bg-cancelled';
		        		} else {
		        			$classes = 'bg-success';
		        		}
		        		?>
						<span class="badge <?php echo esc_attr($classes);?>">
							<?php
								if ( !empty($post_status->label) ) {
									echo esc_html($post_status->label);
								} else {
									echo esc_html($post_status->post_status);
								}
							?>
						</span>
					</div>
				</div>
			</div>
			<div class="freelancer-history-service mt-4">
				<?php
					$freelancer_post_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
					$freelancer = get_post($freelancer_post_id);

					$status = get_post_status($proposal_id);
					$proposed_amount = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'amount', true);
					$estimeted_time = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'estimeted_time', true);
				?>
				<div class="freelancer-item">
					<div class="d-sm-flex align-items-center">
						<div class="flex-shrink-0">
							<?php freeio_freelancer_display_logo($freelancer); ?>
						</div>
						<div class="information-right d-lg-flex align-items-center">
							<div class="inner-middle">
								<div class="d-flex freelancer-title-wrapper">
									<h3 class="freelancer-title">
										<a href="<?php echo esc_url(get_permalink($freelancer)); ?>"><?php echo get_the_title($freelancer); ?></a>
									</h3>
									<span class="flex-shrink-0"><?php freeio_freelancer_display_featured_icon($freelancer); ?></span>
								</div>
								<div class="listing-metas d-flex flex-wrap align-items-start">
									<?php freeio_freelancer_display_job_title($freelancer); ?>
									<?php freeio_freelancer_display_short_location($freelancer, 'icon'); ?>
		                    		<?php freeio_freelancer_display_rating_reviews($freelancer); ?>
								</div>
								<div class="d-flex align-items-center">
									<div class="price-wrapper">
						                <?php echo WP_Freeio_Price::format_price($proposed_amount); ?>
						            </div>
						            <div class="time ms-1">/ <?php echo sprintf(esc_html__('in %d hours', 'freeio'), $estimeted_time); ?></div>
					            </div>
							</div>
							<div class="inner-right">
								<div class="job-table-action mb-3 text-sm-end">
									<a data-toggle="tooltip" href="#view-proposal-description-wrapper-<?php echo esc_attr($proposal_id); ?>" class="btn-show-popup btn-view-proposal-description btn-action-icon" title="<?php echo esc_attr_e('Cover Letter', 'freeio'); ?>"><i class="flaticon-mail"></i></a>
									<div id="view-proposal-description-wrapper-<?php echo esc_attr($proposal_id); ?>" class="view-proposal-description-wrapper mfp-hide">
										<div class="inner">
											<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>

											<h2 class="widget-title"><span><?php esc_html_e('Cover Letter', 'freeio'); ?></span></h2>
											<div class="content">
												<?php echo wpautop(get_post_field('post_content', $proposal_id)); ?>
											</div>
										</div>
									</div>
								</div>
								<div class="job-table-status">
									<?php
									
									$statuses = WP_Freeio::post_statuses();
									if ( $status == 'hired' ) {
									?>
										<select id="proposal_status" class="proposal_status" name="proposal_status">
											<?php foreach ($statuses as $key => $title) { ?>
												<option value="<?php echo esc_attr($key); ?>" <?php selected($key, $status); ?>><?php echo trim($title); ?></option>
											<?php } ?>
										</select>
										<button type="button" class="btn btn-theme w-100 update-proposal-status" data-proposal_id="<?php echo esc_attr($proposal_id); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-update-proposal-status-nonce' )); ?>"><?php esc_html_e('Update', 'freeio'); ?><i class="flaticon-right-up next"></i></button>
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
			</div>
			<div id="messages" class="messages">
				<div id="messages-list" class="messages-list">
					<?php echo WP_Freeio_Project::list_proposal_messages($proposal_id); ?>
				</div>
				<div class="proposal-message-form-wrapper">
					<form id="proposal-message-form-<?php echo esc_attr($proposal_id); ?>" method="post" action="?" class="proposal-message-form form-theme" action="" enctype="multipart/form-data">
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
						<div class="col-12">
					     	<div class="form-group upload-file-btn-wrapper">
					            <input type="file" name="attachments[]" data-file_types="<?php echo esc_attr(!empty($file_types) ? implode('|', $file_types) : ''); ?>" multiple="multiple">

					            <div class="label-can-drag">
									<div class="group-upload">
								        <div class="upload-file-btn" data-text="<?php echo esc_attr(sprintf(esc_html__('Upload File (%s)', 'freeio'), $file_types_str)); ?>">
							            	<span class="text"><?php echo sprintf(esc_html__('Upload File (%s)', 'freeio'), $file_types_str); ?></span>
								        </div>
								    </div>
								</div>
					        </div>

				        </div><!-- /.form-group -->

			            <input type="hidden" name="project_id" value="<?php echo esc_attr($project_id); ?>">
			            <input type="hidden" name="proposal_id" value="<?php echo esc_attr($proposal_id); ?>">
			            <button class="button btn btn-theme btn-outline" name="contact-form"><?php echo esc_html__( 'Send Message', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
			        </form>
				</div>
			</div>
			<?php
			
		} ?>
	</div>
</div>