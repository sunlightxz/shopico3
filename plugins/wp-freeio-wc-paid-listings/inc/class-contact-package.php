<?php
/**
 * Contact Package
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_Contact_Package {
	public static function init() {
		add_filter( 'wp_freeio_settings_freelancer_settings', array( __CLASS__, 'restrict_freelancer_settings_fields' ), 11, 2 );
		add_filter( 'wp-freeio-restrict-freelancer-view-contact', array( __CLASS__, 'restrict_freelancer_settings' ), 11 );

		add_filter( 'wp-freeio-check-view-freelancer-contact-info', array( __CLASS__, 'process_restrict_freelancer_contact' ), 11, 2 );

		add_action( 'wp_freeio_before_job_detail', array( __CLASS__, 'process_viewed_freelancer' ), 10 );
	}

	public static function restrict_freelancer_settings_fields($fields, $pages) {
		$rfields = [];
		foreach ($fields as $field) {
			$rfields[] = $field;
			if ( $field['id'] == 'freelancer_restrict_contact_info' ) {
				$rfields[] = array(
					'name'    => __( 'Contact packages Page', 'wp-freeio-wc-paid-listings' ),
					'desc'    => __( 'Select Contact Packages Page. It will redirect employers at selected page to buy package.', 'wp-freeio-wc-paid-listings' ),
					'id'      => 'contact_package_page_id',
					'type'    => 'select',
					'options' => $pages,
				);
			}
		}
		
		return apply_filters('wp-freeio-wc-paid-listings-restrict-freelancer-settings-fields', $rfields);
	}

	public static function restrict_freelancer_settings($fields) {
		if ( !isset($fields['register_employer_contact_with_package']) ) {
			$fields['register_employer_contact_with_package'] = __( 'All users can view freelancer, but only employers with package can see contact info (Users who purchased <strong class="highlight">Contact Package</strong> can see contact info.)', 'wp-freeio-wc-paid-listings' );
		}
		return apply_filters('wp-freeio-wc-paid-listings-restrict-freelancer-settings', $fields);
	}

	public static function process_restrict_freelancer_contact($return, $post) {
		$restrict_type = wp_freeio_get_option('freelancer_restrict_type', '');
		if ( $restrict_type == 'view_contact_info' ) {
			$view = wp_freeio_get_option('freelancer_restrict_contact_info', 'all');
			if ( $view == 'register_employer_contact_with_package' ) {
				$return = false;
				if ( is_user_logged_in() ) {
					$user_id = get_current_user_id();
					if ( class_exists('WP_Freeio_User') && WP_Freeio_User::is_employer($user_id) ) {
						
						$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_contact_packages_by_user($user_id, true, $post->ID);
						if ( !empty($packages) ) {
							$return = true;
						} else {
							$return = WP_Freeio_Freelancer::freelancer_only_applicants($post);
						}
					}
				}
			}
		}
		return apply_filters('wp-freeio-wc-paid-listings-process-restrict-freelancer-contact', $return, $post);
	}


	public static function process_viewed_freelancer($freelancer_id) {
		$restrict_type = wp_freeio_get_option('freelancer_restrict_type', '');
		if ( $restrict_type == 'view_contact_info' ) {
			$view = wp_freeio_get_option('freelancer_restrict_contact_info', 'all');
			if ( $view == 'register_employer_contact_with_package' && is_user_logged_in() ) {
				$user_id = get_current_user_id();
				if ( WP_Freeio_User::is_employer($user_id) ) {
					$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_contact_packages_by_user($user_id, true, $freelancer_id);
					if ( !empty($packages) && !empty($packages[0]) ) {
						$package = $packages[0];
						WP_Freeio_Wc_Paid_Listings_Mixes::increase_contact_package_viewed_count($freelancer_id, $user_id, $package->ID);
					}
				}
			}
		}
	}

	public static function check_user_can_contact_freelancer($post) {
		$return = true;
		$restrict_type = wp_freeio_get_option('freelancer_restrict_type', '');
		if ( $restrict_type == 'view_contact_info' ) {
			$view = wp_freeio_get_option('freelancer_restrict_contact_info', 'all');
			if ( $view == 'register_employer_contact_with_package' ) {
				$return = false;
				if ( is_user_logged_in() ) {
					$user_id = get_current_user_id();
					if ( class_exists('WP_Freeio_User') && WP_Freeio_User::is_employer($user_id) ) {
						$packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_contact_packages_by_user($user_id, true, $post->ID);
						if ( !empty($packages) ) {
							$return = true;
						} else {
							$return = WP_Freeio_Freelancer::freelancer_only_applicants($post);
						}
					}
				}
			}
		}

		return apply_filters('wp-freeio-wc-paid-listings-check-user-can-contact-freelancer', $return, $post);
	}
}

WP_Freeio_Wc_Paid_Listings_Contact_Package::init();