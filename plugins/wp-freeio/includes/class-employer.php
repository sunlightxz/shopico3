<?php
/**
 * Employer
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Employer {
	
	public static function init() {
		// Ajax endpoints.
		add_action( 'wpfi_ajax_wp_freeio_ajax_employer_add_employee', array( __CLASS__, 'add_employee' ) );
		add_action( 'wpfi_ajax_wp_freeio_ajax_employer_remove_employee', array( __CLASS__, 'remove_employee' ) );

		// invite freelancer
		add_action( 'wpfi_ajax_wp_freeio_ajax_invite_freelancer',  array(__CLASS__, 'process_invite_freelancer') );
		
		add_action( 'wp_freeio_before_employer_archive', array( __CLASS__, 'display_employers_results_filters' ), 3 );

		add_action( 'wp_freeio_before_employer_archive', array( __CLASS__, 'display_employers_results_count_orderby_start' ), 5 );
		add_action( 'wp_freeio_before_employer_archive', array( __CLASS__, 'display_employers_count_results' ), 10 );
		add_action( 'wp_freeio_before_employer_archive', array( __CLASS__, 'display_employers_orderby' ), 15 );
		add_action( 'wp_freeio_before_employer_archive', array( __CLASS__, 'display_employers_results_count_orderby_end' ), 100 );

		// restrict
		add_filter( 'wp-freeio-employer-query-args', array( __CLASS__, 'employer_restrict_listing_query_args'), 100, 2 );
		add_filter( 'wp-freeio-employer-filter-query', array( __CLASS__, 'employer_restrict_listing_query'), 100, 2 );

		add_action( 'wp_freeio_after_employer_archive', array( __CLASS__, 'restrict_employer_listing_information' ), 10 );

		add_action( 'template_redirect', array( __CLASS__, 'track_job_view' ), 20 );
	}

	public static function get_post_meta($post_id, $key, $single = true) {
		return get_post_meta($post_id, WP_FREEIO_EMPLOYER_PREFIX.$key, $single);
	}

	public static function update_post_meta($post_id, $key, $data) {
		return update_post_meta($post_id, WP_FREEIO_EMPLOYER_PREFIX.$key, $data);
	}

	public static function track_job_view() {
	    if ( ! is_singular( 'employer' ) ) {
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

	public static function get_ajax_employees() {
		$query_args = array(
			'paged'         	=> 1,
			'number'    	=> 20,
			'orderby' => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
				'ID'         => 'DESC',
			),
			'order' => 'DESC',
			'role__in' => array('wp_freeio_employee'),
			'search_columns' => array( 'user_login', 'user_email' )
		);
		if ( !empty($_REQUEST['q']) ) {
			$query_args['search'] = '*'.$_REQUEST['q'].'*';
		}
		$user_id = WP_Freeio_User::get_user_id();
		$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
		$employees = self::get_post_meta($employer_id, 'employees', false);
		if ( !empty($employees) ) {
			$query_args['exclude'] = $employees;
		}

		$users = get_users( $query_args );
		$return = array();
		if ( !empty($users) ) {
			foreach ($users as $user) {
				$return[] = array(
					'value' => $user->ID,
					'label' => $user->display_name,
					'img' => get_avatar($user->ID),
				);
			}
		}
		echo json_encode($return);
		exit();
	}

	public static function add_employee() {
		global $reg_errors;
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-employer-add-employee-nonce' ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		// add employee user
		WP_Freeio_User::registration_validation( $_POST['email'], $_POST['password'], $_POST['confirmpassword'], false, false );
		
		$username = !empty($_POST['username']) ? $_POST['username'] : '';
		if ( 4 > strlen( $username ) ) {
		    $reg_errors->add( 'username_length', esc_html__( 'Username too short. At least 4 characters is required', 'wp-freeio' ) );
		}

		if ( username_exists( $username ) ) {
	    	$reg_errors->add('user_name', esc_html__( 'The username already exists', 'wp-freeio' ) );
		}

		if ( ! validate_username( $username ) ) {
		    $reg_errors->add( 'username_invalid', esc_html__( 'The username you entered is not valid', 'wp-freeio' ) );
		}

        if ( 1 > count( $reg_errors->get_error_messages() ) ) {

	 		$userdata = array(
		        'user_login' => sanitize_user( $_POST['username'] ),
		        'user_email' => sanitize_email( $_POST['email'] ),
		        'user_pass' => $_POST['password'],
		        'role' => 'wp_freeio_employee'
	        );
	        $_POST['role'] = 'wp_freeio_employee';
	        
	        $employee_id = wp_insert_user( $userdata );
	        if ( is_wp_error( $employee_id ) ) {
		        $return = array('status' => false, 'msg' => esc_html__( 'Register user error!', 'wp-freeio' ) );
		        echo wp_json_encode($return);
			   	exit;
		    }
	    } else {
	    	$return = array('status' => false, 'msg' => implode('<br>', $reg_errors->get_error_messages()) );
	    	echo wp_json_encode($return);
		   	exit;
	    }

	    // add employee to employer
		$user_id = WP_Freeio_User::get_user_id();
		$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);

		update_user_meta($employee_id, 'employee_employer_id', $employer_id);

		$employees = self::get_post_meta($employer_id, 'employees', false);
		$html = '';
		if ( !empty($employees) ) {
			add_post_meta($employer_id, WP_FREEIO_EMPLOYER_PREFIX.'employees', $employee_id);
            
            $userdata = get_userdata($employee_id);
            $employee_style = apply_filters('wp-freeio-employee-inner-list-team', 'inner-list-team');
            $html = WP_Freeio_Template_Loader::get_template_part( 'employees-styles/'.$employee_style, array('userdata' => $userdata) );
            
			$return = array( 'status' => true, 'msg' => esc_html__('Add employee to team successful', 'wp-freeio'), 'html' => $html );
			echo wp_json_encode($return);
		   	exit;
		} else {
			add_post_meta($employer_id, WP_FREEIO_EMPLOYER_PREFIX.'employees', $employee_id);

			$userdata = get_userdata($employee_id);
			$employee_style = apply_filters('wp-freeio-employee-inner-list-team', 'inner-list-team');
            $html = WP_Freeio_Template_Loader::get_template_part( 'employees-styles/'.$employee_style, array('userdata' => $userdata) );

			$return = array( 'status' => true, 'msg' => esc_html__('Add employee to team successful', 'wp-freeio'), 'html' => $html );
			echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function remove_employee() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-employer-remove-employee-nonce' ) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$employee_id = !empty($_POST['employee_id']) ? $_POST['employee_id'] : '';
		if ( empty($employee_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Employee not found', 'wp-freeio') );
			echo wp_json_encode($return);
		   	exit;
		}

		$user_id = WP_Freeio_User::get_user_id();
		$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
		$employees = self::get_post_meta($employer_id, 'employees', false);
		if ( !empty($employees) && is_array($employees) ) {

			require_once( ABSPATH . 'wp-admin/includes/user.php' );
			
			wp_delete_user($employee_id);
		    delete_post_meta($employer_id, WP_FREEIO_EMPLOYER_PREFIX.'employees', $employee_id);
			$return = array( 'status' => true, 'msg' => esc_html__('Remove employee from team successful', 'wp-freeio') );
			echo wp_json_encode($return);
		   	exit;

		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('Employee not found', 'wp-freeio') );
			echo wp_json_encode($return);
		   	exit;
		}
	}

	
	public static function process_invite_freelancer() {
		if ( !is_user_logged_in() || !WP_Freeio_User::is_employer() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login as "Employer" to invite Freelancer.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$freelancer_id = !empty($_POST['freelancer_id']) ? $_POST['freelancer_id'] : '';
		$post = get_post($freelancer_id);

		if ( !$post || empty($post->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Freelancer doesn\'t exist', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$project_ids = !empty($_POST['project_ids']) ? $_POST['project_ids'] : '';
		if ( empty($project_ids) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please select projects first', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$clean_project_ids = array();
		foreach ($project_ids as $project_id) {
			$project_invited_list = get_post_meta($project_id, '_project_invited_freelancer_apply', true);
            $project_invited_list = !empty($project_invited_list) ? $project_invited_list : array();
            if (!in_array($freelancer_id, $project_invited_list)) {
                $clean_project_ids[] = $project_id;
            }
		}

		if (empty($clean_project_ids)) {
            $return = array( 'status' => false, 'msg' => esc_html__('You already invited this user for these projects.', 'wp-freeio') );
		   	wp_send_json($return);
        } else {

        	$user_id = WP_Freeio_User::get_user_id();
			$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);

			$notify_args = array(
				'post_type' => 'freelancer',
				'user_post_id' => $freelancer_id,
	            'type' => 'invite_freelancer_apply',
	            'employer_id' => $employer_id
			);

			$email_projects_list = '<ul>';
            foreach ($clean_project_ids as $project_id) {
            	$email_projects_list .= '<li><a href="'.get_permalink($project_id).'">'.get_the_title($project_id).'</a></li>';

                $project_invited_list = get_post_meta($project_id, '_project_invited_freelancer_apply', true);
                $project_invited_list = !empty($project_invited_list) ? $project_invited_list : array();
                
                $project_invited_list[] = $freelancer_id;
                update_post_meta($project_id, '_project_invited_freelancer_apply', $project_invited_list);
            }
        	$email_projects_list .= '</ul>';

        	// notify freelancer
			$notify_args['project_ids'] = $clean_project_ids;
			WP_Freeio_User_Notification::add_notification($notify_args);

            // Email
            if ( wp_freeio_get_option('user_notice_add_invite_freelancer') ) {
	            $email_subject = WP_Freeio_Email::render_email_vars( array('project_title' => $post->post_title, 'freelancer_name' => get_the_title($freelancer_id)), 'invite_freelancer_notice', 'subject');
		        $email_content_args = array(
		        	'freelancer_name' => get_the_title($freelancer_id),
		        	'employer_name' => get_the_title($employer_id),
		        	'list_projects' => $email_projects_list,
		        );
		        $email_content = WP_Freeio_Email::render_email_vars( $email_content_args, 'invite_freelancer_notice', 'content');
				
		        $headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
		        
		        $freelancer_email = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
				if ( empty($freelancer_email) ) {
					$freelancer_user_id = WP_Freeio_User::get_user_by_freelancer_id($freelancer_id);
					$freelancer_email = get_the_author_meta( 'user_email', $freelancer_user_id );
				}

				$result = WP_Freeio_Email::wp_mail( $freelancer_email, $email_subject, $email_content, $headers );
			}

            do_action('wp-freeio-invite-apply-to-freelancer', $freelancer_id, $clean_project_ids, $user_id );

            if ( $result ) {
	            $return = array( 'status' => true, 'msg' => esc_html__('Invited successfully.', 'wp-freeio') );
			   	wp_send_json($return);
		   	} else {
		   		$return = array( 'status' => false, 'msg' => esc_html__('Send a email error', 'wp-freeio') );
			   	wp_send_json($return);
		   	}
        }

	}

	public static function employer_only_applicants($post) {
		$return = false;
		if ( is_user_logged_in() ) {
			$user_id = WP_Freeio_User::get_user_id();
			if ( WP_Freeio_User::is_freelancer($user_id) ) {
				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
				$query_vars = array(
				    'post_type' => 'job_applicant',
				    'posts_per_page'    => -1,
				    'paged'    			=> 1,
				    'post_status' => 'publish',
				    'fields' => 'ids',
				    'meta_query' => array(
				    	array(
					    	'key' => WP_FREEIO_APPLICANT_PREFIX . 'freelancer_id',
					    	'value' => $freelancer_id,
					    	'compare' => '=',
					    )
					)
				);
				
				$applicants = WP_Freeio_Query::get_posts($query_vars);
				if ( !empty($applicants) && !empty($applicants->posts) ) {
					$employer_id = $post->ID;
					$employer_user_id = WP_Freeio_User::get_user_by_employer_id($employer_id);
					foreach ($applicants->posts as $applicant_id) {
						$job_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'job_id', true);
						$post_author_id = WP_Freeio_Job_Listing::get_author_id($job_id);

						if ( $post_author_id == $employer_user_id ) {
							$return = true;
							break;
						}
					}
				}
			}
		}
		return $return;
	}

	// check view
	public static function check_view_employer_detail() {
		global $post;
		$restrict_type = wp_freeio_get_option('employer_restrict_type', '');
		$view = wp_freeio_get_option('employer_restrict_detail', 'all');
		
		$return = true;
		if ( $restrict_type == 'view' ) {
			$author_id = WP_Freeio_User::get_user_by_employer_id($post->ID);
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
							$show_profile = self::get_post_meta($post->ID, 'show_profile');
							if ( empty($show_profile) || $show_profile == 'show' ) {
								$return = true;
							}
						}
						break;
					case 'register_freelancer':
						$return = false;
						if ( is_user_logged_in() ) {
							if ( WP_Freeio_User::is_freelancer($user_id) ) {
								$show_profile = self::get_post_meta($post->ID, 'show_profile');
								if ( empty($show_profile) || $show_profile == 'show' ) {
									$return = true;
								}
							}
						}
						break;
					case 'only_applicants':
						$return = self::employer_only_applicants($post);
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
		}
		return apply_filters('wp-freeio-check-view-employer-detail', $return, $post);
	}

	public static function employer_restrict_listing_query($query, $filter_params) {
		$query_vars = $query->query_vars;
		$query_vars = self::employer_restrict_listing_query_args($query_vars, $filter_params);
		$query->query_vars = $query_vars;
		
		return apply_filters('wp-freeio-check-view-employer-listing-query', $query);
	}

	public static function employer_restrict_listing_query_args($query_args, $filter_params) {
		$restrict_type = wp_freeio_get_option('employer_restrict_type', '');

		if ( $restrict_type == 'view' ) {
			$view = wp_freeio_get_option('employer_restrict_listing', 'all');
			
			$user_id = WP_Freeio_User::get_user_id();
			switch ($view) {
				case 'always_hidden':
					$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
					$meta_query[] = array(
						'key'       => 'employer_restrict_listing',
						'value'     => 'always_hidden',
						'compare'   => '==',
					);
					$query_args['meta_query'] = $meta_query;
					break;
				case 'register_user':
					if ( !is_user_logged_in() ) {
						$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
						$meta_query[] = array(
							'key'       => 'employer_restrict_listing',
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
							'key'       => 'employer_restrict_listing',
							'value'     => 'register_freelancer',
							'compare'   => '==',
						);
						$query_args['meta_query'] = $meta_query;
					}
					break;
				case 'only_applicants':

					$ids = array(0);
					if ( is_user_logged_in() ) {
						$applicants = WP_Freeio_Applicant::get_all_applicants_by_freelancer($user_id);
						foreach ($applicants as $applicant_id) {
							$job_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'job_id', true);
							$post_author_id = WP_Freeio_Job_Listing::get_author_id($job_id);
							$employer_id = WP_Freeio_User::get_employer_by_user_id($post_author_id);
							if ( $employer_id ) {
								$return[] = $employer_id;
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
			'key'       => WP_FREEIO_EMPLOYER_PREFIX.'show_profile',
			'value'     => 'hide',
			'compare'   => '!=',
		);
		$query_args['meta_query'] = $meta_query;

		return apply_filters('wp-freeio-check-view-employer-listing-query-args', $query_args);
	}

	public static function check_restrict_view_contact_info($post) {
		$return = true;
		$restrict_type = wp_freeio_get_option('employer_restrict_type', '');
		if ( $restrict_type == 'view_contact_info' ) {
			$view = wp_freeio_get_option('employer_restrict_contact_info', 'all');

			$user_id = WP_Freeio_User::get_user_id();

			$author_id = WP_Freeio_User::get_user_by_employer_id($post->ID);
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
					case 'only_applicants':
						$return = self::employer_only_applicants($post);
						break;
					default:
						$return = true;
						break;
				}
			}
		}
		return apply_filters('wp-freeio-check-view-employer-contact-info', $return, $post);
	}

	public static function check_restrict_review($post) {
		$return = true;
		
		$user_id = WP_Freeio_User::get_user_id();

		$view = wp_freeio_get_option('employers_restrict_review', 'all');
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
			case 'only_applicants':
				$return = self::employer_only_applicants($post);
				break;
			default:
				$return = true;
				break;
		}
		return apply_filters('wp-freeio-check-restrict-employer-review', $return, $post);
	}

	public static function display_employers_count_results($wp_query) {
		$total = $wp_query->found_posts;
		$per_page = $wp_query->query_vars['posts_per_page'];
		$current = max( 1, $wp_query->get( 'paged', 1 ) );
		$args = array(
			'total' => $total,
			'per_page' => $per_page,
			'current' => $current,
		);
		echo WP_Freeio_Template_Loader::get_template_part('loop/employer/results-count', $args);
	}

	public static function display_employers_results_filters() {
		$filters = WP_Freeio_Abstract_Filter::get_filters();

		echo WP_Freeio_Template_Loader::get_template_part('loop/employer/results-filters', array('filters' => $filters));
	}

	public static function display_employers_orderby() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/employer/orderby');
	}

	public static function display_employers_results_count_orderby_start() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/employer/results-count-orderby-start');
	}

	public static function display_employers_results_count_orderby_end() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/employer/results-count-orderby-end');
	}

	public static function restrict_employer_listing_information($query) {
		$restrict_type = wp_freeio_get_option('employer_restrict_type', '');
		if ( $restrict_type == 'view' ) {
			$user_id = WP_Freeio_User::get_user_id();
			$view =  wp_freeio_get_option('employer_restrict_listing', 'all');
			$output = '';
			switch ($view) {
				case 'always_hidden':
						$output = '
						<div class="employer-listing-info">
							<h2 class="restrict-title">'.__( 'The page is restricted. You can not view this page', 'wp-freeio' ).'</h2>
						</div>';
					break;
				case 'register_user':
					if ( !is_user_logged_in() ) {
						$output = '
						<div class="employer-listing-info">
							<h2 class="restrict-title">'.__( 'The page is restricted only for register user.', 'wp-freeio' ).'</h2>
							<div class="restrict-content">'.__( 'You need login to view this page', 'wp-freeio' ).'</div>
						</div>';
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
						$output = '<div class="employer-listing-info"><h2 class="restrict-title">'.__( 'The page is restricted only for freelancers.', 'wp-freeio' ).'</h2></div>';
					}
					break;
				case 'only_applicants':
					$return = array();
					if ( is_user_logged_in() ) {
						$applicants = WP_Freeio_Applicant::get_all_applicants_by_freelancer($user_id);
						if ( !empty($applicants) ) {
							foreach ($applicants as $applicant_id) {
								$job_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'job_id', true);
								$post_author_id = WP_Freeio_Job_Listing::get_author_id($job_id);

								$employer_id = WP_Freeio_User::get_employer_by_user_id($post_author_id);
								if ( $employer_id ) {
									$return[] = $employer_id;
								}
							}
						}
					}
					if ( empty($return) ) {
						$output = '<div class="employer-listing-info"><h2 class="restrict-title">'.__( 'The page is restricted only for freelancers view his applicants.', 'wp-freeio' ).'</h2></div>';
					}
					break;
				default:
					$output = apply_filters('wp-freeio-restrict-employer-listing-default-information', '', $query);
					break;
			}

			echo apply_filters('wp-freeio-restrict-employer-listing-information', $output, $query);
		}
	}

	public static function get_display_email($post) {
		if ( is_object($post) ) {
			$post_id = $post->ID;
		} else {
			$post_id = $post;
			$post = get_post($post_id);
		}
		$email = '';
		if ( self::check_restrict_view_contact_info($post) || wp_freeio_get_option('restrict_contact_employer_email', 'on') !== 'on' ) {
			$email = self::get_post_meta( $post_id, 'email', true );
		}
		return apply_filters('wp-freeio-get-display-employer-email', $email, $post_id);
	}

	public static function get_display_phone($post) {
		if ( is_object($post) ) {
			$post_id = $post->ID;
		} else {
			$post_id = $post;
			$post = get_post($post_id);
		}
		$phone = '';
		if ( self::check_restrict_view_contact_info($post) || wp_freeio_get_option('restrict_contact_employer_phone', 'on') !== 'on' ) {
			$phone = self::get_post_meta( $post_id, 'phone', true );
		}
		return apply_filters('wp-freeio-get-display-employer-phone', $phone, $post_id);
	}
}

WP_Freeio_Employer::init();