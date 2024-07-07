<?php
/**
 * Post Type: Verification
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Verification {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );
	  	
	  	add_filter( 'manage_edit-verification_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_verification_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) , 10 );

		add_action( 'wpfi_ajax_wp_freeio_ajax_verification_identity',  array(__CLASS__, 'process_verification_identity') );
		add_action( 'wpfi_ajax_wp_freeio_ajax_revoke_verification_identity',  array(__CLASS__, 'process_revoke_verification_identity') );

		add_action( 'pending_to_publish', array( __CLASS__, 'process_pending_to_publish' ) );
	}

	public static function admin_menu() {
		//Settings
	 	
	 	$pending_approve = wp_count_posts( 'verification' )->pending_approve;
		$pending = wp_count_posts( 'verification' )->pending;
		$count = $pending_approve + $pending;

		$menu_title = __( 'Verifications', 'wp-freeio' );
		if ( $count > 0 ) {
			$menu_title = sprintf('%s <span class="awaiting-mod"><span class="pending-count">%d</span></span>', $menu_title, $count );
		}
		
		add_submenu_page('freelancer-settings', __( 'Verifications', 'wp-freeio' ), $menu_title, 'manage_options', 'edit.php?post_type=verification');
	}

	public static function register_post_type() {
		$singular = __( 'Verification', 'wp-freeio' );
		$plural   = __( 'Verifications', 'wp-freeio' );

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
		
		register_post_type( 'verification',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title' ),
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
		
		$fields = array();
		
		$fields[] = array(
			'name'              => __( 'Contact Number', 'wp-freeio' ),
			'id'                => WP_FREEIO_VERIFICATION_PREFIX . 'contact_number',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'CNIC / Passport / NIN / SSN', 'wp-freeio' ),
			'id'                => WP_FREEIO_VERIFICATION_PREFIX . 'verification_number',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Upload Document', 'wp-freeio' ),
			'id'                => WP_FREEIO_VERIFICATION_PREFIX . 'document',
			'type'              => 'file_list',
		);

		$fields[] = array(
			'name'              => __( 'Address', 'wp-freeio' ),
			'id'                => WP_FREEIO_VERIFICATION_PREFIX . 'address',
			'type'              => 'text',
		);

		$metaboxes[ WP_FREEIO_VERIFICATION_PREFIX . 'general' ] = array(
			'id'                        => WP_FREEIO_VERIFICATION_PREFIX . 'general',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( 'verification' ),
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
			'contact_number' 			=> __( 'Contact Number', 'wp-freeio' ),
			'verification_number' 			=> __( 'Verification Number', 'wp-freeio' ),
			'address' 			=> __( 'Address', 'wp-freeio' ),
			'date' 				=> esc_html__( 'Date', 'wp-freeio' ),
			'post_author' 			=> esc_html__( 'Author', 'wp-freeio' ),
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
			case 'contact_number':
				echo get_post_meta( get_the_ID(), WP_FREEIO_VERIFICATION_PREFIX . 'contact_number', true );
			break;
			case 'verification_number':
				echo get_post_meta( get_the_ID(), WP_FREEIO_VERIFICATION_PREFIX . 'verification_number', true );
			break;
			case 'address':
				echo get_post_meta( get_the_ID(), WP_FREEIO_VERIFICATION_PREFIX . 'address', true );
			break;
			case 'post_author':
				$author_id = get_the_author_meta('ID');
				if ( WP_Freeio_User::is_freelancer($author_id) ) {
					$post_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);
				} elseif ( WP_Freeio_User::is_employer($author_id) ) {
					$post_id = WP_Freeio_User::get_employer_by_user_id($author_id);
				}
				if ( !empty($post_id) ) {
					?>
					<a href="post.php?post=<?php echo $post_id; ?>&action=edit"><?php echo get_the_title($post_id); ?></a>
					<?php
				}
			break;
		}
	}

	public static function process_verification_identity() {
		$return = array();

		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to verification identity.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$user_id = WP_Freeio_User::get_user_id();

		// sent verification
		$verification_sent = intval(0);
        $args = array(
            'post_type' => 'verification',
            'author'    =>  $user_id,
            'fields' => 'ids'
        );

        $query = new WP_Query( $args );
        if( !empty( $query ) ){
           $verification_sent =  $query->found_posts;
        }
        if( $verification_sent > 0 ) {
            $return = array( 'status' => false, 'msg' => esc_html__('You have already sent the verification', 'wp-freeio') );
            wp_send_json( $return );
        }


        // field empty
		$name = !empty($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
		$contact_number = !empty($_POST['contact_number']) ? sanitize_textarea_field($_POST['contact_number']) : '';
		$verification_number = !empty($_POST['verification_number']) ? sanitize_textarea_field($_POST['verification_number']) : '';
		$address = !empty($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '';
		if ( empty($name) || empty($contact_number) || empty($verification_number) || empty($address) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please fill all form fields.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

        // cv file
        $document_attachments = array();
        if ( !empty($_FILES['document']) && !empty($_FILES['document']['name']) ) {

			$files = $_FILES['document'];

    		$has_file = false;
    		$return = array();
		    foreach ($files['name'] as $key => $value) {            
	            if ($files['name'][$key]) { 
	            	$has_file = true;
	                $file = array( 
	                    'name' => $files['name'][$key],
	                    'type' => $files['type'][$key], 
	                    'tmp_name' => $files['tmp_name'][$key], 
	                    'error' => $files['error'][$key],
	                    'size' => $files['size'][$key]
	                ); 
	                $_FILES = array( 'document' => $file ); 
	                foreach ($_FILES as $file => $array) {              
	                    $file_data = WP_Freeio_Image::upload_file($_FILES['document']);
	                    if ( $file_data && !empty($file_data->url) ) {
			            	$document_id = WP_Freeio_Image::create_attachment( $file_data->url, 0 );

			            	if ( !empty($document_id) ) {
			            		$document_attachments = array($document_id => $file_data->url);
				            }
			            }
	                }
	            } 
	        }

	        if ( empty($document_attachments) ) {
				$return = array( 'status' => false, 'msg' => esc_html__('Can not upload file.', 'wp-freeio') );
			   	echo wp_json_encode($return);
			   	exit;
			}
	        
        }

		do_action('wp-freeio-process-save-verification-identity-before', $_POST);

		$report_post = array(
			'post_title'    => wp_strip_all_tags( $name ), //report title
			'post_status'   => 'pending',
			'post_author'   => $user_id,
			'post_type'     => 'verification',
		);

		$report_id = wp_insert_post( $report_post );
		if ( !is_wp_error( $report_id ) ) {
			update_post_meta( $report_id, WP_FREEIO_VERIFICATION_PREFIX.'contact_number', $contact_number );
			update_post_meta( $report_id, WP_FREEIO_VERIFICATION_PREFIX.'verification_number', $verification_number );
			update_post_meta( $report_id, WP_FREEIO_VERIFICATION_PREFIX.'address', $address );
			update_post_meta( $report_id, WP_FREEIO_VERIFICATION_PREFIX.'document', $document_attachments );


			if ( wp_freeio_get_option('admin_notice_user_verification') ) {

				$email_to = get_option( 'admin_email', false );

				if ( WP_Freeio_User::is_employer($user_id) ) {
					$user_post_id = WP_Freeio_User::get_employer_by_user_id($user_id);
				} else {
					$user_post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
				}
				$user_name = get_the_title($user_post_id);

				$email_vars = array(
					'user_name' => $user_name,
					'contact_number' => $contact_number,
					'verification_number' => $verification_number,
					'address' => $address,
					'verification_url' => get_edit_post_link($report_id)
				);
	     		
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
				
				$subject = WP_Freeio_Email::render_email_vars($email_vars, 'admin_notice_user_verification', 'subject');
				$content = WP_Freeio_Email::render_email_vars($email_vars, 'admin_notice_user_verification', 'content');
				
				$files_path = array();
				if ( $document_attachments ) {
					foreach ($document_attachments as $attach_id => $url) {
						$files_path[] = get_attached_file($attach_id);
					}
				}
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers, $files_path );
			}


			$return = array( 'status' => true, 'msg' => esc_html__('Your verification identity has sent Successfully.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		$return = array( 'status' => false, 'msg' => esc_html__('An error occured when save a verification identity.', 'wp-freeio') );
	   	wp_send_json($return);
	}

	public static function process_pending_to_publish($post) {
		if ( $post->post_type === 'verification' ) {
			$author_id = $post->post_author;

			$user_name = '';
			$email_to = '';
			if ( WP_Freeio_User::is_freelancer($author_id) ) {
				$post_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);

				update_post_meta($post_id, WP_FREEIO_FREELANCER_PREFIX.'verified', 'yes');

				$user_name = get_the_title($post_id);
				$email_to = get_post_meta( $post_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
				$notify_post_type = 'employer';
			} elseif ( WP_Freeio_User::is_employer($author_id) ) {
				$post_id = WP_Freeio_User::get_employer_by_user_id($author_id);

				update_post_meta($post_id, WP_FREEIO_EMPLOYER_PREFIX.'verified', 'yes');

				$user_name = get_the_title($post_id);
				$email_to = get_post_meta( $post_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
				$notify_post_type = 'freelancer';
			}
			if ( empty($email_to) ) {
				$email_to = get_the_author_meta( 'user_email', $author_id );
			}

			// send email
			if ( wp_freeio_get_option('user_notice_admin_approve_verification') ) {
				
				// $email_to = get_option( 'admin_email', false );

				$email_vars = array(
					'user_name' => $user_name,
				);
	     		
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), get_option( 'admin_email', false ) );
				
				$subject = WP_Freeio_Email::render_email_vars($email_vars, 'user_notice_admin_approve_verification', 'subject');
				$content = WP_Freeio_Email::render_email_vars($email_vars, 'user_notice_admin_approve_verification', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}

			// send notification
			$notify_args = array(
				'post_type' => $notify_post_type,
				'user_post_id' => $post_id,
	            'type' => 'approve_verification',
	            'employer_user_id' => $author_id,
	            'freelancer_user_id' => $author_id,
			);
			
			WP_Freeio_User_Notification::add_notification($notify_args);
		}
	}

	public static function process_revoke_verification_identity() {
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to revoke verification identity.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$user_id = WP_Freeio_User::get_user_id();

		// sent verification
		$verification_sent = intval(0);
        $args = array(
            'post_type' => 'verification',
            'author'    =>  $user_id,
            'fields' => 'ids'
        );

        $query = new WP_Query( $args );
        if( !empty( $query ) ){
           $verification_sent =  $query->found_posts;
        }
        if( $verification_sent <= 0 ) {
            $return = array( 'status' => false, 'msg' => esc_html__('You have not already sent the verification', 'wp-freeio') );
            wp_send_json( $return );
        }

        foreach ($query->posts as $post_id) {
        	wp_delete_post( $post_id, true );
        }

        if ( WP_Freeio_User::is_freelancer($user_id) ) {
			$post_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);

			update_post_meta($post_id, WP_FREEIO_FREELANCER_PREFIX.'verified', '');
		} elseif ( WP_Freeio_User::is_employer($user_id) ) {
			$post_id = WP_Freeio_User::get_employer_by_user_id($user_id);

			update_post_meta($post_id, WP_FREEIO_EMPLOYER_PREFIX.'verified', '');
		}

        $return = array( 'status' => true, 'msg' => esc_html__('Your revoke verification identity has sent Successfully', 'wp-freeio') );
        wp_send_json( $return );
	}
}
WP_Freeio_Post_Type_Verification::init();