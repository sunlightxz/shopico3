<?php
/**
 * Post Type: Earning
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Earnings {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );
	  	
	  	add_filter( 'manage_edit-earning_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_earning_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) , 10 );
	}

	public static function admin_menu() {
		//Settings
	 	
		add_submenu_page('freelancer-settings', __( 'Earnings', 'wp-freeio' ), __( 'Earnings', 'wp-freeio' ), 'manage_options', 'edit.php?post_type=earning');
	}

	public static function register_post_type() {
		$singular = __( 'Earning', 'wp-freeio' );
		$plural   = __( 'Earnings', 'wp-freeio' );

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
		
		register_post_type( 'earning',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title' ),
				'public'            => true,
		        'has_archive'       => false,
				'show_in_rest'		=> true,
				'capabilities' => array(
				    'create_posts' => false,
				),
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
			'name'              => __( 'Order ID', 'wp-freeio' ),
			'id'                => WP_FREEIO_EARNING_PREFIX . 'order_id',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Project Type', 'wp-freeio' ),
			'id'                => WP_FREEIO_EARNING_PREFIX . 'project_type',
			'type'              => 'select',
			'options' => array(
				'' => '',
				'service' => esc_html__('Service', 'wp-freeio'),
				'project' => esc_html__('Project', 'wp-freeio'),
			)
		);

		$metaboxes[ WP_FREEIO_EARNING_PREFIX . 'general' ] = array(
			'id'                        => WP_FREEIO_EARNING_PREFIX . 'general',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( 'earning' ),
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
			'price' 			=> __( 'Price', 'wp-freeio' ),
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
			case 'price':
				$price = get_post_meta( get_the_ID(), WP_FREEIO_EARNING_PREFIX . 'freelancer_amount', true );
				echo WP_Freeio_Price::format_price($price);
			break;
		}
	}
}
WP_Freeio_Post_Type_Earnings::init();