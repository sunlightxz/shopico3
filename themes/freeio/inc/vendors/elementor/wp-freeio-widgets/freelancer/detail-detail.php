<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Freelancer_Detail extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_freelancer_detail';
	}

	public function get_title() {
		return esc_html__( 'Freelancer Details:: Detail', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-freelancer-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Favorite', 'freeio' ),
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
		if ( !empty($post) ) {
            $meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);


            $project_success = freeio_freelancer_get_project_success($post);
            $total_services = freeio_freelancer_get_total_services($post);
            $completed_services = freeio_freelancer_get_completed_service($post);
            $inqueue_service = freeio_freelancer_get_inqueue_service($post);
	        ?>
			<div class="freelancer-detail-detail service-detail-detail <?php echo esc_attr($el_class); ?>">
                <ul class="list-service-detail column-4 d-flex align-items-center flex-wrap">
                    
                    <li>
                        <div class="icon">
                            <i class="flaticon-target"></i>
                        </div>
                        <div class="details">
                            <div class="text"><?php esc_html_e('Project Success', 'freeio'); ?></div>
                            <div class="value"><?php echo number_format($project_success); ?></div>
                        </div>
                    </li>

                    <li>
                        <div class="icon">
                            <i class="flaticon-goal"></i>
                        </div>
                        <div class="details">
                            <div class="text"><?php esc_html_e('Total Service', 'freeio'); ?></div>
                            <div class="value"><?php echo number_format($total_services); ?></div>
                        </div>
                    </li>

                    <li>
                        <div class="icon">
                            <i class="flaticon-target"></i>
                        </div>
                        <div class="details">
                            <div class="text"><?php esc_html_e('Completed Service', 'freeio'); ?></div>
                            <div class="value"><?php echo number_format($completed_services); ?></div>
                        </div>
                    </li>

                    <li>
                        <div class="icon">
                            <i class="flaticon-file-1"></i>
                        </div>
                        <div class="details">
                            <div class="text"><?php esc_html_e('In Queue service', 'freeio'); ?></div>
                            <div class="value"><?php echo number_format($inqueue_service); ?></div>
                        </div>
                    </li>

                    <?php do_action('wp-freeio-single-freelancer-details', $post); ?>

                </ul>

                
            </div>
			<?php
        }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Freelancer_Detail );
