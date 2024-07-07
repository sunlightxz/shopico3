<?php

function freeio_project_customize_register( $wp_customize ) {
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
    $wp_customize->add_panel( 'freeio_settings_project', array(
        'title' => esc_html__( 'Projects Settings', 'freeio' ),
        'priority' => 4,
    ) );

    // General Section
    $wp_customize->add_section('freeio_settings_project_general', array(
        'title'    => esc_html__('General', 'freeio'),
        'priority' => 1,
        'panel' => 'freeio_settings_project',
    ));

    // Breadcrumbs
    $wp_customize->add_setting('freeio_theme_options[show_project_breadcrumbs]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_project_breadcrumbs', array(
        'settings' => 'freeio_theme_options[show_project_breadcrumbs]',
        'label'    => esc_html__('Breadcrumbs', 'freeio'),
        'section'  => 'freeio_settings_project_general',
        'type'     => 'checkbox',
    ));

    // Breadcrumbs Background Color
    $wp_customize->add_setting('freeio_theme_options[project_breadcrumb_color]', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'project_breadcrumb_color', array(
        'label'    => esc_html__('Breadcrumbs Background Color', 'freeio'),
        'section'  => 'freeio_settings_project_general',
        'settings' => 'freeio_theme_options[project_breadcrumb_color]',
    )));

    // Breadcrumbs Background
    $wp_customize->add_setting('freeio_theme_options[project_breadcrumb_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'project_breadcrumb_image', array(
        'label'    => esc_html__('Breadcrumbs Background', 'freeio'),
        'section'  => 'freeio_settings_project_general',
        'settings' => 'freeio_theme_options[project_breadcrumb_image]',
    )));


    // Other Settings
    $wp_customize->add_setting('freeio_theme_options[freeio_settings_project_other_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'freeio_settings_project_other_setting', array(
        'label'    => esc_html__('Other Settings', 'freeio'),
        'section'  => 'freeio_settings_project_general',
        'settings' => 'freeio_theme_options[freeio_settings_project_other_setting]',
    )));

    // Show Full Phone Number
    $wp_customize->add_setting('freeio_theme_options[job_show_full_phone]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 0,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_job_show_full_phone', array(
        'settings' => 'freeio_theme_options[job_show_full_phone]',
        'label'    => esc_html__('Show Full Phone Number', 'freeio'),
        'section'  => 'freeio_settings_project_general',
        'type'     => 'checkbox',
    ));


    // Project Archives
    $wp_customize->add_section('freeio_settings_project_archive', array(
        'title'    => esc_html__('Project Archives', 'freeio'),
        'priority' => 2,
        'panel' => 'freeio_settings_project',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[projects_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'projects_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_project_archive',
        'settings' => 'freeio_theme_options[projects_general_setting]',
    )));


    // Template Type
    
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[project_archive_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_project_archive_elementor_template', array(
            'label'   => esc_html__('Projects Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_project_archive',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[project_archive_elementor_template]',
        ) );
    }


    // Is Full Width
    $wp_customize->add_setting('freeio_theme_options[project_archive_fullwidth]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_project_archive_fullwidth', array(
        'settings' => 'freeio_theme_options[project_archive_fullwidth]',
        'label'    => esc_html__('Is Full Width', 'freeio'),
        'section'  => 'freeio_settings_project_archive',
        'type'     => 'checkbox',
    ));
    

    // Projects Layout
    $wp_customize->add_setting( 'freeio_theme_options[projects_layout_type]', array(
        'default'        => 'default',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_project_archive_blog_archive', array(
        'label'   => esc_html__('Projects Layout', 'freeio'),
        'section' => 'freeio_settings_project_archive',
        'type'    => 'select',
        'choices' => array(
            'default' => esc_html__('Default', 'freeio'),
            // 'half-map' => esc_html__('Half Map', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[projects_layout_type]',
    ) );

    // layout
    $wp_customize->add_setting( 'freeio_theme_options[project_archive_layout]', array(
        'default'        => 'main',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Freeio_WP_Customize_Radio_Image_Control( 
        $wp_customize, 
        'freeio_settings_project_archive_layout', 
        array(
            'label'   => esc_html__('Layout Type', 'freeio'),
            'section' => 'freeio_settings_project_archive',
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
            'settings' => 'freeio_theme_options[project_archive_layout]',
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
    $wp_customize->add_setting( 'freeio_theme_options[projects_show_top_content]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_projects_show_top_content', array(
        'label'   => esc_html__('Top Content', 'freeio'),
        'section' => 'freeio_settings_project_archive',
        'type'    => 'select',
        'choices' => $template_options,
        'settings' => 'freeio_theme_options[projects_show_top_content]',
    ) );

    // Display Mode
    $wp_customize->add_setting( 'freeio_theme_options[project_display_mode]', array(
        'default'        => 'grid',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_project_display_mode', array(
        'label'   => esc_html__('Display Mode', 'freeio'),
        'section' => 'freeio_settings_project_archive',
        'type'    => 'select',
        'choices' => array(
            'grid' => esc_html__('Grid', 'freeio'),
            'list' => esc_html__('List', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[project_display_mode]',
    ) );

    // Projects list item style
    $wp_customize->add_setting( 'freeio_theme_options[projects_inner_list_style]', array(
        'default'        => 'list',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_projects_inner_list_style', array(
        'label'   => esc_html__('Projects list item style', 'freeio'),
        'section' => 'freeio_settings_project_archive',
        'type'    => 'select',
        'choices' => array(
            'list' => esc_html__('List Default', 'freeio'),
            'list-v1' => esc_html__('List V1', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[projects_inner_list_style]',
    ) );

    // Projects grid item style
    $wp_customize->add_setting( 'freeio_theme_options[projects_inner_grid_style]', array(
        'default'        => 'grid',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_projects_inner_grid_style', array(
        'label'   => esc_html__('Projects grid item style', 'freeio'),
        'section' => 'freeio_settings_project_archive',
        'type'    => 'select',
        'choices' => array(
            'grid' => esc_html__('Grid Default', 'freeio'),
            'grid-v1' => esc_html__('Grid V1', 'freeio'),
            'grid-v2' => esc_html__('Grid V2', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[projects_inner_grid_style]',
    ) );

    // projects Columns
    $wp_customize->add_setting( 'freeio_theme_options[project_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_project_archive_project_columns', array(
        'label'   => esc_html__('Project Columns', 'freeio'),
        'section' => 'freeio_settings_project_archive',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[project_columns]',
    ) );

    // Pagination Type
    $wp_customize->add_setting( 'freeio_theme_options[projects_pagination]', array(
        'default'        => 'default',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_projects_pagination', array(
        'label'   => esc_html__('Pagination Type', 'freeio'),
        'section' => 'freeio_settings_project_archive',
        'type'    => 'select',
        'choices' => array(
            'default' => esc_html__('Default', 'freeio'),
            'loadmore' => esc_html__('Load More Button', 'freeio'),
            'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[projects_pagination]',
    ) );

    // Project Placeholder
    $wp_customize->add_setting('freeio_theme_options[project_placeholder_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'project_placeholder_image', array(
        'label'    => esc_html__('Project Placeholder', 'freeio'),
        'section'  => 'freeio_settings_project_archive',
        'settings' => 'freeio_theme_options[project_placeholder_image]',
    )));



    // Single Project
    $wp_customize->add_section('freeio_settings_project_single', array(
        'title'    => esc_html__('Single Project', 'freeio'),
        'priority' => 3,
        'panel' => 'freeio_settings_project',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[project_single_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'project_single_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_project_single',
        'settings' => 'freeio_theme_options[project_single_general_setting]',
    )));


    // Template Type
    
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[project_single_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_project_single_elementor_template', array(
            'label'   => esc_html__('Projects Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_project_single',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[project_single_elementor_template]',
        ) );
    }


    // Project Layout
    $wp_customize->add_setting( 'freeio_theme_options[project_layout_type]', array(
        'default'        => 'v1',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_project_layout_type', array(
        'label'   => esc_html__('Project Layout', 'freeio'),
        'section' => 'freeio_settings_project_single',
        'type'    => 'select',
        'choices' => array(
            'v1' => esc_html__('Version 1', 'superio'),
            'v2' => esc_html__('Version 2', 'superio'),
            'v3' => esc_html__('Version 3', 'superio'),
        ),
        'settings' => 'freeio_theme_options[project_layout_type]',
    ) );

    // Header Background Image
    $wp_customize->add_setting('freeio_theme_options[project_header_bg_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'project_header_bg_image', array(
        'label'    => esc_html__('Header Background Image', 'freeio'),
        'section'  => 'freeio_settings_project_single',
        'settings' => 'freeio_theme_options[project_header_bg_image]',
    )));

    // Show Project Details
    $wp_customize->add_setting('freeio_theme_options[show_project_detail]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_project_detail', array(
        'settings' => 'freeio_theme_options[show_project_detail]',
        'label'    => esc_html__('Show Project Details', 'freeio'),
        'section'  => 'freeio_settings_project_single',
        'type'     => 'checkbox',
    ));

    // Show Project Description
    $wp_customize->add_setting('freeio_theme_options[show_project_description]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_project_description', array(
        'settings' => 'freeio_theme_options[show_project_description]',
        'label'    => esc_html__('Show Project Description', 'freeio'),
        'section'  => 'freeio_settings_project_single',
        'type'     => 'checkbox',
    ));

    // Show Project Attachments
    $wp_customize->add_setting('freeio_theme_options[show_project_attachments]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_project_attachments', array(
        'settings' => 'freeio_theme_options[show_project_attachments]',
        'label'    => esc_html__('Show Project Attachments', 'freeio'),
        'section'  => 'freeio_settings_project_single',
        'type'     => 'checkbox',
    ));

    // Show Project Skills
    $wp_customize->add_setting('freeio_theme_options[show_project_skills]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_project_skills', array(
        'settings' => 'freeio_theme_options[show_project_skills]',
        'label'    => esc_html__('Show Project Skills', 'freeio'),
        'section'  => 'freeio_settings_project_single',
        'type'     => 'checkbox',
    ));

    // Show Project Proposals
    $wp_customize->add_setting('freeio_theme_options[show_project_proposals]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_project_proposals', array(
        'settings' => 'freeio_theme_options[show_project_proposals]',
        'label'    => esc_html__('Show Project Proposals', 'freeio'),
        'section'  => 'freeio_settings_project_single',
        'type'     => 'checkbox',
    ));

    // Show Project FAQ
    $wp_customize->add_setting('freeio_theme_options[show_project_faq]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_project_faq', array(
        'settings' => 'freeio_theme_options[show_project_faq]',
        'label'    => esc_html__('Show Project FAQ', 'freeio'),
        'section'  => 'freeio_settings_project_single',
        'type'     => 'checkbox',
    ));

    // Show Projects Related
    $wp_customize->add_setting('freeio_theme_options[show_project_related]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_project_related', array(
        'settings' => 'freeio_theme_options[show_project_related]',
        'label'    => esc_html__('Show Projects Related', 'freeio'),
        'section'  => 'freeio_settings_project_single',
        'type'     => 'checkbox',
    ));

    // Number related projects
    $wp_customize->add_setting( 'freeio_theme_options[number_project_related]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_project_single_number_project_related', array(
        'label'   => esc_html__('Number related projects', 'freeio'),
        'section' => 'freeio_settings_project_single',
        'type'    => 'number',
        'settings' => 'freeio_theme_options[number_project_related]',
    ) );

    // Related Projects Columns
    $wp_customize->add_setting( 'freeio_theme_options[related_project_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_project_single_related_project_columns', array(
        'label'   => esc_html__('Related Projects Columns', 'freeio'),
        'section' => 'freeio_settings_project_single',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[related_project_columns]',
    ) );



    // Single Project Expired
    $wp_customize->add_section('freeio_settings_project_expired', array(
        'title'    => esc_html__('Single Project Expired', 'freeio'),
        'priority' => 4,
        'panel' => 'freeio_settings_project',
    ));

    // Image Icon
    $wp_customize->add_setting('freeio_theme_options[project_expired_icon_img]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'project_expired_icon_img', array(
        'label'    => esc_html__('Image Icon', 'freeio'),
        'section'  => 'freeio_settings_project_expired',
        'settings' => 'freeio_theme_options[project_expired_icon_img]',
    )));

    // Title
    $wp_customize->add_setting('freeio_theme_options[project_expired_title]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 'We\'re Sorry Opps! Project Expired',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_project_expired_title', array(
        'settings' => 'freeio_theme_options[project_expired_title]',
        'label'    => esc_html__('Title', 'freeio'),
        'section'  => 'freeio_settings_project_expired',
        'type'     => 'text',
    ));

    // Description
    $wp_customize->add_setting('freeio_theme_options[project_expired_description]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 'Unable to access the link. Project has been expired. Please contact the admin or who shared the link with you.',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_project_expired_description', array(
        'settings' => 'freeio_theme_options[project_expired_description]',
        'label'    => esc_html__('Description', 'freeio'),
        'section'  => 'freeio_settings_project_expired',
        'type'     => 'text',
    ));
}
add_action( 'customize_register', 'freeio_project_customize_register', 15 );