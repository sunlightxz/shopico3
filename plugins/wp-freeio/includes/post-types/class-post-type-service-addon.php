<?php
/**
 * Post Type: Service Addon
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Service_Addon {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );

	  	add_filter( 'manage_edit-service_addon_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_service_addon_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
	}

	public static function register_post_type() {
		$singular = __( 'Service Addon', 'wp-freeio' );
		$plural   = __( 'Service Addons', 'wp-freeio' );

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
		
		register_post_type( 'service_addon',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor', 'author' ),
				'public'            => true,
		        'has_archive'       => false,
		        'publicly_queryable' => false,
				'show_in_rest'		=> false,
				'show_in_menu'		=> 'edit.php?post_type=service',
				
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
		$freelancer_author_id = 0;
		if ( isset($_GET['post']) && $_GET['post'] && is_admin() ) {
			$post = get_post($_GET['post']);
			if ( $post && $post->post_type == 'service_addon' ) {
				$author_id = WP_Freeio_Service::get_author_id($post->ID);
				if ( WP_Freeio_User::is_freelancer($author_id) ) {
					$freelancer_author_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);
				}
				$author_name = get_the_author_meta('display_name', $author_id);
				if ( $freelancer_author_id ) {
					$author_name = get_the_title($freelancer_author_id);
					$author_email = WP_Freeio_Freelancer::get_post_meta($freelancer_author_id, 'email', true);
				}
				if ( empty($author_email) ) {
					$author_email = get_the_author_meta('user_email', $author_id);
				}
				$fields[] = array(
					'name' => sprintf( __('Author: %s (%s)', 'wp-freeio'), $author_name, $author_email ),
					'type' => 'title',
					'id'   => WP_FREEIO_SERVICE_ADDON_PREFIX . 'author'
				);
			}
		}
		$fields[] = array(
			'name'              => __( 'Addon Price', 'wp-freeio' ),
			'id'                => WP_FREEIO_SERVICE_ADDON_PREFIX . 'price',
			'type'              => 'text',
			'attributes'        => array(
                'type'              => 'number',
                'min'               => 0,
                'pattern'           => '\d*',
            ),
		);
		
		$metaboxes[ WP_FREEIO_SERVICE_ADDON_PREFIX . 'general' ] = array(
			'id'                        => WP_FREEIO_SERVICE_ADDON_PREFIX . 'general',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( 'service_addon' ),
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
				$price = get_post_meta( get_the_ID(), WP_FREEIO_SERVICE_ADDON_PREFIX . 'price', true );
				echo WP_Freeio_Price::format_price($price);
			break;
		}
	}

}
WP_Freeio_Post_Type_Service_Addon::init();