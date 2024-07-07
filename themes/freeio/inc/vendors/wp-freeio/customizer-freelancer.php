<?php

function freeio_freelancer_customize_register( $wp_customize ) {
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
    $wp_customize->add_panel( 'freeio_settings_freelancer', array(
        'title' => esc_html__( 'Freelancers Settings', 'freeio' ),
        'priority' => 4,
    ) );

    // General Section
    $wp_customize->add_section('freeio_settings_freelancer_general', array(
        'title'    => esc_html__('General', 'freeio'),
        'priority' => 1,
        'panel' => 'freeio_settings_freelancer',
    ));

    // Breadcrumbs
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_breadcrumbs]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_breadcrumbs', array(
        'settings' => 'freeio_theme_options[show_freelancer_breadcrumbs]',
        'label'    => esc_html__('Breadcrumbs', 'freeio'),
        'section'  => 'freeio_settings_freelancer_general',
        'type'     => 'checkbox',
    ));

    // Breadcrumbs Background Color
    $wp_customize->add_setting('freeio_theme_options[freelancer_breadcrumb_color]', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'freelancer_breadcrumb_color', array(
        'label'    => esc_html__('Breadcrumbs Background Color', 'freeio'),
        'section'  => 'freeio_settings_freelancer_general',
        'settings' => 'freeio_theme_options[freelancer_breadcrumb_color]',
    )));

    // Breadcrumbs Background
    $wp_customize->add_setting('freeio_theme_options[freelancer_breadcrumb_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'freelancer_breadcrumb_image', array(
        'label'    => esc_html__('Breadcrumbs Background', 'freeio'),
        'section'  => 'freeio_settings_freelancer_general',
        'settings' => 'freeio_theme_options[freelancer_breadcrumb_image]',
    )));


    // Freelancer Archives
    $wp_customize->add_section('freeio_settings_freelancer_archive', array(
        'title'    => esc_html__('Freelancer Archives', 'freeio'),
        'priority' => 2,
        'panel' => 'freeio_settings_freelancer',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[freelancers_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'freelancers_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_freelancer_archive',
        'settings' => 'freeio_theme_options[freelancers_general_setting]',
    )));

    // Template Type
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[freelancer_archive_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_freelancer_archive_elementor_template', array(
            'label'   => esc_html__('Freelancers Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_freelancer_archive',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[freelancer_archive_elementor_template]',
        ) );
    }

    // Is Full Width
    $wp_customize->add_setting('freeio_theme_options[freelancer_archive_fullwidth]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_freelancer_archive_fullwidth', array(
        'settings' => 'freeio_theme_options[freelancer_archive_fullwidth]',
        'label'    => esc_html__('Is Full Width', 'freeio'),
        'section'  => 'freeio_settings_freelancer_archive',
        'type'     => 'checkbox',
    ));

    // Freelancers Layout
    $wp_customize->add_setting( 'freeio_theme_options[freelancers_layout_type]', array(
        'default'        => 'default',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancer_archive_blog_archive', array(
        'label'   => esc_html__('Freelancers Layout', 'freeio'),
        'section' => 'freeio_settings_freelancer_archive',
        'type'    => 'select',
        'choices' => array(
            'default' => esc_html__('Default', 'freeio'),
            'half-map' => esc_html__('Half Map', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[freelancers_layout_type]',
    ) );

    // layout
    $wp_customize->add_setting( 'freeio_theme_options[freelancer_archive_layout]', array(
        'default'        => 'main',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Freeio_WP_Customize_Radio_Image_Control( 
        $wp_customize, 
        'freeio_settings_freelancer_archive_layout', 
        array(
            'label'   => esc_html__('Layout Type', 'freeio'),
            'section' => 'freeio_settings_freelancer_archive',
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
            'settings' => 'freeio_theme_options[freelancer_archive_layout]',
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
    $wp_customize->add_setting( 'freeio_theme_options[freelancers_show_top_content]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancers_show_top_content', array(
        'label'   => esc_html__('Top Content', 'freeio'),
        'section' => 'freeio_settings_freelancer_archive',
        'type'    => 'select',
        'choices' => $template_options,
        'settings' => 'freeio_theme_options[freelancers_show_top_content]',
    ) );

    // Show Offcanvas Filter
    $wp_customize->add_setting('freeio_theme_options[freelancers_show_offcanvas_filter]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'       => 0,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_freelancers_show_offcanvas_filter', array(
        'settings' => 'freeio_theme_options[freelancers_show_offcanvas_filter]',
        'label'    => esc_html__('Show Offcanvas Filter', 'freeio'),
        'section'  => 'freeio_settings_freelancer_archive',
        'type'     => 'checkbox',
    ));

    // Display Mode
    $wp_customize->add_setting( 'freeio_theme_options[freelancer_display_mode]', array(
        'default'        => 'grid',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancer_display_mode', array(
        'label'   => esc_html__('Display Mode', 'freeio'),
        'section' => 'freeio_settings_freelancer_archive',
        'type'    => 'select',
        'choices' => array(
            'grid' => esc_html__('Grid', 'freeio'),
            'list' => esc_html__('List', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[freelancer_display_mode]',
    ) );

    // Freelancers grid item style
    $wp_customize->add_setting( 'freeio_theme_options[freelancers_inner_grid_style]', array(
        'default'        => 'grid',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancers_inner_grid_style', array(
        'label'   => esc_html__('Freelancers grid item style', 'freeio'),
        'section' => 'freeio_settings_freelancer_archive',
        'type'    => 'select',
        'choices' => array(
            'grid' => esc_html__('Grid Default', 'freeio'),
            'grid-v1' => esc_html__('Grid V1', 'freeio'),
            'grid-v2' => esc_html__('Grid V2', 'freeio'),
            'grid-v3' => esc_html__('Grid V3', 'freeio'),
            'grid-v4' => esc_html__('Grid V3', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[freelancers_inner_grid_style]',
    ) );

    // Freelancers grid item style
    $wp_customize->add_setting( 'freeio_theme_options[freelancers_inner_list_style]', array(
        'default'        => 'list',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancers_inner_list_style', array(
        'label'   => esc_html__('Freelancers grid item style', 'freeio'),
        'section' => 'freeio_settings_freelancer_archive',
        'type'    => 'select',
        'choices' => array(
            'list' => esc_html__('List Default', 'freeio'),
            'list-v1' => esc_html__('List V1', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[freelancers_inner_list_style]',
    ) );

    // freelancers Columns
    $wp_customize->add_setting( 'freeio_theme_options[freelancer_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancer_archive_freelancer_columns', array(
        'label'   => esc_html__('Freelancer Columns', 'freeio'),
        'section' => 'freeio_settings_freelancer_archive',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[freelancer_columns]',
    ) );

    // Pagination Type
    $wp_customize->add_setting( 'freeio_theme_options[freelancers_pagination]', array(
        'default'        => 'default',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancers_pagination', array(
        'label'   => esc_html__('Pagination Type', 'freeio'),
        'section' => 'freeio_settings_freelancer_archive',
        'type'    => 'select',
        'choices' => array(
            'default' => esc_html__('Default', 'freeio'),
            'loadmore' => esc_html__('Load More Button', 'freeio'),
            'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[freelancers_pagination]',
    ) );

    // Freelancer Placeholder
    $wp_customize->add_setting('freeio_theme_options[freelancer_placeholder_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'freelancer_placeholder_image', array(
        'label'    => esc_html__('Freelancer Placeholder', 'freeio'),
        'section'  => 'freeio_settings_freelancer_archive',
        'settings' => 'freeio_theme_options[freelancer_placeholder_image]',
    )));



    // Single Freelancer
    $wp_customize->add_section('freeio_settings_freelancer_single', array(
        'title'    => esc_html__('Single Freelancer', 'freeio'),
        'priority' => 3,
        'panel' => 'freeio_settings_freelancer',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[freelancer_single_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'freelancer_single_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'settings' => 'freeio_theme_options[freelancer_single_general_setting]',
    )));
    
    // Template Type
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[freelancer_single_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_freelancer_single_elementor_template', array(
            'label'   => esc_html__('Freelancer Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_freelancer_single',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[freelancer_single_elementor_template]',
        ) );
    }

    // Freelancer Layout
    $wp_customize->add_setting( 'freeio_theme_options[freelancer_layout_type]', array(
        'default'        => 'v1',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancer_layout_type', array(
        'label'   => esc_html__('Freelancer Layout', 'freeio'),
        'section' => 'freeio_settings_freelancer_single',
        'type'    => 'select',
        'choices' => array(
            'v1' => esc_html__('Version 1', 'superio'),
            'v2' => esc_html__('Version 2', 'superio'),
            'v3' => esc_html__('Version 3', 'superio'),
        ),
        'settings' => 'freeio_theme_options[freelancer_layout_type]',
    ) );

    // Header Background Image
    $wp_customize->add_setting('freeio_theme_options[freelancer_header_bg_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'freelancer_header_bg_image', array(
        'label'    => esc_html__('Header Background Image', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'settings' => 'freeio_theme_options[freelancer_header_bg_image]',
    )));

    // Show Freelancer Details
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_detail]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_detail', array(
        'settings' => 'freeio_theme_options[show_freelancer_detail]',
        'label'    => esc_html__('Show Freelancer Details', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Show Freelancer Description
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_description]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_description', array(
        'settings' => 'freeio_theme_options[show_freelancer_description]',
        'label'    => esc_html__('Show Freelancer Description', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Show Freelancer Gallery
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_gallery]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_gallery', array(
        'settings' => 'freeio_theme_options[show_freelancer_gallery]',
        'label'    => esc_html__('Show Freelancer Gallery', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Show Freelancer Video
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_video]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_video', array(
        'settings' => 'freeio_theme_options[show_freelancer_video]',
        'label'    => esc_html__('Show Freelancer Video', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Show Freelancer Education
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_education]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_education', array(
        'settings' => 'freeio_theme_options[show_freelancer_education]',
        'label'    => esc_html__('Show Freelancer Education', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Show Freelancer Experience
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_experience]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_experience', array(
        'settings' => 'freeio_theme_options[show_freelancer_experience]',
        'label'    => esc_html__('Show Freelancer Experience', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Show Freelancer Skill
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_skill]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_skill', array(
        'settings' => 'freeio_theme_options[show_freelancer_skill]',
        'label'    => esc_html__('Show Freelancer Skill', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Show Freelancer Award
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_award]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_award', array(
        'settings' => 'freeio_theme_options[show_freelancer_award]',
        'label'    => esc_html__('Show Freelancer Award', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Show Freelancer services
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_services]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_services', array(
        'settings' => 'freeio_theme_options[show_freelancer_services]',
        'label'    => esc_html__('Show Freelancer Services', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Show Freelancers Related
    $wp_customize->add_setting('freeio_theme_options[show_freelancer_related]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_freelancer_related', array(
        'settings' => 'freeio_theme_options[show_freelancer_related]',
        'label'    => esc_html__('Show Freelancers Related', 'freeio'),
        'section'  => 'freeio_settings_freelancer_single',
        'type'     => 'checkbox',
    ));

    // Number related freelancers
    $wp_customize->add_setting( 'freeio_theme_options[number_freelancer_related]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancer_single_number_freelancer_related', array(
        'label'   => esc_html__('Number related freelancers', 'freeio'),
        'section' => 'freeio_settings_freelancer_single',
        'type'    => 'number',
        'settings' => 'freeio_theme_options[number_freelancer_related]',
    ) );

    // Related Freelancers Columns
    $wp_customize->add_setting( 'freeio_theme_options[related_freelancer_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_freelancer_single_related_freelancer_columns', array(
        'label'   => esc_html__('Related Freelancers Columns', 'freeio'),
        'section' => 'freeio_settings_freelancer_single',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[related_freelancer_columns]',
    ) );
}
add_action( 'customize_register', 'freeio_freelancer_customize_register', 15 );