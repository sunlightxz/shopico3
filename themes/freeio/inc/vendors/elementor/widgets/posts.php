<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Posts extends Elementor\Widget_Base {

    public function get_name() {
        return 'apus_element_posts';
    }

    public function get_title() {
        return esc_html__( 'Apus Posts', 'freeio' );
    }
    
    public function get_categories() {
        return [ 'freeio-elements' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Posts', 'freeio' ),
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
            'number',
            [
                'label' => esc_html__( 'Number', 'freeio' ),
                'type' => Elementor\Controls_Manager::NUMBER,
                'input_type' => 'number',
                'description' => esc_html__( 'Number posts to display', 'freeio' ),
                'default' => 4
            ]
        );
        
        $this->add_control(
            'order_by',
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
            'item_style',
            [
                'label' => esc_html__( 'Item Style', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'inner-grid' => esc_html__('Grid Default', 'freeio'),
                    'inner-grid-v1' => esc_html__('Grid 1', 'freeio'),
                    'inner-grid-v2' => esc_html__('Grid 2', 'freeio'),
                    'inner-grid-v3' => esc_html__('Grid 3', 'freeio'),
                ),
                'default' => 'inner-grid',
                'condition' => [
                    'layout_type' => ['grid', 'carousel'],
                ]
            ]
        );
        

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'carousel' => esc_html__('Carousel', 'freeio'),
                    'grid' => esc_html__('Grid', 'freeio'),
                    'list' => esc_html__('List', 'freeio'),
                    'special' => esc_html__('Special', 'freeio'),
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
                'condition' => [
                    'layout_type' => ['grid', 'carousel'],
                ],
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
                'default' => 3,
            ]
        );

        $this->add_control(
            'show_nav',
            [
                'label' => esc_html__( 'Show Nav', 'freeio' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'freeio' ),
                'label_off' => esc_html__( 'Show', 'freeio' ),
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__( 'Show Pagination', 'freeio' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'freeio' ),
                'label_off' => esc_html__( 'Show', 'freeio' ),
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
            'text_view',
            [
                'label' => esc_html__( 'Text View All', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'Browse All News',
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

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
                'default' => 'large',
                'separator' => 'none',
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'label' => esc_html__( 'Image First', 'freeio' ),   
                'name' => 'image_special', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
                'default' => 'large',
                'condition' => [
                    'layout_type' => 'special',
                ],
                'separator' => 'before',
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
            'section_title_style',
            [
                'label' => esc_html__( 'Tyles', 'freeio' ),
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
                    '{{WRAPPER}} .widget-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Title Typography', 'freeio' ),
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .widget-title',
            ]
        );

        $this->add_control(
            'post_title_color',
            [
                'label' => esc_html__( 'Post Title Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .post .title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Post Title Typography', 'freeio' ),
                'name' => 'post_title_typography',
                'selector' => '{{WRAPPER}} .post .title a',
            ]
        );

        $this->add_control(
            'post_excerpt_color',
            [
                'label' => esc_html__( 'Post Excerpt Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .post .description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Post Excerpt Typography', 'freeio' ),
                'name' => 'post_excerpt_typography',
                'selector' => '{{WRAPPER}} .post .description',
            ]
        );

        $this->add_control(
            'post_tag_color',
            [
                'label' => esc_html__( 'Post Tag Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .post .tags' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Post Tag Typography', 'freeio' ),
                'name' => 'post_tag_typography',
                'selector' => '{{WRAPPER}} .post .tags',
            ]
        );

        $this->add_control(
            'post_readmore_color',
            [
                'label' => esc_html__( 'Post Read More Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .post .readmore' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Post Read More Typography', 'freeio' ),
                'name' => 'post_readmore_typography',
                'selector' => '{{WRAPPER}} .post .readmore',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $number,
            'orderby' => $order_by,
            'order' => $order,
        );
        $loop = new WP_Query($args);
        if ( $loop->have_posts() ) {
            if ( $image_size == 'custom' ) {
                
                if ( $image_custom_dimension['width'] && $image_custom_dimension['height'] ) {
                    $thumbsize = $image_custom_dimension['width'].'x'.$image_custom_dimension['height'];
                } else {
                    $thumbsize = 'full';
                }
            } else {
                $thumbsize = $image_size;
            }
            
            if ( $image_special_size == 'custom' ) {
                if ( $image_special_custom_dimension['width'] && $image_special_custom_dimension['height'] ) {
                    $special_thumbsize = $image_special_custom_dimension['width'].'x'.$image_special_custom_dimension['height'];
                } else {
                    $special_thumbsize = 'full';
                }
            } else {
                $special_thumbsize = $image_special_size;
            }

            set_query_var( 'thumbsize', $thumbsize );

            $columns = !empty($columns) ? $columns : 3;
            $columns_tablet_extra = !empty($settings['columns_tablet_extra']) ? $settings['columns_tablet_extra'] : $columns;
            $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 2;
            $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
            
            $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : $columns;
            $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $slides_to_scroll;
            $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : 1;

            ?>
            <div class="widget-blogs widget m-0 <?php echo esc_attr($el_class.' '.$layout_type); ?>">
                <?php if ( $title ) { ?>
                    <h2 class="widget-title"><?php echo esc_html($title); ?></h2>
                <?php } ?>
                <div class="widget-content">

                    <?php if ( $layout_type == 'carousel' ): ?>
                        <div class="inner-carousel">
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

                                data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>">
                                <?php while ( $loop->have_posts() ): $loop->the_post(); ?>
                                    <div class="item">
                                        <?php get_template_part( 'template-posts/loop/'.$item_style); ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php elseif ( $layout_type == 'grid' ): ?>
                        <div class="layout-blog">
                            <div class="row">
                                <?php
                                    $mdcol = 12/$columns;
                                    $columns_tablet_extra = 12/$columns_tablet_extra;
                                    $smcol = 12/$columns_tablet;
                                    $xscol = 12/$columns_mobile;
                                    while ( $loop->have_posts() ) : $loop->the_post();
                                ?>
                                    <div class="col-xl-<?php echo esc_attr($mdcol); ?> col-tablet-extra-<?php echo esc_attr($columns_tablet_extra); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr($xscol); ?>">
                                        <?php get_template_part( 'template-posts/loop/'.$item_style ); ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php elseif( $layout_type == 'special'): $count = 1; ?>
                        <div class="row d-block clearfix">
                            <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                <?php if($count == 1){ ?>
                                    <?php set_query_var( 'thumbsize', $special_thumbsize ); ?>
                                    <div class="col-12 col-lg-7 float-start main-special-blog">
                                        <?php get_template_part( 'template-posts/loop/inner-grid-v4' ); ?>
                                    </div>
                                <?php }else{ ?>
                                    <?php set_query_var( 'thumbsize', $thumbsize ); ?>
                                    <div class="col-12 col-lg-5 float-start">
                                        <?php get_template_part( 'template-posts/loop/inner-list-v3' ); ?>
                                    </div>
                                <?php } ?>
                            <?php  $count++; endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="layout-blog">
                            <div class="row">
                                <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <?php get_template_part( 'template-posts/loop/inner-list' ); ?>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
                <?php if ( $view_all == 'yes' && !(empty($link_view)) && !(empty($text_view)) ) { ?>
                    <div class="bottom-info text-center">
                        <a href="<?php echo esc_url( $link_view ); ?>" class="btn btn-theme-second">
                            <?php echo esc_html($text_view); ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Posts );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Posts );
}