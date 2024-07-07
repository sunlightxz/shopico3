<?php
/**
 * Custom Fields
 *
 * @package    wp-freeio
 * @author     Habq
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Custom_Fields {
	
	public static function init() {
		// submit job admin
		add_filter( 'wp-freeio-job_listing-fields-admin', array( __CLASS__, 'admin_job_listing_custom_fields' ), 10 );

		// submit job frontend
		add_filter( 'wp-freeio-job_listing-fields-front', array( __CLASS__, 'front_job_listing_custom_fields' ), 100, 2 );

		// job filter fields
		add_filter( 'wp-freeio-default-job_listing-filter-fields', array( __CLASS__, 'filter_job_listing_custom_fields' ), 100 );

		// Employer
		// submit admin
		add_filter( 'wp-freeio-employer-fields-admin', array( __CLASS__, 'admin_employer_custom_fields' ), 10 );

		// profile frontend
		add_filter( 'wp-freeio-employer-fields-front', array( __CLASS__, 'front_employer_custom_fields' ), 100, 2 );

		// filter fields
		add_filter( 'wp-freeio-default-employer-filter-fields', array( __CLASS__, 'filter_employer_custom_fields' ), 100 );

		// Freelancer
		// submit admin
		add_filter( 'wp-freeio-freelancer-fields-admin', array( __CLASS__, 'admin_freelancer_custom_fields' ), 10 );
		
		// profile frontend
		add_filter( 'wp-freeio-freelancer-fields-front', array( __CLASS__, 'front_freelancer_profile_custom_fields' ), 100, 2 );

		// resume frontend
		add_filter( 'wp-freeio-freelancer-fields-resume-front', array( __CLASS__, 'front_freelancer_resume_custom_fields' ), 100, 2 );

		// filter fields
		add_filter( 'wp-freeio-default-freelancer-filter-fields', array( __CLASS__, 'filter_freelancer_custom_fields' ), 100 );


		// submit service admin
		add_filter( 'wp-freeio-service-fields-admin', array( __CLASS__, 'admin_service_custom_fields' ), 10 );

		// submit service frontend
		add_filter( 'wp-freeio-service-fields-front', array( __CLASS__, 'front_service_custom_fields' ), 100, 2 );

		// service filter fields
		add_filter( 'wp-freeio-default-service-filter-fields', array( __CLASS__, 'filter_service_custom_fields' ), 100 );


		// submit project admin
		add_filter( 'wp-freeio-project-fields-admin', array( __CLASS__, 'admin_project_custom_fields' ), 10 );

		// submit project frontend
		add_filter( 'wp-freeio-project-fields-front', array( __CLASS__, 'front_project_custom_fields' ), 100, 2 );

		// project filter fields
		add_filter( 'wp-freeio-default-project-filter-fields', array( __CLASS__, 'filter_project_custom_fields' ), 100 );
	}
	
	public static function admin_job_listing_custom_fields() {
		$prefix = WP_FREEIO_JOB_LISTING_PREFIX;
		$init_fields = self::get_custom_fields(array(), 'admin', 0, $prefix);
		$fields = array();
		$key_tab = 'tab-heading-start'.rand(100,1000);
		$tab_data = array(
			'id' => $key_tab,
			'icon' => 'dashicons-admin-home',
			'title'  => esc_html__( 'General', 'wp-freeio' ),
			'fields' => array(),
		);
		$i = 0;
		foreach ($init_fields as $key => $field) {
			if ( $i == 0 && (empty($field['type']) || $field['type'] !== 'title') ) {
				$fields[$key_tab] = $tab_data;
			} elseif ( !empty($field['type']) && $field['type'] == 'title' ) {
				$key_tab = $field['id'];
				$fields[$key_tab] = array(
					'id' => $key_tab,
					'icon' => !empty($field['icon']) ? $field['icon'] : '',
					'title'  => !empty($field['name']) ? $field['name'] : '',
					'fields' => array(),
				);
			}

			$fields[$key_tab]['fields'][] = $field;
			$i++;
		}
		
		// author fields
		$post_author_id = $post_employer_id = '';
		if ( !empty($_GET['post']) ) {
			$post_author_id = get_post_field( 'post_author', $_GET['post'] );
			$post_employer_id = get_post_meta($_GET['post'], $prefix.'employer_posted_by', true);
			if ( empty($post_employer_id) && WP_Freeio_User::is_employer($post_author_id) ) {
				$post_employer_id = WP_Freeio_User::get_employer_by_user_id($post_author_id);
			}
		}
		$author_key = 'tab-heading-author'.rand(100,1000);
		$fields[$author_key] = array(
			'id' => $author_key,
			'icon' => 'dashicons-admin-users',
			'title'  => esc_html__( 'Posted By', 'wp-freeio' ),
			'fields' => array(
				
				array(
					'name'          => __( 'Employer Author', 'wp-freeio' ),
					'id'            => $prefix . 'employer_posted_by',
					'type'          => 'post_ajax_search',
					'default'		=> $post_employer_id,
					'query_args'    => array(
	                    'post_type'			=> array( 'employer' ),
						'posts_per_page'	=> -1
	                ),
	                'attributes' => array(
	                	'placeholder' => esc_html__('Search employers user...', 'wp-freeio')
	                )
				)
			),
		);

		$box_options = array(
			'id'           => 'job_listing_metabox',
			'title'        => esc_html__( 'Job Data', 'wp-freeio' ),
			'object_types' => array( 'job_listing' ),
			'show_names'   => true,
		);
		
		// Setup meta box
		$cmb = new_cmb2_box( $box_options );

		// Set tabs
		$cmb->add_field( [
			'id'   => '__tabs',
			'type' => 'tabs',
			'tabs' => array(
				'config' => $box_options,
				'layout' => 'vertical', // Default : horizontal
				'tabs'   => apply_filters('wp-freeio-job_listing-admin-custom-fields', $fields),
			),
		] );

		return true;
	}

	public static function front_job_listing_custom_fields($old_fields, $post_id) {
		$prefix = WP_FREEIO_JOB_LISTING_PREFIX;
		$fields = self::get_custom_fields($old_fields, 'front', $post_id, $prefix);

		return apply_filters( 'wp-freeio-job_listing-types-submit_form_fields', $fields, $old_fields, $post_id);
	}

	public static function filter_job_listing_custom_fields($old_fields) {
		$prefix = WP_FREEIO_JOB_LISTING_PREFIX;
		$fields = self::get_search_custom_fields($old_fields, 'all', $prefix);

		if ( !empty($fields['center-location']) ) {
			$fields['distance'] = array(
				'name' => __( 'Search Distance', 'wp-freeio' ),
				'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_distance'),
				'placeholder' => __( 'Distance', 'wp-freeio' ),
				'toggle' => false,
				'for_post_type' => 'job_listing',
			);
		}

		$fields['date-posted'] = array(
			'name' => __( 'Date Posted', 'wp-freeio' ),
			'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_date_posted'),
			'toggle' => false,
			'for_post_type' => 'job_listing',
		);

		return apply_filters( 'wp-freeio-job_listing-types-add_custom_fields', $fields, $old_fields);
	}

	public static function admin_service_custom_fields() {
		$prefix = WP_FREEIO_SERVICE_PREFIX;
		$init_fields = self::get_custom_fields(array(), 'admin', 0, $prefix);
		$fields = array();
		$key_tab = 'tab-heading-start'.rand(100,1000);
		$tab_data = array(
			'id' => $key_tab,
			'icon' => 'dashicons-admin-home',
			'title'  => esc_html__( 'General', 'wp-freeio' ),
			'fields' => array(),
		);
		$i = 0;

		$delivery_time_options = array();
		foreach ($init_fields as $field) {
			if ( $field['id'] == $prefix.'delivery_time' ) {
				$delivery_time_options = $field['options'];
				break;
			}
		}

		foreach ($init_fields as $key => &$field) {
			if ( $delivery_time_options ) {
				if ( $field['id'] === $prefix.'price_packages' ) {
					if ( !empty($field['fields']) ) {
						foreach ($field['fields'] as &$value) {
							if ( $value['id'] == 'delivery_time' ) {
								$value['options'] = $delivery_time_options;
							}
						}
					}
				}
			}
			if ( $i == 0 && (empty($field['type']) || $field['type'] !== 'title') ) {
				$fields[$key_tab] = $tab_data;
			} elseif ( !empty($field['type']) && $field['type'] == 'title' ) {
				$key_tab = $field['id'];
				$fields[$key_tab] = array(
					'id' => $key_tab,
					'icon' => !empty($field['icon']) ? $field['icon'] : '',
					'title'  => !empty($field['name']) ? $field['name'] : '',
					'fields' => array(),
				);
			}

			$fields[$key_tab]['fields'][] = $field;
			$i++;
		}
		
		// author fields
		$post_author_id = $post_service_id = '';
		if ( !empty($_GET['post']) ) {
			$post_author_id = get_post_field( 'post_author', $_GET['post'] );
			$post_service_id = get_post_meta($_GET['post'], $prefix.'service_posted_by', true);
			if ( empty($post_service_id) && WP_Freeio_User::is_freelancer($post_author_id) ) {
				$post_service_id = WP_Freeio_User::get_freelancer_by_user_id($post_author_id);
			}
		}
		$author_key = 'tab-heading-author'.rand(100,1000);
		$fields[$author_key] = array(
			'id' => $author_key,
			'icon' => 'dashicons-admin-users',
			'title'  => esc_html__( 'Posted By', 'wp-freeio' ),
			'fields' => array(
				
				array(
					'name'          => __( 'Freelancer Author', 'wp-freeio' ),
					'id'            => $prefix . 'freelancer_posted_by',
					'type'          => 'post_ajax_search',
					'default'		=> $post_service_id,
					'query_args'    => array(
	                    'post_type'			=> array( 'freelancer' ),
						'posts_per_page'	=> -1
	                ),
	                'attributes' => array(
	                	'placeholder' => esc_html__('Search freelancer user...', 'wp-freeio')
	                )
				)
			),
		);

		

		$box_options = array(
			'id'           => 'service_metabox',
			'title'        => esc_html__( 'Service Data', 'wp-freeio' ),
			'object_types' => array( 'service' ),
			'show_names'   => true,
		);
		
		// Setup meta box
		$cmb = new_cmb2_box( $box_options );

		// Set tabs
		$cmb->add_field( [
			'id'   => '__tabs',
			'type' => 'tabs',
			'tabs' => array(
				'config' => $box_options,
				'layout' => 'vertical', // Default : horizontal
				'tabs'   => apply_filters('wp-freeio-service-admin-custom-fields', $fields),
			),
		] );

		return true;
	}

	public static function front_service_custom_fields($old_fields, $post_id) {
		$prefix = WP_FREEIO_SERVICE_PREFIX;
		$fields = self::get_custom_fields($old_fields, 'front', $post_id, $prefix);

		return apply_filters( 'wp-freeio-service-types-submit_form_fields', $fields, $old_fields, $post_id);
	}

	public static function filter_service_custom_fields($old_fields) {
		$prefix = WP_FREEIO_SERVICE_PREFIX;
		$fields = self::get_search_custom_fields($old_fields, 'all', $prefix);

		if ( !empty($fields['center-location']) ) {
			$fields['distance'] = array(
				'name' => __( 'Search Distance', 'wp-freeio' ),
				'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_distance'),
				'placeholder' => __( 'Distance', 'wp-freeio' ),
				'toggle' => false,
				'for_post_type' => 'service',
			);
		}

		$fields['date-posted'] = array(
			'name' => __( 'Date Posted', 'wp-freeio' ),
			'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_date_posted'),
			'toggle' => false,
			'for_post_type' => 'service',
		);

		$fields['rating'] = array(
			'name' => __( 'Rating', 'wp-freeio' ),
			'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_rating_select'),
			'toggle' => false,
			'for_post_type' => 'service',
		);

		return apply_filters( 'wp-freeio-service-types-add_custom_fields', $fields, $old_fields);
	}

	public static function admin_project_custom_fields() {
		$prefix = WP_FREEIO_PROJECT_PREFIX;
		$init_fields = self::get_custom_fields(array(), 'admin', 0, $prefix);

		$fields = array();
		$key_tab = 'tab-heading-start'.rand(100,1000);
		$tab_data = array(
			'id' => $key_tab,
			'icon' => 'dashicons-admin-home',
			'title'  => esc_html__( 'General', 'wp-freeio' ),
			'fields' => array(),
		);
		$i = 0;
		foreach ($init_fields as $key => $field) {
			if ( $i == 0 && (empty($field['type']) || $field['type'] !== 'title') ) {
				$fields[$key_tab] = $tab_data;
			} elseif ( !empty($field['type']) && $field['type'] == 'title' ) {
				$key_tab = $field['id'];
				$fields[$key_tab] = array(
					'id' => $key_tab,
					'icon' => !empty($field['icon']) ? $field['icon'] : '',
					'title'  => !empty($field['name']) ? $field['name'] : '',
					'fields' => array(),
				);
			}

			$fields[$key_tab]['fields'][] = $field;
			$i++;
		}
		
		// author fields
		$post_author_id = $post_project_id = '';
		if ( !empty($_GET['post']) ) {
			$post_author_id = get_post_field( 'post_author', $_GET['post'] );
			$post_project_id = get_post_meta($_GET['post'], $prefix.'project_posted_by', true);
			if ( empty($post_project_id) && WP_Freeio_User::is_employer($post_author_id) ) {
				$post_project_id = WP_Freeio_User::get_employer_by_user_id($post_author_id);
			}
		}
		$author_key = 'tab-heading-author'.rand(100,1000);
		$fields[$author_key] = array(
			'id' => $author_key,
			'icon' => 'dashicons-admin-users',
			'title'  => esc_html__( 'Posted By', 'wp-freeio' ),
			'fields' => array(
				
				array(
					'name'          => __( 'Employer Author', 'wp-freeio' ),
					'id'            => $prefix . 'employer_posted_by',
					'type'          => 'post_ajax_search',
					'default'		=> $post_project_id,
					'query_args'    => array(
	                    'post_type'			=> array( 'employer' ),
						'posts_per_page'	=> -1
	                ),
	                'attributes' => array(
	                	'placeholder' => esc_html__('Search freelancer user...', 'wp-freeio')
	                )
				)
			),
		);

		$box_options = array(
			'id'           => 'project_metabox',
			'title'        => esc_html__( 'Service Data', 'wp-freeio' ),
			'object_types' => array( 'project' ),
			'show_names'   => true,
		);
		
		// Setup meta box
		$cmb = new_cmb2_box( $box_options );

		// Set tabs
		$cmb->add_field( [
			'id'   => '__tabs',
			'type' => 'tabs',
			'tabs' => array(
				'config' => $box_options,
				'layout' => 'vertical', // Default : horizontal
				'tabs'   => apply_filters('wp-freeio-project-admin-custom-fields', $fields),
			),
		] );

		return true;
	}

	public static function front_project_custom_fields($old_fields, $post_id) {
		$prefix = WP_FREEIO_PROJECT_PREFIX;
		$fields = self::get_custom_fields($old_fields, 'front', $post_id, $prefix);

		return apply_filters( 'wp-freeio-project-types-submit_form_fields', $fields, $old_fields, $post_id);
	}

	public static function filter_project_custom_fields($old_fields) {
		$prefix = WP_FREEIO_PROJECT_PREFIX;
		$fields = self::get_search_custom_fields($old_fields, 'all', $prefix);

		if ( !empty($fields['center-location']) ) {
			$fields['distance'] = array(
				'name' => __( 'Search Distance', 'wp-freeio' ),
				'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_distance'),
				'placeholder' => __( 'Distance', 'wp-freeio' ),
				'toggle' => false,
				'for_post_type' => 'project',
			);
		}

		$fields['date-posted'] = array(
			'name' => __( 'Date Posted', 'wp-freeio' ),
			'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_date_posted'),
			'toggle' => false,
			'for_post_type' => 'project',
		);

		return apply_filters( 'wp-freeio-project-types-add_custom_fields', $fields, $old_fields);
	}

	public static function admin_employer_custom_fields() {
		$prefix = WP_FREEIO_EMPLOYER_PREFIX;
		$init_fields = self::get_custom_fields(array(), 'admin', 0, $prefix);
		$fields = array();
		$key_tab = 'tab-heading-start'.rand(100,1000);
		$tab_data = array(
			'id' => $key_tab,
			'icon' => 'dashicons-admin-home',
			'title'  => esc_html__( 'General', 'wp-freeio' ),
			'fields' => array(),
		);
		$i = 0;
		foreach ($init_fields as $key => $field) {
			if ( $i == 0 && (empty($field['type']) || $field['type'] !== 'title') ) {
				$fields[$key_tab] = $tab_data;
			} elseif ( !empty($field['type']) && $field['type'] == 'title' ) {
				$key_tab = $field['id'];
				$fields[$key_tab] = array(
					'id' => $key_tab,
					'icon' => !empty($field['icon']) ? $field['icon'] : '',
					'title'  => !empty($field['name']) ? $field['name'] : '',
					'fields' => array(),
				);
			}

			$fields[$key_tab]['fields'][] = $field;
			$i++;
		}
		

		$box_options = array(
			'id'           => 'employer_metabox',
			'title'        => esc_html__( 'Employer Data', 'wp-freeio' ),
			'object_types' => array( 'employer' ),
			'show_names'   => true,
		);
		
		// Setup meta box
		$cmb = new_cmb2_box( $box_options );

		// Set tabs
		$cmb->add_field( [
			'id'   => '__tabs',
			'type' => 'tabs',
			'tabs' => array(
				'config' => $box_options,
				'layout' => 'vertical', // Default : horizontal
				'tabs'   => apply_filters('wp-freeio-employer-admin-custom-fields', $fields),
			),
		] );

		return true;
	}

	public static function front_employer_custom_fields($old_fields, $post_id) {
		$prefix = WP_FREEIO_EMPLOYER_PREFIX;
		$fields = self::get_custom_fields($old_fields, 'front', $post_id, $prefix);
		
		return apply_filters( 'wp-freeio-employer-types-submit_form_fields', $fields, $old_fields, $post_id);
	}

	public static function filter_employer_custom_fields($old_fields) {
		$prefix = WP_FREEIO_EMPLOYER_PREFIX;
		$fields = self::get_search_custom_fields($old_fields, 'all', $prefix);

		if ( !empty($fields['center-location']) ) {
			$fields['distance'] = array(
				'name' => __( 'Search Distance', 'wp-freeio' ),
				'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_distance'),
				'placeholder' => __( 'Distance', 'wp-freeio' ),
				'toggle' => false,
				'for_post_type' => 'employer',
			);
		}

		$fields['rating'] = array(
			'name' => __( 'Rating', 'wp-freeio' ),
			'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_rating_select'),
			'toggle' => false,
			'for_post_type' => 'service',
		);

		return apply_filters( 'wp-freeio-employer-types-add_custom_fields', $fields, $old_fields);
	}

	// Freelancer
	public static function admin_freelancer_custom_fields() {
		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		$init_fields = self::get_custom_fields(array(), 'admin', 0, $prefix);
		$fields = array();
		$key_tab = 'tab-heading-start'.rand(100,1000);
		$tab_data = array(
			'id' => $key_tab,
			'icon' => 'dashicons-admin-home',
			'title'  => esc_html__( 'General', 'wp-freeio' ),
			'fields' => array(),
		);
		$i = 0;
		foreach ($init_fields as $key => $field) {
			if ( $i == 0 && (empty($field['type']) || $field['type'] !== 'title') ) {
				$fields[$key_tab] = $tab_data;
			} elseif ( !empty($field['type']) && $field['type'] == 'title' ) {
				$key_tab = $field['id'];
				$fields[$key_tab] = array(
					'id' => $key_tab,
					'icon' => !empty($field['icon']) ? $field['icon'] : '',
					'title'  => !empty($field['name']) ? $field['name'] : '',
					'fields' => array(),
				);
			}

			$fields[$key_tab]['fields'][] = $field;
			$i++;
		}
		
		$box_options = array(
			'id'           => 'freelancer_metabox',
			'title'        => esc_html__( 'Freelancer Data', 'wp-freeio' ),
			'object_types' => array( 'freelancer' ),
			'show_names'   => true,
		);
		
		// Setup meta box
		$cmb = new_cmb2_box( $box_options );

		// Set tabs
		$cmb->add_field( [
			'id'   => '__tabs',
			'type' => 'tabs',
			'tabs' => array(
				'config' => $box_options,
				'layout' => 'vertical', // Default : horizontal
				'tabs'   => apply_filters('wp-freeio-freelancer-admin-custom-fields', $fields),
			),
		] );

		return true;
	}

	public static function front_freelancer_profile_custom_fields($old_fields, $post_id) {
		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		$fields = self::get_custom_fields($old_fields, 'front', $post_id, $prefix, 'profile');
		
		return apply_filters( 'wp-freeio-freelancer-profile-types-submit_form_fields', $fields, $old_fields, $post_id, 'profile');
	}

	public static function front_freelancer_resume_custom_fields($old_fields, $post_id) {
		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		$fields = self::get_custom_fields($old_fields, 'front', $post_id, $prefix, 'resume');
		
		return apply_filters( 'wp-freeio-freelancer-resume-types-submit_form_fields', $fields, $old_fields, $post_id, 'resume');
	}

	public static function filter_freelancer_custom_fields($old_fields) {
		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		$fields = self::get_search_custom_fields($old_fields, 'all', $prefix);

		if ( !empty($fields['center-location']) ) {
			$fields['distance'] = array(
				'name' => __( 'Search Distance', 'wp-freeio' ),
				'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_distance'),
				'placeholder' => __( 'Distance', 'wp-freeio' ),
				'toggle' => false,
				'for_post_type' => 'freelancer',
			);
		}

		$fields['date-posted'] = array(
			'name' => __( 'Date Posted', 'wp-freeio' ),
			'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_date_posted'),
			'toggle' => false,
			'for_post_type' => 'freelancer',
		);

		$fields['rating'] = array(
			'name' => __( 'Rating', 'wp-freeio' ),
			'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_rating_select'),
			'toggle' => false,
			'for_post_type' => 'freelancer',
		);

		return apply_filters( 'wp-freeio-freelancer-types-add_custom_fields', $fields, $old_fields);
	}

	public static function get_all_custom_fields($old_fields, $admin_field = 'admin', $prefix = WP_FREEIO_JOB_LISTING_PREFIX) {
		$fields = array();

		$custom_all_fields = WP_Freeio_Fields_Manager::get_custom_fields_data($prefix);
		if (is_array($custom_all_fields) && sizeof($custom_all_fields) > 0) {

			$dtypes = WP_Freeio_Fields_Manager::get_all_field_type_keys();
	        if ( $prefix == WP_FREEIO_JOB_LISTING_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_job_listing_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_job_listing_fields_required();
	            $post_type = 'job_listing';
	        } elseif ( $prefix == WP_FREEIO_EMPLOYER_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_employer_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_employer_fields_required();
	            $post_type = 'employer';
	        } elseif ( $prefix == WP_FREEIO_FREELANCER_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_freelancer_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_freelancer_fields_required();
	            $post_type = 'freelancer';
	        } elseif ( $prefix == WP_FREEIO_SERVICE_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_service_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_service_fields_required();
	            $post_type = 'service';
	        } elseif ( $prefix == WP_FREEIO_PROJECT_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_project_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_project_fields_required();
	            $post_type = 'project';
	        }
			$i = 1;

			foreach ($custom_all_fields as $key => $custom_field) {
				
				$fieldkey = !empty($custom_field['type']) ? $custom_field['type'] : '';
				if ( !empty($fieldkey) ) {
					$type = '';
					$required_values = WP_Freeio_Fields_Manager::get_field_id($fieldkey, $required_types);
					$available_values = WP_Freeio_Fields_Manager::get_field_id($fieldkey, $available_types);

					if ( !empty($required_values) ) {
						$field_data = wp_parse_args( $custom_field, $required_values);
						$fieldtype = isset($required_values['type']) ? $required_values['type'] : '';
						$fieldtype_type = 'required';
					} elseif ( !empty($available_values) ) {
						$field_data = wp_parse_args( $custom_field, $available_values);
						$fieldtype = isset($available_values['type']) ? $available_values['type'] : '';
						$fieldtype_type = 'available';
					} elseif ( in_array($fieldkey, $dtypes) ) {
						$fieldkey = isset($custom_field['key']) ? $custom_field['key'] : '';
						$fieldtype = isset($custom_field['type']) ? $custom_field['type'] : '';
						$fieldtype_type = 'custom';
						$field_data = $custom_field;
						if ( in_array($fieldtype, array('heading', 'file', 'url', 'email')) ) {
							continue;
						}
					}

					$id = str_replace($prefix, '', $field_data['id']);
					if ( $id == 'map_location' ) {
						$id = 'center-location';
					}
					
					$fields[$id] = self::render_field($field_data, $fieldkey, $fieldtype, $i, $admin_field, $fieldtype_type, $prefix);
				}
				$i++;
			}
		} else {
			$fields = $old_fields;
		}

		return $fields;
	}

	public static function get_search_custom_fields($old_fields, $admin_field = 'admin', $prefix = WP_FREEIO_JOB_LISTING_PREFIX) {
		$fields = array();

		$custom_all_fields = WP_Freeio_Fields_Manager::get_custom_fields_data($prefix);
		if (is_array($custom_all_fields) && sizeof($custom_all_fields) > 0) {

			$dtypes = WP_Freeio_Fields_Manager::get_all_field_type_keys();
	        if ( $prefix == WP_FREEIO_JOB_LISTING_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_job_listing_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_job_listing_fields_required();
	            $post_type = 'job_listing';
	        } elseif ( $prefix == WP_FREEIO_EMPLOYER_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_employer_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_employer_fields_required();
	            $post_type = 'employer';
	        } elseif ( $prefix == WP_FREEIO_FREELANCER_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_freelancer_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_freelancer_fields_required();
	            $post_type = 'freelancer';
	        } elseif ( $prefix == WP_FREEIO_SERVICE_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_service_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_service_fields_required();
	            $post_type = 'service';
	        } elseif ( $prefix == WP_FREEIO_PROJECT_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_project_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_project_fields_required();
	            $post_type = 'project';
	        }
			$i = 1;

			foreach ($custom_all_fields as $key => $custom_field) {
				
				$fieldkey = !empty($custom_field['type']) ? $custom_field['type'] : '';
				if ( !empty($fieldkey) ) {
					$type = '';
					$required_values = WP_Freeio_Fields_Manager::get_field_id($fieldkey, $required_types);
					$available_values = WP_Freeio_Fields_Manager::get_field_id($fieldkey, $available_types);

					if ( !empty($required_values) ) {
						$field_data = wp_parse_args( $custom_field, $required_values);
						$fieldtype = isset($required_values['type']) ? $required_values['type'] : '';
						$fieldtype_type = 'required';
					} elseif ( !empty($available_values) ) {
						$field_data = wp_parse_args( $custom_field, $available_values);
						$fieldtype = isset($available_values['type']) ? $available_values['type'] : '';
						$fieldtype_type = 'available';
					} elseif ( in_array($fieldkey, $dtypes) ) {
						$fieldkey = isset($custom_field['key']) ? $custom_field['key'] : '';
						$fieldtype = isset($custom_field['type']) ? $custom_field['type'] : '';
						$fieldtype_type = 'custom';
						$field_data = $custom_field;
						if ( in_array($fieldtype, array('heading', 'file', 'url', 'email')) ) {
							continue;
						}
					}

					if ( !in_array($fieldkey, array( $prefix.'heading', $prefix.'featured_image', $prefix.'gallery', $prefix.'description', $prefix.'expiry_date', $prefix.'video', $prefix.'featured_image', $prefix.'gallery', $prefix.'attachments', $prefix.'address', $prefix.'file' )) ) {

						$id = str_replace($prefix, '', $field_data['id']);
						if ( $id == 'map_location' ) {
							$id = 'center-location';
						}
						$fields[$id] = self::render_field($field_data, $fieldkey, $fieldtype, $i, $admin_field, $fieldtype_type, $prefix);

						if ( empty($fields[$id]['field_call_back']) ) {
							if ( !empty($field_data['field_call_back']) ) {
								$fields[$id]['field_call_back'] = $field_data['field_call_back'];
							} else {
								unset($fields[$id]);
							}
						}
					}
				}
				$i++;
			}
		} else {
			$fields = $old_fields;
		}

		return $fields;
	}

	public static function get_register_custom_fields($old_fields, $prefix = WP_FREEIO_EMPLOYER_PREFIX) {
		
		$fields = array();
		
		$custom_all_fields = WP_Freeio_Fields_Manager::get_custom_fields_data($prefix);
		if (is_array($custom_all_fields) && sizeof($custom_all_fields) > 0) {
			
			$dtypes = WP_Freeio_Fields_Manager::get_all_field_type_keys();
	        
	        if ( $prefix == WP_FREEIO_EMPLOYER_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_employer_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_employer_fields_required();
	        } elseif ( $prefix == WP_FREEIO_FREELANCER_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_freelancer_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_freelancer_fields_required();
	        }

			$i = 1;
			foreach ($custom_all_fields as $key => $custom_field) {
				
				$show_in_register_form = !empty($custom_field['show_in_register_form']) ? $custom_field['show_in_register_form'] : false;
				$fieldkey = !empty($custom_field['type']) ? $custom_field['type'] : '';
				if ( !empty($fieldkey) && $show_in_register_form ) {
					$type = '';
					$required_values = WP_Freeio_Fields_Manager::get_field_id($fieldkey, $required_types);
					$available_values = WP_Freeio_Fields_Manager::get_field_id($fieldkey, $available_types);
					if ( !empty($required_values) ) {
						$field_data = wp_parse_args( $custom_field, $required_values);
						$fieldtype = isset($required_values['type']) ? $required_values['type'] : '';
					} elseif ( !empty($available_values) ) {
						$field_data = wp_parse_args( $custom_field, $available_values);
						$fieldtype = isset($available_values['type']) ? $available_values['type'] : '';
					} elseif ( in_array($fieldkey, $dtypes) ) {
						$fieldkey = isset($custom_field['key']) ? $custom_field['key'] : '';
						$fieldtype = isset($custom_field['type']) ? $custom_field['type'] : '';
						$field_data = $custom_field;
					}

					$fields[] = self::render_field($field_data, $fieldkey, $fieldtype, $i, 'front', '', $prefix, true);
					
				}
				$i++;
			}
		} else {
			$fields = $old_fields;
		}
		
		return $fields;
	}

	public static function get_custom_fields($old_fields, $admin_field = 'admin', $post_id = 0, $prefix = WP_FREEIO_JOB_LISTING_PREFIX, $form_type = 'all') {
		
		$fields = array();

		$package_id = 0;
		
		$custom_all_fields = WP_Freeio_Fields_Manager::get_custom_fields_data($prefix);
		if (is_array($custom_all_fields) && sizeof($custom_all_fields) > 0) {

			$dtypes = WP_Freeio_Fields_Manager::get_all_field_type_keys();
	        
	        if ( $prefix == WP_FREEIO_JOB_LISTING_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_job_listing_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_job_listing_fields_required();

	        	if ( $admin_field == 'front' ) {
					$package_id = self::get_package_id($post_id);
				}
	        } elseif ( $prefix == WP_FREEIO_EMPLOYER_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_employer_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_employer_fields_required();
	        } elseif ( $prefix == WP_FREEIO_FREELANCER_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_freelancer_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_freelancer_fields_required();
	        } elseif ( $prefix == WP_FREEIO_SERVICE_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_service_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_service_fields_required();
	        } elseif ( $prefix == WP_FREEIO_PROJECT_PREFIX ) {
	            $available_types = WP_Freeio_Fields_Manager::get_all_types_project_fields_available();
	        	$required_types = WP_Freeio_Fields_Manager::get_all_types_project_fields_required();
	        }

			$i = 1;
			foreach ($custom_all_fields as $key => $custom_field) {
				$check_package_field = true;
				if ( $prefix == WP_FREEIO_JOB_LISTING_PREFIX && $admin_field == 'front' ) {
					$check_package_field = self::check_package_field($custom_field, $package_id);
				}

				$fieldkey = !empty($custom_field['type']) ? $custom_field['type'] : '';
				if ( !empty($fieldkey) && $check_package_field ) {
					$type = '';
					$required_values = WP_Freeio_Fields_Manager::get_field_id($fieldkey, $required_types);
					$available_values = WP_Freeio_Fields_Manager::get_field_id($fieldkey, $available_types);
					if ( !empty($required_values) ) {
						$field_data = wp_parse_args( $custom_field, $required_values);
						$fieldtype = isset($required_values['type']) ? $required_values['type'] : '';
					} elseif ( !empty($available_values) ) {
						$field_data = wp_parse_args( $custom_field, $available_values);
						$fieldtype = isset($available_values['type']) ? $available_values['type'] : '';
					} elseif ( in_array($fieldkey, $dtypes) ) {
						$fieldkey = isset($custom_field['key']) ? $custom_field['key'] : '';
						$fieldtype = isset($custom_field['type']) ? $custom_field['type'] : '';
						$field_data = $custom_field;
					}
					
					if ( $admin_field == 'front' && (!empty($field_data['show_in_submit_form']) || $fieldtype == 'heading') && $fieldkey !== $prefix.'featured' ) {
						if ( $prefix == WP_FREEIO_FREELANCER_PREFIX && $form_type == 'profile' && $field_data['show_in_submit_form_freelancer'] !== 'profile' ) {
							continue;
						} elseif ( $prefix == WP_FREEIO_FREELANCER_PREFIX && $form_type == 'resume' && $field_data['show_in_submit_form_freelancer'] !== 'resume' ) {
							continue;
						}
						$fields[] = self::render_field($field_data, $fieldkey, $fieldtype, $i, $admin_field, '', $prefix);
					} elseif( $admin_field == 'admin' && (!empty($field_data['show_in_admin_edit']) || $fieldtype == 'heading') && !in_array($fieldkey, apply_filters( 'wp-job-board-exclude-fields-admin', array( $prefix.'title', $prefix.'description', $prefix.'category', $prefix.'type', $prefix.'tag', $prefix.'location', $prefix.'featured_image', $prefix.'project_skill', $prefix.'project_duration', $prefix.'project_experience', '_project_freelancer_type', $prefix.'project_language', $prefix.'project_level' )))) {

						$fields[] = self::render_field($field_data, $fieldkey, $fieldtype, $i, $admin_field, '', $prefix);
					} elseif( $admin_field == 'all' ) {
						$fields[] = self::render_field($field_data, $fieldkey, $fieldtype, $i, $admin_field, '', $prefix);
					}
				}
				$i++;
			}
		} else {
			$fields = $old_fields;
		}
		return $fields;
	}

	public static function get_package_id($post_id) {
		$package_id = apply_filters('wp-freeio-get-listing-package-id', 0, $post_id);

		return apply_filters( 'wp-freeio-types-get_package_id', $package_id);
	}

	public static function check_package_field($field, $package_id) {
		$return = false;
		if ( empty($package_id) ) {
			$return = true;
		}
		if ( empty($field['show_in_package']) ) {
			$return = true;
		}
		if ( !empty($field['show_in_package']) ) {
			$package_display = !empty($field['package_display']) ? $field['package_display'] : array();
			if ( !empty($package_display) && is_array($package_display) && in_array($package_id, $package_display) ) {
				$return = true;
			}
		}
		
		return apply_filters( 'wp-freeio-types-check_package_field', $return, $field, $package_id);
	}

	public static function render_field($field_data, $fieldkey, $fieldtype, $priority, $admin_field = 'front', $fieldtype_type = '', $prefix = WP_FREEIO_JOB_LISTING_PREFIX, $register_form = false) {
		$name = stripslashes(isset($field_data['name']) ? $field_data['name'] : '');
		$id = isset($field_data['id']) ? $field_data['id'] : '';
        $placeholder = stripslashes(isset($field_data['placeholder']) ? $field_data['placeholder'] : '');
        $description = stripslashes(isset($field_data['description']) ? $field_data['description'] : '');
        $format = isset($field_data['format']) ? $field_data['format'] : '';
        $required = isset($field_data['required']) ? $field_data['required'] : '';
        $default = isset($field_data['default']) ? $field_data['default'] : '';

		$field = array(
			'name' => $name,
			'id' => $id,
			'type' => $fieldtype,
			'priority' => $priority,
			'description' => $description,
			'default' => $default,
			'attributes' => array()
		);
		if ( !empty($field_data['attributes']) ) {
			$field['attributes'] = $field_data['attributes'];
		}
		if ( $placeholder ) {
			$field['attributes']['placeholder'] = $placeholder;
			$field['placeholder'] = $placeholder;
		}
		if ( $required ) {
			$field['attributes']['required'] = 'required';
			$field['label_cb'] = array( 'WP_Freeio_Mixes', 'required_add_label' );
		}
		if ( $fieldtype_type == 'custom' ) {
			$field['filter-name-prefix'] = 'filter-cfield';
		}
		switch ($fieldtype) {
			case 'wysiwyg':
			case 'textarea':
				if ( $fieldtype_type == 'custom' ) {
					$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_field_input');
				}
				break;
			case 'text':
				$field['type'] = 'text';
				if ( $fieldtype_type == 'custom' ) {
					$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_field_input');
				}
				break;
			case 'number':
				$field['type'] = 'text';
				$field['attributes']['type'] = 'number';
				$field['attributes']['min'] = 0;
				$field['attributes']['pattern'] = '\d*';
				if ( $fieldtype_type == 'custom' ) {
					$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_field_input');
				}
				break;
			case 'url':
				$field['type'] = 'text';
				$field['attributes']['type'] = 'url';
				$field['attributes']['pattern'] = 'https?://.+';
				if ( $fieldtype_type == 'custom' ) {
					$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_field_input');
				}
				break;
			case 'email':
				$field['type'] = 'text';
				$field['attributes']['type'] = 'email';
				$field['attributes']['pattern'] = '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2, 4}$';
				if ( $fieldtype_type == 'custom' ) {
					$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_field_input');
				}
				break;
			case 'date':
				$field['type'] = 'wpfi_datepicker';
				if ( $fieldtype_type == 'custom' ) {
					$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_date_field_input');
					$field['filter-name-prefix'] = 'filter-cfielddate';
				}
				// $field['date_format'] = 'Y-m-d';

				$datepicker_date_format = str_replace(
		            array( 'd', 'j', 'l', 'z', /* Day. */ 'F', 'M', 'n', 'm', /* Month. */ 'Y', 'y', /* Year. */ ),
		            array( 'dd', 'd', 'DD', 'o', 'MM', 'M', 'm', 'mm', 'yy', 'y', ),
		            get_option( 'date_format' )
		        );
				$field['attributes']['data-datepicker'] = json_encode(array(
                        'dateFormat' => $datepicker_date_format,
                        'altField' => '#'.$field['id'],
                        'altFormat' => 'yy-mm-dd',
                    ));
				break;
			case 'checkbox':
				if ( $fieldtype_type == 'custom' ) {
					$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_field_checkbox');
				}
				break;
			case 'radio':
			case 'select':
				$doptions = !empty($field_data['options']) ? $field_data['options'] : array();
				$options = array();
				
				if ( is_array($doptions) ) {
					$options = $doptions;
				} elseif ( !empty($doptions) ) {
					$doptions = explode("\n", str_replace("\r", "", stripslashes($doptions)));
					foreach ($doptions as $val) {
						$options[$val] = trim($val);
					}
				}
				$field['options'] = $options;
				if ( $fieldtype == 'select' ) {
					$field['type'] = 'pw_select';
					$field['attributes']['data-allowclear'] = true;
					$field['attributes']['data-width'] = '100%';

					if ( $fieldtype_type == 'custom' ) {
						$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_field_select');
					}
				} else {
					if ( isset($field['attributes']['required']) ) {
						unset($field['attributes']['required']);
					}

					if ( $fieldtype_type == 'custom' ) {
						$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_field_radio_list');
					}
				}
				
				break;
			case 'multiselect':
				$doptions = !empty($field_data['options']) ? $field_data['options'] : array();
				$options = array();
				
				if ( is_array($doptions) ) {
					$options = $doptions;
				} elseif ( !empty($doptions) ) {
					$doptions = explode("\n", str_replace("\r", "", stripslashes($doptions)));
					foreach ($doptions as $val) {
						$options[$val] = trim($val);
					}
				}
				$field['options'] = $options;
				$field['type'] = 'pw_multiselect';
				if ( $fieldtype_type == 'custom' ) {
					$field['field_call_back'] = array( 'WP_Freeio_Abstract_Filter', 'filter_field_multiselect');
				}
				break;
			case 'file_list':
			case 'file':
				$allow_types = !empty($field_data['allow_types']) ? $field_data['allow_types'] : array();
				$field['allow_types'] = $allow_types;
				
				$multiples = !empty($field_data['multiple_files']) ? $field_data['multiple_files'] : false;
				if ( $register_form ) {
					$ajax = false;
				} else {
					$ajax = !empty($field_data['ajax']) ? $field_data['ajax'] : true;
				}

				
				$field['ajax'] = $ajax ? true : false;
				if ( $ajax ) {
					$field['file_limit'] = !empty($field_data['file_limit']) ? $field_data['file_limit'] : 10;
				}
				if ( $admin_field == 'front' ) {
					$field['type'] = 'wp_freeio_file';
					$field['file_multiple'] = $multiples ? true : false;

					if ( !empty($allow_types) ) {
						$mime_types = array();
						foreach ($allow_types as $mime_type) {
							$tmime = explode('|', $mime_type);
							$mime_types = array_merge($mime_types, $tmime);
						}
						$field['mime_types'] = $mime_types;
					}
				} else {
					if ( !$multiples ) {
						$field['type'] = 'file';
						$field['preview_size'] = 'thumbnail';
					} else {
						$field['type'] = 'file_list';
					}

					if ( !empty($allow_types) ) {
						$allowed_mime_types = array();
						$mime_types = get_allowed_mime_types();
						foreach ($allow_types as $mime_type) {
							if ( isset($mime_types[$mime_type]) ) {
								$allowed_mime_types[$mime_type] = $mime_types[$mime_type];
							}
						}
						$field['query_args']['type'] = $allowed_mime_types;
					}
				}
				break;
			case 'wp_freeio_file':
				$allow_types = !empty($field_data['allow_types']) ? $field_data['allow_types'] : array();
				$field['allow_types'] = $allow_types;

				$multiples = !empty($field_data['multiple_files']) ? $field_data['multiple_files'] : false;
				if ( $register_form ) {
					$ajax = false;
				} else {
					$ajax = !empty($field_data['ajax']) ? $field_data['ajax'] : false;
				}
				
				$field['ajax'] = $ajax ? true : false;
				if ( $ajax ) {
					$field['file_limit'] = !empty($field_data['file_limit']) ? $field_data['file_limit'] : 10;
				}
				if ( $admin_field == 'front' ) {
					$field['file_multiple'] = $multiples ? true : false;
					if ( !empty($allow_types) ) {
						$allowed_mime_types = array();
						$all_mime_types = get_allowed_mime_types();
						$mime_types = array();
						foreach ($allow_types as $mime_type) {
							$tmime = explode('|', $mime_type);
							$mime_types = array_merge($mime_types, $tmime);

							if ( isset($all_mime_types[$mime_type]) ) {
								$allowed_mime_types[] = $all_mime_types[$mime_type];
							}
						}
						$field['mime_types'] = $mime_types;
						$field['allow_mime_types'] = $allowed_mime_types;
					}
				} else {
					if ( !$multiples ) {
						$field['type'] = 'file';
						$field['preview_size'] = 'thumbnail';
					} else {
						$field['type'] = 'file_list';
					}

					if ( !empty($allow_types) ) {
						$allowed_mime_types = array();
						$mime_types = get_allowed_mime_types();
						foreach ($allow_types as $mime_type) {
							if ( isset($mime_types[$mime_type]) ) {
								$allowed_mime_types[$mime_type] = $mime_types[$mime_type];
							}
						}
						$field['query_args']['type'] = $allowed_mime_types;
					}
				}
				break;
			case 'heading':
				$field['type'] = 'title';
				$field['icon'] = !empty($field_data['icon']) ? $field_data['icon'] : '';
				$field['number_columns'] = !empty($field_data['number_columns']) ? $field_data['number_columns'] : '';
			case 'pw_map':
				$field['split_values'] = isset($field_data['split_values']) ? $field_data['split_values'] : false;
				if ( isset($field['attributes']['placeholder']) ) {
					unset($field['attributes']['placeholder']);
				}
			case 'repeater':
			case 'group':
				$subfields = array();
				if ( !empty($field_data['fields']) ) {
					foreach ($field_data['fields'] as $subf) {
						$subfield = $subf;
						if ( !empty($subfield['type']) && $subfield['type'] == 'wp_freeio_file' ) {
							if ( $admin_field == 'admin' ) {
								$subfield['type'] = 'file';
								$subfield['preview_size'] = 'thumbnail';
							}
							$subfields[] = $subfield;
						} elseif ( !empty($subfield['type']) && $subfield['type'] == 'file' ) {
							if ( $admin_field == 'front' ) {
								$subfield['type'] = 'wp_freeio_file';
								if ( $register_form ) {
									$ajax = false;
								} else {
									$ajax = !empty($subfield['ajax']) ? $subfield['ajax'] : false;
								}
								$field['ajax'] = $ajax ? true : false;
								if ( $ajax ) {
									$field['file_limit'] = !empty($subfield['file_limit']) ? $subfield['file_limit'] : 10;
								}
								$field['file_multiple'] = !empty($subfield['file_multiple']) ? $subfield['file_multiple'] : false;
								$field['mime_types'] = !empty($subfield['mime_types']) ? $subfield['mime_types'] : array( 'gif', 'jpeg', 'jpg', 'png' );
							}
							$subfields[] = $subfield;
						} else {
							$subfields[] = $subfield;
						}
					}
				}
				$field['fields'] = $subfields;
				if ( !empty($field_data['options']) ) {
					$field['options'] = $field_data['options'];
				}
				break;
		}
    	
    	switch ($fieldkey) {
			case $prefix.'posted_by':
				if ( $admin_field == 'admin' ) {
					if ( !empty($_GET['post']) ) {
						$author = get_post_field( 'post_author', $_GET['post'] );
					} else {
						$author = get_current_user_id();
					}
				} else {
					$author = get_current_user_id();
				}

				$field['default'] = $author;
			break;
			case $prefix.'description':
				$field['type'] = !empty($field_data['select_type']) ? $field_data['select_type'] : 'wysiwyg';
				if ( !empty($field_data['options']) ) {
					$field['options'] = $field_data['options'];
				}
			break;
			case $prefix.'salary_type':
				$options = !empty($field_data['allow_salary_types']) ? $field_data['allow_salary_types'] : '';
				$salary_types = WP_Freeio_Mixes::get_default_salary_types();
				if ( !empty($options) ) {
					$f_options = array();
					foreach ($options as $opt) {
						if ( !empty($salary_types[$opt]) ) {
							$f_options[$opt] = $salary_types[$opt];
						}
					}
					$field['options'] = $f_options;
				}
			break;
			case $prefix.'apply_type':
				$options = !empty($field_data['allow_apply_types']) ? $field_data['allow_apply_types'] : '';
				$apply_types = WP_Freeio_Mixes::get_default_apply_types();
				if ( !empty($options) ) {
					$f_options = array();
					foreach ($options as $opt) {
						if ( !empty($apply_types[$opt]) ) {
							$f_options[$opt] = $apply_types[$opt];
						}
					}
					$field['options'] = $f_options;
				}
			break;
			case $prefix.'location':
				$field['taxonomy'] = !empty($field_data['taxonomy']) ? $field_data['taxonomy'] : '';
				$location_type = wp_freeio_get_option('location_multiple_fields', 'yes');
				
				if ( $location_type === 'yes' ) {
					$field['type'] = !empty($field_data['select_type_search']) ? $field_data['select_type_search'] : 'wpjb_taxonomy_location';
				} else {
					$field['type'] = !empty($field_data['select_type']) ? $field_data['select_type'] : 'pw_taxonomy_select_search';
				}
			break;
			case $prefix.'tag':
				$field['type'] = 'wp_freeio_tags';
				$field['taxonomy'] = !empty($field_data['taxonomy']) ? $field_data['taxonomy'] : '';
			break;
			case $prefix.'category':
			case $prefix.'type':
			case $prefix.'project_skill':
			case $prefix.'project_duration':
			case $prefix.'project_experience':
			case $prefix.'freelancer_type':
			case $prefix.'project_language':
			case $prefix.'project_level':
				$field['type'] = !empty($field_data['select_type']) ? $field_data['select_type'] : 'taxonomy_select';
				$field['taxonomy'] = !empty($field_data['taxonomy']) ? $field_data['taxonomy'] : '';

				if ( ($field['type'] == 'taxonomy_multicheck' || $field['type'] == 'taxonomy_radio') && isset($field['attributes']['required']) ) {
					unset($field['attributes']['required']);
				}
				break;
			case $prefix.'expiry_date':
			case $prefix.'application_deadline_date':
				$field['date_format'] = !empty($field_data['date_format']) ? $field_data['date_format'] : 'Y-m-d';
				break;
			case WP_FREEIO_JOB_LISTING_PREFIX.'experience':
			case $prefix.'experience_time':
			case $prefix.'gender':
			case $prefix.'industry':
			case $prefix.'qualification':
			case $prefix.'career_level':
			case $prefix.'languages':
			case $prefix.'age':
				$field['type'] = !empty($field_data['select_type']) ? $field_data['select_type'] : 'pw_select';

				if ( ($field['type'] == 'multicheck' || $field['type'] == 'radio') && isset($field['attributes']['required']) ) {
					unset($field['attributes']['required']);
				}
				break;
			case $prefix.'employees':
				$field['multiple'] = !empty($field_data['multiple']) ? $field_data['multiple'] : true;
				$field['query_args'] = !empty($field_data['query_args']) ? $field_data['query_args'] : '';
				break;
		}

		return apply_filters( 'wp-freeio-types-render_field', $field, $field_data, $fieldkey, $fieldtype, $priority);
	}

}
WP_Freeio_Custom_Fields::init();