<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Packages extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_packages';
    }

	public function get_title() {
        return esc_html__( 'Apus Packages', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-elements' ];
    }

	protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        
        $options = array(
            'service_package' => esc_html__('Service Package', 'freeio'),
            'project_package' => esc_html__('Project Package', 'freeio'),
            'job_package' => esc_html__('Job Package', 'freeio'),
            'cv_package' => esc_html__('CV Package', 'freeio'),
            'contact_package' => esc_html__('Contact Package', 'freeio'),
            'freelancer_package' => esc_html__('Freelancer Package', 'freeio'),
            'resume_package' => esc_html__('Resume Package', 'freeio'),
        );
        
        $this->add_control(
            'package_type',
            [
                'label' => esc_html__( 'Packages Type', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $options,
                'default' => 'service_package'
            ]
        );

        $this->add_control(
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

        $this->add_control(
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

        $this->add_control(
            'number',
            [
                'label' => esc_html__( 'Number Product', 'freeio' ),
                'type' => Elementor\Controls_Manager::NUMBER,
                'input_type' => 'number',
                'description' => esc_html__( 'Number Product to display', 'freeio' ),
                'default' => 3
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
                'condition' => [
                    'layout_type' => ['grid', 'carousel'],
                ],
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
                'default' => 'carousel'
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
            'autoplay',
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
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'freeio' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'freeio' ),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_widget_style',
            [
                'label' => esc_html__( 'Box', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_widget_style' );

            $this->start_controls_tab(
                'tab_widget_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            ); 

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_box',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .subwoo-inner',
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_shadow',
                    'label' => esc_html__( 'Box Shadow', 'freeio' ),
                    'selector' => '{{WRAPPER}} .subwoo-inner',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_widget_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_box_hv',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .subwoo-inner:hover',
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_shadow_hv',
                    'label' => esc_html__( 'Box Shadow', 'freeio' ),
                    'selector' => '{{WRAPPER}} .subwoo-inner:hover',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover


        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Button', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


        $this->start_controls_tabs( 'tabs_box_style' );

            $this->start_controls_tab(
                'tab_box_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            ); 

            $this->add_control(
                'btn_color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .subwoo-inner .add-cart .button' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .subwoo-inner .add-cart .button.loading::after' => 'color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'btn_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .subwoo-inner .add-cart .button' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
            $this->add_control(
                'btn_br_color',
                [
                    'label' => esc_html__( 'Border Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .subwoo-inner .add-cart .button' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_box_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );


                $this->add_control(
                    'btn_hv_color',
                    [
                        'label' => esc_html__( 'Color', 'freeio' ),
                        'type' => Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .subwoo-inner:hover .add-cart .button' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .subwoo-inner .add-cart .added_to_cart' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .subwoo-inner.is_featured .add-cart .button' => 'color: {{VALUE}};',
                        ],
                    ]
                );
                $this->add_control(
                    'btn_hv_bg_color',
                    [
                        'label' => esc_html__( 'Background Color', 'freeio' ),
                        'type' => Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .subwoo-inner:hover .add-cart .button' => 'background-color: {{VALUE}};',
                            '{{WRAPPER}} .subwoo-inner .add-cart .added_to_cart' => 'background-color: {{VALUE}};',
                            '{{WRAPPER}} .subwoo-inner.is_featured .add-cart .button' => 'background-color: {{VALUE}};',
                        ],
                    ]
                );
                $this->add_control(
                    'btn_hv_br_color',
                    [
                        'label' => esc_html__( 'Border Color', 'freeio' ),
                        'type' => Elementor\Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .subwoo-inner:hover .add-cart .button' => 'border-color: {{VALUE}};',
                            '{{WRAPPER}} .subwoo-inner .add-cart .added_to_cart' => 'border-color: {{VALUE}};',
                            '{{WRAPPER}} .subwoo-inner.is_featured .add-cart .button' => 'border-color: {{VALUE}};',
                        ],
                    ]
                );


            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover
        
        $this->add_control(
            'btn_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .subwoo-inner .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );

        $loop = freeio_get_products(array(
            'product_type' => $package_type,
            'post_per_page' => $number,
            'orderby' => $orderby,
            'order' => $order
        ));
        ?>
        <div class="woocommerce widget-subwoo <?php echo esc_attr($el_class); ?>">
            <?php if ($loop->have_posts()): ?>
                <?php 
                    $columns = !empty($columns) ? $columns : 3;
                    $columns_tablet_extra = !empty($settings['columns_tablet_extra']) ? $settings['columns_tablet_extra'] : $columns;
                    $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 2;
                    $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
                    
                    $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
                    $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
                    $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;
                ?>
                <div class="widget-content">
                    <?php if ( $layout_type == 'carousel' ): ?>
                        <div class="slick-carousel <?php echo esc_attr($columns < $loop->post_count?'':'hidden-dots'); ?>"
                            data-items="<?php echo esc_attr($columns); ?>"
                            data-large="<?php echo esc_attr( $columns_tablet_extra ); ?>"
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
                            data-autoplay="<?php echo esc_attr( $autoplay ? 'true' : 'false' ); ?>">
                        <?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
                            <div class="item">
                                <div class="subwoo-inner text-center <?php echo esc_attr($product->is_featured()?'is_featured':''); ?>">
                                    <div class="item">
                                        <div class="header-sub">
                                            <div class="price"><?php echo (!empty($product->get_price())) ? $product->get_price_html() : esc_html__('Free','freeio'); ?></div>
                                            <h3 class="title"><?php the_title(); ?></h3>
                                            <?php if ( has_excerpt() ) { ?>
                                                <div class="short-des"><?php the_excerpt(); ?></div>
                                            <?php } ?>
                                        </div>
                                        <div class="bottom-sub">
                                            <div class="content">
                                                <?php the_content(); ?>
                                            </div>
                                            <div class="button-action"><?php do_action( 'woocommerce_after_shop_loop_item' ); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                        <?php endwhile; ?>
                        </div>
                    <?php elseif( $layout_type == 'grid' ): ?>
                        <?php
                            $mdcol = 12/$columns;
                            $columns_tablet_extra = 12/$columns_tablet_extra;
                            $smcol = 12/$columns_tablet;
                            $xscol = 12/$columns_mobile;
                        ?>
                        <div class="row">
                            <?php $i = 1; while ( $loop->have_posts() ) : $loop->the_post(); global $product;
                                $classes = '';
                                if ( $i%$columns == 1 ) {
                                    $classes .= ' md-clearfix lg-clearfix';
                                }
                                if ( $i%$columns_tablet == 1 ) {
                                    $classes .= ' sm-clearfix';
                                }
                                if ( $i%$columns_mobile == 1 ) {
                                    $classes .= ' xs-clearfix';
                                }
                            ?>
                                <div class="col-md-<?php echo esc_attr($mdcol); ?> col-tablet-extra-<?php echo esc_attr($columns_tablet_extra); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr( $xscol ); ?> <?php echo esc_attr($classes); ?>">
                                    <div class="subwoo-inner text-center <?php echo esc_attr($product->is_featured()?'is_featured':''); ?>">
                                        <div class="item">
                                            <div class="header-sub">
                                                <div class="price"><?php echo (!empty($product->get_price())) ? $product->get_price_html() : esc_html__('Free','freeio'); ?></div>
                                                <h3 class="title"><?php the_title(); ?></h3>
                                                <?php if ( has_excerpt() ) { ?>
                                                    <div class="short-des"><?php the_excerpt(); ?></div>
                                                <?php } ?>
                                            </div>
                                            <div class="bottom-sub">
                                                <div class="content">
                                                    <?php the_content(); ?>
                                                </div>
                                                <div class="button-action"><?php do_action( 'woocommerce_after_shop_loop_item' ); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php $i++; endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>
        <?php
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Packages );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Packages );
}