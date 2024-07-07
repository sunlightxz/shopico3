<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Features_Box extends Widget_Base {

	public function get_name() {
        return 'apus_element_features_box';
    }

	public function get_title() {
        return esc_html__( 'Apus Features Box', 'freeio' );
    }

	public function get_icon() {
        return 'eicon-image-box';
    }

	public function get_categories() {
        return [ 'freeio-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Features Box', 'freeio' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'image_icon',
            [
                'label' => esc_html__( 'Image or Icon', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'icon' => esc_html__('Icon', 'freeio'),
                    'image' => esc_html__('Image', 'freeio'),
                ),
                'default' => 'image'
            ]
        );

        $repeater->add_control(
            'icon',
            [
                'label' => esc_html__( 'Icon', 'freeio' ),
                'type' => Controls_Manager::ICON,
                'default' => 'fa fa-star',
                'condition' => [
                    'image_icon' => 'icon',
                ],
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__( 'Choose Image', 'freeio' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'image_icon' => 'image',
                ],
            ]
        );

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
                'condition' => [
                    'image_icon' => 'image',
                ],
            ]
        );
        $repeater->add_control(
            'title_text',
            [
                'label' => esc_html__( 'Title & Description', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'This is the heading', 'freeio' ),
                'placeholder' => esc_html__( 'Enter your title', 'freeio' ),
            ]
        );

        $repeater->add_control(
            'description_text',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'Description', 'freeio' ),
                'placeholder' => esc_html__( 'Enter your description', 'freeio' ),
                'separator' => 'none',
                'rows' => 10,
                'show_label' => false,
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link to', 'freeio' ),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'freeio' ),
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'features',
            [
                'label' => esc_html__( 'Features Box', 'freeio' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
            ]
        );
        
        $this->add_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'number',
                'default' => '3'
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'style1' => esc_html__('Style 1', 'freeio'),
                    'style2' => esc_html__('Style 2', 'freeio'),
                    'style3' => esc_html__('Style 3', 'freeio'),
                    'style4' => esc_html__('Style 4', 'freeio'),
                    'style5' => esc_html__('Style 5', 'freeio'),
                ),
                'default' => 'style1'
            ]
        );

        $this->add_control(
            'show_circle',
            [
                'label' => esc_html__( 'Show Sub Circle', 'freeio' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'freeio' ),
                'label_off' => esc_html__( 'Show', 'freeio' ),
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
                'label' => esc_html__( 'Box Style', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // tab normal and hover

        $this->start_controls_tabs( 'tabs_box_style' );

            $this->start_controls_tab(
                'tab_box_normal',
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
                        // Stronger selector to avoid section style from overwriting
                        '{{WRAPPER}} .item-features-inner' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'box_color',
                    'selector' => '{{WRAPPER}} .item-features-inner',
                ]
            );

            $this->add_responsive_control(
                'padding-box',
                [
                    'label' => esc_html__( 'Padding', 'freeio' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em' ],
                    'selectors' => [
                        '{{WRAPPER}} .item-features-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'border_box',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .item-features-inner',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_shadow',
                    'label' => esc_html__( 'Box Shadow', 'freeio' ),
                    'selector' => '{{WRAPPER}} .item-features-inner',
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
                'color_hover',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        // Stronger selector to avoid section style from overwriting
                        '{{WRAPPER}} .item-features-inner:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'box_color_hover',
                    'selector' => '{{WRAPPER}} .item-features-inner:hover',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'border_hv_box',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .item-features-inner:hover',
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'box_hv_shadow',
                    'label' => esc_html__( 'Box Shadow', 'freeio' ),
                    'selector' => '{{WRAPPER}} .item-features-inner:hover',
                ]
            );

            $this->end_controls_tab();

            $this->add_control(
                'border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'freeio' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%' ],
                    'selectors' => [
                        '{{WRAPPER}} .item-features-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->end_controls_section();


        $this->start_controls_section(
            'section_heading_style',
            [
                'label' => esc_html__( 'Information Style', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Heading Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'title_color_hover',
            [
                'label' => esc_html__( 'Heading Color Hover', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-features-inner:hover .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Heading Typography', 'freeio' ),
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .title',
            ]
        );


        $this->add_control(
            'des_color',
            [
                'label' => esc_html__( 'Description Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .description' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'des_color_hover',
            [
                'label' => esc_html__( 'Description Color Hover', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-features-inner:hover .description' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Description Typography', 'freeio' ),
                'name' => 'des_typography',
                'selector' => '{{WRAPPER}} .description',
            ]
        );


        $this->end_controls_section();


        $this->start_controls_section(
            'section_icon_style',
            [
                'label' => esc_html__( 'Icon Style', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // tab for icon
        $this->start_controls_tabs( 'tabs_icon_style' );

            $this->start_controls_tab(
                'tab_icon_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            );
            $this->add_control(
                'icon_color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .features-box-image' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'icon_bg_color',
                    'selector' => '{{WRAPPER}} .features-box-image',
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'border_icon',
                    'label' => esc_html__( 'Border', 'nerf' ),
                    'selector' => '{{WRAPPER}} .features-box-image',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tab_icon_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_control(
                'icon_hover_color',
                [
                    'label' => esc_html__( 'Hover Color', 'freeio' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-features-inner:hover .features-box-image' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'icon_bg_hover_color',
                    'selector' => '{{WRAPPER}}  .item-features-inner:hover .features-box-image',
                ]
            );

            $this->add_control(
                'color_br_hv_icon',
                [
                    'label' => esc_html__( 'Border Color', 'nerf' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .item-features-inner:hover .features-box-image' => 'border-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'border_icon_border!' => '',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover
        
        $this->add_responsive_control(
            'margin-icon',
            [
                'label' => esc_html__( 'Space', 'freeio' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .top-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Typography', 'freeio' ),
                'name' => 'icon_typography',
                'selector' => '{{WRAPPER}} .features-box-image',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow_icon',
                'label' => esc_html__( 'Box Shadow', 'freeio' ),
                'selector' => '{{WRAPPER}} .item-features-inner .features-box-image',
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        if ( !empty($features) ) {
            ?>
            <div class="widget-features-box <?php echo esc_attr($el_class); ?>">
                <div class="row">
                    <?php foreach ($features as $item): ?>
                        <div class="item col-12 col-md-<?php echo esc_attr(12/$columns);?> col-lg-<?php echo esc_attr(12/$columns);?>">
                            <div class="item-features-inner <?php echo trim($style); ?>">
                                 <div class="top-inner position-relative">
                                    <?php if(!empty($item['number'])) {?>
                                        <div class="number">
                                            <?php echo (int)$item['number']; ?>
                                        </div>
                                    <?php } ?>
                                    <?php
                                    $has_content = ! empty( $item['title_text'] ) || ! empty( $item['description_text'] );
                                    $html = '';

                                    if ( $item['image_icon'] == 'image' ) {
                                        if ( ! empty( $item['image']['url'] ) ) {
                                            $this->add_render_attribute( 'image', 'src', $item['image']['url'] );
                                            $this->add_render_attribute( 'image', 'alt', Control_Media::get_image_alt( $item['image'] ) );
                                            $this->add_render_attribute( 'image', 'title', Control_Media::get_image_title( $item['image'] ) );


                                            $image_html = Group_Control_Image_Size::get_attachment_image_html( $item, 'thumbnail', 'image' );

                                            if ( ! empty( $item['link']['url'] ) ) {
                                                $image_html = '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'>' . $image_html . '</a>';
                                            }

                                            $html .= '<div class="features-box-image img">' . $image_html . '</div>';
                                            if($show_circle){
                                                $html .= '<span class="sub-circle"></span>';
                                            }
                                        }
                                    } elseif ( $item['image_icon'] == 'icon' && !empty($item['icon'])) {
                                        $html .= '<div class="features-box-image icon"><i class="'.$item['icon'].'"></i></div>';
                                        if($show_circle){
                                            $html .= '<span class="sub-circle"></span>';
                                        }
                                    }
                                $html .= '</div>';
                                if ( $has_content ) {
                                    $html .= '<div class="features-box-content">';

                                    if ( ! empty( $item['title_text'] ) ) {
                                        
                                        $title_html = $item['title_text'];

                                        if ( ! empty( $item['link']['url'] ) ) {
                                            $html .= '<a href="'.esc_url($item['link']['url']).'" target="'.esc_attr($item['link']['is_external'] ? '_blank' : '_self').'" '.($item['link']['nofollow'] ? 'rel="nofollow"' : '').'><h3 class="title">'.$title_html.'</h3></a>';
                                        } else {
                                            $html .= sprintf( '<h3 class="title">%1$s</h3>', $title_html );
                                        }
                                    }

                                    if ( ! empty( $item['description_text'] ) ) {
                                        $html .= sprintf( '<div class="description">%1$s</div>', $item['description_text'] );
                                    }


                                    $html .= '</div>';
                                }

                                echo trim($html);
                                ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Features_Box );
} else {
    Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Features_Box );
}