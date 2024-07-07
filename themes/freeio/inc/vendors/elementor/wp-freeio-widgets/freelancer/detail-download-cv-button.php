<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Freelancer_Download_CV_Button extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_freelancer_download_cv_button';
	}

	public function get_title() {
		return esc_html__( 'Freelancer Details:: Download Resume Button', 'freeio' );
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
            'button_text',
            [
                'label'         => esc_html__( 'Button Text', 'freeio' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'default'       => 'Download CV',
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
        	$show_button = false;
        	if ( WP_Freeio_Freelancer::check_restrict_view_contact_info($post) || wp_freeio_get_option('restrict_contact_freelancer_download_cv', 'on') !== 'on' ) {
        		$show_button = true;
        	}
        	if ( !$show_button ) {
        		return;
        	}
	        ?>
			<div class="freelancer-detail-download-cv <?php echo esc_attr($el_class); ?>">
				<?php
					$post_id = $post->ID;
			        $download_base_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_resume_cv');
					$download_url = add_query_arg(array('post_id' => $post_id), $download_base_url);

					$check_can_download = true;
					if ( !is_user_logged_in() ) {
						$check_can_download = false;
					} else {
						$user = wp_get_current_user();
						$user_id = WP_Freeio_User::get_user_id();
						if ( !WP_Freeio_User::is_employer($user_id) && !in_array('administrator', $user->roles) ) {
							$check_can_download = false;
							
							if( WP_Freeio_User::is_freelancer($user_id) ) {
								$freelancer_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
								if ( $post_id == $freelancer_post_id ) {
									$check_can_download = true;
								}
							}
						}
					}
					$msg = '';
					$classes = 'btn btn-theme btn-invite-freelancer';
					$additional_class = $classes;
					if ( !$check_can_download ) {
						$additional_class .= ' cannot-download-cv-btn ';
						$msg = esc_html__('Please login as employer user to download CV.', 'freeio');
					}
				?>
				<a href="<?php echo esc_url($download_url); ?>" class="<?php echo esc_attr($additional_class); ?>" data-msg="<?php echo esc_attr($msg); ?>">
					<?php echo trim($button_text); ?>
				</a>
			</div>
			<?php
        }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Freelancer_Download_CV_Button );
