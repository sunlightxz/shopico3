<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Freelancer_Header_Bottom extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_freelancer_header_bottom';
	}

	public function get_title() {
		return esc_html__( 'Freelancer Details:: Header Bottom', 'freeio' );
	}

	public function get_icon() {
		return 'eicon-type-tool';
	}

	public function get_categories() {
		return [ 'freeio-freelancer-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Settings', 'freeio' ),
			]
		);


		$this->add_control(
			'view',
			[
				'label' => esc_html__( 'View', 'freeio' ),
				'type' => Elementor\Controls_Manager::HIDDEN,
				'default' => 'traditional',
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

		if ( freeio_is_freelancer_single_page() ) {
			global $post;
			$post_id = get_the_ID();
		} else {
			$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$freelancers = freeio_get_freelancers($args);
			if ( !empty($freelancers->posts) ) {
				$post_id = $freelancers->posts[0];
				$post = get_post($post_id);
			}
		}

		if ( $post ) {
			?>
			<div class="freelancer-detail-header-bottom <?php echo esc_attr($el_class); ?>">
				<div class="service-metas-detail d-flex flex-wrap align-items-center">
                    <?php freeio_freelancer_display_rating_reviews($post); ?>
                    <?php freeio_freelancer_display_short_location($post, 'icon'); ?>
                    
                    <?php freeio_freelancer_display_birthday($post, 'icon'); ?>
                </div>
			</div>
			<?php
		}
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Freelancer_Header_Bottom );