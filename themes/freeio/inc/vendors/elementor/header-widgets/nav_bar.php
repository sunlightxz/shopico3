<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Nav_Bar extends Widget_Base {

	public function get_name() {
        return 'apus_element_nav_bar';
    }

	public function get_title() {
        return esc_html__( 'Apus Header NavBar', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'uomo-header-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $ele_obj = \Elementor\Plugin::$instance;
        $templates = $ele_obj->templates_manager->get_source( 'local' )->get_items();

        if ( empty( $templates ) ) {

            $this->add_control(
                'no_templates',
                array(
                    'label' => false,
                    'type'  => Controls_Manager::RAW_HTML,
                    'raw'   => $this->empty_templates_message(),
                )
            );

            return;
        }

        $options = [
            '0' => '— ' . esc_html__( 'Select', 'freeio' ) . ' —',
        ];

        $types = [];

        foreach ( $templates as $template ) {
            $options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
            $types[ $template['template_id'] ] = $template['type'];
        }

        $this->add_control(
            'item_template_id',
            [
                'label'       => esc_html__( 'Choose Template', 'freeio' ),
                'type'        => Controls_Manager::SELECT,
                'default'     => '0',
                'options'     => $options,
                'types'       => $types,
                'label_block' => 'true',
            ]
        );

        $this->add_responsive_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'st_right' => esc_html__( 'Right', 'freeio' ),
                    'st_left' => esc_html__( 'Left', 'freeio' ),
                ],
                'default' => 'st_right'
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
            'section_icon_style',
            [
                'label' => esc_html__( 'Icon', 'freeio' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__( 'Color Icon', 'freeio' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}} .vertical-icon::before' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .vertical-icon::after' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        ?>
        <div class="navbar-wrapper <?php echo esc_attr($el_class.' '.$style); ?>">
            <span class="show-navbar-sidebar"><i class="vertical-icon"></i></span>
            <div class="navbar-sidebar-wrapper">
                <a href="javascript:void(0);" class="close-navbar-sidebar"><i class="ti-close"></i></a>
                <?php
                $ele_obj = \Elementor\Plugin::$instance;
                $content_html = '';
                
                if ( '0' !== $item_template_id ) {

                    $template_content = $ele_obj->frontend->get_builder_content_for_display( $item_template_id );

                    if ( ! empty( $template_content ) ) {
                        $content_html .= $template_content;

                        if ( Plugin::$instance->editor->is_edit_mode() ) {
                            $link = add_query_arg(
                                array(
                                    'elementor' => '',
                                ),
                                get_permalink( $item_template_id )
                            );

                            $content_html .= sprintf( '<div class="uomo__edit-cover" data-template-edit-link="%s"><i class="fa fa-pencil"></i><span>%s</span></div>', $link, esc_html__( 'Edit Template', 'freeio' ) );
                        }
                    } else {
                        $content_html = $this->no_template_content_message();
                    }
                } else {
                    $content_html = $this->no_templates_message();
                }

                echo trim($content_html);
                ?>
            </div>
            <div class="navbar-sidebar-overlay"></div>
        </div>
        <?php
    }

    public function no_templates_message() {
        return '<div class="no-template-message"><span>' . esc_html__( 'Template is not defined.', 'freeio' ) . '</span></div>';
    }

    public function no_template_content_message() {
        return '<div class="no-template-message"><span>' . esc_html__( 'The tabs are working. Please, note, that you have to add a template to the library in order to be able to display it inside the tabs.', 'freeio' ) . '</span></div>';
    }

    public function empty_templates_message() {
        $output = '<div id="elementor-widget-template-empty-templates">';
            $output .= '<div class="elementor-widget-template-empty-templates-icon"><i class="eicon-nerd"></i></div>';
            $output .= '<div class="elementor-widget-template-empty-templates-title">' . esc_html__( 'You Haven’t Saved Templates Yet.', 'freeio' ) . '</div>';
            $output .= '<div class="elementor-widget-template-empty-templates-footer">';
                $output .= esc_html__( 'What is Library?', 'freeio' );
                $output .= '<a class="elementor-widget-template-empty-templates-footer-url" href="https://go.elementor.com/docs-library/" target="_blank">' . esc_html__( 'Read our tutorial on using Library templates.', 'freeio' ) . '</a>';
            $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Nav_Bar );
} else {
    Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Nav_Bar );
}