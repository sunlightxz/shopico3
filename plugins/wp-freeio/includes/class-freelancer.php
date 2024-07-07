<?php
/**
 * Freelancer
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Freelancer {
	
	public static function init() {

		// Ajax endpoints.
		add_action( 'wpfi_ajax_wp_freeio_ajax_apply_email',  array(__CLASS__,'process_apply_email') );

		// apply job internal
		add_action( 'wpfi_ajax_wp_freeio_ajax_apply_internal',  array(__CLASS__,'process_apply_internal') );

		// removed job internal
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_applied',  array(__CLASS__,'process_remove_applied') );

		// wp_freeio_ajax_save_withdraw_settings
		add_action( 'wpfi_ajax_wp_freeio_ajax_save_withdraw_settings',  array(__CLASS__,'process_save_withdraw_settings') );

		add_action( 'wpfi_ajax_wp_freeio_ajax_save_withdraw',  array(__CLASS__,'process_save_withdraw') );


		// download cv
		add_action('wpfi_ajax_wp_freeio_ajax_download_file', array( __CLASS__, 'process_download_file' ) );
		add_action('wpfi_ajax_wp_freeio_ajax_download_proposal_attachment', array( __CLASS__, 'process_download_proposal_attachment' ) );

		// download cv
		add_action('wpfi_ajax_wp_freeio_ajax_download_cv', array( __CLASS__, 'process_download_cv' ) );
		
		// download resume cv
		add_action('wpfi_ajax_wp_freeio_ajax_download_resume_cv', array( __CLASS__, 'process_download_resume_cv' ) );

		// loop
		add_action( 'wp_freeio_before_freelancer_archive', array( __CLASS__, 'display_freelancers_results_filters' ), 5 );
		add_action( 'wp_freeio_before_freelancer_archive', array( __CLASS__, 'display_freelancers_count_results' ), 10 );

		add_action( 'wp_freeio_before_freelancer_archive', array( __CLASS__, 'display_freelancers_alert_orderby_start' ), 15 );
		add_action( 'wp_freeio_before_freelancer_archive', array( __CLASS__, 'display_freelancers_alert_form' ), 20 );
		add_action( 'wp_freeio_before_freelancer_archive', array( __CLASS__, 'display_freelancers_orderby' ), 25 );
		add_action( 'wp_freeio_before_freelancer_archive', array( __CLASS__, 'display_freelancers_alert_orderby_end' ), 100 );

		// restrict
		add_filter( 'wp-freeio-freelancer-query-args', array( __CLASS__, 'freelancer_restrict_listing_query_args'), 100, 2 );
		add_filter( 'wp-freeio-freelancer-filter-query', array( __CLASS__, 'freelancer_restrict_listing_query'), 100, 2 );

		add_action( 'wp_freeio_after_freelancer_archive', array( __CLASS__, 'restrict_freelancer_listing_information' ), 10 );

		add_action( 'template_redirect', array( __CLASS__, 'track_job_view' ), 20 );
	}
	
	public static function track_job_view() {
	    if ( ! is_singular( 'freelancer' ) ) {
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

		if ( !wp_freeio_get_option('admin_notice_expiring_freelancer') ) {
			return;
		}
		$days_notice = wp_freeio_get_option('admin_notice_expiring_freelancer_days');

		$freelancer_ids = self::get_expiring_freelancers($days_notice);

		if ( $freelancer_ids ) {
			foreach ( $freelancer_ids as $freelancer_id ) {
				// send email here.
				$freelancer = get_post($freelancer_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$email_to = get_option( 'admin_email', false );
				$subject = WP_Freeio_Email::render_email_vars(array('freelancer' => $freelancer), 'admin_notice_expiring_freelancer', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('freelancer' => $freelancer), 'admin_notice_expiring_freelancer', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}
		}
	}

	public static function send_freelancer_expiring_notice() {
		global $wpdb;

		if ( !wp_freeio_get_option('freelancer_notice_expiring_freelancer') ) {
			return;
		}
		$days_notice = wp_freeio_get_option('freelancer_notice_expiring_freelancer_days');

		$freelancer_ids = self::get_expiring_freelancers($days_notice);

		if ( $freelancer_ids ) {
			foreach ( $freelancer_ids as $freelancer_id ) {
				// send email here.
				$freelancer = get_post($freelancer_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$email_to = get_the_author_meta( 'user_email', $freelancer->post_author );
				$subject = WP_Freeio_Email::render_email_vars(array('freelancer' => $freelancer), 'freelancer_notice_expiring_listing', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('freelancer' => $freelancer), 'freelancer_notice_expiring_listing', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				
			}
		}
	}

	public static function get_expiring_freelancers($days_notice) {
		$prefix = WP_FREEIO_FREELANCER_PREFIX;

		$notice_before_ts = current_time( 'timestamp' ) + ( DAY_IN_SECONDS * $days_notice );
		$freelancer_ids          = $wpdb->get_col( $wpdb->prepare(
			"
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = %s
			AND postmeta.meta_value = %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'freelancer'
			",
			$prefix.'expiry_date',
			date( 'Y-m-d', $notice_before_ts )
		) );

		return $freelancer_ids;
	}

	public static function check_for_expired_freelancers() {
		global $wpdb;

		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		
		// Change status to expired.
		$freelancer_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
				LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
				WHERE postmeta.meta_key = %s
				AND postmeta.meta_value > 0
				AND postmeta.meta_value < %s
				AND posts.post_status = 'publish'
				AND posts.post_type = 'freelancer'",
				$prefix.'expiry_date',
				date( 'Y-m-d', current_time( 'timestamp' ) )
			)
		);

		if ( $freelancer_ids ) {
			foreach ( $freelancer_ids as $job_id ) {
				$job_data                = array();
				$job_data['ID']          = $job_id;
				$job_data['post_status'] = 'expired';
				wp_update_post( $job_data );
			}
		}

		// Delete old expired jobs.
		if ( apply_filters( 'wp_freeio_delete_expired_freelancers', false ) ) {
			$freelancer_ids = $wpdb->get_col(
				$wpdb->prepare( "
					SELECT posts.ID FROM {$wpdb->posts} as posts
					WHERE posts.post_type = 'freelancer'
					AND posts.post_modified < %s
					AND posts.post_status = 'expired'",
					date( 'Y-m-d', strtotime( '-' . apply_filters( 'wp_freeio_delete_expired_freelancers_days', 30 ) . ' days', current_time( 'timestamp' ) ) )
				)
			);

			if ( $freelancer_ids ) {
				foreach ( $freelancer_ids as $job_id ) {
					wp_trash_post( $job_id );
				}
			}
		}
	}

	public static function is_freelancer_status_changing( $from_status, $to_status ) {
		return isset( $_POST['post_status'] ) && isset( $_POST['original_post_status'] ) && $_POST['original_post_status'] !== $_POST['post_status'] && ( null === $from_status || $from_status === $_POST['original_post_status'] ) && $to_status === $_POST['post_status'];
	}

	public static function calculate_freelancer_expiry( $freelancer_id ) {
		$duration = absint( wp_freeio_get_option( 'resume_duration' ) );
		$duration = apply_filters( 'wp-freeio-calculate-freelancer-expiry', $duration, $freelancer_id);

		if ( $duration ) {
			return date( 'Y-m-d', strtotime( "+{$duration} days", current_time( 'timestamp' ) ) );
		}

		return '';
	}

	public static function get_post_meta($post_id, $key, $single = true) {
		return get_post_meta($post_id, WP_FREEIO_FREELANCER_PREFIX.$key, $single);
	}

	public static function update_post_meta($post_id, $key, $data) {
		return update_post_meta($post_id, WP_FREEIO_FREELANCER_PREFIX.$key, $data);
	}

	public static function get_salary_html( $post_id = null, $html = true ) {
		$min_salary = self::get_min_salary_html($post_id, $html);
		$max_salary = self::get_max_salary_html($post_id, $html);
		$price_html = '';
		if ( $min_salary ) {
			$price_html = $min_salary;
		}
		if ( $max_salary ) {
			$price_html .= (!empty($price_html) ? ' - ' : '').$max_salary;
		}
		if ( $price_html ) {
			$salary_type_html = esc_html__(' / hr', 'wp-freeio');
			$salary_type_html = apply_filters( 'wp-freeio-get-rate-type-html', $salary_type_html, $post_id );
			$price_html = $price_html.$salary_type_html;
		}
		return apply_filters( 'wp-freeio-get-salary-html', $price_html, $post_id );
	}

	public static function get_min_salary_html( $post_id = null, $html = true  ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post_id);
		if ( !$meta_obj->check_post_meta_exist('min_rate') ) {
			return false;
		}
		$price = $meta_obj->get_post_meta( 'min_rate' );

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

		return apply_filters( 'wp-freeio-get-freelancer-min-rate-html', $price, $post_id );
	}
	
	public static function get_max_salary_html( $post_id = null, $html = true  ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post_id);
		if ( !$meta_obj->check_post_meta_exist('max_rate') ) {
			return false;
		}
		$price = $meta_obj->get_post_meta( 'max_rate' );

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

		return apply_filters( 'wp-freeio-get-freelancer-max-rate-html', $price, $post_id );
	}

	public static function is_featured( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$featured = self::get_post_meta( $post_id, 'featured' );
		$return = $featured ? true : false;
		return apply_filters( 'wp-freeio-job-listing-is-featured', $return, $post_id );
	}

	public static function is_urgent( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$urgent = self::get_post_meta( $post_id, 'urgent' );
		$return = $urgent ? true : false;
		return apply_filters( 'wp-freeio-job-listing-is-urgent', $return, $post_id );
	}

	public static function is_filled( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$filled = self::get_post_meta( $post_id, 'filled' );
		$return = $filled ? true : false;
		return apply_filters( 'wp-freeio-job-listing-is-filled', $return, $post_id );
	}
	
	public static function display_download_cv_btn( $post_id = null, $classes = 'btn btn-download-cv', $echo = true ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$download_base_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_resume_cv');
		$download_url = add_query_arg(array('post_id' => $post_id), $download_base_url);

		$check_can_download = true;
		if ( !is_user_logged_in() ) {
			$check_can_download = false;
		} else {
			$user = wp_get_current_user();
			$user_id = WP_Freeio_User::get_user_id();
			if ( !WP_Freeio_User::is_employer($user_id) && !in_array('administrator', $user->roles) ) {
				$check_can_download = false;
				
				if( WP_Freeio_User::is_freelancer($user_id) ) {
					$freelancer_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
					if ( $post_id == $freelancer_post_id ) {
						$check_can_download = true;
					}
				}
			}
		}
		$msg = '';
		$additional_class = $classes;
		if ( !$check_can_download ) {
			$additional_class .= ' cannot-download-cv-btn ';
			$msg = esc_html__('Please login as employer user to download CV.', 'wp-freeio');
		}

		ob_start();
		?>
		<a href="<?php echo esc_url($download_url); ?>" class="<?php echo esc_attr($additional_class); ?>" data-msg="<?php echo esc_attr($msg); ?>"><?php esc_html_e('Download CV', 'wp-freeio'); ?></a>
		<?php
		$html = ob_get_clean();

		$return = apply_filters('wp-freeio-freelancer-display-download-cv-btn', $html, $post_id, $classes);
		if ( $echo ) {
			echo trim($return );
		} else {
			return $return ;
		}
	}

	public static function display_invite_btn( $post_id = null, $classes = 'btn btn-invite-freelancer btn-theme', $echo = true ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		$check_can_invite = true;
		if ( !is_user_logged_in() ) {
			$check_can_invite = false;
		} else {
			$user = wp_get_current_user();
			if ( !WP_Freeio_User::is_employer() && !in_array('administrator', $user->roles) ) {
				$check_can_invite = false;
			}
		}
		$invite_url = '#invite-freelancer-form-wrapper-'.$post_id;
		$msg = '';
		$additional_class = $classes;
		if ( !$check_can_invite ) {
			$additional_class .= ' cannot-download-cv-btn ';
			$msg = esc_html__('Please login as employer user to invite freelancer.', 'wp-freeio');
			$invite_url = 'javascript:void(0);';
		}
		$invite_text = apply_filters('wp-freeio-freelancer-display-invite-freelancer-text', esc_html__('Invite', 'wp-freeio') );



		ob_start();
		?>
		<a href="<?php echo esc_attr($invite_url); ?>" class="<?php echo esc_attr($additional_class); ?>" data-msg="<?php echo esc_attr($msg); ?>"><?php echo $invite_text; ?></a>
		<?php
		if ( $check_can_invite ) {
			echo WP_Freeio_Template_Loader::get_template_part('single-freelancer/invite-freelancer-form', array('freelancer_id' => $post_id));
		}
		$html = ob_get_clean();


		$return = apply_filters('wp-freeio-freelancer-display-invite-freelancer-btn', $html, $post_id, $classes);
		if ( $echo ) {
			echo trim($return );
		} else {
			return $return ;
		}
	}
	
	public static function process_apply_email() {
		$return = array();
		if (  !isset( $_POST['wp-freeio-apply-email-nonce'] ) || ! wp_verify_nonce( $_POST['wp-freeio-apply-email-nonce'], 'wp-freeio-apply-email' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_Freeio_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
				$return = array( 'status' => false, 'msg' => esc_html__('Your recaptcha did not verify.', 'wp-freeio') );
			   	echo wp_json_encode($return);
			   	exit;
			}
		}

		$fullname = !empty($_POST['fullname']) ? $_POST['fullname'] : '';
		$email = !empty($_POST['email']) ? $_POST['email'] : '';
		$phone = !empty($_POST['phone']) ? $_POST['phone'] : '';
		$message = !empty($_POST['message']) ? $_POST['message'] : '';
		$job_id = !empty($_POST['job_id']) ? $_POST['job_id'] : '';

		if ( empty($fullname) || empty($email) || empty($message) || empty($job_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Form has been not filled correctly.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$post = get_post($job_id);
		if ( !$post || empty($post->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Job doesn\'t exist', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		
		$filled = WP_Freeio_Job_Listing::get_post_meta($post->ID, 'filled', true);
		if ( $filled ) {
			$return = array( 'status' => false, 'msg' => esc_html__('This job is filled and no longer accepting applications.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		
		do_action('wp-freeio-process-apply-email', $_POST);

		// cv file
        $cv_file_url = '';
        $files_path = array();
        if ( !empty($_FILES['cv_file']) && !empty($_FILES['cv_file']['name']) ) {
            $file_data = WP_Freeio_Image::upload_cv_file($_FILES['cv_file']);
            if ( $file_data && !empty($file_data->url) ) {
            	$attach_id = WP_Freeio_Image::create_attachment( $file_data->url, 0 );
            	
            	if ( !empty($attach_id) ) {
	            	$download_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_cv');
	            	$download_key = base64_encode('download-file-'.$attach_id);
	        		$cv_file_url = add_query_arg(array('file_id' => $attach_id, 'download_key' => $download_key), $download_url);

	        		$files_path[] = get_attached_file($attach_id);
	        	}
            }

            if ( empty($cv_file_url) ) {
				$return = array( 'status' => false, 'msg' => esc_html__('Can not upload file.', 'wp-freeio') );
			   	echo wp_json_encode($return);
			   	exit;
			}
        }

        if ( empty($cv_file_url) ) {
        	$apply_cv_id = !empty($_POST['apply_cv_id']) ? $_POST['apply_cv_id'] : '';
        	if ( !empty($apply_cv_id) ) {
        		if ( is_array($apply_cv_id) ) {
        			foreach ($apply_cv_id as $attach_id) {
        				$download_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_cv');
			        	$download_key = base64_encode('download-file-'.$attach_id);
			    		$cv_file_url = add_query_arg(array('file_id' => $attach_id, 'download_key' => $download_key), $download_url);

			    		$files_path[] = get_attached_file($attach_id);
        			}
        		} else {
		        	$download_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_cv');
		        	$download_key = base64_encode('download-file-'.$apply_cv_id);
		    		$cv_file_url = add_query_arg(array('file_id' => $apply_cv_id, 'download_key' => $download_key), $download_url);

		    		$files_path[] = get_attached_file($apply_cv_id);
		    	}
	    	}
        }

        $email_subject = WP_Freeio_Email::render_email_vars( array('job_title' => $post->post_title), 'email_apply_job_notice', 'subject');
        $email_content_args = array(
        	'job' => $post,
        	'job_title' => $post->post_title,
        	'message' => sanitize_text_field($message),
        	'fullname' => $fullname,
        	'email' => $email,
        	'phone' => $phone,
        	'cv_file_url' => $cv_file_url,
        );
        $email_content_args = apply_filters('wp-freeio-email-content-args-email-apply', $email_content_args);

        $email_content = WP_Freeio_Email::render_email_vars( $email_content_args, 'email_apply_job_notice', 'content');
		
        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $fullname, $email );
        
        $author_email = get_post_meta( $post->ID, WP_FREEIO_JOB_LISTING_PREFIX.'apply_email', true);
		if ( empty($author_email) ) {
			$author_email = get_post_meta( $post->ID, WP_FREEIO_JOB_LISTING_PREFIX.'email', true);
		}
		if ( empty($author_email) ) {
			$author_id = WP_Freeio_Job_Listing::get_author_id($post->ID);
			$author_email = get_the_author_meta( 'user_email', $author_id );
		}

		$result = WP_Freeio_Email::wp_mail( $author_email, $email_subject, $email_content, $headers, $files_path );
		if ( $result ) {
			// thanks email
			if ( wp_freeio_get_option('freelancer_notice_add_thanks_apply') ) {
				$email_subject = WP_Freeio_Email::render_email_vars( array('job_title' => $post->post_title), 'applied_job_thanks_notice', 'subject');
				$email_content_args = array(
		        	'job' => $post,
		        	'job_title' => $post->post_title,
		        	'freelancer_name' => $fullname,
		        );
		        $email_content_args = apply_filters('wp-freeio-email-content-args-applied-thanks', $email_content_args);
		        $email_content = WP_Freeio_Email::render_email_vars( $email_content_args, 'applied_job_thanks_notice', 'content');
		        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $fullname, $email );
				WP_Freeio_Email::wp_mail( $email, $email_subject, $email_content, $headers );
			}

			// notification
			$author_id = WP_Freeio_Job_Listing::get_author_id($post->ID);
			$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
			$notify_args = array(
				'post_type' => 'employer',
				'user_post_id' => $employer_id,
	            'type' => 'email_apply',
	            'job_id' => $job_id,
			);
			WP_Freeio_User_Notification::add_notification($notify_args);

			$return = array( 'status' => true, 'msg' => esc_html__('You have successfully applied to the job', 'wp-freeio') );
		   	wp_send_json( $return );
		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('Error accord when applying for the job', 'wp-freeio') );
		   	wp_send_json( $return );
		}
	}

	public static function process_apply_internal() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-apply-internal-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_Freeio_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
				$return = array( 'status' => false, 'msg' => esc_html__('Your recaptcha did not verify.', 'wp-freeio') );
			   	echo wp_json_encode($return);
			   	exit;
			}
		}

		if ( !is_user_logged_in() || !WP_Freeio_User::is_freelancer() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login as "Freelancer" to apply.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$job_id = !empty($_POST['job_id']) ? $_POST['job_id'] : '';
		$job = get_post($job_id);

		if ( !$job || empty($job->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Job doesn\'t exist', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$filled = WP_Freeio_Job_Listing::get_post_meta($job->ID, 'filled', true);
		if ( $filled ) {
			$return = array( 'status' => false, 'msg' => esc_html__('This job is filled and no longer accepting applications.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		$user_id = WP_Freeio_User::get_user_id();

		$check_applied = WP_Freeio_Freelancer::check_applied($user_id, $job_id);
		if ( $check_applied ) {
			$return = array(
				'status' => false,
				'msg' => __('You have applied this job.', 'wp-freeio')
			);
		   	wp_send_json( $return );
		}
		$free_apply = self::check_freelancer_can_apply();
		if ( !$free_apply ) {
			$freelancer_package_page_id = wp_freeio_get_option('freelancer_package_page_id', true);
			$package_page_url = $freelancer_package_page_id ? get_permalink($freelancer_package_page_id) : home_url('/');
			$return = array(
				'status' => false,
				'msg' => sprintf(__('You have no package. <a href="%s" class="text-theme">Click here</a> to subscribe a package.', 'wp-freeio'), $package_page_url)
			);
		   	wp_send_json( $return );
		}

		
		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);

		// apply_job_with_percent_resume
		$min_percent_resume = wp_freeio_get_option('apply_job_with_percent_resume', 70);
		if ( !empty($min_percent_resume) && $min_percent_resume > 0 ) {
			$profile_percents = WP_Freeio_User::compute_profile_percent($freelancer_id);
			$percent = isset($profile_percents['percent']) ? $profile_percents['percent'] : 0;
			$profile_percent = !empty($percent) ? $percent*100 : 0;
			if ( $min_percent_resume > 100 ) {
				$min_percent_resume = 100;
			}
			
			if ( $profile_percent < $min_percent_resume ) {
				$return = array( 'status' => false, 'msg' => esc_html__('You need to complete your resume before you can apply for a job.', 'wp-freeio') );
			   	echo wp_json_encode($return);
			   	exit;
			}
		}

		do_action('wp-freeio-process-apply-internal', $_POST);

		// cv file
        $cv_file_url = '';
        if ( !empty($_FILES['cv_file']) && !empty($_FILES['cv_file']['name']) ) {
            $file_data = WP_Freeio_Image::upload_cv_file($_FILES['cv_file']);
            if ( $file_data && !empty($file_data->url) ) {
            	$cv_file_id = WP_Freeio_Image::create_attachment( $file_data->url, 0 );

            	if ( !empty($cv_file_id) ) {
	            	$cv_attachments = self::get_post_meta($freelancer_id, 'cv_attachment');
	            	if ( !empty($cv_attachments) ) {
	            		$cv_attachments[$cv_file_id] = $file_data->url;
	            	} else {
	            		$cv_attachments = array($cv_file_id => $file_data->url);
	            	}
	            	self::update_post_meta($freelancer_id, 'cv_attachment', $cv_attachments);
	            }
            }

            if ( empty($cv_file_id) ) {
				$return = array( 'status' => false, 'msg' => esc_html__('Can not upload file.', 'wp-freeio') );
			   	echo wp_json_encode($return);
			   	exit;
			}
        }

        if ( empty($cv_file_id) ) {
        	$cv_file_id = !empty($_POST['apply_cv_id']) ? $_POST['apply_cv_id'] : '';
        }

		$applicant_id = self::insert_applicant($user_id, $job, $cv_file_id);
		
        if ( $applicant_id ) {
	        $return = array( 'status' => true, 'msg' => esc_html__('You have successfully applied to the job', 'wp-freeio'), 'text' => esc_html__('Applied', 'wp-freeio') );
		   	wp_send_json( $return );
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Error accord when applying for the job', 'wp-freeio') );
		   	wp_send_json( $return );
		}
	}

	public static function insert_applicant($user_id, $job, $cv_file_id = 0) {
		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
		$job_id = $job->ID;

		$post_args = array(
            'post_title' => get_the_title($freelancer_id),
            'post_type' => 'job_applicant',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => $user_id,
        );
		$post_args = apply_filters('wp-freeio-add-job-applicant-data', $post_args);
		do_action('wp-freeio-before-add-job-applicant');

        // Insert the post into the database
        $applicant_id = wp_insert_post($post_args);
        if ( $applicant_id ) {
	        update_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'freelancer_id', $freelancer_id);
	        update_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'job_id', $job_id);
	        update_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'job_name', $job->post_title);
	        
	        do_action('wp-freeio-before-after-job-applicant-send-email', $applicant_id, $job_id, $freelancer_id, $user_id);
	        
	        $message = !empty($_POST['message']) ? $_POST['message'] : '';
    		WP_Freeio_Applicant::update_post_meta($applicant_id, 'message', $message);
    		$cv_file_url = '';
    		$files_path = array();
        	if ( $cv_file_id ) {
        		WP_Freeio_Applicant::update_post_meta($applicant_id, 'cv_file_id', $cv_file_id);

        		$download_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_cv');
        		$download_key = base64_encode('download-file-'.$cv_file_id);
        		$cv_file_url = add_query_arg(array('file_id' => $cv_file_id, 'download_key' => $download_key), $download_url);

        		$files_path[] = get_attached_file($cv_file_id);
        	}

	        // send email
	        $email = self::get_post_meta($freelancer_id, 'email', true);
	        $phone = self::get_post_meta($freelancer_id, 'phone', true);
	        $freelancer_name = get_post_field('post_title', $freelancer_id);

	        if ( wp_freeio_get_option('employer_notice_add_new_internal_apply') ) {
		        $email_subject = WP_Freeio_Email::render_email_vars( array('job_title' => $job->post_title), 'internal_apply_job_notice', 'subject');
		        $email_content_args = array(
		        	'job' => $job,
		        	'job_title' => $job->post_title,
		        	'freelancer_name' => $freelancer_name,
		        	'email' => $email,
		        	'phone' => $phone,
		        	'resume_url' => get_permalink($freelancer_id),
		        	'cv_file_url' => esc_url($cv_file_url),
		        	'message' => $message,
		        );
		        $email_content_args = apply_filters('wp-freeio-email-content-args-internal-apply', $email_content_args, $applicant_id);

		        $email_content = WP_Freeio_Email::render_email_vars( $email_content_args, 'internal_apply_job_notice', 'content');

		        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
		        
				if ( empty($author_email) ) {
					$author_id = WP_Freeio_Job_Listing::get_author_id($job->ID);
					if ( WP_Freeio_User::is_employer($author_id) ) {
						$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
						$author_email = WP_Freeio_Employer::get_post_meta($employer_id, 'email');
					}
					if ( empty($author_email) ) {
						$author_email = get_the_author_meta( 'user_email', $author_id );
					}
				}

				$result = WP_Freeio_Email::wp_mail( $author_email, $email_subject, $email_content, $headers, $files_path );
				// end send email
			}

			// thanks email
			if ( wp_freeio_get_option('freelancer_notice_add_thanks_apply') ) {
				$email_subject = WP_Freeio_Email::render_email_vars( array('job_title' => $job->post_title), 'applied_job_thanks_notice', 'subject');
				$email_content_args = array(
		        	'job' => $job,
		        	'job_title' => $job->post_title,
		        	'freelancer_name' => $freelancer_name,
		        );
		        $email_content_args = apply_filters('wp-freeio-email-content-args-applied-thanks', $email_content_args);
		        $email_content = WP_Freeio_Email::render_email_vars( $email_content_args, 'applied_job_thanks_notice', 'content');
		        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", $fullname, $email );
				WP_Freeio_Email::wp_mail( $email, $email_subject, $email_content, $headers );
			}

			
			// notification
			$author_id = WP_Freeio_Job_Listing::get_author_id($job_id);
			$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
			$notify_args = array(
				'post_type' => 'employer',
				'user_post_id' => $employer_id,
	            'type' => 'internal_apply',
	            'job_id' => $job_id,
	            'freelancer_id' => $freelancer_id,
	            'applicant_id' => $applicant_id,
			);
			WP_Freeio_User_Notification::add_notification($notify_args);

	        do_action('wp-freeio-before-after-job-applicant', $applicant_id, $job_id, $freelancer_id, $user_id);
	    }

	    return $applicant_id;
	}

	public static function check_freelancer_can_apply() {
		$free_apply = wp_freeio_get_option('freelancer_free_job_apply', 'on');
		$return = true;
		if ( $free_apply == 'off' ) {
			$return = false;
		}
		return apply_filters('wp-freeio-check-freelancer-can-apply', $return);
	}
	
	public static function check_applied( $user_id, $job_id ) {
		if ( !is_user_logged_in() || !WP_Freeio_User::is_freelancer() ) {
			return false;
		}
		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
		$posts = get_posts(array(
			'post_type' => 'job_applicant',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => WP_FREEIO_APPLICANT_PREFIX . 'freelancer_id',
			    	'value' => $freelancer_id,
			    	'compare' => '=',
				),
				array(
					'key' => WP_FREEIO_APPLICANT_PREFIX . 'job_id',
			    	'value' => $job_id,
			    	'compare' => '=',
				)
			)
		));
		if ( $posts && is_array($posts) ) {
			return true;
		}
		
		return false;
	}

	public static function process_remove_applied() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-remove-applied-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to remove applied.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$applicant_id = !empty($_POST['applicant_id']) ? $_POST['applicant_id'] : '';

		if ( empty($applicant_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Applicant doesn\'t exist', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$user_id = WP_Freeio_User::get_user_id();
		$is_allowed = WP_Freeio_Mixes::is_allowed_to_remove( $user_id, $applicant_id );
		$job_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'job_id', true);
		$is_allowed_job = WP_Freeio_Mixes::is_allowed_to_remove( $user_id, $job_id );

		if ( !$is_allowed && !$is_allowed_job ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('You can not remove this applied.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		$freelancer_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'freelancer_id', true);
		$freelancer_package_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'freelancer_package_id', true);

		do_action('wp-freeio-process-remove-applied', $_POST);

		if ( wp_delete_post( $applicant_id ) ) {

			$author_id = WP_Freeio_Job_Listing::get_author_id($job_id);
			$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
			$notify_args = array(
				'post_type' => 'freelancer',
				'user_post_id' => $freelancer_id,
	            'type' => 'remove_apply',
	            'job_id' => $job_id,
	            'employer_id' => $employer_id,
	            'applicant_id' => $applicant_id,
			);
			WP_Freeio_User_Notification::add_notification($notify_args);

	        $return = array( 'status' => true, 'msg' => esc_html__('Application removed successful', 'wp-freeio') );

	        do_action('wp-freeio-after-remove-applied', $applicant_id, $job_id, $freelancer_id, $freelancer_package_id, $_POST);
		   	wp_send_json( $return );
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Remove applied error.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
	}

	public static function process_save_withdraw_settings() {
		$return = array();
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to save settings.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$user_id = WP_Freeio_User::get_user_id();

		do_action('wp-freeio-process-save-withdraw-settings-before', $_POST);

		$payout_method = !empty($_POST['payout_method']) ? $_POST['payout_method'] : '';
		switch ($payout_method) {
			case 'paypal':
				$paypal_email = !empty($_POST['paypal_email']) ? sanitize_text_field($_POST['paypal_email']) : '';
				
				update_user_meta($user_id, 'paypal_email', $paypal_email);
				break;
			case 'payoneer':
				$payoneer_email = !empty($_POST['payoneer_email']) ? sanitize_text_field($_POST['payoneer_email']) : '';
				update_user_meta($user_id, 'payoneer_email', $payoneer_email);
				break;
			case 'bacs':
				$bank_transfer_fields = wp_freeio_get_option('bank_transfer_fields', array('bank_account_name', 'bank_account_number', 'bank_name', 'bank_routing_number', 'bank_iban', 'bank_bic_swift'));
				if ( $bank_transfer_fields ) {
					foreach ($bank_transfer_fields as $val) {
        				$value = !empty($_POST[$val]) ? sanitize_text_field($_POST[$val]) : '';
        				update_user_meta($user_id, $val, $value);
    				}
				}
				break;
		}
		update_user_meta($user_id, 'payout_method', $payout_method);

        $return = array( 'status' => true, 'msg' => esc_html__('Saved payout method successfully.', 'wp-freeio') );

        do_action('wp-freeio-after-save-withdraw-settings', $applicant_id, $job_id, $freelancer_id, $freelancer_package_id, $_POST);
	   	wp_send_json( $return );
	}

	public static function process_save_withdraw() {
		$return = array();
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to withdraw earning.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$user_id = WP_Freeio_User::get_user_id();

		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
		$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($freelancer_id);
        $verified = $meta_obj->get_post_meta( 'verified' );
        if ( !$verified ) {
        	$return = array( 'status' => false, 'msg' => esc_html__('Please verify your account to withdraw earning.', 'wp-freeio') );
		   	wp_send_json( $return );
        }

		do_action('wp-freeio-process-save-withdraw-before', $_POST);

		$minimum_withdraw_amount = wp_freeio_get_option('minimum_withdraw_amount', 50);
		$amount = !empty($_POST['amount']) ? $_POST['amount'] : '';
		if ( empty($amount) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please enter amount to withdraw', 'wp-freeio') );
		   	wp_send_json( $return );
		} elseif ( $amount < $minimum_withdraw_amount ) {
			$return = array( 'status' => false, 'msg' => sprintf(esc_html__('You are not allowed to withdraw amount below the %s', 'wp-freeio'), WP_Freeio_Price::format_price($minimum_withdraw_amount, true)) );
		   	wp_send_json( $return );
		}
		
		$total_pending	= WP_Freeio_Post_Type_Withdraw::sum_freelancer_withdraw(array('publish', 'pending'));
		$total_pending	= !empty($total_pending) ? floatval($total_pending) : 0;

		$totalamount = WP_Freeio_Post_Type_Withdraw::sum_freelancer_earning('publish', $user_id);

		$current_balance = 0;
		if (!empty($totalamount)) {
			$balance_remaining	= floatval($totalamount ) - floatval( $total_pending );
			$current_balance    = !empty( $balance_remaining ) && $balance_remaining > 0  ? floatval( $totalamount ) - floatval( $total_pending ) : 0;
		}
		
		if ( $amount > $current_balance ) {
			$return = array( 'status' => false, 'msg' => esc_html__('We are sorry, you haven\'t enough amount to withdraw', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		$payout_method = !empty($_POST['payout_method']) ? $_POST['payout_method'] : '';
		$payouts_details['payout_method'] = $payout_method;
		$payout_method_error = true;
		switch ($payout_method) {
			case 'paypal':
				$paypal_email = get_user_meta($user_id, 'paypal_email', true);
				if ( !empty($paypal_email) ) {
					$payout_method_error = false;
					$payouts_details['paypal_email'] = $paypal_email;
				}
				break;
			case 'payoneer':
				$payoneer_email = get_user_meta($user_id, 'payoneer_email', true);
				if ( !empty($payoneer_email) ) {
					$payout_method_error = false;
					$payouts_details['payoneer_email'] = $payoneer_email;
				}
				break;
			case 'bacs':
				$bank_transfer_fields = wp_freeio_get_option('bank_transfer_fields', array('bank_account_name', 'bank_account_number', 'bank_name', 'bank_routing_number', 'bank_iban', 'bank_bic_swift'));
				if ( $bank_transfer_fields ) {
					$error = false;
					foreach ($bank_transfer_fields as $val) {
        				$value = get_user_meta($user_id, $val, true);
        				if ( empty($value) ) {
        					$error = true;
        				} else {
        					$payouts_details[$val] = $value;
        				}
    				}
    				$payout_method_error = $error;
				}
				break;
		}
		$payouts_details = apply_filters('wp-freeio-save-withdraw-payout-details', $payouts_details, $payout_method, $user_id);
		$payout_method_error = apply_filters('wp-freeio-save-withdraw-payout-method-error', $payout_method_error, $payout_method, $user_id);

		if ( $payout_method_error ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please update the payout settings for the selected payment gateway in payout settings', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		if ( WP_Freeio_User::is_freelancer($user_id) ) {
			$post_user_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
			$title = get_the_title($post_user_id);
		} elseif ( WP_Freeio_User::is_employer($user_id) ) {
			$post_user_id = WP_Freeio_User::get_employer_by_user_id($user_id);
			$title = get_the_title($post_user_id);
		} else {
			$user = get_userdata( $user_id );
			$title = $user->display_name;
		}
		$withdraw_post = array(
			'post_status'   => 'pending',
			'post_title'    => wp_strip_all_tags( $title ).'-'.$amount,
			'post_author'   => $user_id,
			'post_type'     => 'withdraw',
		);
		$withdraw_id = wp_insert_post( $withdraw_post );
		if ( $withdraw_id ) {
			update_post_meta($withdraw_id, WP_FREEIO_WITHDRAW_PREFIX.'amount', $amount);
        	update_post_meta($withdraw_id, WP_FREEIO_WITHDRAW_PREFIX.'payout_method', $payout_method);
        	update_post_meta($withdraw_id, WP_FREEIO_WITHDRAW_PREFIX.'payouts_details', $payouts_details);

        	// send email
        	if ( wp_freeio_get_option('admin_notice_user_withdraw_money') ) {
	        	$email_subject = WP_Freeio_Email::render_email_vars(array('amount' => WP_Freeio_Price::format_price_without_html($amount), 'freelancer_name' => $title), 'admin_notice_freelancer_withdraw', 'subject');

	        	$payout_method_val = $payout_method;
	        	$all_payout_methods = WP_Freeio_Mixes::get_default_withdraw_payout_methods();
				if ( !empty($all_payout_methods[$payout_method]) ) {
					$payout_method_val = $all_payout_methods[$payout_method];
				}

				ob_start();
				if ( $payouts_details ) {
					?>
					<ul class="payout-details-wrapper">
						<?php
						$bank_transfer_fields = wp_freeio_get_option('bank_transfer_fields', array('bank_account_name', 'bank_account_number', 'bank_name', 'bank_routing_number', 'bank_iban', 'bank_bic_swift'));
						foreach ($payouts_details as $key => $value) {
							
							switch ($key) {
								case 'payout_method':
									$all_payout_methods = WP_Freeio_Mixes::get_default_withdraw_payout_methods();
									$title = esc_html__('Payout Method', 'wp-freeio');
									$val = isset($all_payout_methods[$value]) ? $all_payout_methods[$value] : $value;
									break;
								case 'paypal_email':
									$title = esc_html__('Paypal Email', 'wp-freeio');
									$val = $value;
									break;
								case 'payoneer_email':
									$title = esc_html__('Payoneer Email', 'wp-freeio');
									$val = $value;
									break;
								default:
									$title = isset($bank_transfer_fields[$key]) ? $bank_transfer_fields[$key] : $key;
									$val = $value;
									break;
							}
							?>
							<li class="item">
								<span class="text"><?php echo esc_html($title); ?>:</span>
								<strong class="value"><?php echo esc_html($val); ?></strong>
							</li>
							<?php
						}
						?>
					</ul>
					<?php
				}
				$payouts_details_val = ob_get_clean();

		        $email_content_args = array(
		        	'amount' => WP_Freeio_Price::format_price($amount),
		        	'freelancer_name' => $title,
		        	'job_title' => $post->post_title,
		        	'payout_method' => $payout_method_val,
		        	'payouts_details' => $payouts_details_val,
		        );
		        $email_content_args = apply_filters('wp-freeio-email-content-args-email-admin-withdraw', $email_content_args);

		        $email_content = WP_Freeio_Email::render_email_vars( $email_content_args, 'admin_notice_freelancer_withdraw', 'content');
				
				$admin_email = get_option( 'admin_email', false );
		        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $admin_email);
		        
				$result = WP_Freeio_Email::wp_mail( $admin_email, $email_subject, $email_content, $headers );
			}


			$return = array( 'status' => true, 'msg' => esc_html__('Your withdrawal request has been submitted. We will process your withdrawal request', 'wp-freeio') );
			wp_send_json( $return );
		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('Error', 'wp-freeio') );
			wp_send_json( $return );
		}
	}

	public static function process_download_file() {
	    $attachment_id = isset($_GET['file_id']) ? $_GET['file_id'] : '';
	    $attachment_id = absint($attachment_id);

	    $error_page_url = home_url('/404-error');

	    if ( $attachment_id > 0 ) {

	        $file_post = get_post($attachment_id);
	        $file_path = get_attached_file($attachment_id);

	        if ( !$file_post || !$file_path || !file_exists($file_path) ) {
	            wp_redirect($error_page_url);
	        } else {
	            
	            header('Content-Description: File Transfer');
	            header("Expires: 0");
				header("Cache-Control: no-cache, no-store, must-revalidate"); 
				header('Cache-Control: pre-check=0, post-check=0, max-age=0', false); 
				header("Pragma: no-cache");	
				header("Content-type: " . $file_post->post_mime_type);
				header('Content-Disposition:attachment; filename="'. basename($file_path) .'"');
				header("Content-Type: application/force-download");
				header('Content-Length: ' . @filesize($file_path));

	            @readfile($file_path);
	            exit;
	        }
	    } else {
	        wp_redirect($error_page_url);
	    }

	    die;
	}

	public static function process_download_proposal_attachment() {
		$user_id = WP_Freeio_User::get_user_id();
	    $attachment_id = isset($_GET['file_id']) ? $_GET['file_id'] : '';
	    $post_id = isset($_GET['post_id']) ? $_GET['post_id'] : '';
	    $type = isset($_GET['type']) ? $_GET['type'] : '';
	    $attachment_id = absint($attachment_id);

	    $error_page_url = home_url('/404-error');

	    if ( $attachment_id > 0 && $post_id > 0 && ($type == 'project' || $type == 'service') ) {
	    	if ( $type == 'project' ) {

		    	$project_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
		    	$project_user_id = get_post_field('post_author', $project_id);
				$freelancer_user_id = get_post_field('post_author', $post_id);
				
				if ( WP_Freeio_User::is_employer($user_id) ) {

					if ( $user_id != $project_user_id ) {
						wp_redirect($error_page_url);
					}
				} else {
					if ( $user_id != $freelancer_user_id ) {
						wp_redirect($error_page_url);
					}
				}

				// $messages_attachments = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'messages_attachments', true);
				// if ( empty($messages_attachments[$attachment_id]) ) {
				// 	wp_redirect($error_page_url);
				// }
			} else {
				$service_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
		    	$service_user_id = get_post_field('post_author', $service_id);
				$employer_user_id = get_post_field('post_author', $post_id);
				
				if ( WP_Freeio_User::is_employer($user_id) ) {
					if ( $user_id != $employer_user_id ) {
						wp_redirect($error_page_url);
					}
				} else {
					if ( $user_id != $service_user_id ) {
						wp_redirect($error_page_url);
					}
				}

				// $messages_attachments = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX . 'messages_attachments', true);
				// if ( empty($messages_attachments[$attachment_id]) ) {
				// 	wp_redirect($error_page_url);
				// }
			}

			

	        $file_post = get_post($attachment_id);
	        $file_path = get_attached_file($attachment_id);
	        
	        if ( !$file_post || !$file_path || !file_exists($file_path) ) {
	            wp_redirect($error_page_url);
	        } else {
	            
	            header('Content-Description: File Transfer');
	            header("Expires: 0");
				header("Cache-Control: no-cache, no-store, must-revalidate"); 
				header('Cache-Control: pre-check=0, post-check=0, max-age=0', false); 
				header("Pragma: no-cache");	
				header("Content-type: " . $file_post->post_mime_type);
				header('Content-Disposition:attachment; filename="'. basename($file_path) .'"');
				header("Content-Type: application/force-download");
				header('Content-Length: ' . @filesize($file_path));

	            @readfile($file_path);
	            exit;
	        }
	    } else {
	        wp_redirect($error_page_url);
	    }

	    die;
	}

	public static function check_user_can_download_cv($attachment_id) {
		if ( $attachment_id > 0 && is_user_logged_in() ) {

	        $file_post = get_post($attachment_id);
	        $file_path = get_attached_file($attachment_id);

	        if ( $file_post && $file_path && file_exists($file_path) ) {

	            $attch_parnt = get_post_ancestors($attachment_id);
	            if (isset($attch_parnt[0])) {
	                $attch_parnt = $attch_parnt[0];
	            }
	            
	            $error = true;

	            $user_id = WP_Freeio_User::get_user_id();
	            $cur_user_obj = wp_get_current_user();

	            if ( WP_Freeio_User::is_employer($user_id) ) {
	                $error = false;
	            }

	            if ( WP_Freeio_User::is_freelancer($user_id) ) {
	                $user_cand_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
	                if ($user_cand_id == $attch_parnt) {
	                    $error = false;
	                }
	            }

	            if ( in_array('administrator', (array) $cur_user_obj->roles) ) {
	                $error = false;
	            }

	            if ( !$error ) {
	                return true;
	            }
	            
	        }
	    }
	    return false;
	}

	public static function process_download_cv() {
	    $attachment_id = isset($_GET['file_id']) ? $_GET['file_id'] : '';
	    $attachment_id = absint($attachment_id);

	    $error_page_url = home_url('/404-error');

	    if ( $attachment_id > 0 ) {

	        $file_post = get_post($attachment_id);
	        $file_path = get_attached_file($attachment_id);

	        if ( !$file_post || !$file_path || !file_exists($file_path) ) {
	            wp_redirect($error_page_url);
	        } else {

	            $attch_parnt = get_post_ancestors($attachment_id);
	            if (isset($attch_parnt[0])) {
	                $attch_parnt = $attch_parnt[0];
	            }
	            
	            $error = true;

	            $download_key = isset($_GET['download_key']) ? $_GET['download_key'] : '';

	            if ( !empty($download_key) && $download_key == base64_encode('download-file-'.$attachment_id) ) {
	            	$error = false;
	            } else {
		            if (!is_user_logged_in() && !apply_filters('wp-freeio-loggedin-user-download', false)) {
		                wp_redirect($error_page_url);
		                exit;
		            }
		            $user_id = WP_Freeio_User::get_user_id();
		            $cur_user_obj = wp_get_current_user();

		            if ( WP_Freeio_User::is_employer($user_id) ) {
		                $error = false;
		            }

		            if ( WP_Freeio_User::is_freelancer($user_id) ) {
		                $user_cand_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
		                if ($user_cand_id == $attch_parnt) {
		                    $error = false;
		                }
		            }

		            if ( in_array('administrator', (array) $cur_user_obj->roles) ) {
		                $error = false;
		            }

		            $error = apply_filters('wp-freeio-download-cv-check', $error, $file_post);
	            }

	            if ( $error ) {
	                wp_redirect($error_page_url);
	                exit;
	            }
	            
	            header('Content-Description: File Transfer');
	            header("Expires: 0");
				header("Cache-Control: no-cache, no-store, must-revalidate"); 
				header('Cache-Control: pre-check=0, post-check=0, max-age=0', false); 
				header("Pragma: no-cache");	
				header("Content-type: " . $file_post->post_mime_type);
				header('Content-Disposition:attachment; filename="'. basename($file_path) .'"');
				header("Content-Type: application/force-download");
				header('Content-Length: ' . @filesize($file_path));

	            @readfile($file_path);
	            exit;
	        }
	    } else {
	        wp_redirect($error_page_url);
	    }

	    die;
	}

	public static function process_download_resume_cv() {
		$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : '';
	    $post_id = absint($post_id);

	    $error_page_url = home_url('/404-error');

	    if ( $post_id > 0 ) {

	        $resume_post = get_post($post_id);

	        if ( !$resume_post ) {
	            wp_redirect($error_page_url);
	        } else {

	            $error = true;
	            if (!is_user_logged_in() && !apply_filters('wp-freeio-loggedin-user-download', false)) {
	                wp_redirect($error_page_url);
	                exit;
	            }
	            $user_id = WP_Freeio_User::get_user_id();
	            $cur_user_obj = wp_get_current_user();

	            if ( WP_Freeio_User::is_employer($user_id) ) {
	                $error = false;
	            } elseif ( WP_Freeio_User::is_freelancer($user_id) ) {
					$freelancer_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
					if ( $post_id == $freelancer_post_id ) {
						$error = false;
					}
	            }

	            if ( in_array('administrator', (array) $cur_user_obj->roles) ) {
	                $error = false;
	            }

	            $error = apply_filters('wp-freeio-download-resume-cv-check', $error, $resume_post);

	            $file_path = WP_Freeio_Mpdf::mpdf_exec($resume_post);
	            if ( empty($file_path) ) {
	            	$error = false;
	            }

	            if ( $error ) {
	                wp_redirect($error_page_url);
	                exit;
	            }

	            header('Content-Description: File Transfer');
	            header("Expires: 0");
				header("Cache-Control: no-cache, no-store, must-revalidate"); 
				header('Cache-Control: pre-check=0, post-check=0, max-age=0', false); 
				header("Pragma: no-cache");	
				header("Content-type: application/pdf");
				header('Content-Disposition:attachment; filename="'. basename($file_path) .'"');
				header("Content-Type: application/force-download");
				header('Content-Length: ' . @filesize($file_path));

	            @readfile($file_path);
	            exit;
	        }
	    } else {
	        wp_redirect($error_page_url);
	    }

	    die;
	}

	public static function freelancer_only_applicants($post) {
		$return = false;
		if ( is_user_logged_in() ) {
			$user_id = WP_Freeio_User::get_user_id();
			if ( WP_Freeio_User::is_employer($user_id) ) {
				$query_vars = array(
					'post_type'     => 'job_listing',
					'post_status'   => 'publish',
					'paged'         => 1,
					'author'        => $user_id,
					'fields' => 'ids',
					'posts_per_page'    => -1,
				);
				$jobs = WP_Freeio_Query::get_posts($query_vars);
				if ( !empty($jobs) && !empty($jobs->posts) ) {
					$query_vars = array(
					    'post_type' => 'job_applicant',
					    'posts_per_page'    => -1,
					    'paged'    			=> 1,
					    'post_status' => 'publish',
					    'fields' => 'ids',
					    'meta_query' => array(
					    	array(
						    	'key' => WP_FREEIO_APPLICANT_PREFIX . 'freelancer_id',
						    	'value' => $post->ID,
						    	'compare' => '=',
						    ),
						    array(
						    	'key' => WP_FREEIO_APPLICANT_PREFIX . 'job_id',
						    	'value' => $jobs->posts,
						    	'compare' => 'IN',
						    ),
						)
					);

					$applicants = WP_Freeio_Query::get_posts($query_vars);
					if ( !empty($applicants) && !empty($applicants->posts) ) {
						$return = true;
					}
				}
			}
		}
		return $return;
	}

	// check view
	public static function check_view_freelancer_detail() {
		global $post;
		$restrict_type = wp_freeio_get_option('freelancer_restrict_type', '');
		$view = wp_freeio_get_option('freelancer_restrict_detail', 'all');
		
		$return = true;
		if ( $restrict_type == 'view' ) {
			$author_id = WP_Freeio_User::get_user_by_freelancer_id($post->ID);
			if ( get_current_user_id() == $author_id ) {
				$return = true;
			} else {
				switch ($view) {
					case 'register_user':
						$return = false;
						if ( is_user_logged_in() ) {
							$show_profile = self::get_post_meta($post->ID, 'show_profile');
							if ( empty($show_profile) || $show_profile == 'show' ) {
								$return = true;
							}
						}
						break;
					case 'register_employer':
						$return = false;
						if ( is_user_logged_in() ) {
							$user_id = WP_Freeio_User::get_user_id();
							if ( WP_Freeio_User::is_employer($user_id) ) {
								$show_profile = self::get_post_meta($post->ID, 'show_profile');
								if ( empty($show_profile) || $show_profile == 'show' ) {
									$return = true;
								}
							}
						}
						break;
					case 'only_applicants':
						$return = self::freelancer_only_applicants($post);
						break;
					default:
						$return = false;
						$show_profile = self::get_post_meta($post->ID, 'show_profile');
						if ( empty($show_profile) || $show_profile == 'show' ) {
							$return = true;
						}
						break;
				}
			}

		} else {
			$return = self::freelancer_only_applicants($post);
			if ( !$return ) {
				$show_profile = self::get_post_meta($post->ID, 'show_profile');
				if ( empty($show_profile) || $show_profile == 'show' ) {
					$return = true;
				}
			}
		}

		return apply_filters('wp-freeio-check-view-freelancer-detail', $return, $post);
	}

	public static function freelancer_restrict_listing_query($query, $filter_params) {
		$query_vars = $query->query_vars;
		$query_vars = self::freelancer_restrict_listing_query_args($query_vars, $filter_params);
		$query->query_vars = $query_vars;
		
		return apply_filters('wp-freeio-check-view-freelancer-listing-query', $query);
	}

	public static function freelancer_restrict_listing_query_args($query_args, $filter_params) {
		$restrict_type = wp_freeio_get_option('freelancer_restrict_type', '');
		if ( $restrict_type == 'view' ) {
			$view = wp_freeio_get_option('freelancer_restrict_listing', 'all');
			
			switch ($view) {
				case 'register_user':
					if ( !is_user_logged_in() ) {
						$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
						$meta_query[] = array(
							'key'       => 'freelancer_restrict_listing',
							'value'     => 'register_user',
							'compare'   => '==',
						);
						$query_args['meta_query'] = $meta_query;
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
					if ( !$return ) {
						$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
						$meta_query[] = array(
							'key'       => 'freelancer_restrict_listing',
							'value'     => 'register_employer',
							'compare'   => '==',
						);
						$query_args['meta_query'] = $meta_query;
					}
					break;
				case 'only_applicants':
					$ids = array(0);
					if ( is_user_logged_in() ) {
						$user_id = WP_Freeio_User::get_user_id();

						$applicants = WP_Freeio_Applicant::get_all_applicants_by_employer($user_id);
						foreach ($applicants as $applicant_id) {
							$freelancer_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX.'freelancer_id', true );
							if ( $freelancer_id ) {
								$return[] = $freelancer_id;
							}
						}
					}
					if ( !empty($return) ) {
						$post__in = !empty($query_args['post__in']) ? $query_args['post__in'] : array();
						if ( !empty($post__in) ) {
							$ids = array_intersect($return, $post__in);
						} else {
							$ids = $return;
						}
						$ids[] = 0;
					}
					$query_args['post__in'] = $ids;
					break;
			}
		}

		// show/hide profile
		$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
		$meta_query[] = array(
			'key'       => WP_FREEIO_FREELANCER_PREFIX.'show_profile',
			'value'     => 'hide',
			'compare'   => '!=',
		);
		$query_args['meta_query'] = $meta_query;
		
		return apply_filters('wp-freeio-check-view-freelancer-listing-query-args', $query_args);
	}

	public static function check_restrict_view_contact_info($post) {
		$return = true;
		$restrict_type = wp_freeio_get_option('freelancer_restrict_type', '');
		if ( $restrict_type == 'view_contact_info' ) {
			$view = wp_freeio_get_option('freelancer_restrict_contact_info', 'all');

			$author_id = WP_Freeio_User::get_user_by_freelancer_id($post->ID);
			if ( get_current_user_id() == $author_id ) {
				$return = true;
			} else {
				switch ($view) {
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
					case 'only_applicants':
						$return = self::freelancer_only_applicants($post);
						break;
					default:
						$return = true;
						break;
				}
			}
		}
		return apply_filters('wp-freeio-check-view-freelancer-contact-info', $return, $post);
	}

	public static function check_restrict_review($post) {
		$return = true;
		
		$view = wp_freeio_get_option('freelancers_restrict_review', 'all');
		
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
			case 'only_applicants':
				$return = self::freelancer_only_applicants($post);
				break;
			default:
				$return = true;
				break;
		}

		return apply_filters('wp-freeio-check-restrict-freelancer-review', $return, $post);
	}

	public static function display_freelancers_results_filters() {
		$filters = WP_Freeio_Abstract_Filter::get_filters();

		echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/results-filters', array('filters' => $filters));
	}

	public static function display_freelancers_count_results($wp_query) {
		$total = $wp_query->found_posts;
		$per_page = $wp_query->query_vars['posts_per_page'];
		$current = max( 1, $wp_query->get( 'paged', 1 ) );
		$args = array(
			'total' => $total,
			'per_page' => $per_page,
			'current' => $current,
		);
		echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/results-count', $args);
	}

	public static function display_freelancers_alert_form() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/freelancers-alert-form');
	}

	public static function display_freelancers_orderby() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/orderby');
	}

	public static function display_freelancers_alert_orderby_start() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/alert-orderby-start');
	}

	public static function display_freelancers_alert_orderby_end() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/alert-orderby-end');
	}

	public static function get_display_email($post) {
		if ( is_object($post) ) {
			$post_id = $post->ID;
		} else {
			$post_id = $post;
			$post = get_post($post_id);
		}
		$email = '';
		if ( self::check_restrict_view_contact_info($post) || wp_freeio_get_option('restrict_contact_freelancer_email', 'on') !== 'on' ) {
			$email = self::get_post_meta( $post_id, 'email', true );
		}
		return apply_filters('wp-freeio-get-display-freelancer-email', $email, $post_id);
	}

	public static function get_display_phone($post) {
		if ( is_object($post) ) {
			$post_id = $post->ID;
		} else {
			$post_id = $post;
			$post = get_post($post_id);
		}
		$phone = '';
		if ( self::check_restrict_view_contact_info($post) || wp_freeio_get_option('restrict_contact_freelancer_phone', 'on') !== 'on' ) {
			$phone = self::get_post_meta( $post_id, 'phone', true );
		}
		return apply_filters('wp-freeio-get-display-freelancer-phone', $phone, $post_id);
	}

	public static function get_display_cv_download($post) {
		if ( is_object($post) ) {
			$post_id = $post->ID;
		} else {
			$post_id = $post;
			$post = get_post($post_id);
		}
		$cv_attachment = '';
		if ( self::check_restrict_view_contact_info($post) || wp_freeio_get_option('restrict_contact_freelancer_download_cv', 'on') !== 'on' ) {
			$cv_attachment = self::get_post_meta( $post_id, 'cv_attachment', true );
		}
		return apply_filters('wp-freeio-get-display-freelancer-cv_attachment', $cv_attachment, $post_id);
	}

	public static function restrict_freelancer_listing_information($query) {
		$restrict_type = wp_freeio_get_option('freelancer_restrict_type', '');
		if ( $restrict_type == 'view' ) {
			$view =  wp_freeio_get_option('freelancer_restrict_listing', 'all');
			$output = '';
			switch ($view) {
				case 'register_user':
					if ( !is_user_logged_in() ) {
						$output = '
						<div class="freelancer-listing-info">
							<h2 class="restrict-title">'.__( 'The page is restricted only for register user.', 'wp-freeio' ).'</h2>
							<div class="restrict-content">'.__( 'You need login to view this page', 'wp-freeio' ).'</div>
						</div>';
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
					if ( !$return ) {
						$output = '<div class="freelancer-listing-info"><h2 class="restrict-title">'.__( 'The page is restricted only for employers.', 'wp-freeio' ).'</h2></div>';
					}
					break;
				case 'only_applicants':
					$return = array();
					if ( is_user_logged_in() ) {
						$user_id = WP_Freeio_User::get_user_id();

						$applicants = WP_Freeio_Applicant::get_all_applicants_by_employer($user_id);
						if ( !empty($applicants) ) {
							foreach ($applicants as $applicant_id) {
								$freelancer_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX.'freelancer_id', true );
								if ( $freelancer_id ) {
									$return[] = $freelancer_id;
								}
							}
						}
					}
					if ( empty($return) ) {
						$output = '<div class="freelancer-listing-info"><h2 class="restrict-title">'.__( 'The page is restricted only for employers view his applicants.', 'wp-freeio' ).'</h2></div>';
					}
					break;
				default:
					$output = apply_filters('wp-freeio-restrict-freelancer-listing-default-information', '', $query);
					break;
			}

			echo apply_filters('wp-freeio-restrict-freelancer-listing-information', $output, $query);
		}
	}

	public static function freelancer_feed_url($values = null, $exclude = array(), $current_key = '', $page_rss_url = '', $return = false) {
		if ( empty($page_rss_url) ) {
			$page_rss_url = home_url('/') . '?feed=freelancer_listing_feed';
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
				$page_rss_url = self::freelancer_feed_url( $value, $exclude, $key, $page_rss_url, true );
			} else {
				$page_rss_url = add_query_arg($key, wp_unslash( $value ), $page_rss_url);
			}
		}

		if ( $return ) {
			return $page_rss_url;
		}

		echo $page_rss_url;
	}

	public static function display_freelancer_feed(){
		echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/freelancers-rss-btn');
	}
}

WP_Freeio_Freelancer::init();