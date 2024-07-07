<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Dashboard_Employer_Recent_Proposals extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_dashboard_employer_recent_proposals';
	}

	public function get_title() {
		return esc_html__( 'Employer Dashboard:: Recent Proposals', 'freeio' );
	}
	
	public function get_categories() {
		return [ 'freeio-dashboard-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Settings', 'freeio' ),
			]
		);

		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'freeio' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'freeio' ),
            ]
        );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );

        $user_id = WP_Freeio_User::get_user_id();
        $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
        if ( Elementor\Plugin::$instance->editor->is_edit_mode() ) {
        	$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$employers = freeio_get_employers($args);
			if ( !empty($employers->posts) ) {
				$employer_id = $employers->posts[0];
				$user_id = WP_Freeio_User::get_user_by_employer_id($employer_id);
			}
        } else {
        	if ( !is_user_logged_in() || !WP_Freeio_User::is_employer($user_id) ) {
        		return;
        	}
        }

		$projects = new WP_Query(array(
		    'post_type' => 'project',
		    'post_status' => array('publish', 'hired', 'completed', 'cancelled'),
		    'author' => $user_id,
		    'fields' => 'ids',
		    'posts_per_page' => -1,
		));

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

		if ( empty($project_ids) ) {
			?>
			<div class="no-found"><?php esc_html_e('No proposals found.', 'freeio'); ?></div>
			<?php
			return;
		}

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
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Dashboard_Employer_Recent_Proposals );
