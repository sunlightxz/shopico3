<?php
/**
 * product type: package
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wp_freeio_wc_paid_listings_register_package_product_type() {
	class WP_Freeio_Wc_Paid_Listings_Product_Type_Service_Package extends WC_Product_Simple {
		
		public function __construct( $product ) {
			parent::__construct( $product );
		}

		public function get_type() {
	        return 'service_package';
	    }

	    public function is_sold_individually() {
			return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
		}

		public function is_purchasable() {
			return true;
		}

		public function is_virtual() {
			return true;
		}
	}

	class WP_Freeio_Wc_Paid_Listings_Product_Type_Project_Package extends WC_Product_Simple {
		
		public function __construct( $product ) {
			parent::__construct( $product );
		}

		public function get_type() {
	        return 'project_package';
	    }

	    public function is_sold_individually() {
			return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
		}

		public function is_purchasable() {
			return true;
		}

		public function is_virtual() {
			return true;
		}
	}

	class WP_Freeio_Wc_Paid_Listings_Product_Type_Package extends WC_Product_Simple {
		
		public function __construct( $product ) {
			parent::__construct( $product );
		}

		public function get_type() {
	        return 'job_package';
	    }

	    public function is_sold_individually() {
			return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
		}

		public function is_purchasable() {
			return true;
		}

		public function is_virtual() {
			return true;
		}
	}

	class WP_Freeio_Wc_Paid_Listings_Product_Type_CV_Package extends WC_Product_Simple {
		
		public function __construct( $product ) {
			parent::__construct( $product );
		}

		public function get_type() {
	        return 'cv_package';
	    }

	    public function is_sold_individually() {
			return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
		}

		public function is_purchasable() {
			return true;
		}

		public function is_virtual() {
			return true;
		}
	}

	class WP_Freeio_Wc_Paid_Listings_Product_Type_Contact_Package extends WC_Product_Simple {
		
		public function __construct( $product ) {
			parent::__construct( $product );
		}

		public function get_type() {
	        return 'contact_package';
	    }

	    public function is_sold_individually() {
			return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
		}

		public function is_purchasable() {
			return true;
		}

		public function is_virtual() {
			return true;
		}
	}

	class WP_Freeio_Wc_Paid_Listings_Product_Type_Freelancer_Package extends WC_Product_Simple {
		
		public function __construct( $product ) {
			parent::__construct( $product );
		}

		public function get_type() {
	        return 'freelancer_package';
	    }

	    public function is_sold_individually() {
			return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
		}

		public function is_purchasable() {
			return true;
		}

		public function is_virtual() {
			return true;
		}
	}

	class WP_Freeio_Wc_Paid_Listings_Product_Type_Resume_Package extends WC_Product_Simple {
		
		public function __construct( $product ) {
			parent::__construct( $product );
		}

		public function get_type() {
	        return 'resume_package';
	    }

	    public function is_sold_individually() {
			return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
		}

		public function is_purchasable() {
			return true;
		}

		public function is_virtual() {
			return true;
		}
	}

	if ( class_exists( 'WC_Subscriptions' ) ) {
		class WP_Freeio_Wc_Paid_Listings_Product_Type_Service_Package_Subscription extends WC_Product_Subscription {
		
			public function __construct( $product ) {
				parent::__construct( $product );
			}

			public function get_type() {
		        return 'service_package_subscription';
		    }

		    public function is_type( $type ) {
				return ( 'service_package_subscription' == $type || ( is_array( $type ) && in_array( 'service_package_subscription', $type ) ) ) ? true : parent::is_type( $type );
			}
			
			public function add_to_cart_url() {
				$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

				return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
			}

		    public function is_sold_individually() {
				return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
			}

			public function is_purchasable() {
				return true;
			}

			public function is_virtual() {
				return true;
			}
		}

		class WP_Freeio_Wc_Paid_Listings_Product_Type_Project_Package_Subscription extends WC_Product_Subscription {
		
			public function __construct( $product ) {
				parent::__construct( $product );
			}

			public function get_type() {
		        return 'project_package_subscription';
		    }

		    public function is_type( $type ) {
				return ( 'project_package_subscription' == $type || ( is_array( $type ) && in_array( 'project_package_subscription', $type ) ) ) ? true : parent::is_type( $type );
			}
			
			public function add_to_cart_url() {
				$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

				return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
			}

		    public function is_sold_individually() {
				return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
			}

			public function is_purchasable() {
				return true;
			}

			public function is_virtual() {
				return true;
			}
		}

		class WP_Freeio_Wc_Paid_Listings_Product_Type_Package_Subscription extends WC_Product_Subscription {
		
			public function __construct( $product ) {
				parent::__construct( $product );
			}

			public function get_type() {
		        return 'job_package_subscription';
		    }

		    public function is_type( $type ) {
				return ( 'job_package_subscription' == $type || ( is_array( $type ) && in_array( 'job_package_subscription', $type ) ) ) ? true : parent::is_type( $type );
			}
			
			public function add_to_cart_url() {
				$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

				return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
			}

		    public function is_sold_individually() {
				return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
			}

			public function is_purchasable() {
				return true;
			}

			public function is_virtual() {
				return true;
			}
		}

		class WP_Freeio_Wc_Paid_Listings_Product_Type_CV_Package_Subscription extends WC_Product_Subscription {
		
			public function __construct( $product ) {
				parent::__construct( $product );
			}

			public function get_type() {
		        return 'cv_package_subscription';
		    }

		    public function is_type( $type ) {
				return ( 'cv_package_subscription' == $type || ( is_array( $type ) && in_array( 'cv_package_subscription', $type ) ) ) ? true : parent::is_type( $type );
			}
			
			public function add_to_cart_url() {
				$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

				return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
			}

		    public function is_sold_individually() {
				return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
			}

			public function is_purchasable() {
				return true;
			}

			public function is_virtual() {
				return true;
			}
		}

		class WP_Freeio_Wc_Paid_Listings_Product_Type_Contact_Package_Subscription extends WC_Product_Subscription {
		
			public function __construct( $product ) {
				parent::__construct( $product );
			}

			public function get_type() {
		        return 'contact_package_subscription';
		    }

		    public function is_type( $type ) {
				return ( 'contact_package_subscription' == $type || ( is_array( $type ) && in_array( 'contact_package_subscription', $type ) ) ) ? true : parent::is_type( $type );
			}
			
			public function add_to_cart_url() {
				$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

				return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
			}

		    public function is_sold_individually() {
				return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
			}

			public function is_purchasable() {
				return true;
			}

			public function is_virtual() {
				return true;
			}
		}

		class WP_Freeio_Wc_Paid_Listings_Product_Type_Freelancer_Package_Subscription extends WC_Product_Subscription {
		
			public function __construct( $product ) {
				parent::__construct( $product );
			}

			public function get_type() {
		        return 'freelancer_package_subscription';
		    }

		    public function is_type( $type ) {
				return ( 'freelancer_package_subscription' == $type || ( is_array( $type ) && in_array( 'freelancer_package_subscription', $type ) ) ) ? true : parent::is_type( $type );
			}
			
			public function add_to_cart_url() {
				$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

				return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
			}

		    public function is_sold_individually() {
				return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
			}

			public function is_purchasable() {
				return true;
			}

			public function is_virtual() {
				return true;
			}
		}

		class WP_Freeio_Wc_Paid_Listings_Product_Type_Resume_Package_Subscription extends WC_Product_Subscription {
		
			public function __construct( $product ) {
				parent::__construct( $product );
			}

			public function get_type() {
		        return 'resume_package_subscription';
		    }

		    public function is_type( $type ) {
				return ( 'resume_package_subscription' == $type || ( is_array( $type ) && in_array( 'resume_package_subscription', $type ) ) ) ? true : parent::is_type( $type );
			}
			
			public function add_to_cart_url() {
				$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

				return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
			}
			
		    public function is_sold_individually() {
				return apply_filters( 'wp_freeio_wc_paid_listings_' . $this->product_type . '_is_sold_individually', true );
			}

			public function is_purchasable() {
				return true;
			}

			public function is_virtual() {
				return true;
			}
		}
	}
}

add_action( 'init', 'wp_freeio_wc_paid_listings_register_package_product_type' );


function wp_freeio_wc_paid_listings_add_job_package_product( $types ) {
	$types[ 'service_package' ] = __( 'Service Package', 'wp-freeio-wc-paid-listings' );
	$types[ 'project_package' ] = __( 'Project Package', 'wp-freeio-wc-paid-listings' );
	$types[ 'job_package' ] = __( 'Job Package', 'wp-freeio-wc-paid-listings' );
	$types[ 'cv_package' ] = __( 'CV Package', 'wp-freeio-wc-paid-listings' );
	$types[ 'contact_package' ] = __( 'Contact Package', 'wp-freeio-wc-paid-listings' );
	$types[ 'freelancer_package' ] = __( 'Freelancer Package', 'wp-freeio-wc-paid-listings' );
	$types[ 'resume_package' ] = __( 'Resume Package', 'wp-freeio-wc-paid-listings' );
	

	if ( class_exists( 'WC_Subscriptions' ) ) {
		$types['service_package_subscription'] = __( 'Service Package Subscription', 'wp-freeio-wc-paid-listings' );
		$types['project_package_subscription'] = __( 'Project Package Subscription', 'wp-freeio-wc-paid-listings' );
		$types['job_package_subscription'] = __( 'Job Package Subscription', 'wp-freeio-wc-paid-listings' );
		$types['cv_package_subscription'] = __( 'CV Package Subscription', 'wp-freeio-wc-paid-listings' );
		$types['contact_package_subscription'] = __( 'Contact Package Subscription', 'wp-freeio-wc-paid-listings' );
		$types['freelancer_package_subscription'] = __( 'Freelancer Package Subscription', 'wp-freeio-wc-paid-listings' );
		$types['resume_package_subscription'] = __( 'Resume Package Subscription', 'wp-freeio-wc-paid-listings' );
	}

	return $types;
}

add_filter( 'product_type_selector', 'wp_freeio_wc_paid_listings_add_job_package_product' );

function wp_freeio_wc_paid_listings_woocommerce_product_class( $classname, $product_type ) {

	if ( $product_type == 'service_package' ) { // notice the checking here.
        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Service_Package';
    }

    if ( $product_type == 'project_package' ) { // notice the checking here.
        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Project_Package';
    }

    if ( $product_type == 'job_package' ) { // notice the checking here.
        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Package';
    }

    if ( $product_type == 'cv_package' ) { // notice the checking here.
        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_CV_Package';
    }

    if ( $product_type == 'contact_package' ) { // notice the checking here.
        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Contact_Package';
    }

    if ( $product_type == 'freelancer_package' ) { // notice the checking here.
        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Freelancer_Package';
    }

    if ( $product_type == 'resume_package' ) { // notice the checking here.
        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Resume_Package';
    }

    if ( class_exists( 'WC_Subscriptions' ) ) {
    	if ( $product_type == 'service_package_subscription' ) { // notice the checking here.
	        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Service_Package_Subscription';
	    }

	    if ( $product_type == 'project_package_subscription' ) { // notice the checking here.
	        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Project_Package_Subscription';
	    }

	    if ( $product_type == 'job_package_subscription' ) { // notice the checking here.
	        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Package_Subscription';
	    }

	    if ( $product_type == 'cv_package_subscription' ) { // notice the checking here.
	        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_CV_Package_Subscription';
	    }

	    if ( $product_type == 'contact_package_subscription' ) { // notice the checking here.
	        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Contact_Package_Subscription';
	    }

	    if ( $product_type == 'freelancer_package_subscription' ) { // notice the checking here.
	        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Freelancer_Package_Subscription';
	    }

	    if ( $product_type == 'resume_package_subscription' ) { // notice the checking here.
	        $classname = 'WP_Freeio_Wc_Paid_Listings_Product_Type_Resume_Package_Subscription';
	    }
    }
    return $classname;
}

add_filter( 'woocommerce_product_class', 'wp_freeio_wc_paid_listings_woocommerce_product_class', 10, 2 );


/**
 * Show pricing fields for package product.
 */
function wp_freeio_wc_paid_listings_package_custom_js() {

	if ( 'product' != get_post_type() ) {
		return;
	}

	?><script type='text/javascript'>
		jQuery( document ).ready( function() {
			// service package
			jQuery('.product_data_tabs .general_tab').show();
        	jQuery('#general_product_data .pricing').addClass('show_if_service_package').show();
			jQuery('.inventory_options').addClass('show_if_service_package').show();
			jQuery('.inventory_options').addClass('show_if_service_package').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_service_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_service_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_service_package').show();

            // project package
			jQuery('.product_data_tabs .general_tab').show();
        	jQuery('#general_product_data .pricing').addClass('show_if_project_package').show();
			jQuery('.inventory_options').addClass('show_if_project_package').show();
			jQuery('.inventory_options').addClass('show_if_project_package').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_project_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_project_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_project_package').show();

			// job package
			jQuery('.product_data_tabs .general_tab').show();
        	jQuery('#general_product_data .pricing').addClass('show_if_job_package').show();
			jQuery('.inventory_options').addClass('show_if_job_package').show();
			jQuery('.inventory_options').addClass('show_if_job_package').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_job_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_job_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_job_package').show();

            // cv
            jQuery('#general_product_data .pricing').addClass('show_if_cv_package').show();
			jQuery('.inventory_options').addClass('show_if_cv_package').show();
			jQuery('.inventory_options').addClass('show_if_cv_package').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_cv_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_cv_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_cv_package').show();

            // contact
            jQuery('#general_product_data .pricing').addClass('show_if_contact_package').show();
			jQuery('.inventory_options').addClass('show_if_contact_package').show();
			jQuery('.inventory_options').addClass('show_if_contact_package').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_contact_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_contact_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_contact_package').show();

            // freelancer
            jQuery('#general_product_data .pricing').addClass('show_if_freelancer_package').show();
			jQuery('.inventory_options').addClass('show_if_freelancer_package').show();
			jQuery('.inventory_options').addClass('show_if_freelancer_package').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_freelancer_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_freelancer_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_freelancer_package').show();

            // resume
            jQuery('#general_product_data .pricing').addClass('show_if_resume_package').show();
			jQuery('.inventory_options').addClass('show_if_resume_package').show();
			jQuery('.inventory_options').addClass('show_if_resume_package').show();
            jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_resume_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_resume_package').show();
            jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_resume_package').show();
		});
	</script><?php
}
add_action( 'admin_footer', 'wp_freeio_wc_paid_listings_package_custom_js' );

add_filter( 'woocommerce_subscription_product_types', 'wp_freeio_wc_paid_listings_woocommerce_subscription_product_types' );
function wp_freeio_wc_paid_listings_woocommerce_subscription_product_types( $types ) {
	$types[] = 'service_package_subscription';
	$types[] = 'project_package_subscription';
	$types[] = 'job_package_subscription';
	$types[] = 'cv_package_subscription';
	$types[] = 'contact_package_subscription';
	$types[] = 'freelancer_package_subscription';
	$types[] = 'resume_package_subscription';
	return $types;
}

add_action( 'woocommerce_product_options_general_product_data', 'wp_freeio_wc_paid_listings_package_options_product_tab_content' );

/**
 * Contents of the package options product tab.
 */
function wp_freeio_wc_paid_listings_package_options_product_tab_content() {
	global $post;
	$post_id = $post->ID;
	?>
	<!-- Service Package -->
	<!-- <div id='service_package_options' class='panel woocommerce_options_panel'> -->
	<div class="options_group show_if_service_package show_if_service_package_subscription">
		<?php
			if ( class_exists( 'WC_Subscriptions' ) ) {
				woocommerce_wp_select( array(
					'id' => '_service_package_subscription_type',
					'label' => __( 'Subscription Type', 'wp-freeio-wc-paid-listings' ),
					'description' => __( 'Choose how subscriptions affect this package', 'wp-freeio-wc-paid-listings' ),
					'value' => get_post_meta( $post_id, '_service_package_subscription_type', true ),
					'desc_tip' => true,
					'options' => array(
						'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-freeio-wc-paid-listings' ),
						'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-freeio-wc-paid-listings' )
					),
					'wrapper_class' => 'show_if_service_package_subscription',
				) );
			}
			woocommerce_wp_checkbox( array(
				'id' 		=> '_feature_services',
				'label' 	=> __( 'Feature Services?', 'wp-freeio-wc-paid-listings' ),
				'description'	=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_services_limit',
				'label'			=> __( 'Services Limit', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of listings a user can post with this package', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_services_duration',
				'label'			=> __( 'Services Duration (Days)', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of days that the listings will be active', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
			) );

			do_action('wp_freeio_wc_paid_listings_service_package_options_product_tab_content');
		?>
	</div>

	<!-- Project Package -->
	<!-- <div id='project_package_options' class='panel woocommerce_options_panel'> -->
	<div class="options_group show_if_project_package show_if_project_package_subscription">
		<?php
			if ( class_exists( 'WC_Subscriptions' ) ) {
				woocommerce_wp_select( array(
					'id' => '_project_package_subscription_type',
					'label' => __( 'Subscription Type', 'wp-freeio-wc-paid-listings' ),
					'description' => __( 'Choose how subscriptions affect this package', 'wp-freeio-wc-paid-listings' ),
					'value' => get_post_meta( $post_id, '_project_package_subscription_type', true ),
					'desc_tip' => true,
					'options' => array(
						'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-freeio-wc-paid-listings' ),
						'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-freeio-wc-paid-listings' )
					),
					'wrapper_class' => 'show_if_project_package_subscription',
				) );
			}
			woocommerce_wp_checkbox( array(
				'id' 		=> '_feature_projects',
				'label' 	=> __( 'Feature Projects?', 'wp-freeio-wc-paid-listings' ),
				'description'	=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_projects_limit',
				'label'			=> __( 'Projects Limit', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of listings a user can post with this package', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_projects_duration',
				'label'			=> __( 'Projects Duration (Days)', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of days that the listings will be active', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
			) );

			do_action('wp_freeio_wc_paid_listings_project_package_options_product_tab_content');
		?>
	</div>

	<!-- Job Package -->
	<!-- <div id='job_package_options' class='panel woocommerce_options_panel'> -->
	<div class="options_group show_if_job_package show_if_job_package_subscription">
		<?php
			if ( class_exists( 'WC_Subscriptions' ) ) {
				woocommerce_wp_select( array(
					'id' => '_job_package_subscription_type',
					'label' => __( 'Subscription Type', 'wp-freeio-wc-paid-listings' ),
					'description' => __( 'Choose how subscriptions affect this package', 'wp-freeio-wc-paid-listings' ),
					'value' => get_post_meta( $post_id, '_job_package_subscription_type', true ),
					'desc_tip' => true,
					'options' => array(
						'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-freeio-wc-paid-listings' ),
						'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-freeio-wc-paid-listings' )
					),
					'wrapper_class' => 'show_if_job_package_subscription',
				) );
			}
			woocommerce_wp_checkbox( array(
				'id' 		=> '_urgent_jobs',
				'label' 	=> __( 'Urgent Jobs?', 'wp-freeio-wc-paid-listings' ),
				'description'	=> __( 'Urgent this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
			) );
			woocommerce_wp_checkbox( array(
				'id' 		=> '_feature_jobs',
				'label' 	=> __( 'Feature Jobs?', 'wp-freeio-wc-paid-listings' ),
				'description'	=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_jobs_limit',
				'label'			=> __( 'Jobs Limit', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of listings a user can post with this package', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_jobs_duration',
				'label'			=> __( 'Jobs Duration (Days)', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of days that the listings will be active', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
			) );

			do_action('wp_freeio_wc_paid_listings_package_options_product_tab_content');
		?>
	</div>

	<!-- CV Package -->
	<!-- <div id='cv_package_options' class='panel woocommerce_options_panel'> -->
	<div class="options_group show_if_cv_package show_if_cv_package_subscription">
		<?php
			if ( class_exists( 'WC_Subscriptions' ) ) {
				woocommerce_wp_select( array(
					'id' => '_cv_package_subscription_type',
					'label' => __( 'Subscription Type', 'wp-freeio-wc-paid-listings' ),
					'description' => __( 'Choose how subscriptions affect this package', 'wp-freeio-wc-paid-listings' ),
					'value' => get_post_meta( $post_id, '_cv_package_subscription_type', true ),
					'desc_tip' => true,
					'options' => array(
						'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-freeio-wc-paid-listings' ),
						'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-freeio-wc-paid-listings' )
					),
					'wrapper_class' => 'show_if_cv_package_subscription',
				) );
			}

			woocommerce_wp_text_input( array(
				'id'			=> '_cv_package_expiry_time',
				'label'			=> __( 'Package Expiry Time (Days)', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of days that the user package active. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
				'default'		=> 30
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_cv_number_of_cv',
				'label'			=> __( 'Number of CV\'s', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of CV to view in this package. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
				'default'		=> 10
			) );

			do_action('wp_freeio_wc_paid_cv_listings_package_options_product_tab_content');
		?>
	</div>

	<!-- Contact Package -->
	<!-- <div id='contact_package_options' class='panel woocommerce_options_panel'> -->
	<div class="options_group show_if_contact_package show_if_contact_package_subscription">
		<?php
			if ( class_exists( 'WC_Subscriptions' ) ) {
				woocommerce_wp_select( array(
					'id' => '_contact_package_subscription_type',
					'label' => __( 'Subscription Type', 'wp-freeio-wc-paid-listings' ),
					'description' => __( 'Choose how subscriptions affect this package', 'wp-freeio-wc-paid-listings' ),
					'value' => get_post_meta( $post_id, '_contact_package_subscription_type', true ),
					'desc_tip' => true,
					'options' => array(
						'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-freeio-wc-paid-listings' ),
						'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-freeio-wc-paid-listings' )
					),
					'wrapper_class' => 'show_if_contact_package_subscription',
				) );
			}

			woocommerce_wp_text_input( array(
				'id'			=> '_contact_package_expiry_time',
				'label'			=> __( 'Package Expiry Time (Days)', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of days that the user package active. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
				'default'		=> 30
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_contact_number_of_cv',
				'label'			=> __( 'Number of CV\'s', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of CV to view in this package. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
				'default'		=> 10
			) );

			do_action('wp_freeio_wc_paid_contact_listings_package_options_product_tab_content');
		?>
	</div>

	<!-- Freelancer package -->
	<!-- <div id='freelancer_package_options' class='panel woocommerce_options_panel'> -->
	<div class="options_group show_if_freelancer_package show_if_freelancer_package_subscription">
		<?php
			if ( class_exists( 'WC_Subscriptions' ) ) {
				woocommerce_wp_select( array(
					'id' => '_freelancer_package_subscription_type',
					'label' => __( 'Subscription Type', 'wp-freeio-wc-paid-listings' ),
					'description' => __( 'Choose how subscriptions affect this package', 'wp-freeio-wc-paid-listings' ),
					'value' => get_post_meta( $post_id, '_freelancer_package_subscription_type', true ),
					'desc_tip' => true,
					'options' => array(
						'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-freeio-wc-paid-listings' ),
						'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-freeio-wc-paid-listings' )
					),
					'wrapper_class' => 'show_if_freelancer_package_subscription',
				) );
			}

			woocommerce_wp_text_input( array(
				'id'			=> '_freelancer_package_expiry_time',
				'label'			=> __( 'Package Expiry Time (Days)', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of days that the user package active. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
				'default'		=> 30
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_freelancer_number_of_applications',
				'label'			=> __( 'Number of Applications', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of Applications to freelancer apply. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
				'default'		=> 10
			) );

			do_action('wp_freeio_wc_paid_freelancer_listings_package_options_product_tab_content');
		?>
	</div>

	<!-- Resume package -->
	<!-- <div id='resume_package_options' class='panel woocommerce_options_panel'> -->
	<div class="options_group show_if_resume_package show_if_resume_package_subscription">
		<?php
			if ( class_exists( 'WC_Subscriptions' ) ) {
				woocommerce_wp_select( array(
					'id' => '_resume_package_subscription_type',
					'label' => __( 'Subscription Type', 'wp-freeio-wc-paid-listings' ),
					'description' => __( 'Choose how subscriptions affect this package', 'wp-freeio-wc-paid-listings' ),
					'value' => get_post_meta( $post_id, '_resume_package_subscription_type', true ),
					'desc_tip' => true,
					'options' => array(
						'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-freeio-wc-paid-listings' ),
						'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-freeio-wc-paid-listings' )
					),
					'wrapper_class' => 'show_if_resume_package_subscription',
				) );
			}
			// woocommerce_wp_checkbox( array(
			// 	'id' 		=> '_urgent_resumes',
			// 	'label' 	=> __( 'Urgent Resumes?', 'wp-freeio-wc-paid-listings' ),
			// 	'description'	=> __( 'Urgent this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
			// ) );
			woocommerce_wp_checkbox( array(
				'id' 		=> '_featured_resumes',
				'label' 	=> __( 'Featured Resumes?', 'wp-freeio-wc-paid-listings' ),
				'description'	=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
			) );
			woocommerce_wp_text_input( array(
				'id'			=> '_resumes_duration',
				'label'			=> __( 'Resume Duration (Days)', 'wp-freeio-wc-paid-listings' ),
				'desc_tip'		=> true,
				'description'	=> __( 'The number of days that the resume will be active', 'wp-freeio-wc-paid-listings' ),
				'type' 			=> 'number',
			) );
			do_action('wp_freeio_wc_paid_resume_listings_package_options_product_tab_content');
		?>
	</div>
	<?php
}

/**
 * Save the Service Package custom fields.
 */
function wp_freeio_wc_paid_listings_save_service_package_option_field( $post_id ) {
	
	$feature_services = isset( $_POST['_feature_services'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_feature_services', $feature_services );
	
	if ( isset( $_POST['_service_package_subscription_type'] ) ) {
		update_post_meta( $post_id, '_service_package_subscription_type', sanitize_text_field( $_POST['_service_package_subscription_type'] ) );
	}

	if ( isset( $_POST['_services_limit'] ) ) {
		update_post_meta( $post_id, '_services_limit', sanitize_text_field( $_POST['_services_limit'] ) );
	}

	if ( isset( $_POST['_services_duration'] ) ) {
		update_post_meta( $post_id, '_services_duration', sanitize_text_field( $_POST['_services_duration'] ) );
	}
}
add_action( 'woocommerce_process_product_meta_service_package', 'wp_freeio_wc_paid_listings_save_service_package_option_field'  );
add_action( 'woocommerce_process_product_meta_service_package_subscription', 'wp_freeio_wc_paid_listings_save_service_package_option_field'  );

/**
 * Save the Project Package custom fields.
 */
function wp_freeio_wc_paid_listings_save_project_package_option_field( $post_id ) {
	
	$feature_projects = isset( $_POST['_feature_projects'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_feature_projects', $feature_projects );
	
	if ( isset( $_POST['_project_package_subscription_type'] ) ) {
		update_post_meta( $post_id, '_project_package_subscription_type', sanitize_text_field( $_POST['_project_package_subscription_type'] ) );
	}

	if ( isset( $_POST['_projects_limit'] ) ) {
		update_post_meta( $post_id, '_projects_limit', sanitize_text_field( $_POST['_projects_limit'] ) );
	}

	if ( isset( $_POST['_projects_duration'] ) ) {
		update_post_meta( $post_id, '_projects_duration', sanitize_text_field( $_POST['_projects_duration'] ) );
	}
}
add_action( 'woocommerce_process_product_meta_project_package', 'wp_freeio_wc_paid_listings_save_project_package_option_field'  );
add_action( 'woocommerce_process_product_meta_project_package_subscription', 'wp_freeio_wc_paid_listings_save_project_package_option_field'  );

/**
 * Save the Job Package custom fields.
 */
function wp_freeio_wc_paid_listings_save_package_option_field( $post_id ) {
	$urgent_jobs = isset( $_POST['_urgent_jobs'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_urgent_jobs', $urgent_jobs );
	
	$feature_jobs = isset( $_POST['_feature_jobs'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_feature_jobs', $feature_jobs );
	
	if ( isset( $_POST['_job_package_subscription_type'] ) ) {
		update_post_meta( $post_id, '_job_package_subscription_type', sanitize_text_field( $_POST['_job_package_subscription_type'] ) );
	}

	if ( isset( $_POST['_jobs_limit'] ) ) {
		update_post_meta( $post_id, '_jobs_limit', sanitize_text_field( $_POST['_jobs_limit'] ) );
	}

	if ( isset( $_POST['_jobs_duration'] ) ) {
		update_post_meta( $post_id, '_jobs_duration', sanitize_text_field( $_POST['_jobs_duration'] ) );
	}
}
add_action( 'woocommerce_process_product_meta_job_package', 'wp_freeio_wc_paid_listings_save_package_option_field'  );
add_action( 'woocommerce_process_product_meta_job_package_subscription', 'wp_freeio_wc_paid_listings_save_package_option_field'  );

/**
 * Save the CV Package custom fields.
 */
function wp_freeio_wc_paid_listings_save_cv_package_option_field( $post_id ) {
	if ( isset( $_POST['_cv_package_subscription_type'] ) ) {
		update_post_meta( $post_id, '_cv_package_subscription_type', sanitize_text_field( $_POST['_cv_package_subscription_type'] ) );
	}

	if ( isset( $_POST['_cv_package_expiry_time'] ) ) {
		update_post_meta( $post_id, '_cv_package_expiry_time', sanitize_text_field( $_POST['_cv_package_expiry_time'] ) );
	}

	if ( isset( $_POST['_cv_number_of_cv'] ) ) {
		update_post_meta( $post_id, '_cv_number_of_cv', sanitize_text_field( $_POST['_cv_number_of_cv'] ) );
	}
}
add_action( 'woocommerce_process_product_meta_cv_package', 'wp_freeio_wc_paid_listings_save_cv_package_option_field'  );
add_action( 'woocommerce_process_product_meta_cv_package_subscription', 'wp_freeio_wc_paid_listings_save_cv_package_option_field'  );

/**
 * Save the Contact Package custom fields.
 */
function wp_freeio_wc_paid_listings_save_contact_package_option_field( $post_id ) {
	if ( isset( $_POST['_contact_package_subscription_type'] ) ) {
		update_post_meta( $post_id, '_contact_package_subscription_type', sanitize_text_field( $_POST['_contact_package_subscription_type'] ) );
	}

	if ( isset( $_POST['_contact_package_expiry_time'] ) ) {
		update_post_meta( $post_id, '_contact_package_expiry_time', sanitize_text_field( $_POST['_contact_package_expiry_time'] ) );
	}

	if ( isset( $_POST['_contact_number_of_cv'] ) ) {
		update_post_meta( $post_id, '_contact_number_of_cv', sanitize_text_field( $_POST['_contact_number_of_cv'] ) );
	}
}
add_action( 'woocommerce_process_product_meta_contact_package', 'wp_freeio_wc_paid_listings_save_contact_package_option_field'  );
add_action( 'woocommerce_process_product_meta_contact_package_subscription', 'wp_freeio_wc_paid_listings_save_contact_package_option_field'  );

/**
 * Save the Freelancer Package custom fields.
 */
function wp_freeio_wc_paid_listings_save_freelancer_package_option_field( $post_id ) {
	if ( isset( $_POST['_freelancer_package_subscription_type'] ) ) {
		update_post_meta( $post_id, '_freelancer_package_subscription_type', sanitize_text_field( $_POST['_freelancer_package_subscription_type'] ) );
	}
	
	if ( isset( $_POST['_freelancer_package_expiry_time'] ) ) {
		update_post_meta( $post_id, '_freelancer_package_expiry_time', sanitize_text_field( $_POST['_freelancer_package_expiry_time'] ) );
	}
	
	if ( isset( $_POST['_freelancer_number_of_applications'] ) ) {
		update_post_meta( $post_id, '_freelancer_number_of_applications', sanitize_text_field( $_POST['_freelancer_number_of_applications'] ) );
	}
}
add_action( 'woocommerce_process_product_meta_freelancer_package', 'wp_freeio_wc_paid_listings_save_freelancer_package_option_field'  );
add_action( 'woocommerce_process_product_meta_freelancer_package_subscription', 'wp_freeio_wc_paid_listings_save_freelancer_package_option_field'  );

/**
 * Save the Resume Package custom fields.
 */
function wp_freeio_wc_paid_listings_save_resume_package_option_field( $post_id ) {
	// $urgent_resumes = isset( $_POST['_urgent_resumes'] ) ? 'yes' : 'no';
	// update_post_meta( $post_id, '_urgent_resumes', $urgent_resumes );
	
	$featured_resumes = isset( $_POST['_featured_resumes'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_featured_resumes', $featured_resumes );

	if ( isset( $_POST['_resume_package_subscription_type'] ) ) {
		update_post_meta( $post_id, '_resume_package_subscription_type', sanitize_text_field( $_POST['_resume_package_subscription_type'] ) );
	}
	
	if ( isset( $_POST['_resumes_duration'] ) ) {
		update_post_meta( $post_id, '_resumes_duration', sanitize_text_field( $_POST['_resumes_duration'] ) );
	}
}
add_action( 'woocommerce_process_product_meta_resume_package', 'wp_freeio_wc_paid_listings_save_resume_package_option_field'  );
add_action( 'woocommerce_process_product_meta_resume_package_subscription', 'wp_freeio_wc_paid_listings_save_resume_package_option_field'  );