<?php
/**
 * Shortcodes
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Shortcodes {
	/**
	 * Initialize shortcodes
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
	    add_action( 'wp', array( __CLASS__, 'check_logout' ) );

	    // login | register
		add_shortcode( 'wp_freeio_logout', array( __CLASS__, 'logout' ) );
	    add_shortcode( 'wp_freeio_login', array( __CLASS__, 'login' ) );
	    add_shortcode( 'wp_freeio_register', array( __CLASS__, 'register' ) );
	    add_shortcode( 'wp_freeio_register_freelancer', array( __CLASS__, 'register_freelancer' ) );
	    add_shortcode( 'wp_freeio_register_employer', array( __CLASS__, 'register_employer' ) );

	    // profile
	    add_shortcode( 'wp_freeio_user_dashboard', array( __CLASS__, 'user_dashboard' ) );
	    add_shortcode( 'wp_freeio_change_password', array( __CLASS__, 'change_password' ) );
	    add_shortcode( 'wp_freeio_change_profile', array( __CLASS__, 'change_profile' ) );
	    add_shortcode( 'wp_freeio_change_resume', array( __CLASS__, 'change_resume' ) );
	    add_shortcode( 'wp_freeio_delete_profile', array( __CLASS__, 'delete_profile' ) );
	    add_shortcode( 'wp_freeio_approve_user', array( __CLASS__, 'approve_user' ) );
    	
    	// submission
		add_shortcode( 'wp_freeio_submission_job', array( __CLASS__, 'submission_job' ) );
	    add_shortcode( 'wp_freeio_my_jobs', array( __CLASS__, 'my_jobs' ) );

	    add_shortcode( 'wp_freeio_submission_project', array( __CLASS__, 'submission_project' ) );
	    add_shortcode( 'wp_freeio_my_projects', array( __CLASS__, 'my_projects' ) );

	    add_shortcode( 'wp_freeio_submission_service', array( __CLASS__, 'submission_service' ) );
	    add_shortcode( 'wp_freeio_my_services', array( __CLASS__, 'my_services' ) );

	    add_shortcode( 'wp_freeio_submission_service_addon', array( __CLASS__, 'submission_service_addon' ) );

	    // employer
	    add_shortcode( 'wp_freeio_job_applicants', array( __CLASS__, 'job_applicants' ) );
	    add_shortcode( 'wp_freeio_my_freelancers_alerts', array( __CLASS__, 'my_freelancers_alerts' ) );
	    
	    add_shortcode( 'wp_freeio_my_bought_services', array( __CLASS__, 'my_bought_services' ) );
	    
	    add_shortcode( 'wp_freeio_employer_employees', array( __CLASS__, 'employer_employees' ) );
	    add_shortcode( 'wp_freeio_employer_meetings', array( __CLASS__, 'employer_meetings' ) );

	    // freelancer
	    add_shortcode( 'wp_freeio_my_proposals', array( __CLASS__, 'my_proposals' ) );
	    
	    add_shortcode( 'wp_freeio_my_applied', array( __CLASS__, 'my_applied' ) );
	    add_shortcode( 'wp_freeio_my_jobs_alerts', array( __CLASS__, 'my_jobs_alerts' ) );

	    add_shortcode( 'wp_freeio_payouts', array( __CLASS__, 'payouts' ) );
	    add_shortcode( 'wp_freeio_statements', array( __CLASS__, 'statements' ) );

	    add_shortcode( 'wp_freeio_freelancer_meetings', array( __CLASS__, 'freelancer_meetings' ) );

	    add_shortcode( 'wp_freeio_verify_identity', array( __CLASS__, 'verify_identity' ) );

	    add_shortcode( 'wp_freeio_dispute', array( __CLASS__, 'dispute' ) );


	    add_shortcode( 'wp_freeio_jobs', array( __CLASS__, 'jobs' ) );
	    add_shortcode( 'wp_freeio_services', array( __CLASS__, 'services' ) );
	    add_shortcode( 'wp_freeio_projects', array( __CLASS__, 'projects' ) );
	    add_shortcode( 'wp_freeio_employers', array( __CLASS__, 'employers' ) );
	    add_shortcode( 'wp_freeio_freelancers', array( __CLASS__, 'freelancers' ) );
	}

	/**
	 * Logout checker
	 *
	 * @access public
	 * @param $wp
	 * @return void
	 */
	public static function check_logout( $wp ) {
		$post = get_post();

		if ( is_page() ) {
			if ( has_shortcode( $post->post_content, 'wp_freeio_logout' ) ) {
				wp_safe_redirect( str_replace( '&amp;', '&', wp_logout_url( home_url( '/' ) ) ) );
				exit();
			} elseif ( has_shortcode( $post->post_content, 'wp_freeio_my_jobs' ) ) {
				self::my_jobs_hanlder();
			} elseif ( has_shortcode( $post->post_content, 'wp_freeio_my_projects' ) ) {
				self::my_projects_hanlder();
			} elseif ( has_shortcode( $post->post_content, 'wp_freeio_my_services' ) ) {
				self::my_services_hanlder();
			}
		}

		if ( !empty($_GET['register_msg']) && ($user_data = get_userdata($_GET['register_msg'])) ) {
			$user_login_auth = WP_Freeio_User::get_user_status($user_data);
        	if ( $user_login_auth == 'pending' ) {
				$jsondata = array(
	                'error' => false,
	                'msg' => WP_Freeio_User::register_msg($user_data),
	            );
	            $_SESSION['register_msg'] = $jsondata;
			} elseif ( $user_login_auth == 'denied' ) {
	            $jsondata = array(
	                'status' => false,
	                'msg' => __('Your account denied', 'wp-freeio')
	            );
	            $_SESSION['register_msg'] = $jsondata;
	        }
		}
	}

	/**
	 * Logout
	 *
	 * @access public
	 * @return void
	 */
	public static function logout( $atts ) {}

	/**
	 * Login
	 *
	 * @access public
	 * @return string
	 */
	public static function login( $atts ) {
		if ( is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/loged-in' );
	    }
		return WP_Freeio_Template_Loader::get_template_part( 'misc/login', $atts );
	}

	/**
	 * Register
	 *
	 * @access public
	 * @return string
	 */
	public static function register( $atts ) {
		if ( is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/loged-in' );
	    }
		return WP_Freeio_Template_Loader::get_template_part( 'misc/register', $atts );
	}

	/**
	 * Register Freelancer
	 *
	 * @access public
	 * @return string
	 */
	public static function register_freelancer( $atts ) {
		if ( is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/loged-in' );
	    }
	    $form = WP_Freeio_Freelancer_Register_Form::get_instance();

		return $form->form_output();
	}

	/**
	 * Register Employer
	 *
	 * @access public
	 * @return string
	 */
	public static function register_employer( $atts ) {
		if ( is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/loged-in' );
	    }
	    $form = WP_Freeio_Employer_Register_Form::get_instance();

		return $form->form_output();
	}

	/**
	 * Submission index
	 *
	 * @access public
	 * @return string|void
	 */
	public static function submission_job( $atts ) {
	    if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } else {
	    	$user_id = get_current_user_id();
	    	if ( WP_Freeio_User::is_employee($user_id) ) {
	    		if ( !WP_Freeio_User::is_employee_can_add_submission($user_id) ) {
	    			return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	    		}
	    	} elseif ( !WP_Freeio_User::is_employer($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
			}
	    }
	    
		$form = WP_Freeio_Job_Submit_Form::get_instance();

		return $form->output();
	}

	public static function edit_form( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_employer() || (WP_Freeio_User::is_employee() && wp_freeio_get_option('employee_edit_job') == 'on')  ) {
	    	$user_id = WP_Freeio_User::get_user_id();
	    	if ( empty($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
			}
			$form = WP_Freeio_Job_Edit_Form::get_instance();

			return $form->output();
		}

		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	}
	
	public static function my_jobs_hanlder() {
		$action = !empty($_REQUEST['action']) ? sanitize_title( $_REQUEST['action'] ) : '';
		$job_id = isset( $_REQUEST['job_id'] ) ? absint( $_REQUEST['job_id'] ) : 0;

		if ( $action == 'relist' || $action == 'continue' ) {
			$submit_form_page_id = wp_freeio_get_option('submit_job_form_page_id');
			if ( $submit_form_page_id ) {
				$submit_page_url = get_permalink($submit_form_page_id);
				wp_safe_redirect( add_query_arg( array( 'job_id' => absint( $job_id ), 'action' => $action ), $submit_page_url ) );
				exit;
			}
			
		}
	}

	public static function my_jobs( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_employer() || (WP_Freeio_User::is_employee() && wp_freeio_get_option('employee_view_my_jobs') == 'on') ) {
	    	$user_id = WP_Freeio_User::get_user_id();
	    	if ( empty($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
			}
			if ( ! empty( $_REQUEST['action'] ) ) {
				$action = sanitize_title( $_REQUEST['action'] );
				
				if ( $action == 'edit' ) {
					return self::edit_form($atts);
				}
			}
			return WP_Freeio_Template_Loader::get_template_part( 'submission/my-jobs' );
		}
		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	}
	
	/**
	 * Submission index
	 *
	 * @access public
	 * @return string|void
	 */
	public static function submission_project( $atts ) {
	    if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } else {
	    	$user_id = get_current_user_id();
	    	if ( WP_Freeio_User::is_employee($user_id) ) {
	    		if ( !WP_Freeio_User::is_employee_can_add_submission($user_id) ) {
	    			return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	    		}
	    	} elseif ( !WP_Freeio_User::is_employer($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
			}
	    }
	    
		$form = WP_Freeio_Project_Submit_Form::get_instance();

		return $form->output();
	}

	public static function my_projects_hanlder() {
		$action = !empty($_REQUEST['action']) ? sanitize_title( $_REQUEST['action'] ) : '';
		$project_id = isset( $_REQUEST['project_id'] ) ? absint( $_REQUEST['project_id'] ) : 0;

		if ( $action == 'relist' || $action == 'continue' ) {
			$submit_form_page_id = wp_freeio_get_option('submit_project_form_page_id');
			if ( $submit_form_page_id ) {
				$submit_page_url = get_permalink($submit_form_page_id);
				wp_safe_redirect( add_query_arg( array( 'project_id' => absint( $project_id ), 'action' => $action ), $submit_page_url ) );
				exit;
			}
			
		}
	}

	public static function edit_project_form( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_employer() || (WP_Freeio_User::is_employee() && wp_freeio_get_option('employee_edit_project') == 'on')  ) {
			$form = WP_Freeio_Project_Edit_Form::get_instance();

			return $form->output();
		}

		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	}

	public static function my_projects( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_employer() || (WP_Freeio_User::is_employee() && wp_freeio_get_option('employee_view_my_projects') == 'on') ) {
	    	$user_id = WP_Freeio_User::get_user_id();
	    	if ( empty($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
			}
			if ( ! empty( $_REQUEST['action'] ) ) {
				$action = sanitize_title( $_REQUEST['action'] );
				
				if ( $action == 'edit' ) {
					return self::edit_project_form($atts);
				} elseif ( $action == 'view-proposals' ) {
					return WP_Freeio_Template_Loader::get_template_part( 'misc/my-proposals' );
				} elseif ( $action == 'view-history' ) {
					return WP_Freeio_Template_Loader::get_template_part( 'misc/my-proposal-history' );
				}
			}
			return WP_Freeio_Template_Loader::get_template_part( 'project-submission/my-projects' );
		}
		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	}

	/**
	 * Submission index
	 *
	 * @access public
	 * @return string|void
	 */
	public static function submission_service( $atts ) {
	    if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } else {
	    	$user_id = get_current_user_id();
	    	if ( !WP_Freeio_User::is_freelancer($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
			}
	    }
	    
		$form = WP_Freeio_Service_Submit_Form::get_instance();

		return $form->output();
	}

	public static function edit_service_form( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_freelancer() ) {
	    	$user_id = WP_Freeio_User::get_user_id();
	    	if ( empty($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
			}
			$form = WP_Freeio_Service_Edit_Form::get_instance();

			return $form->output();
		}

		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
	}
	
	public static function my_services_hanlder() {
		$action = !empty($_REQUEST['action']) ? sanitize_title( $_REQUEST['action'] ) : '';
		$service_id = isset( $_REQUEST['service_id'] ) ? absint( $_REQUEST['service_id'] ) : 0;

		if ( $action == 'relist' || $action == 'continue' ) {
			$submit_form_page_id = wp_freeio_get_option('submit_service_form_page_id');
			if ( $submit_form_page_id ) {
				$submit_page_url = get_permalink($submit_form_page_id);
				wp_safe_redirect( add_query_arg( array( 'service_id' => absint( $service_id ), 'action' => $action ), $submit_page_url ) );
				exit;
			}
			
		}
	}

	public static function my_services( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_freelancer() ) {
	    	$user_id = WP_Freeio_User::get_user_id();
	    	if ( empty($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
			}
			if ( ! empty( $_REQUEST['action'] ) ) {
				$action = sanitize_title( $_REQUEST['action'] );
				
				if ( $action == 'edit' ) {
					return self::edit_service_form($atts);
				} elseif ( $action == 'view-inqueue' ) {
					return WP_Freeio_Template_Loader::get_template_part( 'misc/my-services-inqueue' );
				} elseif ( $action == 'view-history' ) {
					return WP_Freeio_Template_Loader::get_template_part( 'misc/my-services-history' );
				}
			}
			return WP_Freeio_Template_Loader::get_template_part( 'service-submission/my-services' );
		}
		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
	}

	public static function submission_service_addon( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_freelancer() ) {
	    	
			return WP_Freeio_Template_Loader::get_template_part( 'service-submission/service-addons' );
		}
		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
	}
	/**
	 * Employer dashboard
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function user_dashboard( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } else {
			$user_id = get_current_user_id();
		    if ( WP_Freeio_User::is_employer($user_id) ) {
				$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
				return WP_Freeio_Template_Loader::get_template_part( 'misc/employer-dashboard', array( 'user_id' => $user_id, 'employer_id' => $employer_id ) );
			} elseif ( WP_Freeio_User::is_freelancer($user_id) ) {
				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
				return WP_Freeio_Template_Loader::get_template_part( 'misc/freelancer-dashboard', array( 'user_id' => $user_id, 'freelancer_id' => $freelancer_id ) );
			} elseif ( WP_Freeio_User::is_employee($user_id) && wp_freeio_get_option('employee_view_dashboard') == 'on' ) {
				$user_id = WP_Freeio_User::get_user_id($user_id);
				if ( empty($user_id) ) {
					return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
				}
				$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
				return WP_Freeio_Template_Loader::get_template_part( 'misc/employer-dashboard', array( 'user_id' => $user_id, 'employer_id' => $employer_id ) );
			}
	    }

    	return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
	}

	/**
	 * Change password
	 *
	 * @access public
	 * @param $atts
	 * @return string
	 */
	public static function change_password( $atts ) {
		if ( ! is_user_logged_in() ) {
			return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
		}

		return WP_Freeio_Template_Loader::get_template_part( 'misc/password-form' );
	}

	/**
	 * Change profile
	 *
	 * @access public
	 * @param $atts
	 * @return void
	 */
	public static function change_profile( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    }
	    
	    $metaboxes = apply_filters( 'cmb2_meta_boxes', array() );
	    $metaboxes_form = array();
	    $user_id = get_current_user_id();
	    if ( WP_Freeio_User::is_employer($user_id) ) {
	    	if ( ! isset( $metaboxes[ WP_FREEIO_EMPLOYER_PREFIX . 'front' ] ) ) {
				return __( 'A metabox with the specified \'metabox_id\' doesn\'t exist.', 'wp-freeio' );
			}
			$metaboxes_form = $metaboxes[ WP_FREEIO_EMPLOYER_PREFIX . 'front' ];
			$post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
	    } elseif( WP_Freeio_User::is_freelancer($user_id) ) {
	    	if ( ! isset( $metaboxes[ WP_FREEIO_FREELANCER_PREFIX . 'front' ] ) ) {
				return __( 'A metabox with the specified \'metabox_id\' doesn\'t exist.', 'wp-freeio' );
			}
			$metaboxes_form = $metaboxes[ WP_FREEIO_FREELANCER_PREFIX . 'front' ];
			$post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
	    } elseif ( WP_Freeio_User::is_employee($user_id) && wp_freeio_get_option('employee_edit_employer_profile') == 'on' ) {
	    	$user_id = WP_Freeio_User::get_user_id($user_id);
	    	if ( empty($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
			}

	    	if ( ! isset( $metaboxes[ WP_FREEIO_EMPLOYER_PREFIX . 'front' ] ) ) {
				return __( 'A metabox with the specified \'metabox_id\' doesn\'t exist.', 'wp-freeio' );
			}
			$metaboxes_form = $metaboxes[ WP_FREEIO_EMPLOYER_PREFIX . 'front' ];
			$post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
	    } else {
	    	return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }

		if ( !$post_id ) {
			return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
		}

		wp_enqueue_script('google-maps');
		wp_enqueue_script('wpfi-select2');
		wp_enqueue_style('wpfi-select2');
		
		return WP_Freeio_Template_Loader::get_template_part( 'misc/profile-form', array('post_id' => $post_id, 'metaboxes_form' => $metaboxes_form ) );
	}

	public static function change_resume( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_freelancer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
	    }
	    
	    $metaboxes = apply_filters( 'cmb2_meta_boxes', array() );
	    $metaboxes_form = array();
	    $user_id = WP_Freeio_User::get_user_id();
	    
    	if ( ! isset( $metaboxes[ WP_FREEIO_FREELANCER_PREFIX . 'resume_front' ] ) ) {
			return __( 'A metabox with the specified \'metabox_id\' doesn\'t exist.', 'wp-freeio' );
		}
		$metaboxes_form = $metaboxes[ WP_FREEIO_FREELANCER_PREFIX . 'resume_front' ];
		$post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
		
		if ( !$post_id ) {
			return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
		}

		wp_enqueue_script('google-maps');
		wp_enqueue_script('wpfi-select2');
		wp_enqueue_style('wpfi-select2');

		return WP_Freeio_Template_Loader::get_template_part( 'misc/resume-form', array('post_id' => $post_id, 'metaboxes_form' => $metaboxes_form ) );
	}

	public static function delete_profile($atts) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_employee() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }
	    $user_id = get_current_user_id();
	    return WP_Freeio_Template_Loader::get_template_part( 'misc/delete-profile-form', array('user_id' => $user_id) );
	}

	public static function approve_user($atts) {
	    return WP_Freeio_Template_Loader::get_template_part( 'misc/approve-user' );
	}

	public static function job_applicants( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_employer() || (WP_Freeio_User::is_employee() && wp_freeio_get_option('employee_view_applications') == 'on') ) {
		   
		    $user_id = WP_Freeio_User::get_user_id();
		    if ( empty($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
			}

			$jobs_loop = new WP_Query( array(
				'post_type' => 'job_listing',
				'fields' => 'ids',
				'author' => $user_id,
				'orderby' => 'date',
				'order' => 'DESC',
				'posts_per_page' => -1,
			));

			$job_ids = array();
			if ( !empty($jobs_loop) && !empty($jobs_loop->posts) ) {
				$job_ids = $jobs_loop->posts;
			}

			return WP_Freeio_Template_Loader::get_template_part( 'misc/job-applicants', array( 'job_ids' => $job_ids ) );

	    }
	    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	}

	public static function my_freelancers_alerts( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_employer() || (WP_Freeio_User::is_employee() && wp_freeio_get_option('employee_view_freelancer_alert') == 'on') ) {
		    
		    $user_id = WP_Freeio_User::get_user_id();
		    if ( empty($user_id) ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
			}
		    if ( get_query_var( 'paged' ) ) {
			    $paged = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
			    $paged = get_query_var( 'page' );
			} else {
			    $paged = 1;
			}
			$query_vars = array(
			    'post_type' => 'freelancer_alert',
			    'posts_per_page'    => get_option('posts_per_page'),
			    'paged'    			=> $paged,
			    'post_status' => 'publish',
			    'fields' => 'ids',
			    'author' => $user_id,
			);
			if ( isset($_GET['search']) ) {
				$query_vars['s'] = $_GET['search'];
			}
			if ( isset($_GET['orderby']) ) {
				switch ($_GET['orderby']) {
					case 'menu_order':
						$query_vars['orderby'] = array(
							'menu_order' => 'ASC',
							'date'       => 'DESC',
							'ID'         => 'DESC',
						);
						break;
					case 'newest':
						$query_vars['orderby'] = 'date';
						$query_vars['order'] = 'DESC';
						break;
					case 'oldest':
						$query_vars['orderby'] = 'date';
						$query_vars['order'] = 'ASC';
						break;
				}
			}

			$alerts = WP_Freeio_Query::get_posts($query_vars);

			return WP_Freeio_Template_Loader::get_template_part( 'misc/my-freelancers-alerts', array( 'alerts' => $alerts ) );
		}
		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	}

	public static function my_proposals( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_freelancer()) {

	    	if ( ! empty( $_REQUEST['action'] ) ) {
				$action = sanitize_title( $_REQUEST['action'] );
				
				if ( $action == 'view-history' ) {
					return WP_Freeio_Template_Loader::get_template_part( 'misc/my-proposal-freelancer-history' );
				}
			}
			return WP_Freeio_Template_Loader::get_template_part( 'misc/my-proposals-freelancer' );
		}
		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
	}

	public static function my_bought_services( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( WP_Freeio_User::is_employer() || (WP_Freeio_User::is_employee() && wp_freeio_get_option('employee_view_my_bought_service') == 'on') ) {
	    	if ( ! empty( $_REQUEST['action'] ) ) {
				$action = sanitize_title( $_REQUEST['action'] );
				
				if ( $action == 'view-history' ) {
					return WP_Freeio_Template_Loader::get_template_part( 'misc/my-services-employer-history' );
				}
			}
			return WP_Freeio_Template_Loader::get_template_part( 'misc/my-services-employer' );
		}

		return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
	}

	public static function employer_employees( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_employer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	    }

	    return WP_Freeio_Template_Loader::get_template_part( 'misc/employer-employees' );
	}

	public static function employer_meetings($atts) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_employer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'employer') );
	    }

	    return WP_Freeio_Template_Loader::get_template_part( 'misc/employer-meetings' );
	}

	public static function my_applied( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_freelancer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
	    }

	    $user_id = WP_Freeio_User::get_user_id();
		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);

		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}
		
		$freelancer_ids = apply_filters( 'wp-freeio-translations-post-ids', $freelancer_id );
		if ( !is_array($freelancer_ids) ) {
			$freelancer_ids = array($freelancer_ids);
		}
		$freelancer_ids = array_merge(array(0), $freelancer_ids);
		$query_vars = array(
		    'post_type' => 'job_applicant',
		    'posts_per_page'    => get_option('posts_per_page'),
		    'paged'    			=> $paged,
		    'post_status' => 'publish',
		    'fields' => 'ids',
		    'meta_query' => array(
		    	array(
			    	'key' => WP_FREEIO_APPLICANT_PREFIX . 'freelancer_id',
			    	'value' => $freelancer_ids,
			    	'compare' => 'IN',
			    ),
			    
			)
		);
		if ( isset($_GET['search']) ) {
			$meta_query = $query_vars['meta_query'];
			$meta_query[] = array(
		    	'key' => WP_FREEIO_APPLICANT_PREFIX . 'job_name',
		    	'value' => $_GET['search'],
		    	'compare' => 'LIKE',
		    );
			$query_vars['meta_query'] = $meta_query;
		}
		if ( isset($_GET['orderby']) ) {
			switch ($_GET['orderby']) {
				case 'menu_order':
					$query_vars['orderby'] = array(
						'menu_order' => 'ASC',
						'date'       => 'DESC',
						'ID'         => 'DESC',
					);
					break;
				case 'newest':
					$query_vars['orderby'] = 'date';
					$query_vars['order'] = 'DESC';
					break;
				case 'oldest':
					$query_vars['orderby'] = 'date';
					$query_vars['order'] = 'ASC';
					break;
			}
		}
		$applicants = WP_Freeio_Query::get_posts($query_vars);

		return WP_Freeio_Template_Loader::get_template_part( 'misc/jobs-applied', array( 'applicants' => $applicants ) );
	}

	public static function my_jobs_alerts( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_freelancer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
	    }

	    $user_id = WP_Freeio_User::get_user_id();
	    if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_vars = array(
		    'post_type' => 'job_alert',
		    'posts_per_page'    => get_option('posts_per_page'),
		    'paged'    			=> $paged,
		    'post_status' => 'publish',
		    'fields' => 'ids',
		    'author' => $user_id,
		);
		if ( isset($_GET['search']) ) {
			$query_vars['s'] = $_GET['search'];
		}
		if ( isset($_GET['orderby']) ) {
			switch ($_GET['orderby']) {
				case 'menu_order':
					$query_vars['orderby'] = array(
						'menu_order' => 'ASC',
						'date'       => 'DESC',
						'ID'         => 'DESC',
					);
					break;
				case 'newest':
					$query_vars['orderby'] = 'date';
					$query_vars['order'] = 'DESC';
					break;
				case 'oldest':
					$query_vars['orderby'] = 'date';
					$query_vars['order'] = 'ASC';
					break;
			}
		}
		$alerts = WP_Freeio_Query::get_posts($query_vars);

		return WP_Freeio_Template_Loader::get_template_part( 'misc/my-jobs-alerts', array( 'alerts' => $alerts ) );
	}

	public static function payouts( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_freelancer() && !WP_Freeio_User::is_employer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }

		return WP_Freeio_Template_Loader::get_template_part( 'misc/payouts' );
	}

	public static function statements( $atts ) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_freelancer() && !WP_Freeio_User::is_employer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }
	    
		return WP_Freeio_Template_Loader::get_template_part( 'misc/statements' );
	}

	public static function freelancer_meetings($atts) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_freelancer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed', array('need_role' => 'freelancer') );
	    }

	    return WP_Freeio_Template_Loader::get_template_part( 'misc/freelancer-meetings' );
	}

	public static function verify_identity($atts) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_freelancer() && !WP_Freeio_User::is_employer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }

	    return WP_Freeio_Template_Loader::get_template_part( 'misc/verify-identity' );
	}

	public static function dispute($atts) {
		if ( ! is_user_logged_in() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/need-login' );
	    } elseif ( !WP_Freeio_User::is_freelancer() && !WP_Freeio_User::is_employer() ) {
		    return WP_Freeio_Template_Loader::get_template_part( 'misc/not-allowed' );
	    }

	    if ( ! empty( $_REQUEST['action'] ) ) {
			$action = sanitize_title( $_REQUEST['action'] );
			
			if ( $action == 'view-detail' ) {
				return WP_Freeio_Template_Loader::get_template_part( 'misc/dispute-detail' );
			}
		}
	    return WP_Freeio_Template_Loader::get_template_part( 'misc/dispute' );
	}
	
	public static function jobs( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'limit' => wp_freeio_get_option('number_jobs_per_page', 10),
			'post__in' => array(),
			'categories' => array(),
			'types' => array(),
			'locations' => array(),
		));

		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_args = array(
			'post_type' => 'job_listing',
		    'post_status' => 'publish',
		    'post_per_page' => $atts['limit'],
		    'paged' => $paged,
		);

		$params = array();
		if (WP_Freeio_Abstract_Filter::has_filter($atts)) {
			$params = $atts;
		}
		if ( WP_Freeio_Job_Filter::has_filter() ) {
			$params = array_merge($params, $_GET);
		}

		$jobs = WP_Freeio_Query::get_posts($query_args, $params);
		// echo "<pre>".print_r($jobs,1); die;
		return WP_Freeio_Template_Loader::get_template_part( 'misc/jobs', array( 'jobs' => $jobs, 'atts' => $atts ) );
	}

	public static function services( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'limit' => wp_freeio_get_option('number_services_per_page', 10),
			'post__in' => array(),
			'categories' => array(),
			'types' => array(),
			'locations' => array(),
		));

		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_args = array(
			'post_type' => 'service',
		    'post_status' => 'publish',
		    'post_per_page' => $atts['limit'],
		    'paged' => $paged,
		);

		$params = array();
		if (WP_Freeio_Abstract_Filter::has_filter($atts)) {
			$params = $atts;
		}
		if ( WP_Freeio_Service_Filter::has_filter() ) {
			$params = array_merge($params, $_GET);
		}

		$services = WP_Freeio_Query::get_posts($query_args, $params);

		return WP_Freeio_Template_Loader::get_template_part( 'misc/services', array( 'services' => $services, 'atts' => $atts ) );
	}

	public static function projects( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'limit' => wp_freeio_get_option('number_projects_per_page', 10),
			'post__in' => array(),
			'categories' => array(),
			'types' => array(),
			'locations' => array(),
		));

		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_args = array(
			'post_type' => 'project',
		    'post_status' => 'publish',
		    'post_per_page' => $atts['limit'],
		    'paged' => $paged,
		);

		$params = array();
		if (WP_Freeio_Abstract_Filter::has_filter($atts)) {
			$params = $atts;
		}
		if ( WP_Freeio_Project_Filter::has_filter() ) {
			$params = array_merge($params, $_GET);
		}

		$projects = WP_Freeio_Query::get_posts($query_args, $params);

		return WP_Freeio_Template_Loader::get_template_part( 'misc/projects', array( 'projects' => $projects, 'atts' => $atts ) );
	}

	public static function employers( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'limit' => wp_freeio_get_option('number_employers_per_page', 10),
			'post__in' => array(),
			'categories' => array(),
			'types' => array(),
			'locations' => array(),
		));

		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_args = array(
			'post_type' => 'employer',
		    'post_status' => 'publish',
		    'post_per_page' => $atts['limit'],
		    'paged' => $paged,
		);

		$params = array();
		if (WP_Freeio_Abstract_Filter::has_filter($atts)) {
			$params = $atts;
		}
		if ( WP_Freeio_Employer_Filter::has_filter() ) {
			$params = array_merge($params, $_GET);
		}

		$employers = WP_Freeio_Query::get_posts($query_args, $params);
		
		return WP_Freeio_Template_Loader::get_template_part( 'misc/employers', array( 'employers' => $employers, 'atts' => $atts ) );
	}

	public static function freelancers( $atts ) {
		$atts = wp_parse_args( $atts, array(
			'limit' => wp_freeio_get_option('number_freelancers_per_page', 10),
			'post__in' => array(),
			'categories' => array(),
			'types' => array(),
			'locations' => array(),
		));

		if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_args = array(
			'post_type' => 'freelancer',
		    'post_status' => 'publish',
		    'post_per_page' => $atts['limit'],
		    'paged' => $paged,
		);
		$params = array();
		if (WP_Freeio_Abstract_Filter::has_filter($atts)) {
			$params = $atts;
		}
		if ( WP_Freeio_Freelancer_Filter::has_filter() ) {
			$params = array_merge($params, $_GET);
		}

		$freelancers = WP_Freeio_Query::get_posts($query_args, $params);
		return WP_Freeio_Template_Loader::get_template_part( 'misc/freelancers', array( 'freelancers' => $freelancers, 'atts' => $atts ) );
	}
}

WP_Freeio_Shortcodes::init();
