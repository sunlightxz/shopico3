<?php
/**
 * Post Type: Dispute
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Dispute {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );

	  	add_filter( 'manage_edit-dispute_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_dispute_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) , 10 );

		add_action( 'wpfi_ajax_wp_freeio_ajax_save_dispute',  array(__CLASS__, 'process_dispute') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_send_dispute_message',  array(__CLASS__, 'process_send_dispute_message') );

		add_action( 'pre_post_update',  array( __CLASS__, 'pre_post_update' ) );
	}

	public static function admin_menu() {
		//Settings
	 	
		$pending_approve = wp_count_posts( 'dispute' )->pending_approve;
		$pending = wp_count_posts( 'dispute' )->pending;
		$count = $pending_approve + $pending;

		$menu_title = __( 'Disputes', 'wp-freeio' );
		if ( $count > 0 ) {
			$menu_title = sprintf('%s <span class="awaiting-mod"><span class="pending-count">%d</span></span>', $menu_title, $count );
		}
		
		add_submenu_page('freelancer-settings', __( 'Disputes', 'wp-freeio' ), $menu_title, 'manage_options', 'edit.php?post_type=dispute');
	}

	public static function register_post_type() {
		$singular = __( 'Dispute', 'wp-freeio' );
		$plural   = __( 'Disputes', 'wp-freeio' );

		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new'               => sprintf(__( 'Add New %s', 'wp-freeio' ), $singular),
			'add_new_item'          => sprintf(__( 'Add New %s', 'wp-freeio' ), $singular),
			'edit_item'             => sprintf(__( 'Edit %s', 'wp-freeio' ), $singular),
			'new_item'              => sprintf(__( 'New %s', 'wp-freeio' ), $singular),
			'all_items'             => $plural,
			'view_item'             => sprintf(__( 'View %s', 'wp-freeio' ), $singular),
			'search_items'          => sprintf(__( 'Search %s', 'wp-freeio' ), $singular),
			'not_found'             => sprintf(__( 'No %s found', 'wp-freeio' ), $plural),
			'not_found_in_trash'    => sprintf(__( 'No %s found in Trash', 'wp-freeio' ), $plural),
			'parent_item_colon'     => '',
			'menu_name'             => $plural,
		);
		
		register_post_type( 'dispute',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor' ),
				'public'            => true,
		        'has_archive'       => false,
				'show_in_rest'		=> true,
				'capabilities' => array(
				    'create_posts' => false,
				),
				'show_in_menu'		=> false,
				'map_meta_cap' => true,
				'query_var' => false,
				'publicly_queryable'  => false
			)
		);
	}

	/**
	 * Defines custom fields
	 *
	 * @access public
	 * @param array $metaboxes
	 * @return array
	 */
	public static function fields( array $metaboxes ) {
		$email_frequency_default = WP_Freeio_Job_Alert::get_email_frequency();
		$email_frequency = array();
		if ( $email_frequency_default && is_admin() ) {
			foreach ($email_frequency_default as $key => $value) {
				if ( !empty($value['label']) && !empty($value['days']) ) {
					$email_frequency[$key] = $value['label'];
				}
			}
		}
		
		$fields = array();
		$receipt_title =  $sender_title = $sender = $receipt = '';
		if ( isset($_GET['post']) && $_GET['post'] && is_admin() ) {
			$post = get_post($_GET['post']);
			if ( $post && $post->post_type == 'dispute' ) {
				$post_id = get_post_meta( $post->ID, WP_FREEIO_DISPUTE_PREFIX . 'post_id', true );
				$post_type = get_post_type($post_id);
				if ( !empty($post_type) && $post_type === 'service_order' ) {
					$p_post_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
				} else {
					$p_post_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
				}

				$fields[] = array(
					'name' => sprintf( __('Service/Project: <a href="post.php?post=%s&action=edit" target="_blank">%s</a>', 'wp-freeio'), $p_post_id, get_the_title($p_post_id) ),
					'type' => 'title',
					'id'   => WP_FREEIO_DISPUTE_PREFIX . 'post_title'
				);

				
				$sender = get_post_meta( $post->ID, WP_FREEIO_DISPUTE_PREFIX . 'sender', true );
				if ( WP_Freeio_User::is_employer($sender) ) {
					$employer_id = WP_Freeio_User::get_employer_by_user_id($sender);
					$sender_title = get_the_title($employer_id);
				} elseif ( WP_Freeio_User::is_freelancer($sender) ) {
					$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($sender);
					$sender_title = get_the_title($freelancer_id);
				}
				$fields[] = array(
					'name' => sprintf( __('Sender: %s', 'wp-freeio'), $sender_title ),
					'type' => 'title',
					'id'   => WP_FREEIO_DISPUTE_PREFIX . 'sender_title'
				);

				
				$receipt = get_post_meta( $post->ID, WP_FREEIO_DISPUTE_PREFIX . 'receipt', true );
				if ( WP_Freeio_User::is_employer($receipt) ) {
					$employer_id = WP_Freeio_User::get_employer_by_user_id($receipt);
					$receipt_title = get_the_title($employer_id);
				} elseif ( WP_Freeio_User::is_freelancer($receipt) ) {
					$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($receipt);
					$receipt_title = get_the_title($freelancer_id);
				}
				$fields[] = array(
					'name' => sprintf( __('Receipter: %s', 'wp-freeio'), $receipt_title ),
					'type' => 'title',
					'id'   => WP_FREEIO_DISPUTE_PREFIX . 'receipt_title'
				);
			}
		}

		$fields[] = array(
			'name'              => __( 'Admin Response', 'wp-freeio' ),
			'id'                => WP_FREEIO_DISPUTE_PREFIX . 'admin_response',
			'type'              => 'textarea',
		);
		$fields[] = array(
			'name'              => __( 'Select Winner', 'wp-freeio' ),
			'id'                => WP_FREEIO_DISPUTE_PREFIX . 'winner',
			'type'              => 'select',
			'options'			=> array(
				'' => esc_html__('choose a user', 'wp-freeio'),
				$sender => $sender_title,
				$receipt => $receipt_title,
			)
		);
		$fields[] = array(
			'name'              => __( 'Mark Dispute as resolved', 'wp-freeio' ),
			'id'                => WP_FREEIO_DISPUTE_PREFIX . 'resolved',
			'type'              => 'checkbox',
		);
		
		$metaboxes[ WP_FREEIO_DISPUTE_PREFIX . 'general' ] = array(
			'id'                        => WP_FREEIO_DISPUTE_PREFIX . 'general',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( 'dispute' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => $fields
		);

		$fields = array();
		$fields[] = array(
			'name'              => __( 'Messages', 'wp-freeio' ),
			'id'                => WP_FREEIO_DISPUTE_PREFIX . 'message_list',
			'type'              => 'wpfr_message_list',
		);
		$metaboxes[ WP_FREEIO_DISPUTE_PREFIX . 'message_log' ] = array(
			'id'                        => WP_FREEIO_DISPUTE_PREFIX . 'message_log',
			'title'                     => __( 'Messages', 'wp-freeio' ),
			'object_types'              => array( 'dispute' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => $fields
		);
		return $metaboxes;
	}

	/**
	 * Custom admin columns for post type
	 *
	 * @access public
	 * @return array
	 */
	public static function custom_columns($columns) {
		if ( isset($columns['comments']) ) {
			unset($columns['comments']);
		}
		if ( isset($columns['date']) ) {
			unset($columns['date']);
		}
		$fields = array_merge($columns, array(
			'title' 			=> __( 'Title', 'wp-freeio' ),
			'post_type' 			=> __( 'Post Type', 'wp-freeio' ),
			'post_title' 			=> __( 'Post Title', 'wp-freeio' ),
			'date' 				=> esc_html__( 'Date', 'wp-freeio' ),
			'author' 			=> esc_html__( 'Dispute By', 'wp-freeio' ),
		));
		return $fields;
	}

	/**
	 * Custom admin columns implementation
	 *
	 * @access public
	 * @param string $column
	 * @return array
	 */
	public static function custom_columns_manage( $column ) {
		switch ( $column ) {
			case 'post_type':
				$post_type = get_post_meta( get_the_ID(), WP_FREEIO_DISPUTE_PREFIX . 'post_type', true );
				$post_type_obj = get_post_type_object($post_type);
				if ( !is_wp_error($post_type_obj) ) {
					echo $post_type_obj->labels->singular_name;
				}
			break;
			case 'post_title':
				$post_id = get_post_meta( get_the_ID(), WP_FREEIO_DISPUTE_PREFIX . 'post_id', true );
				$post_type = get_post_type($post_id);
				if ( !empty($post_type) && $post_type === 'service_order' ) {
					$p_post_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
				} else {
					$p_post_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
				}
				?>
				<a href="post.php?post=<?php echo $p_post_id; ?>&action=edit"><?php echo get_the_title($p_post_id); ?></a>
				<?php
			break;
		}
	}

	public static function process_dispute() {
		$return = array();

		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to dispute.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$user_id = WP_Freeio_User::get_user_id();

		$post_id = !empty($_POST['dispute_project']) ? sanitize_text_field($_POST['dispute_project']) : 0;
		$post_type = get_post_type($post_id);

		if ( empty($post_id) || !in_array($post_type, array('project_proposal', 'service_order')) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('This post is not exists.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		// sent dispute
		$dispute_sent = intval(0);
        $args = array(
            'post_type' => 'dispute',
            // 'author'    =>  $user_id,
            'meta_query' => array(
                array(
                    'key'     => WP_FREEIO_DISPUTE_PREFIX.'post_id',
                    'value'   => intval( $post_id ),
                    'compare' => '=',
                ),
                array(
					'relation' => 'OR',
	             	array(
	                 'key'     => WP_FREEIO_DISPUTE_PREFIX.'sender',
	                 'value'   => intval( $user_id ),
	                 'compare' => '=',
	             	),
	             	array(
	                 'key'     => WP_FREEIO_DISPUTE_PREFIX.'receipt',
	                 'value'   => intval( $user_id ),
	                 'compare' => '=',
	             	),
	          	)
            ),
        );

        $query = new WP_Query( $args );
        if( !empty( $query ) ){
           $dispute_sent =  $query->found_posts;
        }
        if( $dispute_sent > 0 ) {
            $return = array( 'status' => false, 'msg' => esc_html__('You have already sent the dispute', 'wp-freeio') );
            wp_send_json( $return );
        }

        // field empty
		$dispute_title = !empty($_POST['dispute_title']) ? sanitize_text_field($_POST['dispute_title']) : '';
		$description = !empty($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';
		if ( empty($dispute_title) || empty($description) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please fill all form fields.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		do_action('wp-freeio-process-save-dispute-before', $_POST);

		$dispute_post = array(
			'post_title'    => wp_strip_all_tags( $dispute_title ), //dispute title
			'post_status'   => 'publish',
			'post_content'  => $description,
			'post_author'   => $user_id,
			'post_type'     => 'dispute',
		);

		$dispute_id = wp_insert_post( $dispute_post );
		if ( !is_wp_error( $dispute_id ) ) {
			update_post_meta( $dispute_id, WP_FREEIO_DISPUTE_PREFIX.'post_type', $post_type );
			update_post_meta( $dispute_id, WP_FREEIO_DISPUTE_PREFIX.'post_id', $post_id );

			if ( WP_Freeio_User::is_freelancer($user_id) ) {
				if ( !empty($post_type) && $post_type === 'service_order' ) {
					$receipt_id = get_post_field( 'post_author', $post_id );
				} else {
					$receipt_id = get_post_meta( $post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'employer_id', true );
				}
			} else {
				if ( !empty($post_type) && $post_type === 'service_order' ) {
					$receipt_id = get_post_meta( $post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author', true );
				} else {
					$receipt_id = get_post_field( 'post_author', $post_id );
				}
			}

			update_post_meta( $dispute_id, WP_FREEIO_DISPUTE_PREFIX.'receipt', $receipt_id );
			update_post_meta( $dispute_id, WP_FREEIO_DISPUTE_PREFIX.'sender', $user_id );


			$employer_user_id = $freelancer_user_id = 0;
			if ( WP_Freeio_User::is_employer($user_id) ) {
				$employer_user_id = WP_Freeio_User::get_employer_by_user_id($user_id);
				$notify_post_type = 'freelancer';

				$post_type = get_post_type($post_id);

				if ( !empty($post_type) && $post_type === 'service_order' ) {
					$freelancer_id = get_post_meta( $post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author', true );
					$user_post_id = $freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_id);
				} else {
					$freelancer_id = get_post_field( 'post_author', $post_id );
					$user_post_id = $freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_id);
				}
			} else {
				$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
				$notify_post_type = 'employer';

				$post_type = get_post_type($post_id);
				if ( !empty($post_type) && $post_type === 'service_order' ) {
					$employer_id = get_post_field( 'post_author', $post_id );
					$user_post_id = $employer_user_id = WP_Freeio_User::get_employer_by_user_id($employer_id);
				} else {
					$employer_id = get_post_meta( $post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'employer_id', true );
					$user_post_id = $employer_user_id = WP_Freeio_User::get_employer_by_user_id($employer_id);
				}
			}
			if ( wp_freeio_get_option('user_notice_add_new_dispute') ) {

				$my_disputes_page_id = wp_freeio_get_option('my_disputes_page_id');
				$my_disputes_url = get_permalink( $my_disputes_page_id );

				$my_disputes_url = add_query_arg( 'dispute_id', $dispute_id, remove_query_arg( 'dispute_id', $my_disputes_url ) );
				$message_url = add_query_arg( 'action', 'view-detail', remove_query_arg( 'action', $my_disputes_url ) );

				if ( WP_Freeio_User::is_employer($user_id) ) {
					$email_to = get_post_meta( $freelancer_user_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
					if ( empty($email_to) ) {
						$email_to = get_the_author_meta( 'user_email', $freelancer_id );
					}
				} else {
					$email_to = get_post_meta( $employer_user_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
					if ( empty($email_to) ) {
						$email_to = get_the_author_meta( 'user_email', $employer_id );
					}
				}
				$username = get_the_title($user_post_id);
				$post_title = get_the_title($post_id);

				$email_vars = array(
					'username' => $username,
					'post_title' => $post_title,
					'message' => $message,
					'dispute_url' => $message_url,
				);
	     		
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
				
				$subject = WP_Freeio_Email::render_email_vars($email_vars, 'created_dispute_notice', 'subject');
				$content = WP_Freeio_Email::render_email_vars($email_vars, 'created_dispute_notice', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}

			if ( wp_freeio_get_option('admin_notice_add_new_dispute') ) {

				$my_disputes_url = get_edit_post_link($dispute_id);
				$email_to = get_option( 'admin_email', false );

				$username = get_the_title($user_post_id);
				$post_title = get_the_title($post_id);

				$email_vars = array(
					'username' => $username,
					'post_title' => $post_title,
					'message' => $message,
					'dispute_url' => $my_disputes_url,
				);
	     		
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
				
				$subject = WP_Freeio_Email::render_email_vars($email_vars, 'created_dispute_admin_notice', 'subject');
				$content = WP_Freeio_Email::render_email_vars($email_vars, 'created_dispute_admin_notice', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}

			$notify_args = array(
				'post_type' => $notify_post_type,
				'user_post_id' => $user_post_id,
	            'type' => 'new_dispute',
	            'post_id' => $post_id,
	            'employer_user_id' => $employer_user_id,
	            'freelancer_user_id' => $freelancer_user_id,
	            'dispute_id' => $dispute_id,
			);

			WP_Freeio_User_Notification::add_notification($notify_args);

			$return = array( 'status' => true, 'msg' => esc_html__('Your dispute has sent Successfully.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		$return = array( 'status' => false, 'msg' => esc_html__('An error occured when save a dispute.', 'wp-freeio') );
	   	wp_send_json($return);
	}

	public static function process_send_dispute_message() {
		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to send this message', 'wp-freeio') );
		   	wp_send_json($return);
		}
		$dispute_id = empty( $_POST['dispute_id'] ) ? false : intval( $_POST['dispute_id'] );
		if ( !$dispute_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Service Order not found', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$message = empty( $_POST['message'] ) ? false : wp_kses_post($_POST['message']);
		if ( !$message ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Message is required', 'wp-freeio') );
		   	wp_send_json($return);
		}

		$user_id = WP_Freeio_User::get_user_id();
		$sender_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'sender', true);
		$receipt_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'receipt', true);


		if ( $user_id != $sender_id && $user_id != $receipt_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('You have not permission to send the message', 'wp-freeio') );
		   	wp_send_json($return);
		}
		

		do_action('wp-freeio-before-send-dispute-message');

		$unique_id = uniqid();
		$message_args = array(
			'unique_id' => $unique_id,
			'message' => $message,
			'user_id' => $user_id,
            'dispute_id' => $dispute_id,
            'time' => current_time('timestamp'),
		);

		$messages = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX . 'messages', true);
        $messages = !empty($messages) ? $messages : array();

        $new_messages = array_merge( array($unique_id => $message_args), $messages );
		update_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX . 'messages', $new_messages);

		$employer_user_id = $freelancer_user_id = 0;
		if ( WP_Freeio_User::is_employer($user_id) ) {
			$employer_user_id = WP_Freeio_User::get_employer_by_user_id($user_id);
			$notify_post_type = 'freelancer';

			$p_post_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'post_id', true);
			$post_type = get_post_type($p_post_id);
			if ( !empty($post_type) && $post_type === 'service_order' ) {
				$freelancer_id = get_post_meta( $p_post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author', true );
				$user_post_id =$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_id);
			} else {
				$freelancer_id = get_post_field( 'post_author', $p_post_id );
				$user_post_id =$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_id);
			}
		} else {
			$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
			$notify_post_type = 'employer';

			$p_post_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'post_id', true);
			$post_type = get_post_type($p_post_id);
			if ( !empty($post_type) && $post_type === 'service_order' ) {
				$employer_id = get_post_field( 'post_author', $p_post_id );
				$user_post_id =$employer_user_id = WP_Freeio_User::get_employer_by_user_id($employer_id);
			} else {
				$employer_id = get_post_meta( $p_post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'employer_id', true );
				$user_post_id =$employer_user_id = WP_Freeio_User::get_employer_by_user_id($employer_id);
			}
		}

		if ( wp_freeio_get_option('user_notice_add_new_dispute_message') ) {

			$my_disputes_page_id = wp_freeio_get_option('my_disputes_page_id');
			$my_disputes_url = get_permalink( $my_disputes_page_id );

			$my_disputes_url = add_query_arg( 'dispute_id', $dispute_id, remove_query_arg( 'dispute_id', $my_disputes_url ) );
			$message_url = add_query_arg( 'action', 'view-detail', remove_query_arg( 'action', $my_disputes_url ) );

			if ( WP_Freeio_User::is_employer($user_id) ) {
				$email_to = get_post_meta( $freelancer_user_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
				if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $freelancer_id );
				}
			} else {
				$email_to = get_post_meta( $employer_user_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
				if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $employer_id );
				}
			}
			$username = get_the_title($user_post_id);
			$post_title = get_the_title($p_post_id);

			$email_vars = array(
				'username' => $username,
				'post_title' => $post_title,
				'message' => $message,
				'message_url' => $message_url,
			);
     		
			$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
			
			$subject = WP_Freeio_Email::render_email_vars($email_vars, 'dispute_message_notice', 'subject');
			$content = WP_Freeio_Email::render_email_vars($email_vars, 'dispute_message_notice', 'content');
			
			WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
		}


		$notify_args = array(
			'post_type' => $notify_post_type,
			'user_post_id' => $user_post_id,
            'type' => 'dispute_message',
            'post_id' => $p_post_id,
            'employer_user_id' => $employer_user_id,
            'freelancer_user_id' => $freelancer_user_id,
            'dispute_id' => $dispute_id,
		);
		
		WP_Freeio_User_Notification::add_notification($notify_args);


		$return = array( 'status' => true, 'msg' => esc_html__('Message sent Successfully.', 'wp-freeio'), 'html' => self::list_dispute_messages($dispute_id) );
	   	wp_send_json($return);
	}
	
	public static function pre_post_update($post_id) {
		$dispute_id = $post_id;
		$old_resolved = get_post_meta($dispute_id, '_dispute_resolved', true);
		$old_winner = get_post_meta($dispute_id, '_dispute_winner', true);
		if ( !$old_resolved && !$old_winner ) {
			if ( isset($_REQUEST['_dispute_resolved']) && $_REQUEST['_dispute_resolved'] ) {
				$winner_id = isset($_REQUEST['_dispute_winner']) ? $_REQUEST['_dispute_winner'] : '';
				if ( $winner_id ) {
					if ( wp_freeio_get_option('dispute_winner_notice') ) {

						$my_disputes_page_id = wp_freeio_get_option('my_disputes_page_id');
						$my_disputes_url = get_permalink( $my_disputes_page_id );

						$my_disputes_url = add_query_arg( 'dispute_id', $dispute_id, remove_query_arg( 'dispute_id', $my_disputes_url ) );
						$message_url = add_query_arg( 'action', 'view-detail', remove_query_arg( 'action', $my_disputes_url ) );

						if ( WP_Freeio_User::is_employer($winner_id) ) {
							$email_to = get_post_meta( $freelancer_user_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
							if ( empty($email_to) ) {
								$email_to = get_the_author_meta( 'user_email', $freelancer_id );
							}
						} else {
							$email_to = get_post_meta( $employer_user_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
							if ( empty($email_to) ) {
								$email_to = get_the_author_meta( 'user_email', $employer_id );
							}
						}

						if ( WP_Freeio_User::is_employer($winner_id) ) {
							$employer_user_id = WP_Freeio_User::get_employer_by_user_id($winner_id);
							$notify_post_type = 'freelancer';

							$p_post_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'post_id', true);
							$post_type = get_post_type($p_post_id);
							if ( !empty($post_type) && $post_type === 'service_order' ) {
								$freelancer_id = get_post_meta( $p_post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author', true );
								$user_post_id =$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_id);
							} else {
								$freelancer_id = get_post_field( 'post_author', $p_post_id );
								$user_post_id =$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_id);
							}
						} else {
							$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($winner_id);
							$notify_post_type = 'employer';

							$p_post_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'post_id', true);
							$post_type = get_post_type($p_post_id);
							if ( !empty($post_type) && $post_type === 'service_order' ) {
								$employer_id = get_post_field( 'post_author', $p_post_id );
								$user_post_id =$employer_user_id = WP_Freeio_User::get_employer_by_user_id($employer_id);
							} else {
								$employer_id = get_post_meta( $p_post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'employer_id', true );
								$user_post_id =$employer_user_id = WP_Freeio_User::get_employer_by_user_id($employer_id);
							}
						}


						$username = get_the_title($user_post_id);
						$post_title = get_the_title($p_post_id);

						$email_vars = array(
							'username' => $username,
							'post_title' => $post_title,
							'dispute_url' => $message_url,
						);
			     		
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
						
						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'dispute_winner_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'dispute_winner_notice', 'content');
						
						WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
					}

					//

					if ( wp_freeio_get_option('dispute_loser_notice') ) {

						$sender_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'sender', true);
						$receipt_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'receipt', true);

						$loser_id = $sender_id;
						if ( $winner_id == $sender_id ) {
							$loser_id = $receipt_id;
						}

						$my_disputes_page_id = wp_freeio_get_option('my_disputes_page_id');
						$my_disputes_url = get_permalink( $my_disputes_page_id );

						$my_disputes_url = add_query_arg( 'dispute_id', $dispute_id, remove_query_arg( 'dispute_id', $my_disputes_url ) );
						$message_url = add_query_arg( 'action', 'view-detail', remove_query_arg( 'action', $my_disputes_url ) );

						if ( WP_Freeio_User::is_employer($loser_id) ) {
							$email_to = get_post_meta( $freelancer_user_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
							if ( empty($email_to) ) {
								$email_to = get_the_author_meta( 'user_email', $freelancer_id );
							}
						} else {
							$email_to = get_post_meta( $employer_user_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
							if ( empty($email_to) ) {
								$email_to = get_the_author_meta( 'user_email', $employer_id );
							}
						}

						if ( WP_Freeio_User::is_employer($loser_id) ) {
							$employer_user_id = WP_Freeio_User::get_employer_by_user_id($loser_id);
							$notify_post_type = 'freelancer';

							$p_post_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'post_id', true);
							$post_type = get_post_type($p_post_id);
							if ( !empty($post_type) && $post_type === 'service_order' ) {
								$freelancer_id = get_post_meta( $p_post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author', true );
								$user_post_id =$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_id);
							} else {
								$freelancer_id = get_post_field( 'post_author', $p_post_id );
								$user_post_id =$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_id);
							}
						} else {
							$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($loser_id);
							$notify_post_type = 'employer';

							$p_post_id = get_post_meta($dispute_id, WP_FREEIO_DISPUTE_PREFIX.'post_id', true);
							$post_type = get_post_type($p_post_id);
							if ( !empty($post_type) && $post_type === 'service_order' ) {
								$employer_id = get_post_field( 'post_author', $p_post_id );
								$user_post_id =$employer_user_id = WP_Freeio_User::get_employer_by_user_id($employer_id);
							} else {
								$employer_id = get_post_meta( $p_post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'employer_id', true );
								$user_post_id =$employer_user_id = WP_Freeio_User::get_employer_by_user_id($employer_id);
							}
						}


						$username = get_the_title($user_post_id);
						$post_title = get_the_title($p_post_id);

						$email_vars = array(
							'username' => $username,
							'post_title' => $post_title,
							'dispute_url' => $message_url,
						);
			     		
						$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
						
						$subject = WP_Freeio_Email::render_email_vars($email_vars, 'dispute_loser_notice', 'subject');
						$content = WP_Freeio_Email::render_email_vars($email_vars, 'dispute_loser_notice', 'content');
						
						WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
					}
				}
			}
		}
	}

	public static function list_dispute_messages($dispute_id) {
		return apply_filters('wp-freeio-get-list-dispute-messages', '', $dispute_id);
	}

	
}
WP_Freeio_Post_Type_Dispute::init();