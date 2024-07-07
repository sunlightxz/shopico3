<?php
/**
 * CMB2 File
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_CMB2_Field_Datepicker2 {

	public static function init() {
		add_filter( 'cmb2_render_wpfi_datepicker2', array( __CLASS__, 'render_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wpfi_datepicker2', array( __CLASS__, 'sanitize_map' ), 10, 4 );
	}

	/**
	 * Render field
	 */
	public static function render_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		
		self::setup_scripts();
		
		echo $field_type_object->input( array(
			'type'       => 'text',
			'name'       => $field->args( '_name' ),
			'id'       => $field->args( '_name' ),
			'class'      => 'wpfi-datepicker2',
			'desc'       => '',
			'placeholder' => $field->args( 'attributes', 'placeholder' ) ? $field->args( 'attributes', 'placeholder' ) : '',
		) );
	}

	public static function sanitize_map( $override_value, $value, $object_id, $field_args ) {
		return $value;
	}

	public static function setup_scripts() {
		wp_enqueue_script( 'wp-freeio-datepicker-script', plugins_url( 'js/script.js', __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), '1.0' );
		
		$datepicker_date_format = str_replace(
			array(
				'd',
				'j',
				'l',
				'z', // Day.
				'F',
				'M',
				'n',
				'm', // Month.
				'Y',
				'y', // Year.
			),
			array(
				'dd',
				'd',
				'DD',
				'o',
				'MM',
				'M',
				'm',
				'mm',
				'yy',
				'y',
			),
			get_option( 'date_format' )
		);
		wp_localize_script( 'wp-freeio-datepicker-script', 'wp_freeio_datepicker', array(
			'date_format' => $datepicker_date_format,
		));
	}

}

WP_Freeio_CMB2_Field_Datepicker2::init();