<?php
/**
 * Apus Frame Plugin
 *
 * A simple, truly extensible and fully responsive options framework
 * for WordPress themes and plugins. Developed with WordPress coding
 * standards and PHP best practices in mind.
 *
 * Plugin Name:     Apus FrameWork
 * Plugin URI:      http://apusthemes.com
 * Description:     Apus framework for wordpress theme
 * Author:          Team ApusTheme
 * Author URI:      http://apusthemes.com
 * Version:         1.0
 * Text Domain:     apus-frame
 * License:         GPL3+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:     languages
 */

define( 'APUS_FRAME_VERSION', '1.0');
define( 'APUS_FRAME_URL', plugin_dir_url( __FILE__ ) );
define( 'APUS_FRAME_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Custom Post type
 *
 */
add_action( 'init', 'apus_frame_register_post_types', 1 );

function apus_frame_register_post_types() {
    $post_types = apply_filters( 'apus_frame_register_post_types', array('footer', 'header', 'megamenu') );
    if ( !empty($post_types) ) {
        foreach ($post_types as $post_type) {
            if ( file_exists( APUS_FRAME_DIR . 'classes/post-types/'.$post_type.'.php' ) ) {
                require APUS_FRAME_DIR . 'classes/post-types/'.$post_type.'.php';
            }
        }
    }
}

function apus_frame_image_srcset($size_array, $src, $image_meta, $attachment_id) {
    return wp_calculate_image_srcset( $size_array, $src, $image_meta, $attachment_id );
}


function apus_frame_load_textdomain() {

	$lang_dir = APUS_FRAME_DIR . 'languages/';
	$lang_dir = apply_filters( 'apus-frame_languages_directory', $lang_dir );

	// Traditional WordPress plugin locale filter
	$locale = apply_filters( 'plugin_locale', get_locale(), 'apus-frame' );
	$mofile = sprintf( '%1$s-%2$s.mo', 'apus-frame', $locale );

	// Setup paths to current locale file
	$mofile_local  = $lang_dir . $mofile;
	$mofile_global = WP_LANG_DIR . '/apus-frame/' . $mofile;

	if ( file_exists( $mofile_global ) ) {
		// Look in global /wp-content/languages/apus-frame folder
		load_textdomain( 'apus-frame', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) {
		// Look in local /wp-content/plugins/apus-frame/languages/ folder
		load_textdomain( 'apus-frame', $mofile_local );
	} else {
		// Load the default language files
		load_plugin_textdomain( 'apus-frame', false, $lang_dir );
	}
}

add_action( 'plugins_loaded', 'apus_frame_load_textdomain' );