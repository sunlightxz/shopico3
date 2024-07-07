<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Dashboard_Freelancer_Recent_Service_Orders extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_dashboard_freelancer_recent_service_orders';
	}

	public function get_title() {
		return esc_html__( 'Freelancer Dashboard:: Recent Service Orders', 'freeio' );
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
        $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
        if ( Elementor\Plugin::$instance->editor->is_edit_mode() ) {
        	$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$freelancers = freeio_get_freelancers($args);
			if ( !empty($freelancers->posts) ) {
				$freelancer_id = $freelancers->posts[0];
				$user_id = WP_Freeio_User::get_user_by_freelancer_id($freelancer_id);
			}
        } else {
        	if ( !is_user_logged_in() || !WP_Freeio_User::is_freelancer($user_id) ) {
        		return;
        	}
        }

		$services = new WP_Query(array(
		    'post_type' => 'service',
		    'post_status' => array('publish', 'hired', 'completed', 'cancelled'),
		    'author' => $user_id,
		    'fields' => 'ids',
		    'posts_per_page' => -1,
		));

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

		if ( empty($service_ids) ) {
			?>
			<div class="no-found"><?php esc_html_e('No service orders found.', 'freeio'); ?></div>
			<?php
			return;
		}

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
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Dashboard_Freelancer_Recent_Service_Orders );
