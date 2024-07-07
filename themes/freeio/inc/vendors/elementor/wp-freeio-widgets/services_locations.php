<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Freeio_Service_Locations extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_locations';
    }

	public function get_title() {
        return esc_html__( 'Apus Service Locations', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Locations Banner', 'freeio' ),
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
            'slug',
            [
                'label' => esc_html__( 'Location Slug', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your Location Slug here', 'freeio' ),
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
            'img_bg_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Image', 'freeio' ),
                'type' => Elementor\Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Here', 'freeio' ),
            ]
        );

        $this->add_control(
            'locations',
            [
                'label' => esc_html__( 'Locations Box', 'freeio' ),
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
            'show_nb_services',
            [
                'label' => esc_html__( 'Show Number Services', 'freeio' ),
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
                'label' => esc_html__( 'Background Overlay', 'freeio' ),
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
                    'style' => 'style1',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style' );

            $this->start_controls_tab(
                'tab_bg_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'background_overlay',
                    'selector' => '{{WRAPPER}} .type-banner-inner .banner-image::before',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_bg_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Background::get_type(),
                [
                    'name' => 'background_hvoer_overlay',
                    'selector' => '{{WRAPPER}} .type-banner-inner:hover .banner-image::before',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();

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

        if ( !empty($locations) ) {
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
            <div class="widget-service-locations <?php echo esc_attr($el_class); ?>">
                <?php if ( $layout_type == 'carousel' ) {
                    
                    $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
                    $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
                    $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;
                ?>
                    <div class="slick-carousel <?php echo ( ( $columns >= count($locations))?'hidden-dots':'' ); ?>"
                        data-items="<?php echo esc_attr($columns); ?>"
                        data-smallmedium="<?php echo esc_attr( $columns_tablet ); ?>"
                        data-extrasmall="<?php echo esc_attr($columns_mobile); ?>"

                        data-slidestoscroll="<?php echo esc_attr($slides_to_scroll); ?>"
                        data-slidestoscroll_smallmedium="<?php echo esc_attr( $slides_to_scroll_tablet ); ?>"
                        data-slidestoscroll_extrasmall="<?php echo esc_attr($slides_to_scroll_mobile); ?>"

                        data-pagination="<?php echo esc_attr( $show_pagination ? 'true' : 'false' ); ?>" data-nav="<?php echo esc_attr( $show_nav ? 'true' : 'false' ); ?>" data-rows="<?php echo esc_attr( $rows ); ?>" data-infinite="<?php echo esc_attr( $infinite_loop ? 'true' : 'false' ); ?>" data-autoplay="<?php echo esc_attr( $slider_autoplay ? 'true' : 'false' ); ?>">

                        <?php foreach ($locations as $item) {
                            $term = get_term_by( 'slug', $item['slug'], 'location' );
                            $link = $item['custom_url'];
                            $title = $item['title'];
                            if ($term) {
                                if ( empty($link) ) {
                                    $link = get_term_link( $term, 'location' );
                                }
                                if ( empty($title) ) {
                                    $title = $term->name;
                                }
                            }

                            ?>
                            <div class="item">
                                <a class="type-banner-inner <?php echo esc_attr($style); ?>" href="<?php echo esc_url($link); ?>">
                                    
                                    <?php
                                    if ( !empty($item['img_bg_src']['id']) ) {
                                    ?>
                                        <div class="banner-image">
                                            <?php echo freeio_get_attachment_thumbnail($item['img_bg_src']['id'], $thumbsize); ?>
                                        </div>
                                    <?php } ?>

                                    <div class="inner">
                                        <div class="info-city">
                                            
                                            <?php if ( !empty($title) ) { ?>
                                                <h4 class="title">
                                                    <?php echo trim($title); ?>
                                                </h4>
                                            <?php } ?>
                                            <?php if ( $show_nb_services ) {
                                                    $args = array(
                                                        'fields' => 'ids',
                                                        'location' => array($item['slug']),
                                                        'limit' => 1
                                                    );
                                                    $query = freeio_get_services($args);
                                                    $count = $query->found_posts;
                                                    $number_services = $count ? WP_Freeio_Mixes::format_number($count) : 0;
                                            ?>
                                            <div class="number"><?php echo sprintf(_n('<span>%d</span> Service', '<span>%d</span> Services', $count, 'freeio'), $number_services); ?></div>
                                            <?php } ?>
                                        </div>
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
                        <?php $i=1; foreach ($locations as $item) {
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

                            $term = get_term_by( 'slug', $item['slug'], 'location' );
                            $link = $item['custom_url'];
                            $title = $item['title'];
                            if ($term) {
                                if ( empty($link) ) {
                                    $link = get_term_link( $term, 'location' );
                                }
                                if ( empty($title) ) {
                                    $title = $term->name;
                                }
                            }

                            ?>
                            <div class="col-md-<?php echo esc_attr($mdcol); ?> col-sm-<?php echo esc_attr($smcol); ?> col-xs-<?php echo esc_attr( $xscol ); ?> list-item <?php echo esc_attr($classes); ?>">
                                <a class="type-banner-inner <?php echo esc_attr($style); ?>" href="<?php echo esc_url($link); ?>">
                                    
                                    <?php
                                    if ( !empty($item['img_bg_src']['id']) ) {
                                    ?>
                                        <div class="banner-image">
                                            <?php echo freeio_get_attachment_thumbnail($item['img_bg_src']['id'], $thumbsize); ?>
                                        </div>
                                    <?php } ?>

                                    <div class="inner">
                                        <div class="info-city">
                                            
                                            <?php if ( !empty($title) ) { ?>
                                                <h4 class="title">
                                                    <?php echo trim($title); ?>
                                                </h4>
                                            <?php } ?>
                                            <?php if ( $show_nb_services ) {
                                                    $args = array(
                                                        'fields' => 'ids',
                                                        'location' => array($item['slug']),
                                                        'limit' => 1
                                                    );
                                                    $query = freeio_get_services($args);
                                                    $count = $query->found_posts;
                                                    $number_services = $count ? WP_Freeio_Mixes::format_number($count) : 0;
                                            ?>
                                            <div class="number"><?php echo sprintf(_n('<span>%d</span> Service', '<span>%d</span> Services', $count, 'freeio'), $number_services); ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php $i++; } ?>
                    </div>
                <?php } ?>
            </div>
        <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Freeio_Service_Locations );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Freeio_Service_Locations );
}