<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Project_Price extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_project_price';
	}

	public function get_title() {
		return esc_html__( 'Project Details:: Price', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-project-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Price', 'freeio' ),
			]
		);

        $this->add_responsive_control(
            'alignment',
            [
                'label' => esc_html__( 'Alignment', 'freeio' ),
                'type' => Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__( 'Left', 'freeio' ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'freeio' ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__( 'Right', 'freeio' ),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__( 'Justified', 'freeio' ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .project-price' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
			'btn_text',
			[
				'label' => esc_html__( 'Button Text', 'freeio' ),
				'type' => Elementor\Controls_Manager::TEXT,
				'default' => 'Submit a Proposal',
			]
		);

        $this->add_control(
			'selected_icon',
			[
				'label' => esc_html__( 'Button Icon', 'freeio' ),
				'type' => Elementor\Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label' => esc_html__( 'Icon Spacing', 'freeio' ),
				'type' => Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .button-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
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
			}
		}

		if ( !empty($post) ) {
			?>
			<div class="project-widget-price listing-detail-widget <?php echo esc_attr($el_class); ?>">
        
		        <div class="project-price">
		            <?php echo WP_Freeio_Project::get_price_html($post->ID); ?>
		        </div>
	            
	            <button type="submit" class="btn btn-theme btn-inverse w-100 submit-a-proposal-btn">
	            	<?php echo trim($btn_text); ?>
	            	<?php
	                if ( empty( $settings['icon'] ) && ! Elementor\Icons_Manager::is_migration_allowed() ) {
						// add old default
						$settings['icon'] = 'fa fa-star';
					}

					if ( ! empty( $settings['icon'] ) ) {
						$this->add_render_attribute( 'icon', 'class', $settings['icon'] );
						$this->add_render_attribute( 'icon', 'aria-hidden', 'true' );
					}

					$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
					$is_new = empty( $settings['icon'] ) && Elementor\Icons_Manager::is_migration_allowed();
					if ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon']['value'] ) ) :
						if ( $is_new || $migrated ) { ?>
							<span class="button-icon">
							<?php Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] ); ?>
						<?php } else { ?>
							<span class="button-icon">
								<i <?php $this->print_render_attribute_string( 'icon' ); ?>></i>
							</span>
						<?php } ?>
					<?php endif; ?>
	            </button>

		    </div>
			<?php
	    }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Project_Price );
