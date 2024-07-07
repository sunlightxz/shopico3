<?php
/**
 * CV Package
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_CV_Package {
	public static function init() {
		add_filter( 'wp-freeio-restrict-freelancer-detail', array( __CLASS__, 'restrict_freelancer' ), 10 );
		add_filter( 'wp-freeio-restrict-freelancer-listing', array( __CLASS__, 'restrict_freelancer' ), 10 );

		add_filter( 'wp-freeio-check-view-freelancer-detail', array( __CLASS__, 'process_restrict_freelancer_detail' ), 10, 2 );
		add_filter( 'wp-freeio-check-view-freelancer-listing-query-args', array( __CLASS__, 'process_restrict_freelancer_listing' ), 10, 2 );

		add_action( 'wp_freeio_before_job_detail', array( __CLASS__, 'process_viewed_freelancer' ), 10 );

		add_filter( 'wp-freeio-restrict-freelancer-detail-information', array( __CLASS__, 'restrict_freelancer_information' ), 10, 2 );
		add_action( 'wp-freeio-restrict-freelancer-listing-default-information', array( __CLASS__, 'restrict_freelancer_listing_information' ), 10, 2 );
	}

	public static function restrict_freelancer($fields) {
		if ( !isset($fields['register_employer_with_package']) ) {
			$fields['register_employer_with_package'] = __( 'Register Employers with package (Registered employers who purchased <strong class="highlight">CV Package</strong> can view freelancers.)', 'wp-freeio-wc-paid-listings' );
		}
		return apply_filters('wp-freeio-wc-paid-listings-restrict-freelancer', $fields);
	}

	public static function process_restrict_freelancer_detail($return, $post) {
		$restrict_type = wp_freeio_get_option('freelancer_restrict_type', '');
		if ( $restrict_type == 'view' ) {
			$view = wp_freeio_get_option('freelancer_restrict_detail', 'all');
			if ( $view == 'register_employer_with_package' ) {
				$return = false;
				if ( is_user_logged_in() ) {
					$user_id = get_current_user_id();
					$author_id = WP_Freeio_User::get_user_by_freelancer_id($post->ID);
					if ( $user_id == $author_id ) {
						$return = true;
					} elseif ( WP_Freeio_User::is_employer($user_id) ) {
						$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_cv_packages_by_user($user_id, true, $post->ID);
						if ( !empty($packages) ) {
							$return = true;
						}
					}
				}
			}
		}

		return apply_filters('wp-freeio-wc-paid-listings-process-restrict-freelancer-detail', $return, $post);
	}

	public static function process_restrict_freelancer_listing($query_args) {
		$restrict_type = wp_freeio_get_option('freelancer_restrict_type', '');
		if ( $restrict_type == 'view' ) {
			$view = wp_freeio_get_option('freelancer_restrict_listing', 'all');
			if ( $view == 'register_employer_with_package' ) {
				$return = false;
				if ( is_user_logged_in() ) {
					$user_id = get_current_user_id();
					if ( WP_Freeio_User::is_employer($user_id) ) {
						$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_cv_packages_by_user($user_id);
						if ( !empty($packages) ) {
							$return = true;
						}
					}
				}

				if ( !$return ) {
					$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
					$meta_query[] = array(
						'key'       => 'freelancer_restrict_listing',
						'value'     => 'register_employer_with_package',
						'compare'   => '==',
					);
					$query_args['meta_query'] = $meta_query;
				}
			}
		}

		return apply_filters( 'wp-freeio-wc-paid-listings-process-restrict-freelancer-listing', $query_args );
	}

	public static function process_viewed_freelancer($freelancer_id) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			if ( WP_Freeio_User::is_employer($user_id) ) {
				$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_cv_packages_by_user($user_id, true, $freelancer_id);
				if ( !empty($packages) && !empty($packages[0]) ) {
					$package = $packages[0];
					WP_Freeio_Wc_Paid_Listings_Mixes::increase_cv_package_viewed_count($freelancer_id, $user_id, $package->ID);
				}
			}
		}
	}
	
	public static function restrict_freelancer_information($content, $post) {
		$view = wp_freeio_get_option('freelancer_restrict_detail', 'all');
		if ( $view == 'register_employer_with_package' ) {
			$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_cv_package_products();
			$content = apply_filters( 'wp-freeio-wc-paid-listings-restrict-freelancer-information', '<div class="restrict-cv-package-info">'.
					'<h2 class="restrict-title">'.__( 'The page is restricted only for subscribed employers.', 'wp-freeio-wc-paid-listings' ).'</h2>'.
					'<div class="restrict-inner">'.
						WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('cv-packages', array('packages' => $packages) ).
					'</div>'.
				'</div>');
		}
		return $content;
	}

	public static function restrict_freelancer_listing_information($content, $query) {
		
		$view = wp_freeio_get_option('freelancer_restrict_listing', 'all');
		if ( $view == 'register_employer_with_package' ) {
			$return = false;
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				if ( class_exists('WP_Freeio_User') && WP_Freeio_User::is_employer($user_id) ) {
					$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_cv_packages_by_user($user_id);
					if ( !empty($packages) ) {
						$return = true;
					}
				}
			}
			if ( !$return ) {
				$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_cv_package_products();
				$content = apply_filters( 'wp-freeio-wc-paid-listings-restrict-freelancer-listing-information', '<div class="restrict-cv-package-info">'.
						'<h2 class="restrict-title">'.__( 'The page is restricted only for subscribed employers.', 'wp-freeio-wc-paid-listings' ).'</h2>'.
						'<div class="restrict-inner">'.
							WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('cv-packages', array('packages' => $packages) ).
						'</div>'.
					'</div>');
			}
		}
		return $content;
	}
}

WP_Freeio_Wc_Paid_Listings_CV_Package::init();