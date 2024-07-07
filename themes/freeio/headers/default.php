<header id="apus-header" class="apus-header header-default d-none d-xl-block" role="banner">
    <div class="<?php echo (freeio_get_config('keep_header') ? 'main-sticky-header-wrapper' : ''); ?>">
        <div class="<?php echo (freeio_get_config('keep_header') ? 'main-sticky-header' : ''); ?>">
            <div class="container">
                <div class="row d-flex align-items-center">
                    <div class="col-xl-3 col-12">
                        <div class="logo-in-theme">
                            <div class="logo logo-theme">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" >
                                    <img src="<?php echo esc_url( get_template_directory_uri().'/images/logo.svg'); ?>" alt="<?php bloginfo( 'name' ); ?>">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-9 d-flex justify-content-end">
                        <?php if ( has_nav_menu( 'primary' ) ) : ?>
                            <div class="main-menu">
                                <nav data-duration="400" class="apus-megamenu animate navbar navbar-expand-lg" role="navigation">
                                <?php
                                    $args = array(
                                        'theme_location' => 'primary',
                                        'container_class' => 'collapse navbar-collapse no-padding',
                                        'menu_class' => 'nav navbar-nav megamenu effect1',
                                        'fallback_cb' => '',
                                        'menu_id' => 'primary-menu',
                                        'walker' => new Freeio_Nav_Menu()
                                    );
                                    wp_nav_menu($args);
                                ?>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>