<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Project_Overview extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_project_overview';
	}

	public function get_title() {
		return esc_html__( 'Project Details:: Overview', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-project-detail-elements' ];
	}

	public function get_project_fields() {
		if ( method_exists('WP_Freeio_Custom_Fields', 'get_all_custom_fields') ) {
			$fields = WP_Freeio_Custom_Fields::get_all_custom_fields(array(), 'front', WP_FREEIO_PROJECT_PREFIX);
		} else {
			$fields = WP_Freeio_Custom_Fields::get_custom_fields(array(), 'front', 0, WP_FREEIO_PROJECT_PREFIX);
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

        return $all_fields;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Fields', 'freeio' ),
			]
		);

		$all_fields = $this->get_project_fields();

		$repeater = new Elementor\Repeater();

        $repeater->add_control(
            'show_field',
            [
                'label' => esc_html__( 'Show field', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $all_fields
            ]
        );

        $repeater->add_control(
			'custom_title',
			[
				'label' => esc_html__( 'Custom Label', 'freeio' ),
				'type' => Elementor\Controls_Manager::TEXT,
			]
		);

        $repeater->add_control(
            'show_url',
            [
                'label'         => esc_html__( 'Show URL', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'show_field' => array('_project_category', '_project_location', '_project_project_skill', '_project_project_duration', '_project_project_experience', '_project_freelancer_type', '_project_project_language', '_project_project_level'),
                ],
            ]
        );

		$repeater->add_control(
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

		$repeater->add_control(
			'suffix',
			[
				'label' => esc_html__( 'Suffix', 'freeio' ),
				'type' => Elementor\Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
            'items',
            [
                'label' => esc_html__( 'Fields', 'freeio' ),
                'type' => Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
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

		if ( !empty($post_id) && !empty($items) ) {
			$all_fields = $this->get_project_fields();
			?>
			<div class="project-detail-detail <?php echo esc_attr($el_class); ?>">
				<ul class="list-service-detail d-flex align-items-center flex-wrap">
					<?php foreach ($items as $item) {
						$field_name = isset($all_fields[$item['show_field']]) ? $all_fields[$item['show_field']] : array();
						$this->render_meta_field($item, $post_id, $field_name);
					} ?>
				</ul>
			</div>
			<?php
		}
	}

	private function render_meta_field($item, $post_id, $field_name) {
		extract( $item );

		$meta_obj = WP_Freeio_Project_Meta::get_instance($post_id);

		$value_html = '';
		$taxs = array('_project_category', '_project_location', '_project_project_skill', '_project_project_duration', '_project_project_experience', '_project_freelancer_type', '_project_project_language', '_project_project_level');
		
		$taxs_real = array('_project_category' => 'project_category', '_project_location' => 'location', '_project_project_skill' => 'project_skill', '_project_project_duration' => 'project_duration', '_project_project_experience' => 'project_experience', '_project_freelancer_type' => 'project_freelancer_type', '_project_project_language' => 'project_language', '_project_project_level' => 'project_level');
		
		if ( in_array($show_field, $taxs) ) {
			if ( $meta_obj->check_custom_post_meta_exist($show_field) ) {
				$tax_values = get_the_terms( $post_id, $taxs_real[$show_field] );
				if ( $tax_values && ! is_wp_error( $tax_values ) ) {
					$number = 1;
					ob_start();
					foreach ($tax_values as $term) {
						if ( $show_url ) {
						?>
			            	<a class="project-tax" href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a><?php if($number < count($tax_values)) echo trim(', ');?>
			        	<?php
			        	} else {
			        		?>
			        		<span class="project-tax"><?php echo esc_html($term->name); ?></span><?php if($number < count($tax_values)) echo trim(', ');?>
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
		<li>
			<div class="icon">
				<?php
				if ( empty( $item['icon'] ) && ! Elementor\Icons_Manager::is_migration_allowed() ) {
					// add old default
					$item['icon'] = 'fa fa-star';
				}

				if ( ! empty( $item['icon'] ) ) {
					$this->add_render_attribute( 'icon', 'class', $item['icon'] );
					$this->add_render_attribute( 'icon', 'aria-hidden', 'true' );
				}

				$migrated = isset( $item['__fa4_migrated']['selected_icon'] );
				$is_new = empty( $item['icon'] ) && Elementor\Icons_Manager::is_migration_allowed();
				if ( $is_new || $migrated ) {
					Elementor\Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
				} else { ?>
					<i <?php $this->print_render_attribute_string( 'icon' ); ?>></i>
				<?php } ?>
				
			</div>
			<div class="details">
				<div class="text">
					<?php
					$field_name = !empty($item['custom_title']) ? $item['custom_title'] : $field_name;
					if ( $field_name ) {
						echo esc_html($field_name);
					}
					?>
				</div>
				<div class="value">
					<span class="content-value">
						<?php echo trim($value_html); ?>
					</span>
					<?php if ( $suffix ) { ?>
						<span class="suffix"><?php echo trim($suffix); ?></span>
					<?php } ?>
				</div>
			</div>
		</li>
		<?php
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Project_Overview );
