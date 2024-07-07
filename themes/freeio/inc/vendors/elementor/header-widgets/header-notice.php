<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Header_Notice extends Widget_Base {

	public function get_name() {
        return 'apus_element_header_notice';
    }

	public function get_title() {
        return esc_html__( 'Apus Header Notice', 'freeio' );
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

        $this->add_control(
            'content',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'type' => Controls_Manager::TEXTAREA,
                'placeholder' => esc_html__( 'Enter your content here', 'freeio' ),
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

        if ( $content ) {
            $notice_id = md5( $content );
            ?>
            <div class="header-notice-wrapper position-relative <?php echo esc_attr($el_class); ?>" data-notice-id="<?php echo esc_attr( $notice_id ); ?>" style="display:none;">
                <div class="container">
                    <div class="content">
                        <?php echo trim($content); ?>
                    </div>
                    <a href="javascript:void(0);" class="header-notice-dismiss-btn">
                        <i class="ti-close"></i>
                    </a>
                </div>
            </div>
            <?php
        }
    }

}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Header_Notice );
} else {
    Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Header_Notice );
}