<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Employer_Archive_Sortby extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_employer_archive_sortby';
	}

	public function get_title() {
		return esc_html__( 'Employer Archive:: Sort By', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-employer-archive-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Settings', 'freeio' ),
			]
		);

		$this->add_control(
            'title',
            [
                'label'         => esc_html__( 'Title', 'freeio' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'default'   	=> 'Sort by',
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
				'label' => esc_html__( 'Text Color', 'freeio' ),
				'type' => Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Elementor\Core\Schemes\Color::get_type(),
					'value' => Elementor\Core\Schemes\Color::COLOR_1,
				],
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .results-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .results-count',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
        
		$orderby_options = apply_filters( 'wp-freeio-employers-orderby', array(
			'menu_order' => esc_html__('Sort by (Default)', 'freeio'),
			'newest' => esc_html__('Newest', 'freeio'),
			'oldest' => esc_html__('Oldest', 'freeio'),
			'price-lowest' => esc_html__('Lowest Price', 'freeio'),
			'price-highest' => esc_html__('Highest Price', 'freeio'),
			'random' => esc_html__('Random', 'freeio'),
		));
		$orderby = isset( $_GET['filter-orderby'] ) ? wp_unslash( $_GET['filter-orderby'] ) : 'menu_order';
		if ( !WP_Freeio_Mixes::is_ajax_request() ) {
			wp_enqueue_script('wpfi-select2');
			wp_enqueue_style('wpfi-select2');
		}
		?>
		<div class="wrapper-ordering-listing employers-ordering-wrapper <?php echo esc_attr($el_class); ?>">
			<form class="employers-ordering jobs-ordering" method="get" action="<?php echo WP_Freeio_Mixes::get_employers_page_url(); ?>">
				<?php if ( $title ) { ?>
					<div class="label"><?php echo esc_html($title); ?></div>
				<?php } ?>
				<select name="filter-orderby" class="orderby" <?php if ( $title ) { ?>data-placeholder="<?php echo esc_attr($title); ?>" <?php } ?>>
					<?php foreach ( $orderby_options as $id => $name ) : ?>
						<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" name="paged" value="1" />
				<?php WP_Freeio_Mixes::query_string_form_fields( null, array( 'filter-orderby', 'submit', 'paged' ) ); ?>
			</form>
		</div>
		<?php
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Employer_Archive_Sortby );
