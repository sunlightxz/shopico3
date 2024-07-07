<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Register_Form_Tabs extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_register_tabs';
    }

	public function get_title() {
        return esc_html__( 'Apus Register Form Tabs', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-header-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_freelancer',
            [
                'label'         => esc_html__( 'Show Freelancer Register Form', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
            ]
        );

        $this->add_control(
            'show_employer',
            [
                'label'         => esc_html__( 'Show Employer Register Form', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
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

        if ( is_user_logged_in() ) {
            echo WP_Freeio_Template_Loader::get_template_part( 'misc/loged-in' );
        } else {
            if ( $show_freelancer || $show_employer ) {
                $rand = freeio_random_key();
        ?>
                <div class="register-form register-form-wrapper box-account <?php echo esc_attr($el_class); ?>">

                   
                    <ul class="nav nav-tabs" role="tablist">
                        <?php if ( $show_freelancer ) { ?>
                            <li><a class="active" data-bs-toggle="tab" href="#apus_register_form_freelancer_<?php echo esc_attr($rand); ?>"><i class="flaticon-web-design"></i><?php esc_html_e('Driver', 'freeio'); ?></a></li>
                        <?php } ?>

                        <?php if ( $show_employer ) { ?>
                            <li><a class="<?php echo esc_attr($show_freelancer ? '' : 'active'); ?>" data-bs-toggle="tab" href="#apus_register_form_employer_<?php echo esc_attr($rand); ?>"><i class="flaticon-briefcase"></i><?php esc_html_e('User', 'freeio'); ?></a></li>
                        <?php } ?>
                    </ul>
                    

                    <div class="tab-content clearfix">
                        <?php if ( $show_freelancer ) { ?>
                            <div class="tab-pane active in" id="apus_register_form_freelancer_<?php echo esc_attr($rand); ?>">
                                <?php echo do_shortcode( '[wp_freeio_register_freelancer]' ); ?>
                            </div>
                        <?php } ?>
                        <?php if ( $show_employer ) { ?>
                            <div class="tab-pane <?php echo esc_attr( !$show_freelancer ? 'active in' : '' ); ?>" id="apus_register_form_employer_<?php echo esc_attr($rand); ?>">
                                <?php echo do_shortcode( '[wp_freeio_register_employer]' ); ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php do_action('register_form_after'); ?>
                </div>
            <?php }
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Register_Form_Tabs );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Register_Form_Tabs );
}