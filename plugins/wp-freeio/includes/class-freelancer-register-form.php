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

class WP_Freeio_Freelancer_Register_Form extends WP_Freeio_Abstract_Register_Form {
	public $form_name = 'wp_freeio_register_freelancer_form';
	
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

		// add_action( 'wpfi_ajax_wp_freeio_ajax_registernew',  array( $this, 'process_register_new' ) );

		add_filter( 'cmb2_meta_boxes', array( $this, 'fields_front' ) );

		add_action('wp_freeio_freelancer_signup_custom_fields_save', array($this, 'submit_process'));
	}

	public function process_register_new() {
		
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

		delete_post_meta($post_id, $this->prefix . 'password');
		delete_post_meta($post_id, $this->prefix . 'confirmpassword');
	}

}

function wp_freeio_freelancer_register_form() {
	if ( ! empty( $_POST['wp_freeio_register_freelancer_form'] ) ) {
		WP_Freeio_Freelancer_Register_Form::get_instance();
	}
}

add_action( 'init', 'wp_freeio_freelancer_register_form' );