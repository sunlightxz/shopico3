<?php
/**
 * Submit Form
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Freelancer_Register_Apply_Form extends WP_Freeio_Abstract_Register_Form {
	public $form_name = 'wp_freeio_register_apply_freelancer_form';
	
	public $post_type = 'freelancer';
	public $prefix = WP_FREEIO_FREELANCER_PREFIX;

	private static $_instance = null;

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		add_action( 'wpfi_ajax_wp_freeio_ajax_register_apply',  array( $this, 'process_register_new' ) );

		add_filter( 'cmb2_meta_boxes', array( $this, 'fields_front' ) );

		add_action('wp_freeio_freelancer_signup_custom_fields_save', array($this, 'submit_process'));
	}

	public function fields_front($metaboxes) {

		$fields = apply_filters( 'wp-freeio-register-'.$this->post_type.'-fields', array(
			array(
                'id'                => 'post_type',
                'type'              => 'hidden',
                'default'           => $this->post_type,
                'priority'          => 0,
            ),
            array(
                'name'              => __( 'Email', 'wp-freeio' ),
                'id'                => $this->prefix . 'email',
                'type'              => 'text',
                'priority'          => 0,
                'label_cb'			=> array( 'WP_Freeio_Mixes', 'required_add_label' ),
                'attributes' => array(
                	'placeholder' => esc_html__('Email', 'wp-freeio')
                )
            ),
		), $this->post_type, $this->prefix );


		$metaboxes[ $this->prefix . 'register_fields' ] = array(
			'id'                        => $this->prefix . 'register_fields',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( $this->post_type ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'fields'                    => $fields
		);

		return $metaboxes;
	}

	public function process_register_new() {
		global $reg_errors;

		$prefix = WP_FREEIO_FREELANCER_PREFIX;
		$role = 'wp_freeio_freelancer';
		$job_id = !empty($_POST['job_id']) ? $_POST['job_id'] : 0;
		$job = get_post($job_id);

		if ( !$job || empty($job->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Job doesn\'t exist', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		$filled = WP_Freeio_Job_Listing::get_post_meta($job->ID, 'filled', true);
		if ( $filled ) {
			$return = array( 'status' => false, 'msg' => esc_html__('This job is filled and no longer accepting applications.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		
		if ( !isset($_POST[$prefix.'password']) && !isset($_POST[$prefix.'confirmpassword']) ) {
			$password = $confirmpassword = wp_generate_password();
			$no_password = true;
		} else {
			$password = $_POST[$prefix.'password'];
			$confirmpassword = $_POST[$prefix.'confirmpassword'];
			$no_password = false;
		}
        WP_Freeio_User::registration_validation( $_POST[$prefix.'email'], $password, $confirmpassword, true, true, $no_password );
        if ( 1 > count( $reg_errors->get_error_messages() ) ) {

        	$email = $_POST[$prefix.'email'];
        	$usernames = explode('@', $email);

			$username = sanitize_user( str_replace(' ', '_', strtolower($usernames[0])) );

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
		        'user_pass' => $password,
		        'role' => $role,
	        );

	        $user_id = wp_insert_user( $userdata );
	        if ( ! is_wp_error( $user_id ) ) {
	        	
	        	wp_set_current_user( $user_id );
				wp_set_auth_cookie( $user_id, true );

				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
				$cv_attachments = WP_Freeio_Freelancer::get_post_meta($freelancer_id, 'cv_attachment');
				$cv_file_id = 0;
				if ( !empty($cv_attachments) && is_array($cv_attachments) ) {
					$cv_file_id = array_key_first($cv_attachments);
				}

	        	$applicant_id = WP_Freeio_Freelancer::insert_applicant($user_id, $job, $cv_file_id);

	        	if ( $applicant_id ) {
	        		do_action('wp-freeio-after-apply-job-without-login', $applicant_id, $user_id, $job, $freelancer_id);
			        $return = array( 'status' => true, 'msg' => esc_html__('You have successfully applied to the job, redirecting ...', 'wp-freeio'), 'text' => esc_html__('Applied', 'wp-freeio') );
				   	echo wp_json_encode($return);
				   	exit;
			    } else {
					$return = array( 'status' => false, 'msg' => esc_html__('Error accord when applying for the job', 'wp-freeio') );
				   	echo wp_json_encode($return);
				   	exit;
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

	public function submit_process($post_id) {
		$cmb = cmb2_get_metabox( $this->prefix . 'register_fields', $post_id );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return;
		}

		$cmb->save_fields( $post_id, 'post', $_POST );
		
		// Create featured image
		$featured_image = get_post_meta( $post_id, $this->prefix . 'featured_image', true );
		
		if ( !empty($featured_image) ) {
			if ( is_array($featured_image) ) {
				$img_id = $featured_image[0];
			} elseif ( is_integer($featured_image) ) {
				$img_id = $featured_image;
			} else {
				$img_id = WP_Freeio_Image::get_attachment_id_from_url($featured_image);
			}
			set_post_thumbnail( $post_id, $img_id );
		}
	}

	public function form_output() {
		global $post;
		$metaboxes = apply_filters( 'cmb2_meta_boxes', array() );
		if ( ! isset( $metaboxes[ $this->prefix . 'register_fields' ] ) ) {
			return __( 'A metabox with the specified \'metabox_id\' doesn\'t exist.', 'wp-freeio' );
		}
		$metaboxes_form = $metaboxes[ $this->prefix . 'register_fields' ];

		wp_enqueue_script('wpfi-select2');
		wp_enqueue_style('wpfi-select2');

		echo WP_Freeio_Template_Loader::get_template_part( 'misc/register-apply-'.$this->post_type, array(
			'post_id' => 0,
			'job_id' => $post->ID,
			'metaboxes_form' => $metaboxes_form,
			'form_obj'       => $this,
			'submit_button_text' => apply_filters( 'wp_freeio_register_apply_'.$this->post_type.'_form_submit_button_text', __( 'Apply now', 'wp-freeio' ) ),
		) );
	}
}

function wp_freeio_freelancer_register_apply_form() {
	if ( ! empty( $_POST['wp_freeio_register_apply_freelancer_form'] ) ) {
		WP_Freeio_Freelancer_Register_Apply_Form::get_instance();
	}
}

add_action( 'init', 'wp_freeio_freelancer_register_apply_form' );