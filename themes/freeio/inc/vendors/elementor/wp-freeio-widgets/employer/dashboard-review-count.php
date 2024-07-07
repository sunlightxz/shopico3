<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Dashboard_Employer_Review_Count extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_dashboard_employer_review_count';
	}

	public function get_title() {
		return esc_html__( 'Employer Dashboard:: Review Count', 'freeio' );
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
				'default' => 'Reviews',
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
		$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
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
        $total_reviews = WP_Freeio_Review::get_total_reviews($employer_id);

		?>
		<div class="statistics dashboard-review-count <?php echo esc_attr($el_class); ?>">
			<div class="inner-header m-0">
				<div class="review-count-wrapper list-item d-flex align-items-center justify-content-between text-right">
					<div class="inner">
						<?php if ( $title ) { ?>
							<span><?php echo esc_html($title); ?></span>
						<?php } ?>
						<div class="number-count"><?php echo esc_html( $total_reviews ? WP_Freeio_Mixes::format_number($total_reviews) : 0 ); ?></div>
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
									echo '<i class="flaticon-email"></i>';
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

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Dashboard_Employer_Review_Count );
