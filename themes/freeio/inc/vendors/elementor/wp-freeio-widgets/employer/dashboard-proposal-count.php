<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Dashboard_Employer_Proposal_Count extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_dashboard_employer_proposal_count';
	}

	public function get_title() {
		return esc_html__( 'Employer Dashboard:: Proposal Count', 'freeio' );
	}
	
	public function get_categories() {
		return [ 'freeio-dashboard-elements' ];
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
				'label' => esc_html__( 'Title', 'freeio' ),
				'type' => Elementor\Controls_Manager::TEXT,
				'default' => 'Proposals',
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label' => esc_html__( 'Icon', 'freeio' ),
				'type' => Elementor\Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
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

        $user_id = WP_Freeio_User::get_user_id();

        if ( Elementor\Plugin::$instance->editor->is_edit_mode() ) {
        	$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$employers = freeio_get_employers($args);
			if ( !empty($employers->posts) ) {
				$employer_id = $employers->posts[0];
				$user_id = WP_Freeio_User::get_user_by_employer_id($employer_id);
			}
        } else {
        	if ( !is_user_logged_in() || !WP_Freeio_User::is_employer($user_id) ) {
        		return;
        	}
        }

		?>
		<div class="statistics dashboard-proposal-count <?php echo esc_attr($el_class); ?>">
			<?php
			$projects = new WP_Query(array(
			    'post_type' => 'project',
			    'post_status' => array('publish', 'hired', 'completed', 'cancelled'),
			    'author' => $user_id,
			    'fields' => 'ids',
			    'posts_per_page' => -1,
			));
			$count_projects = $projects->found_posts;

			$ids = !empty($projects->posts) ? $projects->posts : array();
			$project_ids = array(0);
			if ( $ids ) {
				foreach ($ids as $id) {
					$project_ida = apply_filters( 'wp-freeio-translations-post-ids', $id );
					if ( !empty($project_ida) && is_array($project_ida) ) {
						$project_ids = array_merge($project_ids, $project_ida );
					} else {
						$project_ids = array_merge($project_ids, array($id) );
					}
				}
			}
			$query_vars = array(
				'post_type'         => 'project_proposal',
				'posts_per_page'    => 1,
				'paged'    			=> 1,
				'post_status'       => array('publish', 'hired', 'completed', 'cancelled'),
				'meta_query'       => array(
					array(
						'key' => WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id',
						'value'     => $project_ids,
						'compare'   => 'IN',
					)
				)
			);
			$proposals = new WP_Query($query_vars);
			$proposals_count = $proposals->found_posts;
			?>
			<div class="inner-header m-0">
				<div class="proposal-count-wrapper list-item d-flex align-items-center justify-content-between text-right">
					<div class="inner">
						<?php if ( $title ) { ?>
							<span><?php echo esc_html($title); ?></span>
						<?php } ?>
						<div class="number-count"><?php echo esc_html( $proposals_count ? WP_Freeio_Mixes::format_number($proposals_count) : 0 ); ?></div>
					</div>
					<div class="icon-wrapper">
						<div class="icon">
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
								if(!empty($settings['selected_icon']['value'])){
									Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
								} else{
									echo '<i class="flaticon-car-3"></i>';
								}
							} else { ?>
								<i <?php $this->print_render_attribute_string( 'icon' ); ?>></i>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Dashboard_Employer_Proposal_Count );