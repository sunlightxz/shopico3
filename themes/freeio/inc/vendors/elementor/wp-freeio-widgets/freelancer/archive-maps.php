<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Freelancer_Archive_Maps extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_freelancer_archive_maps';
	}

	public function get_title() {
		return esc_html__( 'Freelancer Archive:: Maps', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-freelancer-archive-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Title', 'freeio' ),
			]
		);

		$this->add_control(
            'select_height',
            [
                'label' => esc_html__( 'Customize Height', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'full' => esc_html__('Full', 'freeio'),
                    'customize' => esc_html__('Customize', 'freeio'),
                ),
                'default' => 'full'
            ]
        );

		$this->add_responsive_control(
            'height',
            [
                'label' => esc_html__( 'Height', 'freeio' ),
                'type' => Elementor\Controls_Manager::SLIDER,
                'default' => [
                    'size' => 235,
                ],
                'range' => [
                    'px' => [
                        'min' => 235,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} #freelancers-google-maps' => 'height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'select_height' => 'customize',
                ],
            ]
        );

        $this->add_control(
            'show_mobile',
            [
                'label'         => esc_html__( 'Always Show Mobile', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
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
            'section_box_style',
            [
                'label' => esc_html__( 'Style', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );


		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
		?>
		<div class="freelancer-maps <?php echo esc_attr($el_class); ?>">
			<?php if ( empty($show_mobile) ) { ?>
                <span class="action-show-filters action-mobile-map d-inline-block d-dk-none">
                    <svg width="14" height="12" viewBox="0 0 14 12" class="pre" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.06254 0.9375L3.06248 9.7035L3.78979 8.97725L4.58529 9.77275L2.50004 11.858L0.414795 9.77275L1.21029 8.97725L1.93748 9.7035L1.93754 0.9375H3.06254ZM8.12504 9.375V10.5H5.31254V9.375H8.12504ZM9.81254 6.5625V7.6875H5.31254V6.5625H9.81254ZM11.5 3.75V4.875H5.31254V3.75H11.5ZM13.1875 0.9375V2.0625H5.31254V0.9375H13.1875Z" fill="currentColor"></path>
                    </svg><?php esc_html_e('Show Map','freeio') ?>
                </span>
            <?php } ?>
            <div id="jobs-google-maps" class="<?php echo esc_attr( ($select_height == "full")?'fix-map-xl':'' ) ?>"></div>
		</div>
		<?php
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Freelancer_Archive_Maps );
