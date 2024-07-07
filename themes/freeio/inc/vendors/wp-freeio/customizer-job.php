<?php

function freeio_job_customize_register( $wp_customize ) {
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
    $wp_customize->add_panel( 'freeio_settings_job', array(
        'title' => esc_html__( 'Jobs Settings', 'freeio' ),
        'priority' => 4,
    ) );

    // General Section
    $wp_customize->add_section('freeio_settings_job_general', array(
        'title'    => esc_html__('General', 'freeio'),
        'priority' => 1,
        'panel' => 'freeio_settings_job',
    ));

    // Breadcrumbs
    $wp_customize->add_setting('freeio_theme_options[show_job_breadcrumbs]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_job_breadcrumbs', array(
        'settings' => 'freeio_theme_options[show_job_breadcrumbs]',
        'label'    => esc_html__('Breadcrumbs', 'freeio'),
        'section'  => 'freeio_settings_job_general',
        'type'     => 'checkbox',
    ));

    // Breadcrumbs Background Color
    $wp_customize->add_setting('freeio_theme_options[job_breadcrumb_color]', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'job_breadcrumb_color', array(
        'label'    => esc_html__('Breadcrumbs Background Color', 'freeio'),
        'section'  => 'freeio_settings_job_general',
        'settings' => 'freeio_theme_options[job_breadcrumb_color]',
    )));

    // Breadcrumbs Background
    $wp_customize->add_setting('freeio_theme_options[job_breadcrumb_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'job_breadcrumb_image', array(
        'label'    => esc_html__('Breadcrumbs Background', 'freeio'),
        'section'  => 'freeio_settings_job_general',
        'settings' => 'freeio_theme_options[job_breadcrumb_image]',
    )));

    
    // Job Archives
    $wp_customize->add_section('freeio_settings_job_archive', array(
        'title'    => esc_html__('Job Archives', 'freeio'),
        'priority' => 2,
        'panel' => 'freeio_settings_job',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[job_archive_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'job_archive_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_job_archive',
        'settings' => 'freeio_theme_options[job_archive_general_setting]',
    )));

    // Template Type
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[job_archive_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_job_archive_elementor_template', array(
            'label'   => esc_html__('Jobs Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_job_archive',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[job_archive_elementor_template]',
        ) );
    }

    // Is Full Width
    $wp_customize->add_setting('freeio_theme_options[job_archive_fullwidth]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_job_archive_fullwidth', array(
        'settings' => 'freeio_theme_options[job_archive_fullwidth]',
        'label'    => esc_html__('Is Full Width', 'freeio'),
        'section'  => 'freeio_settings_job_archive',
        'type'     => 'checkbox',
    ));
    

    // Jobs Layout
    $wp_customize->add_setting( 'freeio_theme_options[jobs_layout_type]', array(
        'default'        => 'default',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_job_archive_blog_archive', array(
        'label'   => esc_html__('Jobs Layout', 'freeio'),
        'section' => 'freeio_settings_job_archive',
        'type'    => 'select',
        'choices' => array(
            'default' => esc_html__('Default', 'freeio'),
            'half-map' => esc_html__('Half Map', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[jobs_layout_type]',
    ) );

    // layout
    $wp_customize->add_setting( 'freeio_theme_options[jobs_layout_sidebar]', array(
        'default'        => 'main',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );

    $wp_customize->add_control( new Freeio_WP_Customize_Radio_Image_Control( 
        $wp_customize, 
        'freeio_settings_jobs_layout_sidebar', 
        array(
            'label'   => esc_html__('Layout Type', 'freeio'),
            'section' => 'freeio_settings_job_archive',
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
            'settings' => 'freeio_theme_options[jobs_layout_sidebar]',
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
    $wp_customize->add_setting( 'freeio_theme_options[jobs_show_top_content]', array(
        'default'        => '',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_jobs_show_top_content', array(
        'label'   => esc_html__('Top Content', 'freeio'),
        'section' => 'freeio_settings_job_archive',
        'type'    => 'select',
        'choices' => $template_options,
        'settings' => 'freeio_theme_options[jobs_show_top_content]',
    ) );

    // Show Offcanvas Filter
    $wp_customize->add_setting('freeio_theme_options[jobs_show_offcanvas_filter]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'       => 0,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_jobs_show_offcanvas_filter', array(
        'settings' => 'freeio_theme_options[jobs_show_offcanvas_filter]',
        'label'    => esc_html__('Show Offcanvas Filter', 'freeio'),
        'section'  => 'freeio_settings_job_archive',
        'type'     => 'checkbox',
    ));

    // Display Mode
    $wp_customize->add_setting( 'freeio_theme_options[job_display_mode]', array(
        'default'        => 'grid',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_job_display_mode', array(
        'label'   => esc_html__('Display Mode', 'freeio'),
        'section' => 'freeio_settings_job_archive',
        'type'    => 'select',
        'choices' => array(
            'grid' => esc_html__('Grid', 'freeio'),
            'list' => esc_html__('List', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[job_display_mode]',
    ) );

    // jobs Columns
    $wp_customize->add_setting( 'freeio_theme_options[job_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_job_archive_job_columns', array(
        'label'   => esc_html__('Job Columns', 'freeio'),
        'section' => 'freeio_settings_job_archive',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[job_columns]',
    ) );

    // Pagination Type
    $wp_customize->add_setting( 'freeio_theme_options[jobs_pagination]', array(
        'default'        => 'default',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_jobs_pagination', array(
        'label'   => esc_html__('Pagination Type', 'freeio'),
        'section' => 'freeio_settings_job_archive',
        'type'    => 'select',
        'choices' => array(
            'default' => esc_html__('Default', 'freeio'),
            'loadmore' => esc_html__('Load More Button', 'freeio'),
            'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
        ),
        'settings' => 'freeio_theme_options[jobs_pagination]',
    ) );




    // Single Job
    $wp_customize->add_section('freeio_settings_job_single', array(
        'title'    => esc_html__('Single Job', 'freeio'),
        'priority' => 3,
        'panel' => 'freeio_settings_job',
    ));

    // General Setting ?
    $wp_customize->add_setting('freeio_theme_options[job_single_general_setting]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control( new Freeio_WP_Customize_Heading_Control($wp_customize, 'job_single_general_setting', array(
        'label'    => esc_html__('General Settings', 'freeio'),
        'section'  => 'freeio_settings_job_single',
        'settings' => 'freeio_theme_options[job_single_general_setting]',
    )));

    // Template Type
    if ( did_action( 'elementor/loaded' ) ) {
        $wp_customize->add_setting( 'freeio_theme_options[job_single_elementor_template]', array(
            'default'        => '',
            'type'           => 'option',
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        $wp_customize->add_control( 'freeio_theme_options_job_single_elementor_template', array(
            'label'   => esc_html__('Job Template (Elementor)', 'freeio'),
            'section' => 'freeio_settings_job_single',
            'type'    => 'select',
            'choices' => $elementor_options,
            'settings' => 'freeio_theme_options[job_single_elementor_template]',
        ) );
    }

    // Header Background Image
    $wp_customize->add_setting('freeio_theme_options[job_header_bg_image]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'job_header_bg_image', array(
        'label'    => esc_html__('Header Background Image', 'freeio'),
        'section'  => 'freeio_settings_job_single',
        'settings' => 'freeio_theme_options[job_header_bg_image]',
    )));

    // Is Full Width
    $wp_customize->add_setting('freeio_theme_options[job_fullwidth]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_job_fullwidth', array(
        'settings' => 'freeio_theme_options[job_fullwidth]',
        'label'    => esc_html__('Is Full Width', 'freeio'),
        'section'  => 'freeio_settings_job_single',
        'type'     => 'checkbox',
    ));
    
    // Show Job Details
    $wp_customize->add_setting('freeio_theme_options[show_job_detail]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_job_detail', array(
        'settings' => 'freeio_theme_options[show_job_detail]',
        'label'    => esc_html__('Show Job Details', 'freeio'),
        'section'  => 'freeio_settings_job_single',
        'type'     => 'checkbox',
    ));

    // Show Job Description
    $wp_customize->add_setting('freeio_theme_options[show_job_description]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_job_description', array(
        'settings' => 'freeio_theme_options[show_job_description]',
        'label'    => esc_html__('Show Job Description', 'freeio'),
        'section'  => 'freeio_settings_job_single',
        'type'     => 'checkbox',
    ));

    // Show Job Social Share
    $wp_customize->add_setting('freeio_theme_options[show_job_social_share]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 0,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_job_social_share', array(
        'settings' => 'freeio_theme_options[show_job_social_share]',
        'label'    => esc_html__('Show Job Social Share', 'freeio'),
        'section'  => 'freeio_settings_job_single',
        'type'     => 'checkbox',
    ));

    // Show Job Photos
    $wp_customize->add_setting('freeio_theme_options[show_job_photos]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_job_photos', array(
        'settings' => 'freeio_theme_options[show_job_photos]',
        'label'    => esc_html__('Show Job Photos', 'freeio'),
        'section'  => 'freeio_settings_job_single',
        'type'     => 'checkbox',
    ));

    // Show Job Video
    $wp_customize->add_setting('freeio_theme_options[show_job_video]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_job_video', array(
        'settings' => 'freeio_theme_options[show_job_video]',
        'label'    => esc_html__('Show Job Video', 'freeio'),
        'section'  => 'freeio_settings_job_single',
        'type'     => 'checkbox',
    ));

    // Show Jobs Related
    $wp_customize->add_setting('freeio_theme_options[show_job_related]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 1,
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_show_job_related', array(
        'settings' => 'freeio_theme_options[show_job_related]',
        'label'    => esc_html__('Show Jobs Related', 'freeio'),
        'section'  => 'freeio_settings_job_single',
        'type'     => 'checkbox',
    ));

    // Number related jobs
    $wp_customize->add_setting( 'freeio_theme_options[number_job_related]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_job_single_number_job_related', array(
        'label'   => esc_html__('Number related jobs', 'freeio'),
        'section' => 'freeio_settings_job_single',
        'type'    => 'number',
        'settings' => 'freeio_theme_options[number_job_related]',
    ) );

    // Related Jobs Columns
    $wp_customize->add_setting( 'freeio_theme_options[related_job_columns]', array(
        'default'        => '4',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'freeio_settings_job_single_related_job_columns', array(
        'label'   => esc_html__('Related Jobs Columns', 'freeio'),
        'section' => 'freeio_settings_job_single',
        'type'    => 'select',
        'choices' => $columns,
        'settings' => 'freeio_theme_options[related_job_columns]',
    ) );


    // Single Job Expired
    $wp_customize->add_section('freeio_settings_job_expired', array(
        'title'    => esc_html__('Single Job Expired', 'freeio'),
        'priority' => 4,
        'panel' => 'freeio_settings_job',
    ));

    // Image Icon
    $wp_customize->add_setting('freeio_theme_options[job_expired_icon_img]', array(
        'default'           => '',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
        'sanitize_callback' => 'sanitize_text_field',

    ));

    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'job_expired_icon_img', array(
        'label'    => esc_html__('Image Icon', 'freeio'),
        'section'  => 'freeio_settings_job_expired',
        'settings' => 'freeio_theme_options[job_expired_icon_img]',
    )));

    // Title
    $wp_customize->add_setting('freeio_theme_options[job_expired_title]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 'We\'re Sorry Opps! Job Expired',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_job_expired_title', array(
        'settings' => 'freeio_theme_options[job_expired_title]',
        'label'    => esc_html__('Title', 'freeio'),
        'section'  => 'freeio_settings_job_expired',
        'type'     => 'text',
    ));

    // Description
    $wp_customize->add_setting('freeio_theme_options[job_expired_description]', array(
        'capability' => 'edit_theme_options',
        'type'       => 'option',
        'default'    => 'Unable to access the link. Job has been expired. Please contact the admin or who shared the link with you.',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('freeio_theme_options_job_expired_description', array(
        'settings' => 'freeio_theme_options[job_expired_description]',
        'label'    => esc_html__('Description', 'freeio'),
        'section'  => 'freeio_settings_job_expired',
        'type'     => 'text',
    ));
}
add_action( 'customize_register', 'freeio_job_customize_register', 15 );