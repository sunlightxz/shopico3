<?php
/**
 * Job RSS Feed
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Job_RSS_Feed {

	public static function init() {
		 add_action( 'init', array( __CLASS__, 'custom_rss' ) );
	}

	public static function custom_rss() {
        add_feed( 'job_listing_feed', array( __CLASS__, 'custom_feed_template' ) );
        add_feed( 'freelancer_listing_feed', array( __CLASS__, 'custom_freelancer_feed_template' ) );
        add_feed( 'service_listing_feed', array( __CLASS__, 'custom_service_feed_template' ) );
    }

    public static function custom_feed_template() {
        echo WP_Freeio_Template_Loader::get_template_part( 'rss-feed-jobs' );
    }

    public static function custom_freelancer_feed_template() {
        echo WP_Freeio_Template_Loader::get_template_part( 'rss-feed-freelancers' );
    }

    public static function custom_service_feed_template() {
        echo WP_Freeio_Template_Loader::get_template_part( 'rss-feed-services' );
    }
}

WP_Freeio_Job_RSS_Feed::init();