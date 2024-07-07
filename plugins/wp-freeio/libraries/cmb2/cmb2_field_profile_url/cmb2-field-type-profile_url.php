<?php
/**
 * CMB2 File
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_CMB2_Field_Profile_Url {

	public static function init() {
		add_filter( 'cmb2_render_wp_freeio_profile_url', array( __CLASS__, 'render_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wp_freeio_profile_url', array( __CLASS__, 'sanitize_map' ), 10, 4 );

		// Ajax endpoints.
		add_action( 'wpfi_ajax_wp_freeio_ajax_change_slug',  array(__CLASS__,'process_change_slug') );

		// compatible handlers.
		add_action( 'wp_ajax_wp_freeio_ajax_change_slug',  array(__CLASS__,'process_change_slug') );
	}

	/**
	 * Render field
	 */
	public static function render_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		
		$post_slug = $base_slug = '';
		if ( $field_object_id ) {
			self::setup_admin_scripts();
			if ( get_post_type($field_object_id) == 'employer' ) {
				$base_slug = get_option('wp_freeio_employer_base_slug') ? get_option('wp_freeio_employer_base_slug') : 'employer';
			} elseif ( get_post_type($field_object_id) == 'freelancer' ) {
				$base_slug = get_option('wp_freeio_freelancer_base_slug') ? get_option('wp_freeio_freelancer_base_slug') : 'freelancer';
			}
			$post_slug = get_post_field( 'post_name', $field_object_id );
		
		
			$profile_url = get_permalink($field_object_id);

			$html = '<div class="profile-url-wrapper">';
			$html .= '<div class="profile-url"><span class="post-slug">'.$profile_url.'</span> <a class="text-theme edit-profile-slug" href="javascript:void(0);">'.esc_html__('Edit', 'wp-freeio').'</a>
				
				</div>';
			
			$html .= '<div class="profile-url-edit-wrapper">
				<input type="text" class="profile-slug-input" name="profile_url_slug" value="'.$post_slug.'">
				<a class="save-profile-slug btn btn-theme" href="javascript:void(0);" data-post_id="'.$field_object_id.'" data-nonce="'.wp_create_nonce( 'wp-freeio-change-slug-nonce' ).'">'.esc_html__('Save', 'wp-freeio').'</a>
			';
			$html .= '</div>';
			$html .= '</div>';

			echo $html;
		}
	}

	public static function sanitize_map( $override_value, $value, $object_id, $field_args ) {
		return $value;
	}

	public static function process_change_slug() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-change-slug-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to edit slug.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( empty($_POST['profile_url_slug']) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Slug is empty.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		if (isset($_POST['profile_url_slug']) && $_POST['profile_url_slug'] != '') {
            $profile_url_slug = sanitize_text_field($_POST['profile_url_slug']);
            $profile_url_slug = sanitize_title($profile_url_slug);
            
            $user_id = WP_Freeio_User::get_user_id();

            if ( WP_Freeio_User::is_freelancer($user_id) ) {
                $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
                $up_post = array(
                    'ID' => $freelancer_id,
                    'post_name' => $profile_url_slug,
                );

                do_action('wp-freeio-before-change-slug');

                wp_update_post($up_post);
                
                //
                $post_obj = get_post($freelancer_id);
                $user_profile_url = isset($post_obj->post_name) ? $post_obj->post_name : '';
                $profile_url = get_permalink($freelancer_id);

                $return = array( 'status' => true, 'text' => urldecode($user_profile_url), 'url' => urldecode($profile_url) );
                echo wp_json_encode($return);
		   		exit;
            }
            if ( WP_Freeio_User::is_employer($user_id) ) {
                $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
                $up_post = array(
                    'ID' => $employer_id,
                    'post_name' => $profile_url_slug,
                );
                
                do_action('wp-freeio-before-change-slug');

                wp_update_post($up_post);
                
                //
                $post_obj = get_post($employer_id);
                $user_profile_url = isset($post_obj->post_name) ? $post_obj->post_name : '';
                $profile_url = get_permalink($employer_id);

                $return = array( 'status' => true, 'text' => urldecode($user_profile_url), 'url' => urldecode($profile_url) );
                echo wp_json_encode($return);
		   		exit;
            }
        }
        $return = array( 'status' => false, 'msg' => esc_html__('Can not change slug.', 'wp-freeio') );
	   	echo wp_json_encode($return);
	   	exit;
	}

	public static function setup_admin_scripts() {
		wp_enqueue_script( 'profile-url-script', plugins_url( 'js/script.js', __FILE__ ), array(), '1.0' );
		wp_localize_script( 'profile-url-script', 'wp_freeio_profile_url_opts', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'ajaxurl_endpoint' => WP_Freeio_Ajax::get_endpoint(),
		));
	}
}

WP_Freeio_CMB2_Field_Profile_Url::init();