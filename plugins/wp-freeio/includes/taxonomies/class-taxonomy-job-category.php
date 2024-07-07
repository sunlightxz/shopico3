<?php
/**
 * Categories
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WP_Freeio_Taxonomy_Job_Category{

	/**
	 *
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ), 1 );
	}

	/**
	 *
	 */
	public static function definition($args) {
		$singular = __( 'Category', 'wp-freeio' );
		$plural   = __( 'Categories', 'wp-freeio' );

		$labels = array(
			'name'              => sprintf(__( 'Job %s', 'wp-freeio' ), $plural),
			'singular_name'     => $singular,
			'search_items'      => sprintf(__( 'Search %s', 'wp-freeio' ), $plural),
			'all_items'         => sprintf(__( 'All %s', 'wp-freeio' ), $plural),
			'parent_item'       => sprintf(__( 'Parent %s', 'wp-freeio' ), $singular),
			'parent_item_colon' => sprintf(__( 'Parent %s:', 'wp-freeio' ), $singular),
			'edit_item'         => __( 'Edit', 'wp-freeio' ),
			'update_item'       => __( 'Update', 'wp-freeio' ),
			'add_new_item'      => __( 'Add New', 'wp-freeio' ),
			'new_item_name'     => sprintf(__( 'New %s', 'wp-freeio' ), $singular),
			'menu_name'         => $plural,
		);

		$rewrite_slug = get_option('wp_freeio_job_category_slug');
		if ( empty($rewrite_slug) ) {
			$rewrite_slug = _x( 'job-category', 'Job category slug - resave permalinks after changing this', 'wp-freeio' );
		}
		$rewrite = array(
			'slug'         => $rewrite_slug,
			'with_front'   => false,
			'hierarchical' => false,
		);
		register_taxonomy( 'job_listing_category', 'job_listing', array(
			'labels'            => apply_filters( 'wp_freeio_taxomony_job_category_labels', $labels ),
			'hierarchical'      => true,
			'rewrite'           => $rewrite,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'		=> true
		) );
	}

}

WP_Freeio_Taxonomy_Job_Category::init();