<?php
/**
 * Post Type: Withdraw
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Withdraw {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );
	  	
	  	add_filter( 'manage_edit-withdraw_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_withdraw_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) , 10 );
	}

	public static function admin_menu() {
		//Settings
	 	
		add_submenu_page('freelancer-settings', __( 'Withdraws', 'wp-freeio' ), __( 'Withdraws', 'wp-freeio' ), 'manage_options', 'edit.php?post_type=withdraw');
	}

	public static function register_post_type() {
		$singular = __( 'Withdraw', 'wp-freeio' );
		$plural   = __( 'Withdraws', 'wp-freeio' );

		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new'               => sprintf(__( 'Add New %s', 'wp-freeio' ), $singular),
			'add_new_item'          => sprintf(__( 'Add New %s', 'wp-freeio' ), $singular),
			'edit_item'             => sprintf(__( 'Edit %s', 'wp-freeio' ), $singular),
			'new_item'              => sprintf(__( 'New %s', 'wp-freeio' ), $singular),
			'all_items'             => $plural,
			'view_item'             => sprintf(__( 'View %s', 'wp-freeio' ), $singular),
			'search_items'          => sprintf(__( 'Search %s', 'wp-freeio' ), $singular),
			'not_found'             => sprintf(__( 'No %s found', 'wp-freeio' ), $plural),
			'not_found_in_trash'    => sprintf(__( 'No %s found in Trash', 'wp-freeio' ), $plural),
			'parent_item_colon'     => '',
			'menu_name'             => $plural,
		);
		
		register_post_type( 'withdraw',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title' ),
				'public'            => true,
		        'has_archive'       => false,
				'show_in_rest'		=> true,
				'capabilities' => array(
				    'create_posts' => false,
				),
				'map_meta_cap' => true,

				'show_in_menu'		=> false,
				'map_meta_cap' => true,
				'query_var' => false,
				'publicly_queryable'  => false
			)
		);
	}

	/**
	 * Defines custom fields
	 *
	 * @access public
	 * @param array $metaboxes
	 * @return array
	 */
	public static function fields( array $metaboxes ) {
		
		$fields = array();
		
		$fields[] = array(
			'name'              => __( 'Withdraw Amount', 'wp-freeio' ),
			'id'                => WP_FREEIO_WITHDRAW_PREFIX . 'amount',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Payout Details', 'wp-freeio' ),
			'id'                => WP_FREEIO_WITHDRAW_PREFIX . 'payout_d',
			'type'              => 'wpfr_payout_details',
		);

		$metaboxes[ WP_FREEIO_WITHDRAW_PREFIX . 'general' ] = array(
			'id'                        => WP_FREEIO_WITHDRAW_PREFIX . 'general',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( 'withdraw' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => $fields
		);
		return $metaboxes;
	}
	
	/**
	 * Custom admin columns for post type
	 *
	 * @access public
	 * @return array
	 */
	public static function custom_columns($columns) {
		if ( isset($columns['comments']) ) {
			unset($columns['comments']);
		}
		if ( isset($columns['date']) ) {
			unset($columns['date']);
		}
		$fields = array_merge($columns, array(
			'title' 			=> __( 'Title', 'wp-freeio' ),
			'amount' 			=> __( 'Amount', 'wp-freeio' ),
			'account_type' 			=> __( 'Account Type', 'wp-freeio' ),
			'date' 				=> esc_html__( 'Date', 'wp-freeio' ),
			'author' 			=> esc_html__( 'Author', 'wp-freeio' ),
		));
		return $fields;
	}

	/**
	 * Custom admin columns implementation
	 *
	 * @access public
	 * @param string $column
	 * @return array
	 */
	public static function custom_columns_manage( $column ) {
		switch ( $column ) {
			case 'amount':
				$price = get_post_meta( get_the_ID(), WP_FREEIO_WITHDRAW_PREFIX . 'amount', true );
				echo WP_Freeio_Price::format_price($price);
			break;
			case 'account_type':
				$all_payout_methods = WP_Freeio_Mixes::get_default_withdraw_payout_methods();
				$payout_method = get_post_meta( get_the_ID(), WP_FREEIO_WITHDRAW_PREFIX . 'payout_method', true );
				if ( !empty($all_payout_methods[$payout_method]) ) {
					echo esc_html($all_payout_methods[$payout_method]);
				}
				
			break;
		}
	}

	public static function sum_freelancer_withdraw( $status='publish', $user_id='' ) {
		global $current_user;
		$user_id	= !empty($user_id) ? $user_id : $current_user->ID;
		$args = array(
			'fields' 			=> 'ids',
			'post_status'		=> $status,
			'post_type'			=> 'withdraw',
			'author'			=>  $user_id,
			'posts_per_page' 	=> -1 
		);
		
		$posts = get_posts($args);
		$total_amount	= 0;
		
		if ( !empty($posts) ) {
			foreach($posts as $pos_id) {
				$price			= !empty($pos_id) ? get_post_meta( $pos_id, WP_FREEIO_WITHDRAW_PREFIX.'amount', true ) : 0;
				$price			= !empty($price) ? $price : 0;
				$total_amount	= $price + $total_amount;
			}
		}
		
		return $total_amount;
	}

	public static function sum_freelancer_earning( $status='publish', $user_id='' ) {
		global $current_user;
		$user_id	= !empty($user_id) ? $user_id : $current_user->ID;
		$args = array(
			'fields' 			=> 'ids',
			'post_status'		=> $status,
			'post_type'			=> 'earning',
			'author'			=>  $user_id,
			'posts_per_page' 	=> -1 
		);
		
		$posts = get_posts($args);
		$total_amount	= 0;
		
		if ( !empty($posts) ) {
			foreach($posts as $pos_id) {
				$price			= !empty($pos_id) ? get_post_meta( $pos_id, WP_FREEIO_EARNING_PREFIX.'freelancer_amount', true ) : 0;

				$price			= !empty($price) ? $price : 0;
				$total_amount	= $price + $total_amount;
			}
		}
		return $total_amount;
	}

	public static function get_freelancer_balance( $user_id ) {
		$total_withdraw_pending	= WP_Freeio_Post_Type_Withdraw::sum_freelancer_withdraw(array('publish', 'pending'));
		$total_withdraw_pending	= !empty($total_withdraw_pending) ? floatval($total_withdraw_pending) : 0;

		$totalamount = WP_Freeio_Post_Type_Withdraw::sum_freelancer_earning('publish', $user_id);
		
		$current_balance = 0;
		if (!empty($totalamount)) {
			$balance_remaining	= floatval($totalamount ) - floatval( $total_withdraw_pending );
			$current_balance    = !empty( $balance_remaining ) && $balance_remaining > 0  ? $balance_remaining : 0;
		}

		$total_earning_pending = WP_Freeio_Post_Type_Withdraw::sum_freelancer_earning('pending', $user_id);
		$return = array(
			'total_earning' => $totalamount,
			'total_earning_pending' => $total_earning_pending,
			'current_balance' => $current_balance,
			'withdrawn' => WP_Freeio_Post_Type_Withdraw::sum_freelancer_withdraw(array('publish')),
		);
		return apply_filters('wp-freeio-get-freelancer-balance', $return, $user_id);
	}
}
WP_Freeio_Post_Type_Withdraw::init();