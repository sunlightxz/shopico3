<?php
/**
 * Job Filter
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Project_Filter extends WP_Freeio_Abstract_Filter {
	
	public static function init() {
		add_action( 'pre_get_posts', array( __CLASS__, 'archive' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'taxonomy' ) );
	}

	public static function get_fields() {
		return apply_filters( 'wp-freeio-default-project-filter-fields', array());
	}
	
	public static function archive($query) {
		$suppress_filters = ! empty( $query->query_vars['suppress_filters'] ) ? $query->query_vars['suppress_filters'] : '';

		if ( ! is_post_type_archive( 'project' ) || ! $query->is_main_query() || is_admin() || $query->query_vars['post_type'] != 'project' || $suppress_filters ) {
			return;
		}

		$limit = wp_freeio_get_option('number_projects_per_page', 10);
		$query_vars = &$query->query_vars;
		$query_vars['posts_per_page'] = $limit;
		$query->query_vars = $query_vars;
		
		return self::filter_query( $query );
	}

	public static function taxonomy($query) {
		$is_correct_taxonomy = false;
		if ( is_tax( 'project_tag' ) || is_tax( 'location' ) || is_tax( 'project_category' ) || apply_filters( 'wp-freeio-project-query-taxonomy', false ) ) {
			$is_correct_taxonomy = true;
		}

		if ( ! $is_correct_taxonomy  || ! $query->is_main_query() || is_admin() ) {
			return;
		}

		$limit = wp_freeio_get_option('number_projects_per_page', 10);
		$query_vars = $query->query_vars;
		$query_vars['posts_per_page'] = $limit;
		$query->query_vars = $query_vars;

		return self::filter_query( $query );
	}


	public static function filter_query( $query = null, $params = array() ) {
		global $wpdb, $wp_query;

		if ( empty( $query ) ) {
			$query = $wp_query;
		}

		if ( empty( $params ) ) {
			$params = $_GET;
		}
		
		// Filter params
		$params = apply_filters( 'wp_freeio_project_filter_params', $params );

		// Initialize variables
		$query_vars = $query->query_vars;
		$query_vars = self::get_query_var_filter($query_vars, $params);
		$query->query_vars = $query_vars;

		// Meta query
		$meta_query = self::get_meta_filter($params);
		if ( $meta_query ) {
			$query->set( 'meta_query', $meta_query );
		}

		// Tax query
		$tax_query = self::get_tax_filter($params);
		if ( $tax_query ) {
			$query->set( 'tax_query', $tax_query );
		}
		
		return apply_filters('wp-freeio-project-filter-query', $query, $params);
	}

	public static function get_query_var_filter($query_vars, $params) {
		$ids = null;
		$query_vars = self::orderby($query_vars, $params, WP_FREEIO_PROJECT_PREFIX);

		// Property title
		if ( ! empty( $params['filter-title'] ) ) {
			global $wp_freeio_project_keyword;
			$wp_freeio_project_keyword = sanitize_text_field( wp_unslash($params['filter-title']) );
			$query_vars['s'] = sanitize_text_field( wp_unslash($params['filter-title']) );
			add_filter( 'posts_search', array( __CLASS__, 'get_projects_keyword_search' ) );
		}

		$distance_ids = self::filter_by_distance($params, 'project');
		if ( !empty($distance_ids) ) {
			$ids = self::build_post_ids( $ids, $distance_ids );
		}
    	
    	
		if ( ! empty( $params['filter-author'] ) ) {
			if ( is_array($params['filter-author']) ) {
				$query_vars['author__in'] = array_map( 'sanitize_title', wp_unslash( $params['filter-author'] ) );
			} else {
				$query_vars['author'] = sanitize_text_field( wp_unslash($params['filter-author']) );
			}
		}

		// Post IDs
		if ( is_array( $ids ) && count( $ids ) > 0 ) {
			$query_vars['post__in'] = $ids;
		}

		//date posted
		$date_query = self::filter_by_date_posted($params);
		if ( !empty($date_query) ) {
			$query_vars['date_query'] = $date_query;
		}

		return $query_vars;
	}

	public static function get_meta_filter($params) {
		$meta_query = array();
		// price
    	if ( ! empty( $params['filter-price_type'] ) ) {
			$meta_query[] = array(
				'key'       => WP_FREEIO_PROJECT_PREFIX . 'price_type',
				'value'     => sanitize_text_field( wp_unslash($params['filter-price_type']) ),
				'compare'   => '==',
			);
		}
		
    	if ( isset( $params['filter-price-from'] ) && isset( $params['filter-price-to'] ) ) {
    		$price_meta_query = array( 'relation' => 'OR' );
			if ( isset($params['filter-price-from']) && intval($params['filter-price-from']) >= 0 && isset($params['filter-price-to']) && intval($params['filter-price-to']) > 0) {

				$price_from = $params['filter-price-from'];
				$price_to = $params['filter-price-to'];

				$price_meta_query[] = array(
		           	'key' => WP_FREEIO_PROJECT_PREFIX . 'price',
		           	'value' => array( sanitize_text_field($price_from), sanitize_text_field($price_to) ),
		           	'compare'   => 'BETWEEN',
					'type'      => 'NUMERIC',
		       	);

		       	$price_meta_query[] = array(
		           	'key' => WP_FREEIO_PROJECT_PREFIX . 'max_price',
		           	'value' => array( sanitize_text_field($price_from), sanitize_text_field($price_to) ),
		           	'compare'   => 'BETWEEN',
					'type'      => 'NUMERIC',
		       	);
			}
			$meta_query[] = $price_meta_query;
    	}

    	// meta flexible
    	$meta_obj = WP_Freeio_Project_Meta::get_instance(0);
		$meta_keys = ['project_type', 'location_type', 'english_level'];
		foreach ( $meta_keys as $meta_key ) {
			if ( ! empty( $params['filter-'.$meta_key] ) ) {
				$field = $meta_obj->get_meta_field($meta_key);
				
				$value = $params['filter-'.$meta_key];
				if ( is_array($value) ) {
					$multi_meta = array( 'relation' => 'OR' );
					foreach ($value as $val) {
						if ( !empty($field['type']) && in_array($field['type'], array('pw_multiselect', 'multicheck')) ) {
							$f_val = '"'.$val.'"';
							$compare = 'LIKE';
						} else {
							$f_val = $val;
							$compare = '=';
						}
						$multi_meta[] = array(
							'key'       => WP_FREEIO_PROJECT_PREFIX . $meta_key,
							'value'     => $f_val,
							'compare'   => $compare,
						);
					}
					$meta_query[] = $multi_meta;
				} else {
					if ( !empty($field['type']) && in_array($field['type'], array('pw_multiselect', 'multicheck')) ) {
						$f_val = '"'.$value.'"';
						$compare = 'LIKE';
					} else {
						$f_val = $value;
						$compare = '=';
					}

					$meta_query[] = array(
						'key'       => WP_FREEIO_PROJECT_PREFIX . $meta_key,
						'value'     => $f_val,
						'compare'   => $compare,
					);
				}
			}
		}
		
		if ( ! empty( $params['filter-featured'] ) ) {
			$meta_query[] = array(
				'key'       => WP_FREEIO_PROJECT_PREFIX . 'featured',
				'value'     => 'on',
				'compare'   => '==',
			);
		}

		// custom fields
		$filter_fields = self::get_fields();
		$meta_query = self::filter_custom_field_meta($meta_query, $params, $filter_fields);

		return $meta_query;
	}

	public static function get_tax_filter($params) {
		$tax_query = array();
		if ( ! empty( $params['filter-category'] ) ) {
			if ( is_array($params['filter-category']) ) {
				$field = is_numeric( $params['filter-category'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-category'] ) ) );

				$tax_query[] = array(
					'taxonomy'  => 'project_category',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-category'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'project_category',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-category']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-location'] ) ) {
			if ( is_array($params['filter-location']) ) {
				$field = is_numeric( $params['filter-location'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-location'] ) ) );

				$location_type = wp_freeio_get_option('location_multiple_fields', 'yes');
			    if ( $location_type === 'no' ) {
					$tax_query[] = array(
						'taxonomy'  => 'location',
						'field'     => $field,
						'terms'     => array_values($values),
						'compare'   => 'IN',
					);
				} else {
					$location_tax_query = array('relation' => 'AND');
					foreach ($values as $key => $value) {
						$location_tax_query[] = array(
							'taxonomy'  => 'location',
							'field'     => $field,
							'terms'     => $value,
							'compare'   => '==',
						);
					}
					$tax_query[] = $location_tax_query;
				}
			} else {
				$field = is_numeric( $params['filter-location'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'location',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-location']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-project_skill'] ) ) {
			if ( is_array($params['filter-project_skill']) ) {
				$field = is_numeric( $params['filter-project_skill'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-project_skill'] ) ) );

				$tax_query[] = array(
					'taxonomy'  => 'project_skill',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-project_skill'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'project_skill',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-project_skill']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-project_duration'] ) ) {
			if ( is_array($params['filter-project_duration']) ) {
				$field = is_numeric( $params['filter-project_duration'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-project_duration'] ) ) );

				$tax_query[] = array(
					'taxonomy'  => 'project_duration',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-project_duration'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'project_duration',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-project_duration']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-project_experience'] ) ) {
			if ( is_array($params['filter-project_experience']) ) {
				$field = is_numeric( $params['filter-project_experience'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-project_experience'] ) ) );

				$tax_query[] = array(
					'taxonomy'  => 'project_experience',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-project_experience'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'project_experience',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-project_experience']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-freelancer_type'] ) ) {
			if ( is_array($params['filter-freelancer_type']) ) {
				$field = is_numeric( $params['filter-freelancer_type'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-freelancer_type'] ) ) );

				$tax_query[] = array(
					'taxonomy'  => 'project_freelancer_type',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-freelancer_type'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'project_freelancer_type',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-freelancer_type']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-project_language'] ) ) {
			if ( is_array($params['filter-project_language']) ) {
				$field = is_numeric( $params['filter-project_language'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-project_language'] ) ) );

				$tax_query[] = array(
					'taxonomy'  => 'project_language',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-project_language'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'project_language',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-project_language']) ),
					'compare'   => '==',
				);
			}
		}

		if ( ! empty( $params['filter-project_level'] ) ) {
			if ( is_array($params['filter-project_level']) ) {
				$field = is_numeric( $params['filter-project_level'][0] ) ? 'term_id' : 'slug';
				$values = array_filter( array_map( 'sanitize_title', wp_unslash( $params['filter-project_level'] ) ) );

				$tax_query[] = array(
					'taxonomy'  => 'project_level',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-project_level'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'project_level',
					'field'     => $field,
					'terms'     => sanitize_text_field( wp_unslash($params['filter-project_level']) ),
					'compare'   => '==',
				);
			}
		}

		return $tax_query;
	}

	public static function get_projects_keyword_search( $search ) {
		global $wpdb, $wp_freeio_project_keyword;

		if (empty($search)) {
	        return $search; // skip processing - no search term in query
	    }
	    
	    $search = '';
	   	if ( $wp_freeio_project_keyword ) {
	        $search = "($wpdb->posts.post_title LIKE '%{$wp_freeio_project_keyword}%')";
	    }

	    if (!empty($search)) {
	        $search = " AND ({$search}) ";
	        if (!is_user_logged_in()) {
	            $search .= " AND ($wpdb->posts.post_password = '') ";
	        }
	    }

		// Searchable Meta Keys: set to empty to search all meta keys.
		$searchable_meta_keys = array(
			WP_FREEIO_PROJECT_PREFIX.'address',
		);

		$searchable_meta_keys = apply_filters( 'wp_freeio_searchable_meta_keys', $searchable_meta_keys );

		// Set Search DB Conditions.
		$conditions = array();

		// Search Post Meta.
		if ( apply_filters( 'wp_freeio_search_post_meta', true ) ) {

			// Only selected meta keys.
			if ( $searchable_meta_keys ) {
				$conditions[] = "{$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ( '" . implode( "','", array_map( 'esc_sql', $searchable_meta_keys ) ) . "' ) AND meta_value LIKE '%" . esc_sql( $wp_freeio_project_keyword ) . "%' )";
			} else {
				// No meta keys defined, search all post meta value.
				$conditions[] = "{$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%" . esc_sql( $wp_freeio_project_keyword ) . "%' )";
			}
		}

		// Search taxonomy.
		$conditions[] = "{$wpdb->posts}.ID IN ( SELECT object_id FROM {$wpdb->term_relationships} AS tr LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id WHERE t.name LIKE '%" . esc_sql( $wp_freeio_project_keyword ) . "%' )";
		
		$conditions = apply_filters( 'wp_freeio_search_conditions', $conditions, $wp_freeio_project_keyword );
		if ( empty( $conditions ) ) {
			return $search;
		}

		$conditions_str = implode( ' OR ', $conditions );

		if ( ! empty( $search ) ) {
			$search = preg_replace( '/^ AND /', '', $search );
			$search = " AND ( {$search} OR ( {$conditions_str} ) )";
		} else {
			$search = " AND ( {$conditions_str} )";
		}
		remove_filter( 'posts_search', array( __CLASS__, 'get_projects_keyword_search' ) );
		return $search;
	}

	public static function display_filter_value($key, $value, $filters) {
		$meta_obj = WP_Freeio_Project_Meta::get_instance(0);
		$url = urldecode(WP_Freeio_Mixes::get_full_current_url());
		
		if ( is_array($value) ) {
			$value = array_filter( array_map( 'sanitize_text_field', wp_unslash( $value ) ) );
		} else {
			$value = sanitize_text_field( wp_unslash($value) );
		}
		
		switch ($key) {
			case 'filter-category':
				self::render_filter_tax($key, $value, 'project_category', $url);
				break;
			case 'filter-location':
				self::render_filter_tax($key, $value, 'location', $url);
				break;
			case 'filter-project_skill':
				self::render_filter_tax($key, $value, 'project_skill', $url);
				break;
			case 'filter-project_duration':
				self::render_filter_tax($key, $value, 'project_duration', $url);
				break;
			case 'filter-project_experience':
				self::render_filter_tax($key, $value, 'project_experience', $url);
				break;
			case 'filter-freelancer_type':
				self::render_filter_tax($key, $value, 'freelancer_type', $url);
				break;
			case 'filter-project_language':
				self::render_filter_tax($key, $value, 'project_language', $url);
				break;
			case 'filter-project_level':
				self::render_filter_tax($key, $value, 'project_level', $url);
				break;
			case 'filter-price-from':
				$price = WP_Freeio_Price::format_price($value, true);
				$rm_url = self::remove_url_var($key . '=' . $value, $url);
				self::render_filter_result_item( $price, $rm_url );
				break;
			case 'filter-price-to':
				$price = WP_Freeio_Price::format_price($value, true);
				$rm_url = self::remove_url_var($key . '=' . $value, $url);
				self::render_filter_result_item( $price, $rm_url );
				break;
			case 'filter-project_type':
			case 'filter-english_level':
				$types = WP_Freeio_Mixes::get_default_project_types();
				$title = $value;
				if ( in_array($value, $types) ) {
					$title = $types[$value];
				}
				$rm_url = self::remove_url_var( $key . '=' . $value, $url);
				self::render_filter_result_item( $title, $rm_url );
				break;
			case 'filter-date-posted':
				$options = self::date_posted_options();
				foreach ($options as $option) {
					if ( !empty($option['value']) && $option['value'] == $value ) {
						$title = $option['text'];
						$rm_url = self::remove_url_var( $key . '=' . $value, $url);
						self::render_filter_result_item( $title, $rm_url );
						break;
					}
				}
				break;
			case 'filter-distance':
				if ( !empty($filters['filter-center-location']) ) {
					$distance_type = apply_filters( 'wp_freeio_filter_distance_type', 'miles' );
					$title = $value.' '.$distance_type;
					$rm_url = self::remove_url_var( $key . '=' . $value, $url);
					self::render_filter_result_item( $title, $rm_url );
				}
				break;
			case 'filter-featured':
				$title = esc_html__('Featured', 'wp-freeio');
				$rm_url = self::remove_url_var($key . $key . '=' . $value, $url);
				self::render_filter_result_item( $title, $rm_url );
				break;
			case 'filter-urgent':
				$title = esc_html__('Urgent', 'wp-freeio');
				$rm_url = self::remove_url_var(  $key . '=' . $value, $url);
				self::render_filter_result_item( $title, $rm_url );
				break;
			case 'filter-author':
				if ( !empty($value) ) {
					if ( is_array($value) ) {
						foreach ($value as $val) {
							$employer_id = WP_Freeio_User::get_employer_by_user_id($val);
							if ( $employer_id ) {
								$title = get_the_title($employer_id);
							} else {
								$user_info = get_userdata($val);
								if ( is_object($user_info) ) {
									$title = $user_info->display_name;
								} else {
									$title = $val;
								}
							}
							$rm_url = self::remove_url_var(  $key . '=' . $val, $url);
							self::render_filter_result_item( $title, $rm_url );
						}
					} else {
						$employer_id = WP_Freeio_User::get_employer_by_user_id($value);
						if ( $employer_id ) {
							$title = get_the_title($employer_id);
						} else {
							$user_info = get_userdata($value);
							if ( is_object($user_info) ) {
								$title = $user_info->display_name;
							} else {
								$title = $value;
							}
						}
						$rm_url = self::remove_url_var(  $key . '=' . $value, $url);
						self::render_filter_result_item( $title, $rm_url );
					}
				}
				
				break;
			case 'filter-orderby':
				$orderby_options = apply_filters( 'wp-freeio-projects-orderby', array(
					'menu_order' => esc_html__('Default', 'wp-freeio'),
					'newest' => esc_html__('Newest', 'wp-freeio'),
					'oldest' => esc_html__('Oldest', 'wp-freeio'),
					'random' => esc_html__('Random', 'wp-freeio'),
				));
				$title = $value;
				if ( !empty($orderby_options[$value]) ) {
					$title = $orderby_options[$value];
				}
				$rm_url = self::remove_url_var(  $key . '=' . $value, $url);
				self::render_filter_result_item( $title, $rm_url );
				break;
			case 'filter-location_type':
				$label_key = str_replace('filter-', '', $key);
				$field = $meta_obj->get_meta_field($label_key);
				$options = !empty($field['options']) ? $field['options'] : array();
				if ( is_array($value) ) {
					foreach ($value as $val) {
						$rm_url = self::remove_url_var( $key . '[]=' . $val, $url);
						$val_title = $val;
						if ( !empty($options[$val]) ) {
							$val_title = $options[$val];
						}
						self::render_filter_result_item( $val_title, $rm_url);
					}
				} else {
					$rm_url = self::remove_url_var( $key . '=' . $value, $url);
					$val_title = $value;
					if ( !empty($options[$value]) ) {
						$val_title = $options[$value];
					}
					self::render_filter_result_item( $val_title, $rm_url);
				}
				break;
			default:
				if ( is_array($value) ) {
					foreach ($value as $val) {
						$rm_url = self::remove_url_var( $key . '[]=' . $val, $url);
						self::render_filter_result_item( $val, $rm_url);
					}
				} else {
					$rm_url = self::remove_url_var( $key . '=' . $value, $url);
					self::render_filter_result_item( $value, $rm_url);
				}
				
				break;
		}
	}

}

WP_Freeio_Project_Filter::init();