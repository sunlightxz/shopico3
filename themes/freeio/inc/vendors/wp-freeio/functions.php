<?php

function freeio_get_jobs( $params = array() ) {
	$params = wp_parse_args( $params, array(
		'limit' => -1,
		'post_status' => 'publish',
		'get_jobs_by' => 'recent',
		'orderby' => '',
		'order' => '',
		'post__in' => array(),
		'fields' => null, // ids
		'author' => null,
		'category' => array(),
		'type' => array(),
		'location' => array(),
	));
	extract($params);

	$query_args = array(
		'post_type'         => 'job_listing',
		'posts_per_page'    => $limit,
		'post_status'       => $post_status,
		'orderby'       => $orderby,
		'order'       => $order,
	);

	$meta_query = array();
	switch ($get_jobs_by) {
		case 'recent':
			$query_args['orderby'] = 'date';
			$query_args['order'] = 'DESC';
			break;
		case 'featured':
			$meta_query[] = array(
				'key' => WP_FREEIO_JOB_LISTING_PREFIX.'featured',
	           	'value' => 'on',
	           	'compare' => '=',
			);
			break;
		case 'urgent':
			$meta_query[] = array(
				'key' => WP_FREEIO_JOB_LISTING_PREFIX.'urgent',
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
            'taxonomy'      => 'job_listing_category',
            'field'         => 'slug',
            'terms'         => $category,
            'operator'      => 'IN'
        );
    }
    if ( !empty($type) ) {
    	$tax_query[] = array(
            'taxonomy'      => 'job_listing_type',
            'field'         => 'slug',
            'terms'         => $type,
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
    
    if ( !empty($meta_query) ) {
    	$query_args['meta_query'] = $meta_query;
    }

    if ( method_exists('WP_Freeio_Job_Listing', 'job_restrict_listing_query_args') ) {
	    $query_args = WP_Freeio_Job_Listing::job_restrict_listing_query_args($query_args, null);
	}
	
	return new WP_Query( $query_args );
}

if ( !function_exists('freeio_job_content_class') ) {
	function freeio_job_content_class( $class ) {
		$prefix = 'jobs';
		if ( is_singular( 'job_listing' ) ) {
            $prefix = 'job';
        }
		if ( freeio_get_config($prefix.'_fullwidth') ) {
			return 'container-fluid';
		}
		return $class;
	}
}
add_filter( 'freeio_job_content_class', 'freeio_job_content_class', 1 , 1  );

if ( !function_exists('freeio_get_jobs_layout_configs') ) {
	function freeio_get_jobs_layout_configs() {
		$layout_sidebar = freeio_get_jobs_layout_sidebar();

		$sidebar = 'jobs-filter-sidebar';
		switch ( $layout_sidebar ) {
		 	case 'left-main':
		 		$configs['left'] = array( 'sidebar' => $sidebar, 'class' => 'col-lg-3 col-12'  );
		 		$configs['main'] = array( 'class' => 'col-lg-9 col-12' );
		 		break;
		 	case 'main-right':
		 	default:
		 		$configs['right'] = array( 'sidebar' => $sidebar,  'class' => 'col-lg-3 col-12' ); 
		 		$configs['main'] = array( 'class' => 'col-lg-9 col-12' );
		 		break;
	 		case 'main':
	 			$configs['main'] = array( 'class' => 'col-12' );
	 			break;
		}
		return $configs; 
	}
}

function freeio_get_jobs_layout_sidebar() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$layout_type = get_post_meta( $post->ID, 'apus_page_layout', true );
	}
	if ( empty($layout_type) ) {
		$layout_type = freeio_get_config('jobs_layout_sidebar', 'main-right');
	}
	return apply_filters( 'freeio_get_jobs_layout_sidebar', $layout_type );
}

function freeio_get_jobs_layout_type() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$layout_type = get_post_meta( $post->ID, 'apus_page_jobs_layout_type', true );
	}
	if ( empty($layout_type) ) {
		$layout_type = freeio_get_config('jobs_layout_type', 'default');
	}
	return apply_filters( 'freeio_get_jobs_layout_type', $layout_type );
}

function freeio_get_jobs_display_mode() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$display_mode = get_post_meta( $post->ID, 'apus_page_display_mode', true );
	}
	if ( empty($display_mode) ) {
		$display_mode = freeio_get_config('job_display_mode', 'list');
	}
	return apply_filters( 'freeio_get_jobs_display_mode', $display_mode );
}

function freeio_get_jobs_inner_style() {
	global $post;
	$display_mode = freeio_get_jobs_display_mode();
	if ( $display_mode == 'list' ) {
		$inner_style = 'list';
	} else {
		$inner_style = 'grid';
	}
	return apply_filters( 'freeio_get_jobs_inner_style', $inner_style );
}

function freeio_get_jobs_columns() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$columns = get_post_meta( $post->ID, 'apus_page_jobs_columns', true );
	}
	if ( empty($columns) ) {
		$columns = freeio_get_config('job_columns', 3);
	}
	return apply_filters( 'freeio_get_jobs_columns', $columns );
}

function freeio_get_jobs_pagination() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$pagination = get_post_meta( $post->ID, 'apus_page_jobs_pagination', true );
	}
	if ( empty($pagination) ) {
		$pagination = freeio_get_config('jobs_pagination', 'default');
	}
	return apply_filters( 'freeio_get_jobs_pagination', $pagination );
}

function freeio_get_jobs_show_top_content() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$show_filter_top = get_post_meta( $post->ID, 'apus_page_jobs_show_top_content', true );
	}
	if ( empty($show_filter_top) ) {
		$show_filter_top = freeio_get_config('jobs_show_top_content');
	} else {
		if ( $show_filter_top == 'no' ) {
			$show_filter_top = 0;
		}
	}
	return apply_filters( 'freeio_get_jobs_show_top_content', $show_filter_top );
}

function freeio_get_jobs_show_offcanvas_filter() {
	global $post;
	if ( is_page() && is_object($post) ) {
		$show_offcanvas_filter = get_post_meta( $post->ID, 'apus_page_jobs_show_offcanvas_filter', true );
	}
	if ( empty($show_offcanvas_filter) ) {
		$show_offcanvas_filter = freeio_get_config('jobs_show_offcanvas_filter');
	} else {
		if ( $show_offcanvas_filter == 'yes' ) {
			$show_offcanvas_filter = true;
		} else {
			$show_offcanvas_filter = false;
		}
	}
	return apply_filters( 'freeio_get_jobs_show_offcanvas_filter', $show_offcanvas_filter );
}

function freeio_job_scripts() {
	
	wp_enqueue_style( 'leaflet' );
	wp_enqueue_script( 'jquery-highlight' );
    wp_enqueue_script( 'leaflet' );
    
    wp_enqueue_script( 'control-geocoder' );
    wp_enqueue_script( 'leaflet-markercluster' );
    wp_enqueue_script( 'leaflet-HtmlIcon' );

    if ( wp_freeio_get_option('map_service') == 'google-map' ) {
	    wp_enqueue_script( 'leaflet-GoogleMutant' );
	}

	wp_register_script( 'freeio-job', get_template_directory_uri() . '/js/job.js', array( 'jquery', 'wp-freeio-main' ), '20150330', true );
	wp_localize_script( 'freeio-job', 'freeio_job_opts', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'empty_msg' => apply_filters( 'freeio_autocompleate_search_empty_msg', esc_html__( 'Unable to find any listing that match the currenty query', 'freeio' ) ),
        'show' => __( 'Show', 'freeio' ),
		'hide' => __( 'Hide', 'freeio' )
	));
	wp_enqueue_script( 'freeio-job' );

	$here_map_api_key = '';
	$here_style = '';
	$mapbox_token = '';
	$mapbox_style = '';
	$custom_style = '';
	$map_service = wp_freeio_get_option('map_service', '');
	if ( $map_service == 'mapbox' ) {
		$mapbox_token = wp_freeio_get_option('mapbox_token', '');
		$mapbox_style = wp_freeio_get_option('mapbox_style', 'streets-v11');
	} elseif ( $map_service == 'here' ) {
		$here_map_api_key = wp_freeio_get_option('here_map_api_key', '');
		$here_style = wp_freeio_get_option('here_map_style', 'normal.day');
	} else {
		$custom_style = wp_freeio_get_option('google_map_style', '');
	}

	wp_register_script( 'freeio-job-map', get_template_directory_uri() . '/js/job-map.js', array( 'jquery' ), '20150330', true );
	wp_localize_script( 'freeio-job-map', 'freeio_job_map_opts', array(
		'map_service' => $map_service,
		'mapbox_token' => $mapbox_token,
		'mapbox_style' => $mapbox_style,
		'here_map_api_key' => $here_map_api_key,
		'here_style' => $here_style,
		'custom_style' => $custom_style,
		'default_latitude' => wp_freeio_get_option('default_maps_location_latitude', '43.6568'),
		'default_longitude' => wp_freeio_get_option('default_maps_location_longitude', '-79.4512'),
		'default_pin' => wp_freeio_get_option('default_maps_pin', ''),
	));
	wp_enqueue_script( 'freeio-job-map' );
}
add_action( 'wp_enqueue_scripts', 'freeio_job_scripts', 10 );

function freeio_job_create_resume_pdf_styles() {
	return array(
		get_template_directory() . '/css/all-awesome.css',
		get_template_directory() . '/css/flaticon.css',
		get_template_directory() . '/css/themify-icons.css',
		get_template_directory() . '/css/resume-pdf.css'
	);
}
add_filter( 'wp-freeio-style-pdf', 'freeio_job_create_resume_pdf_styles', 10 );


function freeio_job_template_folder_name($folder) {
	$folder = 'template-jobs';
	return $folder;
}
add_filter( 'wp-freeio-theme-folder-name', 'freeio_job_template_folder_name', 10 );

function freeio_check_post_review($post) {
	if ( (comments_open($post) || get_comments_number($post)) ) {
		if ( $post->post_type == 'employer' ) {
			if ( method_exists('WP_Freeio_Employer', 'check_restrict_review') ) {
				if ( WP_Freeio_Employer::check_restrict_review($post) ) {
					return true;
				} else {
					return false;
				}
			}
		} elseif ( $post->post_type == 'freelancer' ) {
			if ( method_exists('WP_Freeio_Freelancer', 'check_restrict_review') ) {
				if ( WP_Freeio_Freelancer::check_restrict_review($post) ) {
					return true;
				} else {
					return false;
				}
			}
		} elseif ( $post->post_type == 'service' ) {
			if ( wp_freeio_get_option('service_enable_review', 'enable') == 'enable' ) {
				return true;
			} else {
				return false;
			}
		}
		return true;
	}
	return false;
}

function freeio_check_post_review_form($post) {
	if ( (comments_open($post) || get_comments_number($post)) ) {
		if ( $post->post_type == 'employer' ) {
			if ( method_exists('WP_Freeio_Employer', 'check_restrict_review') ) {
				if ( WP_Freeio_Employer::check_restrict_review($post) ) {
					return true;
				} else {
					return false;
				}
			}
		} elseif ( $post->post_type == 'freelancer' ) {
			if ( method_exists('WP_Freeio_Freelancer', 'check_restrict_review') ) {
				if ( WP_Freeio_Freelancer::check_restrict_review($post) ) {
					return true;
				} else {
					return false;
				}
			}
		} elseif ( $post->post_type == 'service' ) {
			if ( method_exists('WP_Freeio_Service', 'check_restrict_review') ) {
				if ( WP_Freeio_Service::check_restrict_review($post) ) {
					return true;
				} else {
					return false;
				}
			}
		}
		return true;
	}
	return false;
}

function freeio_placeholder_img_src( $size = 'thumbnail' ) {
	$src               = get_template_directory_uri() . '/images/placeholder.png';
	$placeholder_image = freeio_get_config('employer_placeholder_image');
	if ( !empty($placeholder_image) ) {
        $src = $placeholder_image;
    }

	return apply_filters( 'freeio_employer_placeholder_img_src', $src );
}

function freeio_locations_walk( $terms, $id_parent, &$dropdown ) {
    foreach ( $terms as $key => $term ) {
        if ( $term->parent == $id_parent ) {
            $dropdown = array_merge( $dropdown, array( $term ) );
            unset($terms[$key]);
            freeio_locations_walk( $terms, $term->term_id,  $dropdown );
        }
    }
}

function freeio_display_phone( $phone, $icon = '', $force_show_phone = false ) {
	if ( empty($phone) ) {
		return;
	}
	$show_full = freeio_get_config('job_show_full_phone', false);
	$hide_phone = !$show_full ? true : false;
	if ( $force_show_phone ) {
		$hide_phone = false;
	}
	$hide_phone = apply_filters('freeio_phone_hide_number', $hide_phone);

	$add_class = '';
    if ( $hide_phone ) {
        $add_class = 'phone-hide';
    }
	?>
	<div class="phone-wrapper <?php echo esc_attr($add_class); ?>">
		<?php if ( $icon ) { ?>
			<i class="<?php echo esc_attr($icon); ?>"></i>
		<?php } ?>
		<a class="phone" href="tel:<?php echo trim($phone); ?>"><?php echo trim($phone); ?></a>
        <?php if ( $hide_phone ) {
            $dispnum = substr($phone, 0, (strlen($phone)-3) ) . str_repeat("*", 3);
        ?>
            <span class="phone-show" onclick="this.parentNode.classList.add('show');"><?php echo trim($dispnum); ?> <span class="bg-theme"><?php esc_html_e('show', 'freeio'); ?></span></span>
        <?php } ?>
	</div>
	<?php
}

function freeio_is_jobs_page() {
	if ( is_page() ) {
		$page_name = basename(get_page_template());
		if ( $page_name == 'page-jobs.php' ) {
			return true;
		}
	} elseif( is_post_type_archive('job_listing') || is_tax('job_listing_category') || is_tax('location') || is_tax('job_listing_tag') || is_tax('job_listing_type') ) {
		return true;
	}
	return false;
}

function freeio_is_jobs_archive_page() {
	if( is_post_type_archive('job_listing') || is_tax('job_listing_category') || is_tax('location') || is_tax('job_listing_tag') || is_tax('job_listing_type') ) {
		return true;
	}
	return false;
}

function freeio_is_job_single_page() {
	if ( is_singular('job_listing') || apply_filters('freeio_is_job_single', false) ) {
		return true;
	}
	return false;
}



add_filter( 'wp-freeio-get-salary-type-html', 'freeio_jobs_salary_type_html', 10, 3 );
function freeio_jobs_salary_type_html($salary_type_html, $salary_type, $post_id) {
	switch ($salary_type) {
		case 'yearly':
			$salary_type_html = esc_html__(' / year', 'freeio');
			break;
		case 'monthly':
			$salary_type_html = esc_html__(' / month', 'freeio');
			break;
		case 'weekly':
			$salary_type_html = esc_html__(' / week', 'freeio');
			break;
		case 'hourly':
			$salary_type_html = esc_html__(' / hour', 'freeio');
			break;
		case 'daily':
			$salary_type_html = esc_html__(' / day', 'freeio');
			break;
		default:
			$types = WP_Freeio_Mixes::get_default_salary_types();
			if ( !empty($types[$salary_type]) ) {
				$salary_type_html = ' / '.$types[$salary_type];
			}
			break;
	}
	return $salary_type_html;
}

function freeio_jobs_get_custom_fields_display_hooks($hooks, $prefix) {
	if ( $prefix == WP_FREEIO_JOB_LISTING_PREFIX ) {
		$hooks['wp-freeio-single-job-employer-info'] = esc_html__('Single Job - Employer Info', 'freeio');
	}
	return $hooks;
}
add_filter( 'wp-freeio-get-custom-fields-display-hooks', 'freeio_jobs_get_custom_fields_display_hooks', 10, 2 );


function freeio_jobs_display_custom_fields_display_hooks($html, $custom_field, $post, $field_name, $output_value, $current_hook) {
	if ( $current_hook === 'wp-freeio-single-job-details' || $current_hook === 'wp-freeio-single-freelancer-details' || $current_hook === 'wp-freeio-single-employer-details' || $current_hook === 'wp-freeio-single-service-details' || $current_hook === 'wp-freeio-single-project-details' ) {
		$icon = !empty($custom_field['icon']) ? $custom_field['icon'] : '';
		ob_start();
        ?>
        <li>
            <div class="icon">
                <?php if ( !empty($icon) ) { ?>
                    <i class="<?php echo esc_attr($icon); ?>"></i>
                <?php } ?>
            </div>
            <div class="details">
                <?php if ( $field_name ) { ?>
                    <div class="text"><?php echo trim($field_name); ?></div>
                <?php } ?>
                <div class="value"><?php echo trim($output_value); ?></div>
            </div>
        </li>
        <?php
        $html = ob_get_clean();
    } elseif ( $current_hook === 'wp-freeio-single-job-employer-info' ) {
    	ob_start();
    	?>
    	<div class="job-meta">
            <h3 class="title"><?php echo trim($field_name); ?>:</h3>
            <div class="value">
                <?php echo trim($output_value); ?>
            </div>
        </div>
    	<?php
    	$html = ob_get_clean();
    }

    return $html;
}
add_filter( 'wp_freeio_display_field_data', 'freeio_jobs_display_custom_fields_display_hooks', 10, 6 );


function freeio_get_post_author($post_id) {
	if ( method_exists('WP_Freeio_Job_Listing', 'get_author_id') ) {
		return WP_Freeio_Job_Listing::get_author_id($post_id);
	}

	return get_post_field( 'post_author', $post_id );
}

function freeio_load_select2(){

	wp_enqueue_script('wpfi-select2');
	wp_enqueue_style('wpfi-select2');
	
}


add_action( 'wpfi_ajax_freeio_get_job_chart', 'freeio_job_get_chart_data' );
add_action( 'wp_ajax_freeio_get_job_chart', 'freeio_job_get_chart_data' );
add_action( 'wp_ajax_nopriv_freeio_get_job_chart', 'freeio_job_get_chart_data' );

function freeio_job_get_chart_data() {
	$return = array();
	if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'freeio-job-chart-nonce' ) ) {
		$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'freeio') );
	   	echo wp_json_encode($return);
	   	exit;
	}
	if ( empty($_REQUEST['post_id']) ) {
		$return = array( 'status' => 'error', 'html' => esc_html__('Post not found', 'freeio') );
		echo wp_json_encode($return);
	   	exit;
	}

	if ( freeio_get_config('main_color') != "" ) {
		$main_color = freeio_get_config('main_color');
	} else {
		$main_color = '#1967D2';
	}

	$post_id = $_REQUEST['post_id'];

	// datas
	$nb_days = !empty($_REQUEST['nb_days']) ? $_REQUEST['nb_days'] : 15;
    $number_days = apply_filters('freeio-get-traffic-data-nb-days', $nb_days);
    if( empty($number_days) ) {
        $number_days = 15;
    }
    $number_days--;

    // labels
    $array_labels = array();
	for ($i=$number_days; $i >= 0; $i--) { 
		$date = strtotime(date("Y-m-d", strtotime("-".$i." day")));
		$array_labels[] = date_i18n(get_option('date_format'), $date);
	}

	// values
	$views_by_date = get_post_meta( $post_id, '_views_by_date', true );
    if ( !is_array( $views_by_date ) ) {
        $views_by_date = array();
    }

    $array_values = array();
	for ($i=$number_days; $i >= 0; $i--) { 
		$date = date("Y-m-d", strtotime("-".$i." day"));
		if ( isset($views_by_date[$date]) ) {
			$array_values[] = $views_by_date[$date];
		} else {
			$array_values[] = 0;
		}
	}

	$return = array(
		'stats_labels' => $array_labels,
		'stats_values' => $array_values,
		'stats_view' => esc_html__('Views', 'freeio'),
		'chart_type' => apply_filters('freeio-job-stats-type', 'line'),
		'bg_color' => apply_filters('freeio-job-stats-bg-color', $main_color),
        'border_color' => apply_filters('freeio-job-stats-border-color', $main_color),
	);
	echo json_encode($return);
	die();
}


add_filter('post_class', 'freeio_set_post_class', 10, 3);
function freeio_set_post_class($classes, $class, $post_id){
    if ( is_admin() ) {
        return $classes;
    }
    $post_type = get_post_type($post_id);

    switch ($post_type) {
    	case 'job_listing':
    		$obj_meta = WP_Freeio_Job_Listing_Meta::get_instance($post_id);
    		$featured = $obj_meta->get_post_meta( 'featured' );
    		$urgent = $obj_meta->get_post_meta( 'urgent' );
    		if ( $featured ) {
    			$classes[] = 'is-featured';
    		}

    		if ( $urgent ) {
    			$classes[] = 'is-urgent';
    		}

    		break;
    	case 'freelancer':
    		$obj_meta = WP_Freeio_Freelancer_Meta::get_instance($post_id);
    		$featured = $obj_meta->get_post_meta( 'featured' );
    		$urgent = $obj_meta->get_post_meta( 'urgent' );

    		if ( $featured ) {
    			$classes[] = 'is-featured';
    		}

    		if ( $urgent ) {
    			$classes[] = 'is-urgent';
    		}

    		break;
		case 'employer':

			$obj_meta = WP_Freeio_Employer_Meta::get_instance($post_id);
			$featured = $obj_meta->get_post_meta( 'featured' );
    		if ( $featured ) {
    			$classes[] = 'is-featured';
    		}

    		break;
		case 'service':

			$obj_meta = WP_Freeio_Service_Meta::get_instance($post_id);
			$featured = $obj_meta->get_post_meta( 'featured' );
    		if ( $featured ) {
    			$classes[] = 'is-featured';
    		}

    		break;
    }

    return $classes;
}



// autocomplete search jobs
add_action( 'wpfi_ajax_freeio_autocomplete_search_jobs', 'freeio_autocomplete_search_jobs' );
function freeio_autocomplete_search_jobs() {
    // Query for suggestions
    $suggestions = array();
    $args = array(
		'post_type' => 'job_listing',
		'post_per_page' => 10,
		'fields' => 'ids'
	);
    $filter_params = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;

	$jobs = WP_Freeio_Query::get_posts( $args, $filter_params );

	if ( !empty($jobs->posts) ) {
		foreach ($jobs->posts as $post_id) {
			$post = get_post($post_id);
			
			$suggestion['title'] = get_the_title($post_id);
			$suggestion['url'] = get_permalink($post_id);

			$obj_job_meta = WP_Freeio_Job_Listing_Meta::get_instance($post_id);

			$image = '';
		 	if ( $obj_job_meta->check_post_meta_exist('logo') && ($logo_url = $obj_job_meta->get_post_meta( 'logo' )) ) {
    			$logo_id = WP_Freeio_Job_Listing::get_post_meta($post_id, 'logo_id', true);
    			if ( $logo_id ) {
        			$image = wp_get_attachment_image_url( $logo_id, 'thumbnail' );
        		} else {
        			$image = $logo_url;
        		}
			} else {
				$author_id = freeio_get_post_author($post_id);
				$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
				if ( has_post_thumbnail($employer_id) ) {
					$image = wp_get_attachment_image_url( get_post_thumbnail_id($employer_id), 'thumbnail' );
				} else {
					$image = freeio_placeholder_img_src();
				}
			}

			$suggestion['image'] = $image;
	        
	        
	        $suggestion['salary'] = freeio_job_display_salary($post, 'icon', false);

        	$suggestions[] = $suggestion;

		}
	}
    echo json_encode( $suggestions );
 
    exit;
}

function freeio_hide_string($str = "") {
    $replaced = "";
    for($i = 0; $i < strlen($str) -1; $i++) $replaced .= "*";

    return substr($str, 0, 1)."".$replaced."".substr($str, -1, 1);
}

function freeio_display_top_content($item_template_id) {
	$content_html  = '';
	if ( did_action( 'elementor/loaded' ) ) {
		$ele_obj = \Elementor\Plugin::$instance;
		if ( '0' !== $item_template_id ) {
		    $item_template_id = freeio_get_lang_post_id($item_template_id, 'elementor_library');
		    $template_content = $ele_obj->frontend->get_builder_content_for_display( $item_template_id );

		    if ( ! empty( $template_content ) ) {
		        $content_html .= $template_content;
		    }
		}
	}
	echo trim($content_html);
}

function freeio_user_avarta($user_id) {
	if ( class_exists('WP_Freeio_User') && (WP_Freeio_User::is_employer($user_id) || WP_Freeio_User::is_freelancer($user_id)) ) {
	    if ( WP_Freeio_User::is_employer($user_id) ) {
	        $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
	        $avatar = get_the_post_thumbnail( $employer_id, 'thumbnail' );
	    } else {
	        $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
	        $avatar = get_the_post_thumbnail( $freelancer_id, 'thumbnail' );
	    }
	}

	if ( !empty($avatar)) {
        echo trim($avatar);
    } else {
        echo get_avatar($user_id, 54);
    }
}

function freeio_display_list_messages ($messages, $post_id, $type) {
	ob_start();
    $user_id = get_current_user_id();
    ?>
    <div class="proposals-message-content">
    	<ul class="list-replies">
			<?php
			$messages = array_reverse($messages);
		    foreach ($messages as $key => $message) {
		    	?>
		    	<li class="<?php echo esc_attr($message['user_id'] == $user_id ? 'yourself-reply' : 'user-reply'); ?> author-id-<?php echo esc_attr($message['user_id']); ?>">
					<?php if ( $message['user_id'] != $user_id ) { ?>
						<div class="avatar">
							<?php freeio_user_avarta( $message['user_id'] ); ?>
						</div>
					<?php } ?>
					<div class="reply-content">
						<!-- date -->
						<div class="post-date">
							<?php
                                $time = $message['time'];
                                echo human_time_diff( $time, current_time( 'timestamp' ) ).' '.esc_html__( 'ago', 'freeio' );
                            ?>
						</div>
						<div class="post-content">
							<?php echo wpautop($message['message']); ?>

							<?php
							if ( !empty($message['attachment_ids']) ) {
								$download_base_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_proposal_attachment');
								?>
								<div class="attachments">
									<?php
									foreach ($message['attachment_ids'] as $id => $cv_url) {
							            $file_info = pathinfo($cv_url);
							            if ( $file_info ) {
							            	$download_url = add_query_arg(array('file_id' => $id, 'post_id' => $post_id, 'type' => $type), $download_base_url);
							            ?>
							            	<div class="item">
								                <a href="<?php echo esc_url($download_url); ?>" class="d-inline-block type-file-message">
								                    <span class="icon_type pre d-inline-block text-theme">
								                        <i class="flaticon-file"></i>
								                    </span>
								                    <?php if ( !empty($file_info['basename']) ) { ?>
								                        <span class="filename"><?php echo esc_html($file_info['basename']); ?></span>
								                    <?php } ?>
								                </a>
							                </div>
							            <?php }
							        } ?>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</li>
				<?php
		    }
		    ?>
		</ul>
    </div>
    <?php
    $return = ob_get_clean();
    return $return;
}

add_filter('wp-freeio-add-job-favorite-return', 'freeio_job_add_remove_job_favorite_return', 10, 2);
add_filter('wp-freeio-remove-job-favorite-return', 'freeio_job_add_remove_job_favorite_return', 10, 2);
function freeio_job_add_remove_job_favorite_return($return, $job_id) {
	$return['html'] = freeio_job_display_favorite_btn($job_id);
	return $return;
}


function freeio_dispute_display_list_messages($return, $proposal_id) {
	$messages = get_post_meta($proposal_id, WP_FREEIO_DISPUTE_PREFIX . 'messages', true);;
    $messages = !empty($messages) ? $messages : array();
    if ( empty($messages) ) {
    	return '';
    }
    return freeio_display_list_messages($messages, $proposal_id, 'project');
}
add_filter('wp-freeio-get-list-dispute-messages', 'freeio_dispute_display_list_messages', 10, 2);



// Elementor template
add_filter( 'template_include', 'freeio_job_set_template', 100 );
function freeio_job_set_template($template) {
    if ( is_embed() ) {
        return $template;
    }
    if ( is_singular( 'job_listing' ) ) {
    	$template_id = freeio_get_config('job_single_elementor_template');
        if ( $template_id ) {
            $template = WP_Freeio_Template_Loader::locate('template-jobs/single-job_listing-elementor');
        }
    } elseif ( freeio_is_jobs_archive_page() ) {
        if ( freeio_get_config( 'job_archive_elementor_template' ) ) {
            $template = WP_Freeio_Template_Loader::locate('template-jobs/archive-job_listing-elementor');
        }
    }
    return $template;
}

add_action( 'freeio_job_detail_content', 'freeio_job_detail_builder_content', 5 );
function freeio_job_detail_builder_content() {
	$template_id = freeio_get_config('job_single_elementor_template');
    if ( $template_id ) {
        $post = get_post($template_id);
        echo apply_filters( 'freeio_generate_post_builder', '', $post, $template_id);
    }
}

add_action( 'freeio_job_archive_content', 'freeio_job_archive_builder_content', 5 );
function freeio_job_archive_builder_content() {
    $template_id = freeio_get_config('job_archive_elementor_template');
    if ( $template_id ) {
        $post = get_post($template_id);
        echo apply_filters( 'freeio_generate_post_builder', '', $post, $template_id);
    }
}


// demo function
function freeio_check_demo_account() {
	if ( defined('FREEIO_DEMO_MODE') && FREEIO_DEMO_MODE ) {
		$user_id = get_current_user_id();
		$user_obj = get_user_by('ID', $user_id);
		if ( strtolower($user_obj->data->user_login) == 'freelancer' || strtolower($user_obj->data->user_login) == 'employer' ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Demo users are not allowed to modify information.', 'freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}
}
add_action('wp-freeio-process-apply-email', 'freeio_check_demo_account', 10);
add_action('wp-freeio-process-apply-internal', 'freeio_check_demo_account', 10);
add_action('wp-freeio-process-remove-applied', 'freeio_check_demo_account', 10);
add_action('wp-freeio-process-add-job-shortlist', 'freeio_check_demo_account', 10);
add_action('wp-freeio-process-remove-job-shortlist', 'freeio_check_demo_account', 10);

add_action('wp-freeio-process-add-freelancer-shortlist', 'freeio_check_demo_account', 10);
add_action('wp-freeio-process-remove-freelancer-shortlist', 'freeio_check_demo_account', 10);

add_action('wp-freeio-process-forgot-password', 'freeio_check_demo_account', 10);
add_action('wp-freeio-process-change-password', 'freeio_check_demo_account', 10);
add_action('wp-freeio-before-delete-profile', 'freeio_check_demo_account', 10);
add_action('wp-freeio-before-remove-job-alert', 'freeio_check_demo_account', 10 );

add_action('wp-freeio-before-process-remove-job', 'freeio_check_demo_account', 10 );
add_action('wp-freeio-before-process-remove-service', 'freeio_check_demo_account', 10 );
add_action('wp-freeio-before-process-remove-project', 'freeio_check_demo_account', 10 );
add_action('wp-freeio-before-process-hire-proposal', 'freeio_check_demo_account', 10 );
add_action('wp-freeio-before-send-proposal-message', 'freeio_check_demo_account', 10 );
add_action('wp-freeio-before-change-proposal-status', 'freeio_check_demo_account', 10 );
add_action('wp-freeio-before-save-service-addon', 'freeio_check_demo_account', 10 );
add_action('wp-freeio-before-remove-service-addon', 'freeio_check_demo_account', 10 );
add_action('wp-freeio-before-send-service-message', 'freeio_check_demo_account', 10 );
add_action('wp-freeio-before-change-service-order-status', 'freeio_check_demo_account', 10 );

function freeio_check_demo_account2($error) {
	if ( defined('FREEIO_DEMO_MODE') && FREEIO_DEMO_MODE ) {
		$user_id = get_current_user_id();
		$user_obj = get_user_by('ID', $user_id);
		if ( strtolower($user_obj->data->user_login) == 'freelancer' || strtolower($user_obj->data->user_login) == 'employer' ) {
			$error[] = esc_html__('Demo users are not allowed to modify information.', 'freeio');
		}
	}
	return $error;
}
add_filter('wp-freeio-submission-validate', 'freeio_check_demo_account2', 10, 2);
add_filter('wp-freeio-edit-validate', 'freeio_check_demo_account2', 10, 2);

function freeio_check_demo_account3($post_id, $prefix) {
	if ( defined('FREEIO_DEMO_MODE') && FREEIO_DEMO_MODE ) {
		$user_id = get_current_user_id();
		$user_obj = get_user_by('ID', $user_id);
		if ( strtolower($user_obj->data->user_login) == 'freelancer' || strtolower($user_obj->data->user_login) == 'employer' ) {
			$_SESSION['messages'][] = array( 'danger', esc_html__('Demo users are not allowed to modify information.', 'freeio') );
			$redirect_url = get_permalink( wp_freeio_get_option('edit_profile_page_id') );
			WP_Freeio_Mixes::redirect( $redirect_url );
			exit();
		}
	}
}
add_action('wp-freeio-process-profile-before-change', 'freeio_check_demo_account3', 10, 2);

function freeio_check_demo_account5($post_id, $prefix) {
	if ( defined('FREEIO_DEMO_MODE') && FREEIO_DEMO_MODE ) {
		$user_id = get_current_user_id();
		$user_obj = get_user_by('ID', $user_id);
		if ( strtolower($user_obj->data->user_login) == 'freelancer' || strtolower($user_obj->data->user_login) == 'employer' ) {
			$_SESSION['messages'][] = array( 'danger', esc_html__('Demo users are not allowed to modify information.', 'freeio') );
			$redirect_url = get_permalink( wp_freeio_get_option('my_resume_page_id') );
			WP_Freeio_Mixes::redirect( $redirect_url );
			exit();
		}
	}
}
add_action('wp-freeio-process-resume-before-change', 'freeio_check_demo_account5', 10, 2);

function freeio_check_demo_account4() {
	if ( defined('FREEIO_DEMO_MODE') && FREEIO_DEMO_MODE ) {
		$user_id = get_current_user_id();
		$user_obj = get_user_by('ID', $user_id);
		if ( strtolower($user_obj->data->user_login) == 'freelancer' || strtolower($user_obj->data->user_login) == 'employer' ) {
			$return['msg'] = esc_html__('Demo users are not allowed to modify information.', 'freeio');
			$return['status'] = false;
			echo json_encode($return); exit;
		}
	}
}
add_action('wp-private-message-before-reply-message', 'freeio_check_demo_account4');
add_action('wp-private-message-before-add-message', 'freeio_check_demo_account4');
add_action('wp-private-message-before-delete-message', 'freeio_check_demo_account4');


if(!function_exists('freeio_job_filter_before')){
    function freeio_job_filter_before(){
        echo '<div class="wrapper-fillter"><div class="apus-listing-filter d-sm-flex align-items-center">';
    }
}
if(!function_exists('freeio_job_filter_after')){
    function freeio_job_filter_after(){
        echo '</div></div>';
    }
}
add_action( 'wp_freeio_before_job_archive', 'freeio_job_filter_before' , 9 );
add_action( 'wp_freeio_before_job_archive', 'freeio_job_filter_after' , 101 );