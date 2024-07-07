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

class WP_Freeio_Employer_Register_Form extends WP_Freeio_Abstract_Register_Form {
	public $form_name = 'wp_freeio_register_employer_form';
	
	public $post_type = 'employer';
	public $prefix = WP_FREEIO_EMPLOYER_PREFIX;

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

		add_action('wp_freeio_employer_signup_custom_fields_save', array($this, 'submit_process'));
	}

	public function process_register_new() {
		
	}

	public function submit_process($post_id) {
		$cmb = cmb2_get_metabox( $this->prefix . 'register_fields', $post_id );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return;
		}

		$cmb->save_fields( $post_id, 'post', $_POST );

		if ( !empty($_POST[$this->prefix.'team_members']) ) {
			$team_members = $_POST[$this->prefix.'team_members'];
			if ( isset($_POST['current_'.$this->prefix.'team_members']) ) {
				foreach ($_POST['current_'.$this->prefix.'team_members'] as $gkey => $ar_value) {
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
				update_post_meta( $post_id, $this->prefix.'team_members', $team_members );
			}
		}

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

function wp_freeio_employer_register_form() {
	if ( ! empty( $_POST['wp_freeio_register_employer_form'] ) ) {
		WP_Freeio_Employer_Register_Form::get_instance();
	}
}

add_action( 'init', 'wp_freeio_employer_register_form' );