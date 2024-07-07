<?php
/**
 * Service
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Service {
	
	public static function init() {
		// submit service
		add_action( 'wpfi_ajax_wp_freeio_ajax_submit_service_addon',  array(__CLASS__, 'process_submit_service_addon') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_service_addon',  array(__CLASS__, 'process_remove_service_addon') );

		add_action( 'wpfi_ajax_wp_freeio_ajax_send_service_order_message',  array(__CLASS__, 'process_send_service_order_message') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_change_service_order_status',  array(__CLASS__, 'process_change_service_order_status') );

		// Ajax endpoints.
		add_action( 'wpfi_ajax_wp_freeio_ajax_change_service_addon',  array(__CLASS__, 'process_change_service_addon') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_hire_service',  array(__CLASS__, 'process_hire_service') );

		// loop
		add_action( 'wp_freeio_before_service_archive', array( __CLASS__, 'display_services_results_filters' ), 5 );
		add_action( 'wp_freeio_before_service_archive', array( __CLASS__, 'display_services_count_results' ), 10 );

		add_action( 'wp_freeio_before_service_archive', array( __CLASS__, 'display_services_alert_orderby_start' ), 15 );
		add_action( 'wp_freeio_before_service_archive', array( __CLASS__, 'display_service_feed' ), 22 );
		add_action( 'wp_freeio_before_service_archive', array( __CLASS__, 'display_services_orderby' ), 25 );
		add_action( 'wp_freeio_before_service_archive', array( __CLASS__, 'display_services_alert_orderby_end' ), 100 );


		// restrict
		add_filter( 'wp-freeio-service-query-args', array( __CLASS__, 'service_restrict_listing_query_args'), 100, 2 );
		add_filter( 'wp-freeio-service-filter-query', array( __CLASS__, 'service_restrict_listing_query'), 100, 2 );
	}

	public static function get_author_id($post_id) {
		$freelancer_id = self::get_post_meta($post_id, 'freelancer_posted_by', true);

		if ( !empty($freelancer_id) ) {
			$user_id = WP_Freeio_User::get_user_by_freelancer_id($freelancer_id);
		} else {
			$user_id = get_post_field( 'post_author', $post_id );
		}
		return $user_id;
	}

	public static function get_post_meta($post_id, $key, $single = true) {
		return get_post_meta($post_id, WP_FREEIO_SERVICE_PREFIX.$key, $single);
	}
	
	// add product viewed
	public static function track_service_view() {
	    if ( ! is_singular( 'service' ) ) {
	        return;
	    }

	    global $post;

	    // views count
	    $viewed_count = intval(get_post_meta($post->ID, '_viewed_count', true));
	    $viewed_count++;
	    update_post_meta($post->ID, '_viewed_count', $viewed_count);

	    // view days
	    $today = date('Y-m-d', time());
	    $views_by_date = get_post_meta($post->ID, '_views_by_date', true);

	    if( $views_by_date != '' || is_array($views_by_date) ) {
	        if (!isset($views_by_date[$today])) {
	            if ( count($views_by_date) > 60 ) {
	                array_shift($views_by_date);
	            }
	            $views_by_date[$today] = 1;
	        } else {
	            $views_by_date[$today] = intval($views_by_date[$today]) + 1;
	        }
	    } else {
	        $views_by_date = array();
	        $views_by_date[$today] = 1;
	    }
	    update_post_meta($post->ID, '_views_by_date', $views_by_date);
	    update_post_meta($post->ID, '_recently_viewed', $today);

	}

	public static function send_admin_expiring_notice() {
		global $wpdb;

		if ( !wp_freeio_get_option('admin_notice_expiring_service') ) {
			return;
		}
		$days_notice = wp_freeio_get_option('admin_notice_expiring_service_days');

		$service_ids = self::get_expiring_services($days_notice);

		if ( $service_ids ) {
			foreach ( $service_ids as $service_id ) {
				// send email here.
				$service = get_post($service_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$email_to = get_option( 'admin_email', false );
				$subject = WP_Freeio_Email::render_email_vars(array('service' => $service), 'admin_notice_expiring_service', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('service' => $service), 'admin_notice_expiring_service', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}
		}
	}

	public static function send_freelancer_expiring_notice() {
		global $wpdb;

		if ( !wp_freeio_get_option('freelancer_notice_expiring_service') ) {
			return;
		}
		$days_notice = wp_freeio_get_option('freelancer_notice_expiring_service_days');

		$service_ids = self::get_expiring_services($days_notice);

		if ( $service_ids ) {
			foreach ( $service_ids as $service_id ) {
				// send email here.
				$service = get_post($service_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$author_id = WP_Freeio_Service::get_author_id($service->ID);

				if ( WP_Freeio_User::is_freelancer($author_id) ) {
					$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);
					$email_to = WP_Freeio_Employer::get_post_meta($freelancer_id, 'email');
				}
				if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $author_id );
				}
				
				$subject = WP_Freeio_Email::render_email_vars(array('service' => $service), 'freelancer_notice_expiring_service', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('service' => $service), 'freelancer_notice_expiring_service', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				
			}
		}
	}

	public static function get_expiring_services($days_notice) {
		global $wpdb;
		$prefix = WP_FREEIO_SERVICE_PREFIX;

		$notice_before_ts = current_time( 'timestamp' ) + ( DAY_IN_SECONDS * $days_notice );
		$service_ids          = $wpdb->get_col( $wpdb->prepare(
			"
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = %s
			AND postmeta.meta_value = %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'service'
			",
			$prefix.'expiry_date',
			date( 'Y-m-d', $notice_before_ts )
		) );

		return $service_ids;
	}

	public static function check_for_expired_services() {
		global $wpdb;

		$prefix = WP_FREEIO_SERVICE_PREFIX;
		
		// Change status to expired.
		$service_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
				LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
				WHERE postmeta.meta_key = %s
				AND postmeta.meta_value > 0
				AND postmeta.meta_value < %s
				AND posts.post_status = 'publish'
				AND posts.post_type = 'service'",
				$prefix.'expiry_date',
				date( 'Y-m-d', current_time( 'timestamp' ) )
			)
		);

		if ( $service_ids ) {
			foreach ( $service_ids as $service_id ) {
				$service_data                = array();
				$service_data['ID']          = $service_id;
				$service_data['post_status'] = 'expired';
				wp_update_post( $service_data );
			}
		}

		// Delete old expired services.
		if ( apply_filters( 'wp_freeio_delete_expired_services', false ) ) {
			$service_ids = $wpdb->get_col(
				$wpdb->prepare( "
					SELECT posts.ID FROM {$wpdb->posts} as posts
					WHERE posts.post_type = 'service'
					AND posts.post_modified < %s
					AND posts.post_status = 'expired'",
					date( 'Y-m-d', strtotime( '-' . apply_filters( 'wp_freeio_delete_expired_services_days', 30 ) . ' days', current_time( 'timestamp' ) ) )
				)
			);

			if ( $service_ids ) {
				foreach ( $service_ids as $service_id ) {
					wp_trash_post( $service_id );
				}
			}
		}
	}

	/**
	 * Deletes old previewed services after 30 days to keep the DB clean.
	 */
	public static function delete_old_previews() {
		global $wpdb;

		// Delete old expired services.
		$service_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT posts.ID FROM {$wpdb->posts} as posts
				WHERE posts.post_type = 'service'
				AND posts.post_modified < %s
				AND posts.post_status = 'preview'",
				date( 'Y-m-d', strtotime( '-' . apply_filters( 'wp_freeio_delete_old_previews_services_days', 30 ) . ' days', current_time( 'timestamp' ) ) )
			)
		);

		if ( $service_ids ) {
			foreach ( $service_ids as $service_id ) {
				wp_delete_post( $service_id, true );
			}
		}
	}

	public static function service_statuses() {
		return apply_filters(
			'wp_freeio_service_statuses',
			array(
				'draft'           => _x( 'Draft', 'post status', 'wp-freeio' ),
				'expired'         => _x( 'Expired', 'post status', 'wp-freeio' ),
				'preview'         => _x( 'Preview', 'post status', 'wp-freeio' ),
				'pending'         => _x( 'Pending approval', 'post status', 'wp-freeio' ),
				'pending_approve' => _x( 'Pending approval', 'post status', 'wp-freeio' ),
				'pending_payment' => _x( 'Pending payment', 'post status', 'wp-freeio' ),
				'publish'         => _x( 'Active', 'post status', 'wp-freeio' ),
			)
		);
	}

	public static function is_service_status_changing( $from_status, $to_status ) {
		return isset( $_POST['post_status'] ) && isset( $_POST['original_post_status'] ) && $_POST['original_post_status'] !== $_POST['post_status'] && ( null === $from_status || $from_status === $_POST['original_post_status'] ) && $to_status === $_POST['post_status'];
	}

	public static function calculate_service_expiry( $service_id ) {
		$duration = absint( wp_freeio_get_option( 'submission_service_duration' ) );
		$duration = apply_filters( 'wp-freeio-calculate-service-expiry', $duration, $service_id);

		if ( $duration ) {
			return date( 'Y-m-d', strtotime( "+{$duration} days", current_time( 'timestamp' ) ) );
		}

		return '';
	}

	public static function get_price_html( $post_id = null, $html = true ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		$meta_obj = WP_Freeio_Service_Meta::get_instance($post_id);
		if ( $meta_obj->check_post_meta_exist('price_type') && $meta_obj->get_post_meta('price_type') === 'package' ) {
			
			if ( !$meta_obj->check_post_meta_exist('price_packages') ) {
				return false;
			}
			$price_packages = $meta_obj->get_post_meta( 'price_packages' );
			if ( $price_packages && is_array($price_packages) ) {
				$price = $price_packages[0]['price'];
				foreach ($price_packages as $package) {
					$t_price = $package['price'];
					if ( $t_price == '0' ) {
						$price = 0;
					} elseif ( empty( $t_price ) || ! is_numeric( $t_price ) ) {
						break;
					} else {
						$price = $package['price'] < $price ? $package['price'] : $price;
					}
				}
			} else {
				return false;
			}
			
		} else {
			if ( !$meta_obj->check_post_meta_exist('price') ) {
				return false;
			}
			$price = $meta_obj->get_post_meta( 'price' );
		}
		
		if ( $price == '0' ) {
			$price = 0;
		} elseif ( empty( $price ) || ! is_numeric( $price ) ) {
			return false;
		}

		if ( !$html ) {
			$price = WP_Freeio_Price::format_price_without_html( $price );
		} else {
			$price = WP_Freeio_Price::format_price( $price );
		}

		return apply_filters( 'wp-freeio-get-price-html', $price, $post_id, $html );
	}

	public static function is_featured( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$featured = self::get_post_meta( $post_id, 'featured', true );
		$return = $featured ? true : false;
		return apply_filters( 'wp-freeio-service-listing-is-featured', $return, $post_id );
	}

	public static function is_urgent( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$urgent = self::get_post_meta( $post_id, 'urgent', true );
		$return = $urgent ? true : false;
		return apply_filters( 'wp-freeio-service-listing-is-urgent', $return, $post_id );
	}

	public static function display_services_results_filters() {
		$filters = WP_Freeio_Abstract_Filter::get_filters();

		echo WP_Freeio_Template_Loader::get_template_part('loop/service/results-filters', array('filters' => $filters));
	}

	public static function display_services_count_results($wp_query) {
		$total = $wp_query->found_posts;
		$per_page = $wp_query->query_vars['posts_per_page'];
		$current = max( 1, $wp_query->get( 'paged', 1 ) );
		$args = array(
			'total' => $total,
			'per_page' => $per_page,
			'current' => $current,
		);

		echo WP_Freeio_Template_Loader::get_template_part('loop/service/results-count', $args);
	}

	public static function display_services_orderby() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/service/orderby');
	}

	public static function display_services_alert_orderby_start() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/service/alert-orderby-start');
	}

	public static function display_services_alert_orderby_end() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/service/alert-orderby-end');
	}

	public static function service_feed_url($values = null, $exclude = array(), $current_key = '', $page_rss_url = '', $return = false) {
		if ( empty($page_rss_url) ) {
			$page_rss_url = home_url('/') . '?feed=service_feed';
		}
		if ( is_null( $values ) ) {
			$values = $_GET; // WPCS: input var ok, CSRF ok.
		} elseif ( is_string( $values ) ) {
			$url_parts = wp_parse_url( $values );
			$values    = array();

			if ( ! empty( $url_parts['query'] ) ) {
				parse_str( $url_parts['query'], $values );
			}
		}
		foreach ( $values as $key => $value ) {
			if ( in_array( $key, $exclude, true ) ) {
				continue;
			}
			if ( $current_key ) {
				$key = $current_key . '[' . $key . ']';
			}
			if ( is_array( $value ) ) {
				$page_rss_url = self::service_feed_url( $value, $exclude, $key, $page_rss_url, true );
			} else {
				$page_rss_url = add_query_arg($key, wp_unslash( $value ), $page_rss_url);
			}
		}

		if ( $return ) {
			return $page_rss_url;
		}

		echo $page_rss_url;
	}

	public static function display_service_feed(){
		echo WP_Freeio_Template_Loader::get_template_part('loop/service/services-rss-btn');
	}
	
	// check view
	public static function check_view_service_detail() {
		global $post;
		$restrict_type = wp_freeio_get_option('service_restrict_type', '');
		$view = wp_freeio_get_option('service_restrict_detail', 'all');
		
		$return = true;
		if ( $restrict_type == 'view' ) {
			$author_id = WP_Freeio_Service::get_author_id($post->ID);
			$user_id = WP_Freeio_User::get_user_id();
			if ( $user_id == $author_id ) {
				$return = true;
			} else {
				switch ($view) {
					case 'always_hidden':
						$return = false;
						break;
					case 'register_user':
						$return = false;
						if ( is_user_logged_in() ) {
							$return = true;
						}
						break;
					case 'register_freelancer':
						$return = false;
						if ( is_user_logged_in() ) {
							if ( WP_Freeio_User::is_employer($user_id) ) {
								$return = true;
							}
						}
						break;
					default:
						$return = true;
						break;
				}
			}
		}
		return apply_filters('wp-freeio-check-view-service-detail', $return, $post);
	}

	public static function service_restrict_listing_query($query, $filter_params) {
		$query_vars = $query->query_vars;
		$query_vars = self::service_restrict_listing_query_args($query_vars, $filter_params);
		$query->query_vars = $query_vars;
		
		return apply_filters('wp-freeio-check-view-service-listing-query', $query);
	}

	public static function service_restrict_listing_query_args($query_args, $filter_params) {

		$restrict_type = wp_freeio_get_option('service_restrict_type', '');
		
		if ( $restrict_type == 'view' ) {
			$view = wp_freeio_get_option('service_restrict_listing', 'all');
			
			$user_id = WP_Freeio_User::get_user_id();
			switch ($view) {
				case 'always_hidden':
					$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
					$meta_query[] = array(
						'key'       => 'service_restrict_listing',
						'value'     => 'always_hidden',
						'compare'   => '==',
					);
					$query_args['meta_query'] = $meta_query;
					break;
				case 'register_user':
					if ( !is_user_logged_in() ) {
						$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
						$meta_query[] = array(
							'key'       => 'service_restrict_listing',
							'value'     => 'register_user',
							'compare'   => '==',
						);
						$query_args['meta_query'] = $meta_query;
					}
					break;
				case 'register_freelancer':
					$return = false;
					if ( is_user_logged_in() ) {
						if ( WP_Freeio_User::is_employer($user_id) ) {
							$return = true;
						}
					}
					if ( !$return ) {
						$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
						$meta_query[] = array(
							'key'       => 'service_restrict_listing',
							'value'     => 'register_freelancer',
							'compare'   => '==',
						);
						$query_args['meta_query'] = $meta_query;
					}
					break;
			}
		}
		return apply_filters('wp-freeio-check-view-service-listing-query-args', $query_args);
	}

	public static function check_restrict_review($post) {
		$return = true;
		
		$view = wp_freeio_get_option('services_restrict_review', 'all');
		
		switch ($view) {
			case 'always_hidden':
				$return = false;
				break;
			case 'register_user':
				$return = false;
				if ( is_user_logged_in() ) {
					$return = true;
				}
				break;
			case 'register_employer':
				$return = false;
				if ( is_user_logged_in() ) {
					$user_id = WP_Freeio_User::get_user_id();
					if ( WP_Freeio_User::is_employer($user_id) ) {
						$return = true;
					}
				}
				break;
			case 'employer_bought_service':
				$user_id = WP_Freeio_User::get_user_id();
				if ( WP_Freeio_User::is_employer($user_id) ) {
					$return = self::check_employer_bought_this_service($post->ID, $user_id);
				} else {
					$return = false;
				}
				break;
			default:
				$return = true;
				break;
		}

		return apply_filters('wp-freeio-check-restrict-service-review', $return, $post);
	}

	public static function check_employer_bought_this_service($service_id, $employer_user_id) {
		$args = array(
			'post_type' => 'service_order',
			'post_status' => 'completed',
			'author' => $employer_user_id,
			'numberposts' => 1,
			'meta_query' => array(
				array(
		           	'key' => WP_FREEIO_SERVICE_ORDER_PREFIX . 'service_id',
		           	'value' => $service_id,
		           	'compare'   => '=',
		       	),
			)
		);
		$posts = get_posts($args);
		if ( !empty($posts) ) {
			return true;
		}
		return false;
	}

	public static function process_submit_service_addon() {
		$do_check = check_ajax_referer( 'wp-freeio-submit-service-addon-nonce', 'submit_service_addon_nonce_security', false );
		if ( $do_check == false ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again.', 'wp-freeio') );
            wp_send_json( $json );
        }

		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('You do not have permission to submit service addon. Please log in to continue.', 'wp-freeio') );
		   	wp_send_json($return);
		}

		if ( !WP_Freeio_User::is_freelancer() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('You do not have permission to submit service addon.', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$title = !empty($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
		$description = !empty($_POST['description']) ? sanitize_text_field($_POST['description']) : '';
		$price = !empty($_POST['price']) ? sanitize_text_field($_POST['price']) : '';
		$service_addon_id = !empty($_POST['service_addon_id']) ? sanitize_text_field($_POST['service_addon_id']) : '';

		if ( empty($title) ) {
			$return = array(
				'status' => true,
				'msg' => esc_html__('Title is required.', 'wp-freeio')
			);
			wp_send_json($return);
		}

		if ( empty($price) ) {
			$return = array(
				'status' => true,
				'msg' => esc_html__('Price is required.', 'wp-freeio')
			);
			wp_send_json($return);
		}

		do_action('wp-freeio-before-save-service-addon');

		$post_data = array(
			'post_title'    => wp_strip_all_tags( $title ),
			'post_author'   => get_current_user_id(),
			'post_type'     => 'service_addon',
			'post_content'  => wp_strip_all_tags($description),
			'post_status'  => 'publish',
		);
		
		if ( empty($service_addon_id) ) {
			$service_addon_id = wp_insert_post( $post_data );
		} else {
			$post_data['ID'] = $service_addon_id;
			$service_addon_id = wp_update_post( $post_data );
		}

		if( !is_wp_error( $service_addon_id ) ) {
			update_post_meta($service_addon_id, WP_FREEIO_SERVICE_ADDON_PREFIX . 'price', $price);
			$return = array( 'status' => true, 'msg' => esc_html__('Service Addon created successfully.', 'wp-freeio'), 'html' => '' );
		   	wp_send_json($return);
		}
		$return = array( 'status' => false, 'msg' => esc_html__('Submit service addon error.', 'wp-freeio') );
	   	wp_send_json($return);
	}

	public static function process_remove_service_addon() {
		$do_check = check_ajax_referer( 'wp-freeio-delete-service-addon-nonce', 'nonce', false );
		if ( $do_check == false ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again.', 'wp-freeio') );
            wp_send_json( $json );
        }

		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('You do not have permission to submit service addon. Please log in to continue.', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$user_id = get_current_user_id();

		$service_addon_id = !empty($_POST['service_addon_id']) ? sanitize_text_field($_POST['service_addon_id']) : '';
		$service_addon_user_id = get_post_field('post_author', $service_addon_id);
		if ( $user_id != $service_addon_user_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('You do not have permission to remove this service addon.', 'wp-freeio') );
		   	wp_send_json($return);
		}

		do_action('wp-freeio-before-remove-service-addon');

		if ( wp_delete_post( $service_addon_id ) ) {
			$return = array( 'status' => true, 'msg' => esc_html__('Service Addon has been successfully removed.', 'wp-freeio'), 'html' => '' );
		   	wp_send_json($return);
		}
		$return = array( 'status' => false, 'msg' => esc_html__('An error occured when removing an item.', 'wp-freeio') );
	   	wp_send_json($return);
	}

	public static function process_change_service_addon() {
		$service_id = !empty($_POST['service_id']) ? sanitize_text_field($_POST['service_id']) : '';
		$service_package = isset($_POST['service_package']) ? sanitize_text_field($_POST['service_package']) : 0;

		$meta_obj = WP_Freeio_Service_Meta::get_instance($service_id);
		


		$addon_price = 0;
		$service_addons = !empty($_POST['service_addons']) ? array_map( 'sanitize_text_field', $_POST['service_addons']) : array();
		if ( !empty($service_addons) ) {
			foreach ($service_addons as $addon_id) {
				$addon_price += get_post_meta($addon_id, WP_FREEIO_SERVICE_ADDON_PREFIX.'price', true);
			}
		}
		$service_price = $addon_price;
		if ( $meta_obj->check_post_meta_exist('price_type') && $meta_obj->get_post_meta('price_type') === 'package' ) {
		    $price_packages = $meta_obj->get_post_meta( 'price_packages' );
		    if ( $price_packages && is_array($price_packages) ) {
		    	if ( isset($price_packages[$service_package]) ) {
		    		$service_price += $price_packages[$service_package]['price'];
		    	}
		    }
		} else {
		    $service_price += $meta_obj->get_post_meta( 'price' );
		}

		$return = array(
			'status' => true,
			'price' => WP_Freeio_Price::format_price($service_price),
			'price_without_html' => WP_Freeio_Price::format_price_without_html($service_price)
		);
		wp_send_json($return);
	}

	public static function process_hire_service() {
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('You do not have permission to buy this service. Please log in to continue.', 'wp-freeio') );
		   	wp_send_json($return);
		}

		if ( !WP_Freeio_User::is_employer() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('You do not have permission to buy this service. Only employers can buy the services.', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$service_id = !empty($_POST['service_id']) ? sanitize_text_field($_POST['service_id']) : '';
		$service_package = !empty($_POST['service_package']) ? sanitize_text_field($_POST['service_package']) : 0;

		do_action('wp-freeio-before-hire-service-action', $service_id);

		$product_id	= wp_freeio_get_option('services_woocommerce_product_id');

		if( !empty( $product_id )) {
			if ( class_exists('WooCommerce') ) {
				global $woocommerce;
				$woocommerce->cart->empty_cart(); //empty cart before update cart
				$user_id			= get_current_user_id();
				$cart_meta		= array();
				$addon_data		= array();

				$service_package_content = '';
				$meta_obj = WP_Freeio_Service_Meta::get_instance($service_id);
				if ( $meta_obj->check_post_meta_exist('price_type') && $meta_obj->get_post_meta('price_type') === 'package' ) {
					if ( !$meta_obj->check_post_meta_exist('price_packages') ) {
				        return false;
				    }
				    $price_packages = $meta_obj->get_post_meta( 'price_packages' );
				    if ( $price_packages && is_array($price_packages) ) {
				        if ( isset($price_packages[$service_package]) ) {
				    		$service_price = $single_service_price = $price_packages[$service_package]['price'];
				    		$service_package_content = $price_packages[$service_package];
				    	}
				    } else {
				        return false;
				    }
				} else {
					if ( !$meta_obj->check_post_meta_exist('price') ) {
						return false;
					}
					$service_price = $single_service_price = $meta_obj->get_post_meta( 'price' );
				}

				$service_addons = !empty($_POST['service_addons']) ? array_map( 'sanitize_text_field', $_POST['service_addons']) : array();
				if ( !empty($service_addons) ) {
					foreach ($service_addons as $addon_id) {
						$addon_price = get_post_meta($addon_id, WP_FREEIO_SERVICE_ADDON_PREFIX.'price', true);
						$service_price += $addon_price;

						$addon_data[$addon_id]['id']	= $addon_id;
						$addon_data[$addon_id]['price']	= $addon_price;
					}
				}

				if ( !$meta_obj->check_post_meta_exist('delivery_time') ) {
					$delivery_time = '';
				} else {
					$delivery_time = $meta_obj->get_post_meta( 'delivery_time' );
				}
				$admin_shares = 0.0;
				$freelancer_shares = 0.0;

				if ( !empty( $service_price ) ) {
					$options = [
						'type' => wp_freeio_get_option('freelancers_service_commission_fee', 'none'),
						'fixed_amount' => wp_freeio_get_option('freelancers_service_commission_fixed_amount', 10),
						'percentage' => wp_freeio_get_option('freelancers_service_commission_percentage', 20),
						'comissions_tiers' => wp_freeio_get_option('freelancers_service_comissions_tiers'),
					];
					$service_fee = WP_Freeio_Mixes::process_commission_fee($service_price, $options);
					if( !empty( $service_fee ) ){
						$admin_shares = !empty($service_fee['admin_shares']) ? $service_fee['admin_shares'] : 0.0;
						$freelancer_shares = !empty($service_fee['freelancer_shares']) ? $service_fee['freelancer_shares'] : $service_price;
					} else{
						$admin_shares = 0.0;
						$freelancer_shares = $service_price;
					}

					$admin_shares = number_format($admin_shares, 2, '.', '');
					$freelancer_shares = number_format($freelancer_shares, 2, '.', '');
				}
				
				$options = [
					'type' => wp_freeio_get_option('employers_service_commission_fee', 'none'),
					'fixed_amount' => wp_freeio_get_option('employers_service_commission_fixed_amount', 10),
					'percentage' => wp_freeio_get_option('employers_service_commission_percentage', 20),
					'comissions_tiers' => wp_freeio_get_option('employers_service_comissions_tiers'),
				];
				$employer_service_fee = WP_Freeio_Mixes::employer_hiring_payment_setting($service_price, $options);
				
				$cart_meta['service_id']		= $service_id;
				$cart_meta['delivery_time']		= $delivery_time;
				$cart_meta['price']				= $service_price;
				$cart_meta['processing_fee']	= !empty( $employer_service_fee['commission_amount'] ) ? $employer_service_fee['commission_amount'] : 0.0;
				$cart_meta['service_price']		= $single_service_price;
				$cart_meta['addons']			= $addon_data;
				$cart_meta['service_package_content'] = $service_package_content;

				$freelancer_id = self::get_author_id($service_id);

				$cart_data = array(
					'product_id' 		=> $product_id,
					'cart_data'     	=> $cart_meta,
					'price'				=> WP_Freeio_Price::format_price($service_price, false),
					'payment_type'     	=> 'hiring_service',
					'admin_shares'     	=> $admin_shares,
					'freelancer_shares' => $freelancer_shares,
					'employer_id' 		=> $user_id,
					'freelancer_id' 	=> $freelancer_id,
					'current_project' 	=> $service_id,
				);
				
				$woocommerce->cart->empty_cart();
				WC()->cart->add_to_cart($product_id, 1, null, null, $cart_data);
				
				$return = array( 'status' => true, 'msg' => esc_html__('Please wait you are redirecting to the checkout page.', 'wp-freeio'), 'checkout_url' => wc_get_checkout_url() );
				wp_send_json($return);
				
			} else {
				$return = array( 'status' => false, 'msg' => esc_html__('Please install WooCommerce plugin to process this order.', 'wp-freeio') );
				wp_send_json($return);
			}
		} else{
			$return = array( 'status' => false, 'msg' => esc_html__('Hiring settings is missing, please contact to administrator.', 'wp-freeio') );
			wp_send_json($return);
		}
	}

	public static function process_send_service_order_message() {
		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to send this message', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$service_order_id = empty( $_POST['service_order_id'] ) ? false : intval( $_POST['service_order_id'] );
		if ( !$service_order_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Service Order not found', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$service_id = empty( $_POST['service_id'] ) ? false : intval( $_POST['service_id'] );
		if ( !$service_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Service not found', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$message = empty( $_POST['message'] ) ? false : wp_kses_post($_POST['message']);
		if ( !$message ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Message is required', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$user_id = WP_Freeio_User::get_user_id();
		$service_user_id = get_post_field('post_author', $service_id);
		$employer_user_id = get_post_field('post_author', $service_order_id);
		$service_order_service_id = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);

		if ( WP_Freeio_User::is_employer($user_id) ) {
			if ( $user_id != $employer_user_id || $service_id != $service_order_service_id ) {
				$return = array( 'status' => false, 'msg' => esc_html__('You have not permission to send the message', 'wp-freeio') );
			   	wp_send_json($return);
			}
			$notify_post_type = 'freelancer';
			$notify_user_post_id = WP_Freeio_User::get_freelancer_by_user_id($service_user_id);
			$user_post_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);
		} else {
			if ( $user_id != $service_user_id || $service_id != $service_order_service_id ) {
				$return = array( 'status' => false, 'msg' => esc_html__('You have not permission to send the message', 'wp-freeio') );
			   	wp_send_json($return);
			}
			$notify_post_type = 'employer';
			$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($service_user_id);
			$notify_user_post_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);
		}

		do_action('wp-freeio-before-send-service-message');

		// cv file
        $attachment_ids = array();
        if ( !empty($_FILES['attachments']['name']) ) {
		    $files = $_FILES['attachments'];
	    	if ( is_array($files['name']) ) {
			    foreach ($files['name'] as $key => $value) {            
		            if ($files['name'][$key]) { 
		                $file = array( 
		                    'name' => $files['name'][$key],
		                    'type' => $files['type'][$key], 
		                    'tmp_name' => $files['tmp_name'][$key], 
		                    'error' => $files['error'][$key],
		                    'size' => $files['size'][$key]
		                ); 
		                $_FILES = array( 'attachments' => $file ); 
		                foreach ($_FILES as $file => $array) {              
		                    $attach_id = WP_Freeio_CMB2_Field_File::handle_attachment($file, $service_order_id);
		                    if ( is_numeric($attach_id) ) {
		                    	$url = wp_get_attachment_url( $attach_id );
		                    	$attachment_ids[$attach_id] = $url;
		                    }
		                }
		            } 
		        }
	        } else {
	        	$attach_id = WP_Freeio_CMB2_Field_File::handle_attachment('attachments', $service_order_id);
                if ( is_numeric($attach_id) ) {
                	$url = wp_get_attachment_url( $attach_id );
                	$attachment_ids[$attach_id] = $url;
                }
	        }

	        if ( !empty($attachment_ids) ) {
				
				$messages_attachments = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX . 'messages_attachments', true);
				$messages_attachments = !empty($messages_attachments) ? $messages_attachments : array();

				$new_messages_attachments = array_merge( $attachment_ids, $messages_attachments );
				update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX . 'messages_attachments', $new_messages_attachments);
			}
		}

		$unique_id = uniqid();
		$message_args = array(
			'unique_id' => $unique_id,
			'message' => $message,
			'user_id' => $user_id,
            'service_order_id' => $service_order_id,
            'service_id' => $service_id,
            'time' => current_time('timestamp'),
            'attachment_ids' => $attachment_ids,
		);

		$messages = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX . 'messages', true);
        $messages = !empty($messages) ? $messages : array();

        $new_messages = array_merge( array($unique_id => $message_args), $messages );
		update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX . 'messages', $new_messages);

		if ( wp_freeio_get_option('user_notice_hired_service_message') ) {
			if ( WP_Freeio_User::is_employer($user_id) ) {
				$user_post_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);

				$my_services_page_id = wp_freeio_get_option('my_bought_services_page_id');
				$my_services_url = get_permalink( $my_services_page_id );

				$my_services_url = add_query_arg( 'service_id', $service_id, remove_query_arg( 'service_id', $my_services_url ) );
				$my_services_url = add_query_arg( 'service_order_id', $service_order_id, remove_query_arg( 'service_order_id', $my_services_url ) );
				$message_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_services_url ) );

				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($service_user_id);
				$email_to = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
				if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $service_user_id );
				}
			} else {
				$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($service_user_id);

				$my_services_page_id = wp_freeio_get_option('my_services_page_id');
				$my_services_url = get_permalink( $my_services_page_id );

				$my_services_url = add_query_arg( 'service_id', $service_id, remove_query_arg( 'service_id', $my_services_url ) );
				$my_services_url = add_query_arg( 'service_order_id', $service_order_id, remove_query_arg( 'service_order_id', $my_services_url ) );
				$message_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_services_url ) );

				$employer_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);
				$email_to = get_post_meta( $employer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
				if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $employer_user_id );
				}
			}
			$username = get_the_title($user_post_id);
			$service_title = get_the_title($service_id);

			$email_vars = array(
				'username' => $username,
				'service_title' => $service_title,
				'message' => $message,
				'message_url' => $message_url,
			);
     		
			$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
			
			$subject = WP_Freeio_Email::render_email_vars($email_vars, 'hired_service_message_notice', 'subject');
			$content = WP_Freeio_Email::render_email_vars($email_vars, 'hired_service_message_notice', 'content');
			
			WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
		}


		$notify_args = array(
			'post_type' => $notify_post_type,
			'user_post_id' => $notify_user_post_id,
            'type' => 'service_message',
            'service_id' => $service_id,
            'employer_user_id' => $employer_user_id,
            'freelancer_user_id' => $service_user_id,
            'service_order_id' => $service_order_id,
		);
		WP_Freeio_User_Notification::add_notification($notify_args);


		$return = array( 'status' => true, 'msg' => esc_html__('Message sent Successfully.', 'wp-freeio'), 'html' => self::list_service_order_messages($service_order_id) );
	   	wp_send_json($return);
	}

	public static function list_service_order_messages($service_order_id) {
		return apply_filters('wp-freeio-get-list-service-order-messages', '', $service_order_id);
	}

	public static function process_change_service_order_status() {
		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to change status', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$service_order_id = empty( $_POST['service_order_id'] ) ? false : intval( $_POST['service_order_id'] );
		if ( !$service_order_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Proposal not found', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$status = empty( $_POST['status'] ) ? '' : $_POST['status'];
		if ( !$status || !in_array($status, array('hired', 'completed', 'cancelled')) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Status is not correct', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$user_id = WP_Freeio_User::get_user_id();
		$service_order_author_id = get_post_field('post_author', $service_order_id);
		if ( $user_id != $service_order_author_id) {
			$return = array( 'status' => false, 'msg' => esc_html__('You have not permission to update this service order', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$old_status = get_post_status($service_order_id);
		if ( $status == $old_status ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your status not change', 'wp-freeio') );
		   	wp_send_json($return);
		}

		do_action('wp-freeio-before-change-service-order-status');

		// update service status
		$service_order_data = array(
			'ID' => $service_order_id,
			'post_status' => $status,
		);
		$service_order = wp_update_post( $service_order_data );
		if ( $service_order ) {
			$earning_id = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'earning_id', true);

	        if ( !empty($earning_id) ) {
				if ( $status == 'completed' ) {
					$earning_data = array(
						'ID' => $earning_id,
						'post_status' => 'publish',
					);
					$earning = wp_update_post( $earning_data );

					// email
					$service_id = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
					$freelancer_user_id = WP_Freeio_Service::get_author_id($service_id);
		     		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
		     		$employer_id = WP_Freeio_User::get_employer_by_user_id($service_order_author_id);
		     		$employer = get_post($employer_id);
		     		$freelancer = get_post($freelancer_id);
		     		$service = get_post($service_id);
					$email_from = get_option( 'admin_email', false );

					$amount = get_post_meta($earning_id, WP_FREEIO_EARNING_PREFIX.'amount', true);
					$currency_symbol = get_post_meta($earning_id, WP_FREEIO_EARNING_PREFIX.'currency_symbol', true);
					$email_vars = array(
						'freelancer' => $freelancer,
						'employer' => $employer,
						'service' => $service,
						'amount' => WP_Freeio_Price::format_price($amount, false, $currency_symbol)
					);

					if ( wp_freeio_get_option('freelancer_notice_add_completed_service') ) {
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

						$email_to = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
						if ( empty($email_to) ) {
							$email_to = get_the_author_meta( 'user_email', $freelancer_user_id );
						}

						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'completed_service_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'completed_service_notice', 'content');
						
						WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
					}

					if ( wp_freeio_get_option('employer_notice_add_completed_service') ) {
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

						$email_to = get_post_meta( $freelancer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
						if ( empty($email_to) ) {
							$email_to = get_the_author_meta( 'user_email', $service_order_author_id );
						}

						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'completed_service_employer_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'completed_service_employer_notice', 'content');
						
						WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
					}
				} elseif ( $status == 'hired' ) {
					$earning_data = array(
						'ID' => $earning_id,
						'post_status' => 'pending',
					);
					$earning = wp_update_post( $earning_data );
				} elseif ( $status == 'cancelled' ) {
					$earning_data = array(
						'ID' => $earning_id,
						'post_status' => 'cancelled',
					);
					$earning = wp_update_post( $earning_data );

					// email
					$service_id = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
					$freelancer_user_id = WP_Freeio_Service::get_author_id($service_id);
		     		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
		     		$employer_id = WP_Freeio_User::get_employer_by_user_id($service_order_author_id);
		     		$employer = get_post($employer_id);
		     		$freelancer = get_post($freelancer_id);
					$email_from = get_option( 'admin_email', false );

					$amount = get_post_meta($earning_id, WP_FREEIO_EARNING_PREFIX.'amount', true);
					$currency_symbol = get_post_meta($earning_id, WP_FREEIO_EARNING_PREFIX.'currency_symbol', true);
					$email_vars = array(
						'freelancer' => $freelancer,
						'employer' => $employer,
						'amount' => WP_Freeio_Price::format_price($amount, false, $currency_symbol)
					);
					
					if ( wp_freeio_get_option('freelancer_notice_add_cancelled_service') ) {
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

						$email_to = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
						if ( empty($email_to) ) {
							$email_to = get_the_author_meta( 'user_email', $freelancer_user_id );
						}

						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'cancelled_service_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'cancelled_service_notice', 'content');
						
						WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
					}

					if ( wp_freeio_get_option('employer_notice_add_cancelled_service') ) {
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

						$email_to = get_post_meta( $freelancer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
						if ( empty($email_to) ) {
							$email_to = get_the_author_meta( 'user_email', $service_order_author_id );
						}

						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'cancelled_service_employer_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'cancelled_service_employer_notice', 'content');
						
						WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
					}
				}
			}

			// notification
			$freelancer_user_id = WP_Freeio_Service::get_author_id($service_id);
			$service_id = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
			$notify_args = array(
				'post_type' => 'freelancer',
				'user_post_id' => $freelancer_id,
	            'type' => 'change_service_status',
	            'service_id' => $service_id,
	            'employer_user_id' => $service_order_author_id,
	            'service_order_id' => $service_order_id,
			);
			WP_Freeio_User_Notification::add_notification($notify_args);

			$return = array( 'status' => true, 'msg' => esc_html__('Update service order status has been successfully', 'wp-freeio') );
		   	wp_send_json($return);
	   	} else {
	   		$return = array( 'status' => false, 'msg' => esc_html__('An error occured when hire an service order.', 'wp-freeio') );
		   	wp_send_json($return);
	   	}
	}
}
WP_Freeio_Service::init();