<?php
/**
 * Service Package
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_Service_Package {
	public static function init() {
		add_filter('wp-freeio-calculate-service-expiry', array( __CLASS__, 'calculate_service_expiry' ), 10, 2 );
	}

	public static function calculate_service_expiry($duration, $service_id) {
		if ( metadata_exists( 'post', $service_id, WP_FREEIO_SERVICE_PREFIX.'package_duration' ) ) {
			$duration = get_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'package_duration', true );
		}

		return $duration;
	}
}

WP_Freeio_Wc_Paid_Listings_Service_Package::init();