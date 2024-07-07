<?php
/**
 * Submit Form
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Wc_Paid_Listings_Service_Submit_Form {
	
	public static $service_package;
	public static $service_user_package;

	public static function init() {
		add_filter( 'wp_freeio_submit_service_steps',  array( __CLASS__, 'submit_service_steps' ), 5, 1 );

		// get service package
		if ( ! empty( $_POST['wpfiwpl_service_package'] ) ) {
			if ( is_numeric( $_POST['wpfiwpl_service_package'] ) ) {
				self::$service_package = absint( $_POST['wpfiwpl_service_package'] );
			}
		} elseif ( ! empty( $_POST['wpfiwpl_service_user_package'] ) ) {
			if ( is_numeric( $_POST['wpfiwpl_service_user_package'] ) ) {
				self::$service_user_package = absint( $_POST['wpfiwpl_service_user_package'] );
			}
		} elseif ( ! empty( $_COOKIE['chosen_service_package'] ) ) {
			self::$service_package  = absint( $_COOKIE['chosen_service_package'] );
		} elseif ( ! empty( $_COOKIE['chosen_service_user_package'] ) ) {
			self::$service_user_package = absint( $_COOKIE['chosen_service_user_package'] );
			if ( ! empty( $_COOKIE['chosen_service_package'] ) ) {
				unset($_COOKIE['chosen_service_package']);
				setcookie('chosen_service_package', null, -1, '/');
			}
		}

		if ( empty(self::$service_package) && empty(self::$service_user_package) ) {
			$service_id = ! empty( $_REQUEST['service_id'] ) ? absint( $_REQUEST['service_id'] ) : 0;
			if ( !empty($service_id) ) {
				$user_package_id = get_post_meta( $service_id, '_service_user_package_id', true );
				$package_id = get_post_meta( $service_id, '_service_package_id', true );

				if ( !empty($user_package_id) ) {
					self::$service_user_package = $user_package_id;
				} elseif( !empty($package_id) ) {
					self::$service_package = $package_id;
				}
			}
		}

		add_filter('wp-freeio-get-service-package-id', array( __CLASS__, 'get_package_id_post' ), 10, 2);

		add_action('wp-freeio-before-preview-service', array( __CLASS__, 'before_preview_service' ));

		add_action( 'wp_loaded', array( __CLASS__, 'after_wp_loaded' ) );
		
	}

	public static function after_wp_loaded() {
		add_action('wp_freeio_submit_service_construct', array( __CLASS__, 'before_view_package' ));
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
		            'terms'    => array('service_package', 'service_package_subscription'),
		        ),
		    ),
		);
		$posts = new WP_Query($query_args);
		if ( !empty($posts->posts) ) {
		    return $posts->posts;
		}

		return array();
	}

	public static function submit_service_steps($steps) {
		
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

			add_filter( 'wp_freeio_submit_service_post_status', array( __CLASS__, 'submit_service_post_status' ), 10, 2 );
		}

		return $steps;
	}

	public static function submit_service_post_status( $status, $service ) {
		if ( $service->post_status === 'preview' || $service->post_status === 'expired' ) {
			return 'pending_payment';
		}
		return $status;
	}

	public static function before_view_package($form) {
		if ( !empty($_GET['action']) && $_GET['action'] == 'continue' ) {

			$service_id = $form->get_service_id();
			$step = $form->get_step();

			if ( !empty($service_id) && $step < 1 ) {
				$user_package_id = get_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'user_package_id', true );
				$package_id = get_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'package_id', true );

				if ( !empty($user_package_id) ) {
					self::$service_user_package = $user_package_id;

					$form->set_step(1);
				} elseif( !empty($package_id) && in_array($package_id, self::get_cart_products()) ) {
					
					self::$service_package = $package_id;
					
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
		echo WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('choose-service-package-form', array('atts' => $atts) );
	}

	public static function get_package_id_post($product_id, $post_id) {
		
		if ( self::$service_user_package ) {
			$package_id = get_post_meta( self::$service_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX . 'product_id', true );
			return $package_id;
		} elseif (self::$service_package) {
			return self::$service_package;
		} else {
			if ( !empty($post_id) && metadata_exists('post', $post_id, WP_FREEIO_SERVICE_PREFIX.'package_id') ) {
				$package_id = get_post_meta( $post_id, WP_FREEIO_SERVICE_PREFIX.'package_id', true );
				return $package_id;
			}
		}

		return self::$service_package;
	}

	public static function choose_package_handler() {

		$form = WP_Freeio_Service_Submit_Form::get_instance();

		if ( !isset( $_POST['security-service-submit-package'] ) || ! wp_verify_nonce( $_POST['security-service-submit-package'], 'wp-freeio-service-submit-package-nonce' )  ) {
			$form->add_error(esc_html__('Sorry, your nonce did not verify.', 'wp-freeio-wc-paid-listings'));

			return;
		}

		$validation = self::validate_package();

		if ( is_wp_error( $validation ) ) {
			$form->add_error( $validation->get_error_message() );
			$form->set_step( array_search( 'wpfi-choose-packages', array_keys( $form->get_steps() ) ) );
			return false;
		}
		if ( self::$service_user_package ) {
			wc_setcookie( 'chosen_service_user_package', self::$service_user_package );
		} elseif ( self::$service_package ) {
			wc_setcookie( 'chosen_service_package', self::$service_package );
		}

		$form->next_step();
	}

	private static function validate_package() {
		if ( empty( self::$service_user_package ) && empty( self::$service_package )  ) {
			return new WP_Error( 'error', esc_html__( 'Invalid Package', 'wp-freeio-wc-paid-listings' ) );
		} elseif ( self::$service_user_package ) {
			if ( ! WP_Freeio_Wc_Paid_Listings_Mixes::service_package_is_valid( get_current_user_id(), self::$service_user_package ) ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'wp-freeio-wc-paid-listings' ) );
			}
		} elseif ( self::$service_package ) {
			$package = wc_get_product( self::$service_package );
			if ( empty($package) || ($package->get_type() != 'service_package' && ! $package->is_type( 'service_package_subscription' )) ) {
				return new WP_Error( 'error', esc_html__( 'Invalid Package', 'wp-freeio-wc-paid-listings' ) );
			}

			// Don't let them buy the same subscription twice if the subscription is for the package
			if ( class_exists( 'WC_Subscriptions' ) && is_user_logged_in() && $package->is_type( 'service_package_subscription' ) && 'package' === WP_Freeio_Wc_Paid_Listings_Job_Package_Subscription::get_package_subscription_type(self::$service_package) ) {
				if ( wcs_user_has_subscription( get_current_user_id(), self::$service_package, 'active' ) ) {
					return new WP_Error( 'error', __( 'You already have this subscription.', 'wp-freeio-wc-paid-listings' ) );
				}
			}
		}

		return true;
	}

	public static function before_preview_service($post) {
		if ( self::$service_user_package ) {
			update_post_meta( $post->ID, WP_FREEIO_SERVICE_PREFIX.'user_package_id', self::$service_user_package );
		} elseif ( self::$service_package ) {
			update_post_meta( $post->ID, WP_FREEIO_SERVICE_PREFIX.'package_id', self::$service_package );
		}
	}

	public static function process_package_handler() {
		$form = WP_Freeio_Service_Submit_Form::get_instance();
		$service_id = $form->get_service_id();
		$post_status = get_post_status( $service_id );
		if ( $post_status == 'preview' ) {
			$update_service = array(
				'ID' => $service_id,
				'post_status' => 'pending_payment',
				'post_date' => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 ),
				'post_author' => get_current_user_id(),
			);

			wp_update_post( $update_service );
		}

		if ( self::$service_user_package ) {
			$product_id = get_post_meta(self::$service_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'product_id', true);
			// Urgent
			$urgent_services = get_post_meta(self::$service_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'urgent_services', true );
			$urgent = '';
			if ( !empty($urgent_services) && $urgent_services == 'on') {
				$urgent = 'on';
			}
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX. 'urgent', $urgent );
			// Featured
			$feature_services = get_post_meta(self::$service_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'feature_services', true );
			$featured = '';
			if ( !empty($feature_services) && $feature_services == 'on' ) {
				$featured = 'on';
			}
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX. 'featured', $featured );
			//
			$service_duration = get_post_meta(self::$service_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'service_duration', true );
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'duration', $service_duration );
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'package_duration', $service_duration );
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'package_id', $product_id );
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'user_package_id', self::$service_user_package );

			$subscription_type = get_post_meta(self::$service_user_package, WP_FREEIO_WC_PAID_LISTINGS_PREFIX.'subscription_type', true );
			if ( 'listing' === $subscription_type ) {
				update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'expiry_date', '' ); // Never expire automatically
			}

			// Approve the service
			if ( in_array( get_post_status( $service_id ), array( 'pending_payment', 'expired' ) ) ) {
				WP_Freeio_Wc_Paid_Listings_Mixes::approve_service_with_package( $service_id, get_current_user_id(), self::$service_user_package );
			}
			// remove cookie
			wc_setcookie( 'chosen_service_user_package', '', time() - HOUR_IN_SECONDS );

			do_action( 'wpfiwpl_process_user_package_handler',self::$service_user_package, $service_id );

			$form->next_step();
		} elseif ( self::$service_package ) {
			global $woocommerce;
			// Featured
			$feature_services = get_post_meta(self::$service_package, '_feature_services', true );
			$featured = '';
			if ( !empty($feature_services) && $feature_services == 'yes' ) {
				$featured = 'on';
			}
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'featured', $featured );
			//
			$service_duration = get_post_meta(self::$service_package, '_services_duration', true );
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'duration', $service_duration );
			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'package_duration', $service_duration );

			update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'package_id', self::$service_package );
			
			$subscription_type = get_post_meta(self::$service_package, '_service_package_subscription_type', true );
			if ( 'listing' === $subscription_type ) {
				update_post_meta( $service_id, WP_FREEIO_SERVICE_PREFIX.'expiry_date', '' ); // Never expire automatically
			}

			$woocommerce->cart->empty_cart();
			WC()->cart->add_to_cart( self::$service_package, 1, '', '', array(
				'service_id' => $service_id
			) );

			wc_add_to_cart_message( self::$service_package );

			// remove cookie
			wc_setcookie( 'chosen_service_package', '', time() - HOUR_IN_SECONDS );

			do_action( 'wpfiwpl_process_package_handler', self::$service_package, $service_id );

			wp_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) );
			exit;
		}
	}

}

WP_Freeio_Wc_Paid_Listings_Service_Submit_Form::init();