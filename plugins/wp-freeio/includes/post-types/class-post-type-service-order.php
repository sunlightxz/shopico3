<?php
/**
 * Post Type: Service Order
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Service_Order {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );
	  	
	  	add_filter( 'manage_edit-service_order_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_service_order_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
	}

	public static function register_post_type() {
		$singular = __( 'Service Order', 'wp-freeio' );
		$plural   = __( 'Service Orders', 'wp-freeio' );

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
		
		register_post_type( 'service_order',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title' ),
				'public'            => true,
		        'has_archive'       => false,
		        'publicly_queryable' => false,
				'show_in_rest'		=> true,
				'show_in_menu'		=> 'edit.php?post_type=service',
				'capabilities' => array(
				    'create_posts' => false,
				),
				'map_meta_cap' => true,
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
			'name'              => __( 'Service ID', 'wp-freeio' ),
			'id'                => WP_FREEIO_SERVICE_ORDER_PREFIX . 'service_id',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Order ID', 'wp-freeio' ),
			'id'                => WP_FREEIO_SERVICE_ORDER_PREFIX . 'order_id',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Freelancer ID', 'wp-freeio' ),
			'id'                => WP_FREEIO_SERVICE_ORDER_PREFIX . 'freelancer_id',
			'type'              => 'text',
		);

		$metaboxes[ WP_FREEIO_SERVICE_ORDER_PREFIX . 'general' ] = array(
			'id'                        => WP_FREEIO_SERVICE_ORDER_PREFIX . 'general',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( 'service_order' ),
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
			'status' 			=> __( 'Status', 'wp-freeio' ),
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
				$service_id = get_post_meta(get_the_ID(), WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);

                $meta_obj = WP_Freeio_Service_Meta::get_instance($service_id);

				if ( $meta_obj->check_post_meta_exist('price') ) {
					
					$service_price = $meta_obj->get_post_meta( 'price' );

					if ( !empty($service_addons) ) {
						foreach ($service_addons as $addon_id) {
							$addon_price = get_post_meta($addon_id, WP_FREEIO_SERVICE_ADDON_PREFIX.'price', true);
							$service_price += $addon_price;
						}
					}
	                echo WP_Freeio_Price::format_price($service_price);
	            }
			break;
			case 'status':
				global $post;
				$post_status = get_post_status_object( $post->post_status );
				?>
				<span class="badge">
					<?php
					if ( !empty($post_status->label) ) {
						echo esc_html($post_status->label);
					} else {
						echo esc_html($post_status->post_status);
					}
					?>
				</span>
				<?php
			break;
		}
	}
}
WP_Freeio_Post_Type_Service_Order::init();