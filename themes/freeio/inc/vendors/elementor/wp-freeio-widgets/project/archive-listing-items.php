<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Project_Archive_Listing_Items extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_project_archive_listing_items';
	}

	public function get_title() {
		return esc_html__( 'Project Archive:: Project Items', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-project-archive-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Settings', 'freeio' ),
			]
		);

		$this->add_control(
            'project_item_style',
            [
                'label' => esc_html__( 'Project Item Style', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'grid' => esc_html__('Grid Default', 'freeio'),
		            'grid-v1' => esc_html__('Grid V1', 'freeio'),
		            'grid-v2' => esc_html__('Grid V2', 'freeio'),
		            'list' => esc_html__('List Default', 'freeio'),
            		'list-v1' => esc_html__('List V1', 'freeio'),
                ),
                'default' => 'grid',
            ]
        );

        $columns = range( 1, 12 );
        $columns = array_combine( $columns, $columns );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $columns,
                'frontend_available' => true,
                'default' => 3,
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

        $args = array(
        	'settings' => $settings,
        );

        if ( Elementor\Plugin::$instance->editor->is_edit_mode() ) {
        	$query_args = array(
				'post_type' => 'project',
			    'post_status' => 'publish',
			    'post_per_page' => wp_freeio_get_option('number_projects_per_page'),
			);

			$projects = WP_Freeio_Query::get_posts($query_args);
			$args['projects'] = $projects;
        } else {
	        global $freeio_projects;
	        $args['projects'] = $freeio_projects;
	    }

		?>
		
		<div class="element-projects-listing-wrapper <?php esc_attr($el_class); ?>">
			<?php echo WP_Freeio_Template_Loader::get_template_part('loop/project/archive-inner-elementor', $args); ?>
		</div>
		<?php
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Project_Archive_Listing_Items );