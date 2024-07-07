<div id="apus-mobile-menu" class="apus-offcanvas d-block d-xl-none"> 
    <div class="apus-offcanvas-header d-flex align-items-center">
        <div class="title">
            <?php echo esc_html__('Menu','freeio'); ?>
        </div>
        <span class="close-offcanvas ms-auto d-flex align-items-center justify-content-center"><i class="ti-close"></i></span>
    </div>
    <div class="apus-offcanvas-body flex-column d-flex">

            <div class="offcanvas-content">
                <div class="middle-offcanvas">

                    <nav id="menu-main-menu-navbar" class="navbar navbar-offcanvas" role="navigation">
                        <?php
                            $mobile_menu = 'primary';
                            $menus = get_nav_menu_locations();
                            if( !empty($menus['mobile-primary']) && wp_get_nav_menu_items($menus['mobile-primary'])) {
                                $mobile_menu = 'mobile-primary';
                            }
                            $args = array(
                                'theme_location' => $mobile_menu,
                                'container_class' => '',
                                'menu_class' => '',
                                'fallback_cb' => '',
                                'menu_id' => '',
                                'container' => 'div',
                                'container_id' => 'mobile-menu-container',
                                'walker' => new Freeio_Mobile_Menu()
                            );
                            wp_nav_menu($args);

                        ?>
                        <?php 
                        if ( freeio_get_config('header_mobile_login', true) && freeio_is_freeio_activated() ) {
                            if( !is_user_logged_in() ){ ?>
                                <?php
                                    $login_page_id = wp_freeio_get_option('login_page_id');
                                    $login_page_id = WP_Freeio_Mixes::get_lang_post_id($login_page_id);

                                    $register_page_id = wp_freeio_get_option('register_page_id');
                                    $register_page_id = WP_Freeio_Mixes::get_lang_post_id($register_page_id);
                                ?>
                                <ul class="menu-account-mobile">
                                    <li>
                                        <a href="<?php echo esc_url( get_permalink( $login_page_id ) ); ?>" title="<?php echo esc_attr__('Login','freeio') ?>">
                                            <?php echo esc_html__('Login','freeio') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo esc_url( get_permalink( $register_page_id ) ); ?>" title="<?php echo esc_attr__('Register','freeio') ?>">
                                            <?php echo esc_html__('Register','freeio') ?>
                                        </a>
                                    </li>
                                </ul>
                            <?php } ?>
                        <?php } ?>
                    </nav>
                </div>
            </div>
    </div>
</div>
<div class="over-dark"></div>