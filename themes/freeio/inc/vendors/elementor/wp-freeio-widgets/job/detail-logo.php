<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Job_Logo extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_job_logo';
	}

	public function get_title() {
		return esc_html__( 'Job Details:: Logo', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-job-detail-elements' ];
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

        $this->add_responsive_control(
            'logo_width',
            [
                'label' => esc_html__( 'Size', 'freeio' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'range' => [
                    'px' => [
                        'min' => 6,
                    ],
                    '%' => [
                        'min' => 6,
                    ],
                    'vw' => [
                        'min' => 6,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .detail-employer-logo' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .detail-employer-logo img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
        if ( freeio_is_job_single_page() ) {
        	global $post;
			$post_id = get_the_ID();
		} else {
			$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$jobs = freeio_get_jobs($args);
			if ( !empty($jobs->posts) ) {
				$post_id = $jobs->posts[0];
				$post = get_post($post_id);
			}
		}

		if ( !empty($post) ) {
			?>
			<div class="detail-employer-logo top-detail-job <?php echo esc_attr($el_class); ?>">
				<div class="job-thumbnail">
					<?php freeio_job_display_employer_logo($post, true, true); ?>
				</div>
			</div>
			<?php
	    }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Job_Logo );
