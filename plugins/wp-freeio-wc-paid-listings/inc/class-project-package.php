<?php
/**
 * Project Package
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_Project_Package {
	public static function init() {
		add_filter('wp-freeio-calculate-project-expiry', array( __CLASS__, 'calculate_project_expiry' ), 10, 2 );
	}

	public static function calculate_project_expiry($duration, $project_id) {
		if ( metadata_exists( 'post', $project_id, WP_FREEIO_PROJECT_PREFIX.'package_duration' ) ) {
			$duration = get_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'package_duration', true );
		}

		return $duration;
	}
}

WP_Freeio_Wc_Paid_Listings_Project_Package::init();