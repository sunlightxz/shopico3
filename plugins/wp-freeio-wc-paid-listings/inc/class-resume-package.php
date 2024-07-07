<?php
/**
 * Resume Package
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_Resume_Package {
	public static function init() {
		add_filter('wp-freeio-calculate-freelancer-expiry', array( __CLASS__, 'calculate_resume_expiry' ), 10, 2 );

		add_action( 'wp-freeio-resume-form-status', array( __CLASS__, 'packages' ), 10, 2 );

		add_filter('wp-freeio-create-freelancer-post-args', array( __CLASS__, 'create_freelancer_args' ), 10, 1);
	}

	public static function calculate_resume_expiry($duration, $freelancer_id) {
		if ( metadata_exists( 'post', $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'package_duration' ) ) {
			$duration = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'package_duration', true );
		}

		return $duration;
	}

	public static function packages($post_status, $post_id) {
		if ( $post_status == 'expired' || $post_status == 'pending_payment' ) {
			$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_resume_package_products();
			echo WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('resume-packages', array('packages' => $packages) );
		} elseif ( $post_status == 'pending' || $post_status == 'pending_approve' ) {
			$user_package_id = get_post_meta( $post_id, '_user_package_id', true );
			if ( empty($user_package_id) ) {
				$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_resume_package_products();
				echo WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('resume-packages', array('packages' => $packages) );
			}
		}
	}

	public static function create_freelancer_args($post_args) {

		$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_resume_package_products();
		if ( !empty($packages) ) {
			$post_args['post_status'] = 'pending_payment';
		}
		return $post_args;
	}

}

WP_Freeio_Wc_Paid_Listings_Resume_Package::init();