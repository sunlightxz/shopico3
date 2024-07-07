<?php

//namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_User_Short_Profile extends Elementor\Widget_Base {

    public function get_name() {
        return 'apus_element_user_short_profile';
    }

    public function get_title() {
        return esc_html__( 'Apus User Short Profile', 'freeio' );
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

    }

    protected function render() {
        $settings = $this->get_settings();

        extract( $settings );

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            $userdata = get_userdata($user_id);
            $user_name = $userdata->display_name;
            $author_id = '';
            $post_status = '';
            if ( WP_Freeio_User::is_employer($user_id) || WP_Freeio_User::is_freelancer($user_id) || WP_Freeio_User::is_employee($user_id) ) {
                if ( WP_Freeio_User::is_employer($user_id) ) {
                    $menu_nav = 'employer-menu';
                    $author_id = $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                    $user_name = get_post_field('post_title', $employer_id);
                    $avatar = get_the_post_thumbnail( $employer_id, 'thumbnail' );

                    $post_status = get_post_status($employer_id);
                } elseif ( WP_Freeio_User::is_employee($user_id) ) {
                    $user_id = WP_Freeio_User::get_user_id();
                    
                    $menu_nav = 'employee-menu';
                    $author_id = $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                    $user_name = get_post_field('post_title', $employer_id);
                    $avatar = get_the_post_thumbnail( $employer_id, 'thumbnail' );

                    $post_status = get_post_status($employer_id);
                } else {
                    $menu_nav = 'freelancer-menu';
                    $author_id = $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
                    $user_name = get_post_field('post_title', $freelancer_id);
                    $avatar = get_the_post_thumbnail( $freelancer_id, 'thumbnail' );

                    $total_balance = WP_Freeio_Post_Type_Withdraw::get_freelancer_balance($user_id);
                    $current_balance = isset($total_balance['current_balance']) ? $total_balance['current_balance'] : 0;

                    $post_status = get_post_status($freelancer_id);
                }
            }
            ?>
            <?php if ( empty($show_mobile) ) { ?>
                <span class="action-show-filters action-mobile-map d-inline-block d-dk-none">
                    <svg width="14" height="12" viewBox="0 0 14 12" class="pre" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.06254 0.9375L3.06248 9.7035L3.78979 8.97725L4.58529 9.77275L2.50004 11.858L0.414795 9.77275L1.21029 8.97725L1.93748 9.7035L1.93754 0.9375H3.06254ZM8.12504 9.375V10.5H5.31254V9.375H8.12504ZM9.81254 6.5625V7.6875H5.31254V6.5625H9.81254ZM11.5 3.75V4.875H5.31254V3.75H11.5ZM13.1875 0.9375V2.0625H5.31254V0.9375H13.1875Z" fill="currentColor"></path>
                    </svg><?php esc_html_e('Show Menu','freeio') ?>
                </span>
            <?php } ?>
            <div class="element-user-dashboard">
                <div class="inner-dashboard">
                    <div class="user-short-profile-top <?php echo esc_attr( (WP_Freeio_User::is_freelancer($user_id))? 'is_freelancer': ''); ?>">
                        <div class="d-flex align-items-center">
                            <?php
                                if ( !empty($avatar) ) {
                                    ?>
                                    <div class="user-logo flex-shrink-0"><?php echo trim($avatar); ?></div>
                                    <?php
                                }
                            ?>
                            <div class="inner flex-grow-1">
                                <?php if ( $user_name ) { ?>
                                    <h3 class="title">
                                        <a href="<?php echo esc_url(get_permalink($author_id)); ?>">
                                            <?php echo trim($user_name); ?>
                                        </a>
                                    </h3>
                                <?php } ?>
                                <?php if ( WP_Freeio_User::is_freelancer($user_id) ) {
                                    $total_balance = WP_Freeio_Post_Type_Withdraw::get_freelancer_balance($user_id);
                                    $current_balance = isset($total_balance['current_balance']) ? $total_balance['current_balance'] : 0;
                                    ?>
                                    <div class="balance-available text-success">
                                        <?php echo WP_Freeio_Price::format_price($current_balance, true);?>
                                    </div>
                                    <?php
                                }
                                ?>
                                <?php if ( $post_status == 'publish' ) { ?>
                                    <div class="clearfix">
                                        <a class="view-profile" href="<?php echo esc_url(get_permalink($author_id)); ?>"><?php esc_html_e('View Profile', 'freeio'); ?></a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <?php if ( !empty($menu_nav) ) { ?>
                        <div class="user_short_profile">
                            <?php
                                $args = array(
                                    'theme_location' => $menu_nav,
                                    'container_class' => 'navbar-collapse no-padding',
                                    'menu_class' => 'menu_short_profile',
                                    'fallback_cb' => '',
                                    'walker' => new Freeio_Nav_Menu()
                                );
                                wp_nav_menu($args);
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

        <?php } else { 
            $page_id = wp_freeio_get_option('login_register_page_id');
            $page_url = get_permalink($page_id);
        ?>
            <div class="alert alert-warning not-allow-wrapper">
                <p class="account-sign-in"><?php esc_html_e( 'You need to be signed in to access this page.', 'freeio' ); ?> <a class="button" href="<?php echo esc_url( $page_url ); ?>"><?php esc_html_e( 'Sign in', 'freeio' ); ?></a></p>
            </div><!-- /.alert -->
        <?php }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_User_Short_Profile );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_User_Short_Profile );
}