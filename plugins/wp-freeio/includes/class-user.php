<?php
/**
 * User
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_User {
	
	public static function init() {
		add_action( 'init', array( __CLASS__, 'add_user_roles' ) );
		// add_action( 'init', array( __CLASS__, 'role_caps' ) );
		
		// Ajax endpoints.
		add_action( 'wpfi_ajax_wp_freeio_ajax_login',  array( __CLASS__, 'process_login' ) );
		add_action( 'wpfi_ajax_wp_freeio_ajax_forgotpass',  array( __CLASS__, 'process_forgot_password' ) );
		add_action( 'wpfi_ajax_wp_freeio_ajax_register',  array( __CLASS__, 'process_register' ) );

		add_action( 'wpfi_ajax_wp_freeio_ajax_registernew',  array( __CLASS__, 'process_register_new' ) );

		add_action( 'wpfi_ajax_wp_freeio_ajax_change_password',  array(__CLASS__,'process_change_password') );

		// delete profile
		add_action( 'wp_ajax_wp_freeio_ajax_delete_profile',  array(__CLASS__,'process_delete_profile') );

		// resend approve account
		add_action( 'wpfi_ajax_wp_freeio_ajax_resend_approve_account',  array(__CLASS__,'process_resend_approve_account') );

		
		add_action( 'delete_user', array(__CLASS__,'process_delete_user'), 10, 2 );

		add_action( 'user_register', array( __CLASS__, 'registration_save' ), 10, 1 );
		add_action( 'cmb2_after_init', array( __CLASS__, 'process_change_profile' ) );
		add_action( 'cmb2_after_init', array( __CLASS__, 'process_change_resume' ) );

		add_filter( 'wp_authenticate_user', array( __CLASS__, 'admin_user_auth_callback' ), 11, 2 );

		// action
		add_action( 'load-users.php', array( __CLASS__, 'process_update_user_action' ) );
		add_filter( 'wp_freeio_new_user_approve_validate_status_update', array( __CLASS__, 'validate_status_update' ), 10, 3 );

		add_action( 'wp_freeio_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
		add_action( 'wp_freeio_new_user_approve_deny_user', array( __CLASS__, 'deny_user' ) );

		// Filters
		add_filter( 'user_row_actions', array( __CLASS__, 'user_table_actions' ), 10, 2 );
		add_filter( 'manage_users_columns', array( __CLASS__, 'add_column' ) );
		add_filter( 'manage_users_custom_column', array( __CLASS__, 'status_column' ), 10, 3 );

		add_action( 'restrict_manage_users', array( __CLASS__, 'status_filter' ), 10, 1 );
		add_action( 'pre_get_users', array( __CLASS__, 'filter_by_status' ) );

		// approve user
		add_action( 'wp', array( __CLASS__, 'process_approve_user' ) );

		add_action( 'save_post', array( __CLASS__, 'auto_generate_user' ), 100, 3 );

		add_action( 'wpfi_ajax_wp_freeio_ajax_switch_user_role',  array( __CLASS__, 'switch_user_role' ) );
	}

	public static function add_user_roles() {
	    add_role(
            'wp_freeio_freelancer', esc_html__('Freelancer', 'wp-freeio'), array(
		        'read' => true,
		        'edit_posts' => false,
		        'delete_posts' => false,
		        'upload_files' => true,
            )
	    );
	    add_role(
            'wp_freeio_employer', esc_html__('Employer', 'wp-freeio'), array(
		        'read' => true,
		        'edit_posts' => false,
		        'delete_posts' => false,
		        'upload_files' => true,
            )
	    );
	    add_role(
            'wp_freeio_employee', esc_html__('Employee', 'wp-freeio'), array(
		        'read' => true,
		        'edit_posts' => false,
		        'delete_posts' => false,
		        'upload_files' => true,
            )
	    );
	}

	public static function role_caps() {
	    if ( current_user_can('wp_freeio_freelancer') ) {
	        $role = get_role('wp_freeio_freelancer');
		    $role->add_cap('upload_files');
		    $role->add_cap('edit_post');
		    $role->add_cap('edit_published_pages');
		    $role->add_cap('edit_others_pages');
		    $role->add_cap('edit_others_posts');
	    }
	    
	    if ( current_user_can('wp_freeio_employer') ) {
		    $role = get_role('wp_freeio_employer');
		    $role->add_cap('upload_files');
		    $role->add_cap('edit_post');
		    $role->add_cap('edit_published_pages');
		    $role->add_cap('edit_others_pages');
		    $role->add_cap('edit_others_posts');
	    }
	}

	public static function is_employer_can_add_submission($user_id = null) {
		if ( empty($user_id) ) {
			$user_id = get_current_user_id();
		}
		$return = false;
		if ( self::is_employer($user_id) ) {
			$return = true;
		} elseif ( self::is_employee($user_id) ) {
			$return = self::is_employee_can_add_submission($user_id);
		}
    	
		return apply_filters( 'wp-freeio-is-employer-can-add-submission', $return, $user_id );
	}

	public static function is_employee_can_add_submission($employee_id) {
		$return = false;
		if ( wp_freeio_get_option('employee_submit_job') == 'on' ) {
			$employer_id = WP_Freeio_User::get_employer_by_employee_id($employee_id);
			if ( !empty($employer_id) ) {
				$user_id = WP_Freeio_User::get_user_by_employer_id($employer_id);

				if ( !empty($user_id) ) {
					$return = self::is_employer($user_id);
				}
			}
		}
    	
		return apply_filters( 'wp-freeio-is-employee-can-add-submission', $return, $employee_id );
	}

	public static function is_employer_can_edit_job($job_id) {
		$return = true;
		if ( ! $job_id ) {
			$return = false;
		} elseif ( !is_user_logged_in() ) {
			$return = false;
		} else {
			$user_id = get_current_user_id();
			if ( self::is_employer($user_id) ) {
				$job = get_post( $job_id );
				if ( !$job ) {
					$return = false;
				} else {
					$author_id = WP_Freeio_Job_Listing::get_author_id($job->ID);
					if ( absint( $author_id ) !== get_current_user_id() ) {
						$return = false;
					}
				}
			} elseif ( self::is_employee($user_id) ) {

				$return = self::is_employee_can_edit_job($job_id, $user_id);
			}
		}

		return apply_filters( 'wp-freeio-is-employer-can-edit-job', $return, $job_id );
	}

	public static function is_employee_can_edit_job($job_id, $employee_id) {
		$return = false;
		if ( wp_freeio_get_option('employee_edit_job') == 'on' ) {
			$employer_id = WP_Freeio_User::get_employer_by_employee_id($employee_id);

			if ( $job_id ) {
				$job = get_post( $job_id );
				if ( !empty($employer_id) ) {
					$user_id = WP_Freeio_User::get_user_by_employer_id($employer_id);

					if ( $job && absint( $job->post_author ) == $user_id ) {
						$return = true;
					}
				}
			}
		}
		
		return apply_filters( 'wp-freeio-is-employee-can-edit-job', $return, $job_id );
	}

	public static function is_employer_can_edit_project($project_id) {
		$return = true;
		if ( ! $project_id ) {
			$return = false;
		} elseif ( !is_user_logged_in() ) {
			$return = false;
		} else {
			$user_id = get_current_user_id();
			if ( self::is_employer($user_id) ) {
				$project = get_post( $project_id );
				if ( !$project ) {
					$return = false;
				} else {
					$author_id = WP_Freeio_Project::get_author_id($project->ID);
					if ( absint( $author_id ) !== get_current_user_id() ) {
						$return = false;
					}
				}
			} elseif ( self::is_employee($user_id) ) {

				$return = self::is_employee_can_edit_project($project_id, $user_id);
			}
		}

		return apply_filters( 'wp-freeio-is-employer-can-edit-project', $return, $project_id );
	}

	public static function is_employee_can_edit_project($project_id, $employee_id) {
		$return = false;
		if ( wp_freeio_get_option('employee_edit_project') == 'on' ) {
			$employer_id = WP_Freeio_User::get_employer_by_employee_id($employee_id);

			if ( $project_id ) {
				$project = get_post( $project_id );
				if ( !empty($employer_id) ) {
					$user_id = WP_Freeio_User::get_user_by_employer_id($employer_id);

					if ( $project && absint( $project->post_author ) == $user_id ) {
						$return = true;
					}
				}
			}
		}
		
		return apply_filters( 'wp-freeio-is-employee-can-edit-project', $return, $project_id );
	}

	public static function is_employer($user_id = 0, $active = true) {
		
		if ( empty($user_id) ) {
			if ( !is_user_logged_in() ) {
				return false;
			}
	        $user_id = get_current_user_id();
	    }
	    $user_meta = get_userdata($user_id);

	    if ( !empty($user_meta) && in_array('wp_freeio_employer', $user_meta->roles) ) {
		    $employer_id = get_user_meta($user_id, 'employer_id', true);
		    $employer_id = $employer_id > 0 ? $employer_id : 0;
		    if ($employer_id > 0) {
		    	$employer_id = WP_Freeio_Mixes::get_lang_post_id($employer_id, 'employer');

		        $post = get_post($employer_id);
		        if ( !empty($post->ID) ) {
		            return true;
		        }
		    }
	    }
	    return false;
	}

	public static function is_freelancer($user_id = 0, $active = true) {
		
	    if ( empty($user_id) ) {
	    	if ( !is_user_logged_in() ) {
				return false;
			}
	        $user_id = get_current_user_id();
	    }
	    $user_meta = get_userdata($user_id);
	    if ( !empty($user_meta) && in_array('wp_freeio_freelancer', $user_meta->roles) ) {
		    $freelancer_id = get_user_meta($user_id, 'freelancer_id', true);
		    $freelancer_id = $freelancer_id > 0 ? $freelancer_id : 0;
		    if ($freelancer_id > 0) {
		    	$freelancer_id = WP_Freeio_Mixes::get_lang_post_id($freelancer_id, 'freelancer');

		        $post = get_post($freelancer_id);
		        if ( !empty($post->ID) ) {
		            return true;
		        }
		    }
	    }
	    return false;
	}

	public static function is_freelancer_can_add_submission($user_id = null) {
		if ( empty($user_id) ) {
			$user_id = get_current_user_id();
		}
		$return = false;
		if ( self::is_freelancer($user_id) ) {
			$return = true;
		}
    	
		return apply_filters( 'wp-freeio-is-freelancer-can-add-submission', $return, $user_id );
	}

	public static function is_freelancer_can_edit_service($service_id) {
		$return = true;
		if ( ! $service_id ) {
			$return = false;
		} elseif ( !is_user_logged_in() ) {
			$return = false;
		} else {
			$user_id = get_current_user_id();
			if ( self::is_freelancer($user_id) ) {
				$job = get_post( $service_id );
				if ( !$job ) {
					$return = false;
				} else {
					$author_id = WP_Freeio_Service::get_author_id($job->ID);
					if ( absint( $author_id ) !== get_current_user_id() ) {
						$return = false;
					}
				}
			}
		}

		return apply_filters( 'wp-freeio-is-freelancer-can-edit-service', $return, $service_id );
	}

	public static function is_employee($user_id = 0) {
	    if ( empty($user_id) && is_user_logged_in() ) {
	        $user_id = get_current_user_id();
	    }
	    $user_meta = get_userdata($user_id);
	    if (!empty($user_meta->roles) && in_array('wp_freeio_employee', $user_meta->roles)) {
	    	return true;
	    }

	    return false;
	}

	public static function get_user_id($user_id = 0) {
		if ( empty($user_id) && is_user_logged_in() ) {
	        $user_id = get_current_user_id();
	    }
	    $return = $user_id;
	    if ( self::is_employee($user_id) ) {
	    	$employer_id = self::get_employer_by_employee_id($user_id);
	    	$return = WP_Freeio_User::get_user_by_employer_id($employer_id);
	    }
	    return $return;
	}

	public static function get_user_by_employer_id($employer_id = 0) {
	    $user_id = get_post_meta($employer_id, WP_FREEIO_EMPLOYER_PREFIX . 'user_id', true);
	    $user_id = $user_id > 0 ? $user_id : 0;
	    $wp_user = get_user_by('ID', $user_id);
	    if ($wp_user) {
	        return $wp_user->ID;
	    }
	    return false;
	}

	public static function get_employer_by_user_id($user_id = 0) {
		global $sitepress;
	    $employer_id = get_user_meta($user_id, 'employer_id', true);
	    $employer_id = $employer_id > 0 ? $employer_id : 0;
	    if ($employer_id > 0) {
	    	$employer_id = WP_Freeio_Mixes::get_lang_post_id($employer_id, 'employer');

	        $post = get_post($employer_id);
	        if ( !empty($post->ID) ) {
	            return $post->ID;
	        }
	    }
	    return false;
	}

	public static function get_employer_by_employee_id($employee_user_id = 0) {
		global $sitepress;
	    $employer_ids = WP_Freeio_Query::get_employee_employers($employee_user_id, array('post_per_page' => 1));
	    if ( !empty($employer_ids) ) {

		    $employer_id = $employer_ids[0];
		    
	    	$employer_id = WP_Freeio_Mixes::get_lang_post_id($employer_id, 'employer');
	        $post = get_post($employer_id);
	        if ( !empty($post->ID) ) {
	            return $post->ID;
	        }
	    }
	    return false;
	}

	public static function get_user_by_freelancer_id($freelancer_id = 0) {
	    $user_id = get_post_meta($freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'user_id', true);
	    $user_id = $user_id > 0 ? $user_id : 0;
	    $wp_user = get_user_by('ID', $user_id);
	    if ($wp_user) {
	        return $wp_user->ID;
	    }
	    return false;
	}

	public static function get_freelancer_by_user_id($user_id = 0) {
		global $sitepress;
	    $freelancer_id = get_user_meta($user_id, 'freelancer_id', true);
	    $freelancer_id = $freelancer_id > 0 ? $freelancer_id : 0;
	    if ($freelancer_id > 0) {
	    	$freelancer_id = WP_Freeio_Mixes::get_lang_post_id($freelancer_id, 'freelancer');

	        $post = get_post($freelancer_id);
	        if ( !empty($post->ID) ) {
	            return $post->ID;
	        }
	    }
	    return false;
	}

	public static function get_author_employers() {
		global $wpdb;
		if ( false === ( $author_ids = get_transient( 'wp-freeio-get-filter-employers' ) ) ) {
			$min_posts = 1;
			$author_ids = $wpdb->get_col(
				"SELECT `post_author` FROM
			    (SELECT `post_author`, COUNT(*) AS `count` FROM {$wpdb->posts}
			        WHERE `post_type`='job_listing' AND `post_status`='publish' GROUP BY `post_author`) AS `stats`
			    WHERE `count` >= {$min_posts} ORDER BY `count` DESC;"
			);
			
			set_transient( 'wp-freeio-get-filter-employers', $author_ids );
		}

		return $author_ids;
	}
	
	public static function get_employers() {
        $args = array(
            'posts_per_page' => "-1",
            'post_type' => 'employer',
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => array(),
        );
        $loop = new WP_Query($args);
        $employers = $loop->posts;
        
        return apply_filters('wp-freeio-get-employers', $employers);
    }

    public static function get_freelancers() {
        $args = array(
            'posts_per_page' => "-1",
            'post_type' => 'freelancer',
            'post_status' => 'publish',
            'fields' => 'ids',
            'meta_query' => array(),
        );
        $loop = new WP_Query($args);
        $freelancers = $loop->posts;
        
        return apply_filters('wp-freeio-get-freelancers', $freelancers);
    }

	public static function process_login() {
   		$do_check = check_ajax_referer( 'ajax-login-nonce', 'security_login', false );
		if ( $do_check == false ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again.', 'wp-freeio') );
            wp_send_json( $return );
        }
   		if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_Freeio_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
	            $return = array( 'status' => false, 'msg' => esc_html__('Captcha is not valid.', 'wp-freeio') );
		   		wp_send_json( $return );
			}
		}

   		$info = array();
   		
   		$info['user_login'] = isset($_POST['username']) ? $_POST['username'] : '';
	    $info['user_password'] = isset($_POST['password']) ? $_POST['password'] : '';
	    $info['remember'] = isset($_POST['remember']) ? true : false;
		
		if ( empty($info['user_login']) || empty($info['user_password']) ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Please fill all form fields.', 'wp-freeio') );
		   	wp_send_json( $return );
        }

		if (filter_var($info['user_login'], FILTER_VALIDATE_EMAIL)) {
            $user_obj = get_user_by('email', $info['user_login']);
        } else {
            $user_obj = get_user_by('login', $info['user_login']);
        }
        $user_id = isset($user_obj->ID) ? $user_obj->ID : '0';
        $user_login_auth = self::get_user_status($user_id);
        if ( $user_login_auth == 'pending' && isset($user_obj->ID) ) {
            $return = array( 'status' => false, 'msg' => self::login_msg($user_obj) );
	   		wp_send_json( $return );
        } elseif ( $user_login_auth == 'denied' && isset($user_obj->ID) ) {
            $return = array( 'status' => false, 'msg' => __('Your account denied', 'wp-freeio') );
	   		wp_send_json( $return );
        }

		$user_signon = wp_signon( $info, is_ssl() );
	    if ( is_wp_error($user_signon) ){
			$result = json_encode(array('status' => false, 'msg' => esc_html__('Wrong username or password. Please try again!!!', 'wp-freeio')));
	    } else {
			wp_set_current_user($user_signon->ID); 
			$role = 'wp_freeio_freelancer';
			if ( self::is_employer($user_signon->ID) ) {
				$role = 'wp_freeio_employer';
			}
	        $result = json_encode( array('status' => true, 'msg' => esc_html__('Signin successful, redirecting...', 'wp-freeio'), 'role' => $role) );
	    }

   		echo trim($result);
   		die();
	}

	public static function process_forgot_password() {
	 	
		// First check the nonce, if it fails the function will break
		$do_check = check_ajax_referer( 'ajax-lostpassword-nonce', 'security_lostpassword', false );
		if ( $do_check == false ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again.', 'wp-freeio') );
            wp_send_json( $json );
        }
		if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_Freeio_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
				$error = esc_html__( 'Captcha is not valid', 'wp-freeio' );

				echo json_encode(array('status' => false, 'msg' => $error));
				wp_die();
			}
		}
		global $wpdb;
		
		$account = isset($_POST['user_login']) ? $_POST['user_login'] : '';
		
		if ( empty( $account ) ) {
			$error = esc_html__( 'Enter an username or e-mail address.', 'wp-freeio' );
		} else {
			if(is_email( $account )) {
				if( email_exists($account) ) {
					$get_by = 'email';
				} else {
					$error = esc_html__( 'There is no user registered with that email address.', 'wp-freeio' );			
				}
			} else if (validate_username( $account )) {
				if( username_exists($account) ) {
					$get_by = 'login';
				} else {
					$error = esc_html__( 'There is no user registered with that username.', 'wp-freeio' );				
				}
			} else {
				$error = esc_html__(  'Invalid username or e-mail address.', 'wp-freeio' );		
			}
		}	
		
		do_action('wp-freeio-process-forgot-password', $_POST);

		if ( empty($error) ) {
			if (filter_var($account, FILTER_VALIDATE_EMAIL)) {
	            $user_obj = get_user_by('email', $account);
	        } else {
	            $user_obj = get_user_by('login', $account);
	        }
	        $user_id = isset($user_obj->ID) ? $user_obj->ID : '0';
	        $user_login_auth = self::get_user_status($user_id);
	        if ( $user_login_auth == 'pending' && isset($user_obj->ID) ) {
	            echo json_encode(array(
	            	'status' => false,
	            	'msg' => self::login_msg($user_obj)
	            ));
	            die();
	        } elseif ( $user_login_auth == 'denied' && isset($user_obj->ID) ) {
	            echo json_encode(array(
	            	'status' => false,
	            	'msg' => __('Your account denied.', 'wp-freeio')
	            ));
	            die();
	        }

			$random_password = wp_generate_password();

			$user = get_user_by( $get_by, $account );
				
			$update_user = wp_update_user( array ( 'ID' => $user->ID, 'user_pass' => $random_password ) );
				
			if( $update_user ) {
				
				$from = get_option('admin_email');
				
				$email_to = $user->user_email;
				
				$subject = WP_Freeio_Email::render_email_vars( array('user_name' => $user->display_name), 'user_reset_password', 'subject');

				$email_content_args = array(
		        	'new_password' => $random_password,
		        	'user_name' => $user_name,
		        	'user_email' => $email_to,
		        );
		        $content = WP_Freeio_Email::render_email_vars( $email_content_args, 'user_reset_password', 'content');

				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $from );

				$mail = WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				
				if( $mail ) {
					$success = esc_html__( 'Check your email address for you new password.', 'wp-freeio' );
				} else {
					$error = esc_html__( 'System is unable to send you mail containg your new password.', 'wp-freeio' );						
				}
			} else {
				$error =  esc_html__( 'Oops! Something went wrong while updating your account.', 'wp-freeio' );
			}
		}
	
		if ( ! empty( $error ) ) {
			echo json_encode(array('status' => false, 'msg' => $error));
		}
				
		if ( ! empty( $success ) ) {
			echo json_encode(array('status' => true, 'msg' => $success ));	
		}
		die();
	}

	public static function process_register_new() {
		global $reg_errors;
		// check_ajax_referer( 'ajax-register-nonce', 'security_register' );
		if ( !empty($_POST['post_type'] == 'employer') ) {
			$prefix = WP_FREEIO_EMPLOYER_PREFIX;
			$role = 'wp_freeio_employer';
		} else {
			$prefix = WP_FREEIO_FREELANCER_PREFIX;
			$role = 'wp_freeio_freelancer';
		}
 

        self::registration_validation( $_POST[$prefix.'email'], $_POST[$prefix.'password'], $_POST[$prefix.'confirmpassword'],$_POST[$prefix.'phone'],$_POST[$prefix.'address'] );
        do_action( 'wp-freeio-registration-validation-after', $prefix );
        if ( 1 > count( $reg_errors->get_error_messages() ) ) {

        	$email = $_POST[$prefix.'email'];
        	//$usernames = explode('@', $email);
			$usernames = $_POST[$prefix.'username'];

			$username = sanitize_user( str_replace(' ', '_', strtolower($usernames)) );
			$phone = $_POST[$prefix . 'phone'];
			$address = $_POST[$prefix . 'address'];
	

	        if (username_exists($username)) {
	            $username .= '_' . rand(10000, 99999);

	            if (username_exists($username)) {
		            $username .= '_' . rand(10000, 99999);
		        }
	        }

        	$_POST['role'] = $role;
	 		$userdata = array(
		        'user_login' => sanitize_user( $username ),
		        'user_email' => sanitize_email( $email ),
		        'user_pass' => $_POST[$prefix.'password'],
		        'role' => $role,

	        );
 			
	 		$userdata = apply_filters( 'wp-freeio-register-user-userdata', $userdata, $prefix);

	        $user_id = wp_insert_user( $userdata );
	        if ( ! is_wp_error( $user_id ) ) {
				update_user_meta($user_id, 'phone', sanitize_text_field($phone));
				update_user_meta($user_id, 'address', sanitize_text_field($address));
	        	if ( (self::is_employer($user_id) && wp_freeio_get_option('employers_requires_approval', 'auto') != 'auto') ) {
	        		$user_data = get_userdata($user_id);
	        		$jsondata = apply_filters( 'wp-freeio-register-employer-jsondata', array(
	            		'status' => true,
	            		'msg' => WP_Freeio_User::register_msg($user_data),
	            		'redirect' => false
	            	));
	        	} elseif ( (self::is_freelancer($user_id) && wp_freeio_get_option('freelancers_requires_approval', 'auto') != 'auto') ) {
	        		$user_data = get_userdata($user_id);
	        		$jsondata = apply_filters( 'wp-freeio-register-freelancer-jsondata', array(
	            		'status' => true,
	            		'msg' => WP_Freeio_User::register_msg($user_data),
	            		'redirect' => false
	            	));
	        	} else {
	        		$jsondata = array(
	        			'status' => true,
	        			'msg' => esc_html__( 'You have registered, redirecting ...', 'wp-freeio' ),
	        			'role' => $role,
	        			'redirect' => true
	        		);
	        		
	        		wp_set_current_user( $user_id );
					wp_set_auth_cookie( $user_id, true );
	        	}
				
	        } else {
		        $jsondata = array('status' => false, 'msg' => esc_html__( 'Register user error!', 'wp-freeio' ) );
		    }
	    } else {
	    	$jsondata = array('status' => false, 'msg' => implode('<br>', $reg_errors->get_error_messages()) );
	    }
	    echo json_encode($jsondata);
	    exit;
	}

	public static function process_register() {
		global $reg_errors;
		$do_check = true;
		if ( isset($_POST['role']) && $_POST['role'] == 'wp_freeio_employer' ) {
        	$do_check = check_ajax_referer( 'ajax-register-employer-nonce', 'security_register_employer', false );
        	$prefix = WP_FREEIO_EMPLOYER_PREFIX;
        } elseif ( isset($_POST['role']) && $_POST['role'] == 'wp_freeio_freelancer' ) {
        	$do_check = check_ajax_referer( 'ajax-register-freelancer-nonce', 'security_register_freelancer', false );
        	$prefix = WP_FREEIO_FREELANCER_PREFIX;
        }
		if ( $do_check == false ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again.', 'wp-freeio') );
            wp_send_json( $json );
        }
		
        self::registration_validation( $_POST['email'], $_POST['password'], $_POST['confirmpassword'] );

        do_action( 'wp-freeio-registration-validation-after', $prefix );
        if ( 1 > count( $reg_errors->get_error_messages() ) ) {

        	$email = $_POST['email'];
			$usernames = $_POST[$prefix.'username'];

        	// $usernames = explode('@', $email);
			$username = sanitize_user( str_replace(' ', '_', strtolower($usernames)) );

			// $username = sanitize_user( str_replace(' ', '_', strtolower($usernames[0])) );

	        if (username_exists($username)) {
	            $username .= '_' . rand(10000, 99999);

	            if (username_exists($username)) {
		            $username .= '_' . rand(10000, 99999);
		        }
	        }
	        
	 		$userdata = array(
		        'user_login' => sanitize_user( $username ),
		        'user_email' => sanitize_email( $email ),
		        'user_pass' => $_POST['password'],
	        );

	        if ( isset($_POST['role']) ) {
	        	$userdata['role'] = $_POST['role'];
	        }

	        $userdata = apply_filters( 'wp-freeio-register-user-userdata', $userdata);
	        $user_id = wp_insert_user( $userdata );
	        if ( ! is_wp_error( $user_id ) ) {
	        	if ( (self::is_employer($user_id) && wp_freeio_get_option('employers_requires_approval', 'auto') != 'auto') ) {
	        		$user_data = get_userdata($user_id);
	        		$jsondata = apply_filters( 'wp-freeio-register-employer-jsondata', array(
	            		'status' => true,
	            		'msg' => WP_Freeio_User::register_msg($user_data),
	            		'redirect' => false
	            	));
	        	} elseif ( (self::is_freelancer($user_id) && wp_freeio_get_option('freelancers_requires_approval', 'auto') != 'auto') ) {
	        		$user_data = get_userdata($user_id);
	        		$jsondata = apply_filters( 'wp-freeio-register-freelancer-jsondata', array(
	            		'status' => true,
	            		'msg' => WP_Freeio_User::register_msg($user_data),
	            		'redirect' => false
	            	));
	        	} else {
	        		$role = 'wp_freeio_freelancer';
					if ( self::is_employer($user_id) ) {
						$role = 'wp_freeio_employer';
					}
	        		$jsondata = array(
	        			'status' => true,
	        			'msg' => esc_html__( 'You have registered, redirecting ...', 'wp-freeio' ),
	        			'role' => $role,
	        			'redirect' => true
	        		);
	        		wp_set_current_user( $user_id );
					wp_set_auth_cookie( $user_id, true );
	        	}
				
	        } else {
		        $jsondata = array('status' => false, 'msg' => esc_html__( 'Register user error!', 'wp-freeio' ) );
		    }
	    } else {
	    	$jsondata = array('status' => false, 'msg' => implode('<br>', $reg_errors->get_error_messages()) );
	    }
	    echo json_encode($jsondata);
	    exit;
	}

	public static function registration_validation( $email, $password, $confirmpassword, $phone, $address, $captcha = true, $check_term = true, $no_password = false ) {
		global $reg_errors;
		$reg_errors = new WP_Error;
	
		if ( $captcha && WP_Freeio_Recaptcha::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_Freeio_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
				$reg_errors->add('field', esc_html__( 'Captcha is not valid', 'wp-freeio' ) );
			}
		}
	
		if ( $check_term ) {
			$page_id = wp_freeio_get_option('terms_conditions_page_id');
			if ( !empty($page_id) ) {
				if ( empty($_POST['terms_and_conditions']) ) {
					$reg_errors->add('field', esc_html__( 'Terms and Conditions are required', 'wp-freeio' ) );
				}
			}
		}
	
		if ( !$no_password ) {
			if ( strlen( $password ) < 8 ) {
				$reg_errors->add( 'password', esc_html__( 'Password length must be greater than 8 characters', 'wp-freeio' ) );
			}
	
			if ( ! preg_match( '/[0-9]/', $password ) ) {
				$reg_errors->add( 'my_pass_numeric', esc_html__( 'Password must have at least 1 numeric character', 'wp-freeio' ) );
			}
			if ( ! preg_match( '/[a-z]/', $password ) ) {
				$reg_errors->add( 'my_pass_lowercase', esc_html__( 'Password must have at least 1 lower case character', 'wp-freeio' ) );
			}
			if ( ! preg_match( '/[A-Z]/', $password ) ) {
				$reg_errors->add( 'my_pass_uppercase', esc_html__( 'Password must have at least 1 upper case character', 'wp-freeio' ) );
			}
	
			if ( $password != $confirmpassword ) {
				$reg_errors->add( 'password', esc_html__( 'Password must be equal Confirm Password', 'wp-freeio' ) );
			}
		}
	
		if ( !is_email( $email ) ) {
			$reg_errors->add( 'email_invalid', esc_html__( 'Email is not valid', 'wp-freeio' ) );
		}
	
		if ( email_exists( $email ) ) {
			$reg_errors->add( 'email', esc_html__( 'Email Already in use', 'wp-freeio' ) );
		}
	

		// Add phone number validation
		if (empty($phone)) {
			$reg_errors->add('phone', esc_html__( 'Phone number is required', 'wp-freeio' ) );
		} elseif (!is_numeric($phone)) {
			$reg_errors->add('phone', esc_html__( 'Phone number must be numeric', 'wp-freeio' ) );
		} elseif (strlen($phone) < 8) {
			$reg_errors->add('phone', esc_html__( 'Phone number cannot exceed 10 digits', 'wp-freeio' ) );
		}
	}
	
	public static function auto_generate_user( $post_id, $post, $updated ) {

		if ( $updated ) {
			return;
		}

		global $wp_freeio_stop_process;
        if ( $wp_freeio_stop_process ) {
        	return true;
        }

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return $post_id;
	    }

		// If this is a revision
		if ( wp_is_post_revision( $post_id ) ) {
		    return;
		}
		
		$post_type = get_post_type($post_id);

		if ( !in_array($post_type, array('employer', 'freelancer')) ) {
	        return $post_id;
	    }

	    $post_status = get_post_status($post_id);
	    if ( $post_status == 'auto-draft' ) {
	        return $post_id;
	    }

		self::generate_user_by_post($post_id);
	}
	
	public static function generate_user_by_post($post_id) {
	    
	    $post_type = get_post_type($post_id);
	    if ( !in_array($post_type, array('employer', 'freelancer')) ) {
	    	return;
	    }
	    
	    $user_email = '';
	    if ( !empty($post_id) ) {
	    	if ( $post_type == 'employer' ) {
		    	$user_id = self::get_user_by_employer_id($post_id);
		    	$user_email = get_post_meta($post_id, '_employer_email', true);
		    } else {
		    	$user_id = self::get_user_by_freelancer_id($post_id);
		    	$user_email = get_post_meta($post_id, '_freelancer_email', true);
		    }
	    	if ( !empty($user_id) ) {
	    		return $user_id;
	    	}
	    }

	    $employer_name = get_post_field( 'post_title', $post_id );

        $username = sanitize_title($employer_name);
        if (username_exists($username)) {
            $username .= '_' . rand(10000, 99999);

            if (username_exists($username)) {
	            $username .= '_' . rand(10000, 99999);
	        }
        }
        
        if ( empty($user_email) ) {
	        $user_email = $username . '@fakeuser.com';
	    }
        if ( email_exists( $user_email ) ) {
        	$user_email = $username . '_' . rand(10000, 99999) . '@fakeuser.com';

        	if ( email_exists( $user_email ) ) {
	        	$user_email = $username . '_' . rand(10000, 99999) . '_' . rand(10000, 99999) . '@fakeuser.com';
	        }
        }
        global $wp_freeio_stop_process;
        if ( $post_type == 'employer' ) {
        	$role = 'wp_freeio_employer';
        	$wp_freeio_stop_process = $role;
        } elseif ( $post_type == 'freelancer' ) {
        	$role = 'wp_freeio_freelancer';
        	$wp_freeio_stop_process = $role;
        } else {
        	return;
        }

        $_POST['action_process'] = $role;
        $_POST['role'] = $role;

        $userdata = array(
	        'user_login' => sanitize_user( $username ),
	        'user_email' => sanitize_email( $user_email ),
	        'user_pass' => wp_generate_password(12),
	        'role' => $role,
        );

        $user_id = wp_insert_user( $userdata );
        if (!is_wp_error($user_id)) {
        	if ( $post_type == 'employer' ) {
            	$prefix = WP_FREEIO_EMPLOYER_PREFIX;
            	update_user_meta($user_id, 'employer_id', $post_id);
            } else {
            	$prefix = WP_FREEIO_FREELANCER_PREFIX;
            	update_user_meta($user_id, 'freelancer_id', $post_id);
            }
            $_POST[$prefix.'email'] = $user_email;
            update_post_meta($post_id, $prefix . 'email', $user_email);
        	update_post_meta($post_id, $prefix . 'user_id', $user_id);
            update_post_meta($post_id, $prefix . 'display_name', $employer_name);
            
            if ( $post_id ) {
	    		$data = array( 'ID' => $post_id, 'post_author' => $user_id );
	    		wp_insert_post( $data, true );
	    	}
	    	$wp_freeio_stop_process = false;
            return $user_id;
        }
	}

	public static function generate_user_by_post_name($employer_name) {
	    
	    $user_email = '';
	    $post_obj = get_page_by_title( $employer_name, OBJECT, 'employer' );
	    if ( !empty($post_obj) && !empty($post_obj->ID) ) {

	    	$user_id = self::get_user_by_employer_id($post_obj->ID);

	    	if ( !empty($user_id) ) {
	    		return $user_id;
	    	}
	    	$user_email = get_post_meta($post_obj->ID, '_employer_email', true);
	    }

        $username = sanitize_title($employer_name);
        if (username_exists($username)) {
            $username .= '_' . rand(10000, 99999);

            if (username_exists($username)) {
	            $username .= '_' . rand(10000, 99999);
	        }
        }
        
        if ( empty($user_email) ) {
	        $user_email = $username . '@fakeuser.com';
	    }
        if ( email_exists( $user_email ) ) {
        	$user_email = $username . '_' . rand(10000, 99999) . '@fakeuser.com';

        	if ( email_exists( $user_email ) ) {
	        	$user_email = $username . '_' . rand(10000, 99999) . '_' . rand(10000, 99999) . '@fakeuser.com';
	        }
        }

        global $wp_freeio_stop_process;
    	$role = 'wp_freeio_employer';
    	if ( !empty($post_obj) && !empty($post_obj->ID) ) {
	    	$wp_freeio_stop_process = $role;
	    }

	    $_POST['role'] = $role;

        $userdata = array(
	        'user_login' => sanitize_user( $username ),
	        'user_email' => sanitize_email( $user_email ),
	        'user_pass' => wp_generate_password(12),
	        'role' => $role,
        );

        $user_id = wp_insert_user( $userdata );
        if (!is_wp_error($user_id)) {
            if ( !empty($post_obj) && !empty($post_obj->ID) ) {

            	update_user_meta($user_id, 'employer_id', $post_obj->ID);
	            
            	update_post_meta($post_obj->ID, WP_FREEIO_EMPLOYER_PREFIX . 'user_id', $user_id);
	            update_post_meta($post_obj->ID, WP_FREEIO_EMPLOYER_PREFIX . 'display_name', $employer_name);
	            update_post_meta($post_obj->ID, WP_FREEIO_EMPLOYER_PREFIX . 'email', $user_email);
            }
            $wp_freeio_stop_process = false;
            return $user_id;
        }
        $wp_freeio_stop_process = false;
	}

	public static function process_delete_profile() {
        if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-delete-profile-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$password = isset($_POST['password']) ? $_POST['password'] : '';
        $user_id = get_current_user_id();
        $userdata = get_user_by('ID', $user_id);

        if ( empty($password) ) {
        	$return = array( 'status' => false, 'msg' => esc_html__('Please Enter the password.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
        }
        if ( !is_object($userdata) || !wp_check_password($password, $userdata->data->user_pass, $user_id) ) {
            $return = array( 'status' => false, 'msg' => esc_html__('Please Enter the correct password.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
        }
        
        do_action( 'wp-freeio-before-delete-profile', $user_id, $userdata );

        wp_delete_user($user_id);
        
        $return = array( 'status' => true, 'msg' => esc_html__('Your profile deleted successfully.', 'wp-freeio') );
	   	echo wp_json_encode($return);
	   	exit;
	}

	public static function process_delete_user($user_id, $reassign) {
		if ( self::is_employer($user_id) ) {
        	$employer_id = self::get_employer_by_user_id($user_id);

            wp_delete_post($employer_id);
        } elseif ( self::is_freelancer($user_id) ) {
        	$freelancer_id = self::get_freelancer_by_user_id($user_id);

        	$freelancer_post = get_post($freelancer_id);
        	WP_Freeio_Mpdf::mpdf_delete_file($freelancer_post);

            wp_delete_post($freelancer_id);
        }
	}

	public static function registration_save($user_id) {

		global $wp_freeio_stop_process;
        if ( $wp_freeio_stop_process ) {
        	return true;
        }

        $wp_freeio_stop_process = true;

        $action = isset($_REQUEST['action']) && $_REQUEST['action'] != '' ? $_REQUEST['action'] : '';
        $user_role = isset($_POST['role']) && $_POST['role'] != '' ? $_POST['role'] : '';
        $user_obj = get_user_by('ID', $user_id);

        if ($user_role == 'wp_freeio_employer') {

        	$prefix = WP_FREEIO_EMPLOYER_PREFIX;

        	$post_title = str_replace(array('-', '_'), array(' ', ' '), $user_obj->display_name);
        	$display_name = $user_obj->display_name;
        	if ( !empty($_POST[$prefix.'title']) ) {
                $post_title = $display_name = sanitize_text_field($_POST[$prefix.'title']);
                
                $user_def_array = array(
                    'ID' => $user_id,
                    'display_name' => $display_name,
                );
                wp_update_user($user_def_array);
            }

            $post_args = array(
                'post_title' => $post_title,
                'post_type' => 'employer',
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => $user_id,
            );

            if ( !empty($_POST[$prefix . 'description']) ) {
            	$post_args['post_content'] = wp_kses_post($_POST[$prefix . 'description']);
            }

            if ( wp_freeio_get_option('employers_requires_approval', 'auto') != 'auto' && ($action == 'wp_freeio_ajax_register' || $action == 'wp_freeio_ajax_registernew') ) {
            	$post_args['post_status'] = 'pending';
            }

            if ( wp_freeio_get_option('employer_slug_employer') == 'number' ) {
            	$rand_num = mt_rand(100000000000,999999999999);
            	$post_args['post_name'] = $rand_num;
            }

            $post_args = apply_filters('wp-freeio-create-employer-post-args', $post_args, $user_obj);

            // Insert the post into the database
            $employer_id = wp_insert_post($post_args);

            $post_id = $employer_id;
            update_post_meta($employer_id, $prefix . 'user_id', $user_id);
            update_post_meta($employer_id, $prefix . 'display_name', $display_name);
            update_post_meta($employer_id, $prefix . 'email', $user_obj->user_email);
            update_post_meta($employer_id, $prefix . 'show_profile', 'show');

            update_post_meta($employer_id, 'post_date', strtotime(current_time('d-m-Y H:i:s')));

            //
            update_user_meta($user_id, 'employer_id', $employer_id);



            if ( wp_freeio_get_option('employers_requires_approval', 'auto') != 'auto' && ($action == 'wp_freeio_ajax_register' || $action == 'wp_freeio_ajax_registernew') ) {
            	$code = WP_Freeio_Mixes::random_key();
                update_user_meta($user_id, 'account_approve_key', $code);
            	update_user_meta($user_id, 'user_account_status', 'pending');

            	if ( wp_freeio_get_option('employers_requires_approval', 'auto') == 'email_approve' ) {
					$user_email = stripslashes( $user_obj->user_email );
				} else {
					$user_email = get_option( 'admin_email', false );
				}

				$subject = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'content');
				
				$email_from = get_option( 'admin_email', false );
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				// send the mail
				WP_Freeio_Email::wp_mail( $user_email, $subject, $content, $headers );
            } else {
            	if ( wp_freeio_get_option('user_notice_add_new_user_register') ) {
	            	$user_email = stripslashes( $user_obj->user_email );
	            	$subject = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_auto_approve', 'subject');
					$content = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_auto_approve', 'content');

					$email_from = get_option( 'admin_email', false );
					$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
					// send the mail
					WP_Freeio_Email::wp_mail( $user_email, $subject, $content, $headers );
				}
            }

            if ( isset($_POST['phone']) ) {
	            update_post_meta($employer_id, $prefix . 'phone', $_POST['phone']);
	        }

            if (isset($_POST['employer_category'])) {
                $employer_category = sanitize_text_field($_POST['employer_category']);
                wp_set_post_terms($employer_id, array($employer_category), 'employer_category', false);
            }

            // custom fields saving
            do_action('wp_freeio_employer_signup_custom_fields_save', $employer_id);

            do_action('wp_freeio_signup_custom_fields_save', 'employer', $employer_id);

            
        } elseif ( $user_role == 'wp_freeio_employee' ) {
        	if ( wp_freeio_get_option('user_notice_add_new_user_register') ) {
	        	$user_email = stripslashes( $user_obj->user_email );
	        	$subject = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_auto_approve', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_auto_approve', 'content');

				$email_from = get_option( 'admin_email', false );
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				// send the mail
				WP_Freeio_Email::wp_mail( $user_email, $subject, $content, $headers );
        	}

            do_action('wp_freeio_signup_custom_fields_save', 'employee');

        } elseif ( $user_role == 'wp_freeio_freelancer' ) {
        	
        	$prefix = WP_FREEIO_FREELANCER_PREFIX;
        	global $wp_freeio_socials_register;

        	$post_title = str_replace(array('-', '_'), array(' ', ' '), $user_obj->display_name);
        	$display_name = $user_obj->display_name;
        	if ( !empty($_POST[$prefix.'title']) ) {
                $post_title = $display_name = sanitize_text_field($_POST[$prefix.'title']);
                
                $user_def_array = array(
                    'ID' => $user_id,
                    'display_name' => $display_name,
                );
                wp_update_user($user_def_array);
            }

            $post_args = array(
                'post_title' => $post_title,
                'post_type' => 'freelancer',
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => $user_id,
            );
            if ( !empty($_POST[$prefix.'description']) ) {
            	$post_args['post_content'] = wp_kses_post($_POST[$prefix.'description']);
            }

            $post_status = 'publish';
            if ( wp_freeio_get_option('freelancers_requires_approval', 'auto') != 'auto' && ($action == 'wp_freeio_ajax_register' || $action == 'wp_freeio_ajax_registernew' || $wp_freeio_socials_register ) ) {
            	$post_status = 'pending';
            }
            if ( wp_freeio_get_option('resumes_requires_approval', 'auto') != 'auto' && ($action == 'wp_freeio_ajax_register' || $action == 'wp_freeio_ajax_registernew' || $wp_freeio_socials_register ) ) {
            	$post_status = 'pending_approve';
            }
            $post_args['post_status'] = $post_status;
            
            if ( wp_freeio_get_option('freelancer_slug_freelancer') == 'number' ) {
            	$rand_num = mt_rand(100000000000,999999999999);
            	$post_args['post_name'] = $rand_num;
            }

            $post_args = apply_filters('wp-freeio-create-freelancer-post-args', $post_args, $user_obj);

            // Insert the post into the database
            $freelancer_id = wp_insert_post($post_args);
            
            $post_id = $freelancer_id;

            update_post_meta($freelancer_id, $prefix . 'user_id', $user_id);
            update_post_meta($freelancer_id, $prefix . 'display_name', $display_name);
            update_post_meta($freelancer_id, $prefix . 'email', $user_obj->user_email);
            update_post_meta($freelancer_id, $prefix . 'show_profile', 'show');

            update_user_meta($user_id, 'freelancer_id', $freelancer_id);
            

            if ( wp_freeio_get_option('freelancers_requires_approval', 'auto') != 'auto' && ($action == 'wp_freeio_ajax_register' || $action == 'wp_freeio_ajax_registernew') ) {
            	$code = WP_Freeio_Mixes::random_key();
                update_user_meta($user_id, 'account_approve_key', $code);
            	update_user_meta($user_id, 'user_account_status', 'pending');

            	if ( wp_freeio_get_option('freelancers_requires_approval', 'auto') == 'email_approve' ) {
					$user_email = stripslashes( $user_obj->user_email );
				} else {
					$user_email = get_option( 'admin_email', false );
				}

				$subject = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'content');

				$email_from = get_option( 'admin_email', false );
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				// send the mail
				WP_Freeio_Email::wp_mail( $user_email, $subject, $content, $headers );
            } else {
            	if ( wp_freeio_get_option('user_notice_add_new_user_register') ) {
	            	$user_email = stripslashes( $user_obj->user_email );
	            	$subject = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_auto_approve', 'subject');
					$content = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_auto_approve', 'content');

					$email_from = get_option( 'admin_email', false );
					$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
					// send the mail
					WP_Freeio_Email::wp_mail( $user_email, $subject, $content, $headers );
				}
            }

            update_post_meta($freelancer_id, 'post_date', strtotime(current_time('d-m-Y H:i:s')));

            if ( isset($_POST['phone']) ) {
	            update_post_meta($freelancer_id, $prefix . 'phone', $_POST['phone']);
	        }
            if (isset($_POST['freelancer_category'])) {
                $freelancer_category = sanitize_text_field($_POST['freelancer_category']);
                wp_set_post_terms($freelancer_id, array($freelancer_category), 'freelancer_category', false);
            }

            // custom fields saving
            do_action('wp_freeio_freelancer_signup_custom_fields_save', $freelancer_id);
             
            do_action('wp_freeio_signup_custom_fields_save', 'freelancer', $freelancer_id);
            
            $wp_freeio_socials_register = false;
        }

        do_action('wp_freeio_member_after_making_cand_or_emp', $user_id, $user_role);

        //remove user admin bar
        update_user_meta($user_id, 'show_admin_bar_front', false);
	}

	public static function process_change_password() {
		$old_password = sanitize_text_field( $_POST['old_password'] );
		$new_password = sanitize_text_field( $_POST['new_password'] );
		$retype_password = sanitize_text_field( $_POST['retype_password'] );

		if ( empty( $old_password ) || empty( $new_password ) || empty( $retype_password ) ) {
			echo json_encode(array('status' => false, 'msg'=> __( 'All fields are required.', 'wp-freeio' ) ));
			die();
		}

		if ( $new_password != $retype_password ) {
			echo json_encode(array('status' => false, 'msg'=> __( 'New and retyped password are not same.', 'wp-freeio' ) ));
			die();
		}

		$user = wp_get_current_user();
		if ( ! wp_check_password( $old_password, $user->data->user_pass, $user->ID ) ) {
			echo json_encode(array('status' => false, 'msg'=> __( 'Your old password is not correct.', 'wp-freeio' ) ));
			die();
		}

		do_action('wp-freeio-process-change-password', $_POST);

		wp_set_password( $new_password, $user->ID );
		echo json_encode(array('status' => true, 'msg'=> __( 'Your password has been successfully changed.', 'wp-freeio' ) ));
		die();
	}

	public static function process_change_profile() {
		$user_id = self::get_user_id();
		$prefix = '';
		if ( self::is_employer($user_id) ) {
	    	$prefix = WP_FREEIO_EMPLOYER_PREFIX;
	    	$post_id = self::get_employer_by_user_id($user_id);
	    } elseif( self::is_freelancer($user_id) ) {
	    	$prefix = WP_FREEIO_FREELANCER_PREFIX;
	    	$post_id = self::get_freelancer_by_user_id($user_id);
	    } else {
	    	return;
	    }

		if ( ! isset( $_POST['submit-cmb-profile'] ) || empty( $_POST[$prefix.'post_type'] ) || !in_array($_POST[$prefix.'post_type'], array('freelancer', 'employer') ) ) {
			return;
		}

		if ( isset($_POST[$prefix.'email']) ) {
			$email = sanitize_email( $_POST[$prefix.'email'] );
			if ( empty( $email ) ) {
				$_SESSION['messages'][] = array( 'danger', __( 'E-mail is required.', 'wp-freeio' ) );
				return;
			}
			$user = wp_get_current_user();
			if ( $email != $user->user_email && email_exists($email) ) {
				$_SESSION['messages'][] = array( 'danger', __( 'E-mail is exists.', 'wp-freeio' ) );
				return;
			}
		}

		// $redirect_url = get_permalink( wp_freeio_get_option('edit_profile_page_id') );

		$cmb = cmb2_get_metabox( $prefix . 'front', $post_id );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return;
		}

		$data = array(
			'ID'			=> $post_id,
			'post_title'    => sanitize_text_field( $_POST[ $prefix . 'title' ] ),
			'post_content'  => wp_kses_post( $_POST[ $prefix . 'description' ] ),
		);
		
		do_action( 'wp-freeio-process-profile-before-change', $post_id, $prefix );

		$data = apply_filters('wp-freeio-process-profile-data', $data, $post_id, $prefix);
		
		$post_id = wp_update_post( $data );

		if ( ! empty( $post_id ) && ! empty( $_POST['object_id'] ) ) {

			if ( !empty($email) ) {
				$data = array(
					'ID'			=> $user->ID,
					'user_email'	=> $email,
				);
				$user_id_update = wp_update_user( $data );
			}

			$_POST['object_id'] = $post_id; // object_id in POST contains page ID instead of job ID

			$cmb->save_fields( $post_id, 'post', $_POST );

			if ( self::is_employer($user_id) ) {
				if ( !empty($_POST[$prefix.'team_members']) ) {
					$team_members = $_POST[$prefix.'team_members'];
					if ( isset($_POST['current_'.$prefix.'team_members']) ) {
						foreach ($_POST['current_'.$prefix.'team_members'] as $gkey => $ar_value) {
							foreach ($ar_value as $ikey => $value) {
								if ( is_numeric($value) ) {
									$url = wp_get_attachment_url( $value );
									$team_members[$gkey][$ikey.'_id'] = $value;
									$team_members[$gkey][$ikey] = $url;
								} elseif ( ! empty( $value ) ) {
									$attach_id = WP_Freeio_Image::create_attachment( $value, $post_id );
									$url = wp_get_attachment_url( $attach_id );
									$team_members[$gkey][$ikey.'_id'] = $attach_id;
									$team_members[$gkey][$ikey] = $url;
								}
							}
						}
						update_post_meta( $post_id, $prefix.'team_members', $team_members );
					}
				}
			}

			// Create featured image
			if ( isset( $_FILES[ $prefix . 'featured_image' ] ) ) {
				$featured_image = get_post_meta( $post_id, $prefix . 'featured_image', true );
				if ( ! empty( $_POST[ 'current_' . $prefix . 'featured_image' ] ) ) {
					if ( !empty($featured_image) ) {
						if ( is_array($featured_image) ) {
							$img_id = $featured_image[0];
						} elseif ( is_integer($featured_image) ) {
							$img_id = $featured_image;
						} else {
							$img_id = WP_Freeio_Image::get_attachment_id_from_url($featured_image);
						}
						set_post_thumbnail( $post_id, $img_id );
					} else {
						update_post_meta( $post_id, $prefix . 'featured_image', null );
						delete_post_thumbnail( $post_id );
					}
				} else {
					update_post_meta( $post_id, $prefix . 'featured_image', null );
					delete_post_thumbnail( $post_id );
				}
			}

			do_action( 'wp-freeio-process-profile-after-change', $post_id, $prefix );

			$_SESSION['messages'][] = array( 'success', __( 'Profile has been successfully updated.', 'wp-freeio' ) );

		} else {
			$_SESSION['messages'][] = array( 'danger', __( 'Can not update profile', 'wp-freeio' ) );
		}
	}

	public static function process_change_resume() {
		$user_id = self::get_user_id();
		$prefix = '';
		if( WP_Freeio_User::is_freelancer($user_id) ) {
	    	$prefix = WP_FREEIO_FREELANCER_PREFIX;
	    	$post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
	    } else {
	    	return;
	    }

		if ( ! isset( $_POST['submit-cmb-resume'] ) || empty( $_POST[$prefix.'post_type'] ) || !in_array($_POST[$prefix.'post_type'], array('freelancer') ) ) {
			return;
		}

		$user = wp_get_current_user();
		if ( isset($_POST[$prefix.'email']) ) {
			$email = sanitize_email( $_POST[$prefix.'email'] );
			if ( empty( $email ) ) {
				$_SESSION['messages'][] = array( 'danger', __( 'E-mail is required.', 'wp-freeio' ) );
				return;
			}
			if ( $email != $user->user_email && email_exists($email) ) {
				$_SESSION['messages'][] = array( 'danger', __( 'E-mail is exists.', 'wp-freeio' ) );
				return;
			}
		}

		// $redirect_url = esc_url_raw('//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

		$cmb = cmb2_get_metabox( $prefix . 'resume_front', $post_id );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return;
		}
		$data = array(
			'ID' => $post_id
		);

		$title = !empty($_POST[ $prefix . 'title' ]) ? sanitize_text_field( $_POST[ $prefix . 'title' ] ) : '';
		$description = !empty($_POST[ $prefix . 'description' ]) ? wp_kses_post( $_POST[ $prefix . 'description' ] ) : '';
		if ( $title ) {
			$data['title'] = $title;
		}
		if ( isset($_POST[ $prefix . 'description' ]) ) {
			$data['post_content'] = $description;
		}
		

		do_action( 'wp-freeio-process-resume-before-change', $post_id, $prefix );

		$data = apply_filters('wp-freeio-process-resume-data', $data, $post_id, $prefix);
		
		$post_id = wp_update_post( $data );

		if ( ! empty( $post_id ) && ! empty( $_POST['object_id'] ) ) {

			if ( !empty($email) ) {
				$data = array(
					'ID'			=> $user->ID,
					'user_email'	=> $email,
				);
				$user_id_update = wp_update_user( $data );
			}

			$_POST['object_id'] = $post_id; // object_id in POST contains page ID instead of job ID

			$cmb->save_fields( $post_id, 'post', $_POST );
			
			// Create featured image
			if ( isset( $_FILES[ $prefix . 'featured_image' ] ) ) {
				$featured_image = get_post_meta( $post_id, $prefix . 'featured_image', true );
				if ( ! empty( $_POST[ 'current_' . $prefix . 'featured_image' ] ) ) {
					if ( !empty($featured_image) ) {
						if ( is_array($featured_image) ) {
							$img_id = $featured_image[0];
						} elseif ( is_integer($featured_image) ) {
							$img_id = $featured_image;
						} else {
							$img_id = WP_Freeio_Image::get_attachment_id_from_url($featured_image);
						}
						set_post_thumbnail( $post_id, $img_id );
					} else {
						update_post_meta( $post_id, $prefix . 'featured_image', null );
						delete_post_thumbnail( $post_id );
					}
				} else {
					update_post_meta( $post_id, $prefix . 'featured_image', null );
					delete_post_thumbnail( $post_id );
				}
			}

			do_action( 'wp-freeio-process-resume-after-change', $post_id, $prefix );

			$_SESSION['messages'][] = array( 'success', __( 'Resume has been successfully updated.', 'wp-freeio' ) );

		} else {
			$_SESSION['messages'][] = array( 'danger', __( 'Can not update resume', 'wp-freeio' ) );
		}

	}

	public static function compute_profile_percent($post_id) {
		if ( empty($post_id) ) {
			return;
		}
		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		$metaboxes = apply_filters( 'cmb2_meta_boxes', array() );
		$fields = array();
		if ( !empty($metaboxes[$prefix . 'front']['fields']) ) {
			$fields = array_merge($fields, $metaboxes[$prefix . 'front']['fields']);
		}
		if ( !empty($metaboxes[$prefix . 'resume_front']['fields']) ) {
			$fields = array_merge($fields, $metaboxes[$prefix . 'resume_front']['fields']);
		}
		if ( empty($fields) ) {
			return;
		}

		$profile_percents = $profile_fields = array();
		foreach ($fields as $field) {
			if ( empty($field['type']) || empty($field['id']) || $field['type'] == 'hidden' || $field['type'] == 'title' || in_array($field['id'], array($prefix.'profile_url', $prefix.'featured', $prefix.'urgent', $prefix.'expiry_date')) ) {
				continue;
			}
			switch ($field['id']) {
				case $prefix.'socials':
					$values = get_post_meta($post_id, $field['id'], true);
					if ( !empty($values) ) {
						foreach ($values as $value) {
							if ( !empty($value['network']) && !empty($value['url']) ) {
								$profile_percents[$field['id']] = $field['name'];
								break;
							}
						}
					}
					break;
				case $prefix.'education':
				case $prefix.'experience':
				case $prefix.'award':
				case $prefix.'skill':
					$values = get_post_meta($post_id, $field['id'], true);
					if ( !empty($values) ) {
						foreach ($values as $value) {
							if ( !empty($value['title']) ) {
								$profile_percents[$field['id']] = $field;
								break;
							}
						}
					}
					break;
				case $prefix.'featured_image':
					if ( has_post_thumbnail($post_id) ) {
						$profile_percents[$field['id']] = $field;
					}
					break;
				case $prefix.'title':
					if ( get_the_title($post_id) ) {
						$profile_percents[$field['id']] = $field;
					}
					break;
				case $prefix.'description':
					if ( get_the_content('', false, $post_id) ) {
						$profile_percents[$field['id']] = $field;
					}
					break;
				case $prefix.'category':
					$terms = get_the_terms( $post_id, 'freelancer_category' );
					if ( !empty($terms) ) {
						$profile_percents[$field['id']] = $field;
					}
					break;
				case $prefix.'location':
					$terms = get_the_terms( $post_id, 'location' );
					if ( !empty($terms) ) {
						$profile_percents[$field['id']] = $field;
					}
					break;
				default:
					$value = get_post_meta($post_id, $field['id'], true);
					if ( !empty($value) ) {
						$profile_percents[$field['id']] = $field;
					}
					break;
			}
			$profile_fields[$field['id']] = !empty($field['name']) ? $field['name'] : '';
		}
		$profile_percent = !empty($profile_percents) ? count($profile_percents) : 0;
		$total_fields = !empty($profile_fields) ? count($profile_fields) : 0;
		
		$empty_fields = array();
		foreach ($profile_fields as $key => $name) {
			if ( !isset($profile_percents[$key]) ) {
				$empty_fields[$key] = $name;
			}
		}
		$percent = 0;
		if ( $total_fields > 0 ) {
			$percent = round($profile_percent/$total_fields, 2);
		}

		return array('percent' => $percent, 'empty_fields' => $empty_fields, 'profile_fields' => $profile_fields);
	}

	public static function process_resend_approve_account() {
		$user_login = isset($_POST['login']) ? $_POST['login'] : '';
		
		if ( empty($user_login) ) {
            echo json_encode(array(
            	'status' => false,
            	'msg' => __('Username or Email not exactly.', 'wp-freeio')
            ));
            die();
        }

		if (filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
            $user_obj = get_user_by('email', $user_login);
        } else {
            $user_obj = get_user_by('login', $user_login);
        }
        if ( !empty($user_obj->ID) ) {
	        $user_login_auth = self::get_user_status($user_obj->ID);
	        if ( $user_login_auth == 'pending' ) {

	        	if ( (self::is_employer($user_obj->ID) && wp_freeio_get_option('employers_requires_approval', 'auto') == 'email_approve') ) {
	        		$user_email = stripslashes( $user_obj->user_email );
	        	} elseif ( (self::is_freelancer($user_obj->ID) && wp_freeio_get_option('freelancers_requires_approval', 'auto') == 'email_approve') ) {
	        		$user_email = stripslashes( $user_obj->user_email );
	        	} else {
	        		$user_email = get_option( 'admin_email', false );
	        	}

				$subject = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('user_obj' => $user_obj), 'user_register_need_approve', 'content');

				$email_from = get_option( 'admin_email', false );
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

				// send the mail
				$result = WP_Freeio_Email::wp_mail( $user_email, $subject, $content, $headers );
				if ( $result ) {
					echo json_encode(array(
		            	'status' => true,
		            	'msg' => __('Sent a email successfully.', 'wp-freeio')
		            ));
		            die();
				} else {
					echo json_encode(array(
		            	'status' => false,
		            	'msg' => __('Send a email error.', 'wp-freeio')
		            ));
		            die();
		        }
	        }
        }
        echo json_encode(array(
        	'status' => false,
        	'msg' => __('Your account is not available.', 'wp-freeio')
        ));
        die();
	}

	public static function admin_user_auth_callback($user, $password = '') {
    	global $pagenow;
	    
	    $status = self::get_user_status($user->ID);
	    $message = false;
		switch ( $status ) {
			case 'pending':
				$pending_message = self::login_msg($user);
				$message = new WP_Error( 'pending_approval', $pending_message );
				break;
			case 'denied':
				$denied_message = __('Your account denied.', 'wp-freeio');
				$message = new WP_Error( 'denied_access', $denied_message );
				break;
			case 'approved':
				$message = $user;
				break;
		}

	    return $message;
	}

	public static function process_approve_user() {
		$post = get_post();

		if ( is_object( $post ) ) {
			if ( strpos( $post->post_content, '[wp_freeio_approve_user]' ) !== false ) {
				
				$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
				$code = isset($_GET['approve-key']) ? $_GET['approve-key'] : 0;
				if ( !$user_id ) {
					$error = array(
						'error' => true,
						'msg' => __('The user is not exists.', 'wp-freeio')
					);

				}
				$user = get_user_by('ID', $user_id);
				if ( empty($user) ) {
					$error = array(
						'error' => true,
						'msg' => __('The user is not exists.', 'wp-freeio')
					);
				} else {
					$user_code = get_user_meta($user_id, 'account_approve_key', true);
					if ( $code != $user_code ) {
						$error = array(
							'error' => true,
							'msg' => __('Code is not exactly.', 'wp-freeio')
						);
					}
				}

				if ( empty($error) ) {
					$return = self::update_user_status($user_id, 'approve');
					$error = array(
						'error' => false,
						'msg' => __('Your account approved.', 'wp-freeio')
					);
					$_SESSION['approve_user_msg'] = $error;
				} else {
					$_SESSION['approve_user_msg'] = $error;
				}
			}
		}
	}

	public static function approve_user( $user_id ) {
		$user = get_user_by('ID', $user_id);

		wp_cache_delete( $user->ID, 'users' );
		wp_cache_delete( $user->data->user_login, 'userlogins' );

		if ( wp_freeio_get_option('user_notice_add_approved_user') ) {
			$user_email = stripslashes( $user->data->user_email );

			$subject = WP_Freeio_Email::render_email_vars(array('user_obj' => $user), 'user_register_approved', 'subject');
			$content = WP_Freeio_Email::render_email_vars(array('user_obj' => $user), 'user_register_approved', 'content');

			$email_from = get_option( 'admin_email', false );
			$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
			// send the mail
			WP_Freeio_Email::wp_mail( $user_email, $subject, $content, $headers );
		}

		// change usermeta tag in database to approved
		update_user_meta( $user->ID, 'user_account_status', 'approved' );
		update_user_meta( $user->ID, 'account_approve_key', '' );

		// employer | freelancer
		if ( self::is_employer($user->ID) ) {
			$employer_id = self::get_employer_by_user_id($user->ID);
			$data_args = array(
				'post_status' => 'publish',
				'ID' => $employer_id
			);
			remove_action( 'wp_freeio_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
			remove_action('denied_to_publish', array( 'WP_Freeio_Post_Type_Employer', 'process_denied_to_publish' ) );
			remove_action('pending_to_publish', array( 'WP_Freeio_Post_Type_Employer', 'process_denied_to_publish' ) );
			$employer_id = wp_update_post( $data_args, true );
			add_action( 'denied_to_publish', array( 'WP_Freeio_Post_Type_Employer', 'process_pending_to_publish' ) );
			add_action( 'pending_to_publish', array( 'WP_Freeio_Post_Type_Employer', 'process_pending_to_publish' ) );
			add_action( 'wp_freeio_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
		} elseif ( self::is_freelancer($user->ID) ) {
			$post_status = 'publish';
			if ( wp_freeio_get_option('resumes_requires_approval', 'auto') != 'auto' ) {
				$post_status = 'pending_approve';
			}
			$freelancer_id = self::get_freelancer_by_user_id($user->ID);
			$data_args = array(
				'post_status' => $post_status,
				'ID' => $freelancer_id
			);
			remove_action( 'wp_freeio_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
			remove_action('denied_to_publish', array( 'WP_Freeio_Post_Type_Freelancer', 'process_denied_to_publish' ) );
			remove_action('pending_to_publish', array( 'WP_Freeio_Post_Type_Freelancer', 'process_denied_to_publish' ) );
			$freelancer_id = wp_update_post( $data_args, true );
			add_action( 'denied_to_publish', array( 'WP_Freeio_Post_Type_Freelancer', 'process_pending_to_publish' ) );
			add_action( 'pending_to_publish', array( 'WP_Freeio_Post_Type_Freelancer', 'process_pending_to_publish' ) );
			add_action( 'wp_freeio_new_user_approve_approve_user', array( __CLASS__, 'approve_user' ) );
		}

		do_action( 'wp-freeio-new_user_approve_user_approved', $user );
	}

	public static function deny_user( $user_id ) {
		$user = get_user_by('ID', $user_id);

		if ( wp_freeio_get_option('user_notice_add_denied_user') ) {
			$user_email = stripslashes( $user->data->user_email );

			$subject = WP_Freeio_Email::render_email_vars(array('user_obj' => $user), 'user_register_denied', 'subject');
			$content = WP_Freeio_Email::render_email_vars(array('user_obj' => $user), 'user_register_denied', 'content');

			$email_from = get_option( 'admin_email', false );
			$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
			// send the mail
			WP_Freeio_Email::wp_mail( $user_email, $subject, $content, $headers );
		}

		update_user_meta( $user->ID, 'user_account_status', 'denied' );

		// employer | freelancer
		if ( self::is_employer($user->ID) ) {
			$employer_id = self::get_employer_by_user_id($user->ID);
			$data_args = array(
				'post_status' => 'denied',
				'ID' => $employer_id
			);
			$employer_id = wp_update_post( $data_args, true );
		} elseif ( self::is_freelancer($user->ID) ) {
			$freelancer_id = self::get_freelancer_by_user_id($user->ID);

			$data_args = array(
				'post_status' => 'denied',
				'ID' => $freelancer_id
			);
			$freelancer_id = wp_update_post( $data_args, true );
		}

		do_action( 'wp-freeio-new_user_approve_user_denied', $user );
	}

	public static function get_user_status( $user_id ) {
		$user_status = get_user_meta( $user_id, 'user_account_status', true );

		if ( empty( $user_status ) ) {
			$user_status = 'approved';
		}

		return $user_status;
	}

	public static function update_user_status( $user, $status ) {
		$user_id = absint( $user );
		if ( !$user_id ) {
			return false;
		}

		if ( !in_array( $status, array( 'approve', 'deny' ) ) ) {
			return false;
		}

		$do_update = apply_filters( 'wp_freeio_new_user_approve_validate_status_update', true, $user_id, $status );
		if ( !$do_update ) {
			return false;
		}

		// where it all happens
		do_action( 'wp_freeio_new_user_approve_' . $status . '_user', $user_id );
		do_action( 'wp_freeio_new_user_approve_user_status_update', $user_id, $status );

		return true;
	}

	public static function process_update_user_action() {
		if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'approve', 'deny' ) ) && !isset( $_GET['new_role'] ) ) {
			check_admin_referer( 'wp-freeio' );

			$sendback = remove_query_arg( array( 'approved', 'denied', 'deleted', 'ids', 'wp-freeio-status-query-submit', 'new_role' ), wp_get_referer() );
			if ( !$sendback ) {
				$sendback = admin_url( 'users.php' );
			}

			$wp_list_table = _get_list_table( 'WP_Users_List_Table' );
			$pagenum = $wp_list_table->get_pagenum();
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );

			$status = sanitize_key( $_GET['action'] );
			$user = absint( $_GET['user'] );

			self::update_user_status( $user, $status );

			if ( $_GET['action'] == 'approve' ) {
				$sendback = add_query_arg( array( 'approved' => 1, 'ids' => $user ), $sendback );
			} else {
				$sendback = add_query_arg( array( 'denied' => 1, 'ids' => $user ), $sendback );
			}

			wp_redirect( $sendback );
			exit;
		}
	}

	public static function validate_status_update( $do_update, $user_id, $status ) {
		$current_status = self::get_user_status( $user_id );

		if ( $status == 'approve' ) {
			$new_status = 'approved';
		} else {
			$new_status = 'denied';
		}

		if ( $current_status == $new_status ) {
			$do_update = false;
		}

		return $do_update;
	}

	/**
	 * Add the approve or deny link where appropriate.
	 *
	 * @uses user_row_actions
	 * @param array $actions
	 * @param object $user
	 * @return array
	 */
	public static function user_table_actions( $actions, $user ) {
		if ( $user->ID == get_current_user_id() ) {
			return $actions;
		}

		if ( is_super_admin( $user->ID ) ) {
			return $actions;
		}

		$user_status = self::get_user_status( $user->ID );

		$approve_link = add_query_arg( array( 'action' => 'approve', 'user' => $user->ID ) );
		$approve_link = remove_query_arg( array( 'new_role' ), $approve_link );
		$approve_link = wp_nonce_url( $approve_link, 'wp-freeio' );

		$deny_link = add_query_arg( array( 'action' => 'deny', 'user' => $user->ID ) );
		$deny_link = remove_query_arg( array( 'new_role' ), $deny_link );
		$deny_link = wp_nonce_url( $deny_link, 'wp-freeio' );

		$approve_action = '<a href="' . esc_url( $approve_link ) . '">' . __( 'Approve', 'wp-freeio' ) . '</a>';
		$deny_action = '<a href="' . esc_url( $deny_link ) . '">' . __( 'Deny', 'wp-freeio' ) . '</a>';

		if ( $user_status == 'pending' ) {
			$actions[] = $approve_action;
			$actions[] = $deny_action;
		} else if ( $user_status == 'approved' ) {
			$actions[] = $deny_action;
		} else if ( $user_status == 'denied' ) {
			$actions[] = $approve_action;
		}

		return $actions;
	}

	/**
	 * Add the status column to the user table
	 *
	 * @uses manage_users_columns
	 * @param array $columns
	 * @return array
	 */
	public static function add_column( $columns ) {
		$the_columns['user_status'] = __( 'Status', 'wp-freeio' );

		$newcol = array_slice( $columns, 0, -1 );
		$newcol = array_merge( $newcol, $the_columns );
		$columns = array_merge( $newcol, array_slice( $columns, 1 ) );

		return $columns;
	}

	/**
	 * Show the status of the user in the status column
	 *
	 * @uses manage_users_custom_column
	 * @param string $val
	 * @param string $column_name
	 * @param int $user_id
	 * @return string
	 */
	public static function status_column( $val, $column_name, $user_id ) {
		switch ( $column_name ) {
			case 'user_status' :
				$status = self::get_user_status( $user_id );
				if ( $status == 'approved' ) {
					$status_i18n = __( 'approved', 'wp-freeio' );
				} else if ( $status == 'denied' ) {
					$status_i18n = __( 'denied', 'wp-freeio' );
				} else if ( $status == 'pending' ) {
					$status_i18n = __( 'pending', 'wp-freeio' );
				}
				return $status_i18n;
				break;

			default:
		}

		return $val;
	}

	/**
	 * Add a filter to the user table to filter by user status
	 *
	 * @uses restrict_manage_users
	 */
	public static function status_filter( $which ) {
		$id = 'wp_freeio_filter-' . $which;

		$filter_button = submit_button( __( 'Filter', 'wp-freeio' ), 'button', 'wp-freeio-status-query-submit', false, array( 'id' => 'wp-freeio-status-query-submit' ) );
		$filtered_status = null;
		if ( ! empty( $_REQUEST['wp_freeio_filter-top'] ) || ! empty( $_REQUEST['wp_freeio_filter-bottom'] ) ) {
			$filtered_status = esc_attr( ( ! empty( $_REQUEST['wp_freeio_filter-top'] ) ) ? $_REQUEST['wp_freeio_filter-top'] : $_REQUEST['wp_freeio_filter-bottom'] );
		}
		$statuses = array('pending', 'approved', 'denied');
		?>
		<label class="screen-reader-text" for="<?php echo esc_attr($id); ?>"><?php _e( 'View all users', 'wp-freeio' ); ?></label>
		<select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" style="float: none; margin: 0 0 0 15px;">
			<option value=""><?php _e( 'View all users', 'wp-freeio' ); ?></option>
		<?php foreach ( $statuses as $status ) : ?>
			<option value="<?php echo esc_attr( $status ); ?>"<?php selected( $status, $filtered_status ); ?>><?php echo esc_html( $status ); ?></option>
		<?php endforeach; ?>
		</select>
		<?php echo apply_filters( 'wp_freeio_filter_button', $filter_button ); ?>
		<style>
			#wp-freeio-status-query-submit {
				float: right;
				margin: 2px 0 0 5px;
			}
		</style>
	<?php
	}

	/**
	 * Modify the user query if the status filter is being used.
	 *
	 * @uses pre_user_query
	 * @param $query
	 */
    public static function filter_by_status( $query ) {
		global $wpdb;

		if ( !is_admin() ) {
			return;
		}
		
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();
		if ( isset( $screen ) && 'users' != $screen->id ) {
			return;
		}
		$filter = null;
		if ( ! empty( $_REQUEST['wp_freeio_filter-top'] ) || ! empty( $_REQUEST['wp_freeio_filter-bottom'] ) ) {
			$filter = esc_attr( ( ! empty( $_REQUEST['wp_freeio_filter-top'] ) ) ? $_REQUEST['wp_freeio_filter-top'] : $_REQUEST['wp_freeio_filter-bottom'] );
		}
		if ( $filter != null ) {

			if ( 'approved' == $filter ) {
				$meta_query = array(
					'relation' => 'OR',
					array(
						'key' => 'user_account_status',
						'value' => $filter,
						'compare' => 'LIKE',
					),
					array(
						'key' => 'user_account_status',
						'value' => '',
					),
					array(
						'key' => 'user_account_status',
						'compare' => 'NOT EXISTS',
					)
				);
				$query->set('meta_query', $meta_query);
			} else {
				$meta_query = array (array (
					'key' => 'user_account_status',
					'value' => $filter,
					'compare' => 'LIKE'
				));
				$query->set('meta_query', $meta_query);
			}
		}
	}

	public static function register_msg($user) {
		$requires_approval = 'auto';
		if ( in_array('wp_freeio_freelancer', $user->roles) ) {
			$requires_approval = wp_freeio_get_option('freelancers_requires_approval', 'auto');
		} else {
			$requires_approval = wp_freeio_get_option('employers_requires_approval', 'auto');
		}

		if ( $requires_approval == 'email_approve' ) {
			$return = __('Registration complete. Before you can login, you must activate your account sent to your email address.', 'wp-freeio');
		} elseif ( $requires_approval == 'admin_approve' ) {
			$return = __('Registration complete. Your account has to be confirmed by an administrator before you can login', 'wp-freeio');
		} else {
			$return = __('Your account has to be confirmed yet.', 'wp-freeio');
		}

		return apply_filters('wp-freeio-get-register-msg', $return, $requires_approval);
	}
	
	public static function login_msg($user) {
		$requires_approval = 'auto';
		if ( in_array('wp_freeio_freelancer', $user->roles) ) {
			$requires_approval = wp_freeio_get_option('freelancers_requires_approval', 'auto');
		} else {
			$requires_approval = wp_freeio_get_option('employers_requires_approval', 'auto');
		}
		
		if ( $requires_approval == 'email_approve' ) {
			$return = sprintf(__('Account account has not confirmed yet, you must activate your account with the link sent to your email address. If you did not receive this email, please check your junk/spam folder. <a href="javascript:void(0);" class="wp-freeio-resend-approve-account-btn" data-login="%s">Click here</a> to resend the activation email.', 'wp-freeio'), $user->user_login );
		} elseif ( $requires_approval == 'admin_approve' ) {
			$return = __('Your account has to be confirmed by an administrator before you can login.', 'wp-freeio');
		} else {
			$return = __('Your account has to be confirmed yet.', 'wp-freeio');
		}

		return apply_filters('wp-freeio-get-login-msg', $return, $requires_approval);
	}
	
	public static function switch_user_role() {
		global $wpdb;
		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to switch user role', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$current_user_id = WP_Freeio_User::get_user_id();
		$userdata = get_userdata($current_user_id);
		$roles = $userdata->roles;


		if (!empty($roles[0]) && $roles[0] === 'wp_freeio_freelancer') {
			$new_role	= 'wp_freeio_employer';
			$user_post_type	= 'employer';
			$freelancer_id = self::get_freelancer_by_user_id($current_user_id);

			$prefix = WP_FREEIO_EMPLOYER_PREFIX;

        	$post_title = str_replace(array('-', '_'), array(' ', ' '), $userdata->display_name);
        	$freelancer_name = get_post_field('post_title', $freelancer_id);
        	if ( $freelancer_name ) {
        		$post_title = $freelancer_name;
        	}
        	$post_content = get_post_field('post_content', $freelancer_id);
        	$verified = get_post_meta($employer_id, WP_FREEIO_FREELANCER_PREFIX.'verified', true);
		} else if (!empty($roles[0]) && $roles[0] === 'wp_freeio_employer') {
			$new_role	= 'wp_freeio_freelancer';
			$user_post_type	= 'freelancer';
			$employer_id = self::get_employer_by_user_id($current_user_id);
			$prefix = WP_FREEIO_FREELANCER_PREFIX;
        	

        	$post_title = str_replace(array('-', '_'), array(' ', ' '), $userdata->display_name);
        	$employer_name = get_post_field('post_title', $employer_id);
        	if ( $employer_name ) {
        		$post_title = $employer_name;
        	}
        	$post_content = get_post_field('post_content', $employer_id);
        	$verified = get_post_meta($employer_id, WP_FREEIO_EMPLOYER_PREFIX.'verified', true);
		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('Can not switch user role', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$switch_user_id	= get_user_meta($current_user_id, 'switch_user_id', true);
		
		$switch_user_id	=  !empty($switch_user_id) ? intval($switch_user_id) : '';
		
		$count_user	= 0;
		if ( !empty($switch_user_id) ) {
			$count_user = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $switch_user_id));
		}

		if ( empty($switch_user_id) || empty($count_user) ) {
			$user_login = sanitize_title( $userdata->user_login.'_'.$userdata->ID );
			$defaul_user_name = $user_login;
			
			$i = 1;
			while ( username_exists( $user_login ) ) {
				$user_login = $defaul_user_name . $i;
				$i++;
			}
			
			$query = "INSERT INTO ".$wpdb->prefix."users (`user_login`, `user_pass`, `user_nicename`, `user_email`, `display_name`) VALUES ('".$user_login."', '".$userdata->user_pass."', '".$userdata->display_name."', '".$userdata->user_email."', '".$userdata->display_name."')";
	
			$user_update = $wpdb->query( $wpdb->prepare($query) );
			
			$new_user_id = $wpdb->insert_id;

			wp_update_user( array(
				'ID' => esc_sql( $new_user_id ),
				'role' => $new_role,
				'user_status' => 1
			) );

			$first_name	= get_user_meta( $current_user_id, 'first_name',true );
			$last_name = get_user_meta( $current_user_id, 'last_name',true );
			$full_name = get_user_meta( $current_user_id, 'full_name',true );

			update_user_meta( $new_user_id, 'first_name', $first_name );
            update_user_meta( $new_user_id, 'last_name', $last_name );             

			update_user_meta($new_user_id, 'show_admin_bar_front', false);
            update_user_meta($new_user_id, 'full_name', esc_html($full_name));

            $post_args = array(
                'post_title' => $post_title,
                'post_type' => $user_post_type,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => $new_user_id,
                'post_content' => $post_content,
            );

            if ( $new_role == 'wp_freeio_employer' ) {
	            if ( wp_freeio_get_option('employer_slug_employer') == 'number' ) {
	            	$rand_num = mt_rand(100000000000,999999999999);
	            	$post_args['post_name'] = $rand_num;
	            }
	            $post_args = apply_filters('wp-freeio-create-employer-post-args', $post_args, $userdata);
            } else {
            	if ( wp_freeio_get_option('freelancer_slug_freelancer') == 'number' ) {
	            	$rand_num = mt_rand(100000000000,999999999999);
	            	$post_args['post_name'] = $rand_num;
	            }

	            $post_args = apply_filters('wp-freeio-create-freelancer-post-args', $post_args, $userdata);
            }

            remove_action( 'save_post', array( __CLASS__, 'auto_generate_user' ), 100, 3 );
            $new_post_id = wp_insert_post($post_args);
            add_action( 'save_post', array( __CLASS__, 'auto_generate_user' ), 100, 3 );
            if ( $new_post_id ) {
            	
            	update_user_meta($current_user_id, 'switch_user_id', $new_user_id); 
				update_user_meta($new_user_id, 'switch_user_id', $current_user_id); 
				
	            update_post_meta($new_post_id, $prefix . 'user_id', $new_user_id);
	            update_post_meta($new_post_id, $prefix . 'email', $userdata->user_email);
	            update_post_meta($new_post_id, $prefix . 'show_profile', 'show');
	            update_post_meta($new_post_id, $prefix . 'verified', $verified);

	            //
	            if ( $new_role == 'wp_freeio_employer' ) {
		            update_user_meta($new_user_id, 'employer_id', $new_post_id);
		        } else {
		        	update_user_meta($new_user_id, 'freelancer_id', $new_post_id);
		        }

		        $user = get_user_by('ID', $new_user_id );
				wp_clear_auth_cookie();
				wp_set_current_user( $user->ID );
				wp_set_auth_cookie( $user->ID );

				$return = array( 'status' => true, 'msg' => esc_html__('Switch user role successfully', 'wp-freeio') );
			   	wp_send_json($return);
	        } else {
	        	$return = array( 'status' => false, 'msg' => esc_html__('Switch user role error', 'wp-freeio') );
			   	wp_send_json($return);
	        }
		} else {
			$user = get_user_by('ID', $switch_user_id );
			wp_clear_auth_cookie();
			wp_set_current_user( $user->ID );
			wp_set_auth_cookie( $user->ID );

			$return = array( 'status' => true, 'msg' => esc_html__('Switch user role successfully', 'wp-freeio') );
	   		wp_send_json($return);
		}
	}

}

WP_Freeio_User::init();