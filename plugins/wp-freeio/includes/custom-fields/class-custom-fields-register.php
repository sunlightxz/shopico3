<?php
/**
 * Custom Fields Register
 *
 * @package    wp-freeio
 * @author     Habq
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WP_Freeio_Custom_Fields_Register {
	
	public static function init() {
        add_filter('wp-freeio-register-employer-fields', array(__CLASS__, 'employer_fields_display'));
		add_action('wp-freeio-register-freelancer-fields', array(__CLASS__, 'freelancer_fields_display'));
	}

	public static function employer_fields_display($fields) {
        $prefix = WP_FREEIO_EMPLOYER_PREFIX;
        $custom_fields = WP_Freeio_Custom_Fields::get_register_custom_fields(array(), $prefix);
        
        return array_merge($fields, $custom_fields);
	}

    public static function freelancer_fields_display($fields) {
        $prefix = WP_FREEIO_FREELANCER_PREFIX;
        
        $custom_fields = WP_Freeio_Custom_Fields::get_register_custom_fields(array(), $prefix);
        
        return array_merge($fields, $custom_fields);
    }

}

WP_Freeio_Custom_Fields_Register::init();