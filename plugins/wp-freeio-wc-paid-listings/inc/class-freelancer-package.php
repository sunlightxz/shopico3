<?php
/**
 * Freelancer Package
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_Freelancer_Package {
	public static function init() {
		add_filter( 'wp-freeio-check-freelancer-can-apply', array( __CLASS__, 'process_freelancer_can_apply' ), 10 );

		add_action( 'wp-freeio-before-after-job-applicant', array( __CLASS__, 'process_added_applicant' ), 10, 4 );
		add_action( 'wp-freeio-after-remove-applied', array( __CLASS__, 'process_removed_applicant' ), 10, 5 );
	}

	public static function process_freelancer_can_apply($return) {
		$free_apply = wp_freeio_get_option('freelancer_free_job_apply', 'on');
		if ( $free_apply == 'off' ) {
			$return = false;
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				if ( class_exists('WP_Freeio_User') && WP_Freeio_User::is_freelancer($user_id) ) {
					$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_freelancer_packages_by_user($user_id);
					if ( !empty($packages) ) {
						$return = true;
					}
				}
			}
		}
		
		return apply_filters( 'wp-freeio-wc-paid-listings-process-freelancer-can-apply', $return );
	}

	public static function process_added_applicant($applicant_id, $job_id, $freelancer_id, $user_id) {
		$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_freelancer_packages_by_user($user_id);
		if ( !empty($packages) && !empty($packages[0]) ) {
			$package = $packages[0];
			WP_Freeio_Wc_Paid_Listings_Mixes::increase_freelancer_package_applied_count($applicant_id, $user_id, $package->ID);
			update_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX . 'freelancer_package_id', $package->ID);
		}
	}

	public static function process_removed_applicant($applicant_id, $job_id, $freelancer_id, $freelancer_package_id, $data) {
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_FREELANCER_PREFIX;
		if ( $freelancer_package_id ) {
			$freelancer_applied_counts = array();
			$freelancer_applied_count = get_post_meta($freelancer_package_id, $prefix.'freelancer_applied_count', true);
			if ( !empty($freelancer_applied_count) ) {
				$freelancer_applied_counts = array_map( 'trim', explode(',', $freelancer_applied_count) );
				if ( in_array($applicant_id, $freelancer_applied_counts) ) {
					$key = array_search($applicant_id, $freelancer_applied_counts);
					unset($freelancer_applied_counts[$key]);
				}
			}
			$freelancer_applied_counts = !empty($freelancer_applied_counts) ? implode(',', $freelancer_applied_counts) : '';
			update_post_meta( $freelancer_package_id, $prefix.'freelancer_applied_count', $freelancer_applied_counts );
		}
	}

}

WP_Freeio_Wc_Paid_Listings_Freelancer_Package::init();