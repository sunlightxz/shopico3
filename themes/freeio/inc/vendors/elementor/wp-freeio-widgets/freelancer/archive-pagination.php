<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Freelancer_Archive_Pagination extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_freelancer_archive_pagination';
	}

	public function get_title() {
		return esc_html__( 'Freelancer Archive:: Pagination', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-freelancer-archive-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Settings', 'freeio' ),
			]
		);

		$this->add_control(
            'pagination_type',
            [
                'label' => esc_html__( 'Pagination Type', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'default' => esc_html__('Default', 'freeio'),
		            'loadmore' => esc_html__('Load More Button', 'freeio'),
		            'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
                ),
                'default' => 'default',
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
        
		global $freeio_freelancers;
		$args = array(
        	'freelancers' => $freeio_freelancers,
        	'settings' => $settings,
        );
		?>
		<div class="elements-freelancers-pagination-wrapper <?php echo esc_attr($el_class); ?>">
			<?php echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/pagination-elementor', $args); ?>
		</div>
		<?php
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Freelancer_Archive_Pagination );
