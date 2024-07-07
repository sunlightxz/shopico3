<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Freeio_Project_Categories extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_project_categories';
    }

	public function get_title() {
        return esc_html__( 'Apus Project Categories', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Categories Banner', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Elementor\Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your title here', 'freeio' ),
            ]
        );

        $repeater->add_control(
            'des',
            [
                'label' => esc_html__( 'Description', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXTAREA,
                'placeholder' => esc_html__( 'Enter your description here', 'freeio' ),
            ]
        );

        $repeater->add_control(
            'slug',
            [
                'label' => esc_html__( 'Category Slug', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your Category Slug here', 'freeio' ),
            ]
        );

        $repeater->add_control(
            'custom_url',
            [
                'label' => esc_html__( 'Custom URL', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => esc_html__( 'Enter your custom url here', 'freeio' ),
            ]
        );

        $repeater->add_control(
            'image_icon',
            [
                'label' => esc_html__( 'Image or Icon', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'icon' => esc_html__('Icon', 'freeio'),
                    'image' => esc_html__('Image', 'freeio'),
                ),
                'default' => 'image'
            ]
        );

        $repeater->add_control(
            'category_icon',
            [
                'label' => esc_html__( 'Category Icon', 'freeio' ),
                'type' => Elementor\Controls_Manager::ICON,
                'default' => 'fa fa-star',
                'condition' => [
                    'image_icon' => 'icon',
                ],
            ]
        );

        $repeater->add_control(
            'img_bg_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Image', 'freeio' ),
                'type' => Elementor\Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Here', 'freeio' ),
                'condition' => [
                    'image_icon' => 'image',
                ],
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
            'categories',
            [
                'label' => esc_html__( 'Categories Box', 'freeio' ),
                'type' => Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'show_nb_projects',
            [
                'label' => esc_html__( 'Show Number Projects', 'freeio' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'freeio' ),
                'label_off' => esc_html__( 'Show', 'freeio' ),
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'style1' => esc_html__('Style 1', 'freeio'),
                    'style2' => esc_html__('Style 2', 'freeio'),
                    'style3' => esc_html__('Style 3', 'freeio'),
                    'style4' => esc_html__('Style 4', 'freeio'),
                ),
                'default' => 'style1'
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
                'default' => 'grid'
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
            'view_all',
            [
                'label' => esc_html__( 'View All', 'freeio' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'freeio' ),
                'label_off' => esc_html__( 'Show', 'freeio' ),
            ]
        );

        $this->add_control(
            'button_type',
            [
                'label' => esc_html__( 'Style Button', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'st1' => esc_html__('Style 1', 'freeio'),
                    'st2' => esc_html__('Style 2', 'freeio'),
                ),
                'default' => 'st1',
                'condition' => [
                    'view_all' => ['yes'],
                ]
            ]
        );

        $this->add_control(
            'text_view',
            [
                'label' => esc_html__( 'Text View All', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'Browse All',
                'condition' => [
                    'view_all' => ['yes'],
                ]
            ]
        );

        $this->add_control(
            'link_view',
            [
                'label' => esc_html__( 'View Link', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => esc_html__( 'Enter your Link here', 'freeio' ),
                'condition' => [
                    'view_all' => ['yes'],
                ]
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
            'section_overlay',
            [
                'label' => esc_html__( 'Style Box', 'freeio' ),
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
                    '{{WRAPPER}} .banner-image' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'style' => 'style2',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_box_style' );

            $this->start_controls_tab(
                'tab_box_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_box',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .category-banner-inner',
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_shadow',
                    'label' => esc_html__( 'Box Shadow', 'freeio' ),
                    'selector' => '{{WRAPPER}} .category-banner-inner',
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

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_box_hv',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .category-banner-inner:hover',
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_shadow_hv',
                    'label' => esc_html__( 'Box Shadow', 'freeio' ),
                    'selector' => '{{WRAPPER}} .category-banner-inner:hover',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab 

        $this->add_control(
            'heading_options',
            [
                'label' => esc_html__( 'Button', 'freeio' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
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
                        '{{WRAPPER}} .view_more .btn' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_header_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .view_more .btn' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button_button',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .view_more .btn',
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
                        '{{WRAPPER}} .view_more .btn:hover' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .view_more .btn:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_header_bg_color_hv',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .view_more .btn:hover' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .view_more .btn:focus' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button_header_hv',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .view_more .btn:hover',
                    'selector' => '{{WRAPPER}} .view_more .btn:focus',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab 

        $this->end_controls_section();


        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Typography', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Title Typography', 'freeio' ),
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .title',
            ]
        );

        $this->add_control(
            'number_color',
            [
                'label' => esc_html__( 'Number Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Number Typography', 'freeio' ),
                'name' => 'number_typography',
                'selector' => '{{WRAPPER}} .number',
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {
        $settings = $this->get_settings();

        extract( $settings );

        if ( !empty($categories) ) {
            if ( $image_size == 'custom' ) {
                
                if ( $image_custom_dimension['width'] && $image_custom_dimension['height'] ) {
                    $thumbsize = $image_custom_dimension['width'].'x'.$image_custom_dimension['height'];
                } else {
                    $thumbsize = 'full';
                }
            } else {
                $thumbsize = $image_size;
            }

            $columns = !empty($columns) ? $columns : 3;
            $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 2;
            $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
        ?>
            <div class="widget-project-categories <?php echo esc_attr($el_class); ?>">
                <?php if ( $title || !empty($des) || ( $view_all == 'yes' && !(empty($link_view)) && !(empty($text_view)) ) ) { ?>
                    <div class="top-widget-info d-md-flex align-items-end">
                        <div class="inner-left">
                            <?php if ( $title ) { ?>
                                <h2 class="widget-title"><?php echo esc_html($title); ?></h2>
                            <?php } ?>
                            <?php if ( !empty($des) ) { ?>
                                <div class="des"><?php echo trim($des); ?></div>
                            <?php } ?>
                        </div>
                        <?php if ( $view_all == 'yes' && !(empty($link_view)) && !(empty($text_view)) ) { ?>
                            <div class="view_more ms-auto">
                                <?php if( $button_type == 'st1' ) { ?>
                                    <a href="<?php echo esc_url( $link_view ); ?>" class="btn btn-view">
                                        <?php echo esc_html($text_view); ?><i class="flaticon-right-arrow-1 next"></i>
                                    </a>
                                <?php } else { ?>
                                    <a href="<?php echo esc_url( $link_view ); ?>" class="btn btn-small btn-theme-rgba10 radius-50">
                                        <?php echo esc_html($text_view); ?><i class="flaticon-right-up next"></i>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if ( $layout_type == 'carousel' ) {
                    
                    $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
                    $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
                    $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;
                ?>
                    <div class="slick-carousel <?php echo ( ( $columns >= count($categories))?'hidden-dots':'' ); ?>"
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

                        data-pagination="<?php echo esc_attr( $show_pagination ? 'true' : 'false' ); ?>" data-nav="<?php echo esc_attr( $show_nav ? 'true' : 'false' ); ?>" data-rows="<?php echo esc_attr( $rows ); ?>" data-infinite="<?php echo esc_attr( $infinite_loop ? 'true' : 'false' ); ?>" data-autoplay="<?php echo esc_attr( $slider_autoplay ? 'true' : 'false' ); ?>">

                        <?php foreach ($categories as $item) {
                            $term = get_term_by( 'slug', $item['slug'], 'project_category' );
                            $link = $item['custom_url'];
                            $title = $item['title'];
                            if ($term) {
                                if ( empty($link) ) {
                                    $link = get_term_link( $term, 'project_category' );
                                }
                                if ( empty($title) ) {
                                    $title = $term->name;
                                }
                            }

                            ?>
                            <div class="item">
                                <a class="category-banner-inner <?php echo esc_attr($style); ?>" href="<?php echo esc_url($link); ?>">
                                    
                                    <?php
                                    if ( !empty($item['img_bg_src']['id']) ) {
                                    ?>
                                        <div class="banner-image">
                                            <?php echo freeio_get_attachment_thumbnail($item['img_bg_src']['id'], $thumbsize); ?>
                                        </div>
                                    <?php } ?>

                                    <div class="inner">

                                        <?php if ( $show_nb_projects ) {
                                                $args = array(
                                                    'fields' => 'ids',
                                                    'category' => array($item['slug']),
                                                    'limit' => 1
                                                );
                                                $query = freeio_get_projects($args);
                                                $count = $query->found_posts;
                                                $number_projects = $count ? WP_Freeio_Mixes::format_number($count) : 0;
                                        ?>
                                        <div class="number"><?php echo sprintf(_n('<span>%d</span> Project', '<span>%d</span> Projects', $count, 'freeio'), $number_projects); ?></div>
                                        <?php } ?>

                                        <?php if ( !empty($title) ) { ?>
                                            <h4 class="title">
                                                <?php echo trim($title); ?>
                                            </h4>
                                        <?php } ?>
                                        
                                        <?php if ( !empty($des) ) { ?>
                                            <div class="des">
                                                <?php echo trim($des); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <?php
                            $mdcol = 12/$columns;
                            $smcol = 12/$columns_tablet;
                            $xscol = 12/$columns_mobile;
                        ?>
                        <?php foreach ($categories as $item) {

                            $term = get_term_by( 'slug', $item['slug'], 'project_category' );
                            $link = $item['custom_url'];
                            $title = $item['title'];
                            if ($term) {
                                if ( empty($link) ) {
                                    $link = get_term_link( $term, 'project_category' );
                                }
                                if ( empty($title) ) {
                                    $title = $term->name;
                                }
                            }

                            ?>
                            <div class="col-lg-<?php echo esc_attr($mdcol); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr( $xscol ); ?> list-item">
                                <a class="category-banner-inner <?php echo esc_attr($style); ?>" href="<?php echo esc_url($link); ?>">
                                    
                                    <?php
                                    if ( $item['image_icon'] == 'image' ) {
                                        if ( !empty($item['img_bg_src']['id']) ) {
                                        ?>
                                            <div class="banner-image image">
                                                <?php echo freeio_get_attachment_thumbnail($item['img_bg_src']['id'], $thumbsize); ?>
                                            </div>
                                        <?php } ?>
                                    <?php } else {
                                        if ( !empty($item['category_icon']) ) {
                                        ?>
                                            <div class="banner-image icon"><i class="<?php echo esc_attr($item['category_icon']); ?>"></i></div>
                                        <?php }
                                    } ?>

                                    <div class="inner">
                                        <?php if ( $show_nb_projects ) {
                                                $args = array(
                                                    'fields' => 'ids',
                                                    'category' => array($item['slug']),
                                                    'limit' => 1
                                                );
                                                $query = freeio_get_projects($args);
                                                $count = $query->found_posts;
                                                $number_projects = $count ? WP_Freeio_Mixes::format_number($count) : 0;
                                        ?>
                                        <div class="number"><?php echo sprintf(_n('<span>%d</span> Project', '<span>%d</span> Projects', $count, 'freeio'), $number_projects); ?></div>
                                        <?php } ?>
                                        <?php if ( !empty($title) ) { ?>
                                            <h4 class="title">
                                                <?php echo trim($title); ?>
                                            </h4>
                                        <?php } ?>
                                        <?php if ( !empty($des) ) { ?>
                                            <div class="des">
                                                <?php echo trim($des); ?>
                                            </div>
                                        <?php } ?>
                                        
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Freeio_Project_Categories );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Freeio_Project_Categories );
}