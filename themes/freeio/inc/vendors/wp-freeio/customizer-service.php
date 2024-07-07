<?php

function freeio_service_customize_register( $wp_customize ) {
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
    
    $elementor_options = ['' => esc_html__('Choose a elementor template', 'freeio')];
    if ( did_action( 'elementor/loaded' ) ) {
        $ele_obj = \Elementor\Plugin::$instance;
        $templates = $ele_obj->templates_manager->get_source( 'local' )->get_items();
        
        if ( !empty( $templates ) ) {
            foreach ( $templates as $template ) {
                $elementor_options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
            }
        }
    }

    // Shop Panel
    $wp_customize->add_panel( 'freeio_settings_service', array(
        'title' => esc_html__( 'Services Settings', 'freeio' ),
        'priority' => 4,
    ) );

    // General Section
    $wp_customize->add_section('freeio_settings_service_general', array(
        'title'    => esc_html__('General', 'freeio'),
        'priority' => 1,
        'panel' => 'freeio_settings_service',
    ));

    // Breadcrumbs
    $wp_customize->add_setting('freeio_theme_options[show_service_breadcrumbs]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_service_breadcrumbs', array(
        'settings' => 'freeio_theme_options[show_service_breadcrumbs]',
        'label'    => esc_html__('Breadcrumbs', 'freeio'),
        'section'  => 'freeio_settings_service_general',
        'type'     => 'checkbox',
    ));

    // Breadcrumbs Background Color
    $wp_customize->add_setting('freeio_theme_options[service_breadcrumb_color]', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'service_breadcrumb_color', array(
        'label'    => esc_html__('Breadcrumbs Background Color', 'freeio'),
        'section'  => 'freeio_settings_service_general',
        'settings' => 'freeio_theme_options[service_breadcrumb_color]',
    )));

    // Breadcrumbs Background
    $wp_customize->add_setting('freeio_theme_options[service_breadcrumb_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'service_breadcrumb_image', array(
        'label'    => esc_html__('Breadcrumbs Background', 'freeio'),
        'section'  => 'freeio_settings_service_general',
        'settings' => 'freeio_theme_options[service_breadcrumb_image]',
    )));


    // Service Archives
    $wp_customize->add_section('freeio_settings_service_archive', array(
        'title'    => esc_html__('Service Archives', 'freeio'),
        'priority' => 2,
        'panel' => 'freeio_settings_service',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[show_service_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'show_service_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_service_archive',
        'settings' => 'freeio_theme_options[show_service_general_setting]',
    )));

    // Template Type
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[service_archive_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_service_archive_elementor_template', array(
            'label'   => esc_html__('Services Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_service_archive',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[service_archive_elementor_template]',
        ) );
    }

    // Is Full Width
    $wp_customize->add_setting('freeio_theme_options[service_archive_fullwidth]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_service_archive_fullwidth', array(
        'settings' => 'freeio_theme_options[service_archive_fullwidth]',
        'label'    => esc_html__('Is Full Width', 'freeio'),
        'section'  => 'freeio_settings_service_archive',
        'type'     => 'checkbox',
    ));
    

    // Services Layout
    $wp_customize->add_setting( 'freeio_theme_options[services_layout_type]', array(
        'default'        => 'default',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_service_archive_blog_archive', array(
        'label'   => esc_html__('Services Layout', 'freeio'),
        'section' => 'freeio_settings_service_archive',
        'type'    => 'select',
        'choices' => array(
            'default' => esc_html__('Default', 'freeio'),
            'half-map' => esc_html__('Half Map', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[services_layout_type]',
    ) );

    // layout
    $wp_customize->add_setting( 'freeio_theme_options[services_layout_sidebar]', array(
        'default'        => 'main',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Freeio_WP_Customize_Radio_Image_Control( 
        $wp_customize, 
        'freeio_settings_services_layout_sidebar', 
        array(
            'label'   => esc_html__('Layout Type', 'freeio'),
            'section' => 'freeio_settings_service_archive',
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
            'settings' => 'freeio_theme_options[services_layout_sidebar]',
            'description' => esc_html__('Select the variation you want to apply on your shop/archive page.', 'freeio'),
        ) 
    ));

    $template_options = ['' => esc_html__('No', 'freeio')];
    if ( did_action( 'elementor/loaded' ) ) {
        $ele_obj = \Elementor\Plugin::$instance;
        $templates = $ele_obj->templates_manager->get_source( 'local' )->get_items();
        
        if ( !empty( $templates ) ) {
            foreach ( $templates as $template ) {
                $template_options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
            }
        }
    }
    // Show Top Content
    $wp_customize->add_setting( 'freeio_theme_options[services_show_top_content]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_services_show_top_content', array(
        'label'   => esc_html__('Top Content', 'freeio'),
        'section' => 'freeio_settings_service_archive',
        'type'    => 'select',
        'choices' => $template_options,
        'settings' => 'freeio_theme_options[services_show_top_content]',
    ) );
    
    // Show Offcanvas Filter
    $wp_customize->add_setting('freeio_theme_options[services_show_offcanvas_filter]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'       => 0,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_services_show_offcanvas_filter', array(
        'settings' => 'freeio_theme_options[services_show_offcanvas_filter]',
        'label'    => esc_html__('Show Offcanvas Filter', 'freeio'),
        'section'  => 'freeio_settings_service_archive',
        'type'     => 'checkbox',
    ));

    // Display Mode
    $wp_customize->add_setting( 'freeio_theme_options[services_display_mode]', array(
        'default'        => 'grid',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_services_display_mode', array(
        'label'   => esc_html__('Display Mode', 'freeio'),
        'section' => 'freeio_settings_service_archive',
        'type'    => 'select',
        'choices' => array(
            'grid' => esc_html__('Grid', 'freeio'),
            'list' => esc_html__('List', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[services_display_mode]',
    ) );


    // Services list item style
    $wp_customize->add_setting( 'freeio_theme_options[services_inner_list_style]', array(
        'default'        => 'list',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_services_inner_list_style', array(
        'label'   => esc_html__('Services list item style', 'freeio'),
        'section' => 'freeio_settings_service_archive',
        'type'    => 'select',
        'choices' => array(
            'list' => esc_html__('List Default', 'freeio'),
            'list-v1' => esc_html__('List V1', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[services_inner_list_style]',
    ) );

    // Services grid item style
    $wp_customize->add_setting( 'freeio_theme_options[services_inner_grid_style]', array(
        'default'        => 'grid',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_services_inner_grid_style', array(
        'label'   => esc_html__('Services grid item style', 'freeio'),
        'section' => 'freeio_settings_service_archive',
        'type'    => 'select',
        'choices' => array(
            'grid' => esc_html__('Grid Default', 'freeio'),
            'grid-v1' => esc_html__('Grid V1', 'freeio'),
            'grid-v2' => esc_html__('Grid V2', 'freeio'),
            'grid-v3' => esc_html__('Grid V3', 'freeio'),
            'grid-v4' => esc_html__('Grid V4', 'freeio'),
            'grid-v5' => esc_html__('Grid V5', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[services_inner_grid_style]',
    ) );


    // services Columns
    $wp_customize->add_setting( 'freeio_theme_options[services_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_service_archive_services_columns', array(
        'label'   => esc_html__('Service Columns', 'freeio'),
        'section' => 'freeio_settings_service_archive',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[services_columns]',
    ) );

    // Pagination Type
    $wp_customize->add_setting( 'freeio_theme_options[services_pagination]', array(
        'default'        => 'default',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_services_pagination', array(
        'label'   => esc_html__('Pagination Type', 'freeio'),
        'section' => 'freeio_settings_service_archive',
        'type'    => 'select',
        'choices' => array(
            'default' => esc_html__('Default', 'freeio'),
            'loadmore' => esc_html__('Load More Button', 'freeio'),
            'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[services_pagination]',
    ) );

    // Service Placeholder
    $wp_customize->add_setting('freeio_theme_options[service_placeholder_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'service_placeholder_image', array(
        'label'    => esc_html__('Service Placeholder', 'freeio'),
        'section'  => 'freeio_settings_service_archive',
        'settings' => 'freeio_theme_options[service_placeholder_image]',
    )));



    // Single Service
    $wp_customize->add_section('freeio_settings_service_single', array(
        'title'    => esc_html__('Single Service', 'freeio'),
        'priority' => 3,
        'panel' => 'freeio_settings_service',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[service_single_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'service_single_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_service_single',
        'settings' => 'freeio_theme_options[service_single_general_setting]',
    )));
    
    // Template Type
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[service_single_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_service_single_elementor_template', array(
            'label'   => esc_html__('Service Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_service_single',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[service_single_elementor_template]',
        ) );
    }

    // Service Layout
    $wp_customize->add_setting( 'freeio_theme_options[service_layout_type]', array(
        'default'        => 'v1',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_service_layout_type', array(
        'label'   => esc_html__('Service Layout', 'freeio'),
        'section' => 'freeio_settings_service_single',
        'type'    => 'select',
        'choices' => array(
            'v1' => esc_html__('Version 1', 'superio'),
            'v2' => esc_html__('Version 2', 'superio'),
            'v3' => esc_html__('Version 3', 'superio'),
        ),
        'settings' => 'freeio_theme_options[service_layout_type]',
    ) );

    // Header Background Image
    $wp_customize->add_setting('freeio_theme_options[service_header_bg_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'service_header_bg_image', array(
        'label'    => esc_html__('Header Background Image', 'freeio'),
        'section'  => 'freeio_settings_service_single',
        'settings' => 'freeio_theme_options[service_header_bg_image]',
    )));


    // Show Service Details
    $wp_customize->add_setting('freeio_theme_options[show_service_detail]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_service_detail', array(
        'settings' => 'freeio_theme_options[show_service_detail]',
        'label'    => esc_html__('Show Service Details', 'freeio'),
        'section'  => 'freeio_settings_service_single',
        'type'     => 'checkbox',
    ));

    // Show Service Gallery
    $wp_customize->add_setting('freeio_theme_options[show_service_gallery]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_service_gallery', array(
        'settings' => 'freeio_theme_options[show_service_gallery]',
        'label'    => esc_html__('Show Service Gallery', 'freeio'),
        'section'  => 'freeio_settings_service_single',
        'type'     => 'checkbox',
    ));

    // Show Service Description
    $wp_customize->add_setting('freeio_theme_options[show_service_description]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_service_description', array(
        'settings' => 'freeio_theme_options[show_service_description]',
        'label'    => esc_html__('Show Service Description', 'freeio'),
        'section'  => 'freeio_settings_service_single',
        'type'     => 'checkbox',
    ));

    // Show Service Video
    $wp_customize->add_setting('freeio_theme_options[show_service_video]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_service_video', array(
        'settings' => 'freeio_theme_options[show_service_video]',
        'label'    => esc_html__('Show Service Video', 'freeio'),
        'section'  => 'freeio_settings_service_single',
        'type'     => 'checkbox',
    ));

    // Show Service FAQ
    $wp_customize->add_setting('freeio_theme_options[show_service_faq]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_service_faq', array(
        'settings' => 'freeio_theme_options[show_service_faq]',
        'label'    => esc_html__('Show Service FAQ', 'freeio'),
        'section'  => 'freeio_settings_service_single',
        'type'     => 'checkbox',
    ));

    // Show Services Related
    $wp_customize->add_setting('freeio_theme_options[show_service_related]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_service_related', array(
        'settings' => 'freeio_theme_options[show_service_related]',
        'label'    => esc_html__('Show Services Related', 'freeio'),
        'section'  => 'freeio_settings_service_single',
        'type'     => 'checkbox',
    ));

    // Number related services
    $wp_customize->add_setting( 'freeio_theme_options[number_service_related]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_service_single_number_service_related', array(
        'label'   => esc_html__('Number related services', 'freeio'),
        'section' => 'freeio_settings_service_single',
        'type'    => 'number',
        'settings' => 'freeio_theme_options[number_service_related]',
    ) );

    // Related Services Columns
    $wp_customize->add_setting( 'freeio_theme_options[related_services_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_service_single_related_services_columns', array(
        'label'   => esc_html__('Related Services Columns', 'freeio'),
        'section' => 'freeio_settings_service_single',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[related_services_columns]',
    ) );



    // Single Service Expired
    $wp_customize->add_section('freeio_settings_service_expired', array(
        'title'    => esc_html__('Single Service Expired', 'freeio'),
        'priority' => 4,
        'panel' => 'freeio_settings_service',
    ));

    // Image Icon
    $wp_customize->add_setting('freeio_theme_options[service_expired_icon_img]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'service_expired_icon_img', array(
        'label'    => esc_html__('Image Icon', 'freeio'),
        'section'  => 'freeio_settings_service_expired',
        'settings' => 'freeio_theme_options[service_expired_icon_img]',
    )));

    // Title
    $wp_customize->add_setting('freeio_theme_options[service_expired_title]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 'We\'re Sorry Opps! Service Expired',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_service_expired_title', array(
        'settings' => 'freeio_theme_options[service_expired_title]',
        'label'    => esc_html__('Title', 'freeio'),
        'section'  => 'freeio_settings_service_expired',
        'type'     => 'text',
    ));

    // Description
    $wp_customize->add_setting('freeio_theme_options[service_expired_description]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 'Unable to access the link. Service has been expired. Please contact the admin or who shared the link with you.',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_service_expired_description', array(
        'settings' => 'freeio_theme_options[service_expired_description]',
        'label'    => esc_html__('Description', 'freeio'),
        'section'  => 'freeio_settings_service_expired',
        'type'     => 'text',
    ));
}
add_action( 'customize_register', 'freeio_service_customize_register', 15 );