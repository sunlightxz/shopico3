<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Service_Author extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_service_author';
	}

	public function get_title() {
		return esc_html__( 'Service Details:: Author', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-service-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Fields', 'freeio' ),
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

        if ( freeio_is_service_single_page() ) {
			$post_id = get_the_ID();
		} else {
			$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$services = freeio_get_services($args);
			if ( !empty($services->posts) ) {
				$post_id = $services->posts[0];
			}
		}

		if ( !empty($post_id) ) {
			$author_id = freeio_get_service_post_author($post_id);
			$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);
			if ( !$freelancer_id ) {
			    return;
			}
			$freelancer_obj = get_post($freelancer_id);

			?>
			<div class="service-detail-author listing-detail-widget widget m-0 <?php echo esc_attr($el_class); ?>">
				<?php if ( $title ) { ?>
                    <h4 class="widget-title"><?php echo esc_html($title); ?></h4>
                <?php } ?>
				<div class="widget-service-author">
			        <div class="service-author-heading d-flex align-items-center">
			            <div class="service-author-image flex-shrink-0">
			                <a href="<?php echo esc_url( get_permalink($freelancer_id) ); ?>">
			                    <?php freeio_freelancer_display_logo($freelancer_obj, false); ?>
			                </a>
			            </div>
			            <div class="service-author-right flex-grow-1">
			                <h5><a href="<?php echo esc_url( get_permalink($freelancer_id) ); ?>">
			                    <?php echo freeio_freelancer_name($freelancer_obj); ?>
			                </a></h5>
			                <!-- job -->
			                <?php freeio_freelancer_display_job_title($freelancer_obj); ?>
			                <?php freeio_freelancer_display_rating_reviews($freelancer_obj); ?>
			            </div>
			        </div>

			        <div class="metas">
			            <?php freeio_freelancer_display_short_location($freelancer_obj, 'title'); ?>
			            <?php freeio_freelancer_display_salary($freelancer_obj, 'title'); ?>
			        </div>

			    </div>

			</div>
			<?php
		}
	}


}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Service_Author );