<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Popup_Video extends Widget_Base {

	public function get_name() {
        return 'apus_element_popup_video';
    }

	public function get_title() {
        return esc_html__( 'Apus Popup Video', 'freeio' );
    }

	public function get_icon() {
        return 'eicon-youtube';
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

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'video_link',
            [
                'label' => esc_html__( 'Youtube Video Link', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'url',
            ]
        );

        $this->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Image', 'freeio' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Image Here', 'freeio' ),
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'style1' => esc_html__('Style 1', 'freeio'),
                ),
                'default' => ''
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
            'section_title_style',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // tab normal and hover

        $this->start_controls_tabs( 'tabs_title_style' );

            $this->start_controls_tab(
                'tab_title_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            );

            $this->add_control(
                'color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .video-wrapper-inner .title' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_title_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_control(
                'hv-color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .video-wrapper-inner:hover .title' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->end_controls_section();

        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__( 'Icon', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

         // tab normal and hover

        $this->start_controls_tabs( 'tabs_box_style' );

            $this->start_controls_tab(
                'tab_icon_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            );

            $this->add_control(
                'iconcolor',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .video-wrapper-inner .video-icon' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'bg-color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .video-wrapper-inner .video-icon' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'br-color',
                [
                    'label' => esc_html__( 'Border Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .video-wrapper-inner .video-icon' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_icon_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_control(
                'hv-iconcolor',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .video-wrapper-inner:hover .video-icon' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'bg-hv-color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .video-wrapper-inner:hover .video-icon' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'br-hv-color',
                [
                    'label' => esc_html__( 'Border Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .video-wrapper-inner:hover .video-icon' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->end_controls_section();

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        ?>
        <div class="widget-video <?php echo esc_attr($el_class);?>">
            <div class="video-wrapper-inner position-relative <?php echo esc_attr($style); ?>">
                <?php
                if ( !empty($img_src['id']) ) {
                ?>
                    <?php echo freeio_get_attachment_thumbnail($img_src['id'], 'full'); ?>
                <?php } ?>
                <a class="popup-video d-flex align-items-center" href="<?php echo esc_url($video_link); ?>">
                    <span class="popup-video-inner flex-shrink-0">
                        <div class="d-flex align-items-center justify-content-center video-icon">
                            <i class="fa fa-play"></i>
                        </div>
                    </span>
                    <?php if ( !empty($title) ) { ?>
                        <div class="flex-grow-1">
                            <h2 class="title">
                                <?php echo esc_html($title); ?>
                            </h2>
                        </div>
                    <?php } ?>
                </a>
            </div>
        </div>
        <?php
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Popup_Video );
} else {
    Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Popup_Video );
}