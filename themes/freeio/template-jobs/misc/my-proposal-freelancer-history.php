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
		$freelancer_user_id = get_post_field('post_author', $proposal_id);
		$proposal_project_id = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);

		if ( $user_id != $freelancer_user_id || $project_id != $proposal_project_id ) {
			?>
			<div class="not-found"><?php esc_html_e('You have not permission to view this page.', 'freeio'); ?></div>
			<?php
		} else {
			?>
			<div class="project-details-history mb-4">
				<h2 class="inner-title"><?php esc_html_e('Project Details', 'freeio'); ?></h2>
				<div class="inner-content">
					<?php
					$project = get_post($project_id);
					if ( $project ) {
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

						<?php
							$proposed_amount = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'amount', true);
							$estimeted_time = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'estimeted_time', true);
						?>
						<div class="d-flex align-items-center">
							<div class="price-wrapper">
				                <?php echo WP_Freeio_Price::format_price($proposed_amount); ?>
				            </div>
				            <div class="time ms-1">/ <?php echo sprintf(esc_html__('in %d hours', 'freeio'), $estimeted_time); ?></div>
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
					<?php } else { ?>
						<div class="alert alert-danger" role="alert">
							<?php esc_html_e('This project is not available', 'freeio'); ?>
						</div>
					<?php } ?>
				</div>
			</div>

			<div class="freelancer-history-service mt-4">
				<?php
					$employer_user_id = get_post_field('post_author', $project_id);
					$employer_post_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);
					$employer = get_post($employer_post_id);
				?>
				<div class="freelancer-item">
					<div class="d-sm-flex align-items-center">
						<div class="flex-shrink-0">
							<?php freeio_employer_display_logo($employer); ?>
						</div>
						<div class="information-right d-lg-flex">
							<div class="inner-middle">
								<div class="d-flex freelancer-title-wrapper">
									<h3 class="freelancer-title">
										<a href="<?php echo esc_url(get_permalink($employer)); ?>"><?php echo get_the_title($employer); ?></a>
									</h3>
									<span class="flex-shrink-0"><?php freeio_employer_display_featured_icon($employer); ?></span>
								</div>
								<div class="listing-metas d-flex flex-wrap align-items-start">
									<?php freeio_employer_display_short_location($employer, 'icon'); ?>
		                    		<?php freeio_employer_display_rating_reviews($employer); ?>
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