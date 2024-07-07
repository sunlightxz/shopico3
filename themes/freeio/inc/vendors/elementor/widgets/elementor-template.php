<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Elementor_Template extends Widget_Base {

    public function get_name() {
        return 'apus_element_elementor_template';
    }

    public function get_title() {
        return esc_html__( 'Apus Load Elementor Template', 'freeio' );
    }

    public function get_icon() {
        return 'eicon-tabs';
    }

    public function get_categories() {
        return [ 'freeio-elements' ];
    }

    protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Settings', 'freeio' ),
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

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings();

        extract( $settings );
        
        $ele_obj = \Elementor\Plugin::$instance;
        $content_html = '';
        
        if ( '0' !== $item_template_id ) {

            $item_template_id = freeio_get_lang_post_id($item_template_id, 'elementor_library');
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

                    $content_html .= sprintf( '<div class="freeio__edit-cover" data-template-edit-link="%s"><i class="fa fa-pencil"></i><span>%s</span></div>', $link, esc_html__( 'Edit Template', 'freeio' ) );
                }
            } else {
                $content_html = $this->no_template_content_message();
            }
        } else {
            $content_html = $this->no_templates_message();
        }

        echo trim($content_html);
    }

    public function no_templates_message() {
        return '<div class="no-template-message"><span>' . esc_html__( 'Template is not defined.', 'freeio' ) . '</span></div>';
    }

    public function no_template_content_message() {
        return '<div class="no-template-message"><span>' . esc_html__( 'The element are working. Please, note, that you have to add a template to the library in order to be able to display it inside the section.', 'freeio' ) . '</span></div>';
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

Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Elementor_Template );
