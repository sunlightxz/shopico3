<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Employer_Archive_Results_Count extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_employer_archive_results_count';
	}

	public function get_title() {
		return esc_html__( 'Employer Archive:: Results Count', 'freeio' );
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
        global $freeio_employers;

        if ( !empty($freeio_employers) ) {
	        $total = $freeio_employers->found_posts;
			$per_page = $freeio_employers->query_vars['posts_per_page'];
			$current = max( 1, $freeio_employers->get( 'paged', 1 ) );
		} else {
			$total = $per_page = $current = 1;
		}
		
		?>
		<div class="wrapper-ordering-listing">
			<div class="results-count <?php echo esc_attr($el_class); ?>">
				<?php
					if ( $total <= $per_page || -1 === $per_page ) {
						/* translators: %d: total results */
						printf( _n( 'Showing the single result', 'Showing all %d results', $total, 'freeio' ), $total );
					} else {
						$first = ( $per_page * $current ) - $per_page + 1;
						$last  = min( $total, $per_page * $current );
						/* translators: 1: first result 2: last result 3: total results */
						printf( _nx( 'Showing the single result', 'Showing <span class="first">%1$d</span> &ndash; <span class="last">%2$d</span> of %3$d results', $total, 'with first and last result', 'freeio' ), $first, $last, $total );
					}
				?>
			</div>
		</div>
		<?php
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Employer_Archive_Results_Count );
