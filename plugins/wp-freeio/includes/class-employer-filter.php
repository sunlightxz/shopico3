<?php
/**
 * Employer Filter
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Employer_Filter extends WP_Freeio_Abstract_Filter {
	
	public static function init() {
		add_action( 'pre_get_posts', array( __CLASS__, 'archive' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'taxonomy' ) );
	}

	public static function get_fields() {
		return apply_filters( 'wp-freeio-default-employer-filter-fields', array());
	}

	public static function archive($query) {
		$suppress_filters = ! empty( $query->query_vars['suppress_filters'] ) ? $query->query_vars['suppress_filters'] : '';

		if ( ! is_post_type_archive( 'employer' ) || ! $query->is_main_query() || is_admin() || $query->query_vars['post_type'] != 'employer' || $suppress_filters ) {
			return;
		}

		$limit = wp_freeio_get_option('number_employers_per_page', 10);
		$query_vars = $query->query_vars;
		$query_vars['posts_per_page'] = $limit;
		$query->query_vars = $query_vars;

		return self::filter_query( $query );
	}

	public static function taxonomy($query) {
		$is_correct_taxonomy = false;
		if ( is_tax( 'employer_category' ) || is_tax( 'location' ) || apply_filters( 'wp-freeio-employer-query-taxonomy', false ) ) {
			$is_correct_taxonomy = true;
		}

		if ( ! $is_correct_taxonomy  || ! $query->is_main_query() || is_admin() ) {
			return;
		}

		$limit = wp_freeio_get_option('number_employers_per_page', 10);
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
		$params = apply_filters( 'wp_freeio_employer_filter_params', $params );

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
	
		return apply_filters('wp-freeio-employer-filter-query', $query, $params);
	}
	
	public static function get_query_var_filter($query_vars, $params) {
		$ids = null;
		$query_vars = self::orderby($query_vars, $params, WP_FREEIO_EMPLOYER_PREFIX);

		// Property title
		if ( ! empty( $params['filter-title'] ) ) {
			global $wp_freeio_employer_keyword;
			$wp_freeio_employer_keyword = sanitize_text_field( wp_unslash($params['filter-title']) );
			$query_vars['s'] = sanitize_text_field( wp_unslash($params['filter-title']) );
			add_filter( 'posts_search', array( __CLASS__, 'get_employers_keyword_search' ) );
		}

		$distance_ids = self::filter_by_distance($params, 'employer');
		if ( !empty($distance_ids) ) {
			$ids = self::build_post_ids( $ids, $distance_ids );
		}
    	
		// Post IDs
		if ( is_array( $ids ) && count( $ids ) > 0 ) {
			$query_vars['post__in'] = $ids;
		}

		return $query_vars;
	}

	public static function get_meta_filter($params) {
		$meta_query = array();
		if ( ! empty( $params['filter-featured'] ) ) {
			$meta_query[] = array(
				'key'       => WP_FREEIO_EMPLOYER_PREFIX . 'featured',
				'value'     => 'on',
				'compare'   => '==',
			);
		}

		if ( isset( $params['filter-founded_date-from'] ) && isset( $params['filter-founded_date-to'] ) ) {
			if ( intval($params['filter-founded_date-from']) >= 0 && intval($params['filter-founded_date-to']) > 0) {
				$meta_query[] = array(
		           	'key' => WP_FREEIO_EMPLOYER_PREFIX . 'founded_date',
		           	'value' => array( intval($params['filter-founded_date-from']), intval($params['filter-founded_date-to']) ),
		           	'compare'   => 'BETWEEN',
					'type'      => 'NUMERIC',
		       	);
			}
			
    	}

    	// Rating
	    if ( ! empty( $params['filter-rating'] ) ) {
	    	if ( is_array($params['filter-rating']) ) {
	    		$multi_meta = array( 'relation' => 'OR' );
	    		$value = $params['filter-rating'];

	    		if (strpos($value[0], '+') !== false) {
	    			$compare = '>=';
	    		}
	    		foreach ($value as $val) {
	    			$compare = '=';
	    			if (strpos($val, '+') !== false) {
		    			$compare = '>=';
		    			$val = rtrim($val, '+');
		    		}
					$multi_meta[] = array(
						'key'       => '_average_rating',
						'value'     =>  sanitize_text_field( wp_unslash($val) ),
						'compare'   => $compare,
						'type'      => 'NUMERIC',
					);
				}
				$meta_query[] = $multi_meta;
	    	} else {
		    	$value = sanitize_text_field( wp_unslash($params['filter-rating']) );
		    	
		    	if (strpos($value, '+') !== false) {
		    		$value = rtrim($value, '+');
				    $meta_query[] = array(
					    'key'       => '_average_rating',
					    'value'     => $value,
					    'compare'   => '>=',
					    'type'      => 'NUMERIC',
				    );
				} else {
				    $meta_query[] = array(
					    'key'       => '_average_rating',
					    'value'     => $value,
					    'compare'   => '=',
					    'type'      => 'NUMERIC',
				    );
				}
			}
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
					'taxonomy'  => 'employer_category',
					'field'     => $field,
					'terms'     => array_values($values),
					'compare'   => 'IN',
				);
			} else {
				$field = is_numeric( $params['filter-category'] ) ? 'term_id' : 'slug';

				$tax_query[] = array(
					'taxonomy'  => 'employer_category',
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

		return $tax_query;
	}

	public static function get_employers_keyword_search( $search ) {
		global $wpdb, $wp_freeio_employer_keyword;

		if (empty($search)) {
	        return $search; // skip processing - no search term in query
	    }
	    
	    $search = '';
	   	if ( $wp_freeio_employer_keyword ) {
	        $search = "($wpdb->posts.post_title LIKE '%{$wp_freeio_employer_keyword}%')";
	    }

	    if (!empty($search)) {
	        $search = " AND ({$search}) ";
	        if (!is_user_logged_in()) {
	            $search .= " AND ($wpdb->posts.post_password = '') ";
	        }
	    }

		// Searchable Meta Keys: set to empty to search all meta keys.
		$searchable_meta_keys = array(
			WP_FREEIO_EMPLOYER_PREFIX.'address',
			WP_FREEIO_EMPLOYER_PREFIX.'phone',
			WP_FREEIO_EMPLOYER_PREFIX.'website',
		);

		$searchable_meta_keys = apply_filters( 'wp_freeio_searchable_meta_keys', $searchable_meta_keys );

		// Set Search DB Conditions.
		$conditions = array();

		// Search Post Meta.
		if ( apply_filters( 'wp_freeio_search_post_meta', true ) ) {

			// Only selected meta keys.
			if ( $searchable_meta_keys ) {
				$conditions[] = "{$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ( '" . implode( "','", array_map( 'esc_sql', $searchable_meta_keys ) ) . "' ) AND meta_value LIKE '%" . esc_sql( $wp_freeio_employer_keyword ) . "%' )";
			} else {
				// No meta keys defined, search all post meta value.
				$conditions[] = "{$wpdb->posts}.ID IN ( SELECT post_id FROM {$wpdb->postmeta} WHERE meta_value LIKE '%" . esc_sql( $wp_freeio_employer_keyword ) . "%' )";
			}
		}

		// Search taxonomy.
		$conditions[] = "{$wpdb->posts}.ID IN ( SELECT object_id FROM {$wpdb->term_relationships} AS tr LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id WHERE t.name LIKE '%" . esc_sql( $wp_freeio_employer_keyword ) . "%' )";

		$conditions = apply_filters( 'wp_freeio_search_conditions', $conditions, $wp_freeio_employer_keyword );
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
		remove_filter( 'posts_search', array( __CLASS__, 'get_employers_keyword_search' ) );
		return $search;
	}

	public static function display_filter_value($key, $value, $filters) {

		$meta_obj = WP_Freeio_Employer_Meta::get_instance(0);

		$url = urldecode(WP_Freeio_Mixes::get_full_current_url());
		
		if ( is_array($value) ) {
			$value = array_filter( array_map( 'sanitize_text_field', wp_unslash( $value ) ) );
		} else {
			$value = sanitize_text_field( wp_unslash($value) );
		}
		switch ($key) {
			case 'filter-category':
				self::render_filter_tax($key, $value, 'employer_category', $url);
				break;
			case 'filter-location':
				self::render_filter_tax($key, $value, 'location', $url);
				break;
			case 'filter-founded_date-from':
				$rm_url = self::remove_url_var($key . '=' . $value, $url);
				self::render_filter_result_item( $value, $rm_url );
				break;
			case 'filter-founded_date-to':
				$rm_url = self::remove_url_var($key . '=' . $value, $url);
				self::render_filter_result_item( $value, $rm_url );
				break;
			case 'filter-date-posted':
				$options = self::date_posted_options();
				foreach ($options as $option) {
					if ( !empty($option['value']) && $option['value'] == $value ) {
						$title = $option['text'];
						$rm_url = self::remove_url_var(  $key . '=' . $value, $url);
						self::render_filter_result_item( $title, $rm_url );
						break;
					}
				}
				break;
			case 'filter-distance':
				if ( !empty($filters['filter-center-location']) ) {
					$distance_type = apply_filters( 'wp_freeio_filter_distance_type', 'miles' );
					$title = $value.' '.$distance_type;
					$rm_url = self::remove_url_var(  $key . '=' . $value, $url);
					self::render_filter_result_item( $title, $rm_url );
				}
				break;
			case 'filter-orderby':
				$orderby_options = apply_filters( 'wp-freeio-jobs-orderby', array(
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
			case 'filter-featured':
				$title = esc_html__('Featured', 'wp-freeio');
				$rm_url = self::remove_url_var(  $key . '=' . $value, $url);
				self::render_filter_result_item( $title, $rm_url );
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

WP_Freeio_Employer_Filter::init();