<?php
/**
 * Order
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_Mixes {

	public static function get_user_id( $user_id ) {
		if ( method_exists('WP_Freeio_User', 'get_user_id') ) {
			$user_id = WP_Freeio_User::get_user_id($user_id);
			return $user_id;
		}
		return $user_id;
	}

	public static function get_job_package_products($product_type = 'job_package') {
		if ( !is_array($product_type) ) {
			$product_type = array($product_type);
		}
		$query_args = array(
		   	'post_type' => 'product',
		   	'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
		   	'tax_query' => array(
		        array(
		            'taxonomy' => 'product_type',
		            'field'    => 'slug',
		            'terms'    => $product_type,
		        ),
		    ),
		);
		$posts = get_posts( $query_args );

		return $posts;
	}

	public static function get_packages_by_order_id( $order_id, $package_type = 'job_package' ) {
		
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$meta_query = array(
			array(
				'key'     => $prefix.'order_id',
				'value'   => $order_id,
				'compare' => '='
			)
		);
		if ( $package_type != 'all' ) {
			$meta_query[] = array(
				'key'     => $prefix.'package_type',
				'value'   => $package_type,
				'compare' => '='
			);
		}
		$query_args = array(
			'post_type' => 'job_package',
			'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => $meta_query,
			'fields' => 'ids'
		);

		$packages = get_posts($query_args);
		
		return $packages;
	}

	public static function get_jobs_by_user_package_id( $user_package_id ) {
		$prefix = WP_FREEIO_JOB_LISTING_PREFIX;
		$meta_query = array(
			array(
				'key'     => $prefix.'user_package_id',
				'value'   => $user_package_id,
				'compare' => '='
			),
		);
		$query_args = array(
			'post_type' => 'job_listing',
			'post_status' => array('publish', 'expired', 'pending'),
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => $meta_query,
			'fields' => 'ids'
		);

		$jobs = get_posts($query_args);
		
		return $jobs;
	}

	public static function get_services_by_user_package_id( $user_package_id ) {
		$prefix = WP_FREEIO_SERVICE_PREFIX;
		$meta_query = array(
			array(
				'key'     => $prefix.'user_package_id',
				'value'   => $user_package_id,
				'compare' => '='
			),
		);
		$query_args = array(
			'post_type' => 'service',
			'post_status' => array('publish', 'expired', 'pending'),
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => $meta_query,
			'fields' => 'ids'
		);

		$services = get_posts($query_args);
		
		return $services;
	}

	public static function get_projects_by_user_package_id( $user_package_id ) {
		$prefix = WP_FREEIO_SERVICE_PREFIX;
		$meta_query = array(
			array(
				'key'     => $prefix.'user_package_id',
				'value'   => $user_package_id,
				'compare' => '='
			),
		);
		$query_args = array(
			'post_type' => 'project',
			'post_status' => array('publish', 'expired', 'pending'),
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => $meta_query,
			'fields' => 'ids'
		);

		$projects = get_posts($query_args);
		
		return $projects;
	}

	public static function create_user_package( $user_id, $product_id, $order_id ) {
		$package = wc_get_product( $product_id );
		
		$user_id = self::get_user_id($user_id);

		if ( !$package->is_type( array('job_package', 'job_package_subscription') )) {
			return false;
		}

		$args = apply_filters( 'wp_freeio_wc_paid_listings_create_user_package_data', array(
			'post_title' => $package->get_title(),
			'post_status' => 'publish',
			'post_type' => 'job_package',
		), $user_id, $product_id, $order_id);

		$user_package_id = wp_insert_post( $args );
		if ( $user_package_id ) {
			// general metas
			$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
			update_post_meta( $user_package_id, $prefix.'product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'order_id', $order_id );
			update_post_meta( $user_package_id, $prefix.'package_count', 0 );
			update_post_meta( $user_package_id, $prefix.'user_id', $user_id );
			update_post_meta( $user_package_id, $prefix.'package_type', 'job_package' );

			// listing metas
			$urgent_jobs = get_post_meta($product_id, '_urgent_jobs', true );
			$feature_jobs = get_post_meta($product_id, '_feature_jobs', true );
			$duration_jobs = get_post_meta($product_id, '_jobs_duration', true );
			$limit_jobs = get_post_meta($product_id, '_jobs_limit', true );
			$subscription_type = get_post_meta($product_id, '_job_package_subscription_type', true );

			if ( $urgent_jobs == 'yes' ) {
				update_post_meta( $user_package_id, $prefix.'urgent_jobs', 'on' );
			}
			if ( $feature_jobs == 'yes' ) {
				update_post_meta( $user_package_id, $prefix.'feature_jobs', 'on' );
			}
			update_post_meta( $user_package_id, $prefix.'job_duration', $duration_jobs );
			update_post_meta( $user_package_id, $prefix.'job_limit', $limit_jobs );
			update_post_meta( $user_package_id, $prefix.'subscription_type', $subscription_type );

			do_action('wp_freeio_wc_paid_listings_create_user_package_meta', $user_package_id, $user_id, $product_id, $order_id);
		}

		return $user_package_id;
	}

	public static function approve_job_with_package( $job_id, $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);
		if ( self::package_is_valid( $user_id, $user_package_id ) ) {
			$listing = array(
				'ID'            => $job_id,
				'post_date'     => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 )
			);
			$post_type = get_post_type( $job_id );
			if ( $post_type === 'job_listing' ) {
				delete_post_meta( $job_id, WP_FREEIO_JOB_LISTING_PREFIX.'expiry_date' );

				$review_before = wp_freeio_get_option( 'submission_requires_approval' );
				$post_status = 'publish';
				if ( $review_before == 'on' ) {
					$post_status = 'pending';
				}

				$listing['post_status'] = $post_status;
			}

			// Do update
			wp_update_post( $listing );
			update_post_meta( $job_id, WP_FREEIO_JOB_LISTING_PREFIX.'user_package_id', $user_package_id );
			self::increase_package_count( $user_id, $user_package_id );

			do_action('wp_freeio_wc_paid_listings_approve_job_with_package', $job_id, $user_id, $user_package_id);
		}
	}

	public static function package_is_valid( $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		$package_count = get_post_meta($user_package_id, $prefix.'package_count', true);
		$job_limit = get_post_meta($user_package_id, $prefix.'job_limit', true);

		if ( ($package_user_id != $user_id) || ($package_count >= $job_limit && $job_limit != 0) ) {
			return false;
		}

		return true;
	}

	public static function increase_package_count( $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		
		if ( $package_user_id != $user_id ) {
			return false;
		}
		$package_count = intval(get_post_meta($user_package_id, $prefix.'package_count', true)) + 1;
		
		update_post_meta($user_package_id, $prefix.'package_count', $package_count);
	}

	public static function get_packages_by_user( $user_id, $valid = true, $package_type = 'job_package' ) {
		$user_id = self::get_user_id($user_id);
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$meta_query = array(
			array(
				'key'     => $prefix.'user_id',
				'value'   => $user_id,
				'compare' => '='
			)
		);
		if ( $package_type != 'all' ) {
			$meta_query[] = array(
				'key'     => $prefix.'package_type',
				'value'   => $package_type,
				'compare' => '='
			);
		}
		$query_args = array(
			'post_type' => 'job_package',
			'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => $meta_query
		);

		$packages = get_posts($query_args);
		$return = array();
		if ( $valid && $packages ) {
			if ( $package_type == 'job_package' ) {
				foreach ($packages as $package) {
					$package_count = get_post_meta($package->ID, $prefix.'package_count', true);
					$job_limit = get_post_meta($package->ID, $prefix.'job_limit', true);
					if ( empty($job_limit) || $package_count < $job_limit ) {
						$return[] = $package;
					}
				}
			} elseif ( $package_type == 'service_package' ) {
				foreach ($packages as $package) {
					$package_count = get_post_meta($package->ID, $prefix.'package_count', true);
					$service_limit = get_post_meta($package->ID, $prefix.'service_limit', true);
					if ( empty($service_limit) || $package_count < $service_limit ) {
						$return[] = $package;
					}
				}
			} elseif ( $package_type == 'project_package' ) {
				foreach ($packages as $package) {
					$package_count = get_post_meta($package->ID, $prefix.'package_count', true);
					$project_limit = get_post_meta($package->ID, $prefix.'project_limit', true);
					if ( empty($project_limit) || $package_count < $project_limit ) {
						$return[] = $package;
					}
				}
			}
		} else {
			$return = $packages;
		}
		return $return;
	}

	public static function get_listings_for_package( $user_package_id, $post_type = '' ) {
		if ( $post_type == 'job_listing' ) {
			$prefix = WP_FREEIO_JOB_LISTING_PREFIX;
		} elseif ( $post_type == 'freelancer' ) {
			$prefix = WP_FREEIO_FREELANCER_PREFIX;
		} elseif ( $post_type == 'employer' ) {
			$prefix = WP_FREEIO_EMPLOYER_PREFIX;
		} elseif ( $post_type == 'service' ) {
			$prefix = WP_FREEIO_SERVICE_PREFIX;
		} elseif ( $post_type == 'project' ) {
			$prefix = WP_FREEIO_PROJECT_PREFIX;
		}

		$query_args = array(
			'post_status' => 'publish',
			'posts_per_page'   => -1,
			'fields' => 'ids',
			'meta_query' => array(
				array(
					'key'     => $prefix.'user_package_id',
					'value'   => $user_package_id,
					'compare' => '='
				)
			)
		);
		if ( !empty($post_type) ) {
			$query_args['post_type'] = $post_type;
		}
		$posts = get_posts( $query_args );

		return $posts;
	}

	// service package
	public static function get_service_package_products($product_type = 'service_package') {
		if ( !is_array($product_type) ) {
			$product_type = array($product_type);
		}
		$query_args = array(
		   	'post_type' => 'product',
		   	'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
		   	'tax_query' => array(
		        array(
		            'taxonomy' => 'product_type',
		            'field'    => 'slug',
		            'terms'    => $product_type,
		        ),
		    ),
		);
		$posts = get_posts( $query_args );

		return $posts;
	}

	public static function service_create_user_package( $user_id, $product_id, $order_id ) {
		$package = wc_get_product( $product_id );
		
		$user_id = self::get_user_id($user_id);

		if ( !$package->is_type( array('service_package', 'service_package_subscription') )) {
			return false;
		}

		$args = apply_filters( 'wp_freeio_wc_paid_listings_create_user_package_data', array(
			'post_title' => $package->get_title(),
			'post_status' => 'publish',
			'post_type' => 'job_package',
		), $user_id, $product_id, $order_id);

		$user_package_id = wp_insert_post( $args );
		if ( $user_package_id ) {
			// general metas
			$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
			update_post_meta( $user_package_id, $prefix.'product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'order_id', $order_id );
			update_post_meta( $user_package_id, $prefix.'package_count', 0 );
			update_post_meta( $user_package_id, $prefix.'user_id', $user_id );
			update_post_meta( $user_package_id, $prefix.'package_type', 'service_package' );

			// listing metas
			$urgent_services = get_post_meta($product_id, '_urgent_services', true );
			$feature_services = get_post_meta($product_id, '_feature_services', true );
			$duration_services = get_post_meta($product_id, '_services_duration', true );
			$limit_services = get_post_meta($product_id, '_services_limit', true );
			$subscription_type = get_post_meta($product_id, '_service_package_subscription_type', true );

			if ( $urgent_services == 'yes' ) {
				update_post_meta( $user_package_id, $prefix.'urgent_services', 'on' );
			}
			if ( $feature_services == 'yes' ) {
				update_post_meta( $user_package_id, $prefix.'feature_services', 'on' );
			}
			update_post_meta( $user_package_id, $prefix.'service_duration', $duration_services );
			update_post_meta( $user_package_id, $prefix.'service_limit', $limit_services );
			update_post_meta( $user_package_id, $prefix.'subscription_type', $subscription_type );

			do_action('wp_freeio_wc_paid_listings_create_user_package_meta', $user_package_id, $user_id, $product_id, $order_id);
		}

		return $user_package_id;
	}

	public static function approve_service_with_package( $service_id, $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);
		if ( self::service_package_is_valid( $user_id, $user_package_id ) ) {
			$listing = array(
				'ID'            => $service_id,
				'post_date'     => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 )
			);
			$post_type = get_post_type( $service_id );
			if ( $post_type === 'service' ) {
				delete_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'expiry_date' );

				$review_before = wp_freeio_get_option( 'submission_service_requires_approval' );
				$post_status = 'publish';
				if ( $review_before == 'on' ) {
					$post_status = 'pending';
				}

				$listing['post_status'] = $post_status;
			}

			// Do update
			wp_update_post( $listing );
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'user_package_id', $user_package_id );
			self::service_increase_package_count( $user_id, $user_package_id );

			do_action('wp_freeio_wc_paid_listings_approve_service_with_package', $service_id, $user_id, $user_package_id);
		}
	}

	public static function service_package_is_valid( $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		$package_count = get_post_meta($user_package_id, $prefix.'package_count', true);
		$service_limit = get_post_meta($user_package_id, $prefix.'service_limit', true);

		if ( ($package_user_id != $user_id) || ($package_count >= $service_limit && $service_limit != 0) ) {
			return false;
		}

		return true;
	}

	public static function service_increase_package_count( $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		
		if ( $package_user_id != $user_id ) {
			return false;
		}
		$package_count = intval(get_post_meta($user_package_id, $prefix.'package_count', true)) + 1;
		
		update_post_meta($user_package_id, $prefix.'package_count', $package_count);
	}

	// project package
	public static function get_project_package_products($product_type = 'project_package') {
		if ( !is_array($product_type) ) {
			$product_type = array($product_type);
		}
		$query_args = array(
		   	'post_type' => 'product',
		   	'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
		   	'tax_query' => array(
		        array(
		            'taxonomy' => 'product_type',
		            'field'    => 'slug',
		            'terms'    => $product_type,
		        ),
		    ),
		);
		$posts = get_posts( $query_args );

		return $posts;
	}

	public static function project_create_user_package( $user_id, $product_id, $order_id ) {
		$package = wc_get_product( $product_id );
		
		$user_id = self::get_user_id($user_id);

		if ( !$package->is_type( array('project_package', 'project_package_subscription') )) {
			return false;
		}

		$args = apply_filters( 'wp_freeio_wc_paid_listings_create_user_package_data', array(
			'post_title' => $package->get_title(),
			'post_status' => 'publish',
			'post_type' => 'job_package',
		), $user_id, $product_id, $order_id);

		$user_package_id = wp_insert_post( $args );
		if ( $user_package_id ) {
			// general metas
			$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
			update_post_meta( $user_package_id, $prefix.'product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'order_id', $order_id );
			update_post_meta( $user_package_id, $prefix.'package_count', 0 );
			update_post_meta( $user_package_id, $prefix.'user_id', $user_id );
			update_post_meta( $user_package_id, $prefix.'package_type', 'project_package' );

			// listing metas
			$urgent_projects = get_post_meta($product_id, '_urgent_projects', true );
			$feature_projects = get_post_meta($product_id, '_feature_projects', true );
			$duration_projects = get_post_meta($product_id, '_projects_duration', true );
			$limit_projects = get_post_meta($product_id, '_projects_limit', true );
			$subscription_type = get_post_meta($product_id, '_project_package_subscription_type', true );

			if ( $urgent_projects == 'yes' ) {
				update_post_meta( $user_package_id, $prefix.'urgent_projects', 'on' );
			}
			if ( $feature_projects == 'yes' ) {
				update_post_meta( $user_package_id, $prefix.'feature_projects', 'on' );
			}
			update_post_meta( $user_package_id, $prefix.'project_duration', $duration_projects );
			update_post_meta( $user_package_id, $prefix.'project_limit', $limit_projects );
			update_post_meta( $user_package_id, $prefix.'subscription_type', $subscription_type );

			do_action('wp_freeio_wc_paid_listings_create_user_package_meta', $user_package_id, $user_id, $product_id, $order_id);
		}

		return $user_package_id;
	}

	public static function approve_project_with_package( $project_id, $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);
		if ( self::project_package_is_valid( $user_id, $user_package_id ) ) {
			$listing = array(
				'ID'            => $project_id,
				'post_date'     => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 )
			);
			$post_type = get_post_type( $project_id );
			if ( $post_type === 'project' ) {
				delete_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'expiry_date' );

				$review_before = wp_freeio_get_option( 'submission_project_requires_approval' );
				$post_status = 'publish';
				if ( $review_before == 'on' ) {
					$post_status = 'pending';
				}

				$listing['post_status'] = $post_status;
			}

			// Do update
			wp_update_post( $listing );
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'user_package_id', $user_package_id );
			self::project_increase_package_count( $user_id, $user_package_id );

			do_action('wp_freeio_wc_paid_listings_approve_project_with_package', $project_id, $user_id, $user_package_id);
		}
	}

	public static function project_package_is_valid( $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		$package_count = get_post_meta($user_package_id, $prefix.'package_count', true);
		$project_limit = get_post_meta($user_package_id, $prefix.'project_limit', true);

		if ( ($package_user_id != $user_id) || ($package_count >= $project_limit && $project_limit != 0) ) {
			return false;
		}

		return true;
	}

	public static function project_increase_package_count( $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		
		if ( $package_user_id != $user_id ) {
			return false;
		}
		$package_count = intval(get_post_meta($user_package_id, $prefix.'package_count', true)) + 1;
		
		update_post_meta($user_package_id, $prefix.'package_count', $package_count);
	}

	// CV package
	public static function get_cv_package_products() {
		$query_args = array(
		   	'post_type' => 'product',
		   	'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
		   	'tax_query' => array(
		        array(
		            'taxonomy' => 'product_type',
		            'field'    => 'slug',
		            'terms'    => array('cv_package', 'cv_package_subscription'),
		        ),
		    ),
		);
		$posts = get_posts( $query_args );

		return $posts;
	}

	public static function create_user_cv_package( $user_id, $product_id, $order_id ) {
		$user_id = self::get_user_id($user_id);

		$package = wc_get_product( $product_id );

		if ( !$package->is_type( array('cv_package', 'cv_package_subscription') ) ) {
			return false;
		}

		$args = apply_filters( 'wp_freeio_wc_paid_listings_create_user_cv_package_data', array(
			'post_title' => $package->get_title(),
			'post_status' => 'publish',
			'post_type' => 'job_package',
		), $user_id, $product_id, $order_id);

		$user_package_id = wp_insert_post( $args );
		if ( $user_package_id ) {
			// general metas
			$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
			update_post_meta( $user_package_id, $prefix.'product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'cv_product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'order_id', $order_id );
			update_post_meta( $user_package_id, $prefix.'cv_viewed_count', '' );
			update_post_meta( $user_package_id, $prefix.'user_id', $user_id );
			update_post_meta( $user_package_id, $prefix.'package_type', 'cv_package' );

			// listing metas
			$nb_expiry_time = get_post_meta($product_id, '_cv_package_expiry_time', true );
			$nb_of_cv = get_post_meta($product_id, '_cv_number_of_cv', true );
			$subscription_type = get_post_meta($product_id, '_cv_package_subscription_type', true );

			update_post_meta( $user_package_id, $prefix.'cv_package_expiry_time', $nb_expiry_time );
			update_post_meta( $user_package_id, $prefix.'cv_number_of_cv', $nb_of_cv );
			update_post_meta( $user_package_id, $prefix.'subscription_type', $subscription_type );

			do_action('wp_freeio_wc_paid_listings_create_user_cv_package_meta', $user_package_id, $user_id, $product_id, $order_id);
		}

		return $user_package_id;
	}

	public static function cv_package_is_valid( $user_id, $user_package_id, $freelancer_id = null ) {
		$user_id = self::get_user_id($user_id);
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);

		$subscription_type = get_post_meta($user_package_id, $prefix.'subscription_type', true);
		$cv_package_expiry_time = get_post_meta($user_package_id, $prefix.'cv_package_expiry_time', true);
		if ( $subscription_type == 'listing' ) {
			$cv_package_expiry_time = '';
		}
		
		$freelancer_ids = get_post_meta($user_package_id, $prefix.'cv_viewed_count', true);
		if ( !empty($freelancer_ids) ) {
			$freelancer_ids = explode(',', $freelancer_ids);
			$cv_viewed_count = count( $freelancer_ids );
		} else {
			$cv_viewed_count = 0;
			$freelancer_ids = array();
		}

		$cv_number_of_cv = get_post_meta($user_package_id, $prefix.'cv_number_of_cv', true);

		$package_date = get_the_date( 'Y-m-d', $user_package_id );

		$date_expiry = true;
		if ( !empty($cv_package_expiry_time) && $cv_package_expiry_time > 0 ) {
			$final_date = strtotime($package_date . "+".$cv_package_expiry_time." days");
			if ( $final_date < strtotime('now') ) {
				$date_expiry = false;
			}
		}

		if ( !$date_expiry || ($package_user_id != $user_id) ) {
			return false;
		}

		if ( $freelancer_id ) {
			if ( !in_array($freelancer_id, $freelancer_ids) ) {
				if ( ($cv_viewed_count >= $cv_number_of_cv && $cv_number_of_cv != 0) ) {
					return false;
				}
			}
		} else {
			if ( ($cv_viewed_count >= $cv_number_of_cv && $cv_number_of_cv != 0) ) {
				return false;
			}
		}

		return true;
	}

	public static function increase_cv_package_viewed_count( $freelancer_id, $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);

		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		if ( $package_user_id != $user_id ) {
			return false;
		}

		$cv_viewed_count = get_post_meta($user_package_id, $prefix.'cv_viewed_count', true);
		if ( !empty($cv_viewed_count) ) {
			$cv_viewed_counts = array_map( 'trim', explode(',', $cv_viewed_count) );
			if ( !in_array($freelancer_id, $cv_viewed_counts) ) {
				$cv_viewed_counts[] = $freelancer_id;
			}
		} else {
			$cv_viewed_counts = array($freelancer_id);
		}
		update_post_meta( $user_package_id, $prefix.'cv_viewed_count', implode(',', $cv_viewed_counts) );
		update_post_meta( $user_package_id, $prefix.'cv_viewed_count_nb', count($cv_viewed_counts) );
	}

	public static function get_cv_packages_by_user( $user_id, $valid = true, $freelancer_id = null ) {
		$user_id = self::get_user_id($user_id);

		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$query_args = array(
			'post_type' => 'job_package',
			'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => array(
				array(
					'key'     => $prefix.'user_id',
					'value'   => $user_id,
					'compare' => '='
				),
				array(
					'key'     => $prefix.'package_type',
					'value'   => 'cv_package',
					'compare' => '='
				)
			)
		);
		
		$packages = get_posts($query_args);
		$return = array();
		if ( $valid && $packages ) {
			foreach ($packages as $package) {
				if ( self::cv_package_is_valid($user_id, $package->ID, $freelancer_id) ) {
					$return[] = $package;
				}
			}
		} else {
			$return = $packages;
		}
		return $return;
	}


	// Contact package
	public static function get_contact_package_products() {
		$query_args = array(
		   	'post_type' => 'product',
		   	'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
		   	'tax_query' => array(
		        array(
		            'taxonomy' => 'product_type',
		            'field'    => 'slug',
		            'terms'    => array('contact_package', 'contact_package_subscription'),
		        ),
		    ),
		);
		$posts = get_posts( $query_args );

		return $posts;
	}

	public static function create_user_contact_package( $user_id, $product_id, $order_id ) {
		$user_id = self::get_user_id($user_id);

		$package = wc_get_product( $product_id );

		if ( !$package->is_type( array('contact_package', 'contact_package_subscription') ) ) {
			return false;
		}

		$args = apply_filters( 'wp_freeio_wc_paid_listings_create_user_contact_package_data', array(
			'post_title' => $package->get_title(),
			'post_status' => 'publish',
			'post_type' => 'job_package',
		), $user_id, $product_id, $order_id);

		$user_package_id = wp_insert_post( $args );
		if ( $user_package_id ) {
			// general metas
			$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
			update_post_meta( $user_package_id, $prefix.'product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'contact_product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'order_id', $order_id );
			update_post_meta( $user_package_id, $prefix.'contact_viewed_count', '' );
			update_post_meta( $user_package_id, $prefix.'user_id', $user_id );
			update_post_meta( $user_package_id, $prefix.'package_type', 'contact_package' );

			// listing metas
			$nb_expiry_time = get_post_meta($product_id, '_contact_package_expiry_time', true );
			$nb_of_contact = get_post_meta($product_id, '_contact_number_of_cv', true );
			$subscription_type = get_post_meta($product_id, '_contact_package_subscription_type', true );

			update_post_meta( $user_package_id, $prefix.'contact_package_expiry_time', $nb_expiry_time );
			update_post_meta( $user_package_id, $prefix.'contact_number_of_cv', $nb_of_contact );
			update_post_meta( $user_package_id, $prefix.'subscription_type', $subscription_type );

			do_action('wp_freeio_wc_paid_listings_create_user_contact_package_meta', $user_package_id, $user_id, $product_id, $order_id);
		}

		return $user_package_id;
	}

	public static function contact_package_is_valid( $user_id, $user_package_id, $freelancer_id = null ) {
		$user_id = self::get_user_id($user_id);

		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		$subscription_type = get_post_meta($user_package_id, $prefix.'subscription_type', true);
		$contact_package_expiry_time = get_post_meta($user_package_id, $prefix.'contact_package_expiry_time', true);
		if ( $subscription_type == 'listing' ) {
			$contact_package_expiry_time = '';
		}
		$freelancer_ids = get_post_meta($user_package_id, $prefix.'contact_viewed_count', true);
		if ( !empty($freelancer_ids) ) {
			$freelancer_ids = explode(',', $freelancer_ids);
			$contact_viewed_count = count( $freelancer_ids );
		} else {
			$contact_viewed_count = 0;
			$freelancer_ids = array();
		}

		$contact_number_of_cv = get_post_meta($user_package_id, $prefix.'contact_number_of_cv', true);

		$package_date = get_the_date( 'Y-m-d', $user_package_id );

		$date_expiry = true;
		if ( !empty($contact_package_expiry_time) && $contact_package_expiry_time > 0 ) {
			$final_date = strtotime($package_date . "+".$contact_package_expiry_time." days");
			if ( $final_date < strtotime('now') ) {
				$date_expiry = false;
			}
		}

		if ( !$date_expiry || ($package_user_id != $user_id) ) {
			return false;
		}

		if ( $freelancer_id ) {
			if ( !in_array($freelancer_id, $freelancer_ids) ) {
				if ( ($contact_viewed_count >= $contact_number_of_cv && $contact_number_of_cv != 0) ) {
					return false;
				}
			}
		} else {
			if ( ($contact_viewed_count >= $contact_number_of_cv && $contact_number_of_cv != 0) ) {
				return false;
			}
		}

		return true;
	}

	public static function increase_contact_package_viewed_count( $freelancer_id, $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);

		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		if ( $package_user_id != $user_id ) {
			return false;
		}

		$contact_viewed_count = get_post_meta($user_package_id, $prefix.'contact_viewed_count', true);
		if ( !empty($contact_viewed_count) ) {
			$contact_viewed_counts = array_map( 'trim', explode(',', $contact_viewed_count) );
			if ( !in_array($freelancer_id, $contact_viewed_counts) ) {
				$contact_viewed_counts[] = $freelancer_id;
			}
		} else {
			$contact_viewed_counts = array($freelancer_id);
		}
		update_post_meta( $user_package_id, $prefix.'contact_viewed_count', implode(',', $contact_viewed_counts) );
		update_post_meta( $user_package_id, $prefix.'contact_viewed_count_nb', count($contact_viewed_counts) );
	}

	public static function get_contact_packages_by_user( $user_id, $valid = true, $freelancer_id = null ) {
		$user_id = self::get_user_id($user_id);

		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$query_args = array(
			'post_type' => 'job_package',
			'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => array(
				array(
					'key'     => $prefix.'user_id',
					'value'   => $user_id,
					'compare' => '='
				),
				array(
					'key'     => $prefix.'package_type',
					'value'   => 'contact_package',
					'compare' => '='
				)
			)
		);
		
		$packages = get_posts($query_args);
		$return = array();

		if ( $valid && $packages ) {
			foreach ($packages as $package) {
				if ( self::contact_package_is_valid($user_id, $package->ID, $freelancer_id) ) {
					$return[] = $package;
				}
			}
		} else {
			$return = $packages;
		}
		return $return;
	}


	// Freelancer package
	public static function get_freelancer_package_products() {
		$query_args = array(
		   	'post_type' => 'product',
		   	'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
		   	'tax_query' => array(
		        array(
		            'taxonomy' => 'product_type',
		            'field'    => 'slug',
		            'terms'    => array('freelancer_package', 'freelancer_package_subscription'),
		        ),
		    ),
		);
		$posts = get_posts( $query_args );

		return $posts;
	}

	public static function create_user_freelancer_package( $user_id, $product_id, $order_id ) {
		$user_id = self::get_user_id($user_id);

		$package = wc_get_product( $product_id );

		if ( !$package->is_type( array('freelancer_package', 'freelancer_package_subscription') ) ) {
			return false;
		}

		$args = apply_filters( 'wp_freeio_wc_paid_listings_create_user_freelancer_package_data', array(
			'post_title' => $package->get_title(),
			'post_status' => 'publish',
			'post_type' => 'job_package',
		), $user_id, $product_id, $order_id);

		$user_package_id = wp_insert_post( $args );
		if ( $user_package_id ) {
			// general metas
			$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
			update_post_meta( $user_package_id, $prefix.'product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'freelancer_product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'order_id', $order_id );
			update_post_meta( $user_package_id, $prefix.'freelancer_applied_count', '' );
			update_post_meta( $user_package_id, $prefix.'user_id', $user_id );
			update_post_meta( $user_package_id, $prefix.'package_type', 'freelancer_package' );

			// listing metas
			$nb_expiry_time = get_post_meta($product_id, '_freelancer_package_expiry_time', true );
			$nb_applications = get_post_meta($product_id, '_freelancer_number_of_applications', true );
			$subscription_type = get_post_meta($product_id, '_freelancer_package_subscription_type', true );

			update_post_meta( $user_package_id, $prefix.'freelancer_package_expiry_time', $nb_expiry_time );
			update_post_meta( $user_package_id, $prefix.'freelancer_number_of_applications', $nb_applications );
			update_post_meta( $user_package_id, $prefix.'subscription_type', $subscription_type );

			do_action('wp_freeio_wc_paid_listings_create_user_freelancer_package_meta', $user_package_id, $user_id, $product_id, $order_id);
		}

		return $user_package_id;
	}

	public static function freelancer_package_is_valid( $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);

		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		$subscription_type = get_post_meta($user_package_id, $prefix.'subscription_type', true);
		$freelancer_package_expiry_time = get_post_meta($user_package_id, $prefix.'freelancer_package_expiry_time', true);
		if ( $subscription_type == 'listing' ) {
			$freelancer_package_expiry_time = '';
		}
		$freelancer_applied_count = get_post_meta($user_package_id, $prefix.'freelancer_applied_count', true);
		if ( !empty($freelancer_applied_count) ) {
			$freelancer_applied_count = count( explode(',', $freelancer_applied_count) );
		} else {
			$freelancer_applied_count = 0;
		}

		$freelancer_number_of_applications = get_post_meta($user_package_id, $prefix.'freelancer_number_of_applications', true);

		$package_date = get_the_date( 'Y-m-d', $user_package_id );

		$date_expiry = true;
		if ( !empty($freelancer_package_expiry_time) && $freelancer_package_expiry_time > 0 ) {
			$final_date = strtotime($package_date . "+".$freelancer_package_expiry_time." days");
			if ( $final_date < strtotime('now') ) {
				$date_expiry = false;
			}
		}

		if ( !$date_expiry || ($package_user_id != $user_id) || ($freelancer_applied_count >= $freelancer_number_of_applications && $freelancer_number_of_applications != 0) ) {
			return false;
		}

		return true;
	}

	public static function increase_freelancer_package_applied_count( $application_id, $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);

		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}

		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		if ( $package_user_id != $user_id ) {
			return false;
		}

		$freelancer_applied_count = get_post_meta($user_package_id, $prefix.'freelancer_applied_count', true);
		if ( !empty($freelancer_applied_count) ) {
			$freelancer_applied_counts = array_map( 'trim', explode(',', $freelancer_applied_count) );
			if ( !in_array($application_id, $freelancer_applied_counts) ) {
				$freelancer_applied_counts[] = $application_id;
			}
		} else {
			$freelancer_applied_counts = array($application_id);
		}

		update_post_meta( $user_package_id, $prefix.'freelancer_applied_count', implode(',', $freelancer_applied_counts) );
		update_post_meta( $user_package_id, $prefix.'freelancer_applied_count_nb', count($freelancer_applied_counts) );
	}

	public static function get_freelancer_packages_by_user( $user_id, $valid = true ) {
		$user_id = self::get_user_id($user_id);

		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$query_args = array(
			'post_type' => 'job_package',
			'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => array(
				array(
					'key'     => $prefix.'user_id',
					'value'   => $user_id,
					'compare' => '='
				),
				array(
					'key'     => $prefix.'package_type',
					'value'   => 'freelancer_package',
					'compare' => '='
				)
			)
		);
		
		$packages = get_posts($query_args);
		$return = array();
		if ( $valid && $packages ) {
			foreach ($packages as $package) {
				if ( self::freelancer_package_is_valid($user_id, $package->ID) ) {
					$return[] = $package;
				}
			}
		} else {
			$return = $packages;
		}
		return $return;
	}


	// Resume package
	public static function get_resume_package_products() {
		$query_args = array(
		   	'post_type' => 'product',
		   	'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
		   	'tax_query' => array(
		        array(
		            'taxonomy' => 'product_type',
		            'field'    => 'slug',
		            'terms'    => array('resume_package', 'resume_package_subscription'),
		        ),
		    ),
		);
		$posts = get_posts( $query_args );

		return $posts;
	}

	public static function create_user_resume_package( $user_id, $product_id, $order_id ) {
		$user_id = self::get_user_id($user_id);

		$package = wc_get_product( $product_id );

		if ( !$package->is_type( array('resume_package', 'resume_package_subscription') ) ) {
			return false;
		}

		$args = apply_filters( 'wp_freeio_wc_paid_listings_create_user_resume_package_data', array(
			'post_title' => $package->get_title(),
			'post_status' => 'publish',
			'post_type' => 'job_package',
		), $user_id, $product_id, $order_id);

		$user_package_id = wp_insert_post( $args );
		if ( $user_package_id ) {
			// general metas
			$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
			update_post_meta( $user_package_id, $prefix.'product_id', $product_id );
			update_post_meta( $user_package_id, $prefix.'order_id', $order_id );
			update_post_meta( $user_package_id, $prefix.'user_id', $user_id );
			update_post_meta( $user_package_id, $prefix.'package_type', 'resume_package' );

			// listing metas
			// $urgent_resumes = get_post_meta($product_id, '_urgent_resumes', true );
			$featured_resumes = get_post_meta($product_id, '_featured_resumes', true );
			$resumes_duration = get_post_meta($product_id, '_resumes_duration', true );
			$subscription_type = get_post_meta($product_id, '_resume_package_subscription_type', true );

			// if ( $urgent_resumes == 'yes' ) {
			// 	update_post_meta( $user_package_id, $prefix.'urgent_resumes', 'on' );
			// }
			if ( $featured_resumes == 'yes' ) {
				update_post_meta( $user_package_id, $prefix.'featured_resumes', 'on' );
			}
			update_post_meta( $user_package_id, $prefix.'resumes_duration', $resumes_duration );
			update_post_meta( $user_package_id, $prefix.'subscription_type', $subscription_type );

			do_action('wp_freeio_wc_paid_listings_create_user_resume_package_meta', $user_package_id, $user_id, $product_id, $order_id);
		}

		return $user_package_id;
	}

	public static function resume_package_is_valid( $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);

		$post = get_post($user_package_id);
		if ( empty($post) ) {
			return false;
		}
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$package_user_id = get_post_meta($user_package_id, $prefix.'user_id', true);
		
		if ( $package_user_id != $user_id ) {
			return false;
		}

		return true;
	}

	public static function get_resume_packages_by_user( $user_id, $valid = true ) {
		$user_id = self::get_user_id($user_id);

		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		$query_args = array(
			'post_type' => 'job_package',
			'post_status' => 'publish',
			'posts_per_page'   => -1,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'meta_query' => array(
				array(
					'key'     => $prefix.'user_id',
					'value'   => $user_id,
					'compare' => '='
				),
				array(
					'key'     => $prefix.'package_type',
					'value'   => 'resume_package',
					'compare' => '='
				)
			)
		);
		
		$packages = get_posts($query_args);
		$return = array();
		if ( $valid && $packages ) {
			foreach ($packages as $package) {
				if ( self::resume_package_is_valid($user_id, $package->ID) ) {
					$return[] = $package;
				}
			}
		} else {
			$return = $packages;
		}
		return $return;
	}

	public static function increase_expiry_with_package( $user_id, $user_package_id ) {
		$user_id = self::get_user_id($user_id);

		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;

		if ( self::resume_package_is_valid( $user_id, $user_package_id ) && WP_Freeio_User::is_freelancer($user_id) ) {

			$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
			$resumes_duration = get_post_meta( $user_package_id, $prefix.'resumes_duration', true );
			$urgent_resumes = get_post_meta( $user_package_id, $prefix.'urgent_resumes', true );
			$featured_resumes = get_post_meta( $user_package_id, $prefix.'featured_resumes', true );
			$listing = array(
				'ID'            => $freelancer_id,
				'post_date'     => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 )
			);
			$post_type = get_post_type( $freelancer_id );

			if ( $post_type === 'freelancer' ) {
				delete_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'expiry_date' );

				update_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'user_package_id', $user_package_id );
				update_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'package_duration', $resumes_duration );
				if ( $urgent_resumes  == 'on' ) {
					update_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'urgent', 'on' );
					$_POST[WP_FREEIO_FREELANCER_PREFIX . 'urgent'] = 'on';
				} else {
					update_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'urgent', '' );
				}

				if ( $featured_resumes == 'on' ) {
					update_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'featured', 'on' );
					$_POST[WP_FREEIO_FREELANCER_PREFIX . 'featured'] = 'on';
				} else {
					update_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'featured', '' );
				}

				
				$post_status = 'publish';
				$freelancer_status = get_post_status($freelancer_id);
				if ( $freelancer_status == 'pending' || $freelancer_status == 'pending_approve' ) {
					$post_status = $freelancer_status;
				} elseif ( $freelancer_status == 'pending_payment' && function_exists('wp_freeio_get_option') ) {
					if ( wp_freeio_get_option('freelancers_requires_approval', 'auto') != 'auto' ) {
		            	$post_status = 'pending';
		            }
		            if ( wp_freeio_get_option('resumes_requires_approval', 'auto') != 'auto' ) {
		            	$post_status = 'pending_approve';
		            }
				}

				$listing['post_status'] = $post_status;

				// Do update
				wp_update_post( $listing );
			}

		}
	}

	public static function is_woocommerce_subscriptions_pre( $version ) {
		if ( class_exists( 'WC_Subscriptions' ) && version_compare( WC_Subscriptions::$version, $version, '<' ) ) {
			return true;
		}

		return false;
	}

}

