<?php
/**
 * Post Type: Report
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Report {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	
	  	add_filter( 'manage_edit-report_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_report_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) , 10 );

		add_action( 'wpfi_ajax_wp_freeio_ajax_report_form',  array(__CLASS__, 'process_report') );
	}

	public static function admin_menu() {
		//Settings
	 	
		$pending_approve = wp_count_posts( 'report' )->pending_approve;
		$pending = wp_count_posts( 'report' )->pending;
		$count = $pending_approve + $pending;

		$menu_title = __( 'Reports', 'wp-freeio' );
		if ( $count > 0 ) {
			$menu_title = sprintf('%s <span class="awaiting-mod"><span class="pending-count">%d</span></span>', $menu_title, $count );
		}
		
		add_submenu_page('freelancer-settings', __( 'Reports', 'wp-freeio' ), $menu_title, 'manage_options', 'edit.php?post_type=report');
	}

	public static function register_post_type() {
		$singular = __( 'Report', 'wp-freeio' );
		$plural   = __( 'Reports', 'wp-freeio' );

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
		
		register_post_type( 'report',
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
			'author' 			=> esc_html__( 'Report By', 'wp-freeio' ),
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
				$post_type = get_post_meta( get_the_ID(), WP_FREEIO_REPORT_PREFIX . 'post_type', true );
				$post_type_obj = get_post_type_object($post_type);
				if ( !is_wp_error($post_type_obj) ) {
					echo $post_type_obj->labels->singular_name;
				}
			break;
			case 'post_title':
				$post_id = get_post_meta( get_the_ID(), WP_FREEIO_REPORT_PREFIX . 'post_id', true );
				?>
				<a href="post.php?post=<?php echo $post_id; ?>&action=edit"><?php echo get_the_title($post_id); ?></a>
				<?php
			break;
		}
	}

	public static function process_report() {
		$return = array();

		if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) {
			$is_recaptcha_valid = array_key_exists( 'g-recaptcha-response', $_POST ) ? WP_Freeio_Recaptcha::is_recaptcha_valid( sanitize_text_field( $_POST['g-recaptcha-response'] ) ) : false;
			if ( !$is_recaptcha_valid ) {
	            $return = array( 'status' => false, 'msg' => esc_html__('Captcha is not valid.', 'wp-freeio') );
		   		wp_send_json( $return );
			}
		}

		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to report.', 'wp-freeio') );
		   	wp_send_json( $return );
		}
		$user_id = WP_Freeio_User::get_user_id();

		$post_id = !empty($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : 0;
		$post_type = get_post_type($post_id);

		if ( empty($post_id) || !in_array($post_type, array('project', 'service', 'job_listing', 'employer', 'freelancer')) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('This post is not exists.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		// sent report
		$report_sent = intval(0);
        $args = array(
            'post_type' => 'report',
            'author'    =>  $user_id,
            'meta_query' => array(
                array(
                    'key'     => WP_FREEIO_REPORT_PREFIX.'post_id',
                    'value'   => intval( $post_id ),
                    'compare' => '=',
                ),
            ),
        );

        $query = new WP_Query( $args );
        if( !empty( $query ) ){
           $report_sent =  $query->found_posts;
        }
        if( $report_sent > 0 ) {
            $return = array( 'status' => false, 'msg' => esc_html__('You have already sent the report', 'wp-freeio') );
            wp_send_json( $return );
        }

        // field empty
		$subject = !empty($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
		$message = !empty($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
		if ( empty($subject) || empty($message) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please fill all form fields.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		do_action('wp-freeio-process-save-report-before', $_POST);

		$report_post = array(
			'post_title'    => wp_strip_all_tags( $subject ), //report title
			'post_status'   => 'pending',
			'post_content'  => $message,
			'post_author'   => $user_id,
			'post_type'     => 'report',
		);

		$report_id = wp_insert_post( $report_post );
		if ( !is_wp_error( $report_id ) ) {
			update_post_meta( $report_id, WP_FREEIO_REPORT_PREFIX.'post_type', $post_type );
			update_post_meta( $report_id, WP_FREEIO_REPORT_PREFIX.'post_id', $post_id );

			$user_email = get_option( 'admin_email', false );
			$email_subject = WP_Freeio_Email::render_email_vars(
				array(
					'post_title' => get_the_title($post_id),
					'subject' => $subject
				),
				'report_notice', 'subject'
			);
			$email_content = WP_Freeio_Email::render_email_vars(
				array(
					'post_title' => get_the_title($post_id),
					'post_url' => get_permalink($post_id),
					'subject' => $subject,
					'message' => $message
				),
				'report_notice', 'content'
			);
			
			$email_from = get_option( 'admin_email', false );
			$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
			// send the mail
			WP_Freeio_Email::wp_mail( $email_from, $email_subject, $email_content, $headers );
			
			$return = array( 'status' => true, 'msg' => esc_html__('Your report has sent Successfully.', 'wp-freeio') );
		   	wp_send_json( $return );
		}

		$return = array( 'status' => false, 'msg' => esc_html__('An error occured when save a report.', 'wp-freeio') );
	   	wp_send_json($return);
	}
}
WP_Freeio_Post_Type_Report::init();