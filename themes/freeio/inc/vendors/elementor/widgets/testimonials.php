<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Testimonials extends Widget_Base {

    public function get_name() {
        return 'apus_element_testimonials';
    }

    public function get_title() {
        return esc_html__( 'Apus Testimonials', 'freeio' );
    }

    public function get_icon() {
        return 'eicon-testimonial';
    }

    public function get_categories() {
        return [ 'freeio-elements' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Choose Image', 'freeio' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Brand Image', 'freeio' ),
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label' => esc_html__( 'Name', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );


        $repeater->add_control(
            'content', [
                'label' => esc_html__( 'Content', 'freeio' ),
                'type' => Controls_Manager::TEXTAREA
            ]
        );

        $repeater->add_control(
            'job',
            [
                'label' => esc_html__( 'Job', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'star',
            [
                'label' => esc_html__( 'Star Scores', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link To', 'freeio' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'Enter your social link here', 'freeio' ),
                'placeholder' => esc_html__( 'https://your-link.com', 'freeio' ),
            ]
        );

        $this->add_control(
            'testimonials',
            [
                'label' => esc_html__( 'Testimonials', 'freeio' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );

        $columns = range( 1, 12 );
        $columns = array_combine( $columns, $columns );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => $columns,
                'frontend_available' => true,
                'default' => 1,
            ]
        );

        $this->add_control(
            'show_nav',
            [
                'label' => esc_html__( 'Show Nav', 'freeio' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'freeio' ),
                'label_off' => esc_html__( 'Show', 'freeio' ),
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => esc_html__( 'Show Pagination', 'freeio' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'freeio' ),
                'label_off' => esc_html__( 'Show', 'freeio' ),
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'style1' => esc_html__('Style 1', 'freeio'),
                    'style2' => esc_html__('Style 2', 'freeio'),
                    'style3' => esc_html__('Style 3', 'freeio'),
                    'style4' => esc_html__('Style 4', 'freeio'),
                    'style5' => esc_html__('Style 5', 'freeio'),
                    'style6' => esc_html__('Style 6', 'freeio'),
                ),
                'default' => 'style1'
            ]
        );

        $this->add_control(
            'fullscreen',
            [
                'label'         => esc_html__( 'Full Screen', 'freeio' ),
                'type'          => Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'freeio' ),
                'label_off'     => esc_html__( 'No', 'freeio' ),
                'return_value'  => true,
                'default'       => false,
            ]
        );


        $this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'freeio' ),
                'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'freeio' ),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_box_style',
            [
                'label' => esc_html__( 'Style Box', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_box',
                'label' => esc_html__( 'Border Box', 'freeio' ),
                'selector' => '{{WRAPPER}} [class*="testimonials-item"]',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__( 'Style Info', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .title' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Title Typography', 'freeio' ),
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .title',
            ]
        );

        $this->add_control(
            'test_title_color',
            [
                'label' => esc_html__( 'Name Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .name-client' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .name-client a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Name Typography', 'freeio' ),
                'name' => 'test_title_typography',
                'selector' => '{{WRAPPER}} .name-client',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__( 'Content Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Content Typography', 'freeio' ),
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .description',
            ]
        );

        $this->add_control(
            'listing_color',
            [
                'label' => esc_html__( 'Listing Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .job' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Listing Typography', 'freeio' ),
                'name' => 'listing_typography',
                'selector' => '{{WRAPPER}} .job',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_img',
                'label' => esc_html__( 'Border Image Active', 'freeio' ),
                'selector' => '{{WRAPPER}} .slick-current .avarta',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_dots_style',
            [
                'label' => esc_html__( 'Style Dots', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'dots_color',
            [
                'label' => esc_html__( 'Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-carousel .slick-dots li button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'dots_active_color',
            [
                'label' => esc_html__( 'Color Active', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .slick-carousel .slick-dots .slick-active button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        $columns = !empty($columns) ? $columns : 1;
        $columns_tablet_extra = !empty($settings['columns_tablet_extra']) ? $settings['columns_tablet_extra'] : $columns;
        $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 1;
        $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
        
        if ( !empty($testimonials) ) {
            ?>
            <div class="widget-testimonials <?php echo esc_attr($el_class.' '.$layout_type); ?> <?php echo esc_attr($show_nav ? 'show_nav' : ''); ?> <?php echo esc_attr( $fullscreen ? 'fullscreen' : 'nofullscreen' ); ?>">
                <?php if($layout_type == 'style2') { ?>

                    <div class="slick-carousel v1 testimonial-main" data-items="1" data-large="1" data-medium="1" data-small="1" data-smallest="1" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-thumbnail" data-slickparent="true">
                        <?php foreach ($testimonials as $item) { ?>
                            <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                            
                            <div class="testimonials-item2 text-center">
                                <?php if ( !empty($item['content']) ) { ?>
                                    <div class="description"><?php echo trim($item['content']); ?></div>
                                <?php } ?>
                                <?php if ( !empty($item['name']) ) {
                                    $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                    if ( ! empty( $item['link']['url'] ) ) {
                                        $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                    }
                                    echo trim($title);
                                ?>
                                <?php } ?>
                                <?php if ( !empty($item['job']) ) { ?>
                                    <div class="job"><?php echo esc_html($item['job']); ?></div>
                                <?php } ?>

                            </div>
                        <?php } ?>
                    </div>

                    <div class="wrapper-testimonial-thumbnail">
                        <div class="slick-carousel testimonial-thumbnail" data-centerpadding='0px' data-centermode="true" data-items="5" data-large="5" data-medium="5" data-small="3" data-smallest="3" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-main" data-slidestoscroll="1" data-focusonselect="true" data-infinite="true">
                            <?php foreach ($testimonials as $item) { ?>
                                <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                                <?php if ( $img_src ) { ?>
                                    <div class="wrapper-avarta">
                                        <div class="avarta">
                                            <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } elseif($layout_type == 'style6' ) { ?>
                    
                    <div class="d-flex row style6-testimonial">
                        <div class="wrapper-testimonial-thumbnail style6 col-lg-3 col-12">
                            <div class="slick-carousel testimonial-thumbnail" data-centerpadding='0px' data-centermode="true" data-items="3" data-large="3" data-medium="3" data-small="3" data-smallest="3" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-main" data-slidestoscroll="1" data-focusonselect="true" data-infinite="true">
                                <?php foreach ($testimonials as $item) { ?>
                                    <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                                    <?php if ( $img_src ) { ?>
                                        <div class="wrapper-avarta">
                                            <div class="avarta">
                                                <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-lg-9 col-12">
                            <div class="slick-carousel testimonial-main pl-80" data-items="1" data-large="1" data-medium="1" data-small="1" data-smallest="1" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-thumbnail" data-slickparent="true">
                                <?php foreach ($testimonials as $item) { ?>
                                    <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                                    
                                    <div class="testimonials-item6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="top-icon" width="60" height="43" viewBox="0 0 60 43" fill="none">
                                        <path d="M14.837 42.0652C11.0326 42.0652 7.6087 40.5435 4.56522 37.5C1.52174 34.3478 0 29.7283 0 23.6413C0 16.6848 1.84783 11.0326 5.54348 6.68478C9.34783 2.22825 14.7283 0 21.6848 0C24.1848 0 26.1413 0.163038 27.5543 0.489121V7.98912C26.0326 7.77173 24.0761 7.66304 21.6848 7.66304C17.9891 7.66304 15 8.91304 12.7174 11.413C10.5435 13.5869 9.29348 16.4674 8.96739 20.0543C10.3804 18.3152 12.663 17.4456 15.8152 17.4456C19.0761 17.4456 21.8478 18.587 24.1304 20.8696C26.413 23.0435 27.5543 25.9239 27.5543 29.5109C27.5543 33.2065 26.3587 36.25 23.9674 38.6413C21.5761 40.9239 18.5326 42.0652 14.837 42.0652ZM47.2826 42.0652C43.4783 42.0652 40.0543 40.5435 37.0109 37.5C33.9674 34.3478 32.4456 29.7283 32.4456 23.6413C32.4456 16.6848 34.2935 11.0326 37.9891 6.68478C41.7935 2.22825 47.1739 0 54.1304 0C56.6304 0 58.587 0.163038 60 0.489121V7.98912C58.4783 7.77173 56.5217 7.66304 54.1304 7.66304C50.4348 7.66304 47.4456 8.91304 45.163 11.413C42.9891 13.5869 41.7391 16.4674 41.413 20.0543C42.8261 18.3152 45.1087 17.4456 48.2609 17.4456C51.5217 17.4456 54.2935 18.587 56.5761 20.8696C58.8587 23.0435 60 25.9239 60 29.5109C60 33.2065 58.8043 36.25 56.413 38.6413C54.0217 40.9239 50.9783 42.0652 47.2826 42.0652Z" fill="#F0EFEC"/>
                                        </svg>
                                        <?php if ( !empty($item['content']) ) { ?>
                                            <div class="description"><?php echo trim($item['content']); ?></div>
                                        <?php } ?>
                                        <?php if ( !empty($item['name']) ) {
                                            $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                            if ( ! empty( $item['link']['url'] ) ) {
                                                $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                            }
                                            echo trim($title);
                                        ?>
                                        <?php } ?>
                                        <?php if ( !empty($item['job']) ) { ?>
                                            <div class="job"><?php echo esc_html($item['job']); ?></div>
                                        <?php } ?>

                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } elseif($layout_type == 'style1' || $layout_type == 'style5' ) { ?>

                    <div class="slick-carousel testimonial-main <?php echo trim( ($columns >= count($testimonials))?'hidden-dots':'' ); ?>" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr( $columns_tablet_extra ); ?>" data-medium="<?php echo esc_attr( $columns_tablet ); ?>" data-small="<?php echo esc_attr($columns_mobile); ?>" data-smallest="<?php echo esc_attr($columns_mobile); ?>" data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>" data-infinite="true">
                        <?php foreach ($testimonials as $item) { ?>
                        <div class="item">

                            <div class="testimonials-item <?php echo esc_attr($layout_type); ?>">
                                <?php if($layout_type == 'style5'){ ?>
                                    <div class="d-flex align-items-center">
                                        <?php if ( !empty($item['title']) ) { ?>
                                            <h3 class="title text-theme flex-grow-1"><?php echo esc_html($item['title']); ?></h3>
                                        <?php } ?>
                                        <div class="ms-auto">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="37" height="26" viewBox="0 0 37 26" fill="none">
                                            <path d="M9.1 25.8C6.76667 25.8 4.66667 24.8667 2.8 23C0.933334 21.0667 0 18.2333 0 14.5C0 10.2333 1.13333 6.76666 3.4 4.1C5.73333 1.36667 9.03333 0 13.3 0C14.8333 0 16.0333 0.099998 16.9 0.299995V4.9C15.9667 4.76666 14.7667 4.7 13.3 4.7C11.0333 4.7 9.2 5.46666 7.8 7C6.46667 8.33333 5.7 10.1 5.5 12.3C6.36667 11.2333 7.76667 10.7 9.7 10.7C11.7 10.7 13.4 11.4 14.8 12.8C16.2 14.1333 16.9 15.9 16.9 18.1C16.9 20.3667 16.1667 22.2333 14.7 23.7C13.2333 25.1 11.3667 25.8 9.1 25.8ZM29 25.8C26.6667 25.8 24.5667 24.8667 22.7 23C20.8333 21.0667 19.9 18.2333 19.9 14.5C19.9 10.2333 21.0333 6.76666 23.3 4.1C25.6333 1.36667 28.9333 0 33.2 0C34.7333 0 35.9333 0.099998 36.8 0.299995V4.9C35.8667 4.76666 34.6667 4.7 33.2 4.7C30.9333 4.7 29.1 5.46666 27.7 7C26.3667 8.33333 25.6 10.1 25.4 12.3C26.2667 11.2333 27.6667 10.7 29.6 10.7C31.6 10.7 33.3 11.4 34.7 12.8C36.1 14.1333 36.8 15.9 36.8 18.1C36.8 20.3667 36.0667 22.2333 34.6 23.7C33.1333 25.1 31.2667 25.8 29 25.8Z" fill="#fff"/>
                                            </svg>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <?php if ( !empty($item['title']) ) { ?>
                                        <h3 class="title text-theme flex-grow-1"><?php echo esc_html($item['title']); ?></h3>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ( !empty($item['content']) ) { ?>
                                    <div class="description"><?php echo trim($item['content']); ?></div>
                                <?php } ?>
                                <div class="inner-bottom">
                                    <div class="d-flex align-items-center">

                                        <?php if ( isset( $item['img_src']['id'] ) ) { ?>
                                        <div class="wrapper-avarta">
                                            <div class="avarta d-flex justify-content-center align-items-center">
                                                <?php echo freeio_get_attachment_thumbnail($item['img_src']['id'], 'full'); ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        
                                        <div class="info-testimonials">
                                            <?php if ( !empty($item['name']) ) {

                                                $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                                }
                                                echo trim($title);
                                            ?>
                                            <?php } ?>

                                            <?php if ( !empty($item['job']) ) { ?>
                                                <div class="job"><?php echo esc_html($item['job']); ?></div>
                                            <?php } ?>
                                            <?php if ( !empty($item['star']) ) { ?>
                                                <div class="star d-flex align-items-center">
                                                    <span class="text"><?php echo number_format($item['star'], 1, '.', ''); ?> </span>
                                                    <div class="inner">
                                                        <div class="w-percent" style="width: <?php echo trim($item['star']*20)?>%;"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <?php } ?>
                    </div>
                <?php } elseif($layout_type == 'style3' ) { ?>

                    <div class="slick-carousel testimonial-main <?php echo trim( ($columns >= count($testimonials))?'hidden-dots':'' ); ?>" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr( $columns_tablet_extra ); ?>" data-medium="<?php echo esc_attr( $columns_tablet ); ?>" data-small="<?php echo esc_attr($columns_mobile); ?>" data-smallest="<?php echo esc_attr($columns_mobile); ?>" data-pagination="<?php echo esc_attr($show_pagination ? 'true' : 'false'); ?>" data-nav="<?php echo esc_attr($show_nav ? 'true' : 'false'); ?>" data-infinite="true">
                        <?php foreach ($testimonials as $item) { ?>
                        <div class="item">

                            <div class="testimonials-item3">
                                <div class="d-flex align-items-center">
                                    <?php if ( isset( $item['img_src']['id'] ) && !empty($item['img_src']['id']) ) { ?>
                                        <div class="flex-shrink-0">
                                            <div class="wrapper-avarta position-relative">
                                                <div class="avarta d-flex justify-content-center align-items-center">
                                                    <?php echo freeio_get_attachment_thumbnail($item['img_src']['id'], 'full'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="info-testimonials flex-grow-1">
                                        <?php if ( !empty($item['star']) ) { ?>
                                            <div class="star d-flex align-items-center">
                                                <span class="text"><?php echo number_format($item['star'], 1, '.', ''); ?> </span>
                                                <div class="inner">
                                                    <div class="w-percent" style="width: <?php echo trim($item['star']*20)?>%;"></div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                        <?php if ( !empty($item['title']) ) { ?>
                                            <h3 class="title"><?php echo esc_html($item['title']); ?></h3>
                                        <?php } ?>
                                        <?php if ( !empty($item['content']) ) { ?>
                                            <div class="description"><?php echo trim($item['content']); ?></div>
                                        <?php } ?>
                                        <?php if ( !empty($item['name']) ) {
                                            $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                            if ( ! empty( $item['link']['url'] ) ) {
                                                $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                            }
                                            echo trim($title);
                                        ?>
                                        <?php } ?>
                                        <?php if ( !empty($item['job']) ) { ?>
                                            <div class="job"><?php echo esc_html($item['job']); ?></div>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                <?php } elseif($layout_type == 'style4' ) { ?>

                    <div class="slick-carousel testimonial-main" data-items="1" data-large="1" data-medium="1" data-small="1" data-smallest="1" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-thumbnail" data-slickparent="true">
                        <?php foreach ($testimonials as $item) { ?>
                            <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                            
                            <div class="testimonials-item4 text-center">
                                <?php if ( !empty($item['content']) ) { ?>
                                    <div class="description"><?php echo trim($item['content']); ?></div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="wrapper-testimonial-thumbnail4">
                        <div class="slick-carousel testimonial-thumbnail" data-centerpadding='0px' data-centermode="true" data-items="3" data-large="3" data-medium="3" data-small="3" data-smallest="1" data-pagination="false" data-nav="false" data-asnavfor=".testimonial-main" data-slidestoscroll="1" data-focusonselect="true" data-infinite="true">
                            <?php foreach ($testimonials as $item) { ?>
                                <div class="item">
                                    <div class="item-testimonials4 d-flex align-items-center justify-content-center">
                                        <?php $img_src = ( isset( $item['img_src']['id'] ) && $item['img_src']['id'] != 0 ) ? wp_get_attachment_url( $item['img_src']['id'] ) : ''; ?>
                                        <?php if ( $img_src ) { ?>
                                            <div class="wrapper-avarta flex-shrink-0">
                                                <div class="avarta">
                                                    <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr(!empty($item['name']) ? $item['name'] : ''); ?>">
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="inner-right flex-grow-1">
                                            <?php if ( !empty($item['name']) ) {
                                                $title = '<h3 class="name-client">'.$item['name'].'</h3>';
                                                if ( ! empty( $item['link']['url'] ) ) {
                                                    $title = sprintf( '<h3 class="name-client"><a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>%1$s</a></h3>', $item['name'] );
                                                }
                                                echo trim($title);
                                            ?>
                                            <?php } ?>
                                            <?php if ( !empty($item['job']) ) { ?>
                                                <div class="job"><?php echo esc_html($item['job']); ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                <?php } ?>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Testimonials );
} else {
    Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Testimonials );
}