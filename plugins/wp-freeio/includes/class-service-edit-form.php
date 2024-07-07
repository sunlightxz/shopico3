<?php
/**
 * Edit Form
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Service_Edit_Form extends WP_Freeio_Service_Abstract_Form {
	
	public $form_name = 'wp_freeio_service_edit_form';
	
	private static $_instance = null;

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		add_action( 'wp', array( $this, 'submit_process' ) );

		$this->service_id = ! empty( $_REQUEST['service_id'] ) ? absint( $_REQUEST['service_id'] ) : 0;

		if ( ! WP_Freeio_User::is_freelancer_can_edit_service( $this->service_id ) ) {
			$this->service_id = 0;
		}

		parent::__construct();
	}

	public function output( $atts = array() ) {
		ob_start();
		$this->form_output();
		$output = ob_get_clean();
		return $output;
	}

	public function submit_process() {
		
		if ( ! isset( $_POST['submit-cmb-service'] ) || empty( $_POST[WP_FREEIO_SERVICE_PREFIX.'post_type'] ) || 'service' !== $_POST[WP_FREEIO_SERVICE_PREFIX.'post_type'] ) {
			return;
		}
		
		$cmb = cmb2_get_metabox( WP_FREEIO_SERVICE_PREFIX . 'front' );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return;
		}
		// Setup and sanitize data
		if ( isset( $_POST[ WP_FREEIO_SERVICE_PREFIX . 'title' ] ) && !empty($this->service_id) ) {
			$post_id = $this->service_id;

			$old_post = get_post( $post_id );
			$post_date = $old_post->post_date;
			$old_post_status = get_post_status( $post_id );
			if ( $old_post_status === 'draft' ) {
				$post_status = 'preview';
			} elseif ( $old_post_status === 'publish' ) {
				$review_before = wp_freeio_get_option( 'user_edit_published_submission_service' );
				$post_status = 'publish';
				if ( $review_before == 'yes_moderated' ) {
					$post_status = 'pending_approve';
				}
			} else {
				$post_status = $old_post_status;
			}

			$user_id = WP_Freeio_User::get_user_id();
			$employer_user_id = WP_Freeio_User::get_user_id($user_id);
			$data = array(
				'post_title'     => sanitize_text_field( $_POST[ WP_FREEIO_SERVICE_PREFIX . 'title' ] ),
				'post_author'    => $employer_user_id,
				'post_status'    => $post_status,
				'post_type'      => 'service',
				'post_date'      => $post_date,
				'post_content'   => wp_kses_post( $_POST[ WP_FREEIO_SERVICE_PREFIX . 'description' ] ),
				'ID' 			 => $post_id
			);

			do_action( 'wp-freeio-process-edit-service-before-save', $post_id, $this );

			$data = apply_filters('wp-freeio-process-edit-service-data', $data, $post_id);
			
			$this->errors = $this->edit_validate($data);
			if ( sizeof($this->errors) ) {
				return;
			}

			$post_id = wp_update_post( $data, true );

			if ( ! empty( $post_id ) && ! empty( $_POST['object_id'] ) ) {
				$_POST['object_id'] = $post_id; // object_id in POST contains page ID instead of service ID

				$cmb->save_fields( $post_id, 'post', $_POST );

				// Create featured image
				$featured_image = get_post_meta( $post_id, WP_FREEIO_SERVICE_PREFIX . 'featured_image', true );
				if ( ! empty( $_POST[ 'current_' . WP_FREEIO_SERVICE_PREFIX . 'featured_image' ] ) ) {
					$img_id = get_post_meta( $post_id, WP_FREEIO_SERVICE_PREFIX . 'featured_image_img', true );
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
						update_post_meta( $post_id, WP_FREEIO_SERVICE_PREFIX . 'featured_image', null );
						delete_post_thumbnail( $post_id );
					}
				} else {
					update_post_meta( $post_id, WP_FREEIO_SERVICE_PREFIX . 'featured_image', null );
					delete_post_thumbnail( $post_id );
				}

				do_action( 'wp-freeio-process-edit-service-after-save', $post_id );
				
				// send email
				if ( wp_freeio_get_option('admin_notice_updated_service') ) {
					$service = get_post($this->service_id);
					$email_from = get_option( 'admin_email', false );
					
					$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
					$email_to = get_option( 'admin_email', false );
					$subject = WP_Freeio_Email::render_email_vars(array('service' => $service), 'admin_notice_updated_service', 'subject');
					$content = WP_Freeio_Email::render_email_vars(array('service' => $service), 'admin_notice_updated_service', 'content');
					
					WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				}
				$this->success_msg[] = __( 'Your changes have been saved.', 'wp-freeio' );
			} else {
				$this->errors[] = __( 'Can not update service', 'wp-freeio' );
			}
		}

		return;
	}

	public function edit_validate( $data ) {
		$error = array();
		if ( !is_user_logged_in() ) {
			$error[] = __( 'Please login to submit service', 'wp-freeio' );
		}
		if ( empty($data['post_title']) ) {
			$error[] = __( 'Title is required.', 'wp-freeio' );
		}
		if ( empty($data['post_content']) ) {
			$error[] = __( 'Description is required.', 'wp-freeio' );
		}

		$error = apply_filters('wp-freeio-edit-validate', $error);

		return $error;
	}

}

function wp_freeio_service_edit_form() {
	if ( ! empty( $_POST['wp_freeio_service_edit_form'] ) ) {
		WP_Freeio_Service_Edit_Form::get_instance();
	}
}

add_action( 'init', 'wp_freeio_service_edit_form' );