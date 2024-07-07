<?php

function freeio_get_employers( $params = array() ) {
	$params = wp_parse_args( $params, array(
		'limit' => -1,
		'post_status' => 'publish',
		'get_employers_by' => 'recent',
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
		'post_type'         => 'employer',
		'posts_per_page'    => $limit,
		'post_status'       => $post_status,
		'orderby'       => $orderby,
		'order'       => $order,
	);

	$meta_query = array();
	switch ($get_employers_by) {
		case 'recent':
			$query_args['orderby'] = 'date';
			$query_args['order'] = 'DESC';
			break;
		case 'featured':
			$meta_query[] = array(
				'key' => WP_FREEIO_EMPLOYER_PREFIX.'featured',
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
            'taxonomy'      => 'employer_category',
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
			'key'       => WP_FREEIO_EMPLOYER_PREFIX.'show_profile',
			'value'     => 'show',
			'compare'   => '==',
		),
		array(
			'key'       => WP_FREEIO_EMPLOYER_PREFIX.'show_profile',
			'compare' => 'NOT EXISTS',
		),
	);

    if ( !empty($meta_query) ) {
    	$query_args['meta_query'] = $meta_query;
    }

    if ( method_exists('WP_Freeio_Employer', 'employer_restrict_listing_query_args') ) {
	    $query_args = WP_Freeio_Employer::employer_restrict_listing_query_args($query_args, null);
	}
	
	return new WP_Query( $query_args );
}

if ( !function_exists('freeio_employer_content_class') ) {
	function freeio_employer_content_class( $class ) {
		$prefix = 'employers';
		if ( is_singular( 'employer' ) ) {
            $prefix = 'employer';
        }
		if ( freeio_get_config($prefix.'_fullwidth') ) {
			return 'container-fluid';
		}
		return $class;
	}
}
add_filter( 'freeio_employer_content_class', 'freeio_employer_content_class', 1, 1  );

if ( !function_exists('freeio_get_employers_layout_configs') ) {
	function freeio_get_employers_layout_configs() {
		$layout_type = freeio_get_employers_layout_sidebar();
		switch ( $layout_type ) {
		 	case 'left-main':
		 		$configs['left'] = array( 'sidebar' => 'employers-filter-sidebar', 'class' => 'col-lg-3 col-12'  );
		 		$configs['main'] = array( 'class' => 'col-lg-9 col-12' );
		 		break;
		 	case 'main-right':
		 	default:
		 		$configs['right'] = array( 'sidebar' => 'employers-filter-sidebar',  'class' => 'col-lg-3 col-12' ); 
		 		$configs['main'] = array( 'class' => 'col-lg-9 col-12' );
		 		break;
	 		case 'main':
	 			$configs['main'] = array( 'class' => 'col-12' );
	 			break;
		}
		return $configs; 
	}
}

function freeio_get_employers_layout_sidebar() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$layout_type = get_post_meta( $post->ID, 'apus_page_layout', true );
	}
	if ( empty($layout_type) ) {
		$layout_type = freeio_get_config('employer_archive_layout', 'main-right');
	}
	return apply_filters( 'freeio_get_employers_layout_sidebar', $layout_type );
}

function freeio_get_employers_display_mode() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$columns = get_post_meta( $post->ID, 'apus_page_employers_display_mode', true );
	}
	if ( empty($columns) ) {
		$columns = freeio_get_config('employer_display_mode', 3);
	}
	return apply_filters( 'freeio_get_employers_columns', $columns );
}

function freeio_get_employers_inner_style() {
	global $post;
	$display_mode = freeio_get_employers_display_mode();
	if ( $display_mode == 'list' ) {
		$inner_style = 'list';
	} else {
		$inner_style = 'grid';
	}
	return apply_filters( 'freeio_get_employers_inner_style', $inner_style );
}

function freeio_get_employers_columns() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$columns = get_post_meta( $post->ID, 'apus_page_employers_columns', true );
	}
	if ( empty($columns) ) {
		$columns = freeio_get_config('employer_columns', 3);
	}
	return apply_filters( 'freeio_get_employers_columns', $columns );
}

function freeio_get_employers_pagination() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$pagination = get_post_meta( $post->ID, 'apus_page_employers_pagination', true );
	}
	if ( empty($pagination) ) {
		$pagination = freeio_get_config('employers_pagination', 'default');
	}
	return apply_filters( 'freeio_get_employers_pagination', $pagination );
}

function freeio_get_employers_show_top_content() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$show_filter_top = get_post_meta( $post->ID, 'apus_page_employers_show_top_content', true );
	}
	if ( empty($show_filter_top) ) {
		$show_filter_top = freeio_get_config('employers_show_top_content');
	} else {
		if ( $show_filter_top == 'no' ) {
			$show_filter_top = 0;
		}
	}
	return apply_filters( 'freeio_get_employers_show_top_content', $show_filter_top );
}

function freeio_get_employers_show_offcanvas_filter() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$show_offcanvas_filter = get_post_meta( $post->ID, 'apus_page_employers_show_offcanvas_filter', true );
	}
	if ( empty($show_offcanvas_filter) ) {
		$show_offcanvas_filter = freeio_get_config('employers_show_offcanvas_filter');
	} else {
		if ( $show_offcanvas_filter == 'yes' ) {
			$show_offcanvas_filter = true;
		} else {
			$show_offcanvas_filter = false;
		}
	}
	return apply_filters( 'freeio_get_employers_show_offcanvas_filter', $show_offcanvas_filter );
}

function freeio_is_employers_page() {
	if ( is_page() ) {
		$page_name = basename(get_page_template());
		if ( $page_name == 'page-employers.php' ) {
			return true;
		}
	} elseif( is_post_type_archive('employer') || is_tax('employer_category') || is_tax('location') ) {
		return true;
	}
	return false;
}

function freeio_is_employers_archive_page() {
	if( is_post_type_archive('employer') || is_tax('employer_category') || is_tax('location') ) {
		return true;
	}
	return false;
}

function freeio_is_employer_single_page() {
	if ( is_singular('employer') || apply_filters('freeio_is_employer_single', false) ) {
		return true;
	}
	return false;
}


function freeio_employer_placeholder_img_src( $size = 'thumbnail' ) {
	$src               = get_template_directory_uri() . '/images/placeholder.png';
	$placeholder_image = freeio_get_config('employer_placeholder_image');
	if ( !empty($placeholder_image['id']) ) {
        if ( is_numeric( $placeholder_image['id'] ) ) {
			$image = wp_get_attachment_image_src( $placeholder_image['id'], $size );

			if ( ! empty( $image[0] ) ) {
				$src = $image[0];
			}
		} else {
			$src = $placeholder_image;
		}
    }

	return apply_filters( 'freeio_employer_placeholder_img_src', $src );
}

add_action( 'wpfi_ajax_freeio_autocomplete_search_employers', 'freeio_autocomplete_search_employers' );
function freeio_autocomplete_search_employers() {
    // Query for suggestions
    $suggestions = array();
    $args = array(
		'post_type' => 'employer',
		'post_per_page' => 10,
		'fields' => 'ids'
	);
    $filter_params = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;

	$employers = WP_Freeio_Query::get_posts( $args, $filter_params );

	if ( !empty($employers->posts) ) {
		foreach ($employers->posts as $post_id) {
			$post = get_post($post_id);
			
			$suggestion['title'] = freeio_employer_name($post);
			$suggestion['url'] = get_permalink($post_id);
			
			$image = '';
		 	if ( has_post_thumbnail($post_id) ) {
    			$image_id = get_post_thumbnail_id($post_id);
    			if ( $image_id ) {
        			$image = wp_get_attachment_image_url( $image_id, 'thumbnail' );
        		}
			}

			$suggestion['image'] = $image;
			$suggestion['salary'] = '';
	        
        	$suggestions[] = $suggestion;

		}
	}
    echo json_encode( $suggestions );
 
    exit;
}

add_filter('wp-freeio-add-employer-favorite-return', 'freeio_employer_add_remove_employer_favorite_return', 10, 2);
add_filter('wp-freeio-remove-employer-favorite-return', 'freeio_employer_add_remove_employer_favorite_return', 10, 2);
function freeio_employer_add_remove_employer_favorite_return($return, $employer_id) {
	$return['html'] = freeio_employer_display_favorite_btn($employer_id);
	return $return;
}


if(!function_exists('freeio_employer_filter_before')){
    function freeio_employer_filter_before(){
        echo '<div class="wrapper-fillter"><div class="apus-listing-filter d-sm-flex align-items-center">';
    }
}
if(!function_exists('freeio_employer_filter_after')){
    function freeio_employer_filter_after(){
        echo '</div></div>';
    }
}
add_action( 'wp_freeio_before_employer_archive', 'freeio_employer_filter_before' , 9 );
add_action( 'wp_freeio_before_employer_archive', 'freeio_employer_filter_after' , 101 );


// Elementor template
add_filter( 'template_include', 'freeio_employer_set_template', 100 );
function freeio_employer_set_template($template) {
    if ( is_embed() ) {
        return $template;
    }
    if ( is_singular( 'employer' ) ) {
    	$template_id = freeio_get_config('employer_single_elementor_template');
        if ( $template_id ) {
            $template = WP_Freeio_Template_Loader::locate('template-jobs/single-employer-elementor');
        }
    } elseif ( freeio_is_employers_archive_page() ) {
        if ( freeio_get_config( 'employer_archive_elementor_template' ) ) {
            $template = WP_Freeio_Template_Loader::locate('template-jobs/archive-employer-elementor');
        }
    }
    return $template;
}

add_action( 'freeio_employer_detail_content', 'freeio_employer_detail_builder_content', 5 );
function freeio_employer_detail_builder_content() {
	$template_id = freeio_get_config('employer_single_elementor_template');
    if ( $template_id ) {
        $post = get_post($template_id);
        echo apply_filters( 'freeio_generate_post_builder', '', $post, $template_id);
    }
}

add_action( 'freeio_employer_archive_content', 'freeio_employer_archive_builder_content', 5 );
function freeio_employer_archive_builder_content() {
    $template_id = freeio_get_config('employer_archive_elementor_template');
    if ( $template_id ) {
        $post = get_post($template_id);
        echo apply_filters( 'freeio_generate_post_builder', '', $post, $template_id);
    }
}