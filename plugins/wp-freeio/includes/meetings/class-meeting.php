<?php
/**
 * Meeting
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Meeting {
	
	public static function init() {

		// Ajax endpoints.		
		add_action( 'wpfi_ajax_wp_freeio_ajax_create_meeting',  array(__CLASS__, 'process_create_meeting') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_reschedule_meeting',  array(__CLASS__, 'process_reschedule_meeting') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_meeting',  array(__CLASS__, 'process_remove_meeting') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_cancel_meeting',  array(__CLASS__, 'process_cancel_meeting') );
	}
	
	public static function get_post_meta($post_id, $key, $single = true) {
		return get_post_meta($post_id, WP_FREEIO_MEETING_PREFIX.$key, $single);
	}

	public static function update_post_meta($post_id, $key, $data) {
		return update_post_meta($post_id, WP_FREEIO_MEETING_PREFIX.$key, $data);
	}

	public static function process_create_meeting() {
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-create-meeting-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		if ( !is_user_logged_in() || !WP_Freeio_User::is_employer() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login as "Employer" to create meeting.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : '';
		$post_obj = get_post($post_id);

		if ( !$post_obj || empty($post_obj->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Application doesn\'t exist', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$date = sanitize_text_field(!empty($_POST['date']) ? $_POST['date'] : '');
		$time = sanitize_text_field(!empty($_POST['time']) ? $_POST['time'] : '');
		$time_duration = sanitize_text_field(!empty($_POST['time_duration']) ? $_POST['time_duration'] : '');
		$message = sanitize_text_field(!empty($_POST['message']) ? $_POST['message'] : '');
		if ( empty($date) || empty($time) || empty($time_duration) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Fill all fields', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$user_id = WP_Freeio_User::get_user_id();
		
		if ( $post_obj->post_type == 'job_applicant') {
			$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
			$freelancer_id = WP_Freeio_Applicant::get_post_meta($post_id, 'freelancer_id');
			$job_id = WP_Freeio_Applicant::get_post_meta($post_id, 'job_id');
			$topic_title = esc_html__('job', 'wp-freeio');
		} elseif( $post_obj->post_type == 'project_proposal' ) {
			$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
			$freelancer_id = $post_obj->post_author;
			$job_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
			$topic_title = esc_html__('project', 'wp-freeio');
		} elseif( $post_obj->post_type == 'service_order' ) {
			$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
			$job_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
			$freelancer_id = get_post_field('post_author', $job_id);
			$topic_title = esc_html__('service', 'wp-freeio');
		}

		$post_args = array(
            'post_title' => sanitize_text_field( get_the_title($freelancer_id)),
            'post_type' => 'job_meeting',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => $user_id,
        );
		$post_args = apply_filters('wp-freeio-create-meeting-data', $post_args);
		do_action('wp-freeio-before-create-meeting');


		$meeting_platform = 'onboard';
        
        $meet_exct_stime = strtotime($date . ' ' . $time);
        
        $zoom_email = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_email', true);
        $zoom_client_id = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_client_id', true);
        $zoom_client_secret = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_client_secret', true);

        if ( !empty($zoom_email) && !empty($zoom_client_id) && !empty($zoom_client_secret) && !empty($_POST['zoom_meeting']) ) {
            
            $access_token = WP_Freeio_Meeting_Zoom::user_zoom_access_token($user_id);
            $data = array(
                'schedule_for' => $zoom_email,
                'topic' => sprintf(esc_html__('Interview meeting for %s - %s', 'wp-freeio'), $topic_title, get_the_title($job_id)),
                'start_time' => date('Y-m-d', $meet_exct_stime) . 'T' . date('H:i:s', $meet_exct_stime),
                'timezone' => wp_timezone_string(),
                'duration' => $time_duration,
                'agenda' => $message,
            );
            $data_str = json_encode($data);

            $url = 'https://api.zoom.us/v2/users/' . $zoom_email . '/meetings';
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_POST, 1);
            // make sure we are POSTing
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
            // allow us to use the returned data from the request
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            //we are sending json
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $access_token,
            ));

            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($result, true);
            if (isset($result['id'])) {
                $zoom_meeting_id = $result['id'];
                $meeting_platform = 'zoom';
                
                $zoom_meeting_url = isset($result['join_url']) ? $result['join_url'] : '';
            }
        }


        // Insert the post into the database
        $meeting_id = wp_insert_post($post_args);

        if ( $meeting_id ) {
	        self::update_post_meta($meeting_id, 'freelancer_id', $freelancer_id);
	        self::update_post_meta($meeting_id, 'post_id', $post_id);
	        self::update_post_meta($meeting_id, 'date', $date);
	        self::update_post_meta($meeting_id, 'time', $time);
	        self::update_post_meta($meeting_id, 'time_duration', $time_duration);
	        
	        self::update_post_meta($meeting_id, 'meeting_platform', $meeting_platform);
	        
	        if ($meeting_platform == 'zoom') {
	            self::update_post_meta($meeting_id, 'zoom_meeting_id', $zoom_meeting_id);
	            self::update_post_meta($meeting_id, 'zoom_meeting_url', $zoom_meeting_url);
	        }

	        // messages
	        $messages = array(array(
	        	'type' => 'create',
	        	'date' => strtotime('now'),
	        	'message' => $message,
	        ));
	        self::update_post_meta($meeting_id, 'messages', $messages);

	        // send email
	        if ( wp_freeio_get_option('user_notice_add_new_meeting') ) {
		        $email = WP_Freeio_Freelancer::get_post_meta($freelancer_id, 'email', true);

		        $email_args = array(
		        	'user_name' => get_the_title($freelancer_id),
		        	'date' => date( get_option('date_format'), $meet_exct_stime),
		        	'time' => $time,
		        	'time_duration' => $time_duration,
		        	'message' => $message,
		        	'job_title' => get_the_title($job_id),
		        	'employer_name' => get_the_title($employer_id),
		        	'zoom_meeting_url' => $zoom_meeting_url,
		        );

		        $email_subject = WP_Freeio_Email::render_email_vars( $email_args, 'meeting_create', 'subject');
		        $email_content = WP_Freeio_Email::render_email_vars( $email_args, 'meeting_create', 'content');

		        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
		        
				$result = WP_Freeio_Email::wp_mail( $email, $email_subject, $email_content, $headers );
			}
			// end send email

			$notify_args = array(
				'post_type' => 'freelancer',
				'user_post_id' => $freelancer_id,
	            'type' => 'create_meeting',
	            'post_id' => $post_id,
	            'employer_id' => $employer_id,
	            'job_id' => $job_id,
	            'meeting_id' => $meeting_id,
			);
			WP_Freeio_User_Notification::add_notification($notify_args);

	        do_action('wp-freeio-before-after-create-meeting', $meeting_id);

	        $return = array( 'status' => true, 'msg' => esc_html__('You have successfully created a meeting', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
	    }

	    $return = array( 'status' => false, 'msg' => esc_html__('Error accord when creating a meeting', 'wp-freeio') );
	   	echo wp_json_encode($return);
	   	exit;
	}

	public static function process_reschedule_meeting() {
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-reschedule-meeting-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		if ( !is_user_logged_in() || (!WP_Freeio_User::is_employer() && !WP_Freeio_User::is_freelancer() ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to reschedule meeting.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$meeting_id = !empty($_POST['meeting_id']) ? $_POST['meeting_id'] : '';
		$meeting = get_post($meeting_id);

		if ( !$meeting || empty($meeting->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Meeting doesn\'t exist', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$date = sanitize_text_field(!empty($_POST['date']) ? $_POST['date'] : '');
		$time = sanitize_text_field(!empty($_POST['time']) ? $_POST['time'] : '');
		$time_duration = sanitize_text_field(!empty($_POST['time_duration']) ? $_POST['time_duration'] : '');
		$message = sanitize_text_field(!empty($_POST['message']) ? $_POST['message'] : '');
		if ( empty($date) || empty($time) || empty($time_duration) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Fill all fields', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$user_id = WP_Freeio_User::get_user_id();

		if ( WP_Freeio_User::is_employer() ) {
			$user_post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
			
			$email_user_post_id = self::get_post_meta($meeting_id, 'freelancer_id');
			$email = WP_Freeio_Freelancer::get_post_meta($email_user_post_id, 'email', true);
			$post_type = 'employer';
		} else {
			$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
			$email = WP_Freeio_Freelancer::get_post_meta($user_post_id, 'email', true);

			$post_author = get_post_field('post_author', $meeting_id);
			$email_user_post_id = WP_Freeio_User::get_employer_by_user_id($post_author);
			$email = WP_Freeio_Employer::get_post_meta($email_user_post_id, 'email', true);
			$post_type = 'freelancer';
		}

		do_action('wp-freeio-before-reschedule-meeting');

        // Insert the post into the database
        
        self::update_post_meta($meeting_id, 'status', '');
        self::update_post_meta($meeting_id, 'date', $date);
        self::update_post_meta($meeting_id, 'time', $time);
        self::update_post_meta($meeting_id, 'time_duration', $time_duration);
        
        $messages = self::get_post_meta($meeting_id, 'messages');
        // messages
        if ( empty($messages) ) {
	        $messages = array(array(
	        	'type' => 'reschedule',
	        	'date' => strtotime('now'),
	        	'user_post_id' => $user_post_id,
	        	'message' => sanitize_text_field($message),
	        ));
	    } else {
	    	$messages = array_merge(
	    		array(array(
		        	'type' => 'reschedule',
		        	'date' => strtotime('now'),
		        	'user_post_id' => $user_post_id,
		        	'message' => sanitize_text_field($message),
		        )),
		        $messages
	    	);
	    }
        self::update_post_meta($meeting_id, 'messages', $messages);

        // send email
        if ( wp_freeio_get_option('user_notice_add_reschedule_meeting') ) {
	        $post_id = self::get_post_meta($meeting_id, 'post_id');
	        if ( get_post_type($post_id) == 'job_applicant') {
				$job_id = WP_Freeio_Applicant::get_post_meta($post_id, 'job_id');
			} elseif( get_post_type($post_id) == 'project_proposal' ) {
				$job_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id');
			} elseif( get_post_type($post_id) == 'service_order' ) {
				$job_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id');
			}

	        $email_args = array(
	        	'user_name' => get_the_title($email_user_post_id),
	        	'date' => $date,
	        	'time' => $time,
	        	'time_duration' => $time_duration,
	        	'message' => $message,
	        	'job_title' => get_the_title($job_id),
	        );

	        $email_subject = WP_Freeio_Email::render_email_vars( $email_args, 'meeting_reschedule', 'subject');
	        $email_content = WP_Freeio_Email::render_email_vars( $email_args, 'meeting_reschedule', 'content');

	        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
	        
			$result = WP_Freeio_Email::wp_mail( $email, $email_subject, $email_content, $headers );
		}
		// end send email

		$notify_args = array(
			'post_type' => $post_type,
			'user_post_id' => $email_user_post_id,
            'type' => 'reschedule_meeting',
            'meeting_id' => $meeting_id,
            'reschedule_user_id' => $email_user_post_id,
		);
		WP_Freeio_User_Notification::add_notification($notify_args);

        do_action('wp-freeio-before-after-reschedule-meeting', $meeting_id);

        $return = array( 'status' => true, 'msg' => esc_html__('You have successfully re-scheduled a meeting', 'wp-freeio') );
	   	echo wp_json_encode($return);
	   	exit;
    
	}

	public static function process_remove_meeting() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-remove-meeting-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to remove meeting.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$meeting_id = !empty($_POST['meeting_id']) ? $_POST['meeting_id'] : '';

		if ( empty($meeting_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Meeting doesn\'t exist', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$user_id = WP_Freeio_User::get_user_id();
		$author_id = get_post_field('post_author', $meeting_id);

		if ( $author_id != $user_id ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('You can not remove this meeting.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$freelancer_id = self::get_post_meta($meeting_id, 'freelancer_id');
		$post_id = self::get_post_meta($meeting_id, 'post_id');
		$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);

		do_action('wp-freeio-process-remove-meeting', $_POST);

		if ( wp_delete_post( $meeting_id ) ) {

			$notify_args = array(
				'post_type' => 'freelancer',
				'user_post_id' => $freelancer_id,
	            'type' => 'remove_meeting',
	            'meeting_id' => $meeting_id,
	            'post_id' => $post_id,
	            'employer_id' => $employer_id,
			);
			WP_Freeio_User_Notification::add_notification($notify_args);

	        $return = array( 'status' => true, 'msg' => esc_html__('Meeting removed successful', 'wp-freeio') );

	        do_action('wp-freeio-after-remove-meeting', $meeting_id, $_POST);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Remove meeting error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function process_cancel_meeting() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-cancel-meeting-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to cancel meeting.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$meeting_id = !empty($_POST['meeting_id']) ? $_POST['meeting_id'] : '';

		if ( empty($meeting_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Meeting doesn\'t exist', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$user_id = WP_Freeio_User::get_user_id();
		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
		$post_freelancer_id = self::get_post_meta($meeting_id, 'freelancer_id');

		if ( $freelancer_id != $post_freelancer_id ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('You can not cancel this meeting.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-cancel-meeting', $_POST);

		self::update_post_meta($meeting_id, 'status', 'cancel');

		$post_id = self::get_post_meta($meeting_id, 'post_id');
		$author_id = get_post_field('post_author', $meeting_id);
		$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);

		$notify_args = array(
			'post_type' => 'employer',
			'user_post_id' => $employer_id,
            'type' => 'cancel_meeting',
            'meeting_id' => $meeting_id,
            'post_id' => $post_id,
            'freelancer_id' => $freelancer_id,
		);
		WP_Freeio_User_Notification::add_notification($notify_args);
		
        do_action('wp-freeio-after-cancel-meeting', $meeting_id, $_POST);

        $return = array( 'status' => true, 'msg' => esc_html__('Meeting canceled successful', 'wp-freeio') );
	   	echo wp_json_encode($return);
	   	exit;
	}

	
}

WP_Freeio_Meeting::init();