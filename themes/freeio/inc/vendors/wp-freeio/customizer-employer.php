<?php

function freeio_employer_customize_register( $wp_customize ) {
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
    $wp_customize->add_panel( 'freeio_settings_employer', array(
        'title' => esc_html__( 'Employers Settings', 'freeio' ),
        'priority' => 4,
    ) );

    // General Section
    $wp_customize->add_section('freeio_settings_employer_general', array(
        'title'    => esc_html__('General', 'freeio'),
        'priority' => 1,
        'panel' => 'freeio_settings_employer',
    ));

    // Breadcrumbs
    $wp_customize->add_setting('freeio_theme_options[show_employer_breadcrumbs]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_employer_breadcrumbs', array(
        'settings' => 'freeio_theme_options[show_employer_breadcrumbs]',
        'label'    => esc_html__('Breadcrumbs', 'freeio'),
        'section'  => 'freeio_settings_employer_general',
        'type'     => 'checkbox',
    ));

    // Breadcrumbs Background Color
    $wp_customize->add_setting('freeio_theme_options[employer_breadcrumb_color]', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'employer_breadcrumb_color', array(
        'label'    => esc_html__('Breadcrumbs Background Color', 'freeio'),
        'section'  => 'freeio_settings_employer_general',
        'settings' => 'freeio_theme_options[employer_breadcrumb_color]',
    )));

    // Breadcrumbs Background
    $wp_customize->add_setting('freeio_theme_options[employer_breadcrumb_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'employer_breadcrumb_image', array(
        'label'    => esc_html__('Breadcrumbs Background', 'freeio'),
        'section'  => 'freeio_settings_employer_general',
        'settings' => 'freeio_theme_options[employer_breadcrumb_image]',
    )));


    // Employer Archives
    $wp_customize->add_section('freeio_settings_employer_archive', array(
        'title'    => esc_html__('Employer Archives', 'freeio'),
        'priority' => 2,
        'panel' => 'freeio_settings_employer',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[show_shop_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'show_shop_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_employer_archive',
        'settings' => 'freeio_theme_options[show_shop_general_setting]',
    )));

    // Template Type
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[employer_archive_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_employer_archive_elementor_template', array(
            'label'   => esc_html__('Employers Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_employer_archive',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[employer_archive_elementor_template]',
        ) );
    }

    // Is Full Width
    $wp_customize->add_setting('freeio_theme_options[employer_archive_fullwidth]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_employer_archive_fullwidth', array(
        'settings' => 'freeio_theme_options[employer_archive_fullwidth]',
        'label'    => esc_html__('Is Full Width', 'freeio'),
        'section'  => 'freeio_settings_employer_archive',
        'type'     => 'checkbox',
    ));
    
    // layout
    $wp_customize->add_setting( 'freeio_theme_options[employer_archive_layout]', array(
        'default'        => 'main',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Freeio_WP_Customize_Radio_Image_Control( 
        $wp_customize, 
        'freeio_settings_employer_archive_layout', 
        array(
            'label'   => esc_html__('Layout Type', 'freeio'),
            'section' => 'freeio_settings_employer_archive',
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
            'settings' => 'freeio_theme_options[employer_archive_layout]',
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
    $wp_customize->add_setting( 'freeio_theme_options[employers_show_top_content]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_employers_show_top_content', array(
        'label'   => esc_html__('Top Content', 'freeio'),
        'section' => 'freeio_settings_employer_archive',
        'type'    => 'select',
        'choices' => $template_options,
        'settings' => 'freeio_theme_options[employers_show_top_content]',
    ) );

    // Show Offcanvas Filter
    $wp_customize->add_setting('freeio_theme_options[employers_show_offcanvas_filter]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'       => 0,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_employers_show_offcanvas_filter', array(
        'settings' => 'freeio_theme_options[employers_show_offcanvas_filter]',
        'label'    => esc_html__('Show Offcanvas Filter', 'freeio'),
        'section'  => 'freeio_settings_employer_archive',
        'type'     => 'checkbox',
    ));

    // Display Mode
    $wp_customize->add_setting( 'freeio_theme_options[employer_display_mode]', array(
        'default'        => 'grid',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_employer_display_mode', array(
        'label'   => esc_html__('Display Mode', 'freeio'),
        'section' => 'freeio_settings_employer_archive',
        'type'    => 'select',
        'choices' => array(
            'grid' => esc_html__('Grid', 'freeio'),
            'list' => esc_html__('List', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[employer_display_mode]',
    ) );
    
    // employers Columns
    $wp_customize->add_setting( 'freeio_theme_options[employer_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_employer_archive_employer_columns', array(
        'label'   => esc_html__('Employer Columns', 'freeio'),
        'section' => 'freeio_settings_employer_archive',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[employer_columns]',
    ) );

    // Pagination Type
    $wp_customize->add_setting( 'freeio_theme_options[employers_pagination]', array(
        'default'        => 'default',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_employers_pagination', array(
        'label'   => esc_html__('Pagination Type', 'freeio'),
        'section' => 'freeio_settings_employer_archive',
        'type'    => 'select',
        'choices' => array(
            'default' => esc_html__('Default', 'freeio'),
            'loadmore' => esc_html__('Load More Button', 'freeio'),
            'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[employers_pagination]',
    ) );

    // Employer Placeholder
    $wp_customize->add_setting('freeio_theme_options[employer_placeholder_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'employer_placeholder_image', array(
        'label'    => esc_html__('Employer Placeholder', 'freeio'),
        'section'  => 'freeio_settings_employer_archive',
        'settings' => 'freeio_theme_options[employer_placeholder_image]',
    )));




    // Single Employer
    $wp_customize->add_section('freeio_settings_employer_single', array(
        'title'    => esc_html__('Single Employer', 'freeio'),
        'priority' => 3,
        'panel' => 'freeio_settings_employer',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[show_shop_single_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'show_shop_single_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'settings' => 'freeio_theme_options[show_shop_single_general_setting]',
    )));

    // Template Type
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[employer_single_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_employer_single_elementor_template', array(
            'label'   => esc_html__('Employer Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_employer_single',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[employer_single_elementor_template]',
        ) );
    }

    // Header Background Image
    $wp_customize->add_setting('freeio_theme_options[employer_header_bg_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'employer_header_bg_image', array(
        'label'    => esc_html__('Header Background Image', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'settings' => 'freeio_theme_options[employer_header_bg_image]',
    )));

    // Show Employer Description
    $wp_customize->add_setting('freeio_theme_options[show_employer_description]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_employer_description', array(
        'settings' => 'freeio_theme_options[show_employer_description]',
        'label'    => esc_html__('Show Employer Description', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'type'     => 'checkbox',
    ));

    // Show Employer Gallery
    $wp_customize->add_setting('freeio_theme_options[show_employer_gallery]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_employer_gallery', array(
        'settings' => 'freeio_theme_options[show_employer_gallery]',
        'label'    => esc_html__('Show Employer Gallery', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'type'     => 'checkbox',
    ));

    // Show Employer Video
    $wp_customize->add_setting('freeio_theme_options[show_employer_video]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_employer_video', array(
        'settings' => 'freeio_theme_options[show_employer_video]',
        'label'    => esc_html__('Show Employer Video', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'type'     => 'checkbox',
    ));

    // Show Employer Members
    $wp_customize->add_setting('freeio_theme_options[show_employer_members]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_employer_members', array(
        'settings' => 'freeio_theme_options[show_employer_members]',
        'label'    => esc_html__('Show Employer Members', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'type'     => 'checkbox',
    ));

    // Show Employer Projects
    $wp_customize->add_setting('freeio_theme_options[show_employer_projects]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_employer_projects', array(
        'settings' => 'freeio_theme_options[show_employer_projects]',
        'label'    => esc_html__('Show Employer Projects', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'type'     => 'checkbox',
    ));

    // Show Employer Open Jobs
    $wp_customize->add_setting('freeio_theme_options[show_employer_open_jobs]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_employer_open_jobs', array(
        'settings' => 'freeio_theme_options[show_employer_open_jobs]',
        'label'    => esc_html__('Show Employer Open Jobs', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'type'     => 'checkbox',
    ));

    // Number Projects
    $wp_customize->add_setting('freeio_theme_options[employer_number_projects]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 3,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_employer_number_projects', array(
        'settings' => 'freeio_theme_options[employer_number_projects]',
        'label'    => esc_html__('Limit projects number', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'type'     => 'number',
    ));

    // Number Open Jobs
    $wp_customize->add_setting('freeio_theme_options[employer_number_open_jobs]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 3,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_employer_number_open_jobs', array(
        'settings' => 'freeio_theme_options[employer_number_open_jobs]',
        'label'    => esc_html__('Limit Open Jobs number', 'freeio'),
        'section'  => 'freeio_settings_employer_single',
        'type'     => 'number',
    ));

}
add_action( 'customize_register', 'freeio_employer_customize_register', 15 );