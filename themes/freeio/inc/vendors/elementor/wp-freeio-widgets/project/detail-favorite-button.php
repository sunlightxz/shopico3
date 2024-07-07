<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Project_Favorite_Button extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_project_favorite_button';
	}

	public function get_title() {
		return esc_html__( 'Project Details:: Favorite Button', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-project-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Favorite', 'freeio' ),
			]
		);

		$this->add_control(
            'show_text',
            [
                'label' => esc_html__( 'Show Text', 'freeio' ),
                'type' => Elementor\Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__( 'Hide', 'freeio' ),
                'label_off' => esc_html__( 'Show', 'freeio' ),
                'return_value' => 'yes',
				'default' => 'yes',
            ]
        );

		$this->add_control(
			'add_text',
			[
				'label' => esc_html__( 'Add Text', 'freeio' ),
				'type' => Elementor\Controls_Manager::TEXT,
				'default' => 'Save',
				'condition' => [
                    'show_text' => 'yes',
                ],
			]
		);

		$this->add_control(
			'added_text',
			[
				'label' => esc_html__( 'Added Text', 'freeio' ),
				'type' => Elementor\Controls_Manager::TEXT,
				'default' => 'Saved',
				'condition' => [
                    'show_text' => 'yes',
                ],
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
            'section_icon_style',
            [
                'label' => esc_html__( 'Icon Style', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_box_style' );

            $this->start_controls_tab(
                'tab_icon_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            );

            $this->add_control(
                'color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-item [class*="btn"] i' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-item [class*="btn"] i' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'space_color',
                [
                    'label' => esc_html__( 'Border Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-item [class*="btn"] i' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_icon_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_control(
                'hv_color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-item [class*="btn"]:hover i,{{WRAPPER}} .action-item [class*="btn"]:focus i' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'bg_hv_color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-item [class*="btn"]:hover i, {{WRAPPER}} .action-item [class*="btn"]:focus i' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'space_hv_color',
                [
                    'label' => esc_html__( 'Border Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .action-item [class*="btn"]:hover i, {{WRAPPER}} .action-item [class*="btn"]:focus i' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab normal and hover

        $this->end_controls_section();

        $this->start_controls_section(
            'section_text_style',
            [
                'label' => esc_html__( 'Text Style', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__( 'Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .action-item span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
        if ( freeio_is_project_single_page() ) {
			$post_id = get_the_ID();
		} else {
			$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$projects = freeio_get_projects($args);
			if ( !empty($projects->posts) ) {
				$post_id = $projects->posts[0];
			}
		}
		if ( !empty($post_id) ) {
	        ?>
			<div class="project-detail-favorite action-item <?php echo esc_attr($el_class); ?>">
				<?php
			        echo WP_Freeio_Favorite::display_project_favorite_btn($post_id, array(
                        'show_text' => $show_text,
                        'tooltip' => true,
                        'added_text' => $added_text,
                        'add_text' => $add_text,
                        'added_tooltip_title' => esc_html__('Remove Favorite', 'freeio'),
                        'add_tooltip_title' => esc_html__('Add Favorite', 'freeio'),
                        'echo' => false,
                    ));
				?>
			</div>
			<?php
        }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Project_Favorite_Button );
