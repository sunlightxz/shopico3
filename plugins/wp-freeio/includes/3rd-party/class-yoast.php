<?php
/**
 * Yoast
 *
 * @package    wp-job-board
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Yoast {
	
	public static function init() {
		add_action( 'wpseo_sitemap_entry', array(__CLASS__, 'skip_filled_job_listings'), 10, 3 );
	}

	public static function skip_filled_job_listings($url, $type, $post) {
		if ( 'job_listing' !== $post->post_type && 'freelancer' !== $post->post_type ) {
			return $url;
		}

		if ( $post->post_type == 'job_listing' ) {
			if ( $post->post_status == 'expired' || WP_Freeio_Job_Listing::is_filled( $post->ID ) ) {
				return false;
			}
		} elseif ( $post->post_type == 'freelancer' ) {
			$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);
			if ( $post->post_status == 'expired' || ($meta_obj->check_post_meta_exist('show_profile') && $meta_obj->get_post_meta('show_profile') == 'hide') ) {
				return false;
			}
		} elseif ( $post->post_type == 'employer' ) {
			$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);
			if ( $post->post_status == 'expired' || ($meta_obj->check_post_meta_exist('show_profile') && $meta_obj->get_post_meta('show_profile') == 'hide') ) {
				return false;
			}
		}

		return $url;
	}

}

WP_Freeio_Yoast::init();