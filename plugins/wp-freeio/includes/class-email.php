<?php
/**
 * Email
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Email {
	
	public static $emails_vars;

	public static function init() {
		// Ajax endpoints.
		add_action( 'wpfi_ajax_wp_freeio_ajax_contact_form',  array(__CLASS__,'process_send_contact') );

		
		// compatible handlers.
		add_action( 'wp_ajax_wp_freeio_ajax_contact_form',  array(__CLASS__,'process_send_contact') );
		add_action( 'wp_ajax_nopriv_wp_freeio_ajax_contact_form',  array(__CLASS__,'process_send_contact') );
	}

	public static function wp_mail( $author_email, $subject, $content, $headers, $attachments = null) {
		if ( !preg_match( '%<html[>\s].*</html>%is', $content ) ) {
			$header = apply_filters( 'wp-freeio-mail-html-header',
				'<!doctype html>
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset='.get_bloginfo( 'charset' ).'" />
			<title>' . esc_html( $subject ) . '</title>
			</head>
			<body>
			', $subject );

			$footer = apply_filters( 'wp-freeio-mail-html-footer',
						'</body>
			</html>' );

			$content = $header . wpautop( $content ) . $footer;
		}
		
		return wp_mail( $author_email, $subject, $content, $headers, $attachments );
	}

	public static function process_send_contact() {
		$is_form_filled = ! empty( $_POST['email'] ) && ! empty( $_POST['subject'] ) && ! empty( $_POST['message'] ) && ! empty( $_POST['post_id'] );

		if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) {

			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_Freeio_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
				$is_form_filled = false;
			}
		}
		
		$post_type = get_post_type( $_POST['post_id'] );
		if ( $post_type == 'employer' ) {
			$author_email = get_post_meta( $_POST['post_id'], WP_FREEIO_EMPLOYER_PREFIX.'email', true );
		} elseif ( $post_type == 'freelancer' ) {
			$author_email = get_post_meta( $_POST['post_id'], WP_FREEIO_FREELANCER_PREFIX.'email', true );
		} elseif ( $post_type == 'service' ) {
			$author_id = get_post_field('post_author', $_POST['post_id']);
			if ( WP_Freeio_User::is_freelancer($author_id) ) {
				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);
				$author_email = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true );
			}
			if ( empty($author_email) ) {
				$user_info = get_userdata($author_id);
				$author_email = $user_info->user_email;
			}
		} elseif ( $post_type == 'project' ) {
			$author_id = get_post_field('post_author', $_POST['post_id']);
			if ( WP_Freeio_User::is_employer($author_id) ) {
				$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
				$author_email = get_post_meta( $employer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true );
			}
			if ( empty($author_email) ) {
				$user_info = get_userdata($author_id);
				$author_email = $user_info->user_email;
			}
		} elseif ( $post_type == 'job_listing' ) {
			$author_id = get_post_field('post_author', $_POST['post_id']);
			if ( WP_Freeio_User::is_employer($author_id) ) {
				$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
				$author_email = get_post_meta( $employer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true );
			}
			if ( empty($author_email) ) {
				$user_info = get_userdata($author_id);
				$author_email = $user_info->user_email;
			}
		}
		
		if ( $is_form_filled && !empty($author_email) ) {
			$post = get_post($_POST['post_id']);
			if ( $post->post_type == 'freelancer' && !WP_Freeio_Freelancer::check_restrict_view_contact_info($post) ) {
				$return = array(
					'status' => false,
					'msg' => esc_html__('You have no package.', 'wp-freeio')
				);
				echo wp_json_encode($return);
	   			exit;
			}
			// contact email check
			do_action('wp-freeio-before-process-send-contact', $post_type, $_POST);

	        $email = sanitize_text_field( $_POST['email'] );
	        $phone = sanitize_text_field( $_POST['phone'] );
	        $t_subject = sanitize_text_field( $_POST['subject'] );
	        $message = sanitize_textarea_field( $_POST['message'] );

	        $subject = str_replace('{{subject}}', $t_subject, wp_freeio_get_option('contact_form_notice_subject'));

	        $content = wp_freeio_get_option('contact_form_notice_content');
	        $content = str_replace('{{subject}}', $t_subject, $content);
	        $content = str_replace('{{website_url}}', home_url(), $content);
	        $content = str_replace('{{website_name}}', get_bloginfo( 'name' ), $content);
	        $content = str_replace('{{email}}', $email, $content);
	        $content = str_replace('{{phone}}', $phone, $content);
	        $content = str_replace('{{message}}', $message, $content);
	        
	        $job_title = $job_url = '';
	        if ( ! empty( $_POST['job_id'] ) ) {
	        	$job_id = $_POST['job_id'];
	        	$job_title = get_the_title($job_id);
	        	$job_url = get_permalink($job_id);
	        }

	        $content = str_replace('{{job_title}}', $job_title, $content);
        	$content = str_replace('{{job_url}}', $job_url, $content);

	        $email_admin = get_option( 'admin_email', false );
	        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $email_admin, $email_admin );
	        
	        $result = false;
			$result = self::wp_mail( $author_email, $subject, $content, $headers );
	        if ( $result ) {
	        	$return = array( 'status' => true, 'msg' => esc_html__('Your message has been successfully sent.', 'wp-freeio') );

	        	do_action('wp-freeio-after-process-send-contact', $post_type, $_POST);
	        } else {
	        	$return = array( 'status' => false, 'msg' => esc_html__('An error occurred when sending an email.', 'wp-freeio') );
	        }
	    } else {
	    	$return = array( 'status' => false, 'msg' => esc_html__('Form has been not filled correctly.', 'wp-freeio') );
	    }
	    echo wp_json_encode($return);
	   	exit;
	}

	public static function emails_vars() {
		self::$emails_vars = apply_filters( 'wp-freeio-emails-vars', array(
			'admin_notice_add_new_job' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'job_title', 'job_type', 'job_category', 'job_publish_date', 'job_expiry_date', 'job_featured', 'job_status', 'job_url', 'author', 'website_url', 'website_name' )
			),
			'admin_notice_updated_job' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'job_title', 'job_type', 'job_category', 'job_publish_date', 'job_expiry_date', 'job_featured', 'job_status', 'job_url', 'author', 'website_url', 'website_name' )
			),
			'admin_notice_expiring_job' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'job_title', 'job_type', 'job_category', 'job_publish_date', 'job_expiry_date', 'job_featured', 'job_status', 'job_url', 'website_url', 'website_name', 'job_admin_edit_url' )
			),
			'employer_notice_expiring_job' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'job_title', 'job_type', 'job_category', 'job_publish_date', 'job_expiry_date', 'job_featured', 'job_status', 'job_url', 'website_url', 'website_name', 'employer_dashboard_url', 'my_jobs' )
			),
			// project
			'admin_notice_add_new_project' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'project_location', 'project_category', 'project_publish_date', 'project_expiry_date', 'project_featured', 'project_status', 'project_url', 'author', 'website_url', 'website_name' )
			),
			'admin_notice_updated_project' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'project_location', 'project_category', 'project_publish_date', 'project_expiry_date', 'project_featured', 'project_status', 'project_url', 'author', 'website_url', 'website_name' )
			),
			'admin_notice_expiring_project' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'project_location', 'project_category', 'project_publish_date', 'project_expiry_date', 'project_featured', 'project_status', 'project_url', 'website_url', 'website_name', 'project_admin_edit_url' )
			),
			'employer_notice_expiring_project' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'project_location', 'project_category', 'project_publish_date', 'project_expiry_date', 'project_featured', 'project_status', 'project_url', 'website_url', 'website_name', 'employer_dashboard_url', 'my_projects' )
			),
			// service
			'admin_notice_add_new_service' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_location', 'service_category', 'service_publish_date', 'service_expiry_date', 'service_featured', 'service_status', 'service_url', 'author', 'website_url', 'website_name' )
			),
			'admin_notice_updated_service' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_location', 'service_category', 'service_publish_date', 'service_expiry_date', 'service_featured', 'service_status', 'service_url', 'author', 'website_url', 'website_name' )
			),
			'admin_notice_expiring_service' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_location', 'service_category', 'service_publish_date', 'service_expiry_date', 'service_featured', 'service_status', 'service_url', 'website_url', 'website_name', 'service_admin_edit_url' )
			),
			'freelancer_notice_expiring_service' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_location', 'service_category', 'service_publish_date', 'service_expiry_date', 'service_featured', 'service_status', 'service_url', 'website_url', 'website_name', 'dashboard_url', 'my_services' )
			),
			//
			'send_proposal_notice' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'project_url', 'freelancer_name', 'email', 'phone', 'message', 'amount', 'estimeted_time', 'website_name', 'website_url' )
			),
			'hired_proposal_notice' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'freelancer_name', 'amount', 'estimeted_time', 'website_name', 'website_url' )
			),
			'hired_proposal_employer_notice' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'freelancer_name', 'amount', 'estimeted_time', 'website_name', 'website_url' )
			),
			'completed_project_notice' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'completed_project_employer_notice' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'cancelled_project_notice' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'cancelled_project_employer_notice' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'hired_project_message_notice' => array(
				'subject' => array( 'project_title' ),
				'content' => array( 'project_title', 'project_url', 'username', 'message', 'message_url', 'website_name', 'website_url' )
			),
			// service
			'hired_service_notice' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_url', 'employer_name', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'hired_service_employer_notice' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_url', 'employer_name', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'completed_service_notice' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_url', 'employer_name', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'completed_service_employer_notice' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_url', 'employer_name', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'cancelled_service_notice' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_url', 'employer_name', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'cancelled_service_employer_notice' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_url', 'employer_name', 'freelancer_name', 'amount', 'website_name', 'website_url' )
			),
			'hired_service_message_notice' => array(
				'subject' => array( 'service_title' ),
				'content' => array( 'service_title', 'service_url', 'username', 'message', 'message_url', 'website_name', 'website_url' )
			),
			
			//
			'created_dispute_notice' => array(
				'subject' => array( 'post_title' ),
				'content' => array( 'post_title', 'post_url', 'dispute_url', 'username', 'message', 'website_name', 'website_url' )
			),
			'created_dispute_admin_notice' => array(
				'subject' => array( 'post_title' ),
				'content' => array( 'post_title', 'post_url', 'dispute_url', 'username', 'message', 'website_name', 'website_url' )
			),
			'dispute_message_notice' => array(
				'subject' => array( 'post_title' ),
				'content' => array( 'post_title', 'post_url', 'username', 'message', 'message_url', 'website_name', 'website_url' )
			),
			'dispute_user_winner_notice' => array(
				'subject' => array( 'post_title' ),
				'content' => array( 'post_title', 'post_url', 'username', 'dispute_url', 'website_name', 'website_url' )
			),
			'dispute_user_loser_notice' => array(
				'subject' => array( 'post_title' ),
				'content' => array( 'post_title', 'post_url', 'username', 'dispute_url', 'website_name', 'website_url' )
			),
			//
			'email_apply_job_notice' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'job_title', 'fullname', 'email', 'phone', 'message', 'cv_file_url', 'website_name', 'website_url' )
			),
			'internal_apply_job_notice' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'job_title', 'freelancer_name', 'email', 'phone', 'resume_url', 'cv_file_url', 'message', 'website_name', 'website_url' )
			),
			'applied_job_thanks_notice' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'job_title', 'job_url', 'freelancer_name', 'website_name', 'website_url' )
			),

			'job_alert_notice' => array(
				'subject' => array( 'alert_title' ),
				'content' => array( 'alert_title', 'jobs_found', 'website_url', 'website_name', 'email_frequency_type', 'jobs_alert_url' )
			),
			'freelancer_alert_notice' => array(
				'subject' => array( 'alert_title' ),
				'content' => array( 'alert_title', 'freelancers_found', 'website_url', 'website_name', 'email_frequency_type', 'freelancers_alert_url' )
			),
			'contact_form_notice' => array(
				'subject' => array( 'subject' ),
				'content' => array( 'subject', 'message', 'email', 'phone', 'job_title', 'job_url', 'website_url', 'website_name' )
			),
			'reject_interview_notice' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'freelancer_name', 'employer_name', 'job_title', 'job_url', 'website_url', 'website_name' )
			),
			'undo_reject_interview_notice' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'freelancer_name', 'employer_name', 'job_title', 'job_url', 'website_url', 'website_name' )
			),
			'approve_interview_notice' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'freelancer_name', 'employer_name', 'job_title', 'job_url', 'website_url', 'website_name' )
			),
			'undo_approve_interview_notice' => array(
				'subject' => array( 'job_title' ),
				'content' => array( 'freelancer_name', 'employer_name', 'job_title', 'job_url', 'website_url', 'website_name' )
			),

			'user_register_auto_approve' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'login_url', 'website_url', 'website_name' )
			),
			'user_register_need_approve' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'approve_url', 'website_url', 'website_name' )
			),
			'user_register_approved' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'login_url', 'dashboard_url', 'website_url', 'website_name' )
			),
			'user_register_denied' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'website_url', 'website_name' )
			),
			'user_reset_password' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'user_email', 'new_password', 'website_url', 'website_name' )
			),

			'meeting_create' => array(
				'subject' => array( 'user_name', 'date', 'time', 'time_duration' ),
				'content' => array( 'employer_name', 'job_title', 'user_name', 'date', 'time', 'time_duration', 'message', 'zoom_meeting_url', 'website_url', 'website_name' )
			),

			'meeting_reschedule' => array(
				'subject' => array( 'user_name', 'date', 'time', 'time_duration' ),
				'content' => array( 'job_title', 'user_name', 'date', 'time', 'time_duration', 'message', 'website_url', 'website_name' )
			),

			'invite_freelancer_notice' => array(
				'subject' => array( 'freelancer_name' ),
				'content' => array( 'list_projects', 'freelancer_name' , 'employer_name', 'website_url', 'website_name' )
			),
			'report_notice' => array(
				'subject' => array( 'subject', 'post_title' ),
				'content' => array( 'post_title', 'post_url', 'subject', 'message', 'website_url', 'website_name' )
			),
			// withdraw
			'admin_notice_freelancer_withdraw' => array(
				'subject' => array( 'amount', 'freelancer_name' ),
				'content' => array( 'amount', 'payout_method', 'payouts_details', 'freelancer_name', 'website_url', 'website_name' )
			),
			// verification
			'admin_notice_user_verification' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'contact_number', 'verification_number', 'address', 'verification_url', 'website_url', 'website_name' )
			),
			'user_notice_admin_approve_verification' => array(
				'subject' => array( 'user_name' ),
				'content' => array( 'user_name', 'website_url', 'website_name' )
			),
		));
		return self::$emails_vars;
	}

	public static function display_email_vars($key, $type = 'subject') {
		self::emails_vars();
		$output = '';
		if ( !empty(self::$emails_vars[$key][$type]) ) {
			$i = 1;
			foreach (self::$emails_vars[$key][$type] as $value) {
				$output .= '{{'.$value.'}}'.($i < count(self::$emails_vars[$key][$type]) ? ', ' : '');
				$i++;
			}
		}
		return $output;
	}

	public static function render_email_vars($args, $key, $type = 'subject') {
		self::emails_vars();
		$output = wp_freeio_get_option($key.'_'.$type);
		if ( empty($output) && $type == 'content' ) {
			$output = self::get_email_default_content($key);
		}
		if ( !empty(self::$emails_vars[$key][$type]) ) {
			$vars = self::$emails_vars[$key][$type];
			foreach ($vars as $var) {
				if ( strpos($output, '{{'.$var.'}}') !== false ) {
					if ( isset($args[$var]) ) {
						$value = $args[$var];
					} elseif ( is_callable( array('WP_Freeio_Email', $var) ) ) {
						$value = call_user_func( array('WP_Freeio_Email', $var), $args );
					} else {
						$value = apply_filters('wp-freeio-render-email-var-'.$var, '', $args);
					}
					$output = str_replace('{{'.$var.'}}', $value, $output);
				}
			}
		}
		return apply_filters( 'wp-freeio-render-emails-vars', $output, $args, $key, $type );
	}

	public static function get_email_default_content($key) {
		$return = '';
		if ( !empty($key) ) {
			$name = 'html-'.str_replace('_', '-', $key);
            if ( file_exists( WP_FREEIO_PLUGIN_DIR . "includes/email-templates-default/{$name}.php" ) ) {
				ob_start();
	                load_template(WP_FREEIO_PLUGIN_DIR . "includes/email-templates-default/{$name}.php", false);
	            $return = ob_get_clean();
			}
		}
		return trim($return);
	}

	public static function job_title($args) {
		$output = '';
		if ( isset($args['job']) && !empty($args['job']->post_title) ) {
			$output = $args['job']->post_title;
		}
		return $output;
	}

	public static function job_type($args) {
		$output = '';
		if ( isset($args['job']) && !empty($args['job']->ID) ) {
			$terms = get_the_terms( $args['job']->ID, 'job_listing_type' );
			if ( $terms ) {
				$k = count( $terms );
				foreach ($terms as $term) {
					$k -= 1;
					if ( $k == 0 ) {
						$output .= '<a class="type-job" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>';
					} else {
						$output .= '<a class="type-job" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>, ';
					}
		    	}
			}
		}
		return $output;
	}

	public static function job_category($args) {
		$output = '';
		if ( isset($args['job']) && !empty($args['job']->ID) ) {
			$terms = get_the_terms( $args['job']->ID, 'job_listing_category' );
			if ( $terms ) {
				$k = count( $terms );
				foreach ($terms as $term) {
					$k -= 1;
					if ( $k == 0 ) {
						$output .= '<a class="type-job" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>';
					} else {
						$output .= '<a class="type-job" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>, ';
					}
		    	}
			}
		}
		return $output;
	}

	public static function job_publish_date($args) {
		$output = '';
		if ( isset($args['job']) && !empty($args['job']->ID) ) {
			$output = get_the_date(get_option('date_format'), $args['job']->ID);
		}
		return $output;
	}

	public static function job_expiry_date($args) {
		$output = '';
		if ( isset($args['job']) && !empty($args['job']->ID) ) {
			$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($args['job']->ID);

			$expiry_date = $meta_obj->get_post_meta( 'expiry_date' );
			if ( $expiry_date ) {
				$expiry_date = strtotime($expiry_date);
				$output = date_i18n(get_option('date_format'), $expiry_date);
			}
		}
		return $output;
	}

	public static function job_featured($args) {
		$output = '';
		if ( isset($args['job']) && !empty($args['job']->ID) ) {
			$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($args['job']->ID);

			$featured = $meta_obj->get_post_meta( 'featured' );
			if ( $featured ) {
				$output = esc_html__('Yes', 'wp-freeio');
			} else {
				$output = esc_html__('No', 'wp-freeio');
			}
		}
		return $output;
	}

	public static function job_urgent($args) {
		$output = '';
		if ( isset($args['job']) && !empty($args['job']->ID) ) {
			$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($args['job']->ID);

			$urgent = $meta_obj->get_post_meta( 'urgent' );
			if ( $urgent ) {
				$output = esc_html__('Yes', 'wp-freeio');
			} else {
				$output = esc_html__('No', 'wp-freeio');
			}
		}
		return $output;
	}

	public static function job_status($args) {
		$output = '';
		if ( isset($args['job']) && !empty($args['job']->post_status) ) {
			$post_status = get_post_status_object( $args['job']->post_status );
			if ( !empty($post_status->label) ) {
				$output = $post_status->label;
			} else {
				$output = $post_status->post_status;
			}
		}
		return $output;
	}

	public static function job_url($args) {
		$output = '';
		if ( !empty($args['job']) ) {
			$output = get_permalink($args['job']);
		}
		return $output;
	}

	public static function website_url($args) {
		$output = home_url();
		
		return $output;
	}

	public static function website_name($args) {
		$output = get_bloginfo( 'name' );
		
		return $output;
	}

	public static function employer_dashboard_url($args) {
		$output = '';
		$dashboard_page_id = wp_freeio_get_option('employer_dashboard_page_id');
		$output = get_permalink($dashboard_page_id);
		return $output;
	}

	public static function dashboard_url($args) {
		$output = '';
		$dashboard_page_id = wp_freeio_get_option('user_dashboard_page_id');
		$output = get_permalink($dashboard_page_id);
		if ( !empty($args['user_obj']->ID) ) {
			$user_id = $args['user_obj']->ID;
			if ( WP_Freeio_User::is_employer($user_id) ) {
				$dashboard_page_id = wp_freeio_get_option('employer_dashboard_page_id');
				if ( $dashboard_page_id ) {
					$output = get_permalink($dashboard_page_id);
				}
			}
		}
		return $output;
	}

	public static function my_jobs($args) {
		$output = '';
		$my_jobs_page_id = wp_freeio_get_option('my_jobs_page_id');
		$output = get_permalink($my_jobs_page_id);
		return $output;
	}

	public static function job_admin_edit_url($args) {
		$output = '';
		if ( !empty($args['job']) ) {
			$output = admin_url( sprintf( 'post.php?post=%d&amp;action=edit', $args['job']->ID ) );
		}
		return $output;
	}

	public static function author($job) {
		$output = '';
		if ( !empty($args['job']) && !empty($args['job']->ID) ) {
			$author_id = WP_Freeio_Job_Listing::get_author_id($args['job']->ID);
			$output = get_the_author_meta( 'display_name', $author_id );
		}
		return $output;
	}

	// services
	public static function my_services($args) {
		$output = '';
		$my_services_page_id = wp_freeio_get_option('my_services_page_id');
		$output = get_permalink($my_services_page_id);
		return $output;
	}

	public static function service_admin_edit_url($args) {
		$output = '';
		if ( !empty($args['service']) ) {
			$output = admin_url( sprintf( 'post.php?post=%d&amp;action=edit', $args['service']->ID ) );
		}
		return $output;
	}

	public static function service_title($args) {
		$output = '';
		if ( isset($args['service']) && !empty($args['service']->post_title) ) {
			$output = $args['service']->post_title;
		}
		return $output;
	}

	public static function service_type($args) {
		$output = '';
		if ( isset($args['service']) && !empty($args['service']->ID) ) {
			$terms = get_the_terms( $args['service']->ID, 'service_type' );
			if ( $terms ) {
				$k = count( $terms );
				foreach ($terms as $term) {
					$k -= 1;
					if ( $k == 0 ) {
						$output .= '<a class="type-service" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>';
					} else {
						$output .= '<a class="type-service" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>, ';
					}
		    	}
			}
		}
		return $output;
	}

	public static function service_category($args) {
		$output = '';
		if ( isset($args['service']) && !empty($args['service']->ID) ) {
			$terms = get_the_terms( $args['service']->ID, 'service_category' );
			if ( $terms ) {
				$k = count( $terms );
				foreach ($terms as $term) {
					$k -= 1;
					if ( $k == 0 ) {
						$output .= '<a class="type-service" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>';
					} else {
						$output .= '<a class="type-service" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>, ';
					}
		    	}
			}
		}
		return $output;
	}

	public static function service_publish_date($args) {
		$output = '';
		if ( isset($args['service']) && !empty($args['service']->ID) ) {
			$output = get_the_date(get_option('date_format'), $args['service']->ID);
		}
		return $output;
	}

	public static function service_expiry_date($args) {
		$output = '';
		if ( isset($args['service']) && !empty($args['service']->ID) ) {
			$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($args['service']->ID);

			$expiry_date = $meta_obj->get_post_meta( 'expiry_date' );
			if ( $expiry_date ) {
				$expiry_date = strtotime($expiry_date);
				$output = date_i18n(get_option('date_format'), $expiry_date);
			}
		}
		return $output;
	}

	public static function service_featured($args) {
		$output = '';
		if ( isset($args['service']) && !empty($args['service']->ID) ) {
			$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($args['service']->ID);

			$featured = $meta_obj->get_post_meta( 'featured' );
			if ( $featured ) {
				$output = esc_html__('Yes', 'wp-freeio');
			} else {
				$output = esc_html__('No', 'wp-freeio');
			}
		}
		return $output;
	}

	public static function service_urgent($args) {
		$output = '';
		if ( isset($args['service']) && !empty($args['service']->ID) ) {
			$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($args['service']->ID);

			$urgent = $meta_obj->get_post_meta( 'urgent' );
			if ( $urgent ) {
				$output = esc_html__('Yes', 'wp-freeio');
			} else {
				$output = esc_html__('No', 'wp-freeio');
			}
		}
		return $output;
	}

	public static function service_status($args) {
		$output = '';
		if ( isset($args['service']) && !empty($args['service']->post_status) ) {
			$post_status = get_post_status_object( $args['service']->post_status );
			if ( !empty($post_status->label) ) {
				$output = $post_status->label;
			} else {
				$output = $post_status->post_status;
			}
		}
		return $output;
	}

	public static function service_url($args) {
		$output = '';
		if ( !empty($args['service']) ) {
			$output = get_permalink($args['service']);
		}
		return $output;
	}


	// projects
	public static function my_projects($args) {
		$output = '';
		$my_projects_page_id = wp_freeio_get_option('my_projects_page_id');
		$output = get_permalink($my_projects_page_id);
		return $output;
	}

	public static function project_admin_edit_url($args) {
		$output = '';
		if ( !empty($args['project']) ) {
			$output = admin_url( sprintf( 'post.php?post=%d&amp;action=edit', $args['project']->ID ) );
		}
		return $output;
	}

	public static function project_title($args) {
		$output = '';
		if ( isset($args['project']) && !empty($args['project']->post_title) ) {
			$output = $args['project']->post_title;
		}
		return $output;
	}

	public static function project_type($args) {
		$output = '';
		if ( isset($args['project']) && !empty($args['project']->ID) ) {
			$terms = get_the_terms( $args['project']->ID, 'project_type' );
			if ( $terms ) {
				$k = count( $terms );
				foreach ($terms as $term) {
					$k -= 1;
					if ( $k == 0 ) {
						$output .= '<a class="type-project" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>';
					} else {
						$output .= '<a class="type-project" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>, ';
					}
		    	}
			}
		}
		return $output;
	}

	public static function project_category($args) {
		$output = '';
		if ( isset($args['project']) && !empty($args['project']->ID) ) {
			$terms = get_the_terms( $args['project']->ID, 'project_category' );
			if ( $terms ) {
				$k = count( $terms );
				foreach ($terms as $term) {
					$k -= 1;
					if ( $k == 0 ) {
						$output .= '<a class="type-project" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>';
					} else {
						$output .= '<a class="type-project" href="'.get_term_link($term).'" >'.esc_html($term->name).'</a>, ';
					}
		    	}
			}
		}
		return $output;
	}

	public static function project_publish_date($args) {
		$output = '';
		if ( isset($args['project']) && !empty($args['project']->ID) ) {
			$output = get_the_date(get_option('date_format'), $args['project']->ID);
		}
		return $output;
	}

	public static function project_expiry_date($args) {
		$output = '';
		if ( isset($args['project']) && !empty($args['project']->ID) ) {
			$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($args['project']->ID);

			$expiry_date = $meta_obj->get_post_meta( 'expiry_date' );
			if ( $expiry_date ) {
				$expiry_date = strtotime($expiry_date);
				$output = date_i18n(get_option('date_format'), $expiry_date);
			}
		}
		return $output;
	}

	public static function project_featured($args) {
		$output = '';
		if ( isset($args['project']) && !empty($args['project']->ID) ) {
			$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($args['project']->ID);

			$featured = $meta_obj->get_post_meta( 'featured' );
			if ( $featured ) {
				$output = esc_html__('Yes', 'wp-freeio');
			} else {
				$output = esc_html__('No', 'wp-freeio');
			}
		}
		return $output;
	}

	public static function project_urgent($args) {
		$output = '';
		if ( isset($args['project']) && !empty($args['project']->ID) ) {
			$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($args['project']->ID);

			$urgent = $meta_obj->get_post_meta( 'urgent' );
			if ( $urgent ) {
				$output = esc_html__('Yes', 'wp-freeio');
			} else {
				$output = esc_html__('No', 'wp-freeio');
			}
		}
		return $output;
	}

	public static function project_status($args) {
		$output = '';
		if ( isset($args['project']) && !empty($args['project']->post_status) ) {
			$post_status = get_post_status_object( $args['project']->post_status );
			if ( !empty($post_status->label) ) {
				$output = $post_status->label;
			} else {
				$output = $post_status->post_status;
			}
		}
		return $output;
	}

	public static function project_url($args) {
		$output = '';
		if ( !empty($args['project']) ) {
			$output = get_permalink($args['project']);
		}
		return $output;
	}


	// freelancer
	public static function freelancer_name($args) {
		$output = '';
		if ( isset($args['freelancer']) && !empty($args['freelancer']->post_title) ) {
			$output = $args['freelancer']->post_title;
		}
		return $output;
	}

	public static function freelancer_url($args) {
		$output = '';
		if ( isset($args['freelancer']) && !empty($args['freelancer']->post_title) ) {
			$output = get_permalink($args['freelancer']);
		}
		return $output;
	}

	public static function employer_name($args) {
		$output = '';
		if ( isset($args['employer']) && !empty($args['employer']->post_title) ) {
			$output = $args['employer']->post_title;
		}
		return $output;
	}

	public static function employer_url($args) {
		$output = '';
		if ( isset($args['employer']) && !empty($args['employer']->post_title) ) {
			$output = get_permalink($args['employer']);
		}
		return $output;
	}

	public static function login_url($args) {
		$output = '';
		$login_page_id = wp_freeio_get_option('login_register_page_id');
		$output = get_permalink($login_page_id);
		return $output;
	}

	public static function user_name($args) {
		$output = '';
		if ( isset($args['user_obj']) && !empty($args['user_obj']->data->display_name) ) {
			$output = $args['user_obj']->data->display_name;
		}
		return $output;
	}

	public static function user_email($args) {
		$output = '';
		if ( isset($args['user_obj']) && !empty($args['user_obj']->data->user_email) ) {
			$output = $args['user_obj']->data->user_email;
		}
		return $output;
	}

	public static function approve_url($args) {
		$output = '';
		if ( isset($args['user_obj']) && !empty($args['user_obj']->ID) ) {
			$approve_user_page_id = wp_freeio_get_option('approve_user_page_id');
			$admin_url = get_permalink($approve_user_page_id);

			$user_id = $args['user_obj']->ID;
            $code = get_user_meta($user_id, 'account_approve_key', true);
			$output = add_query_arg(array('action' => 'wp_freeio_approve_user', 'user_id' => $user_id, 'approve-key' => $code), $admin_url);
		}
		return $output;
	}
}

WP_Freeio_Email::init();