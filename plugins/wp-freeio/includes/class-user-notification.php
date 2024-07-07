<?php
/**
 * User Notification
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_User_Notification {
	
	public static function init() {

		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_notify',  array(__CLASS__, 'process_remove_notification') );

		// for private message
		add_action('wp-private-message-after-add-message', array( __CLASS__, 'add_private_message_notify'), 10, 3 );
	}

	public static function add_notification($args) {
		
		$args = wp_parse_args( $args, array(
			'post_type' => 'employer',
			'user_post_id' => 0,
			'unique_id' => uniqid(),
			'viewed' => 0,
            'time' => current_time('timestamp'),
            'type' => '',
            'application_id' => 0,
            'employer_id' => 0,
            'job_id' => 0,
		));

		extract( $args );
		
		if ( empty($user_post_id) || empty($post_type) ) {
			return;
		}

		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		if ( !empty($post_type) && $post_type == 'employer' ) {
			$prefix = WP_FREEIO_EMPLOYER_PREFIX;
		}
		$notifications = get_post_meta($user_post_id, $prefix . 'notifications', true);
        $notifications = !empty($notifications) ? $notifications : array();

        $new_notifications = array_merge( array($unique_id => $args), $notifications );

		update_post_meta($user_post_id, $prefix . 'notifications', $new_notifications);
	}

	public static function process_remove_notification() {
		$return = array();
		if (  !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-remove-notify-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$unique_id = !empty($_POST['unique_id']) ? $_POST['unique_id'] : '';

		$user_id = WP_Freeio_User::get_user_id();
		if ( WP_Freeio_User::is_employer() ) {
			$user_post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
			$post_type = 'employer';
			$prefix = WP_FREEIO_EMPLOYER_PREFIX;
		} elseif ( WP_Freeio_User::is_freelancer() ) {
			$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
			$post_type = 'freelancer';
			$prefix = WP_FREEIO_FREELANCER_PREFIX;
		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('You can not remove the notification', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}


		$notifications = self::get_notifications($user_post_id, $post_type);
		if ( !empty($notifications[$unique_id]) ) {
			unset($notifications[$unique_id]);
			update_post_meta($user_post_id, $prefix . 'notifications', $notifications);

			$return = array( 'status' => true, 'msg' => esc_html__('The notification removed successful', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('The notification dosen\'t exist', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		
	}

	public static function get_notifications($user_post_id, $post_type = 'employer') {
		
		if ( empty($user_post_id) || empty($post_type) ) {
			return;
		}

		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		if ( !empty($post_type) && $post_type == 'employer' ) {
			$prefix = WP_FREEIO_EMPLOYER_PREFIX;
		}
		$notifications = get_post_meta($user_post_id, $prefix . 'notifications', true);;
        $notifications = !empty($notifications) ? $notifications : array();

        return $notifications;
	}

	public static function get_not_seen_notifications($user_post_id, $post_type = 'employer') {
		$notifications = self::get_notifications($user_post_id, $post_type);
		if ( empty($notifications) ) {
			return;
		}
		$return = [];
		foreach ( $notifications as $key => $notify ) {
			if ( isset($notify['viewed']) ) {
				$return[] = $notify;
			}
		}
        return $return;
	}
	
	public static function remove_notification($user_post_id, $post_type = 'employer', $unique_id = '') {
		$notifications = self::get_notifications($user_post_id, $post_type);
		if ( empty($notifications) ) {
			return true;
		}
		if ( !empty($notifications[$unique_id]) ) {
			unset($notifications[$unique_id]);
		} else {
			return false;
		}

		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		if ( !empty($post_type) && $post_type == 'employer' ) {
			$prefix = WP_FREEIO_EMPLOYER_PREFIX;
		}

		update_post_meta($user_post_id, $prefix . 'notifications', $notifications);

        return true;
	}
	
	public static function add_private_message_notify($message_id, $recipient, $user_id) {
		if ( WP_Freeio_User::is_employer($recipient) ) {
			$post_type = 'employer';
			$user_post_id = WP_Freeio_User::get_employer_by_user_id($recipient);
		} elseif ( WP_Freeio_User::is_freelancer($recipient) ) {
			$post_type = 'freelancer';
			$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($recipient);
		}
		
		if ( $post_type == 'employer' || $post_type == 'freelancer' ) {
			$notify_args = array(
				'post_type' => $post_type,
				'user_post_id' => $user_post_id,
	            'type' => 'new_private_message',
	            'user_id' => $user_id,
	            'message_id' => $message_id,
			);
			WP_Freeio_User_Notification::add_notification($notify_args);
		}
	}

	public static function display_notify($args) {
		$type = !empty($args['type']) ? $args['type'] : '';
		switch ($type) {
			case 'email_apply':
				$job_id = !empty($args['job_id']) ? $args['job_id'] : '';
				$html = sprintf(__('A new application is submitted on your job <a href="%s">%s</a>', 'wp-freeio'), get_permalink($job_id), get_the_title($job_id));
				break;
			case 'internal_apply':
				$job_id = !empty($args['job_id']) ? $args['job_id'] : '';
				$freelancer_id = !empty($args['freelancer_id']) ? $args['freelancer_id'] : '';
				$html = sprintf(__('A new application is submitted on your job <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($job_id), get_the_title($job_id), get_permalink($freelancer_id), get_the_title($freelancer_id) );
				break;
			case 'remove_apply':
				$employer_id = !empty($args['employer_id']) ? $args['employer_id'] : '';
				$job_id = !empty($args['job_id']) ? $args['job_id'] : '';

				$html = sprintf(__('The application is removed on your job <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($job_id), get_the_title($job_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'create_meeting':
				$employer_id = !empty($args['employer_id']) ? $args['employer_id'] : '';
				$post_id = !empty($args['post_id']) ? $args['post_id'] : '';

				$topic_title = '';
				$job_id = 0;
				if ( get_post_type($post_id) == 'job_applicant') {
					$job_id = WP_Freeio_Applicant::get_post_meta($post_id, 'job_id');
					$topic_title = esc_html__('job', 'wp-freeio');
				} elseif( get_post_type($post_id) == 'project_proposal' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id');
					$topic_title = esc_html__('project', 'wp-freeio');
				} elseif( get_post_type($post_id) == 'service_order' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id');
					$topic_title = esc_html__('service', 'wp-freeio');
				} else {
					$html = '';
					break;
				}

				$html = sprintf(__('A new meeting is created on the %s <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), $topic_title, get_permalink($job_id), get_the_title($job_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'reschedule_meeting':
				$reschedule_user_id = !empty($args['reschedule_user_id']) ? $args['reschedule_user_id'] : '';
				$meeting_id = !empty($args['meeting_id']) ? $args['meeting_id'] : '';
				$post_id = WP_Freeio_Meeting::get_post_meta($meeting_id, 'post_id');

				if ( get_post_type($post_id) == 'job_applicant') {
					$job_id = WP_Freeio_Applicant::get_post_meta($post_id, 'job_id');
					$topic_title = esc_html__('job', 'wp-freeio');
				} elseif( get_post_type($post_id) == 'project_proposal' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id');
					$topic_title = esc_html__('project', 'wp-freeio');
				} elseif( get_post_type($post_id) == 'service_order' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id');
					$topic_title = esc_html__('service', 'wp-freeio');
				}

				$html = sprintf(__('A meeting is re-schedule on the %s <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), $topic_title, get_permalink($job_id), get_the_title($job_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'remove_meeting':
				$post_id = !empty($args['post_id']) ? $args['post_id'] : '';
				$employer_id = !empty($args['employer_id']) ? $args['employer_id'] : '';
				$topic_title = $job_id = '';
				if ( get_post_type($post_id) == 'job_applicant') {
					$job_id = WP_Freeio_Applicant::get_post_meta($post_id, 'job_id');
					$topic_title = esc_html__('job', 'wp-freeio');
				} elseif( get_post_type($post_id) == 'project_proposal' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id');
					$topic_title = esc_html__('project', 'wp-freeio');
				} elseif( get_post_type($post_id) == 'service_order' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id');
					$topic_title = esc_html__('service', 'wp-freeio');
				}
				$html = '';
				if ( $job_id && $topic_title) {
					$html = sprintf(__('A meeting is removed on the %s <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), $topic_title, get_permalink($job_id), get_the_title($job_id), get_permalink($employer_id), get_the_title($employer_id) );
				}
				break;
			case 'cancel_meeting':
				$post_id = !empty($args['post_id']) ? $args['post_id'] : '';
				$freelancer_id = !empty($args['freelancer_id']) ? $args['freelancer_id'] : '';
				if ( get_post_type($post_id) == 'job_applicant') {
					$job_id = WP_Freeio_Applicant::get_post_meta($post_id, 'job_id');
					$topic_title = esc_html__('job', 'wp-freeio');
				} elseif( get_post_type($post_id) == 'project_proposal' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id');
					$topic_title = esc_html__('project', 'wp-freeio');
				} elseif( get_post_type($post_id) == 'service_order' ) {
					$job_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id');
					$topic_title = esc_html__('service', 'wp-freeio');
				}

				$html = sprintf(__('A meeting is canceled on your %s <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), $topic_title, get_permalink($job_id), get_the_title($job_id), get_permalink($freelancer_id), get_the_title($freelancer_id) );
				break;
			case 'reject_applied':
				$employer_id = !empty($args['employer_id']) ? $args['employer_id'] : '';
				$job_id = !empty($args['job_id']) ? $args['job_id'] : '';

				$html = sprintf(__('The application is rejected on your job <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($job_id), get_the_title($job_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'undo_reject_applied':
				$employer_id = !empty($args['employer_id']) ? $args['employer_id'] : '';
				$job_id = !empty($args['job_id']) ? $args['job_id'] : '';

				$html = sprintf(__('The application is undo rejected on your job <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($job_id), get_the_title($job_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'approve_applied':
				$employer_id = !empty($args['employer_id']) ? $args['employer_id'] : '';
				$job_id = !empty($args['job_id']) ? $args['job_id'] : '';

				$html = sprintf(__('The application is approved on your job <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($job_id), get_the_title($job_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'undo_approve_applied':
				$employer_id = !empty($args['employer_id']) ? $args['employer_id'] : '';
				$job_id = !empty($args['job_id']) ? $args['job_id'] : '';

				$html = sprintf(__('The application is undo approved on your job <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($job_id), get_the_title($job_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'new_private_message':
				$user_id = !empty($args['user_id']) ? $args['user_id'] : '';
				if ( WP_Freeio_User::is_employer() ) {
					$user_post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
				} elseif ( WP_Freeio_User::is_freelancer() ) {
					$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
				}
				$message_id = !empty($args['message_id']) ? $args['message_id'] : '';
				if ( !empty($user_post_id) ) {
					$html = sprintf(__('A new private message from <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($user_post_id), get_the_title($user_post_id) );
				} else {
					$user = get_userdata( $user_id );
					$html = sprintf(__('A new private message from %s.', 'wp-freeio'), $user->display_name );
				}
				break;
			case 'invite_freelancer_apply':
				$project_ids = !empty($args['project_ids']) ? $args['project_ids'] : '';
				
				if ( !empty($project_ids) && count($project_ids) == 1 ) {
					$html = sprintf(__('You are invited to send proposal for this project <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($project_ids[0]), get_the_title($project_ids[0]) );
				} elseif( !empty($project_ids) ) {
					$jobs_html = '';
					$count = 1;
					foreach ($project_ids as $project_id) {
						$jobs_html .= '<a href="'.get_permalink($project_id).'">'.get_the_title($project_id).'</a>'.($count < count($project_ids) ? ', ' : '');
						$count++;
					}
					$html = sprintf(__('You are invited to apply jobs %s.', 'wp-freeio'), $jobs_html );
				}
				break;
			case 'new_proposal':
				$project_id = !empty($args['project_id']) ? $args['project_id'] : '';
				$freelancer_user_id = !empty($args['freelancer_user_id']) ? $args['freelancer_user_id'] : '';
				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
				
				$html = sprintf(__('A new proposal is submitted on your project <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($project_id), get_the_title($project_id), get_permalink($freelancer_id), get_the_title($freelancer_id) );
				break;
			case 'edit_proposal':
				$project_id = !empty($args['project_id']) ? $args['project_id'] : '';
				$freelancer_user_id = !empty($args['freelancer_user_id']) ? $args['freelancer_user_id'] : '';
				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
				
				$html = sprintf(__('A proposal is edited on your project <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($project_id), get_the_title($project_id), get_permalink($freelancer_id), get_the_title($freelancer_id) );
				break;
			case 'hired_proposal':
				$project_id = !empty($args['project_id']) ? $args['project_id'] : '';
				$employer_user_id = !empty($args['employer_user_id']) ? $args['employer_user_id'] : '';
				$employer_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);

				$html = sprintf(__('You have hired for the following project <a href="%s">%s</a> by the employer <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($project_id), get_the_title($project_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'cancelled_hired_proposal':
				$project_id = !empty($args['project_id']) ? $args['project_id'] : '';
				$employer_user_id = !empty($args['employer_user_id']) ? $args['employer_user_id'] : '';
				$employer_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);

				$html = sprintf(__('You have cancelled hiring for the following project <a href="%s">%s</a> by the employer <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($project_id), get_the_title($project_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'proposal_message':
				$post_type = !empty($args['post_type']) ? $args['post_type'] : '';
				$project_id = !empty($args['project_id']) ? $args['project_id'] : '';
				$proposal_id = !empty($args['proposal_id']) ? $args['proposal_id'] : '';
				$user_id = !empty($args['freelancer_user_id']) ? $args['freelancer_user_id'] : '';
				
				$view_history_url = '';
				if ( $post_type == 'freelancer' ) {
					$my_projects_page_id = wp_freeio_get_option('my_projects_page_id');
					$my_projects_url = get_permalink( $my_projects_page_id );

					$my_projects_url = add_query_arg( 'project_id', $project_id, remove_query_arg( 'project_id', $my_projects_url ) );
					$my_projects_url = add_query_arg( 'proposal_id', $proposal_id, remove_query_arg( 'proposal_id', $my_projects_url ) );
					$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_projects_url ) );

					$user_id = !empty($args['freelancer_user_id']) ? $args['freelancer_user_id'] : '';

					$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
				} elseif ( $post_type == 'employer' ) {
					$my_proposals_page_id = wp_freeio_get_option('my_proposals_page_id');
					$my_proposals_url = get_permalink( $my_proposals_page_id );

					$my_proposals_url = add_query_arg( 'project_id', $project_id, remove_query_arg( 'project_id', $my_proposals_url ) );
					$my_proposals_url = add_query_arg( 'proposal_id', $proposal_id, remove_query_arg( 'proposal_id', $my_proposals_url ) );
					$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_proposals_url ) );

					$user_id = !empty($args['user_post_id']) ? $args['user_post_id'] : '';
					$user_post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
				}

				$html = sprintf(__('A message is sent on your project proposal <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), $view_history_url, get_the_title($project_id), get_permalink($user_post_id), get_the_title($user_post_id) );
				break;
			case 'change_proposal_status':
				$project_id = !empty($args['project_id']) ? $args['project_id'] : '';
				$proposal_id = !empty($args['proposal_id']) ? $args['proposal_id'] : '';
				$user_id = !empty($args['user_id']) ? $args['user_id'] : '';
				
				$my_projects_page_id = wp_freeio_get_option('my_projects_page_id');
				$my_projects_url = get_permalink( $my_projects_page_id );
				$my_projects_url = add_query_arg( 'project_id', $project_id, remove_query_arg( 'project_id', $my_projects_url ) );
				$my_projects_url = add_query_arg( 'proposal_id', $proposal_id, remove_query_arg( 'proposal_id', $my_projects_url ) );
				$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_projects_url ) );
				
				$post_type = get_post_status($project_id);
				if ( $post_type == 'completed' ) {
					$html = sprintf(__('Your project completed <a href="%s">%s</a>.', 'wp-freeio'), $view_history_url, get_the_title($project_id) );
				} elseif ( $post_type == 'cancelled' ) {
					$html = sprintf(__('Your project cancelled <a href="%s">%s</a>.', 'wp-freeio'), $view_history_url, get_the_title($project_id) );
				} else {
					$html = '';
				}
				break;

			case 'hired_service':
				$service_id = !empty($args['service_id']) ? $args['service_id'] : '';
				$employer_user_id = !empty($args['employer_user_id']) ? $args['employer_user_id'] : '';
				$employer_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);

				$html = sprintf(__('You have hired for the following service <a href="%s">%s</a> by the employer <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($service_id), get_the_title($service_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'cancelled_hired_service':
				$service_id = !empty($args['service_id']) ? $args['service_id'] : '';
				$employer_user_id = !empty($args['employer_user_id']) ? $args['employer_user_id'] : '';
				$employer_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);
				
				$html = sprintf(__('You have cancelled hiring for the following service <a href="%s">%s</a> by the employer <a href="%s">%s</a>.', 'wp-freeio'), get_permalink($service_id), get_the_title($service_id), get_permalink($employer_id), get_the_title($employer_id) );
				break;
			case 'service_message':
				$post_type = !empty($args['post_type']) ? $args['post_type'] : '';
				$service_id = !empty($args['service_id']) ? $args['service_id'] : '';
				$service_order_id = !empty($args['service_order_id']) ? $args['service_order_id'] : '';
				$user_id = !empty($args['freelancer_user_id']) ? $args['freelancer_user_id'] : '';
				
				$view_history_url = '';
				if ( $post_type == 'freelancer' ) {
					$my_services_page_id = wp_freeio_get_option('my_services_page_id');
					
					$my_services_url = get_permalink( $my_services_page_id );

					$my_services_url = add_query_arg( 'service_id', $service_id, remove_query_arg( 'service_id', $my_services_url ) );
					$my_services_url = add_query_arg( 'service_order_id', $service_order_id, remove_query_arg( 'service_order_id', $my_services_url ) );
					$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_services_url ) );

					
					$user_id = !empty($args['employer_user_id']) ? $args['employer_user_id'] : '';
					$user_post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
				} elseif ( $post_type == 'employer' ) {
					$my_services_page_id = wp_freeio_get_option('my_bought_services_page_id');
					$my_services_url = get_permalink( $my_services_page_id );

					$my_services_url = add_query_arg( 'service_id', $service_id, remove_query_arg( 'service_id', $my_services_url ) );
					$my_services_url = add_query_arg( 'service_order_id', $service_order_id, remove_query_arg( 'service_order_id', $my_services_url ) );
					$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_services_url ) );

					$user_id = !empty($args['freelancer_user_id']) ? $args['freelancer_user_id'] : '';
					$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
				}

				$html = sprintf(__('A message is sent on your service <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), $view_history_url, get_the_title($service_id), get_permalink($user_post_id), get_the_title($user_post_id) );
				break;
			case 'change_service_status':
				$service_id = !empty($args['service_id']) ? $args['service_id'] : '';
				$service_order_id = !empty($args['service_order_id']) ? $args['service_order_id'] : '';
				
				$my_services_page_id = wp_freeio_get_option('my_services_page_id');
				$my_services_url = get_permalink( $my_services_page_id );
				$my_services_url = add_query_arg( 'service_id', $service_id, remove_query_arg( 'service_id', $my_services_url ) );
				$my_services_url = add_query_arg( 'service_order_id', $service_order_id, remove_query_arg( 'service_order_id', $my_services_url ) );
				$view_history_url = add_query_arg( 'action', 'view-history', remove_query_arg( 'action', $my_services_url ) );
				
				$post_type = get_post_status($service_order_id);
				if ( $post_type == 'completed' ) {
					$html = sprintf(__('Your service completed <a href="%s">%s</a>.', 'wp-freeio'), $view_history_url, get_the_title($service_id) );
				} elseif ( $post_type == 'cancelled' ) {
					$html = sprintf(__('Your service cancelled <a href="%s">%s</a>.', 'wp-freeio'), $view_history_url, get_the_title($service_id) );
				} else {
					$html = '';
				}
				break;
			case 'new_dispute':
				$dispute_id = !empty($args['dispute_id']) ? $args['dispute_id'] : '';
				$user_post_type = !empty($args['post_type']) ? $args['post_type'] : '';
				if ( $user_post_type == 'employer') {
					$user_post_id = !empty($args['freelancer_user_id']) ? $args['freelancer_user_id'] : '';
				} else {
					$user_post_id = !empty($args['employer_user_id']) ? $args['employer_user_id'] : '';
				}
				$post_id = !empty($args['post_id']) ? $args['post_id'] : '';
				$p_post_type = get_post_type($post_id);
				if ( !empty($post_type) && $post_type === 'service_order' ) {
					$p_post_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
				} else {
					$p_post_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
				}

				$my_disputes_page_id = wp_freeio_get_option('my_disputes_page_id');
				$my_disputes_url = get_permalink( $my_disputes_page_id );

				$my_disputes_url = add_query_arg( 'dispute_id', $dispute_id, remove_query_arg( 'dispute_id', $my_disputes_url ) );
				$message_url = add_query_arg( 'action', 'view-detail', remove_query_arg( 'action', $my_disputes_url ) );
				

				$html = sprintf(__('A dispute is sent on <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), $message_url, get_the_title($post_id), get_permalink($user_post_id), get_the_title($user_post_id) );
				break;
			case 'dispute_message':
				$dispute_id = !empty($args['dispute_id']) ? $args['dispute_id'] : '';
				$user_post_type = !empty($args['post_type']) ? $args['post_type'] : '';
				$user_id = !empty($args['freelancer_user_id']) ? $args['freelancer_user_id'] : '';
				if ( $user_post_type == 'employer') {
					$user_post_id = !empty($args['freelancer_user_id']) ? $args['freelancer_user_id'] : '';
				} else {
					$user_post_id = !empty($args['employer_user_id']) ? $args['employer_user_id'] : '';
				}
				$post_id = !empty($args['post_id']) ? $args['post_id'] : '';
				$p_post_type = get_post_type($post_id);
				if ( !empty($post_type) && $post_type === 'service_order' ) {
					$p_post_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
				} else {
					$p_post_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
				}

				$my_disputes_page_id = wp_freeio_get_option('my_disputes_page_id');
				$my_disputes_url = get_permalink( $my_disputes_page_id );

				$my_disputes_url = add_query_arg( 'dispute_id', $dispute_id, remove_query_arg( 'dispute_id', $my_disputes_url ) );
				$message_url = add_query_arg( 'action', 'view-detail', remove_query_arg( 'action', $my_disputes_url ) );
				

				$html = sprintf(__('A new dispute message is sent on <a href="%s">%s</a> by <a href="%s">%s</a>.', 'wp-freeio'), $message_url, get_the_title($dispute_id), get_permalink($user_post_id), get_the_title($user_post_id) );
				break;
			default:
				$html = '';
				break;
		}

		return apply_filters( 'wp-freeio-display-notify', $html, $args);
	}
	
}

WP_Freeio_User_Notification::init();