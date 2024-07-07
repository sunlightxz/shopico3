<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_User_Notification extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_user_notification';
    }

	public function get_title() {
        return esc_html__( 'Apus Notification', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-header-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
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

        $this->add_control(
            'dropdown',
            [
                'label' => esc_html__( 'Dropdown', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'right' => esc_html__('Right', 'freeio'),
                    'left' => esc_html__('Left', 'freeio'),
                ),
                'default' => 'right'
            ]
        );

        

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__( 'Color Icon', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Elementor\Core\Schemes\Color::get_type(),
                    'value' => Elementor\Core\Schemes\Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .message-notification i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label' => esc_html__( 'Color Hover Icon', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Elementor\Core\Schemes\Color::get_type(),
                    'value' => Elementor\Core\Schemes\Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .message-notification:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .message-notification:focus i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        $count = 0;
        
        if ( is_user_logged_in() ) {
            $user_id = WP_Freeio_User::get_user_id();
            if ( WP_Freeio_User::is_employer($user_id) ) {
                $user_post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                $post_type = 'employer';
            } elseif ( method_exists('WP_Freeio_User', 'is_employee') && WP_Freeio_User::is_employee($user_id) ) {
                $user_id = WP_Freeio_User::get_user_id();
                $user_post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                $post_type = 'employer';
            } elseif ( WP_Freeio_User::is_freelancer($user_id) ) {
                $user_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
                $post_type = 'freelancer';
            }
        }
        
        if ( !empty($user_post_id) && !empty($post_type) ) {
            $notifications = WP_Freeio_User_Notification::get_notifications($user_post_id, $post_type);
        }

        ?>
        <div class="message-top <?php echo esc_attr($el_class); ?>">
            <a class="message-notification" href="javascript:void(0);">
                <i class="flaticon-notification"></i>
                <?php if ( !empty($notifications) ) { ?>
                    <span class="unread-count bg-warning"><?php echo count($notifications); ?></span>
                <?php } ?>
            </a>
            <?php if ( !empty($notifications) ) { ?>
                <div class="notifications-wrapper <?php echo trim($dropdown); ?>">
                    <ul>
                        <?php foreach ($notifications as $key => $notify) {
                            $type = !empty($notify['type']) ? $notify['type'] : '';
                            if ( $type ) {
                        ?>
                                <li>
                                    <!-- display notify content -->
                                    <i class="time">
                                        <?php
                                            $time = $notify['time'];
                                            echo human_time_diff( $time, current_time( 'timestamp' ) ).' '.esc_html__( 'ago', 'freeio' );
                                        ?>
                                    </i>
                                    <p>
                                        <?php echo trim(WP_Freeio_User_Notification::display_notify($notify)); ?>
                                        <a href="javascript:void(0);" class="remove-notify-btn" data-id="<?php echo esc_attr($notify['unique_id']); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-remove-notify-nonce' )); ?>"><i class="ti-close"></i></a>
                                    </p>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>      
                </div>
            <?php } ?>
        </div>
        <?php
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_User_Notification );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_User_Notification );
}