<?php
/**
 * Project Submit Form
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_Project_Submit_Form {
	
	public static $project_package;
	public static $project_user_package;

	public static function init() {
		add_filter( 'wp_freeio_submit_project_steps',  array( __CLASS__, 'submit_project_steps' ), 5, 1 );

		// get project package
		if ( ! empty( $_POST['wpfiwpl_project_package'] ) ) {
			if ( is_numeric( $_POST['wpfiwpl_project_package'] ) ) {
				self::$project_package = absint( $_POST['wpfiwpl_project_package'] );
			}
		} elseif ( ! empty( $_POST['wpfiwpl_project_user_package'] ) ) {
			if ( is_numeric( $_POST['wpfiwpl_project_user_package'] ) ) {
				self::$project_user_package = absint( $_POST['wpfiwpl_project_user_package'] );
			}
		} elseif ( ! empty( $_COOKIE['chosen_project_package'] ) ) {
			self::$project_package  = absint( $_COOKIE['chosen_project_package'] );
		} elseif ( ! empty( $_COOKIE['chosen_project_user_package'] ) ) {
			self::$project_user_package = absint( $_COOKIE['chosen_project_user_package'] );
			if ( ! empty( $_COOKIE['chosen_project_package'] ) ) {
				unset($_COOKIE['chosen_project_package']);
				setcookie('chosen_project_package', null, -1, '/');
			}
		}

		if ( empty(self::$project_package) && empty(self::$project_user_package) ) {
			$project_id = ! empty( $_REQUEST['project_id'] ) ? absint( $_REQUEST['project_id'] ) : 0;
			if ( !empty($project_id) ) {
				$user_package_id = get_post_meta( $project_id, '_project_user_package_id', true );
				$package_id = get_post_meta( $project_id, '_project_package_id', true );

				if ( !empty($user_package_id) ) {
					self::$project_user_package = $user_package_id;
				} elseif( !empty($package_id) ) {
					self::$project_package = $package_id;
				}
			}
		}

		add_filter('wp-freeio-get-project-package-id', array( __CLASS__, 'get_package_id_post' ), 10, 2);

		add_action('wp-freeio-before-preview-project', array( __CLASS__, 'before_preview_project' ));

		add_action( 'wp_loaded', array( __CLASS__, 'after_wp_loaded' ) );
		
	}

	public static function after_wp_loaded() {
		add_action('wp_freeio_submit_project_construct', array( __CLASS__, 'before_view_package' ));
	}

	public static function get_products() {
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
		            'terms'    => array('project_package', 'project_package_subscription'),
		        ),
		    ),
		);
		$posts = new WP_Query($query_args);
		if ( !empty($posts->posts) ) {
		    return $posts->posts;
		}
// 		$posts = get_posts( $query_args );

		return array();
	}

	public static function submit_project_steps($steps) {
		
		$packages = self::get_products();

		if ( !empty($packages) ) {
			$steps['wpfi-choose-packages'] = array(
				'view'     => array( __CLASS__, 'choose_package' ),
				'handler'  => array( __CLASS__, 'choose_package_handler' ),
				'priority' => 1
			);

			$steps['wpfi-process-packages'] = array(
				'name'     => '',
				'view'     => false,
				'handler'  => array( __CLASS__, 'process_package_handler' ),
				'priority' => 25
			);

			add_filter( 'wp_freeio_submit_project_post_status', array( __CLASS__, 'submit_project_post_status' ), 10, 2 );
		}

		return $steps;
	}

	public static function submit_project_post_status( $status, $project ) {
		if ( $project->post_status === 'preview' || $project->post_status === 'expired' ) {
			return 'pending_payment';
		}
		return $status;
	}

	public static function before_view_package($form) {
		if ( !empty($_GET['action']) && $_GET['action'] == 'continue' ) {

			$project_id = $form->get_project_id();
			$step = $form->get_step();

			if ( !empty($project_id) && $step < 1 ) {
				$user_package_id = get_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'user_package_id', true );
				$package_id = get_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'package_id', true );

				if ( !empty($user_package_id) ) {
					self::$project_user_package = $user_package_id;

					$form->set_step(1);
				} elseif( !empty($package_id) && in_array($package_id, self::get_cart_products()) ) {
					
					self::$project_package = $package_id;
					
					$form->set_step(1);
				}
			}
		}

	}

	public static function get_cart_products() {
		$products = [];
		$carts = WC()->cart->get_cart();
		if ( !empty($carts) ) {
			foreach ($carts as $key => $cart_item) {
				$products[] = $cart_item['product_id'];
			}
		}
		return $products;
	}

	public static function choose_package($atts = array()) {
		echo WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('choose-project-package-form', array('atts' => $atts) );
	}

	public static function get_package_id_post($product_id, $post_id) {
		
		if ( self::$project_user_package ) {
			$package_id = get_post_meta( self::$project_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX . 'product_id', true );
			return $package_id;
		} elseif (self::$project_package) {
			return self::$project_package;
		} else {
			if ( !empty($post_id) && metadata_exists('post', $post_id, WP_FREEIO_PROJECT_PREFIX.'package_id') ) {
				$package_id = get_post_meta( $post_id, WP_FREEIO_PROJECT_PREFIX.'package_id', true );
				return $package_id;
			}
		}

		return self::$project_package;
	}

	public static function choose_package_handler() {

		$form = WP_Freeio_Project_Submit_Form::get_instance();

		if ( !isset( $_POST['security-project-submit-package'] ) || ! wp_verify_nonce( $_POST['security-project-submit-package'], 'wp-freeio-project-submit-package-nonce' )  ) {
			$form->add_error(esc_html__('Sorry, your nonce did not verify.', 'wp-freeio-wc-paid-listings'));
			return;
		}

		$validation = self::validate_package();

		if ( is_wp_error( $validation ) ) {
			$form->add_error( $validation->get_error_message() );
			$form->set_step( array_search( 'wpfi-choose-packages', array_keys( $form->get_steps() ) ) );
			return false;
		}
		if ( self::$project_user_package ) {
			wc_setcookie( 'chosen_project_user_package', self::$project_user_package );
		} elseif ( self::$project_package ) {
			wc_setcookie( 'chosen_project_package', self::$project_package );
		}
		

		$form->next_step();
	}

	private static function validate_package() {
		if ( empty( self::$project_user_package ) && empty( self::$project_package )  ) {
			return new WP_Error( 'error', esc_html__( 'Invalid Package', 'wp-freeio-wc-paid-listings' ) );
		} elseif ( self::$project_user_package ) {
			if ( ! WP_Freeio_Wc_Paid_Listings_Mixes::project_package_is_valid( get_current_user_id(), self::$project_user_package ) ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'wp-freeio-wc-paid-listings' ) );
			}
		} elseif ( self::$project_package ) {
			$package = wc_get_product( self::$project_package );
			if ( empty($package) || ($package->get_type() != 'project_package' && ! $package->is_type( 'project_package_subscription' )) ) {
				return new WP_Error( 'error', esc_html__( 'Invalid Package', 'wp-freeio-wc-paid-listings' ) );
			}

			// Don't let them buy the same subscription twice if the subscription is for the package
			if ( class_exists( 'WC_Subscriptions' ) && is_user_logged_in() && $package->is_type( 'project_package_subscription' ) && 'package' === WP_Freeio_Wc_Paid_Listings_Job_Package_Subscription::get_package_subscription_type(self::$project_package) ) {
				if ( wcs_user_has_subscription( get_current_user_id(), self::$project_package, 'active' ) ) {
					return new WP_Error( 'error', __( 'You already have this subscription.', 'wp-freeio-wc-paid-listings' ) );
				}
			}
		}

		return true;
	}

	public static function before_preview_project($post) {
		if ( self::$project_user_package ) {
			update_post_meta( $post->ID, WP_FREEIO_PROJECT_PREFIX.'user_package_id', self::$project_user_package );
		} elseif ( self::$project_package ) {
			update_post_meta( $post->ID, WP_FREEIO_PROJECT_PREFIX.'package_id', self::$project_package );
		}
	}

	public static function process_package_handler() {
		$form = WP_Freeio_Project_Submit_Form::get_instance();
		$project_id = $form->get_project_id();
		$post_status = get_post_status( $project_id );
		if ( $post_status == 'preview' ) {
			$update_project = array(
				'ID' => $project_id,
				'post_status' => 'pending_payment',
				'post_date' => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 ),
				'post_author' => get_current_user_id(),
			);

			wp_update_post( $update_project );
		}

		if ( self::$project_user_package ) {
			$product_id = get_post_meta(self::$project_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'product_id', true);
			// Urgent
			$urgent_projects = get_post_meta(self::$project_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'urgent_projects', true );
			$urgent = '';
			if ( !empty($urgent_projects) && $urgent_projects == 'on') {
				$urgent = 'on';
			}
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX. 'urgent', $urgent );
			// Featured
			$feature_projects = get_post_meta(self::$project_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'feature_projects', true );
			$featured = '';
			if ( !empty($feature_projects) && $feature_projects == 'on' ) {
				$featured = 'on';
			}
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX. 'featured', $featured );
			//
			$project_duration = get_post_meta(self::$project_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'project_duration', true );
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'duration', $project_duration );
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'package_duration', $project_duration );
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'package_id', $product_id );
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'user_package_id', self::$project_user_package );

			$subscription_type = get_post_meta(self::$project_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'subscription_type', true );
			if ( 'listing' === $subscription_type ) {
				update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'expiry_date', '' ); // Never expire automatically
			}

			// Approve the project
			if ( in_array( get_post_status( $project_id ), array( 'pending_payment', 'expired' ) ) ) {
				WP_Freeio_Wc_Paid_Listings_Mixes::approve_project_with_package( $project_id, get_current_user_id(), self::$project_user_package );
			}
			// remove cookie
			wc_setcookie( 'chosen_project_user_package', '', time() - HOUR_IN_SECONDS );

			do_action( 'wpfiwpl_process_user_package_handler',self::$project_user_package, $project_id );

			$form->next_step();
		} elseif ( self::$project_package ) {
			global $woocommerce;
			// Featured
			$feature_projects = get_post_meta(self::$project_package, '_feature_projects', true );
			$featured = '';
			if ( !empty($feature_projects) && $feature_projects == 'yes' ) {
				$featured = 'on';
			}
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'featured', $featured );
			//
			$project_duration = get_post_meta(self::$project_package, '_projects_duration', true );
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'duration', $project_duration );
			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'package_duration', $project_duration );

			update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'package_id', self::$project_package );
			
			$subscription_type = get_post_meta(self::$project_package, '_project_package_subscription_type', true );
			if ( 'listing' === $subscription_type ) {
				update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'expiry_date', '' ); // Never expire automatically
			}

			$woocommerce->cart->empty_cart();
			WC()->cart->add_to_cart( self::$project_package, 1, '', '', array(
				'project_id' => $project_id
			) );

			wc_add_to_cart_message( self::$project_package );

			// remove cookie
			wc_setcookie( 'chosen_project_package', '', time() - HOUR_IN_SECONDS );

			do_action( 'wpfiwpl_process_package_handler', self::$project_package, $project_id );

			wp_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) );
			exit;
		}
	}

}

WP_Freeio_Wc_Paid_Listings_Project_Submit_Form::init();