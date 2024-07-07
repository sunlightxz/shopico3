<?php
/**
 * Tags
 *
 * @package    wp-freeio
 * @author     ApusTheme <apusthemes@gmail.com >
 * @license    GNU General Public License, version 3
 * @copyright  13/06/2016 ApusTheme
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WP_Freeio_Taxonomy_Freelancer_Tag{

	/**
	 *
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ), 1 );
	}

	/**
	 *
	 */
	public static function definition() {
		$singular = __( 'Tag', 'wp-freeio' );
		$plural   = __( 'Tags', 'wp-freeio' );

		$labels = array(
			'name'              => sprintf(__( 'Freelancer %s', 'wp-freeio' ), $plural),
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
		
		$rewrite_slug = get_option('wp_freeio_freelancer_tag_slug');
		if ( empty($rewrite_slug) ) {
			$rewrite_slug = _x( 'freelancer-tag', 'Freelancer tag slug - resave permalinks after changing this', 'wp-freeio' );
		}
		$rewrite = array(
			'slug'         => $rewrite_slug,
			'with_front'   => false,
			'hierarchical' => false,
		);
		register_taxonomy( 'freelancer_tag', 'freelancer', array(
			'labels'            => apply_filters( 'wp_freeio_taxomony_freelancer_tag_labels', $labels ),
			'hierarchical'      => false,
			'rewrite'           => $rewrite,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'		=> true
		) );
	}

}

WP_Freeio_Taxonomy_Freelancer_Tag::init();