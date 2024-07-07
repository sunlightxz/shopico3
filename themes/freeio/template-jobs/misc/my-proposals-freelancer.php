<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
freeio_load_select2();
?>
<div class="widget-proposals box-dashboard-wrapper">
	<h3 class="widget-title"><?php echo esc_html__('My Proposals','freeio') ?></h3>
	<div class="inner-list">
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

		$user_id = WP_Freeio_User::get_user_id();
		$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
		$query_vars = array(
			'post_type'     => 'project_proposal',
			'post_status'   => array('publish', 'hired', 'completed', 'cancelled'),
			'paged'         => $paged,
			'author'        => $user_id,
			'orderby'		=> 'date',
			'order'			=> 'DESC',
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
		$proposals = new WP_Query($query_vars);

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
					<?php while ( $proposals->have_posts() ) : $proposals->the_post(); global $post;
						$status = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'status', true);
						$proposed_amount = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'amount', true);
						$default_proposed_amount = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'default_proposed_amount', true);
						$per_hour_amount = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'per_hour_amount', true);
						$estimeted_time = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'estimeted_time', true);
						$project_id = get_post_meta($post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
						$project = get_post($project_id);

						$employer_user_id = get_post_field('post_author', $project_id);
						$employer_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);
						$employer_obj = get_post($employer_id);
					?>
						<tr class="my-item-wrapper">
							<td class="job-table-info">
								
								<?php if ( $employer_obj ) { ?>
									<div class="title-wrapper d-flex align-items-center">
										<div class="flex-shrink-0">
											<?php freeio_employer_display_logo($employer_obj, true, '40x40'); ?>
										</div>
										<h3 class="job-table-info-content-title">
											<a href="<?php echo esc_url(get_permalink($employer_obj)); ?>">
												<?php echo get_the_title($employer_id); ?>
											</a>
										</h3>
									</div>
								<?php } ?>
								<?php if ( $project ){ ?>
									<div class="job-project-title">
										<strong><a href="<?php echo esc_url(get_permalink($project_id)); ?>"><?php echo get_the_title($project_id); ?></a></strong>
									</div>
								<?php } ?>
								<div class="listing-metas d-flex flex-wrap align-items-start">
									<?php
									if ( $project ){
										freeio_project_display_short_location($project, 'icon');
									}
									?>
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
								
								if ( $post->post_status == 'hired' ) {
									$my_proposals_page_id = wp_freeio_get_option('my_proposals_page_id');
									$my_proposals_url = get_permalink( $my_proposals_page_id );

									$my_proposals_url = add_query_arg( 'project_id', $project_id, remove_query_arg( 'project_id', $my_proposals_url ) );
									$my_proposals_url = add_query_arg( 'proposal_id', $post->ID, remove_query_arg( 'proposal_id', $my_proposals_url ) );
									$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_proposals_url ) );
									?>
									<a class="btn btn-sm btn-theme" href="<?php echo esc_url($view_history_url); ?>" title="<?php esc_attr_e('View history', 'freeio'); ?>">
										<?php esc_html_e('View history', 'freeio'); ?>
									</a>
									<?php
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

								<a data-toggle="tooltip" class="edit-btn btn-action-icon edit job-table-action proposal-button-edit btn-show-popup" href="#apus-edit-proposal-form-wrapper-<?php echo esc_attr($post->ID); ?>" title="<?php esc_attr_e('Edit', 'freeio'); ?>">
									<i class="flaticon-pencil"></i>
								</a>
								<div id="apus-edit-proposal-form-wrapper-<?php echo esc_attr($post->ID); ?>" class="apus-contact-form mfp-hide apus-popup-form" data-effect="fadeIn">
							        <a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>

							        <form id="project-proposal-form-<?php echo esc_attr($post->ID); ?>" method="post" action="?" class="project-proposal-form">
							            <div class="row">
							                <div class="col-12 col-sm-6">
							                	<div class="form-group">
							                		<?php
							                		$obj_meta = WP_Freeio_Project_Meta::get_instance($project_id);
							                		if ( $obj_meta->check_post_meta_exist('project_type') && $obj_meta->get_post_meta( 'project_type' ) == 'fixed' ) { ?>
								                        <label for="proposed-amount-id"><?php esc_html_e('Your Total Price','freeio'); ?></label>
								                    <?php } else { ?>
								                        <label for="proposed-amount-id"><?php esc_html_e('Your Hourly Price','freeio'); ?></label>
								                    <?php } ?>
							                        <input id="proposed-amount-id" autocomplete="off" type="number" name="proposed_amount" class="form-control" placeholder="<?php esc_attr_e('Price','freeio'); ?>" required value="<?php echo esc_attr($default_proposed_amount); ?>">
							                    </div>
							                </div>
							                <div class="col-12 col-sm-6">
							                	<div class="form-group">
							                        <label for="estimated-time-id"><?php esc_html_e('Estimated Hours','freeio'); ?></label>
							                        <input id="estimated-time-id" autocomplete="off" type="number" name="estimeted_time" class="form-control" placeholder="4" required value="<?php echo esc_attr($estimeted_time); ?>" placeholder="4" pattern="\d*" min="0">
							                    </div>
							                </div>
							            </div>

							            <div class="form-group">
							                <label for="description-id"><?php esc_html_e('Cover Letter','freeio'); ?></label>
							                <textarea id="description-id" class="form-control" name="description" required><?php echo trim($post->post_content); ?></textarea>
							            </div>

							            <input type="hidden" name="project_id" value="<?php echo esc_attr($project_id); ?>">
							            <input type="hidden" name="proposal_id" value="<?php echo esc_attr($post->ID); ?>">
            							<button type="submit" class="btn btn-theme btn-inverse"><?php esc_html_e('Submit a Proposal', 'freeio'); ?> <i class="flaticon-right-up next"></i></button>
							        </form>
							    </div>
							    <?php if ( $post->post_status !== 'hired' ) { ?>
									<a data-toggle="tooltip" class="remove-btn btn-action-icon deleted job-table-action proposal-button-delete" href="javascript:void(0)" data-proposal_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-delete-proposal-nonce' )); ?>" title="<?php esc_attr_e('Remove', 'freeio'); ?>">
										<i class="flaticon-delete"></i>
									</a>
								<?php } ?>
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
	</div>
</div>