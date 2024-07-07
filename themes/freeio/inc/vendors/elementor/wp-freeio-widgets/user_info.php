<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_User_Info extends Elementor\Widget_Base {

    public function get_name() {
        return 'apus_element_user_info';
    }

    public function get_title() {
        return esc_html__( 'Apus Header User Info', 'freeio' );
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
            'show_login',
            [
                'label'         => esc_html__( 'Show Login', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
            ]
        );

        $this->add_control(
            'login_text',
            [
                'label' => esc_html__( 'Login Text', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => 'Login'
            ]
        );

        $this->add_control(
            'show_register',
            [
                'label'         => esc_html__( 'Show Register', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
            ]
        );

        $this->add_control(
            'register_text',
            [
                'label' => esc_html__( 'Register Text', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => 'Sign Up'
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout Type', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'swhich_user_role' => esc_html__('Switch User Role', 'freeio'),
                ),
                'default' => 'popup'
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
            'section_button_style',
            [
                'label' => esc_html__( 'Button', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_options',
            [
                'label' => esc_html__( 'Login', 'freeio' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
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
                'button_color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-login' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-login' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .btn-login',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_button_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_control(
                'button_color_hv',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-login:hover' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .btn-login:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_bg_color_hv',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-login:hover' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .btn-login:focus' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button_hv',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .btn-login:hover',
                    'selector' => '{{WRAPPER}} .btn-login:focus',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab 

        $this->add_control(
            'button_padding',
            [
                'label' => esc_html__( 'Padding', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .btn-login' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .btn-login' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'heading_2_options',
            [
                'label' => esc_html__( 'Register', 'freeio' ),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs( 'tabs_button_res_style' );

            $this->start_controls_tab(
                'tab_button_res_normal',
                [
                    'label' => esc_html__( 'Normal', 'freeio' ),
                ]
            );

            $this->add_control(
                'button_res_color',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-register' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_res_bg_color',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-register' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'button_res_button',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .btn-register',
                ]
            );

            $this->end_controls_tab();

            // tab hover
            $this->start_controls_tab(
                'tab_button_res_hover',
                [
                    'label' => esc_html__( 'Hover', 'freeio' ),
                ]
            );

            $this->add_control(
                'button_res_color_hv',
                [
                    'label' => esc_html__( 'Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-register:hover' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .btn-register:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_res_bg_color_hv',
                [
                    'label' => esc_html__( 'Background Color', 'freeio' ),
                    'type' => Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .btn-register:hover' => 'background-color: {{VALUE}};',
                        '{{WRAPPER}} .btn-register:focus' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Elementor\Group_Control_Border::get_type(),
                [
                    'name' => 'border_button_res_hv',
                    'label' => esc_html__( 'Border', 'freeio' ),
                    'selector' => '{{WRAPPER}} .btn-register:hover, {{WRAPPER}} .btn-register:focus',
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();
        // end tab 

        $this->add_control(
            'button_res_padding',
            [
                'label' => esc_html__( 'Padding', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .btn-register' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_res_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .btn-register' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'space_button',
            [
                'label' => esc_html__( 'Space Button', 'freeio' ),
                'type' => Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .top-wrapper-menu .btn-account + .btn-account' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_name_style',
            [
                'label' => esc_html__( 'Text', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text-color',
            [
                'label' => esc_html__( 'Color', 'freeio' ),
                'type' => Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .top-wrapper-menu .name-wrapper' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {
        $settings = $this->get_settings();

        extract( $settings );

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $userdata = get_userdata($user_id);
            $user_name = $userdata->display_name;
            $role_title = '';
            if ( WP_Freeio_User::is_employer($user_id) || WP_Freeio_User::is_freelancer($user_id) || WP_Freeio_User::is_employee($user_id) ) {
                if ( WP_Freeio_User::is_employer($user_id) ) {
                    $menu_nav = 'employer-menu';
                    $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                    $user_name = get_post_field('post_title', $employer_id);
                    $avatar = get_the_post_thumbnail( $employer_id, 'thumbnail' );
                    $role_title = esc_html__('Employer', 'freeio');
                } elseif ( WP_Freeio_User::is_employee($user_id) ) {
                    $user_id = WP_Freeio_User::get_user_id();
                    
                    $menu_nav = 'employee-menu';
                    $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                    $user_name = get_post_field('post_title', $employer_id);
                    $avatar = get_the_post_thumbnail( $employer_id, 'thumbnail' );

                    $role_title = esc_html__('Employer', 'freeio');
                } else {
                    $menu_nav = 'freelancer-menu';
                    $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
                    $user_name = get_post_field('post_title', $freelancer_id);
                    $avatar = get_the_post_thumbnail( $freelancer_id, 'thumbnail' );

                    $total_balance = WP_Freeio_Post_Type_Withdraw::get_freelancer_balance($user_id);
                    $current_balance = isset($total_balance['current_balance']) ? $total_balance['current_balance'] : 0;

                    $role_title = esc_html__('Freelancer', 'freeio');
                }
            }
            ?>
            <div class="top-wrapper-menu <?php echo esc_attr($el_class); ?>">
                <div class="infor-account d-flex align-items-center">
                    <div class="avatar-wrapper">
                        <?php if ( !empty($avatar)) {
                            echo trim($avatar);
                        } else {
                            echo get_avatar($user_id, 50);
                        } ?>
                    </div>
                    <div class="name-acount d-flex align-items-center">
                        <div class="name-wrapper">
                            <?php echo esc_html($user_name); ?>
                            <div class="balance-available">
                                <?php echo trim($role_title); ?>
                                <?php if ( isset($current_balance) ) { ?>
                                    (<?php echo WP_Freeio_Price::format_price($current_balance, true);?>)
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    if ( $layout_type == 'swhich_user_role' && (WP_Freeio_User::is_freelancer() || WP_Freeio_User::is_employer()) ) {
                        ?>
                        <div class="inner-top-menu">
                            <ul class="nav navbar-nav topmenu-menu">
                                <?php
                                $switch_user_id = get_user_meta($user_id, 'switch_user_id', true);
                                
                                if ( $switch_user_id && WP_Freeio_User::is_freelancer($switch_user_id) ) {
                                    $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($switch_user_id);
                                    ?>
                                    <li>
                                        <a href="javascript:void(0);" class="switch-user-role">
                                            <i class="flaticon-refresh"></i>
                                            <div class="switch-user-role-inner">
                                                <?php esc_html_e('Switch Account', 'freeio'); ?>
                                                <div class="role">
                                                    <?php esc_html_e('Freelancer', 'freeio'); ?>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <?php
                                } elseif ( $switch_user_id && WP_Freeio_User::is_employer($switch_user_id) ) {
                                        $employer_id = WP_Freeio_User::get_employer_by_user_id($switch_user_id);
                                        ?>
                                        <li>
                                            <a href="javascript:void(0);" class="switch-user-role">
                                                <i class="flaticon-refresh"></i>
                                                <div class="switch-user-role-inner">
                                                    <?php esc_html_e('Switch Account', 'freeio'); ?>
                                                    <div class="role">
                                                        <?php esc_html_e('Employer', 'freeio'); ?>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <?php
                                } else { ?>
                                    <li>
                                        <a href="javascript:void(0);" class="switch-user-role">
                                            <i class="flaticon-refresh"></i>
                                            <?php esc_html_e('Switch User Role', 'freeio'); ?>
                                        </a>
                                    </li>
                                    <?php
                                }?>

                                <?php

                                if ( !empty($menu_nav) && has_nav_menu( $menu_nav ) ) {
                                    $args = array(
                                        'theme_location' => $menu_nav,
                                        'container'       => false, 
                                        'menu_class' => false,
                                        'items_wrap' => '%3$s',
                                        'fallback_cb' => '',
                                        'menu_id' => '',
                                        'walker' => new Freeio_Nav_Menu()
                                    );
                                    wp_nav_menu($args);
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    } else {
                        if ( !empty($menu_nav) && has_nav_menu( $menu_nav ) ) {
                            $args = array(
                                'theme_location' => $menu_nav,
                                'container_class' => 'inner-top-menu',
                                'menu_class' => 'nav navbar-nav topmenu-menu',
                                'fallback_cb' => '',
                                'menu_id' => '',
                                'walker' => new Freeio_Nav_Menu()
                            );
                            wp_nav_menu($args);
                        }
                    }
                ?>
            </div>
        <?php } else { ?>

            <div class="top-wrapper-menu <?php echo esc_attr($el_class); ?>">
                <?php
                    $login_page_id = wp_freeio_get_option('login_page_id');
                    $login_page_id = WP_Freeio_Mixes::get_lang_post_id($login_page_id);

                    $register_page_id = wp_freeio_get_option('register_page_id');
                    $register_page_id = WP_Freeio_Mixes::get_lang_post_id($register_page_id);
                
                if ( $show_login ) {
                ?>
                    <a class="btn-account btn-login" href="<?php echo esc_url( get_permalink( $login_page_id ) ); ?>" title="<?php echo esc_attr($login_text); ?>">
                        <?php echo esc_html($login_text); ?>
                    </a>
                <?php }
                if ( $show_register ) {
                ?>
                    <a class="btn-account btn-register" href="<?php echo esc_url( get_permalink( $register_page_id ) ); ?>" title="<?php echo esc_attr($register_text); ?>">
                        <?php echo esc_html($register_text); ?>
                    </a>
                <?php } ?>
            </div>
        <?php }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_User_Info );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_User_Info );
}