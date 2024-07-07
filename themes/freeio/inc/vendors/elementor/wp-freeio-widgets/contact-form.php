<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Contact_Form extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_contact_form';
	}

	public function get_title() {
		return esc_html__( 'Apus Contact Form', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Settings', 'freeio' ),
			]
		);

		$args = array(
			'post_type'   => 'wpcf7_contact_form',
			'numberposts' => -1,
		);
		$posts = get_posts( $args );
		$options = array();
		if ( $posts ) {
			foreach ($posts as $post) {
				$options[$post->ID] = $post->post_title;
			}
		}
		$this->add_control(
			'contact_form',
			[
				'label' => esc_html__( 'Contact Form', 'freeio' ),
				'type' => Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => $options,
			]
		);

		$this->add_control(
            'contact_form_type',
            [
                'label' => esc_html__( 'User Role', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'employer' => esc_html__('Employer', 'freeio'),
                    'freelancer' => esc_html__('Freelancer', 'freeio'),
                ),
                'default' => ''
            ]
        );

		$this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'default' => esc_html__('Default', 'freeio'),
                    'popup' => esc_html__('Popup', 'freeio'),
                ),
                'default' => 'popup'
            ]
        );

		$this->add_control(
            'title', [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
                'condition' => [
                    'layout_type' => 'popup',
                ],
            ]
        );

		$this->add_control(
            'text',
            [
                'label' => esc_html__( 'Text', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => 'Contact Me',
                'condition' => [
                    'layout_type' => 'popup',
                ],
            ]
        );

		$this->add_control(
			'selected_icon',
			[
				'label' => esc_html__( 'Icon', 'freeio' ),
				'type' => Elementor\Controls_Manager::ICONS,
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


		$this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Button', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

	        $this->start_controls_tabs( 'tabs_button_style' );

	        $this->start_controls_tab(
	            'tab_button_normal',
	            [
	                'label' => esc_html__( 'Normal', 'freeio' ),
	            ]
	        );

	        $this->add_control(
	            'button_text_color',
	            [
	                'label' => esc_html__( 'Text Color', 'freeio' ),
	                'type' => Elementor\Controls_Manager::COLOR,
	                'default' => '',
	                'selectors' => [
	                    '{{WRAPPER}} .btn-show-popup ' => 'fill: {{VALUE}}; color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'background_color',
	            [
	                'label' => esc_html__( 'Background Color', 'freeio' ),
	                'type' => Elementor\Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .btn-show-popup ' => 'background-color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .btn-show-popup ',
                ]
            );

	        $this->end_controls_tab();

	        $this->start_controls_tab(
	            'tab_button_hover',
	            [
	                'label' => esc_html__( 'Hover', 'freeio' ),
	            ]
	        );

	        $this->add_control(
	            'hover_color',
	            [
	                'label' => esc_html__( 'Text Color', 'freeio' ),
	                'type' => Elementor\Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .btn-show-popup:hover, {{WRAPPER}} .btn-show-popup:focus' => 'color: {{VALUE}};',
	                    '{{WRAPPER}} .btn-show-popup:hover svg, {{WRAPPER}} .btn-show-popup:focus svg' => 'fill: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'button_background_hover_color',
	            [
	                'label' => esc_html__( 'Background Color', 'freeio' ),
	                'type' => Elementor\Controls_Manager::COLOR,
	                'selectors' => [
	                    '{{WRAPPER}} .btn-show-popup:hover, {{WRAPPER}} .btn-show-popup:focus' => 'background-color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->add_control(
	            'button_hover_border_color',
	            [
	                'label' => esc_html__( 'Border Color', 'freeio' ),
	                'type' => Elementor\Controls_Manager::COLOR,
	                'condition' => [
	                    'border_button_border!' => '',
	                ],
	                'selectors' => [
	                    '{{WRAPPER}} .btn-show-popup:hover, {{WRAPPER}} .btn-show-popup:focus' => 'border-color: {{VALUE}};',
	                ],
	            ]
	        );

	        $this->end_controls_tab();

	        $this->end_controls_tabs();

	        $this->add_responsive_control(
	            'button_padding',
	            [
	                'label' => esc_html__( 'Padding', 'freeio' ),
	                'type' => Elementor\Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', '%' ],
	                'selectors' => [
	                    '{{WRAPPER}} .btn-show-popup' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	                'separator' => 'before',
	            ]
	        );

	        $this->add_responsive_control(
	            'button_border_radius',
	            [
	                'label' => esc_html__( 'Border Radius', 'freeio' ),
	                'type' => Elementor\Controls_Manager::DIMENSIONS,
	                'size_units' => [ 'px', '%' ],
	                'selectors' => [
	                    '{{WRAPPER}} .btn-show-popup' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	        $this->add_group_control(
	            Elementor\Group_Control_Typography::get_type(),
	            [
	                'label' => esc_html__( 'Typography', 'freeio' ),
	                'name' => 'btn_typography',
	                'selector' => '{{WRAPPER}} .btn-show-popup',
	            ]
	        );
		$this->end_controls_section();

		$this->start_controls_section(
            'section_style_icon',
            [
                'label' => esc_html__( 'Icon', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Typography', 'freeio' ),
                'name' => 'icon_typography',
                'selector' => '{{WRAPPER}} .btn-show-popup i',
            ]
        );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
        if ( $contact_form ) {
        	$show_contact_form = true;
        	if ( $contact_form_type == 'freelancer' ) {
        		global $post;
        		if ( !WP_Freeio_Freelancer::check_restrict_view_contact_info($post) ) {
        			$show_contact_form = false;
        		}
        	} elseif ( $contact_form_type == 'employer' ) {
        		global $post;
        		if ( !WP_Freeio_Employer::check_restrict_view_contact_info($post) ) {
        			$show_contact_form = false;
        		}
        	}
        	if ( !$show_contact_form ) {
        		return;
        	}
			?>
			<div class="apus-contact-form <?php echo esc_attr($el_class); ?>">
				<?php
				$rand = freeio_random_key();
				if ($layout_type == 'popup') {
					?>
					<a href="#contact-form-wrapper-<?php echo esc_attr($rand); ?>" class="btn-show-popup btn btn-theme btn-outline w-100">
		                <?php if ( $text ) {
	                		echo esc_html($text);
		            	} ?>

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
		            </a>
		            <div id="contact-form-wrapper-<?php echo esc_attr($rand); ?>" class="popup-inner contact-form-wrapper1 mfp-hide" data-effect="fadeIn">
		            	<div class="header-info d-flex align-items-center">
		            		<?php if ( !empty($title) ) { ?>
			                    <h3 class="title"><?php echo esc_html($title); ?></h3>
			                <?php } ?>
		            		<a href="javascript:void(0);" class="close-magnific-popup ms-auto"><i class="ti-close"></i></a>
		            	</div>
		            	<?php echo do_shortcode('[contact-form-7 id="'.$contact_form.'"]'); ?>
		            </div>
					<?php
				} else {
					echo do_shortcode('[contact-form-7 id="'.$contact_form.'"]');
				}
				?>
			</div>
			<?php
		}
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Contact_Form );
