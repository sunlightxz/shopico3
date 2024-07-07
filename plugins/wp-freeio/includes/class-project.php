<?php
/**
 * Project
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Project {
	
	public static function init() {

		add_action( 'wpfi_ajax_wp_freeio_ajax_project_proposal',  array(__CLASS__, 'process_project_proposal') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_hire_proposal',  array(__CLASS__, 'process_hire_proposal') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_send_proposal_message',  array(__CLASS__, 'process_send_proposal_message') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_change_proposal_status',  array(__CLASS__, 'process_change_proposal_status') );


		// loop
		add_action( 'wp_freeio_before_project_archive', array( __CLASS__, 'display_projects_results_filters' ), 5 );
		add_action( 'wp_freeio_before_project_archive', array( __CLASS__, 'display_projects_count_results' ), 10 );

		add_action( 'wp_freeio_before_project_archive', array( __CLASS__, 'display_projects_alert_orderby_start' ), 15 );
		add_action( 'wp_freeio_before_project_archive', array( __CLASS__, 'display_project_feed' ), 22 );
		add_action( 'wp_freeio_before_project_archive', array( __CLASS__, 'display_projects_orderby' ), 25 );
		add_action( 'wp_freeio_before_project_archive', array( __CLASS__, 'display_projects_alert_orderby_end' ), 100 );


		// restrict
		add_filter( 'wp-freeio-project-query-args', array( __CLASS__, 'project_restrict_listing_query_args'), 100, 2 );
		add_filter( 'wp-freeio-project-filter-query', array( __CLASS__, 'project_restrict_listing_query'), 100, 2 );
	}

	public static function get_author_id($post_id) {
		$employer_id = self::get_post_meta($post_id, 'employer_posted_by', true);

		if ( !empty($employer_id) ) {
			$user_id = WP_Freeio_User::get_user_by_employer_id($employer_id);
		} else {
			$user_id = get_post_field( 'post_author', $post_id );
		}
		return $user_id;
	}

	public static function get_post_meta($post_id, $key, $single = true) {
		return get_post_meta($post_id, WP_FREEIO_PROJECT_PREFIX.$key, $single);
	}
	
	// add product viewed
	public static function track_project_view() {
	    if ( ! is_singular( 'project' ) ) {
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

	    // 

	    if ( empty( $_COOKIE['wp_freeio_recently_viewed'] ) ) {
	        $viewed_products = array();
	    } else {
	        $viewed_products = (array) explode( '|', $_COOKIE['wp_freeio_recently_viewed'] );
	    }

	    if ( ! in_array( $post->ID, $viewed_products ) ) {
	        $viewed_products[] = $post->ID;
	    }

	    if ( sizeof( $viewed_products ) > 15 ) {
	        array_shift( $viewed_products );
	    }

	    // Store for session only
	    setcookie( 'wp_freeio_recently_viewed', implode( '|', $viewed_products ) );


	}

	public static function send_admin_expiring_notice() {
		global $wpdb;

		if ( !wp_freeio_get_option('admin_notice_expiring_project') ) {
			return;
		}
		$days_notice = wp_freeio_get_option('admin_notice_expiring_project_days');

		$project_ids = self::get_expiring_projects($days_notice);

		if ( $project_ids ) {
			foreach ( $project_ids as $project_id ) {
				// send email here.
				$project = get_post($project_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$email_to = get_option( 'admin_email', false );
				$subject = WP_Freeio_Email::render_email_vars(array('project' => $project), 'admin_notice_expiring_project', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('project' => $project), 'admin_notice_expiring_project', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}
		}
	}

	public static function send_employer_expiring_notice() {
		global $wpdb;

		if ( !wp_freeio_get_option('employer_notice_expiring_project') ) {
			return;
		}
		$days_notice = wp_freeio_get_option('employer_notice_expiring_project_days');

		$project_ids = self::get_expiring_projects($days_notice);

		if ( $project_ids ) {
			foreach ( $project_ids as $project_id ) {
				// send email here.
				$project = get_post($project_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$author_id = WP_Freeio_Project::get_author_id($project->ID);

				if ( WP_Freeio_User::is_employer($author_id) ) {
					$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
					$email_to = WP_Freeio_Employer::get_post_meta($employer_id, 'email');
				}
				if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $author_id );
				}
				
				$subject = WP_Freeio_Email::render_email_vars(array('project' => $project), 'employer_notice_expiring_project', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('project' => $project), 'employer_notice_expiring_project', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				
			}
		}
	}

	public static function get_expiring_projects($days_notice) {
		global $wpdb;
		$prefix = WP_FREEIO_PROJECT_PREFIX;

		$notice_before_ts = current_time( 'timestamp' ) + ( DAY_IN_SECONDS * $days_notice );
		$project_ids          = $wpdb->get_col( $wpdb->prepare(
			"
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = %s
			AND postmeta.meta_value = %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'project'
			",
			$prefix.'expiry_date',
			date( 'Y-m-d', $notice_before_ts )
		) );

		return $project_ids;
	}

	public static function check_for_expired_projects() {
		global $wpdb;

		$prefix = WP_FREEIO_PROJECT_PREFIX;
		
		// Change status to expired.
		$project_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
				LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
				WHERE postmeta.meta_key = %s
				AND postmeta.meta_value > 0
				AND postmeta.meta_value < %s
				AND posts.post_status = 'publish'
				AND posts.post_type = 'project'",
				$prefix.'expiry_date',
				date( 'Y-m-d', current_time( 'timestamp' ) )
			)
		);

		if ( $project_ids ) {
			foreach ( $project_ids as $project_id ) {
				$project_data                = array();
				$project_data['ID']          = $project_id;
				$project_data['post_status'] = 'expired';
				wp_update_post( $project_data );
			}
		}

		// Delete old expired projects.
		if ( apply_filters( 'wp_freeio_delete_expired_projects', false ) ) {
			$project_ids = $wpdb->get_col(
				$wpdb->prepare( "
					SELECT posts.ID FROM {$wpdb->posts} as posts
					WHERE posts.post_type = 'project'
					AND posts.post_modified < %s
					AND posts.post_status = 'expired'",
					date( 'Y-m-d', strtotime( '-' . apply_filters( 'wp_freeio_delete_expired_projects_days', 30 ) . ' days', current_time( 'timestamp' ) ) )
				)
			);

			if ( $project_ids ) {
				foreach ( $project_ids as $project_id ) {
					wp_trash_post( $project_id );
				}
			}
		}
	}

	/**
	 * Deletes old previewed projects after 30 days to keep the DB clean.
	 */
	public static function delete_old_previews() {
		global $wpdb;

		// Delete old expired projects.
		$project_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT posts.ID FROM {$wpdb->posts} as posts
				WHERE posts.post_type = 'project'
				AND posts.post_modified < %s
				AND posts.post_status = 'preview'",
				date( 'Y-m-d', strtotime( '-' . apply_filters( 'wp_freeio_delete_old_previews_projects_days', 30 ) . ' days', current_time( 'timestamp' ) ) )
			)
		);

		if ( $project_ids ) {
			foreach ( $project_ids as $project_id ) {
				wp_delete_post( $project_id, true );
			}
		}
	}

	public static function project_statuses() {
		return apply_filters(
			'wp_freeio_project_statuses',
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

	public static function is_project_status_changing( $from_status, $to_status ) {
		return isset( $_POST['post_status'] ) && isset( $_POST['original_post_status'] ) && $_POST['original_post_status'] !== $_POST['post_status'] && ( null === $from_status || $from_status === $_POST['original_post_status'] ) && $to_status === $_POST['post_status'];
	}

	public static function calculate_project_expiry( $project_id ) {
		$duration = absint( wp_freeio_get_option( 'submission_project_duration' ) );
		$duration = apply_filters( 'wp-freeio-calculate-project-expiry', $duration, $project_id);

		if ( $duration ) {
			return date( 'Y-m-d', strtotime( "+{$duration} days", current_time( 'timestamp' ) ) );
		}

		return '';
	}

	public static function get_price_html( $post_id = null, $html = true ) {
		$min_price = self::get_min_price_html($post_id, $html);
		$max_price = self::get_max_price_html($post_id, $html);
		$price_html = '';
		if ( $min_price ) {
			$price_html = $min_price;
		}
		if ( $max_price ) {
			$price_html .= (!empty($price_html) ? ' - ' : '').$max_price;
		}
		if ( $price_html ) {
			$project_type = self::get_post_meta( $post_id, 'project_type', true );

			$price_type_html = '';
			switch ($project_type) {
				case 'fixed':
					if ( $html ) {
						$price_type_html = '<span class="surfix"> '.esc_html__('Fixed', 'wp-freeio').'</span>';
					} else {
						$price_type_html = ' '.esc_html__('Fixed', 'wp-freeio');
					}
					break;
				case 'hourly':
					if ( $html ) {
						$price_type_html = '<span class="surfix"> '.esc_html__('Hourly rate', 'wp-freeio').'</span>';
					} else {
						$price_type_html = ' '.esc_html__('Hourly rate', 'wp-freeio');
					}
					break;
				default:
					$types = WP_Freeio_Mixes::get_default_project_types();
					if ( !empty($types[$project_type]) ) {
						if ( $html ) {
							$price_type_html = '<span class="surfix">'.$types[$project_type].'</span>';
						} else {
							$price_type_html = ' '.$types[$project_type];
						}
					}
					break;
			}
			$price_type_html = apply_filters( 'wp-freeio-service-get-price-type-html', $price_type_html, $project_type, $post_id );
			$price_html = $price_html.$price_type_html;
		}
		return apply_filters( 'wp-freeio-get-price-html', $price_html, $post_id );
	}

	public static function get_min_price_html( $post_id = null, $html = true ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		$meta_obj = WP_Freeio_Project_Meta::get_instance($post_id);
		if ( !$meta_obj->check_post_meta_exist('price') ) {
			return false;
		}
		$price = $meta_obj->get_post_meta( 'price' );

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

		return apply_filters( 'wp-freeio-service-get-min-price-html', $price, $post_id, $html );
	}

	public static function get_max_price_html( $post_id = null, $html = true ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		$meta_obj = WP_Freeio_Project_Meta::get_instance($post_id);
		if ( !$meta_obj->check_post_meta_exist('max_price') ) {
			return false;
		}
		$price = $meta_obj->get_post_meta( 'max_price' );

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

		return apply_filters( 'wp-freeio-service-get-max-price-html', $price, $post_id, $html );
	}
	
	public static function is_featured( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$featured = self::get_post_meta( $post_id, 'featured', true );
		$return = $featured ? true : false;
		return apply_filters( 'wp-freeio-project-listing-is-featured', $return, $post_id );
	}

	public static function is_urgent( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$urgent = self::get_post_meta( $post_id, 'urgent', true );
		$return = $urgent ? true : false;
		return apply_filters( 'wp-freeio-project-listing-is-urgent', $return, $post_id );
	}
	
	public static function display_projects_results_filters() {
		$filters = WP_Freeio_Abstract_Filter::get_filters();

		echo WP_Freeio_Template_Loader::get_template_part('loop/project/results-filters', array('filters' => $filters));
	}

	public static function display_projects_count_results($wp_query) {
		$total = $wp_query->found_posts;
		$per_page = $wp_query->query_vars['posts_per_page'];
		$current = max( 1, $wp_query->get( 'paged', 1 ) );
		$args = array(
			'total' => $total,
			'per_page' => $per_page,
			'current' => $current,
		);

		echo WP_Freeio_Template_Loader::get_template_part('loop/project/results-count', $args);
	}

	public static function display_projects_orderby() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/project/orderby');
	}

	public static function display_projects_alert_orderby_start() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/project/alert-orderby-start');
	}

	public static function display_projects_alert_orderby_end() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/project/alert-orderby-end');
	}

	public static function project_feed_url($values = null, $exclude = array(), $current_key = '', $page_rss_url = '', $return = false) {
		if ( empty($page_rss_url) ) {
			$page_rss_url = home_url('/') . '?feed=project_feed';
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
				$page_rss_url = self::project_feed_url( $value, $exclude, $key, $page_rss_url, true );
			} else {
				$page_rss_url = add_query_arg($key, wp_unslash( $value ), $page_rss_url);
			}
		}

		if ( $return ) {
			return $page_rss_url;
		}

		echo $page_rss_url;
	}

	public static function display_project_feed(){
		echo WP_Freeio_Template_Loader::get_template_part('loop/project/projects-rss-btn');
	}


	// check view
	public static function check_view_project_detail() {
		global $post;
		$restrict_type = wp_freeio_get_option('project_restrict_type', '');
		$view = wp_freeio_get_option('project_restrict_detail', 'all');
		
		$return = true;
		if ( $restrict_type == 'view' ) {
			$author_id = WP_Freeio_Project::get_author_id($post->ID);
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
							if ( WP_Freeio_User::is_freelancer($user_id) ) {
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
		return apply_filters('wp-freeio-check-view-project-detail', $return, $post);
	}

	public static function project_restrict_listing_query($query, $filter_params) {
		$query_vars = $query->query_vars;
		$query_vars = self::project_restrict_listing_query_args($query_vars, $filter_params);
		$query->query_vars = $query_vars;
		
		return apply_filters('wp-freeio-check-view-project-listing-query', $query);
	}

	public static function project_restrict_listing_query_args($query_args, $filter_params) {

		$restrict_type = wp_freeio_get_option('project_restrict_type', '');
		
		if ( $restrict_type == 'view' ) {
			$view = wp_freeio_get_option('project_restrict_listing', 'all');
			
			$user_id = WP_Freeio_User::get_user_id();
			switch ($view) {
				case 'always_hidden':
					$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
					$meta_query[] = array(
						'key'       => 'project_restrict_listing',
						'value'     => 'always_hidden',
						'compare'   => '==',
					);
					$query_args['meta_query'] = $meta_query;
					break;
				case 'register_user':
					if ( !is_user_logged_in() ) {
						$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
						$meta_query[] = array(
							'key'       => 'project_restrict_listing',
							'value'     => 'register_user',
							'compare'   => '==',
						);
						$query_args['meta_query'] = $meta_query;
					}
					break;
				case 'register_freelancer':
					$return = false;
					if ( is_user_logged_in() ) {
						if ( WP_Freeio_User::is_freelancer($user_id) ) {
							$return = true;
						}
					}
					if ( !$return ) {
						$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
						$meta_query[] = array(
							'key'       => 'project_restrict_listing',
							'value'     => 'register_freelancer',
							'compare'   => '==',
						);
						$query_args['meta_query'] = $meta_query;
					}
					break;
			}
		}
		return apply_filters('wp-freeio-check-view-project-listing-query-args', $query_args);
	}


	public static function process_project_proposal() {
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('You do not have permission to apply on a project. Please log in to continue.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		if ( !WP_Freeio_User::is_freelancer() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('You are not allowed to send  the proposals.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$project_id = !empty($_POST['project_id']) ? sanitize_text_field($_POST['project_id']) : '';

		if ( empty($project_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Some thing went wrong, try again', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$proposal_id = !empty($_POST['proposal_id']) ? sanitize_text_field($_POST['proposal_id']) : '';

		do_action('wp_freeio_before_project_proposal_action', $project_id);

		if ( get_post_status( $project_id ) === 'hired' ){
            $return = array( 'status' => false, 'msg' => esc_html__('This project has been assigned to one of the freelancer. You can\'t send proposals.', 'wp-freeio') );
            wp_send_json( $return );
		} elseif( get_post_status( $project_id ) === 'completed' ){
            $return = array( 'status' => false, 'msg' => esc_html__('This project has been completed, so you can\'t send proposals', 'wp-freeio') );
            wp_send_json( $return );
		} elseif( get_post_status( $project_id ) === 'cancelled' ){
            $return = array( 'status' => false, 'msg' => esc_html__('This project has been cancelled, when employer will re-open this project then you would be able to send proposal.', 'wp-freeio') );
            wp_send_json( $return );
		} elseif( get_post_status( $project_id ) === 'pending' ){
            $return = array( 'status' => false, 'msg' => esc_html__('This project is under review. You can\'t send proposals.', 'wp-freeio') );
            wp_send_json( $return );
		}

		$current_user_id = get_current_user_id();
		
		if ( empty($proposal_id) ) {
			$proposals_sent = intval(0);
	        $args = array(
	            'post_type' => 'project_proposal',
	            'author'    =>  $current_user_id,
	            'meta_query' => array(
	                array(
	                    'key'     => WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id',
	                    'value'   => intval( $project_id ),
	                    'compare' => '=',
	                ),
	            ),
	        );

	        $query = new WP_Query( $args );
	        if( !empty( $query ) ){
	           $proposals_sent =  $query->found_posts;
	        }

	        if( $proposals_sent > 0 ) {
	            $return = array( 'status' => false, 'msg' => esc_html__('You have already sent the proposal', 'wp-freeio') );
	            wp_send_json( $return );
	        }

	        //Check if project is open
	        $project_status = get_post_status( $project_id );
	        if ( $project_status === 'closed' ) {
	            $return = array( 'status' => false, 'msg' => esc_html__('You can not send proposal for a closed project', 'wp-freeio') );
	            wp_send_json( $return );
	        }
        }

        $proposed_amount = $default_proposed_amount = !empty($_POST['proposed_amount']) ? floatval($_POST['proposed_amount']) : '';
        $estimeted_time = !empty($_POST['estimeted_time']) ? sanitize_text_field($_POST['estimeted_time']) : '';
        $description = !empty($_POST['description']) ? sanitize_text_field($_POST['description']) : '';
        if ( empty( $proposed_amount ) ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Amount field is required', 'wp-freeio') );
            wp_send_json( $return );
        }
        if ( empty( $description ) ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Cover letter field is required', 'wp-freeio') );
            wp_send_json( $return );
        }
        if ( empty( $estimeted_time) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Estimeted time are required', 'wp-freeio') );
			wp_send_json( $return );
		}

		$project_type     = get_post_meta($project_id, WP_FREEIO_PROJECT_PREFIX.'project_type', true);
		if( !empty( $project_type ) && $project_type == 'hourly' ){
			$per_hour_amount	= $proposed_amount;
			$proposed_amount	= $proposed_amount * $estimeted_time;
		}
		
		//Calculate Service and Freelance Share
        $options = [
			'type' => wp_freeio_get_option('freelancers_project_commission_fee', 'none'),
			'fixed_amount' => wp_freeio_get_option('freelancers_project_commission_fixed_amount', 10),
			'percentage' => wp_freeio_get_option('freelancers_project_commission_percentage', 20),
			'comissions_tiers' => wp_freeio_get_option('freelancers_project_comissions_tiers'),
		];
		$project_fee = WP_Freeio_Mixes::process_commission_fee($proposed_amount, $options);

		if ( !empty( $project_fee ) ) {
			$admin_amount       = !empty($project_fee['admin_shares']) ? $project_fee['admin_shares'] : 0.0;
        	$freelancer_amount  = !empty($project_fee['freelancer_shares']) ? $project_fee['freelancer_shares'] : $proposed_amount;
		} else {
			$admin_amount       = 0;
        	$freelancer_amount  = $proposed_amount;
		}
		
		$admin_amount 		= number_format($admin_amount, 2, '.', '');
		$freelancer_amount 	= number_format($freelancer_amount, 2, '.', '');

		if ( empty($proposal_id) ) {
			$edit_proposal = false;
			//Create Proposal
			$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($current_user_id);
	        $username   = esc_html( get_the_title( $freelancer_id ) );
	        $title      = esc_html( get_the_title( $project_id ));
	        $title      = $title .' ' . '(' . $username . ')';
			
			$proposal_post = array(
				'post_title'    => wp_strip_all_tags( $title ), //proposal title
				'post_status'   => 'publish',
				'post_content'  => $description,
				'post_author'   => $current_user_id,
				'post_type'     => 'project_proposal',
			);

			$proposal_id = wp_insert_post( $proposal_post );
		} else {
			$edit_proposal = true;
			$proposal_post = array(
				'post_status'   => 'publish',
				'post_content'  => $description,
				'post_author'   => $current_user_id,
				'ID'   => $proposal_id,
			);

			wp_update_post( $proposal_post );
		}

		$user_employer_id = WP_Freeio_Project::get_author_id($project_id);
		if ( !is_wp_error( $proposal_id ) ) {
			update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'estimeted_time', $estimeted_time );
			
			if( !empty( $project_type ) && $project_type === 'hourly' ){
				update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'per_hour_amount', $per_hour_amount );
			}
			
            //Update post meta
            update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_type', $project_type );
            update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', $project_id );
            update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'employer_id', $user_employer_id );
            update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'default_proposed_amount', $default_proposed_amount);
            update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'amount', $proposed_amount);
            update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'status', 'pending');
            update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'admin_amount', $admin_amount);
            update_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'freelancer_amount', $freelancer_amount);
        } else {
        	$return = array( 'status' => false, 'msg' => esc_html__('An error occured when save a proposal.', 'wp-freeio'), 'html' => '' );
        }

        // Send email to employer
     	if ( !$edit_proposal ) {
     		$employer_id = WP_Freeio_User::get_employer_by_user_id($user_employer_id);
     		if ( wp_freeio_get_option('employer_notice_add_new_proposal') ) {
	     		$employer = get_post($employer_id);
	     		$email_to = WP_Freeio_Employer::get_post_meta($employer_id, 'email');
	     		if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $user_employer_id );
				}
				$project = get_post($project_id);

				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($current_user_id);
	        	$freelancer_name   = esc_html( get_the_title( $freelancer_id ) );
	        	$freelancer_email = WP_Freeio_Employer::get_post_meta($freelancer_id, 'email');
	     		if ( empty($freelancer_email) ) {
					$freelancer_email = get_the_author_meta( 'user_email', $current_user_id );
				}
				$freelancer_phone = WP_Freeio_Employer::get_post_meta($freelancer_id, 'phone');

				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				
				$email_vars = array(
					'employer' => $employer, 'project' => $project, 'freelancer_name' => $freelancer_name,
					'amount' => WP_Freeio_Price::format_price($proposed_amount),
					'estimeted_time' => $estimeted_time.' '. esc_html__('Hours', 'wp-freeio'),
					'message' => $description,
					'phone' => $freelancer_phone,
					'email' => $freelancer_email,
				);

				$subject = WP_Freeio_Email::render_email_vars($email_vars, 'send_proposal_notice', 'subject');
				$content = WP_Freeio_Email::render_email_vars($email_vars, 'send_proposal_notice', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}


			$notify_args = array(
				'post_type' => 'employer',
				'user_post_id' => $employer_id,
	            'type' => 'new_proposal',
	            'proposal_id' => $proposal_id,
	            'freelancer_user_id' => $current_user_id,
	            'project_id' => $project_id,
			);

			WP_Freeio_User_Notification::add_notification($notify_args);
        } else {
        	$employer_id = WP_Freeio_User::get_employer_by_user_id($user_employer_id);
        	$notify_args = array(
				'post_type' => 'employer',
				'user_post_id' => $employer_id,
	            'type' => 'edit_proposal',
	            'proposal_id' => $proposal_id,
	            'freelancer_user_id' => $current_user_id,
	            'project_id' => $project_id,
			);

			WP_Freeio_User_Notification::add_notification($notify_args);
        }
        
        do_action('wp-freeio-before-process-save-proposal', $proposal_id, $project_id);

        if ( $edit_proposal ) {
	        $return = array( 'status' => true, 'edit' => true, 'msg' => esc_html__('Your proposal has sent Successfully', 'wp-freeio'), 'html' => '' );
	    } else {
	    	$return = array( 'status' => true, 'edit' => false, 'msg' => esc_html__('Your proposal has sent Successfully', 'wp-freeio'), 'html' => self::list_proposals($project_id) );
	    }
	    $return = apply_filters('wp-freeio-save-proposal-return', $return);
        wp_send_json( $return );
	}

	public static function list_proposals($project_id) {
		$html = '';
		return apply_filters('wp-freeio-project-list-proposals', $html, $project_id);
	}

	public static function process_hire_proposal() {

		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-hire-proposal-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	wp_send_json($return);
		}

		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to hire this proposal', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$proposal_id = empty( $_POST['proposal_id'] ) ? false : intval( $_POST['proposal_id'] );
		if ( !$proposal_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Proposal not found', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$user_id = WP_Freeio_User::get_user_id();
		$project_id = get_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true );
		$project_employer_id = get_post_field('post_author', $project_id);

		if ( $user_id != $project_employer_id ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('You can not hire this proposal.', 'wp-freeio') );
		   	wp_send_json($return);
		}

		do_action('wp-freeio-before-process-hire-proposal');


		$product_id	= wp_freeio_get_option('projects_woocommerce_product_id');
		if( !empty( $product_id )) {
			if ( class_exists('WooCommerce') ) {
				global $woocommerce;
				$woocommerce->cart->empty_cart(); //empty cart before update cart
				$user_id			= $current_user->ID;
				$project_price				= get_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'amount', true);
				$admin_shares 		= 0.0;
				$freelancer_shares 	= 0.0;

				if( !empty( $project_price ) ){
					$options = [
						'type' => wp_freeio_get_option('freelancers_project_commission_fee', 'none'),
						'fixed_amount' => wp_freeio_get_option('freelancers_project_commission_fixed_amount', 10),
						'percentage' => wp_freeio_get_option('freelancers_project_commission_percentage', 20),
						'comissions_tiers' => wp_freeio_get_option('freelancers_project_comissions_tiers'),
					];
					$project_fee = WP_Freeio_Mixes::process_commission_fee($project_price, $options);

					if( !empty( $project_fee ) ){
						$admin_shares       = !empty($project_fee['admin_shares']) ? $project_fee['admin_shares'] : 0.0;
						$freelancer_shares  = !empty($project_fee['freelancer_shares']) ? $project_fee['freelancer_shares'] : $project_price;
					} else{
						$admin_shares       = 0.0;
						$freelancer_shares  = $project_price;
					}

					$admin_shares 		= number_format($admin_shares,2,'.', '');
					$freelancer_shares 	= number_format($freelancer_shares,2,'.', '');
				}
				

				$options = [
					'type' => wp_freeio_get_option('employers_project_commission_fee', 'none'),
					'fixed_amount' => wp_freeio_get_option('employers_project_commission_fixed_amount', 10),
					'percentage' => wp_freeio_get_option('employers_project_commission_percentage', 20),
					'comissions_tiers' => wp_freeio_get_option('employers_project_comissions_tiers'),
				];
				$employer_project_fee = WP_Freeio_Mixes::employer_hiring_payment_setting($project_price, $options);


				$cart_meta['project_id']		= $project_id;
				$cart_meta['price']				= $project_price;
				$cart_meta['proposal_id']		= $proposal_id;
				$cart_meta['processing_fee']	= !empty( $employer_project_fee['commission_amount'] ) ? $employer_project_fee['commission_amount'] : 0.0;

				$hired_freelance_id			= get_post_field('post_author', $proposal_id);
				$hired_freelance_id			= !empty( $hired_freelance_id ) ? intval( $hired_freelance_id ) : '';

				$cart_data = array(
					'product_id' 		=> $product_id,
					'cart_data'     	=> $cart_meta,
					'price'				=> WP_Freeio_Price::format_price($project_price,'return'),
					'payment_type'     	=> 'hiring',
					'admin_shares'     	=> $admin_shares,
					'freelancer_shares' => $freelancer_shares,
					'employer_id' 		=> $current_user->ID,
					'freelancer_id' 	=> $hired_freelance_id,
					'current_project' 	=> $project_id,
				);

				$woocommerce->cart->empty_cart();
				$cart_item_data = $cart_data;
				WC()->cart->add_to_cart($product_id, 1, null, null, $cart_item_data);
				
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

	public static function process_send_proposal_message() {
		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to send this this', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$proposal_id = empty( $_POST['proposal_id'] ) ? false : intval( $_POST['proposal_id'] );
		if ( !$proposal_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Proposal not found', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$project_id = empty( $_POST['project_id'] ) ? false : intval( $_POST['project_id'] );
		if ( !$project_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Project not found', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$message = empty( $_POST['message'] ) ? false : sanitize_textarea_field($_POST['message']);
		if ( !$message ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Message is required', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$user_id = WP_Freeio_User::get_user_id();
		$project_user_id = get_post_field('post_author', $project_id);
		$freelancer_user_id = get_post_field('post_author', $proposal_id);
		$proposal_project_id = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);

		if ( WP_Freeio_User::is_employer($user_id) ) {
			if ( $user_id != $project_user_id || $project_id != $proposal_project_id ) {
				$return = array( 'status' => false, 'msg' => esc_html__('You have not permission to send the message', 'wp-freeio') );
			   	echo wp_json_encode($return);
			   	exit;
			}
		} else {
			if ( $user_id != $freelancer_user_id || $project_id != $proposal_project_id ) {
				$return = array( 'status' => false, 'msg' => esc_html__('You have not permission to send the message', 'wp-freeio') );
			   	echo wp_json_encode($return);
			   	exit;
			}
		}

		do_action('wp-freeio-before-send-proposal-message');

		// cv file
        $attachment_ids = array();
        if ( !empty($_FILES['attachments']['name']) ) {
		    $files = $_FILES['attachments'];
	    	if ( is_array($files['name']) ) {
	    		$return = array();
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
		                    $attach_id = WP_Freeio_CMB2_Field_File::handle_attachment($file, $proposal_id);
		                    if ( is_numeric($attach_id) ) {
		                    	$url = wp_get_attachment_url( $attach_id );
		                    	$attachment_ids[$attach_id] = $url;
		                    }
		                }
		            } 
		        }
	        } else {
	        	$attach_id = WP_Freeio_CMB2_Field_File::handle_attachment('attachments', $proposal_id);
                if ( is_numeric($attach_id) ) {
                	$url = wp_get_attachment_url( $attach_id );
                	$attachment_ids[$attach_id] = $url;
                }
	        }

	        if ( !empty($attachment_ids) ) {
				
				$messages_attachments = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'messages_attachments', true);
				$messages_attachments = !empty($messages_attachments) ? $messages_attachments : array();

				$new_messages_attachments = array_merge( $attachment_ids, $messages_attachments );
				update_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'messages_attachments', $new_messages_attachments);
			}
		}

		$unique_id = uniqid();
		$message_args = array(
			'unique_id' => $unique_id,
			'message' => $message,
			'user_id' => $user_id,
            'proposal_id' => $proposal_id,
            'project_id' => $project_id,
            'time' => current_time('timestamp'),
            'attachment_ids' => $attachment_ids,
		);

		$messages = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'messages', true);
        $messages = !empty($messages) ? $messages : array();

        $new_messages = array_merge( array($unique_id => $message_args), $messages );
		update_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'messages', $new_messages);


		if ( wp_freeio_get_option('user_notice_hired_project_message') ) {
			if ( WP_Freeio_User::is_employer($user_id) ) {
				$user_post_id = WP_Freeio_User::get_employer_by_user_id($project_user_id);

				$my_projects_page_id = wp_freeio_get_option('my_projects_page_id');
				$my_projects_url = get_permalink( $my_projects_page_id );

				$my_projects_url = add_query_arg( 'project_id', $project_id, remove_query_arg( 'project_id', $my_projects_url ) );
				$my_projects_url = add_query_arg( 'proposal_id', $proposal_id, remove_query_arg( 'proposal_id', $my_projects_url ) );
				$message_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_projects_url ) );

				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($service_user_id);
				$email_to = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
				if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $service_user_id );
				}
			} else {
				$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);

				$my_proposals_page_id = wp_freeio_get_option('my_proposals_page_id');
				$my_proposals_url = get_permalink( $my_proposals_page_id );

				$my_proposals_url = add_query_arg( 'project_id', $project_id, remove_query_arg( 'project_id', $my_proposals_url ) );
				$my_proposals_url = add_query_arg( 'proposal_id', $proposal_id, remove_query_arg( 'proposal_id', $my_proposals_url ) );
				$message_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_proposals_url ) );

				$employer_id = WP_Freeio_User::get_employer_by_user_id($project_user_id);
				$email_to = get_post_meta( $employer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
				if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $project_user_id );
				}
			}
			$username = get_the_title($user_post_id);
			$project_title = get_the_title($project_id);

			$email_vars = array(
				'username' => $username,
				'project_title' => $project_title,
				'message' => $message,
				'message_url' => $message_url,
			);
			
			$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
			
			$subject = WP_Freeio_Email::render_email_vars($email_vars, 'hired_project_message_notice', 'subject');
			$content = WP_Freeio_Email::render_email_vars($email_vars, 'hired_project_message_notice', 'content');
			
			WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
		}

		// notification
		if ( WP_Freeio_User::is_employer($user_id) ) {
			$post_type = 'employer';
			$user_post_id = WP_Freeio_User::get_employer_by_user_id($freelancer_user_id);
		} else {
			$post_type = 'freelancer';
			$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($project_user_id);
		}
		$notify_args = array(
			'post_type' => $post_type,
			'user_post_id' => $user_post_id,
            'type' => 'proposal_message',
            'proposal_id' => $proposal_id,
            'user_id' => $user_id,
            'project_id' => $project_id,
		);
		WP_Freeio_User_Notification::add_notification($notify_args);


		$return = array( 'status' => true, 'msg' => esc_html__('Message sent Successfully.', 'wp-freeio'), 'html' => self::list_proposal_messages($proposal_id) );
	   	wp_send_json( $return );
	}

	public static function list_proposal_messages($proposal_id) {
		return apply_filters('wp-freeio-get-list-proposal-messages', '', $proposal_id);
	}

	public static function process_change_proposal_status() {
		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to change proposal status', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$proposal_id = empty( $_POST['proposal_id'] ) ? false : intval( $_POST['proposal_id'] );
		if ( !$proposal_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Proposal not found', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$status = empty( $_POST['status'] ) ? '' : $_POST['status'];
		if ( !$status || !in_array($status, array('hired', 'completed', 'cancelled')) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Status is not correct', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$user_id = WP_Freeio_User::get_user_id();
		$project_id = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
		$project_author_id = WP_Freeio_Project::get_author_id($project_id);
		if ( $user_id != $project_author_id) {
			$return = array( 'status' => false, 'msg' => esc_html__('You have not permission to update this proposal', 'wp-freeio') );
		   	wp_send_json($return);
		}

		do_action('wp-freeio-before-change-proposal-status');

		$old_status = get_post_status($proposal_id);
		if ( $status == $old_status ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your status not change', 'wp-freeio') );
		   	wp_send_json($return);
		}
		// update proposal status
		$proposal_data = array(
			'ID' => $proposal_id,
			'post_status' => $status,
		);
		$proposal = wp_update_post( $proposal_data );

		// update project status 
		$project_data = array(
			'ID' => $project_id,
			'post_status' => $status,
		);
		$project = wp_update_post( $project_data );

		if ( $proposal && $project ) {

			$earning_id = get_post_meta($proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'earning_id', true);

	        if ( !empty($earning_id) ) {
				if ( $status == 'completed' ) {
					$earning_data = array(
						'ID' => $earning_id,
						'post_status' => 'publish',
					);
					$earning = wp_update_post( $earning_data );

					$freelancer_user_id =  get_post_field ('post_author', $proposal_id);
		     		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
		     		$employer_id = WP_Freeio_User::get_employer_by_user_id($project_author_id);
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

					if ( wp_freeio_get_option('freelancer_notice_add_completed_project') ) {
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

						$email_to = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
						if ( empty($email_to) ) {
							$email_to = get_the_author_meta( 'user_email', $freelancer_user_id );
						}

						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'completed_project_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'completed_project_notice', 'content');
						
						WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
					}

					if ( wp_freeio_get_option('employer_notice_add_completed_project') ) {
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

						$email_to = get_post_meta( $employer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
						if ( empty($email_to) ) {
							$email_to = get_the_author_meta( 'user_email', $project_author_id );
						}

						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'completed_project_employer_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'completed_project_employer_notice', 'content');
						
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


					$freelancer_user_id =  get_post_field ('post_author', $proposal_id);
		     		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
		     		$employer_id = WP_Freeio_User::get_employer_by_user_id($project_author_id);
		     		$employer = get_post($employer_id);
		     		$freelancer = get_post($freelancer_id);
		     		$project = get_post($project_id);
					$email_from = get_option( 'admin_email', false );

					$amount = get_post_meta($earning_id, WP_FREEIO_EARNING_PREFIX.'amount', true);
					$currency_symbol = get_post_meta($earning_id, WP_FREEIO_EARNING_PREFIX.'currency_symbol', true);
					$email_vars = array(
						'freelancer' => $freelancer,
						'employer' => $employer,
						'project' => $project,
						'amount' => WP_Freeio_Price::format_price($amount, false, $currency_symbol)
					);

					if ( wp_freeio_get_option('freelancer_notice_add_cancelled_project') ) {
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

						$email_to = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
						if ( empty($email_to) ) {
							$email_to = get_the_author_meta( 'user_email', $freelancer_user_id );
						}

						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'cancelled_project_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'cancelled_project_notice', 'content');
						
						WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
					}

					if ( wp_freeio_get_option('employer_notice_add_cancelled_project') ) {
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

						$email_to = get_post_meta( $employer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
						if ( empty($email_to) ) {
							$email_to = get_the_author_meta( 'user_email', $project_author_id );
						}

						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'cancelled_project_employer_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'cancelled_project_employer_notice', 'content');
						
						WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
					}
				}
			}

			// notification
			$freelancer_user_id = get_post_field('post_author', $proposal_id);
			$notify_args = array(
				'post_type' => 'freelancer',
				'user_post_id' => $freelancer_user_id,
	            'type' => 'change_proposal_status',
	            'proposal_id' => $proposal_id,
	            'user_id' => $project_author_id,
	            'project_id' => $project_id,
			);
			WP_Freeio_User_Notification::add_notification($notify_args);
			

			$return = array( 'status' => true, 'msg' => esc_html__('Update proposal status has been successfully', 'wp-freeio') );
		   	wp_send_json($return);
	   	} else {
	   		$return = array( 'status' => false, 'msg' => esc_html__('An error occured when hire a proposal.', 'wp-freeio') );
		   	wp_send_json($return);
	   	}
	}
}
WP_Freeio_Project::init();