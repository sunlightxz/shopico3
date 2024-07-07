<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Nav_Menu extends Widget_Base {

	public function get_name() {
        return 'apus_element_nav_menu';
    }

	public function get_title() {
        return esc_html__( 'Apus Navigation Menu', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-elements' ];
    }

	protected function register_controls() {

        $custom_menus = array();
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
        if ( is_array( $menus ) && ! empty( $menus ) ) {
            foreach ( $menus as $menu ) {
                if ( is_object( $menu ) && isset( $menu->name, $menu->slug ) ) {
                    $custom_menus[ $menu->slug ] = $menu->name;
                }
            }
        }

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Navigation Menu', 'freeio' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Enter your title here', 'freeio' ),
            ]
        );

        $this->add_control(
            'nav_menu',
            [
                'label' => esc_html__( 'Menu', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => $custom_menus,
                'default' => ''
            ]
        );
        
        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'st_circle' => esc_html__('Circle', 'freeio'),
                    'st_line' => esc_html__('Line', 'freeio'),
                    'st_underline' => esc_html__('Line Under', 'freeio'),
                ),
                'default' => ''
            ]
        );
        
        $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__( 'Alignment', 'freeio' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'freeio' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'freeio' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'freeio' ),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__( 'Justified', 'freeio' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .menu' => 'text-align: {{VALUE}};',
                ],
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

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__( 'Title Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .widget-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Title Typography', 'freeio' ),
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .widget-title',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_menu_style',
            [
                'label' => esc_html__( 'Menu Item', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'menu_color',
            [
                'label' => esc_html__( 'Menu Color', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .widget-content a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'menu_color_hover',
            [
                'label' => esc_html__( 'Menu Color Hover', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} li:hover > a, 
                    {{WRAPPER}} .menu li.current-cat-parent > a, 
                    {{WRAPPER}} .menu li.current-cat > a ' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Menu Typography', 'freeio' ),
                'name' => 'menu_typography',
                'selector' => '{{WRAPPER}} .widget-content a',
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

        ?>
        <div class="widget-nav-menu no-margin widget <?php echo esc_attr($el_class.' '.$style); ?>">
            
            <?php if ( !empty($title) ) { ?>
                <h2 class="widget-title"><?php echo esc_html($title); ?></h2>
            <?php } ?>

            <?php if ( !empty($menu_id) ) { ?>
                <div class="widget-content">
                    <?php
                        $nav_menu_args = array(
                            'fallback_cb' => '',
                            'menu'        => $menu_id
                        );

                        wp_nav_menu( $nav_menu_args, $menu_id );
                    ?>
                </div>
            <?php } ?>

        </div>
        <?php
    }

}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Nav_Menu );
} else {
    Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Nav_Menu );
}