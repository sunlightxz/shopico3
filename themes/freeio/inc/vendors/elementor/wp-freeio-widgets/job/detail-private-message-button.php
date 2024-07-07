<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Job_Private_Message_Button extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_job_private_message_button';
	}

	public function get_title() {
		return esc_html__( 'Job Details:: Private Message Button', 'freeio' );
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
            'btn_text',
            [
                'label'         => esc_html__( 'Button Text', 'freeio' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'default'   => 'Message'
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
			<div class="private-message-btn <?php echo esc_attr($el_class); ?>">
				<?php
					$user_id = $post->post_author;
	                freeio_private_message_form($post, $user_id, $btn_text);
	            ?>
			</div>
			<?php
        }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Job_Private_Message_Button );
