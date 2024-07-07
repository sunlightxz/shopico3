<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Project_Archive_Results_Filter extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_project_archive_results_filter';
	}

	public function get_title() {
		return esc_html__( 'Project Archive:: Results Filter', 'freeio' );
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
				'label' => esc_html__( 'Title', 'freeio' ),
				'tab' => Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'freeio' ),
				'type' => Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Elementor\Core\Schemes\Color::get_type(),
					'value' => Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .title',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();
        extract( $settings );
        
		$filters = WP_Freeio_Abstract_Filter::get_filters();
		?>
		<div class="element-results-filter-wrapper <?php echo esc_attr($el_class); ?>">
			<?php echo WP_Freeio_Template_Loader::get_template_part('loop/project/results-filters', array('filters' => $filters)); ?>
		</div>
		<?php
	}
}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Project_Archive_Results_Filter );
