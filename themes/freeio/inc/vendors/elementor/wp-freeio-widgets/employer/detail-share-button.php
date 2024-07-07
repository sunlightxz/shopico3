<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Employer_Share_Button extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_employer_share_button';
	}

	public function get_title() {
		return esc_html__( 'Employer Details:: Share Button', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-employer-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Share', 'freeio' ),
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

	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
        ?>
		<div class="employer-detail-sharebox action-item <?php echo esc_attr($el_class); ?>">
			<?php get_template_part('template-parts/sharebox-listing' ); ?>
		</div>
		<?php
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Employer_Share_Button );
