<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Search_Form extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_search_form';
    }

	public function get_title() {
        return esc_html__( 'Apus Homepage Search Form', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Search Form', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your title here', 'freeio' ),
            ]
        );

        $this->add_control(
            'placeholder',
            [
                'label' => esc_html__( 'Placeholder', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'What are you looking for?',
            ]
        );

        $this->add_control(
            'filter_btn_text',
            [
                'label' => esc_html__( 'Button Text', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'Find Listing',
                'separator' => 'before',
            ]
        );

        $options = [
            'service' => esc_html__('Service', 'freeio'),
            'project' => esc_html__('Project', 'freeio'),
            'job' => esc_html__('Job', 'freeio'),
            'employer' => esc_html__('Employer', 'freeio'),
            'freelancer' => esc_html__('Freelancer', 'freeio'),
        ];

        foreach ($options as $key => $label) {
            $this->add_control(
                'search_'.$key,
                [
                    'label'         => sprintf(esc_html__( 'Search %s', 'freeio' ), $label),
                    'type'          => Elementor\Controls_Manager::SWITCHER,
                    'label_on'      => esc_html__( 'Show', 'freeio' ),
                    'label_off'     => esc_html__( 'Hide', 'freeio' ),
                    'return_value'  => true,
                    'default'       => true,
                ]
            );
        }

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'button' => esc_html__('Show Only Button', 'freeio'),
                ),
                'default' => ''
            ]
        );

   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'freeio' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'freeio' ),
                'separator' => 'before',
            ]
        );

        $this->end_controls_section();



        $this->start_controls_section(
            'section_button_style',
            [
                'label' => esc_html__( 'Button', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Typography', 'freeio' ),
                'name' => 'btn_typography',
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
                'button_color',
                [
                    'label' => esc_html__( 'Button Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_group_control(
                Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'background_button',
                    'label' => esc_html__( 'Background', 'freeio' ),
                    'types' => [ 'classic', 'gradient', 'video' ],
                    'selector' => '{{WRAPPER}} .btn-submit',
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .btn-submit',
                ]
            );

            $this->add_control(
                'padding_button',
                [
                    'label' => esc_html__( 'Padding', 'freeio' ),
                    'type' => Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'btn_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'freeio' ),
                    'type' => Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_button_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_control(
                'button_hover_color',
                [
                    'label' => esc_html__( 'Button Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'background_button_hover',
                    'label' => esc_html__( 'Background', 'freeio' ),
                    'types' => [ 'classic', 'gradient', 'video' ],
                    'selector' => '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus',
                ]
            );

            $this->add_control(
                'button_hover_border_color',
                [
                    'label' => esc_html__( 'Border Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'condition' => [
                        'border_button_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'padding_button_hover',
                [
                    'label' => esc_html__( 'Padding', 'freeio' ),
                    'type' => Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_control(
                'btn_hv_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'freeio' ),
                    'type' => Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .btn-submit:hover, {{WRAPPER}} .btn-submit:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab 

        $this->end_controls_section();


        $this->start_controls_section(
            'section_border_style',
            [
                'label' => esc_html__( 'Box', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'padding_box',
            [
                'label' => esc_html__( 'Padding', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .widget-listing-search-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'bg_box',
            [
                'label' => esc_html__( 'Background Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .widget-listing-search-form' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'label' => esc_html__( 'Box Shadow', 'freeio' ),
                'selector' => '{{WRAPPER}} .widget-listing-search-form',
            ]
        );

        $this->add_control(
            'box_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .widget-listing-search-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_typography_style',
            [
                'label' => esc_html__( 'Typography', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Text Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-search' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .advance-search-btn' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .circle-check' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control::-webkit-input-placeholder' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control:-ms-input-placeholder ' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .form-control::placeholder ' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .select2-selection--single .select2-selection__rendered' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .select2-selection--single .select2-selection__placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_color',
            [
                'label' => esc_html__( 'Border Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-control' => 'border-color: {{VALUE}} !important;',
                    '{{WRAPPER}} .select2-container--default.select2-container .select2-selection--single' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'border_hv_color',
            [
                'label' => esc_html__( 'Border Active Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .form-control:focus' => 'border-color: {{VALUE}} !important;',
                    '{{WRAPPER}} .select2-container--default.select2-container.select2-container--open .select2-selection--single' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        
        $service_page_url = WP_Freeio_Mixes::get_services_page_url();
        $project_page_url = WP_Freeio_Mixes::get_projects_page_url();
        $job_page_url = WP_Freeio_Mixes::get_jobs_page_url();
        $employer_page_url = WP_Freeio_Mixes::get_employers_page_url();
        $freelancer_page_url = WP_Freeio_Mixes::get_freelancers_page_url();

        freeio_load_select2();

        $widget_id = freeio_random_key();
        
        $search_page_url = $service_page_url;
        if ( $search_service ) {
            $search_page_url = $service_page_url;
        } elseif($search_project) {
            $search_page_url = $project_page_url;
        } elseif($search_job) {
            $search_page_url = $job_page_url;
        } elseif($search_employer) {
            $search_page_url = $employer_page_url;
        } elseif($search_freelancer) {
            $search_page_url = $freelancer_page_url;
        }
        ?>
        <div class="widget-listing-search-form horizontal <?php echo esc_attr($el_class.' '.$style); ?>">
            
            <?php if ( $title ) { ?>
                <h2 class="title"><?php echo esc_html($title); ?></h2>
            <?php } ?>

            <?php if ( $style == 'button' ) { ?>
                <button type="button" class="btn-search-header" data-bs-toggle="modal" data-bs-target="#search-header"><i class="flaticon-loupe"></i></button>
                <div class="modal modal-search-header fade" id="search-header" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"><i class="ti-close"></i></button>
            <?php } ?>

            <form id="filter-listing-form-<?php echo esc_attr($widget_id); ?>" action="<?php echo esc_url($search_page_url); ?>" class="form-search filter-listing-form <?php echo esc_attr($style); ?>" method="GET">
                <div class="search-form-inner">

                        <div class="main-inner clearfix">
                            <div class="content-main-inner">
                                <div class="row row-20 align-items-center list-fileds">
                                    <div class="col-12 col-md-5 form-group-search item-column">
                                        <input type="text" class="form-control" name="filter-title" placeholder="<?php echo esc_attr($placeholder); ?>">
                                    </div>
                                    <div class="col-12 col-md-5 form-group-search item-column item-last">
                                        <select class="filter-role" name="filter-role">
                                            <?php if ( $search_service ) { ?>
                                                <option value="service" data-url="<?php echo esc_url($service_page_url); ?>"><?php esc_html_e('Service', 'freeio'); ?></option>
                                            <?php } ?>
                                            <?php if ( $search_project ) { ?>
                                                <option value="project" data-url="<?php echo esc_url($project_page_url); ?>"><?php esc_html_e('Project', 'freeio'); ?></option>
                                            <?php } ?>
                                            <?php if ( $search_job ) { ?>
                                                <option value="job" data-url="<?php echo esc_url($job_page_url); ?>"><?php esc_html_e('Job', 'freeio'); ?></option>
                                            <?php } ?>
                                            <?php if ( $search_employer ) { ?>
                                                <option value="employer" data-url="<?php echo esc_url($employer_page_url); ?>"><?php esc_html_e('Employer', 'freeio'); ?></option>
                                            <?php } ?>
                                            <?php if ( $search_freelancer ) { ?>
                                                <option value="freelancer" data-url="<?php echo esc_url($freelancer_page_url); ?>"><?php esc_html_e('Freelancer', 'freeio'); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-2 form-group-search">
                                        <button class="btn-submit btn w-100 btn-theme btn-inverse" type="submit">
                                            <?php if( !empty($filter_btn_text) ) { ?>
                                                <?php echo trim($filter_btn_text); ?>
                                            <?php } ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                </div>
            </form>

        </div>
        <?php
    }

}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Search_Form );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Search_Form );
}