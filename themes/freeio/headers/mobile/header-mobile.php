<div id="apus-header-mobile" class="header-mobile d-block d-xl-none clearfix">   
    <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-5">
                    <?php
                        $logo_url = freeio_get_config('media-mobile-logo');
                    ?>
                    <?php if( !empty($logo_url) ): ?>
                        <div class="logo">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>">
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="logo logo-theme">
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <img src="<?php echo esc_url( get_template_directory_uri().'/images/logo.svg'); ?>" alt="<?php bloginfo( 'name' ); ?>">
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-7 d-flex align-items-center justify-content-end">

                        <?php
                            if ( freeio_get_config('header_mobile_login', true) && freeio_is_freeio_activated() ) {
                                if ( is_user_logged_in() ) {
                                    $user_id = get_current_user_id();
                                    $userdata = get_userdata($user_id);
                                    $user_name = $userdata->display_name;
                                    if ( WP_Freeio_User::is_employer($user_id) || WP_Freeio_User::is_freelancer($user_id) || WP_Freeio_User::is_employee($user_id) ) {
                                        if ( WP_Freeio_User::is_employer($user_id) ) {
                                            $menu_nav = 'employer-menu';
                                            $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                                            $user_name = get_post_field('post_title', $employer_id);
                                            $avatar = get_the_post_thumbnail( $employer_id, 'thumbnail' );
                                        } elseif ( WP_Freeio_User::is_employee($user_id) ) {
                                            $user_id = WP_Freeio_User::get_user_id();
                                            
                                            $menu_nav = 'employee-menu';
                                            $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                                            $user_name = get_post_field('post_title', $employer_id);
                                            $avatar = get_the_post_thumbnail( $employer_id, 'thumbnail' );
                                        } else {
                                            $menu_nav = 'freelancer-menu';
                                            $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
                                            $user_name = get_post_field('post_title', $freelancer_id);
                                            $avatar = get_the_post_thumbnail( $freelancer_id, 'thumbnail' );
                                        }
                                    }
                                    ?>
                                    <div class="top-wrapper-menu">
                                        <div class="avatar-wrapper">
                                            <?php if ( !empty($avatar)) {
                                                echo trim($avatar);
                                            } else {
                                                echo get_avatar($user_id, 54);
                                            } ?>
                                        </div>
                                        <?php
                                            $show_swhich_user_role = freeio_get_config('show_swhich_user_role');
                                            if ( $show_swhich_user_role && (WP_Freeio_User::is_freelancer() || WP_Freeio_User::is_employer()) ) {
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
                            <?php } else {
                            ?>
                                <div class="top-wrapper-menu">
                                    <?php
                                        $login_page_id = wp_freeio_get_option('login_page_id');
                                        $login_page_id = WP_Freeio_Mixes::get_lang_post_id($login_page_id);                                    
                                    ?>
                                    <a class="btn-account btn-login " href="<?php echo esc_url( get_permalink( $login_page_id ) ); ?>" title="<?php echo esc_attr__('Login','freeio') ?>">
                                        <?php echo esc_html__('Login','freeio') ?>
                                    </a>
                                </div>

                            <?php }
                        }
                        ?>

                        <?php if ( freeio_get_config('header_mobile_menu', true) ) { ?>
                            <a href="#navbar-offcanvas" class="btn-showmenu">
                                <i class="mobile-menu-icon"></i>
                            </a>
                        <?php } ?>
                </div>
            </div>
    </div>
</div>