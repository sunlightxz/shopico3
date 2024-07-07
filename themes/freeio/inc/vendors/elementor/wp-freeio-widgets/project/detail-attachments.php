<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Project_Attachments extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_project_attachments';
	}

	public function get_title() {
		return esc_html__( 'Project Details:: Attachments', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-project-detail-elements' ];
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
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
        if ( freeio_is_project_single_page() ) {
        	global $post;
			$post_id = get_the_ID();
		} else {
			$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$projects = freeio_get_projects($args);
			if ( !empty($projects->posts) ) {
				$post_id = $projects->posts[0];
				$post = get_post($post_id);
				setup_postdata( $GLOBALS['post'] =& $post );
			}
		}

		if ( !empty($post) ) {
			?>
			<div class="project-widget-attachments <?php echo esc_attr($el_class); ?>">
        		<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-project/attachments', array('show_title' => false) ); ?>
		    </div>
			<?php

			if ( !freeio_is_project_single_page() ) {
				wp_reset_postdata();
			}
	    }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Project_Attachments );