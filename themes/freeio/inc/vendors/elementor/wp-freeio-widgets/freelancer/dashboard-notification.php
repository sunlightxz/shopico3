<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Dashboard_Freelancer_Notification extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_dashboard_freelancer_notification';
	}

	public function get_title() {
		return esc_html__( 'Freelancer Dashboard:: Notification', 'freeio' );
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

		$this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'freeio' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1440,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dashboard-notifications-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                ],
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

		$notifications = WP_Freeio_User_Notification::get_notifications($freelancer_id, 'freelancer');
		if ( empty($notifications) ) {
			return;
		}
		?>
		<div class="dashboard-notifications-wrapper <?php echo esc_attr($el_class); ?>">
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
        
		<?php
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Dashboard_Freelancer_Notification );