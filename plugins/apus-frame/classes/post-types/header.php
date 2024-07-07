<?php
/**
 * Header manager for apus frame
 *
 * @package    apus-frame
 * @author     Team Apusthemes <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Apus Frame
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Apus_Frame_PostType_Header {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => __( 'Header Builder', 'apus-frame' ),
			'singular_name'         => __( 'Header', 'apus-frame' ),
			'add_new'               => __( 'Add New Header', 'apus-frame' ),
			'add_new_item'          => __( 'Add New Header', 'apus-frame' ),
			'edit_item'             => __( 'Edit Header', 'apus-frame' ),
			'new_item'              => __( 'New Header', 'apus-frame' ),
			'all_items'             => __( 'All Headers', 'apus-frame' ),
			'view_item'             => __( 'View Header', 'apus-frame' ),
			'search_items'          => __( 'Search Header', 'apus-frame' ),
			'not_found'             => __( 'No Headers found', 'apus-frame' ),
			'not_found_in_trash'    => __( 'No Headers found in Trash', 'apus-frame' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Headers Builder', 'apus-frame' ),
	    );

	    register_post_type( 'apus_header',
	      	array(
		        'labels'            => apply_filters( 'apus_postype_header_labels' , $labels ),
		        'supports'          => array( 'title', 'editor' ),
		        'public'            => true,
		        'has_archive'       => false,
		        'show_in_nav_menus' => false,
		        'menu_position'     => 51,
		        'menu_icon'         => 'dashicons-admin-post',
	      	)
	    );

  	}
  
}

Apus_Frame_PostType_Header::init();