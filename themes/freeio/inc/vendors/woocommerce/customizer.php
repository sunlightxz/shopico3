<?php

function freeio_woo_customize_register( $wp_customize ) {
    global $wp_registered_sidebars;
    $sidebars = array();

    if ( is_admin() && !empty($wp_registered_sidebars) ) {
        foreach ($wp_registered_sidebars as $sidebar) {
            $sidebars[$sidebar['id']] = $sidebar['name'];
        }
    }
    $columns = array( '1' => esc_html__('1 Column', 'freeio'),
        '2' => esc_html__('2 Columns', 'freeio'),
        '3' => esc_html__('3 Columns', 'freeio'),
        '4' => esc_html__('4 Columns', 'freeio'),
        '5' => esc_html__('5 Columns', 'freeio'),
        '6' => esc_html__('6 Columns', 'freeio'),
        '7' => esc_html__('7 Columns', 'freeio'),
        '8' => esc_html__('8 Columns', 'freeio'),
    );
    
    // Shop Panel
    $wp_customize->add_panel( 'freeio_settings_shop', array(
        'title' => esc_html__( 'Shop Settings', 'freeio' ),
        'priority' => 4,
    ) );

    // General Section
    $wp_customize->add_section('freeio_settings_shop_general', array(
        'title'    => esc_html__('General', 'freeio'),
        'priority' => 1,
        'panel' => 'freeio_settings_shop',
    ));

    // Breadcrumbs
    $wp_customize->add_setting('freeio_theme_options[show_product_breadcrumbs]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_product_breadcrumbs', array(
        'settings' => 'freeio_theme_options[show_product_breadcrumbs]',
        'label'    => esc_html__('Breadcrumbs', 'freeio'),
        'section'  => 'freeio_settings_shop_general',
        'type'     => 'checkbox',
    ));

    // Breadcrumbs Background Color
    $wp_customize->add_setting('freeio_theme_options[woo_breadcrumb_color]', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'woo_breadcrumb_color', array(
        'label'    => esc_html__('Breadcrumbs Background Color', 'freeio'),
        'section'  => 'freeio_settings_shop_general',
        'settings' => 'freeio_theme_options[woo_breadcrumb_color]',
    )));

    // Breadcrumbs Background
    $wp_customize->add_setting('freeio_theme_options[woo_breadcrumb_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'woo_breadcrumb_image', array(
        'label'    => esc_html__('Breadcrumbs Background', 'freeio'),
        'section'  => 'freeio_settings_shop_general',
        'settings' => 'freeio_theme_options[woo_breadcrumb_image]',
    )));


    // Product Archives
    $wp_customize->add_section('freeio_settings_shop_archive', array(
        'title'    => esc_html__('Product Archives', 'freeio'),
        'priority' => 2,
        'panel' => 'freeio_settings_shop',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[show_shop_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'show_shop_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_shop_archive',
        'settings' => 'freeio_theme_options[show_shop_general_setting]',
    )));


    // Show Shop/Category Title ?
    $wp_customize->add_setting('freeio_theme_options[show_shop_cat_title]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_shop_cat_title', array(
        'settings' => 'freeio_theme_options[show_shop_cat_title]',
        'label'    => esc_html__('Show Shop/Category Title ?', 'freeio'),
        'section'  => 'freeio_settings_shop_archive',
        'type'     => 'checkbox',
    ));

    // Display Mode
    $wp_customize->add_setting( 'freeio_theme_options[product_display_mode]', array(
        'default'        => 'grid',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_archive_blog_archive', array(
        'label'   => esc_html__('Display Mode', 'freeio'),
        'section' => 'freeio_settings_shop_archive',
        'type'    => 'select',
        'choices' => array(
            'grid' => esc_html__('Grid', 'freeio'),
            'list' => esc_html__('List', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[product_display_mode]',
    ) );

    // products Columns
    $wp_customize->add_setting( 'freeio_theme_options[product_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_archive_product_columns', array(
        'label'   => esc_html__('Product Columns', 'freeio'),
        'section' => 'freeio_settings_shop_archive',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[product_columns]',
    ) );

    // Number of Products Per Page
    $wp_customize->add_setting( 'freeio_theme_options[number_products_per_page]', array(
        'default'        => '12',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_archive_number_products_per_page', array(
        'label'   => esc_html__('Number of Products Per Page', 'freeio'),
        'section' => 'freeio_settings_shop_archive',
        'type'    => 'number',
        'settings' => 'freeio_theme_options[number_products_per_page]',
    ) );

    // Enable Swap Image
    $wp_customize->add_setting('freeio_theme_options[enable_swap_image]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'       => '1',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_enable_swap_image', array(
        'settings' => 'freeio_theme_options[enable_swap_image]',
        'label'    => esc_html__('Enable Swap Image', 'freeio'),
        'section'  => 'freeio_settings_shop_archive',
        'type'     => 'checkbox',
    ));

    // Sidebar Setting ?
    $wp_customize->add_setting('freeio_theme_options[show_shop_sidebar_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'show_shop_sidebar_setting', array(
        'label'    => esc_html__('Sidebar Settings', 'freeio'),
        'section'  => 'freeio_settings_shop_archive',
        'settings' => 'freeio_theme_options[show_shop_sidebar_setting]',
    )));

    // layout
    $wp_customize->add_setting( 'freeio_theme_options[product_archive_layout]', array(
        'default'        => 'main',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Freeio_WP_Customize_Radio_Image_Control( 
        $wp_customize, 
        'freeio_settings_shop_archive_layout', 
        array(
            'label'   => esc_html__('Layout Type', 'freeio'),
            'section' => 'freeio_settings_shop_archive',
            'type'    => 'select',
            'choices' => array(
                'main' => array(
                    'title' => esc_html__('Main Only', 'freeio'),
                    'img' => get_template_directory_uri() . '/inc/assets/images/screen1.png'
                ),
                'left-main' => array(
                    'title' => esc_html__('Left - Main Sidebar', 'freeio'),
                    'img' => get_template_directory_uri() . '/inc/assets/images/screen2.png'
                ),
                'main-right' => array(
                    'title' => esc_html__('Main - Right Sidebar', 'freeio'),
                    'img' => get_template_directory_uri() . '/inc/assets/images/screen3.png'
                ),
            ),
            'settings' => 'freeio_theme_options[product_archive_layout]',
            'description' => esc_html__('Select the variation you want to apply on your shop/archive page.', 'freeio'),
        ) 
    ));

    // Is Full Width
    $wp_customize->add_setting('freeio_theme_options[product_archive_fullwidth]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_product_archive_fullwidth', array(
        'settings' => 'freeio_theme_options[product_archive_fullwidth]',
        'label'    => esc_html__('Is Full Width', 'freeio'),
        'section'  => 'freeio_settings_shop_archive',
        'type'     => 'checkbox',
    ));

    

    // Left Sidebar
    $wp_customize->add_setting( 'freeio_theme_options[product_archive_left_sidebar]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_archive_left_sidebar', array(
        'label'   => esc_html__('Archive Left Sidebar', 'freeio'),
        'section' => 'freeio_settings_shop_archive',
        'type'    => 'select',
        'choices' => $sidebars,
        'settings' => 'freeio_theme_options[product_archive_left_sidebar]',
        'description' => esc_html__('Choose a sidebar for left sidebar', 'freeio'),
    ) );

    // Right Sidebar
    $wp_customize->add_setting( 'freeio_theme_options[product_archive_right_sidebar]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_archive_right_sidebar', array(
        'label'   => esc_html__('Archive Right Sidebar', 'freeio'),
        'section' => 'freeio_settings_shop_archive',
        'type'    => 'select',
        'choices' => $sidebars,
        'settings' => 'freeio_theme_options[product_archive_right_sidebar]',
        'description' => esc_html__('Choose a sidebar for right sidebar', 'freeio'),
    ) );




    // Single Product
    $wp_customize->add_section('freeio_settings_shop_single', array(
        'title'    => esc_html__('Single Product', 'freeio'),
        'priority' => 3,
        'panel' => 'freeio_settings_shop',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[show_shop_single_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'show_shop_single_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_shop_single',
        'settings' => 'freeio_theme_options[show_shop_single_general_setting]',
    )));

    // Thumbnails Position
    $wp_customize->add_setting( 'freeio_theme_options[product_thumbs_position]', array(
        'default'        => 'thumbnails-bottom',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_single_thumbs_position', array(
        'label'   => esc_html__('Thumbnails Position', 'freeio'),
        'section' => 'freeio_settings_shop_single',
        'type'    => 'select',
        'choices' => array(
            'thumbnails-left' => esc_html__('Thumbnails Left', 'freeio'),
            'thumbnails-right' => esc_html__('Thumbnails Right', 'freeio'),
            'thumbnails-bottom' => esc_html__('Thumbnails Bottom', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[product_thumbs_position]',
    ) );

    // Number Thumbnails Per Row
    $wp_customize->add_setting( 'freeio_theme_options[number_product_thumbs]', array(
        'default'        => '5',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_single_number_product_thumbs', array(
        'label'   => esc_html__('Number Thumbnails Per Row', 'freeio'),
        'section' => 'freeio_settings_shop_single',
        'type'    => 'number',
        'settings' => 'freeio_theme_options[number_product_thumbs]',
    ) );

    // Show Social Share
    $wp_customize->add_setting('freeio_theme_options[show_product_social_share]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_product_social_share', array(
        'settings' => 'freeio_theme_options[show_product_social_share]',
        'label'    => esc_html__('Show Social Share', 'freeio'),
        'section'  => 'freeio_settings_shop_single',
        'type'     => 'checkbox',
    ));

    // Show Product Review Tab
    $wp_customize->add_setting('freeio_theme_options[show_product_review_tab]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_product_review_tab', array(
        'settings' => 'freeio_theme_options[show_product_review_tab]',
        'label'    => esc_html__('Show Product Review Tab', 'freeio'),
        'section'  => 'freeio_settings_shop_single',
        'type'     => 'checkbox',
    ));

    // Sidebar Setting ?
    $wp_customize->add_setting('freeio_theme_options[show_shop_single_sidebar_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'show_shop_single_sidebar_setting', array(
        'label'    => esc_html__('Sidebar Settings', 'freeio'),
        'section'  => 'freeio_settings_shop_single',
        'settings' => 'freeio_theme_options[show_shop_single_sidebar_setting]',
    )));

    // layout
    $wp_customize->add_setting( 'freeio_theme_options[product_single_layout]', array(
        'default'        => 'left-main',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_single_layout', array(
        'label'   => esc_html__('Layout Type', 'freeio'),
        'section' => 'freeio_settings_shop_single',
        'type'    => 'select',
        'choices' => array(
            'main' => esc_html__('Main Only', 'freeio'),
            'left-main' => esc_html__('Left - Main Sidebar', 'freeio'),
            'main-right' => esc_html__('Main - Right Sidebar', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[product_single_layout]',
        'description' => esc_html__('Select the variation you want to apply on your blog.', 'freeio'),
    ) );

    // Is Full Width
    $wp_customize->add_setting('freeio_theme_options[product_single_fullwidth]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_product_single_fullwidth', array(
        'settings' => 'freeio_theme_options[product_single_fullwidth]',
        'label'    => esc_html__('Is Full Width', 'freeio'),
        'section'  => 'freeio_settings_shop_single',
        'type'     => 'checkbox',
    ));

    // Left Sidebar
    $wp_customize->add_setting( 'freeio_theme_options[product_single_left_sidebar]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_single_left_sidebar', array(
        'label'   => esc_html__('Single Left Sidebar', 'freeio'),
        'section' => 'freeio_settings_shop_single',
        'type'    => 'select',
        'choices' => $sidebars,
        'settings' => 'freeio_theme_options[product_single_left_sidebar]',
        'description' => esc_html__('Choose a sidebar for left sidebar', 'freeio'),
    ) );

    // Right Sidebar
    $wp_customize->add_setting( 'freeio_theme_options[product_single_right_sidebar]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_single_right_sidebar', array(
        'label'   => esc_html__('Single Right Sidebar', 'freeio'),
        'section' => 'freeio_settings_shop_single',
        'type'    => 'select',
        'choices' => $sidebars,
        'settings' => 'freeio_theme_options[product_single_right_sidebar]',
        'description' => esc_html__('Choose a sidebar for right sidebar', 'freeio'),
    ) );

    // Product Block Setting ?
    $wp_customize->add_setting('freeio_theme_options[show_shop_single_product_block_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'show_shop_single_product_block_setting', array(
        'label'    => esc_html__('Product Block Settings', 'freeio'),
        'section'  => 'freeio_settings_shop_single',
        'settings' => 'freeio_theme_options[show_shop_single_product_block_setting]',
    )));

    // Show Products Related
    $wp_customize->add_setting('freeio_theme_options[show_product_related]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_product_related', array(
        'settings' => 'freeio_theme_options[show_product_related]',
        'label'    => esc_html__('Show Products Related', 'freeio'),
        'section'  => 'freeio_settings_shop_single',
        'type'     => 'checkbox',
    ));

    // Number related products
    $wp_customize->add_setting( 'freeio_theme_options[number_product_related]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_single_number_product_related', array(
        'label'   => esc_html__('Number related products', 'freeio'),
        'section' => 'freeio_settings_shop_single',
        'type'    => 'number',
        'settings' => 'freeio_theme_options[number_product_related]',
    ) );

    // Related Products Columns
    $wp_customize->add_setting( 'freeio_theme_options[related_product_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_single_related_product_columns', array(
        'label'   => esc_html__('Related Products Columns', 'freeio'),
        'section' => 'freeio_settings_shop_single',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[related_product_columns]',
    ) );

    // Show Products upsells
    $wp_customize->add_setting('freeio_theme_options[show_product_upsells]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_product_upsells', array(
        'settings' => 'freeio_theme_options[show_product_upsells]',
        'label'    => esc_html__('Show Products upsells', 'freeio'),
        'section'  => 'freeio_settings_shop_single',
        'type'     => 'checkbox',
    ));

    // Upsells Products Columns
    $wp_customize->add_setting( 'freeio_theme_options[upsells_product_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_shop_single_upsells_product_columns', array(
        'label'   => esc_html__('Upsells Products Columns', 'freeio'),
        'section' => 'freeio_settings_shop_single',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[upsells_product_columns]',
    ) );
}
add_action( 'customize_register', 'freeio_woo_customize_register', 15 );