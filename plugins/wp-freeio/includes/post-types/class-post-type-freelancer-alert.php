<?php
/**
 * Post Type: Freelancer Alert
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Freelancer_Alert {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );

	  	add_filter( 'manage_edit-freelancer_alert_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_freelancer_alert_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
	}

	public static function register_post_type() {

		$singular = __( 'Freelancer Alert', 'wp-freeio' );
		$plural   = __( 'Freelancer Alerts', 'wp-freeio' );

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

		register_post_type( 'freelancer_alert',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title' ),
				'public'            => true,
		        'has_archive'       => false,
		        'publicly_queryable' => false,
				'show_in_rest'		=> true,
				'show_in_menu'		=> 'edit.php?post_type=freelancer',
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
		$email_frequency_default = WP_Freeio_Job_Alert::get_email_frequency();
		$email_frequency = array();
		if ( $email_frequency_default && is_admin() ) {
			foreach ($email_frequency_default as $key => $value) {
				if ( !empty($value['label']) && !empty($value['days']) ) {
					$email_frequency[$key] = $value['label'];
				}
			}
		}
		$fields = array();
		if ( isset($_GET['post']) && $_GET['post'] && is_admin() ) {
			$post = get_post($_GET['post']);
			if ( $post && $post->post_type == 'freelancer_alert' ) {
				$author_name = get_the_author_meta('display_name', $post->post_author);
				$author_email = get_the_author_meta('user_email', $post->post_author);
				$fields[] = array(
					'name' => sprintf( __('Author: %s (%s)', 'wp-freeio'), $author_name, $author_email ),
					'type' => 'title',
					'id'   => WP_FREEIO_FREELANCER_ALERT_PREFIX . 'author'
				);
			}
		}
		$fields[] = array(
			'name'              => __( 'Alert Query', 'wp-freeio' ),
			'id'                => WP_FREEIO_FREELANCER_ALERT_PREFIX . 'alert_query',
			'type'              => 'textarea',
		);
		$fields[] = array(
			'name'              => __( 'Email Frequency', 'wp-freeio' ),
			'id'                => WP_FREEIO_FREELANCER_ALERT_PREFIX . 'email_frequency',
			'type'              => 'select',
			'options'			=> $email_frequency
		);
		$metaboxes[ WP_FREEIO_FREELANCER_ALERT_PREFIX . 'general' ] = array(
			'id'                        => WP_FREEIO_FREELANCER_ALERT_PREFIX . 'general',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( 'freelancer_alert' ),
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
			'email_frequency' 	=> __( 'Email Frequency', 'wp-freeio' ),
			'date' 				=> esc_html__( 'Date', 'wp-freeio' ),
			'auhtor' 			=> esc_html__( 'Auhtor', 'wp-freeio' ),
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
			case 'email_frequency':
					$email_frequency = get_post_meta( get_the_ID(), WP_FREEIO_FREELANCER_ALERT_PREFIX . 'email_frequency', true );
					echo wp_kses_post($email_frequency);
				break;
		}
	}

}
WP_Freeio_Post_Type_Freelancer_Alert::init();