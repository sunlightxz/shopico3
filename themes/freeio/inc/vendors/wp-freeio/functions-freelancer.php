<?php

function freeio_get_freelancers( $params = array() ) {
	$params = wp_parse_args( $params, array(
		'limit' => -1,
		'post_status' => 'publish',
		'get_freelancers_by' => 'recent',
		'orderby' => '',
		'order' => '',
		'post__in' => array(),
		'fields' => null, // ids
		'author' => null,
		'category' => array(),
		'location' => array(),
	));
	extract($params);

	$query_args = array(
		'post_type'         => 'freelancer',
		'posts_per_page'    => $limit,
		'post_status'       => $post_status,
		'orderby'       => $orderby,
		'order'       => $order,
	);

	$meta_query = array();
	switch ($get_freelancers_by) {
		case 'recent':
			$query_args['orderby'] = 'date';
			$query_args['order'] = 'DESC';
			break;
		case 'featured':
			$meta_query[] = array(
				'key' => WP_FREEIO_FREELANCER_PREFIX.'featured',
	           	'value' => 'on',
	           	'compare' => '=',
			);
			break;
		case 'urgent':
			$meta_query[] = array(
				'key' => WP_FREEIO_FREELANCER_PREFIX.'urgent',
	           	'value' => 'on',
	           	'compare' => '=',
			);
			break;
	}

	if ( !empty($post__in) ) {
    	$query_args['post__in'] = $post__in;
    }

    if ( !empty($fields) ) {
    	$query_args['fields'] = $fields;
    }

    if ( !empty($author) ) {
    	$query_args['author'] = $author;
    }

    $tax_query = array();
    if ( !empty($category) ) {
    	$tax_query[] = array(
            'taxonomy'      => 'freelancer_category',
            'field'         => 'slug',
            'terms'         => $category,
            'operator'      => 'IN'
        );
    }
    if ( !empty($location) ) {
    	$tax_query[] = array(
            'taxonomy'      => 'location',
            'field'         => 'slug',
            'terms'         => $location,
            'operator'      => 'IN'
        );
    }

    if ( !empty($tax_query) ) {
    	$query_args['tax_query'] = $tax_query;
    }
    
    $meta_query[] = array(
		'relation' => 'OR',
		array(
			'key'       => WP_FREEIO_FREELANCER_PREFIX.'show_profile',
			'value'     => 'show',
			'compare'   => '==',
		),
		array(
			'key'       => WP_FREEIO_FREELANCER_PREFIX.'show_profile',
			'compare' => 'NOT EXISTS',
		),
	);
	
    if ( !empty($meta_query) ) {
    	$query_args['meta_query'] = $meta_query;
    }

    if ( method_exists('WP_Freeio_Freelancer', 'freelancer_restrict_listing_query_args') ) {
	    $query_args = WP_Freeio_Freelancer::freelancer_restrict_listing_query_args($query_args, null);
	}
	
	return new WP_Query( $query_args );
}

if ( !function_exists('freeio_freelancer_content_class') ) {
	function freeio_freelancer_content_class( $class ) {
		$prefix = 'freelancers';
		if ( is_singular( 'freelancer' ) ) {
            $prefix = 'freelancer';
        }
		if ( freeio_get_config($prefix.'_fullwidth') ) {
			return 'container-fluid';
		}
		return $class;
	}
}
add_filter( 'freeio_freelancer_content_class', 'freeio_freelancer_content_class', 1 , 1 );

if ( !function_exists('freeio_get_freelancers_layout_configs') ) {
	function freeio_get_freelancers_layout_configs() {
		$layout_type = freeio_get_freelancers_layout_sidebar();
		switch ( $layout_type ) {
		 	case 'left-main':
		 		$configs['left'] = array( 'sidebar' => 'freelancers-filter-sidebar', 'class' => 'col-lg-3 col-12'  );
		 		$configs['main'] = array( 'class' => 'col-lg-9 col-12' );
		 		break;
		 	case 'main-right':
		 	default:
		 		$configs['right'] = array( 'sidebar' => 'freelancers-filter-sidebar',  'class' => 'col-lg-3 col-12' ); 
		 		$configs['main'] = array( 'class' => 'col-lg-9 col-12' );
		 		break;
	 		case 'main':
	 			$configs['main'] = array( 'class' => 'col-lg-12 col-12' );
	 			break;
		}
		return $configs; 
	}
}

function freeio_get_freelancers_layout_sidebar() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$layout_type = get_post_meta( $post->ID, 'apus_page_layout', true );
	}
	if ( empty($layout_type) ) {
		$layout_type = freeio_get_config('freelancer_archive_layout', 'main-right');
	}
	return apply_filters( 'freeio_get_freelancers_layout_sidebar', $layout_type );
}

function freeio_get_freelancers_layout_type() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$layout_type = get_post_meta( $post->ID, 'apus_page_freelancers_layout_type', true );
	}
	if ( empty($layout_type) ) {
		$layout_type = freeio_get_config('freelancers_layout_type', 'default');
	}
	return apply_filters( 'freeio_get_freelancers_layout_type', $layout_type );
}

function freeio_get_freelancers_display_mode() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$display_mode = get_post_meta( $post->ID, 'apus_page_freelancer_display_mode', true );
	}
	if ( empty($display_mode) ) {
		$display_mode = freeio_get_config('freelancer_display_mode', 'grid');
	}
	return apply_filters( 'freeio_get_freelancers_display_mode', $display_mode );
}

function freeio_get_freelancers_inner_style() {
	global $post;
	$display_mode = freeio_get_freelancers_display_mode();
	if ( $display_mode == 'list' ) {
		if ( is_page() && is_object($post) ) {
			$inner_style = get_post_meta( $post->ID, 'apus_page_freelancers_inner_list_style', true );
		}
		if ( empty($inner_style) ) {
			$inner_style = freeio_get_config('freelancers_inner_list_style', 'list');
		}
	} else {
		if ( is_page() && is_object($post) ) {
			$inner_style = get_post_meta( $post->ID, 'apus_page_freelancers_inner_grid_style', true );
		}
		if ( empty($inner_style) ) {
			$inner_style = freeio_get_config('freelancers_inner_grid_style', 'grid');
		}
	}
	return apply_filters( 'freeio_get_freelancers_inner_style', $inner_style );
}

function freeio_get_freelancers_columns() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$columns = get_post_meta( $post->ID, 'apus_page_freelancer_columns', true );
	}
	if ( empty($columns) ) {
		$columns = freeio_get_config('freelancer_columns', 3);
	}
	return apply_filters( 'freeio_get_freelancers_columns', $columns );
}

function freeio_get_freelancers_pagination() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$pagination = get_post_meta( $post->ID, 'apus_page_freelancers_pagination', true );
	}
	if ( empty($pagination) ) {
		$pagination = freeio_get_config('freelancers_pagination', 'default');
	}
	return apply_filters( 'freeio_get_freelancers_pagination', $pagination );
}

function freeio_freelancer_check_hidden_review() {
	$view = wp_freeio_get_option('freelancers_restrict_review', 'all');
	if ( $view == 'always_hidden' ) {
		return false;
	}
	return true;
}

function freeio_get_freelancers_show_top_content() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$show_filter_top = get_post_meta( $post->ID, 'apus_page_freelancers_show_top_content', true );
	}
	if ( empty($show_filter_top) ) {
		$show_filter_top = freeio_get_config('freelancers_show_top_content');
	} else {
		if ( $show_filter_top == 'no' ) {
			$show_filter_top = 0;
		}
	}
	return apply_filters( 'freeio_get_freelancers_show_top_content', $show_filter_top );
}

function freeio_get_freelancers_show_offcanvas_filter() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$show_offcanvas_filter = get_post_meta( $post->ID, 'apus_page_freelancers_show_offcanvas_filter', true );
	}
	if ( empty($show_offcanvas_filter) ) {
		$show_offcanvas_filter = freeio_get_config('freelancers_show_offcanvas_filter');
	} else {
		if ( $show_offcanvas_filter == 'yes' ) {
			$show_offcanvas_filter = true;
		} else {
			$show_offcanvas_filter = false;
		}
	}
	return apply_filters( 'freeio_get_freelancers_show_offcanvas_filter', $show_offcanvas_filter );
}

add_filter('wp-freeio-freelancer-admin-custom-fields', 'freeio_freelancer_metaboxes_fields', 10);
function freeio_freelancer_metaboxes_fields($fields) {
	$prefix = WP_FREEIO_FREELANCER_PREFIX;

	$elementor_options = ['' => esc_html__('Global Settings', 'freeio')];
    if ( did_action( 'elementor/loaded' ) ) {
        $ele_obj = \Elementor\Plugin::$instance;
        $templates = $ele_obj->templates_manager->get_source( 'local' )->get_items();
        
        if ( !empty( $templates ) ) {
            foreach ( $templates as $template ) {
                $elementor_options[ $template['template_id'] ] = $template['title'] . ' (' . $template['type'] . ')';
            }
        }
    }

	$layout_key = 'tab-heading-freelancer-layout'.rand(100,1000);
	$fields[$layout_key] = array(
		'id' => $layout_key,
		'icon' => 'dashicons-admin-appearance',
		'title'  => esc_html__( 'Layout Type', 'freeio' ),
		'fields' => array(
			array(
				'name'              => esc_html__( 'Layout Template', 'freeio' ),
				'id'                => $prefix . 'layout_template',
				'type'              => 'select',
				'options'			=> array(
					'' => esc_html__('Global Settings', 'freeio'),
					'default' => esc_html__('Default', 'freeio'),
	                'elementor' => esc_html__('Elementor Template', 'freeio'),
	            ),
			),
			array(
				'name'              => esc_html__( 'Elementor Template', 'freeio' ),
				'id'                => $prefix . 'elementor_template',
				'type'              => 'select',
				'options'			=> $elementor_options,
			),
			array(
				'name'              => esc_html__( 'Layout Type', 'freeio' ),
				'id'                => $prefix . 'layout_type',
				'type'              => 'select',
				'options'			=> array(
	                '' => esc_html__('Global Settings', 'freeio'),
	                'v1' => esc_html__('Version 1', 'freeio'),
	                'v2' => esc_html__('Version 2', 'freeio'),
	                'v3' => esc_html__('Version 3', 'freeio'),
	            ),
			)
		),
	);

	return $fields;
}

function freeio_get_freelancer_layout_type() {
	global $post;
	$layout_type = get_post_meta($post->ID, WP_FREEIO_FREELANCER_PREFIX.'layout_type', true);
	
	if ( empty($layout_type) ) {
		$layout_type = freeio_get_config('freelancer_layout_type', 'v1');
	}
	return apply_filters( 'freeio_get_freelancer_layout_type', $layout_type );
}

function freeio_get_freelancer_elementor_template() {
	global $post;
	$layout_template = get_post_meta($post->ID, WP_FREEIO_FREELANCER_PREFIX.'layout_template', true);
	$elementor_template = '';
	if ( $layout_template == '' ) {
		if ( empty($elementor_template) ) {
			$elementor_template = freeio_get_config('freelancer_single_elementor_template');
		}
	} elseif ( $layout_template == 'elementor' ) {
		$elementor_template = get_post_meta($post->ID, WP_FREEIO_FREELANCER_PREFIX.'elementor_template', true);
		
		if ( empty($elementor_template) ) {
			$elementor_template = freeio_get_config('freelancer_single_elementor_template');
		}
	}
	return apply_filters( 'freeio_get_freelancer_elementor_template', $elementor_template );
}

function freeio_is_freelancers_page() {
	if ( is_page() ) {
		$page_name = basename(get_page_template());
		if ( $page_name == 'page-freelancers.php' ) {
			return true;
		}
	} elseif( is_post_type_archive('freelancer') || is_tax('freelancer_category') || is_tax('location') ) {
		return true;
	}
	return false;
}

function freeio_is_freelancers_archive_page() {
	if( is_post_type_archive('freelancer') || is_tax('freelancer_category') || is_tax('location') ) {
		return true;
	}
	return false;
}

function freeio_is_freelancer_single_page() {
	if ( is_singular('freelancer') || apply_filters('freeio_is_freelancer_single', false) ) {
		return true;
	}
	return false;
}


function freeio_freelancer_placeholder_img_src( $size = 'thumbnail' ) {
	$src               = get_template_directory_uri() . '/images/placeholder.png';
	$placeholder_image = freeio_get_config('freelancer_placeholder_image');
	if ( !empty($placeholder_image) ) {
		$src = $placeholder_image;
    }

	return apply_filters( 'freeio_freelancer_placeholder_img_src', $src );
}

add_action( 'wpfi_ajax_freeio_autocomplete_search_freelancers', 'freeio_autocomplete_search_freelancers' );
function freeio_autocomplete_search_freelancers() {
    // Query for suggestions
    $suggestions = array();
    $args = array(
		'post_type' => 'freelancer',
		'post_per_page' => 10,
		'fields' => 'ids'
	);
    $filter_params = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;

	$freelancers = WP_Freeio_Query::get_posts( $args, $filter_params );

	if ( !empty($freelancers->posts) ) {
		foreach ($freelancers->posts as $post_id) {
			$post = get_post($post_id);
			
			$suggestion['title'] = freeio_freelancer_name($post);
			$suggestion['url'] = get_permalink($post_id);
			
			$image = '';
		 	if ( has_post_thumbnail($post_id) ) {
    			$image_id = get_post_thumbnail_id($post_id);
    			if ( $image_id ) {
        			$image = wp_get_attachment_image_url( $image_id, 'thumbnail' );
        		}
			}

			$suggestion['image'] = $image;
	        
	        $suggestion['salary'] = freeio_freelancer_display_salary($post, '', false);

        	$suggestions[] = $suggestion;

		}
	}
    echo json_encode( $suggestions );
 
    exit;
}

add_filter('wp-freeio-add-freelancer-favorite-return', 'freeio_freelancer_add_remove_freelancer_favorite_return', 10, 2);
add_filter('wp-freeio-remove-freelancer-favorite-return', 'freeio_freelancer_add_remove_freelancer_favorite_return', 10, 2);
function freeio_freelancer_add_remove_freelancer_favorite_return($return, $freelancer_id) {
	$return['html'] = freeio_freelancer_display_favorite_btn($freelancer_id);
	return $return;
}

if(!function_exists('freeio_freelancer_filter_before')){
    function freeio_freelancer_filter_before(){
        echo '<div class="wrapper-fillter"><div class="apus-listing-filter d-sm-flex align-items-center">';
    }
}
if(!function_exists('freeio_freelancer_filter_after')){
    function freeio_freelancer_filter_after(){
        echo '</div></div>';
    }
}
add_action( 'wp_freeio_before_freelancer_archive', 'freeio_freelancer_filter_before' , 9 );
add_action( 'wp_freeio_before_freelancer_archive', 'freeio_freelancer_filter_after' , 101 );


function freeio_check_freelancer_can_download_project_attachment($attachment_id) {
	if ( $attachment_id > 0 && is_user_logged_in() ) {

        $file_post = get_post($attachment_id);
        $file_path = get_attached_file($attachment_id);

        if ( $file_post && $file_path && file_exists($file_path) ) {

            $attch_parnt = get_post_ancestors($attachment_id);
            if (isset($attch_parnt[0])) {
                $attch_parnt = $attch_parnt[0];
            }
            
            $error = true;

            $user_id = WP_Freeio_User::get_user_id();
            $cur_user_obj = wp_get_current_user();

            if ( WP_Freeio_User::is_freelancer($user_id) ) {
                $error = false;
            }

            if ( WP_Freeio_User::is_employer($user_id) ) {
                $user_cand_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                if ($user_cand_id == $attch_parnt) {
                    $error = false;
                }
            }

            if ( in_array('administrator', (array) $cur_user_obj->roles) ) {
                $error = false;
            }

            if ( !$error ) {
                return true;
            }
            
        }
    }
    return false;
}

// Elementor template
add_filter( 'template_include', 'freeio_freelancer_set_template', 100 );
function freeio_freelancer_set_template($template) {
    if ( is_embed() ) {
        return $template;
    }
    if ( is_singular( 'freelancer' ) ) {
    	$template_id = freeio_get_freelancer_elementor_template();
        if ( $template_id ) {
            $template = WP_Freeio_Template_Loader::locate('template-jobs/single-freelancer-elementor');
        }
    } elseif ( freeio_is_freelancers_archive_page() ) {
        if ( freeio_get_config( 'freelancer_archive_elementor_template' ) ) {
            $template = WP_Freeio_Template_Loader::locate('template-jobs/archive-freelancer-elementor');
        }
    }
    return $template;
}

add_action( 'freeio_freelancer_detail_content', 'freeio_freelancer_detail_builder_content', 5 );
function freeio_freelancer_detail_builder_content() {
	$template_id = freeio_get_freelancer_elementor_template();
    if ( $template_id ) {
        $post = get_post($template_id);
        echo apply_filters( 'freeio_generate_post_builder', '', $post, $template_id);
    }
}

add_action( 'freeio_freelancer_archive_content', 'freeio_freelancer_archive_builder_content', 5 );
function freeio_freelancer_archive_builder_content() {
    $template_id = freeio_get_config('freelancer_archive_elementor_template');
    if ( $template_id ) {
        $post = get_post($template_id);
        echo apply_filters( 'freeio_generate_post_builder', '', $post, $template_id);
    }
}