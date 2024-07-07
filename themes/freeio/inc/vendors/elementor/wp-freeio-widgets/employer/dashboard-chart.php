<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Dashboard_Employer_Chart extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_dashboard_employer_chart';
	}

	public function get_title() {
		return esc_html__( 'Employer Dashboard:: Chart', 'freeio' );
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

        $query_vars = array(
			'post_type'     => 'project',
			'post_status'   => apply_filters('wp-freeio-my-projects-post-statuses', array( 'publish', 'expired', 'pending', 'pending_approve', 'pending_payment', 'draft', 'preview' )),
			'posts_per_page' => -1,
			'author'        => $user_id,
			'orderby'		=> 'date',
			'order'			=> 'DESC',
			'fields'			=> 'ids'
		);

		$projects = new WP_Query($query_vars);
		?>
		<div class="dashboard-chart <?php echo esc_attr($el_class); ?>">
			
				
			<?php
			if ( !empty($projects->posts) ) {
				freeio_load_select2();
				wp_enqueue_script( 'chart', get_template_directory_uri() . '/js/chart.min.js', array( 'jquery' ), '1.0.0', true );
			?>
				
				<div class="page_views-wrapper">
						
					<div class="page_views-wrapper">
						<canvas id="dashboard_job_chart_wrapper" data-post_id="<?php echo esc_attr($projects->posts[0]); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'freeio-job-chart-nonce' )); ?>"></canvas>
					</div>

					<div class="search-form-wrapper">
						<form class="stats-graph-search-form form-theme" method="post">
							<div class="row">
								<div class="col-12 col-sm-6">
									<div class="form-group m-0">
										<label><?php esc_html_e('Projects', 'freeio'); ?></label>
										<select class="form-control" name="post_id">
											<?php foreach ($projects->posts as $post_id) { ?>
												<option value="<?php echo esc_attr($post_id); ?>"><?php echo esc_html(get_the_title($post_id)); ?></option>
											<?php } ?>
										</select>
									</div>
								</div>

								<div class="col-12 col-sm-6 mt-3 mt-sm-0">
									<div class="form-group m-0">
										<label><?php esc_html_e('Number Days', 'freeio'); ?></label>
										<select class="form-control" name="nb_days">
											<option value="30"><?php esc_html_e('30 days', 'freeio'); ?></option>
											<option value="15" selected><?php esc_html_e('15 days', 'freeio'); ?></option>
											<option value="7"><?php esc_html_e('7 days', 'freeio'); ?></option>
										</select>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Dashboard_Employer_Chart );
