<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Employer_Tagline extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_employer_tagline';
	}

	public function get_title() {
		return esc_html__( 'Employer Details:: Tagline', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-employer-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Settings', 'freeio' ),
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
                'label' => esc_html__( 'Style', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'element_color',
            [
                'label' => esc_html__( 'Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Typography', 'freeio' ),
                'name' => 'element_typography',
                'selector' => '{{WRAPPER}}',
            ]
        );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
        if ( freeio_is_employer_single_page() ) {
        	global $post;
			$post_id = get_the_ID();
		} else {
			$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$employers = freeio_get_employers($args);
			if ( !empty($employers->posts) ) {
				$post_id = $employers->posts[0];
				$post = get_post($post_id);
			}
		}
		if ( !empty($post) ) {
	        ?>
			<div class="employer-detail-tagline <?php echo esc_attr($el_class); ?>">
				<?php freeio_employer_display_tagline($post); ?>
			</div>
			<?php
	    }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Employer_Tagline );
