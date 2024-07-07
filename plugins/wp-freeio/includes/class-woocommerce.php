<?php
/**
 * Service
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_WooCommerce {
	
	public static function init() {
		add_action( 'woocommerce_payment_complete',  array(__CLASS__, 'order_paid') );
		add_action( 'woocommerce_order_status_processing', array( __CLASS__, 'order_paid' ) );
		add_action( 'woocommerce_order_status_completed', array( __CLASS__, 'order_paid' ) );

		add_action( 'woocommerce_order_status_cancelled', array( __CLASS__, 'order_cancelled' ) );
		add_action( 'woocommerce_order_status_refunded', array( __CLASS__, 'order_cancelled' ) );


		add_action( 'woocommerce_before_calculate_totals', array(__CLASS__, 'apply_custom_price_to_cart_item'), 99 );

		add_action( 'woocommerce_new_order_item', array(__CLASS__, 'convert_item_session_to_order_meta'),  1, 3 );
		add_action( 'woocommerce_cart_calculate_fees', array(__CLASS__, 'cart_calculate_fees'),  1, 3 );

		add_action( 'woocommerce_thankyou', array(__CLASS__, 'display_order_data'), 20 );
		add_action( 'woocommerce_view_order', array(__CLASS__, 'display_order_data'), 20 );

		add_filter( 'woocommerce_after_order_itemmeta', array(__CLASS__, 'woo_order_meta'), 10, 3 );

		add_filter( 'woocommerce_display_item_meta', array(__CLASS__, 'woocommerce_display_item_meta'), 10, 3 );

		add_filter( 'woocommerce_order_item_permalink', array(__CLASS__, 'woocommerce_order_item_permalink'), 10, 3 );


		add_filter( 'woocommerce_price_format', array(__CLASS__, 'get_woocommerce_price_format'), 10 );
	}

	public static function order_paid( $order_id ) {
		$order = wc_get_order( $order_id );
        $user = $order->get_user();
		if ( get_post_meta( $order_id, 'wp_freeio_order_processed', true ) ) {
			return;
		}

		foreach ( $order->get_items() as $key => $item ) {

			if ($user) {
				$order_item_id = $item->get_id();
				$payment_type = wc_get_order_item_meta( $order_item_id, 'payment_type', true );
				
				if( !empty( $payment_type ) && $payment_type == 'hiring' ) {
					self::update_hiring_data( $order_id );
					
				} elseif ( !empty( $payment_type )  && $payment_type == 'hiring_service') {
					self::update_hiring_service_data( $order_id, $user->ID );
				}
            }
		}
		
		update_post_meta( $order_id, 'wp_freeio_order_processed', true );
	}

	public static function order_cancelled( $order_id ) {
		$order = wc_get_order( $order_id );
        $user = $order->get_user();

		foreach ( $order->get_items() as $key => $item ) {

			if ($user) {
				$order_item_id = $item->get_id();
				$payment_type = wc_get_order_item_meta( $order_item_id, 'payment_type', true );
				
				if( !empty( $payment_type ) && $payment_type == 'hiring' ) {
					self::cancelled_hiring_data( $order_id );
					
				} elseif ( !empty( $payment_type )  && $payment_type == 'hiring_service') {
					self::cancelled_hiring_service_data( $order_id, $user->ID );
				}
            }
		}
		
		update_post_meta( $order_id, 'wp_freeio_order_cancelled', true );
	}

    public static function update_hiring_service_data( $order_id, $user_id ) {
        
		$current_date 	= current_time('mysql');
		$gmt_time		= current_time( 'mysql', 1 );

		$order 		= new WC_Order( $order_id );
		$items 		= $order->get_items();
		$earning_meta	= array();
		
		if( !empty( $items ) ) {
			$counter	= 0;
			foreach( $items as $key => $order_item ){
				$order_item_id = $order_item->get_id();
				$counter++;
				$order_detail = wc_get_order_item_meta( $order_item_id, 'cus_woo_product_data', true );
				$earning_meta['freelancer_amount'] = wc_get_order_item_meta( $order_item_id, 'freelancer_shares', true );
				$earning_meta['admin_amount'] = wc_get_order_item_meta( $order_item_id, 'admin_shares', true );
				$earning_meta['amount'] = $order_detail['price'];
				
			}
			
			$earning_meta['order_id'] = $order_id;
			$earning_meta['project_type'] = 'service';
			$earning_meta['status'] = 'hired';
			
			$earning_meta['currency_symbol']	= WP_Freeio_Price::currency_symbol();
			
			if( !empty($order_detail['service_id']) ) {
				$addons				= !empty( $order_detail['addons'] ) ? $order_detail['addons'] : array();
				$service_package_content = !empty( $order_detail['service_package_content'] ) ? $order_detail['service_package_content'] : array();
				$freelancer_user_id	= WP_Freeio_Service::get_author_id($order_detail['service_id'] );
				$service_title		= get_the_title( $order_detail['service_id'] );
				$service_link		= get_the_permalink( $order_detail['service_id'] );
				
				$order_post = array(
					'post_title'    => wp_strip_all_tags( $service_title ).' #'.$order_id,
					'post_status'   => 'hired',
					'post_author'   => $user_id,
					'post_type'     => 'service_order',
				);

				$service_order_id = wp_insert_post( $order_post );
				
				if ( !empty( $service_order_id ) ) {
					update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id',$order_detail['service_id']);
					update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_title', esc_attr( $service_title ));
					update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author', $freelancer_user_id);
					update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'order_id',$order_id);
					update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'amount',$earning_meta['amount']);
					update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'currency_symbol',$currency_symbol);
					update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'addons',$addons);
					update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_package_content', $service_package_content);
					update_post_meta( $order_id, '_hiring_id', $service_order_id );
					
					//update order meta 
					update_post_meta( $order_id, 'freelancer_id', $freelancer_user_id );
				}
				
				$earning_meta['user_id'] = $freelancer_user_id;
				$earning_meta['project_id'] = $service_order_id;
				$earning_meta = apply_filters('wp-freeio-update-hiring-service-data-earning-meta', $earning_meta, $order_id, $user_id);

				$earning_post = array(
					'post_title'    => wp_strip_all_tags( $service_title ).' #'.$order_id,
					'post_status'   => 'pending',
					'post_author'   => $freelancer_user_id,
					'post_type'     => 'earning',
				);
				$earning_post_id    = wp_insert_post( $earning_post );
				if( !empty( $earning_post_id ) ) {

					foreach ($earning_meta as $key => $value) {
						update_post_meta($earning_post_id, WP_FREEIO_EARNING_PREFIX.$key, $value);
					}
					update_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'earning_id', $earning_post_id);
				}
				
				$service_id	=  $order_detail['service_id'];
				$service = get_post($service_id);

				//Send email to users
				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
	     		$employer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
	     		$employer = get_post($employer_id);
	     		$freelancer = get_post($freelancer_id);
				$email_from = get_option( 'admin_email', false );

				$email_vars = array();
				$email_vars['amount'] = !empty($earning_meta['amount']) ? WP_Freeio_Price::format_price($earning_meta['amount']) : 0;
				$email_vars['freelancer'] = $freelancer;
				$email_vars['employer'] = $employer;
				$email_vars['service'] = $service;
				if ( wp_freeio_get_option('freelancer_notice_add_hired_service') ) {
					$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
					
					$email_to = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
					if ( empty($email_to) ) {
						$email_to = get_the_author_meta( 'user_email', $freelancer_user_id );
					}
					
					$subject = WP_Freeio_Email::render_email_vars($email_vars, 'hired_service_notice', 'subject');
					$content = WP_Freeio_Email::render_email_vars($email_vars, 'hired_service_notice', 'content');
					
					WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				}

				if ( wp_freeio_get_option('employer_notice_add_hired_service') ) {
					$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
					
					$email_to = get_post_meta( $freelancer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
					if ( empty($email_to) ) {
						$email_to = get_the_author_meta( 'user_email', $user_id );
					}
					
					$subject = WP_Freeio_Email::render_email_vars($email_vars, 'hired_service_employer_notice', 'subject');
					$content = WP_Freeio_Email::render_email_vars($email_vars, 'hired_service_employer_notice', 'content');
					
					WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				}

				$notify_args = array(
					'post_type' => 'freelancer',
					'user_post_id' => $freelancer_id,
		            'type' => 'hired_service',
		            'service_id' => $service_id,
		            'employer_user_id' => $user_id,
		            'service_order_id' => $service_order_id,
		            'service_package_content' => $service_package_content,
				);
				WP_Freeio_User_Notification::add_notification($notify_args);
			}
		}
    }

    public static function update_hiring_data( $order_id ) {
		
		$current_date 	= current_time('mysql');
		$gmt_time		= current_time( 'mysql', 1 );
		
		$order 		= new WC_Order( $order_id );
		$items 		= $order->get_items();
		$earning_meta	= array();
		
		if( !empty( $items ) ) {
			$counter	= 0;
			foreach( $items as $key => $order_item ){
				$order_item_id = $order_item->get_id();
				$counter++;
				$order_detail = wc_get_order_item_meta( $order_item_id, 'cus_woo_product_data', true );
				$earning_meta['freelancer_amount'] = wc_get_order_item_meta( $order_item_id, 'freelancer_shares', true );
				$earning_meta['admin_amount'] = wc_get_order_item_meta( $order_item_id, 'admin_shares', true );
				
				$earning_meta['user_id'] = get_post_field('post_author', $order_detail['proposal_id']);
				$earning_meta['amount'] = !empty( $order_detail['price'] ) ? $order_detail['price'] : '';
				$earning_meta['project_id']	= !empty( $order_detail['project_id'] ) ? $order_detail['project_id'] : '';
			}
			
			$earning_meta['order_id'] = $order_id;
			$earning_meta['status'] = 'hired';
			
			$earning_meta['currency_symbol'] = WP_Freeio_Price::currency_symbol();
			
			
			if( !empty($earning_meta['project_id']) && !empty($order_detail['proposal_id']) ) {
				self::hired_freelancer_after_payment( $earning_meta['project_id'],$order_detail['proposal_id']);

				$earning_post = array(
					'post_title' => wp_strip_all_tags( get_the_title($earning_meta['project_id']) ).' #'.$order_id,
					'post_status' => 'pending',
					'post_author' => get_post_field ('post_author', $order_detail['proposal_id']),
					'post_type' => 'earning',
				);
				$earning_post_id    = wp_insert_post( $earning_post );
				if( !empty( $earning_post_id ) ) {
					foreach ($earning_meta as $key => $value) {
						update_post_meta($earning_post_id, WP_FREEIO_EARNING_PREFIX.$key, $value);
					}

					update_post_meta( $order_detail['proposal_id'],  WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'earning_id', $earning_post_id );
				}

				$project_id	= !empty( $earning_meta['project_id'] ) ? $earning_meta['project_id'] : '';
				$employer_user_id = WP_Freeio_Project::get_author_id($project_id);
				$freelancer_user_id = get_post_field ('post_author', $order_detail['proposal_id']);
				
				update_post_meta( $order_id, '_hiring_id', $order_detail['proposal_id'] );
				update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'order_id', $order_id );
				update_post_meta( $order_detail['proposal_id'],  WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'order_id', $order_id );
				
				//update order meta 
				update_post_meta( $order_id, 'freelancer_id', $freelancer_user_id );
				
				//Send email to users
				$project = get_post($project_id);
				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
	     		$employer_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);
	     		$employer = get_post($employer_id);
	     		$freelancer = get_post($freelancer_id);
				$email_from = get_option( 'admin_email', false );


				$email_vars = array();
				$email_vars['amount'] = !empty($earning_meta['amount']) ? WP_Freeio_Price::format_price($earning_meta['amount']) : 0;
				$email_vars['freelancer'] = $freelancer;
				$email_vars['employer'] = $employer;
				$email_vars['project'] = $project;
				if ( wp_freeio_get_option('freelancer_notice_add_hired_proposal') ) {
					$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

					$email_to = get_post_meta( $freelancer_id, WP_FREEIO_FREELANCER_PREFIX.'email', true);
					if ( empty($email_to) ) {
						$email_to = get_the_author_meta( 'user_email', $freelancer_user_id );
					}

					$subject = WP_Freeio_Email::render_email_vars($email_vars, 'hired_proposal_notice', 'subject');
					$content = WP_Freeio_Email::render_email_vars($email_vars, 'hired_proposal_notice', 'content');
					
					WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				}

				if ( wp_freeio_get_option('employer_notice_add_hired_proposal') ) {
					$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );

					$email_to = get_post_meta( $employer_id, WP_FREEIO_EMPLOYER_PREFIX.'email', true);
					if ( empty($email_to) ) {
						$email_to = get_the_author_meta( 'user_email', $employer_user_id );
					}

					$subject = WP_Freeio_Email::render_email_vars($email_vars, 'hired_proposal_employer_notice', 'subject');
					$content = WP_Freeio_Email::render_email_vars($email_vars, 'hired_proposal_employer_notice', 'content');
					
					WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				}

				$notify_args = array(
					'post_type' => 'freelancer',
					'user_post_id' => $freelancer_id,
		            'type' => 'hired_proposal',
		            'proposal_id' => $proposal_id,
		            'employer_user_id' => $employer_user_id,
		            'project_id' => $project_id,
				);
				WP_Freeio_User_Notification::add_notification($notify_args);
			}
		}
    }

    public static function hired_freelancer_after_payment( $project_id, $proposal_id ) {
		global $current_user;
		
		update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'proposal_id', $proposal_id );
		$project_data = array(
			'ID' => $project_id,
			'post_status' => 'hired',
		);
		wp_update_post( $project_data );

		// proposal
		$proposal_data = array(
			'ID' => $proposal_id,
			'post_status' => 'hired',
		);
		wp_update_post( $proposal_data );

		$hired_freelance_id = get_post_field('post_author', $proposal_id);
		update_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'freelancer_id', $hired_freelance_id );
	}

	public static function cancelled_hiring_data($order_id) {

		$proposal_id = get_post_meta( $order_id, '_hiring_id', true );
		$project_id = get_post_meta( $proposal_id,  WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true );
		$earning_id = get_post_meta( $proposal_id,  WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'earning_id', true );
		$earning_data = array(
			'ID'            => $earning_id,
			'post_status'   => 'cancelled',
		);
		wp_update_post( $earning_data );

		delete_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'order_id' );
		delete_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'proposal_id' );
		delete_post_meta( $project_id, WP_FREEIO_PROJECT_PREFIX.'freelancer_id' );

		delete_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'order_id' );

		$project_data = array(
			'ID'            => $project_id,
			'post_status'   => 'publish',
		);
		wp_update_post( $project_data );

		// proposal
		$proposal_data = array(
			'ID'            => $proposal_id,
			'post_status'   => 'publish',
		);
		wp_update_post( $proposal_data );

		$freelancer_user_id = get_post_field ('post_author', $order_detail['proposal_id']);
		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
		$employer_user_id = WP_Freeio_Project::get_author_id($project_id);
		$notify_args = array(
			'post_type' => 'freelancer',
			'user_post_id' => $freelancer_id,
            'type' => 'cancelled_hired_proposal',
            'proposal_id' => $proposal_id,
            'employer_user_id' => $employer_user_id,
            'project_id' => $project_id,
		);
		WP_Freeio_User_Notification::add_notification($notify_args);
	}

	public static function cancelled_hiring_service_data($order_id, $user_id) {

		$service_order_id = get_post_meta( $order_id, '_hiring_id', true );
		$earning_id = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'earning_id', true);

		$service_order_data = array(
			'ID'            => $service_order_id,
			'post_status'   => 'cancelled',
		);
		wp_update_post( $service_order_data );
		
		// earning
		$earning_data = array(
			'ID'            => $earning_id,
			'post_status'   => 'cancelled',
		);
		wp_update_post( $earning_data );

		$freelancer_user_id = get_post_field ('post_author', $order_detail['proposal_id']);
		$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
		$service_id = get_post_meta($service_order_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);

		$notify_args = array(
			'post_type' => 'freelancer',
			'user_post_id' => $freelancer_id,
            'type' => 'cancelled_hired_service',
            'service_id' => $service_id,
            'employer_user_id' => $user_id,
            'service_order_id' => $service_order_id,
		);
		WP_Freeio_User_Notification::add_notification($notify_args);
	}

	public static function apply_custom_price_to_cart_item( $cart_object ) {  
		if( !WC()->session->__isset( "reload_checkout" )) {
			foreach ( $cart_object->cart_contents as $key => $value ) {
				$product 		= $value['data'];
				$product_id		= !empty($value['product_id']) ? $value['product_id'] : 0;
				$original_name  = !empty($product->get_name()) ?  $product->get_name() : '';
				$original_name  = !empty($original_name) && !empty($product_id) ?  get_the_title($product_id) : $original_name;

				if( !empty( $value['payment_type'] ) && $value['payment_type'] == 'hiring' ){
					if( isset( $value['cart_data']['price'] ) ){
						$bk_price = floatval( $value['cart_data']['price'] );
						$value['data']->set_price($bk_price);
					}

					$new_name 	= !empty($value['cart_data']['project_id']) ? get_the_title($value['cart_data']['project_id']) : $original_name;
				} elseif( !empty( $value['payment_type'] ) && $value['payment_type'] == 'hiring_service' ){
					if( isset( $value['cart_data']['price'] ) ){
						$bk_price = floatval( $value['cart_data']['price'] );
						$value['data']->set_price($bk_price);
					}

					$new_name 	= !empty($value['cart_data']['service_id']) ? get_the_title($value['cart_data']['service_id']) : $original_name;
				}

				if( !empty($new_name) && method_exists( $product, 'set_name' ) ){
					$product->set_name( $new_name );
				}
			}   
		}
	}

	public static function convert_item_session_to_order_meta( $item_id, $item, $order_id ) {
		if ( !empty( $item->legacy_values['cart_data'] ) ) {
			wc_add_order_item_meta( $item_id, 'cus_woo_product_data', $item->legacy_values['cart_data'] );
			update_post_meta( $order_id, 'cus_woo_product_data', $item->legacy_values['cart_data'] );
		}
		
		if ( !empty( $item->legacy_values['payment_type'] ) ) {
			wc_add_order_item_meta( $item_id, 'payment_type', $item->legacy_values['payment_type'] );
			update_post_meta( $order_id, 'payment_type', $item->legacy_values['payment_type'] );
		}
		
		if ( !empty( $item->legacy_values['admin_shares'] ) ) {
			wc_add_order_item_meta( $item_id, 'admin_shares', $item->legacy_values['admin_shares'] );
			update_post_meta( $order_id, 'admin_shares', $item->legacy_values['admin_shares'] );
		}
		
		if ( !empty( $item->legacy_values['freelancer_shares'] ) ) {
			wc_add_order_item_meta( $item_id, 'freelancer_shares', $item->legacy_values['freelancer_shares'] );
			update_post_meta( $order_id, 'freelancer_shares', $item->legacy_values['freelancer_shares'] );
		}
		
		if ( !empty( $item->legacy_values['employer_id'] ) ) {
			wc_add_order_item_meta( $item_id, 'employer_id', $item->legacy_values['employer_id'] );
			update_post_meta( $order_id, 'employer_id', $item->legacy_values['employer_id'] );
		}
		
		if ( !empty( $item->legacy_values['freelancer_id'] ) ) {
			wc_add_order_item_meta( $item_id, 'freelancer_id', $item->legacy_values['freelancer_id'] );
			update_post_meta( $order_id, 'freelancer_id', $item->legacy_values['freelancer_id'] );
		}
		
		if ( !empty( $item->legacy_values['current_project'] ) ) {
			wc_add_order_item_meta( $item_id, 'current_project', $item->legacy_values['current_project'] );
		}
	}

	public static function cart_calculate_fees( $cart_object ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ){
			return;
		}
		
		$item_count 	= 0;
		
		foreach( WC()->cart->get_cart() as $values ) {
			$item = $values['data'];
			if ( empty( $item ) ){
				break;
			}
			
			$fee	= !empty($values['cart_data']['processing_fee']) ? $values['cart_data']['processing_fee'] : 0.0;
			$item_id = $item->get_id();
			$item_count++;
		}
		
		if(!empty($fee)){
			$fee = $item_count *  $fee;
			WC()->cart->add_fee( esc_html__('Processing/taxes fee', 'wp-freeio'), $fee, false );
		}
	}

    public static function get_hiring_payment_title($key) {
		$hirings = self::get_hiring_payment();
		
		if( !empty( $hirings[$key] ) ){
			return $hirings[$key]['title'];
		} else{
			return '';
		}
	}

    public static function get_hiring_payment() {
		$hiring	= array(
			'project_id' => array('title' => esc_html__('Project title','wp-freeio')),
			'price' => array('title' => esc_html__('Amount','wp-freeio')),
			'proposal_id' => array('title' => esc_html__('Freelancer','wp-freeio')),
			'processing_fee' => array('title' => esc_html__('Processing/taxes fee','wp-freeio')),
		);
		
		return $hiring;
	}

	public static function get_hiring_value($val='',$key='') {
		
		if( !empty($key) && $key === 'project_id' ) {
			$val 			= esc_html( get_the_title( $val ) );
		} elseif( !empty($key) && $key === 'proposal_id' ) {
			$author_id	= get_post_field('post_author', $val);
			$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);
			
			$title = esc_html( get_the_title( intval($freelancer_id) ) );
			$permalink = esc_url( get_the_permalink( $freelancer_id ));
			$val = '<a href="'.esc_url($permalink).'" title="'.esc_attr($title).'" >'.esc_html($title).'</a>';
		} elseif( !empty($key) && $key === 'price' ) {
			$val = WP_Freeio_Price::format_price($val);
		} elseif( !empty($key) && $key === 'processing_fee' ) {
			$val = WP_Freeio_Price::format_price($val);
		}
		
		return $val;
	}

	public static function service_cart_attributes(){
		$list = array(
		    'service_id' => esc_html__('Service title', 'wp-freeio'),
			'delivery_time' => esc_html__('Delivery time', 'wp-freeio')
		);

		$list = apply_filters('wp-freeio-set-service-cart-attributes', $list);			
		return $list;
	}

	public static function get_service_attribute($key, $val) {
		$services = array();
		$delviery = array();

		$services = self::service_cart_attributes();

		$return	= array();
		
		if( !empty( $services[$key] ) ) {
			if( $key === 'service_id' ) {
				$return['title'] = $services[$key];
				$return['value'] = get_the_title($val);
			} elseif( $key === 'delivery_time' ) {
				$return['title'] = $services[$key];
				$return['value'] = $val;
			} else {
				$return['title'] = $services[$key];
				$return['value'] = $val;
			}
		} elseif( $key === 'addons') {
			if( !empty( $val ) ) {
				$title	= '';
				foreach( $val as $akey => $addon_id ){
					if (!empty($addon_id['price'])) {
						$price	= $addon_id['price'];
					} else {
						$price	= get_post_meta($akey, '_price', true);
					}
					
					$title	.= '<p>'.get_the_title($akey).' ('.WP_Freeio_Price::format_price($price).') </p>';
				}
				
				$return['title'] = esc_html__('Addons','wp-freeio');
				$return['value'] = $title;
			}
		} elseif( $key === 'service_price') {
			if( !empty( $val ) ) {
				
				$return['title'] = esc_html__('Service Price','wp-freeio');
				$return['value'] = WP_Freeio_Price::format_price($val);
			}
		} elseif( $key === 'processing_fee') {
			if( !empty( $val ) ) {
				
				$return['title'] = esc_html__('Processing/taxes fee','wp-freeio');
				$return['value'] = WP_Freeio_Price::format_price($val);
			}
		}
		return $return;
	}

	public static function display_order_data( $order_id ) {
		global $product,$woocommerce,$wpdb,$current_user;
		
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		if( !empty( $items ) ) {
			$counter = 0;
			foreach( $items as $key => $order_item ){
				$counter++;
				$payment_type = wc_get_order_item_meta( $key, 'payment_type', true );
				$order_detail = wc_get_order_item_meta( $key, 'cus_woo_product_data', true );
				$item_id = $order_item['product_id'];
				
				if( !empty($payment_type)  && $payment_type === 'hiring' ) {
					$order_item_name 	= self::get_hiring_value($order_detail['project_id'],'project_id');
				}
				
				$name		= !empty( $order_item_name ) ?  $order_item_name : $order_item['name'];
				$quantity	= !empty( $order_item['qty'] ) ?  $order_item['qty'] : 5;
				if( !empty( $order_detail ) ) {?>
					<div class="row">
						<div class="col-md-12">
							<div class="cart-data-wrap">
							  <h3><?php echo esc_html($name);?>( <span class="cus-quantity">×<?php echo esc_html( $quantity );?></span> )</h3>
							  <div class="selection-wrap">
								<?php 
									$counter	= 0;
									foreach( $order_detail as $key => $value ){
										$counter++;
										if( !empty($payment_type)  && $payment_type === 'hiring' ) {?>
											<div class="cart-style"> 
												<span class="title"><?php echo self::get_hiring_payment_title( $key );?></span> 
												<span class="value"><?php echo self::get_hiring_value( $value,$key );?></span> 
											</div>
										<?php } elseif( !empty($payment_type)  && $payment_type === 'hiring_service' ) {
											$attributes	= self::get_service_attribute($key,$value);
											if( !empty( $attributes ) ){
												?>
											<div class="cart-style"> 
												<span class="title"><?php echo esc_html($attributes['title']);?></span> 
												<span class="value"><?php echo do_shortcode($attributes['value']);?></span> 
											</div>
											<?php }?>
										<?php } ?>
									<?php }?>
							  </div>
							</div>
						 </div>
						<?php if( !empty( $current_user->ID ) ){
							$page_id = wp_freeio_get_option('user_dashboard_page_id');
							$page_id = WP_Freeio_Mixes::get_lang_post_id($page_id);
					 	?>
							 <div class="col-md-12">
								<a class="btn btn-theme" href="<?php echo get_permalink($page_id); ?>"><?php esc_html_e('Return to dashboard', 'wp-freeio');?></a>
							 </div>
						 <?php }?>	
					</div>
				<?php
				}
			}
		}
	}

	public static function woo_order_meta( $item_id, $item, $_product ) {
		global $product,$woocommerce,$wpdb;
		$order_detail = wc_get_order_item_meta( $item_id, 'cus_woo_product_data', true );
		
		$order_item 		= new WC_Order_Item_Product($item_id);
		$order				= $order_item->get_order();
		$order_status		= $order->get_status();
  		$customer_user 		= get_post_meta( $order->get_id(), '_customer_user', true );
		$payment_type 		= wc_get_order_item_meta( $item_id, 'payment_type', true );

		if( !empty( $order_detail ) ) {?>
			<div class="apus-order-details-wrap">
				
					<div class="inner">
						<h2 class="widget-title"><?php esc_html_e('Order Detail', 'wp-freeio');?></h2>
						<div class="content">
							<div class="order-details-inner">
								
								<?php 
								$counter	= 0;
								foreach( $order_detail as $key => $value ){
									$counter++;
									
									if( !empty($payment_type) && $payment_type === 'hiring') {?>
										<div class="cus-options-data">
											<span class="label"><?php echo self::get_hiring_payment_title($key);?>: </span>
											<span class="value"><?php echo self::get_hiring_value( $value, $key );?></span>
										</div>
									<?php } elseif( !empty($payment_type) && $payment_type === 'hiring_service') {
											$attributes	= self::get_service_attribute($key,$value);
											if( !empty( $attributes ) ){
											?>
											<div class="cus-options-data">
												<span class="label"><?php echo esc_html($attributes['title']);?>: </span>
												<span class="value"><?php echo do_shortcode($attributes['value']);?></span>
											</div>
										<?php }?>
									<?php }
									}
								?>
							</div>
						</div>

					</div>
			</div>
		<?php						
		}
	}

	public static function woocommerce_display_item_meta( $html, $item, $args ) {
		$item_id = $item->get_id();
		$payment_type = wc_get_order_item_meta( $item_id, 'payment_type', true );
		if ( $payment_type == 'hiring' || $payment_type == 'hiring_service' ) {
			return '';
		} else {
			return $html;
		}
	}

	public static function woocommerce_order_item_permalink($url, $item, $order) {
		if ( $url ) {
			$order_item_id = $item->get_id();
			$payment_type = wc_get_order_item_meta( $order_item_id, 'payment_type', true );
			if ( $payment_type == 'hiring_service' ) {
				$order_detail = wc_get_order_item_meta( $order_item_id, 'cus_woo_product_data', true );
				if( !empty($order_detail['service_id']) ) {
					$url = get_permalink($order_detail['service_id']);
				}
			} elseif( $payment_type == 'hiring' ) {
				$order_detail = wc_get_order_item_meta( $order_item_id, 'cus_woo_product_data', true );
				if( !empty($order_detail['project_id']) ) {
					$url = get_permalink($order_detail['project_id']);
				}
			}
		}
		return $url;
	}

	public static function get_woocommerce_price_format() {
		$currency_pos = get_option( 'woocommerce_currency_pos' );
		$format       = '%1$s%2$s';

		switch ( $currency_pos ) {
			case 'left':
				$format = '%1$s<span class="price-text">%2$s</span>';
				break;
			case 'right':
				$format = '<span class="price-text">%2$s</span>%1$s';
				break;
			case 'left_space':
				$format = '%1$s&nbsp;<span class="price-text">%2$s</span>';
				break;
			case 'right_space':
				$format = '<span class="price-text">%2$s</span>&nbsp;%1$s';
				break;
		}

		return $format;
	}
}
WP_Freeio_WooCommerce::init();