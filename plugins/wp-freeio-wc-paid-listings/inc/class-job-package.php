<?php
/**
 * Job Package
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_Job_Package {
	public static function init() {
		add_filter('wp-freeio-calculate-job-expiry', array( __CLASS__, 'calculate_job_expiry' ), 10, 2 );
	}

	public static function calculate_job_expiry($duration, $job_id) {
		if ( metadata_exists( 'post', $job_id, WP_FREEIO_JOB_LISTING_PREFIX.'package_duration' ) ) {
			$duration = get_post_meta( $job_id, WP_FREEIO_JOB_LISTING_PREFIX.'package_duration', true );
		}

		return $duration;
	}
}

WP_Freeio_Wc_Paid_Listings_Job_Package::init();