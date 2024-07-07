<?php
/**
 * Custom Header functionality for Freeio
 *
 * @package WordPress
 * @subpackage Freeio
 * @since Freeio 1.0
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses freeio_header_style()
 */
function freeio_custom_header_setup() {
	$color_scheme        = freeio_get_color_scheme();
	$default_text_color  = trim( $color_scheme[4], '#' );

	/**
	 * Filter Freeio custom-header support arguments.
	 *
	 * @since Freeio 1.0
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type string $default_text_color     Default color of the header text.
	 *     @type int    $width                  Width in pixels of the custom header image. Default 954.
	 *     @type int    $height                 Height in pixels of the custom header image. Default 1300.
	 *     @type string $wp-head-callback       Callback function used to styles the header image and text
	 *                                          displayed on the blog.
	 * }
	 */
	add_theme_support( 'custom-header', apply_filters( 'freeio_custom_header_args', array(
		'default-text-color'     => $default_text_color,
		'width'                  => 954,
		'height'                 => 1300,
		'wp-head-callback'       => 'freeio_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'freeio_custom_header_setup' );

if ( ! function_exists( 'freeio_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog.
 *
 * @since Freeio 1.0
 *
 * @see freeio_custom_header_setup()
 */
function freeio_header_style() {
	return '';
}
endif; // freeio_header_style

