<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Vertical_Menu extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_vertical_menu';
    }

	public function get_title() {
        return esc_html__( 'Apus Header Vertical Menu', 'freeio' );
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
            'title',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your title here', 'freeio' ),
            ]
        );

        $custom_menus = array();
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
        if ( is_array( $menus ) && ! empty( $menus ) ) {
            foreach ( $menus as $menu ) {
                if ( is_object( $menu ) && isset( $menu->name, $menu->slug ) ) {
                    $custom_menus[ $menu->slug ] = $menu->name;
                }
            }
        }

        $this->add_control(
            'nav_menu',
            [
                'label' => esc_html__( 'Menu', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $custom_menus,
                'default' => ''
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
                'label' => esc_html__( 'Style Menu', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_menu_style' );

            $this->start_controls_tab(
                'tab_menu_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            );

            $this->add_control(
                'action_color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-vertical' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'action_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-vertical' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_menu_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_control(
                'action_color_hv',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-vertical:hover' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .action-vertical:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'action_bg_color_hv',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-vertical:hover' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .action-vertical:focus' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->add_control(
            'padding_action',
            [
                'label' => esc_html__( 'Padding', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .action-vertical' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'radius_action',
            [
                'label' => esc_html__( 'Border Radius', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .action-vertical' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        $menu_id = 0;
        if ($nav_menu) {
            $term = get_term_by( 'slug', $nav_menu, 'nav_menu' );
            if ( !empty($term) ) {
                $menu_id = $term->term_id;
            }
        }

        if ( !empty($menu_id) ) {
        ?>
            <div class="vertical-wrapper <?php echo esc_attr($el_class.' show-hover'); ?>">
                <span class="action-vertical d-flex align-items-center">
                    <i class="flaticon-menu"></i>
                    <?php if( !empty($title) ) { ?>
                        <span class="title">
                           <?php echo esc_html( $title ); ?>
                        </span>
                    <?php } ?>
                </span>
                <?php
                    $args = array(
                        'container_class' => 'content-vertical',
                        'menu_class' => 'apus-vertical-menu nav',
                        'fallback_cb' => '',
                        'menu'        => $menu_id,
                        'menu_id' => 'vertical-menu',
                        'walker' => new Freeio_Nav_Menu()
                    );
                    wp_nav_menu($args);
                ?>
            </div>
            <?php
            
        }
    }
}
if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Vertical_Menu );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Vertical_Menu );
}