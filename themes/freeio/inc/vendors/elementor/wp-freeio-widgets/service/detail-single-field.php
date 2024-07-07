<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Service_Single_Field extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_service_single_field';
	}

	public function get_title() {
		return esc_html__( 'Service Details:: Single Field Data', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-service-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Field', 'freeio' ),
			]
		);

		if ( method_exists('WP_Freeio_Custom_Fields', 'get_all_custom_fields') ) {
			$fields = WP_Freeio_Custom_Fields::get_all_custom_fields(array(), 'front', WP_FREEIO_SERVICE_PREFIX);
		} else {
			$fields = WP_Freeio_Custom_Fields::get_custom_fields(array(), 'front', 0, WP_FREEIO_SERVICE_PREFIX);
		}
		
        $all_fields = array( '' => esc_html__('Choose a field', 'freeio') );
        if ( !empty($fields) ) {
	        foreach ($fields as $key => $field) {
	        	if ( $field['type'] !== 'title' ) {
		            $name = $field['name'];
		            if ( empty($field['name']) ) {
		                $name = $field['id'];
		            }
		            $all_fields[$field['id']] = $name;
		        }
	        }
        }

        $this->add_control(
            'show_field',
            [
                'label' => esc_html__( 'Show field', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $all_fields
            ]
        );

        $this->add_control(
            'show_url',
            [
                'label'         => esc_html__( 'Show URL', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'show_field' => array('_service_category', '_service_location', '_service_tag'),
                ],
            ]
        );

		$this->add_control(
			'selected_icon',
			[
				'label' => esc_html__( 'Icon', 'freeio' ),
				'type' => Elementor\Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'suffix',
			[
				'label' => esc_html__( 'Suffix', 'freeio' ),
				'type' => Elementor\Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
            'style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'st_button' => esc_html__('Button', 'freeio'),
                ),
                'default' => ''
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
                'label' => esc_html__( 'Style', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'color',
            [
                'label' => esc_html__( 'Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .service-detail-field, {{WRAPPER}} .service-detail-field a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Typography::get_type(),
            [
                'label' => esc_html__( 'Typography', 'freeio' ),
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .service-detail-field',
            ]
        );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
        if ( freeio_is_service_single_page() ) {
			$post_id = get_the_ID();
		} else {
			$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$services = freeio_get_services($args);
			if ( !empty($services->posts) ) {
				$post_id = $services->posts[0];
			}
		}
		if ( !empty($post_id) ) {
			$meta_obj = WP_Freeio_Service_Meta::get_instance($post_id);
			?>
			<div class="service-detail-field <?php echo esc_attr($el_class.' '.$style); ?>">
				<?php
				$value_html = '';
				$taxs = array('_service_category', '_service_location', '_service_tag');
				
				if ( in_array($show_field, $taxs) ) {
					if ( $meta_obj->check_custom_post_meta_exist($show_field) ) {
						$tax_values = get_the_terms( $post_id, ltrim($show_field, '_') );
						if ( $tax_values && ! is_wp_error( $tax_values ) ) {
							$number = 1;
							ob_start();
							foreach ($tax_values as $term) {
								if ( $show_url ) {
								?>
					            	<a class="service-tax" href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a><?php if($number < count($tax_values)) echo trim(', ');?>
					        	<?php
					        	} else {
					        		?>
					        		<span class="service-tax"><?php echo esc_html($term->name); ?></span><?php if($number < count($tax_values)) echo trim(', ');?>
					        		<?php
					        	}
					        	$number++;
					    	}
					    	$value_html = ob_get_clean();
						}
					}
				} else {
					if ( $meta_obj->check_custom_post_meta_exist($show_field) && ($value = $meta_obj->get_custom_post_meta($show_field)) ) {
						$value_html = is_array($value) ? implode(', ', $value) : $value;
						
					}
				}
				?>
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
				if ( $is_new || $migrated ) {
					Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
				} else { ?>
					<i <?php $this->print_render_attribute_string( 'icon' ); ?>></i>
				<?php } ?>

				<span class="content-value">
					<?php echo trim($value_html); ?>
				</span>

				<?php if ( $suffix ) { ?>
					<span class="suffix"><?php echo trim($suffix); ?></span>
				<?php } ?>
			</div>
			<?php
        }
	}
}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Service_Single_Field );
