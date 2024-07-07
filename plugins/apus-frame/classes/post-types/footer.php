<?php
/**
 * Footer manager for apus frame
 *
 * @package    apus-frame
 * @author     Team Apusthemes <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  2015-2016 Apus Frame
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class Apus_Frame_PostType_Footer {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => __( 'Footer Builder', 'apus-frame' ),
			'singular_name'         => __( 'Footer', 'apus-frame' ),
			'add_new'               => __( 'Add New Footer', 'apus-frame' ),
			'add_new_item'          => __( 'Add New Footer', 'apus-frame' ),
			'edit_item'             => __( 'Edit Footer', 'apus-frame' ),
			'new_item'              => __( 'New Footer', 'apus-frame' ),
			'all_items'             => __( 'All Footers', 'apus-frame' ),
			'view_item'             => __( 'View Footer', 'apus-frame' ),
			'search_items'          => __( 'Search Footer', 'apus-frame' ),
			'not_found'             => __( 'No Footers found', 'apus-frame' ),
			'not_found_in_trash'    => __( 'No Footers found in Trash', 'apus-frame' ),
			'parent_item_colon'     => '',
			'menu_name'             => __( 'Footers Builder', 'apus-frame' ),
	    );

	    register_post_type( 'apus_footer',
	      	array(
		        'labels'            => apply_filters( 'apus_postype_footer_labels' , $labels ),
		        'supports'          => array( 'title', 'editor' ),
		        'public'            => true,
		        'has_archive'       => false,
		        'show_in_nav_menus' => false,
		        'menu_position'     => 52,
		        'menu_icon'         => 'dashicons-admin-post',
	      	)
	    );

  	}
  
}

Apus_Frame_PostType_Footer::init();