<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Filter_Button_Template extends Widget_Base {

    public function get_name() {
        return 'apus_element_filter_button_template';
    }

    public function get_title() {
        return esc_html__( 'Apus Filter Button Template', 'freeio' );
    }
    
    public function get_categories() {
        return [ 'freeio-elements' ];
    }

    protected function register_controls() {
        
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Template Options', 'freeio' ),
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

        $this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Button', 'freeio'),
                    'st_normal' => esc_html__('Normal', 'freeio'),
                ),
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

        if (!empty($content_html)) {?>
            <?php if($style == 'st_normal') { ?>
                <div class="d-block d-lg-none">
                    <span class="filter-in-sidebar"><svg width="14" height="12" viewBox="0 0 14 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.06254 0.9375L3.06248 9.7035L3.78979 8.97725L4.58529 9.77275L2.50004 11.858L0.414795 9.77275L1.21029 8.97725L1.93748 9.7035L1.93754 0.9375H3.06254ZM8.12504 9.375V10.5H5.31254V9.375H8.12504ZM9.81254 6.5625V7.6875H5.31254V6.5625H9.81254ZM11.5 3.75V4.875H5.31254V3.75H11.5ZM13.1875 0.9375V2.0625H5.31254V0.9375H13.1875Z" fill="currentColor"></path>
                        </svg><span class="text"><?php echo esc_html__('Filter','freeio'); ?></span>
                    </span>
                    <div class="filter-sidebar offcanvas-filter-sidebar">
                        <div class="offcanvas-filter-sidebar-header d-flex align-items-center">
                            <div class="title"><?php echo esc_html__('All Filters','freeio'); ?></div>
                            <span class="close-filter-sidebar ms-auto d-flex align-items-center justify-content-center"><i class="ti-close"></i></span>
                        </div>
                        <?php echo trim($content_html); ?>
                    </div>
                    <div class="over-dark"></div>
                </div>
                <div class="d-none d-lg-block">
                    <?php echo trim($content_html); ?>
                </div>
            <?php } else { ?>
                <span class="filter-in-sidebar"><svg width="14" height="12" viewBox="0 0 14 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.06254 0.9375L3.06248 9.7035L3.78979 8.97725L4.58529 9.77275L2.50004 11.858L0.414795 9.77275L1.21029 8.97725L1.93748 9.7035L1.93754 0.9375H3.06254ZM8.12504 9.375V10.5H5.31254V9.375H8.12504ZM9.81254 6.5625V7.6875H5.31254V6.5625H9.81254ZM11.5 3.75V4.875H5.31254V3.75H11.5ZM13.1875 0.9375V2.0625H5.31254V0.9375H13.1875Z" fill="currentColor"></path>
                    </svg><span class="text"><?php echo esc_html__('Filter','freeio'); ?></span>
                </span>
                <div class="filter-sidebar offcanvas-filter-sidebar">
                    <div class="offcanvas-filter-sidebar-header d-flex align-items-center">
                        <div class="title"><?php echo esc_html__('All Filters','freeio'); ?></div>
                        <span class="close-filter-sidebar ms-auto d-flex align-items-center justify-content-center"><i class="ti-close"></i></span>
                    </div>
                    <?php echo trim($content_html); ?>
                </div>
                <div class="over-dark"></div>
            <?php } ?>
            <?php
        }
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

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Filter_Button_Template );
} else {
    Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Filter_Button_Template );
}