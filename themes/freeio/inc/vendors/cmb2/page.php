<?php

if ( !function_exists( 'freeio_page_metaboxes' ) ) {
    function freeio_page_metaboxes(array $metaboxes) {
        global $wp_registered_sidebars;
        
        $sidebars = array();
        if ( !empty($wp_registered_sidebars) ) {
            foreach ($wp_registered_sidebars as $sidebar) {
                $sidebars[$sidebar['id']] = $sidebar['name'];
            }
        }
        $headers = array_merge( array('global' => esc_html__( 'Global Setting', 'freeio' )), freeio_get_header_layouts() );
        $footers = array_merge( array('global' => esc_html__( 'Global Setting', 'freeio' )), freeio_get_footer_layouts() );

        $template_options = ['' => esc_html__( 'Global Setting', 'freeio' ), 'no' => esc_html__('No', 'freeio'),];
        if ( did_action( 'elementor/loaded' ) ) {
            $ele_obj = \Elementor\Plugin::$instance;
            $templates = $ele_obj->templates_manager->get_source( 'local' )->get_items();
            
            if ( !empty( $templates ) ) {
                foreach ( $templates as $template ) {
                    $template_options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
                }
            }
        }

        $prefix = 'apus_page_';

        $columns = array(
            '' => esc_html__( 'Global Setting', 'freeio' ),
            '1' => esc_html__('1 Column', 'freeio'),
            '2' => esc_html__('2 Columns', 'freeio'),
            '3' => esc_html__('3 Columns', 'freeio'),
            '4' => esc_html__('4 Columns', 'freeio'),
            '6' => esc_html__('6 Columns', 'freeio')
        );
        // Jobs Page
        $fields = array(
            array(
                'name' => esc_html__( 'Jobs Layout', 'freeio' ),
                'id'   => $prefix.'jobs_layout_type',
                'type' => 'select',
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'default' => esc_html__('Default', 'freeio'),
                    'half-map' => esc_html__('Half Map', 'freeio'),
                )
            ),
            array(
                'id' => $prefix.'display_mode',
                'type' => 'select',
                'name' => esc_html__('Default Display Mode', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'grid' => esc_html__('Grid', 'freeio'),
                    'list' => esc_html__('List', 'freeio'),
                )
            ),
            array(
                'id' => $prefix.'jobs_columns',
                'type' => 'select',
                'name' => esc_html__('Grid Listing Columns', 'freeio'),
                'options' => $columns,
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'display_mode',
                    'data-conditional-value' => 'grid',
                )
            ),
            array(
                'id' => $prefix.'jobs_pagination',
                'type' => 'select',
                'name' => esc_html__('Pagination Type', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'default' => esc_html__('Default', 'freeio'),
                    'loadmore' => esc_html__('Load More Button', 'freeio'),
                    'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
                ),
            ),
            array(
                'id' => $prefix.'jobs_show_top_content',
                'type' => 'select',
                'name' => esc_html__('Show Top Content (Elementor Template)', 'freeio'),
                'options' => $template_options,
            ),
            array(
                'id' => $prefix.'jobs_show_offcanvas_filter',
                'type' => 'select',
                'name' => esc_html__('Show Offcanvas Filter', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'no' => esc_html__('No', 'freeio'),
                    'yes' => esc_html__('Yes', 'freeio')
                ),
            ),
        );
        
        $metaboxes[$prefix . 'jobs_setting'] = array(
            'id'                        => $prefix . 'jobs_setting',
            'title'                     => esc_html__( 'Jobs Settings', 'freeio' ),
            'object_types'              => array( 'page' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );

        // Services Page
        $fields = array(
            array(
                'name' => esc_html__( 'Services Layout', 'freeio' ),
                'id'   => $prefix.'services_layout_type',
                'type' => 'select',
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'default' => esc_html__('Default', 'freeio'),
                    'half-map' => esc_html__('Half Map', 'freeio'),
                )
            ),
            array(
                'id' => $prefix.'services_display_mode',
                'type' => 'select',
                'name' => esc_html__('Default Display Mode', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'grid' => esc_html__('Grid', 'freeio'),
                    'list' => esc_html__('List', 'freeio'),
                )
            ),
            array(
                'id' => $prefix.'services_inner_list_style',
                'type' => 'select',
                'name' => esc_html__('Services list item style', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'list' => esc_html__('List Default', 'freeio'),
                ),
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'services_display_mode',
                    'data-conditional-value' => 'list',
                )
            ),
            array(
                'id' => $prefix.'services_inner_grid_style',
                'type' => 'select',
                'name' => esc_html__('Services grid item style', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'grid' => esc_html__('Grid Default', 'freeio'),
                ),
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'services_display_mode',
                    'data-conditional-value' => 'grid',
                )
            ),
            array(
                'id' => $prefix.'services_columns',
                'type' => 'select',
                'name' => esc_html__('Grid Listing Columns', 'freeio'),
                'options' => $columns,
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'services_display_mode',
                    'data-conditional-value' => 'grid',
                )
            ),
            array(
                'id' => $prefix.'services_pagination',
                'type' => 'select',
                'name' => esc_html__('Pagination Type', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'default' => esc_html__('Default', 'freeio'),
                    'loadmore' => esc_html__('Load More Button', 'freeio'),
                    'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
                ),
            ),

            array(
                'id' => $prefix.'services_show_top_content',
                'type' => 'select',
                'name' => esc_html__('Show Top Content (Elementor Template)', 'freeio'),
                'options' => $template_options,
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'services_layout_type',
                    'data-conditional-value' => 'default',
                )
            ),
            array(
                'id' => $prefix.'services_show_offcanvas_filter',
                'type' => 'select',
                'name' => esc_html__('Show Offcanvas Filter', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'no' => esc_html__('No', 'freeio'),
                    'yes' => esc_html__('Yes', 'freeio')
                ),
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'services_layout_type',
                    'data-conditional-value' => 'default',
                )
            ),
        );
        
        $metaboxes[$prefix . 'services_setting'] = array(
            'id'                        => $prefix . 'services_setting',
            'title'                     => esc_html__( 'Services Settings', 'freeio' ),
            'object_types'              => array( 'page' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );

        // Projects Page
        $fields = array(
            array(
                'id' => $prefix.'projects_display_mode',
                'type' => 'select',
                'name' => esc_html__('Default Display Mode', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'grid' => esc_html__('Grid', 'freeio'),
                    'list' => esc_html__('List', 'freeio'),
                )
            ),
            array(
                'id' => $prefix.'projects_inner_list_style',
                'type' => 'select',
                'name' => esc_html__('Projects list item style', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'list' => esc_html__('List Default', 'freeio'),
                    'list-v1' => esc_html__('List v1', 'freeio'),
                ),
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'projects_display_mode',
                    'data-conditional-value' => 'list',
                )
            ),
            array(
                'id' => $prefix.'projects_inner_grid_style',
                'type' => 'select',
                'name' => esc_html__('Projects grid item style', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'grid' => esc_html__('Grid Default', 'freeio'),
                    'grid-v1' => esc_html__('Grid V1', 'freeio'),
                    'grid-v2' => esc_html__('Grid V2', 'freeio'),
                ),
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'projects_display_mode',
                    'data-conditional-value' => 'grid',
                )
            ),
            array(
                'id' => $prefix.'projects_columns',
                'type' => 'select',
                'name' => esc_html__('Grid Listing Columns', 'freeio'),
                'options' => $columns,
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'projects_display_mode',
                    'data-conditional-value' => 'grid',
                )
            ),
            array(
                'id' => $prefix.'projects_pagination',
                'type' => 'select',
                'name' => esc_html__('Pagination Type', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'default' => esc_html__('Default', 'freeio'),
                    'loadmore' => esc_html__('Load More Button', 'freeio'),
                    'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
                ),
            ),
            array(
                'id' => $prefix.'projects_show_top_content',
                'type' => 'select',
                'name' => esc_html__('Show Top Content (Elementor Template)', 'freeio'),
                'options' => $template_options,
            ),
            array(
                'id' => $prefix.'projects_show_offcanvas_filter',
                'type' => 'select',
                'name' => esc_html__('Show Offcanvas Filter', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'no' => esc_html__('No', 'freeio'),
                    'yes' => esc_html__('Yes', 'freeio')
                ),
            ),
        );
        
        $metaboxes[$prefix . 'projects_setting'] = array(
            'id'                        => $prefix . 'projects_setting',
            'title'                     => esc_html__( 'Projects Settings', 'freeio' ),
            'object_types'              => array( 'page' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );

        // Employers Page
        $fields = array(
            array(
                'id' => $prefix.'employers_display_mode',
                'type' => 'select',
                'name' => esc_html__('Employers display mode', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'grid' => esc_html__('Grid', 'freeio'),
                    'list' => esc_html__('List', 'freeio'),
                )
            ),

            array(
                'id' => $prefix.'employers_columns',
                'type' => 'select',
                'name' => esc_html__('Employer Columns', 'freeio'),
                'options' => $columns,
                'description' => esc_html__('Apply for display mode is grid.', 'freeio'),
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'employers_display_mode',
                    'data-conditional-value' => 'grid',
                )
            ),

            array(
                'id' => $prefix.'employers_show_top_content',
                'type' => 'select',
                'name' => esc_html__('Show Top Content (Elementor Template)', 'freeio'),
                'options' => $template_options,
            ),
            array(
                'id' => $prefix.'employers_show_offcanvas_filter',
                'type' => 'select',
                'name' => esc_html__('Show Offcanvas Filter', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'no' => esc_html__('No', 'freeio'),
                    'yes' => esc_html__('Yes', 'freeio')
                ),
            ),
            array(
                'id' => $prefix.'employers_pagination',
                'type' => 'select',
                'name' => esc_html__('Pagination Type', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'default' => esc_html__('Default', 'freeio'),
                    'loadmore' => esc_html__('Load More Button', 'freeio'),
                    'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
                ),
            ),


        );
        $metaboxes[$prefix . 'employers_setting'] = array(
            'id'                        => $prefix . 'employers_setting',
            'title'                     => esc_html__( 'Employers Settings', 'freeio' ),
            'object_types'              => array( 'page' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );

        // Freelancers Page
        $fields = array(
            array(
                'name' => esc_html__( 'Freelancers Layout', 'freeio' ),
                'id'   => $prefix.'freelancers_layout_type',
                'type' => 'select',
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'default' => esc_html__('Default', 'freeio'),
                    'half-map' => esc_html__('Half Map', 'freeio'),
                )
            ),
            array(
                'id' => $prefix.'freelancer_display_mode',
                'type' => 'select',
                'name' => esc_html__('Freelancers display mode', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'grid' => esc_html__('Grid', 'freeio'),
                    'list' => esc_html__('List', 'freeio'),
                )
            ),
            array(
                'id' => $prefix.'freelancers_inner_grid_style',
                'type' => 'select',
                'name' => esc_html__('Freelancers grid item style', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'grid' => esc_html__('Grid Default', 'freeio'),
                    'grid-v1' => esc_html__('Grid v1', 'freeio'),
                    'grid-v2' => esc_html__('Grid v2', 'freeio'),
                    'grid-v3' => esc_html__('Grid v3', 'freeio'),
                    'grid-v4' => esc_html__('Grid v4', 'freeio'),
                ),
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'freelancer_display_mode',
                    'data-conditional-value' => 'grid',
                )
            ),
            array(
                'id' => $prefix.'freelancers_inner_list_style',
                'type' => 'select',
                'name' => esc_html__('Freelancers list item style', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'list' => esc_html__('List Default', 'freeio'),
                    'list-v1' => esc_html__('List v1', 'freeio'),
                ),
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'freelancer_display_mode',
                    'data-conditional-value' => 'list',
                )
            ),
            array(
                'id' => $prefix.'freelancer_columns',
                'type' => 'select',
                'name' => esc_html__('Freelancer Columns', 'freeio'),
                'options' => $columns,
                'description' => esc_html__('Apply for display mode is grid.', 'freeio'),
                'attributes'        => array(
                    'data-conditional-id' => $prefix.'freelancer_display_mode',
                    'data-conditional-value' => 'grid',
                )
            ),

            array(
                'id' => $prefix.'freelancers_show_top_content',
                'type' => 'select',
                'name' => esc_html__('Show Top Content (Elementor Template)', 'freeio'),
                'options' => $template_options,
            ),
            array(
                'id' => $prefix.'freelancers_show_offcanvas_filter',
                'type' => 'select',
                'name' => esc_html__('Show Offcanvas Filter', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'no' => esc_html__('No', 'freeio'),
                    'yes' => esc_html__('Yes', 'freeio')
                ),
            ),
            
            array(
                'id' => $prefix.'freelancers_pagination',
                'type' => 'select',
                'name' => esc_html__('Pagination Type', 'freeio'),
                'options' => array(
                    '' => esc_html__( 'Global Setting', 'freeio' ),
                    'default' => esc_html__('Default', 'freeio'),
                    'loadmore' => esc_html__('Load More Button', 'freeio'),
                    'infinite' => esc_html__('Infinite Scrolling', 'freeio'),
                ),
            ),
        );
        $metaboxes[$prefix . 'freelancers_setting'] = array(
            'id'                        => $prefix . 'freelancers_setting',
            'title'                     => esc_html__( 'Freelancers Settings', 'freeio' ),
            'object_types'              => array( 'page' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );

        // General
        $fields = array(
            array(
                'name' => esc_html__( 'Select Layout', 'freeio' ),
                'id'   => $prefix.'layout',
                'type' => 'select',
                'options' => array(
                    'main' => esc_html__('Main Content Only', 'freeio'),
                    'left-main' => esc_html__('Left Sidebar - Main Content', 'freeio'),
                    'main-right' => esc_html__('Main Content - Right Sidebar', 'freeio')
                )
            ),
            array(
                'id' => $prefix.'fullwidth',
                'type' => 'select',
                'name' => esc_html__('Is Full Width?', 'freeio'),
                'default' => 'no',
                'options' => array(
                    'no' => esc_html__('No', 'freeio'),
                    'yes' => esc_html__('Yes', 'freeio')
                )
            ),
            array(
                'id' => $prefix.'left_sidebar',
                'type' => 'select',
                'name' => esc_html__('Left Sidebar', 'freeio'),
                'options' => $sidebars
            ),
            array(
                'id' => $prefix.'right_sidebar',
                'type' => 'select',
                'name' => esc_html__('Right Sidebar', 'freeio'),
                'options' => $sidebars
            ),
            array(
                'id' => $prefix.'show_breadcrumb',
                'type' => 'select',
                'name' => esc_html__('Show Breadcrumb?', 'freeio'),
                'options' => array(
                    'no' => esc_html__('No', 'freeio'),
                    'yes' => esc_html__('Yes', 'freeio')
                ),
                'default' => 'yes',
            ),
            array(
                'id' => $prefix.'breadcrumb_color',
                'type' => 'colorpicker',
                'name' => esc_html__('Breadcrumb Background Color', 'freeio')
            ),
            array(
                'id' => $prefix.'breadcrumb_image',
                'type' => 'file',
                'name' => esc_html__('Breadcrumb Background Image', 'freeio')
            ),
            array(
                'id' => $prefix.'header_type',
                'type' => 'select',
                'name' => esc_html__('Header Layout Type', 'freeio'),
                'description' => esc_html__('Choose a header for your website.', 'freeio'),
                'options' => $headers,
                'default' => 'global'
            ),
            array(
                'id' => $prefix.'header_transparent',
                'type' => 'select',
                'name' => esc_html__('Header Transparent', 'freeio'),
                'description' => esc_html__('Choose a header for your website.', 'freeio'),
                'options' => array(
                    'no' => esc_html__('No', 'freeio'),
                    'yes' => esc_html__('Yes', 'freeio')
                ),
                'default' => 'global'
            ),
            array(
                'id' => $prefix.'footer_type',
                'type' => 'select',
                'name' => esc_html__('Footer Layout Type', 'freeio'),
                'description' => esc_html__('Choose a footer for your website.', 'freeio'),
                'options' => $footers,
                'default' => 'global'
            ),
            array(
                'id' => $prefix.'color',
                'type' => 'colorpicker',
                'name' => esc_html__('Background Color', 'freeio')
            ),
            array(
                'id' => $prefix.'extra_class',
                'type' => 'text',
                'name' => esc_html__('Extra Class', 'freeio'),
                'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'freeio')
            )
        );
        
        $metaboxes[$prefix . 'display_setting'] = array(
            'id'                        => $prefix . 'display_setting',
            'title'                     => esc_html__( 'Display Settings', 'freeio' ),
            'object_types'              => array( 'page' ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );

        return $metaboxes;
    }
}
add_filter( 'cmb2_meta_boxes', 'freeio_page_metaboxes' );

if ( !function_exists( 'freeio_cmb2_style' ) ) {
    function freeio_cmb2_style() {
        wp_enqueue_style( 'freeio-cmb2-style', get_template_directory_uri() . '/inc/vendors/cmb2/assets/style.css', array(), '1.0' );
        wp_enqueue_script( 'freeio-admin', get_template_directory_uri() . '/js/admin.js', array( 'jquery' ), '20150330', true );

        global $pagenow, $typenow;
        if ('post.php' == $pagenow && 'page' == $typenow ) {
            wp_enqueue_script( 'freeio-admin-page', get_template_directory_uri() . '/js/admin-page.js', array( 'jquery' ), '20150330', true );
        }
    }
}
add_action( 'admin_enqueue_scripts', 'freeio_cmb2_style' );


