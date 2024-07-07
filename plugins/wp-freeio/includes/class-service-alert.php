<?php
/**
 * Service Alert
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Service_Alert {
	public static function init() {
		add_action( 'wp_freeio_email_daily_notices', array( __CLASS__, 'send_service_alert_notice' ) );
		// Ajax endpoints.
		add_action( 'wpfi_ajax_wp_freeio_ajax_add_service_alert',  array(__CLASS__,'process_add_service_alert') );

		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_service_alert',  array(__CLASS__,'process_remove_service_alert') );


		// compatible handlers.
		add_action( 'wp_ajax_wp_freeio_ajax_add_service_alert',  array(__CLASS__,'process_add_service_alert') );
		add_action( 'wp_ajax_nopriv_wp_freeio_ajax_add_service_alert',  array(__CLASS__,'process_add_service_alert') );

		add_action( 'wp_ajax_wp_freeio_ajax_remove_service_alert',  array(__CLASS__,'process_remove_service_alert') );
		add_action( 'wp_ajax_nopriv_wp_freeio_ajax_remove_service_alert',  array(__CLASS__,'process_remove_service_alert') );
	}

	public static function get_email_frequency() {
		$email_frequency = apply_filters( 'wp-freeio-service-alert-email-frequency', array(
			'daily' => array(
				'label' => __('Daily', 'wp-freeio'),
				'days' => '1',
			),
			'weekly' => array(
				'label' => __('Weekly', 'wp-freeio'),
				'days' => '7',
			),
			'fortnightly' => array(
				'label' => __('Fortnightly', 'wp-freeio'),
				'days' => '15',
			),
			'monthly' => array(
				'label' => __('Monthly', 'wp-freeio'),
				'days' => '30',
			),
			'biannually' => array(
				'label' => __('Biannually', 'wp-freeio'),
				'days' => '182',
			),
			'annually' => array(
				'label' => __('Annually', 'wp-freeio'),
				'days' => '365',
			),
		));
		return $email_frequency;
	}

	public static function send_service_alert_notice() {
		
		$email_frequency_default = self::get_email_frequency();
		if ( $email_frequency_default ) {
			foreach ($email_frequency_default as $key => $value) {
				if ( !empty($value['days']) ) {
					$meta_query = array(
						'relation' => 'OR',
						array(
							'key' => WP_FREEIO_SERVICE_ALERT_PREFIX.'send_email_time',
							'compare' => 'NOT EXISTS',
						)
					);
					$current_time = apply_filters( 'wp-freeio-service-alert-current-'.$key.'-time', date( 'Y-m-d', strtotime( '-'.intval($value['days']).' days', current_time( 'timestamp' ) ) ) );
					$meta_query[] = array(
						'relation' => 'AND',
						array(
							'key' => WP_FREEIO_SERVICE_ALERT_PREFIX.'send_email_time',
							'value' => $current_time,
							'compare' => '<=',
						),
						array(
							'key' => WP_FREEIO_SERVICE_ALERT_PREFIX.'email_frequency',
							'value' => $key,
							'compare' => '=',
						),
					);

					$query_args = apply_filters( 'wp-freeio-service-alert-query-args', array(
						'post_type' => 'service_alert',
						'post_per_page' => -1,
						'post_status' => 'publish',
						'fields' => 'ids',
						'meta_query' => $meta_query
					));

					$service_alerts = new WP_Query($query_args);
					if ( !empty($service_alerts->posts) ) {
						foreach ($service_alerts->posts as $post_id) {
							$alert_query = get_post_meta($post_id, WP_FREEIO_SERVICE_ALERT_PREFIX . 'alert_query', true);
							
							$params = $alert_query;
							if ( !empty($alert_query) && !is_array($alert_query) ) {
								$params = json_decode($alert_query, true);
							}

							$query_args = array(
								'post_type' => 'service',
							    'post_status' => 'publish',
							    'post_per_page' => 1,
							    'fields' => 'ids'
							);
							$services = WP_Freeio_Query::get_posts($query_args, $params);
							$count_services = $services->found_posts;
							$service_alert_title = get_the_title($post_id);
							// send email action
							$email_from = get_option( 'admin_email', false );
							
							$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
							
							$author_id = get_post_field('post_author', $post_id);
							$email_to = get_the_author_meta('user_email', $author_id);
							$subject = WP_Freeio_Email::render_email_vars(array('alert_title' => $service_alert_title), 'service_alert_notice', 'subject');

							$email_frequency = get_post_meta($post_id, WP_FREEIO_SERVICE_ALERT_PREFIX.'email_frequency', true);
							if ( !empty($email_frequency_default[$email_frequency]['label']) ) {
								$email_frequency = $email_frequency_default[$email_frequency]['label'];
							}
							$services_alert_url = WP_Freeio_Mixes::get_services_page_url();
							if ( !empty($params) ) {
								foreach ($params as $key => $value) {
									if ( is_array($value) ) {
										$services_alert_url = remove_query_arg( $key.'[]', $services_alert_url );
										foreach ($value as $val) {
											$services_alert_url = add_query_arg( $key.'[]', $val, $services_alert_url );
										}
									} else {
										$services_alert_url = add_query_arg( $key, $value, remove_query_arg( $key, $services_alert_url ) );
									}
								}
							}
							$content_args = apply_filters( 'wp-freeio-service-alert-email-content-args', array(
								'alert_title' => $service_alert_title,
								'services_found' => $count_services,
								'email_frequency_type' => $email_frequency,
								'services_alert_url' => $services_alert_url
							));
							$content = WP_Freeio_Email::render_email_vars($content_args, 'service_alert_notice', 'content');
										
							WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
							$current_time = date( 'Y-m-d', current_time( 'timestamp' ) );
							delete_post_meta($post_id, WP_FREEIO_SERVICE_ALERT_PREFIX.'send_email_time');
							add_post_meta($post_id, WP_FREEIO_SERVICE_ALERT_PREFIX.'send_email_time', $current_time);
						}
					}
				}
			}
		}
		
	}

	public static function process_add_service_alert() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-add-service-alert-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() || !WP_Freeio_User::is_freelancer() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login as "Freelancer" to add service alert.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$user_id = WP_Freeio_User::get_user_id();
		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);

		$errors = self::validate_add_service_alert();
		if ( !empty($errors) && sizeof($errors) > 0 ) {
			$return = array( 'status' => false, 'msg' => implode(', ', $errors) );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$name = !empty($_POST['name']) ? $_POST['name'] : '';
		
		$post_args = array(
            'post_title' => $name,
            'post_type' => 'service_alert',
            'post_content' => '',
            'post_status' => 'publish',
            'user_id' => $user_id
        );
		$post_args = apply_filters('wp-freeio-add-service-alert-data', $post_args);
		
		do_action('wp-freeio-before-add-service-alert');

        // Insert the post into the database
        $alert_id = wp_insert_post($post_args);
        if ( $alert_id ) {
	        update_post_meta($alert_id, WP_FREEIO_SERVICE_ALERT_PREFIX . 'freelancer_id', $freelancer_id);
	        $email_frequency = !empty($_POST['email_frequency']) ? $_POST['email_frequency'] : '';
	        update_post_meta($alert_id, WP_FREEIO_SERVICE_ALERT_PREFIX . 'email_frequency', $email_frequency);

	        $alert_query = array();
			if ( ! empty( $_POST ) && is_array( $_POST ) ) {
				foreach ( $_POST as $key => $value ) {
					if ( strrpos( $key, 'filter-', -strlen( $key ) ) !== false ) {
						$alert_query[$key] = $value;
					}
				}
			}
	        if ( !empty($alert_query) ) {
	        	// $alert_query = json_encode($alert_query);
	        	update_post_meta($alert_id, WP_FREEIO_SERVICE_ALERT_PREFIX . 'alert_query', $alert_query);	
	        }
	        
	        do_action('wp-freeio-after-add-service-alert', $alert_id);

	        $return = array( 'status' => true, 'msg' => esc_html__('Service alert added successfully', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Error adding service alert', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function validate_add_service_alert() {
		$name = !empty($_POST['name']) ? $_POST['name'] : '';
		if ( empty($name) ) {
			$return[] = esc_html__('Name is required.', 'wp-freeio');
		}
		$email_frequency = !empty($_POST['email_frequency']) ? $_POST['email_frequency'] : '';
		if ( empty($email_frequency) ) {
			$return[] = esc_html__('Email frequency is required.', 'wp-freeio');
		}
		return $return;
	}

	public static function process_remove_service_alert() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-remove-service-alert-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to remove service alert.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$alert_id = !empty($_POST['alert_id']) ? $_POST['alert_id'] : '';

		if ( empty($alert_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Applicant doesn\'t exist', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$user_id = WP_Freeio_User::get_user_id();
		$is_allowed = WP_Freeio_Mixes::is_allowed_to_remove( $user_id, $alert_id );

		if ( ! $is_allowed ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('You can not remove this service alert.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-before-remove-service-alert', $alert_id);

		if ( wp_delete_post( $alert_id ) ) {
	        $return = array( 'status' => true, 'msg' => esc_html__('Remove service alert successfully.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Remove service alert error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}
}

WP_Freeio_Service_Alert::init();