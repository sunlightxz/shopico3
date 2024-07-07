<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Job_Alert_Form extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_job_alert_form';
	}

	public function get_title() {
		return esc_html__( 'Apus Job Alert Form', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Content', 'freeio' ),
			]
		);

		$this->add_control(
            'title', [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__( 'Button Text', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your title here', 'freeio' ),
                'default' => 'Submit Listing',
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
        $widget_id = freeio_random_key();
        $email_frequency_default = WP_Freeio_Job_Alert::get_email_frequency();
        ?>
        	<div class="widget widget-alert-form">
	        	<?php if ( $title ) { ?>
	                <h2 class="widget-title"><?php echo esc_html($title); ?></h2>
	            <?php } ?>
				<form method="get" action="" class="job-alert-form form-theme">
					<div class="form-group">
					    <label for="<?php echo esc_attr( $widget_id ); ?>_title"><?php esc_html_e('Title', 'freeio'); ?></label>

					    <input type="text" name="name" class="form-control" id="<?php echo esc_attr( $widget_id ); ?>_title">
					</div><!-- /.form-group -->

					<div class="form-group">
					    <label for="<?php echo esc_attr( $widget_id ); ?>_email_frequency"><?php esc_html_e('Email Frequency', 'freeio'); ?></label>
					    <select name="email_frequency" class="form-control" id="<?php echo esc_attr( $widget_id ); ?>_email_frequency">
					        <?php if ( !empty($email_frequency_default) ) { ?>
					            <?php foreach ($email_frequency_default as $key => $value) {
					                if ( !empty($value['label']) && !empty($value['days']) ) {
					            ?>
					                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($value['label']); ?></option>

					                <?php } ?>
					            <?php } ?>
					        <?php } ?>
					    </select>
					</div><!-- /.form-group -->

					<?php
						do_action('wp-freeio-add-job-alert-form');

						wp_nonce_field('wp-freeio-add-job-alert-nonce', 'nonce');
					?>
					<?php if ( ! empty( $button_text ) ) : ?>
						<div class="form-group">
							<button class="button btn btn-theme btn-inverse w-100"><?php echo esc_attr( $button_text ); ?><i class="flaticon-right-up next"></i></button>
						</div><!-- /.form-group -->
					<?php endif; ?>
				</form>
			</div>
		<?php
	}
}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Job_Alert_Form );