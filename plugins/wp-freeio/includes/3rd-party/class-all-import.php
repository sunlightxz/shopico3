<?php
/**
 * All Import
 *
 * @package    wp-job-board
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Job_Board_All_Import {
	
	public static function init() {
		add_action( 'pmxi_saved_post', array(__CLASS__, 'pmxi_saved_post' ), 10, 1 );
	}

	public static function pmxi_saved_post( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( 'job_listing' === $post_type ) {
			$location = get_post_meta( $post_id, WP_FREEIO_JOB_LISTING_PREFIX.'address', true );
			if ( $location ) {
				WP_Job_Board_Geocode::generate_location_data( $post_id, $location );
			}
		} elseif ( in_array($post_type, array('employer', 'freelancer')) ) {
			update_post_meta($post_id, $prefix . '_'.$post_type.'_show_profile', 'show');
			WP_Freeio_User::generate_user_by_post($post_id);
		}
	}

}

WP_Job_Board_All_Import::init();