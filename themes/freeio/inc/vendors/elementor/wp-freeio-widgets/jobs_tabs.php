<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Freeio_Jobs_Tabs extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_freeio_jobs_tabs';
    }

	public function get_title() {
        return esc_html__( 'Apus Jobs Tabs', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-elements' ];
    }

    public function get_tax_keys() {
        return array('category', 'type', 'location');
    }

	protected function register_controls() {

        $meta_obj = WP_Freeio_Job_Listing_Meta::get_instance(0);

        $fields = $meta_obj->get_metas();

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Jobs', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'title', [
                'label' => esc_html__( 'Tab Title', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT
            ]
        );

        $tax_keys = $this->get_tax_keys();
        foreach( $tax_keys as $tax_key ) {
            if ( $meta_obj->check_post_meta_exist($tax_key) ) {
                $repeater->add_control(
                    $tax_key.'_slugs',
                    [
                        'label' => sprintf(esc_html__( '%s Slug', 'freeio' ), $fields[WP_FREEIO_JOB_LISTING_PREFIX.$tax_key]['name']),
                        'type' => Elementor\Controls_Manager::TEXTAREA,
                        'rows' => 1,
                        'default' => '',
                        'placeholder' => esc_html__( 'Enter slugs spearate by comma(,)', 'freeio' ),
                    ]
                );
            }
        }

        $repeater->add_control(
            'orderby',
            [
                'label' => esc_html__( 'Order by', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'date' => esc_html__('Date', 'freeio'),
                    'ID' => esc_html__('ID', 'freeio'),
                    'author' => esc_html__('Author', 'freeio'),
                    'title' => esc_html__('Title', 'freeio'),
                    'modified' => esc_html__('Modified', 'freeio'),
                    'rand' => esc_html__('Random', 'freeio'),
                    'comment_count' => esc_html__('Comment count', 'freeio'),
                    'menu_order' => esc_html__('Menu order', 'freeio'),
                ),
                'default' => ''
            ]
        );

        $repeater->add_control(
            'order',
            [
                'label' => esc_html__( 'Sort order', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'ASC' => esc_html__('Ascending', 'freeio'),
                    'DESC' => esc_html__('Descending', 'freeio'),
                ),
                'default' => ''
            ]
        );

        $repeater->add_control(
            'get_jobs_by',
            [
                'label' => esc_html__( 'Get Jobs By', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'featured' => esc_html__('Featured Jobs', 'freeio'),
                    'urgent' => esc_html__('Urgent Jobs', 'freeio'),
                    'recent' => esc_html__('Recent Jobs', 'freeio'),
                ),
                'default' => ''
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
            'des',
            [
                'label' => esc_html__( 'Description', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your title here', 'freeio' ),
            ]
        );

        $this->add_control(
            'tabs',
            [
                'label' => esc_html__( 'Tabs', 'freeio' ),
                'type' => Elementor\Controls_Manager::REPEATER,
                'placeholder' => esc_html__( 'Enter your job tabs here', 'freeio' ),
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->add_control(
            'limit',
            [
                'label' => esc_html__( 'Limit', 'freeio' ),
                'type' => Elementor\Controls_Manager::NUMBER,
                'input_type' => 'number',
                'description' => esc_html__( 'Limit jobs to display', 'freeio' ),
                'default' => 4
            ]
        );
        
        $this->add_control(
            'job_item_style',
            [
                'label' => esc_html__( 'Job Item Style', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'list' => esc_html__('List Default', 'freeio'),
                    'grid' => esc_html__('Grid Default', 'freeio'),
                ),
                'default' => 'list'
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'grid' => esc_html__('Grid', 'freeio'),
                    'carousel' => esc_html__('Carousel', 'freeio'),
                ),
                'default' => 'list'
            ]
        );

        $columns = range( 1, 12 );
        $columns = array_combine( $columns, $columns );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $columns,
                'frontend_available' => true,
                'default' => 3,
            ]
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'label' => esc_html__( 'Slides to Scroll', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'freeio' ),
                'options' => $columns,
                'condition' => [
                    'columns!' => '1',
                    'layout_type' => 'carousel',
                ],
                'frontend_available' => true,
                'default' => 1,
            ]
        );

        $this->add_control(
            'rows',
            [
                'label' => esc_html__( 'Rows', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'number',
                'placeholder' => esc_html__( 'Enter your rows number here', 'freeio' ),
                'default' => 1,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'show_nav',
            [
                'label'         => esc_html__( 'Show Navigation', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'         => esc_html__( 'Show Pagination', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'slider_autoplay',
            [
                'label'         => esc_html__( 'Autoplay', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'freeio' ),
                'label_off'     => esc_html__( 'No', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'infinite_loop',
            [
                'label'         => esc_html__( 'Infinite Loop', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'freeio' ),
                'label_off'     => esc_html__( 'No', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'view_more_text',
            [
                'label' => esc_html__( 'View More Button Text', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your view more text here', 'freeio' ),
            ]
        );

        $this->add_control(
            'view_more_url',
            [
                'label' => esc_html__( 'View More URL', 'freeio' ),
                'type' => Elementor\Controls_Manager::URL,
                'placeholder' => esc_html__( 'Enter your view more url here', 'freeio' ),
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
            'section_nav_style',
            [
                'label' => esc_html__( 'Nav', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_control(
                'nav_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'freeio' ),
                    'type' => Elementor\Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .nav-categories > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_button_top_style',
            [
                'label' => esc_html__( 'Button Bottom', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_button_header_style' );

            $this->start_controls_tab(
                'tab_button_header_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            );

            $this->add_control(
                'button_header_color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .bottom-remore .btn' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_header_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .bottom-remore .btn' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button_header',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .bottom-remore .btn',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_button_header_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_control(
                'button_header_color_hv',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .bottom-remore .btn:hover' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .bottom-remore .btn:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_header_bg_color_hv',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .bottom-remore .btn:hover' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .bottom-remore .btn:focus' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button_header_hv',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .bottom-remore .btn:hover',
                    'selector' => '{{WRAPPER}} .bottom-remore .btn:focus',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab 

        $this->add_control(
            'padding_button_header',
            [
                'label' => esc_html__( 'Padding', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .bottom-remore .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        $_id = freeio_random_key();

        $columns = !empty($columns) ? $columns : 3;
        $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 2;
        $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
        
        $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
        $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
        $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;
        ?>
        <div class="widget-jobs-tabs <?php echo esc_attr($layout_type); ?> <?php echo esc_attr($el_class); ?>">

            <div class="top-widget-info d-xl-flex align-items-end">
                <?php if ( !empty($title) || !empty($des) ) { ?>
                    <div class="inner-left">
                        <?php if ( !empty($title) ) { ?>
                            <h2 class="widget-title"><?php echo esc_html($title); ?></h2>
                        <?php } ?>
                        <?php if ( !empty($des) ) { ?>
                            <div class="des"><?php echo esc_html($des); ?></div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <ul role="tablist" class="nav nav-tabs nav-categories justify-content-xl-center">
                    <?php $tab_count = 0; foreach ($tabs as $tab) : ?>
                        <li class="<?php echo esc_attr($tab_count == 0 ? 'active' : '');?>">
                            <a href="#tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($tab_count); ?>" data-bs-toggle="tab">
                                <?php if ( !empty($tab['title']) ) { ?>
                                    <?php echo trim($tab['title']); ?>
                                <?php } ?>
                            </a>
                        </li>
                    <?php $tab_count++; endforeach; ?>
                </ul>
            </div>

            <div class="tab-content">
                <?php $tab_count = 0; foreach ($tabs as $tab) : ?>
                    <div id="tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($tab_count); ?>" class="tab-pane <?php echo esc_attr($tab_count == 0 ? 'active' : ''); ?>">
                        <?php

                        $args = array(
                            'limit' => $limit,
                            'get_jobs_by' => !empty($tab['get_jobs_by']) ? $tab['get_jobs_by'] : 'recent',
                            'orderby' => !empty($tab['orderby']) ? $tab['orderby'] : '',
                            'order' => !empty($tab['order']) ? $tab['order'] : '',
                        );

                        $tax_keys = $this->get_tax_keys();
                        foreach( $tax_keys as $tax_key ) {
                            $args[$tax_key] = !empty($settings[$tax_key.'_slugs']) ? array_map('trim', explode(',', $settings[$tax_key.'_slugs'])) : array();
                        }

                        $loop = freeio_get_jobs($args);
                        if ( $loop->have_posts() ) {
                            ?>
                            <?php if ( $layout_type == 'carousel' ): ?>
                                <div class="slick-carousel <?php echo esc_attr( ( ($columns * $rows) >= ($loop->post_count)) ? 'hidden-dots':'' ); ?>"

                                    data-items="<?php echo esc_attr($columns); ?>"
                                    data-large="<?php echo esc_attr( $columns_tablet ); ?>"
                                    data-medium="<?php echo esc_attr( $columns_tablet ); ?>"
                                    data-small="<?php echo esc_attr($columns_mobile); ?>"
                                    data-smallest="<?php echo esc_attr($columns_mobile); ?>"

                                    data-slidestoscroll="<?php echo esc_attr($slides_to_scroll); ?>"
                                    data-slidestoscroll_large="<?php echo esc_attr( $slides_to_scroll_tablet ); ?>"
                                    data-slidestoscroll_medium="<?php echo esc_attr( $slides_to_scroll_tablet ); ?>"
                                    data-slidestoscroll_small="<?php echo esc_attr($slides_to_scroll_mobile); ?>"
                                    data-slidestoscroll_smallest="<?php echo esc_attr($slides_to_scroll_mobile); ?>"

                                    data-pagination="<?php echo esc_attr( $show_pagination ? 'true' : 'false' ); ?>"
                                    data-nav="<?php echo esc_attr( $show_nav ? 'true' : 'false' ); ?>"
                                    data-rows="<?php echo esc_attr( $rows ); ?>"
                                    data-infinite="<?php echo esc_attr( $infinite_loop ? 'true' : 'false' ); ?>"
                                    data-autoplay="<?php echo esc_attr( $slider_autoplay ? 'true' : 'false' ); ?>">
                                    <?php while ( $loop->have_posts() ): $loop->the_post(); ?>
                                        <div class="cl-inner">
                                            <?php get_template_part( 'template-jobs/jobs-styles/inner', $job_item_style); ?>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php elseif( $layout_type == 'grid' ): ?>
                                <?php
                                    $mdcol = 12/$columns;
                                    $smcol = 12/$columns_tablet;
                                    $xscol = 12/$columns_mobile;
                                ?>
                                <div class="row">
                                    <?php while ( $loop->have_posts() ) : $loop->the_post();
                                    ?>
                                        <div class="col-lg-<?php echo esc_attr($mdcol); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr( $xscol ); ?> list-item">
                                            <?php get_template_part( 'template-jobs/jobs-styles/inner', $job_item_style ); ?>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php endif; ?>
                            <?php wp_reset_postdata(); ?>
                        <?php } ?>
                    </div>
                <?php $tab_count++; endforeach; ?>
            </div>
            <?php 
            if ( $view_more_text ) { ?>
                <div class="bottom-remore text-center">
                    <?php
                    $view_more_html = '<a class="btn btn-theme-rgba10 radius-50" href="'.esc_url($view_more_url['url']).'" target="'.esc_attr($view_more_url['is_external'] ? '_blank' : '_self').'" '.($view_more_url['nofollow'] ? 'rel="nofollow"' : '').'>' . $view_more_text . '<i class="flaticon-right-up next"></i></a>';
                    echo trim($view_more_html);
                    ?>
                </div>
            <?php } ?>
        </div>
        <?php
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Freeio_Jobs_Tabs );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Freeio_Jobs_Tabs );
}