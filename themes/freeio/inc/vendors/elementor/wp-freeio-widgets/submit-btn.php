<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Submit_Btn extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_submit_btn';
    }

	public function get_title() {
        return esc_html__( 'Apus Header Submit Button', 'freeio' );
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
            'submit_type',
            [
                'label' => esc_html__( 'Submit Type', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'service' => esc_html__('Service', 'freeio'),
                    'project' => esc_html__('Project', 'freeio'),
                    'job' => esc_html__('Job', 'freeio'),
                ),
                'default' => 'popup'
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__( 'Button Text', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your title here', 'freeio' ),
                'default' => 'Submit Listing',
            ]
        );

        $this->add_control(
            'show_add_listing',
            [
                'label' => esc_html__( 'Show Add Job Button', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => [
                    '' => esc_html__('No', 'freeio'),
                    'always' => esc_html__('Always', 'freeio'),
                    'show-logedin-employer' => esc_html__('Employer Logged in', 'freeio'),
                    'show-logedin-freelancer' => esc_html__('Freelancer Logged in', 'freeio'),
                    'none-register-employer' => esc_html__('None Register and Employer', 'freeio'),
                    'none-register-freelancer' => esc_html__('None Register and Freelancer', 'freeio'),
                ],
                'default' => 'always',
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => esc_html__( 'Alignment', 'freeio' ),
                'type' => Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'freeio' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'freeio' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'freeio' ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
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
                'label' => esc_html__( 'Button', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .btn-submit',
            ]
        );


        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'freeio' ),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .btn-submit' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => esc_html__( 'Background Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .btn-submit' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'freeio' ),
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => esc_html__( 'Text Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .btn-submit:hover svg, {{WRAPPER}} .btn-submit:focus svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_background_hover_color',
            [
                'label' => esc_html__( 'Background Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => esc_html__( 'Hover Animation', 'freeio' ),
                'type' => Elementor\Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .btn-submit',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .btn-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_padding',
            [
                'label' => esc_html__( 'Padding', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .btn-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        
        
        if ( Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            ?>
            <div class="widget-submit-btn <?php echo esc_attr($el_class); ?>">
                <a class="btn-theme btn " href="javascript:void(0);">
                    <?php echo trim($button_text); ?>
                </a>
            </div>
            <?php
        } elseif ( $show_add_listing == 'always' || ($show_add_listing == 'show_logedin_employer' && is_user_logged_in() && WP_Freeio_User::is_employer(get_current_user_id()) ) || ($show_add_listing == 'show_logedin_freelancer' && is_user_logged_in() && WP_Freeio_User::is_freelancer(get_current_user_id()) ) || ($show_add_listing == 'none-register-employer' && (!is_user_logged_in() || WP_Freeio_User::is_employer(get_current_user_id())) ) || ($show_add_listing == 'none-register-freelancer' && (!is_user_logged_in() || WP_Freeio_User::is_freelancer(get_current_user_id())) ) ) {
            
            if ( $submit_type == 'job' ) {
                $page_id = wp_freeio_get_option('submit_job_form_page_id');
            } elseif ( $submit_type == 'project' ) {
                $page_id = wp_freeio_get_option('submit_project_form_page_id');
            } else {
                $page_id = wp_freeio_get_option('submit_service_form_page_id');
            }
            $page_id = WP_Freeio_Mixes::get_lang_post_id($page_id);

            $classes = '';
            if ( ($show_add_listing == 'always' || $show_add_listing == 'none-register-employer' || $show_add_listing == 'none-register-freelancer') && !is_user_logged_in() ) {
                $classes = 'user-login-form';
            }
            ?>
            <div class="widget-submit-btn <?php echo esc_attr($el_class); ?>">
                <a class="btn-theme btn <?php echo esc_attr($classes); ?>" href="<?php echo esc_url( get_permalink( $page_id ) ); ?>">
                    <?php echo trim($button_text); ?>
                </a>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Submit_Btn );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Submit_Btn );
}