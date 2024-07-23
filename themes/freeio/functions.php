<?php
/**
 * freeio functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Freeio
 * @since Freeio 1.3.3
 */

define( 'FREEIO_THEME_VERSION', '1.3.3' );
define( 'FREEIO_DEMO_MODE', false );

if ( ! isset( $content_width ) ) {
	$content_width = 660;
}

if ( ! function_exists( 'freeio_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Freeio 1.0
 */
function freeio_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on freeio, use a find and replace
	 * to change 'freeio' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'freeio', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	add_theme_support( 'post-thumbnails' );
	add_image_size( 'freeio-listing-grid', 495, 370, true );

	add_image_size( 'freeio-gallery', 950, 500, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'freeio' ),
		'mobile-primary' => esc_html__( 'Primary Mobile Menu', 'freeio' ),
		'employer-menu' => esc_html__( 'Employer Menu', 'freeio' ),
		'freelancer-menu' => esc_html__( 'Freelancer Menu', 'freeio' ),
		'employee-menu' => esc_html__( 'Employee Menu', 'freeio' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	add_theme_support( "woocommerce", array('gallery_thumbnail_image_width' => 410) );
	
	add_theme_support( 'wc-product-gallery-slider' );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );

	$color_scheme  = freeio_get_color_scheme();
	$default_color = trim( $color_scheme[0], '#' );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'freeio_custom_background_args', array(
		'default-color'      => $default_color,
		'default-attachment' => 'fixed',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for Block Styles.
	add_theme_support( 'wp-block-styles' );

	// Add support for full and wide align images.
	add_theme_support( 'align-wide' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Enqueue editor styles.
	add_editor_style('css/style-editor.css');

	freeio_get_load_plugins();
}
endif; // freeio_setup
add_action( 'after_setup_theme', 'freeio_setup' );

/**
 * Load Google Front
 */
function freeio_get_fonts_url() {
    $fonts_url = '';

    $main_font = freeio_get_config('main-font');
	$main_font = !empty($main_font) ? json_decode($main_font, true) : array();
	if (  !empty($main_font['fontfamily']) ) {
		$main_font_family = $main_font['fontfamily'];
		$main_font_weight = !empty($main_font['fontweight']) ? $main_font['fontweight'] : '400,500,700';
		$main_font_subsets = !empty($main_font['subsets']) ? $main_font['subsets'] : 'latin,latin-ext';
	} else {
		$main_font_family = 'DM+Sans';
		$main_font_weight = '400,500,700';
		$main_font_subsets = 'latin,latin-ext';
	}

	$heading_font = freeio_get_config('heading-font');
	$heading_font = !empty($heading_font) ? json_decode($heading_font, true) : array();
	if (  !empty($heading_font['fontfamily']) ) {
		$heading_font_family = $heading_font['fontfamily'];
		$heading_font_weight = !empty($heading_font['fontweight']) ? $heading_font['fontweight'] : '400,500,700';
		$heading_font_subsets = !empty($heading_font['subsets']) ? $heading_font['subsets'] : 'latin,latin-ext';
	} else {
		$heading_font_family = 'DM+Sans';
		$heading_font_weight = '400,500,700';
		$heading_font_subsets = 'latin,latin-ext';
	}

	if ( $main_font_family == $heading_font_family ) {
		$font_weight = $main_font_weight.','.$heading_font_weight;
		$font_subsets = $main_font_subsets.','.$heading_font_subsets;
		$fonts = array(
			$main_font_family => array(
				'weight' => $font_weight,
				'subsets' => $font_subsets,
			),
		);
	} else {
		$fonts = array(
			$main_font_family => array(
				'weight' => $main_font_weight,
				'subsets' => $main_font_subsets,
			),
			$heading_font_family => array(
				'weight' => $heading_font_weight,
				'subsets' => $heading_font_subsets,
			),
		);
	}

	$font_families = array();
	$subset = array();

	foreach ($fonts as $key => $opt) {
		$font_families[] = $key.':'.$opt['weight'];
		$subset[] = $opt['subsets'];
	}



    $query_args = array(
        'family' => implode( '|', $font_families ),
        'subset' => urlencode( implode( ',', $subset ) ),
    );
		
		$protocol = is_ssl() ? 'https:' : 'http:';
    $fonts_url = add_query_arg( $query_args, $protocol .'//fonts.googleapis.com/css' );
    
 
    return esc_url( $fonts_url );
}

/**
 * Enqueue styles.
 *
 * @since Freeio 1.0
 */
function freeio_enqueue_styles() {
	
	// load font
	wp_enqueue_style( 'freeio-theme-fonts', freeio_get_fonts_url(), array(), null );

	//load font awesome
	wp_enqueue_style( 'all-awesome', get_template_directory_uri() . '/css/all-awesome.css', array(), '5.11.2' );

	//load font flaticon
	wp_enqueue_style( 'flaticon', get_template_directory_uri() . '/css/flaticon.css', array(), '1.0.0' );

	// load font themify icon
	wp_enqueue_style( 'themify-icons', get_template_directory_uri() . '/css/themify-icons.css', array(), '1.0.0' );
			
	// load animate version 3.6.0
	wp_enqueue_style( 'animate', get_template_directory_uri() . '/css/animate.css', array(), '3.6.0' );

	// load bootstrap style
	if( is_rtl() ){
		wp_enqueue_style( 'bootstrap-rtl', get_template_directory_uri() . '/css/bootstrap.rtl.css', array(), '5.0.2' );
	} else {
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.css', array(), '5.0.2' );
	}
	// slick
	wp_enqueue_style( 'slick', get_template_directory_uri() . '/css/slick.css', array(), '1.8.0' );
	// magnific-popup
	wp_enqueue_style( 'magnific-popup', get_template_directory_uri() . '/css/magnific-popup.css', array(), '1.1.0' );
	// perfect scrollbar
	wp_enqueue_style( 'perfect-scrollbar', get_template_directory_uri() . '/css/perfect-scrollbar.css', array(), '0.6.12' );
	
	// mobile menu
	wp_enqueue_style( 'sliding-menu', get_template_directory_uri() . '/css/sliding-menu.min.css', array(), '0.3.0' );

	// main style
	if( is_rtl() ){
		wp_enqueue_style( 'freeio-template', get_template_directory_uri() . '/css/template.rtl.css', array(), '1.0' );
	} else {
		wp_enqueue_style( 'freeio-template', get_template_directory_uri() . '/css/template.css', array(), '1.0' );
	}
	
	$custom_style = freeio_custom_styles();
	if ( !empty($custom_style) ) {
		wp_add_inline_style( 'freeio-template', $custom_style );
	}
	wp_enqueue_style( 'freeio-style', get_template_directory_uri() . '/style.css', array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'freeio_enqueue_styles', 100 );

function freeio_admin_enqueue_styles() {

	// load font
	wp_enqueue_style( 'freeio-theme-fonts', freeio_get_fonts_url(), array(), null );

	//load font awesome
	wp_enqueue_style( 'all-awesome', get_template_directory_uri() . '/css/all-awesome.css', array(), '5.11.2' );

	//load font flaticon
	wp_enqueue_style( 'flaticon', get_template_directory_uri() . '/css/flaticon.css', array(), '1.0.0' );

	// load font themify icon
	wp_enqueue_style( 'themify-icons', get_template_directory_uri() . '/css/themify-icons.css', array(), '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'freeio_admin_enqueue_styles', 100 );

function freeio_login_enqueue_styles() {
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.css', array(), '4.5.0' );
	wp_enqueue_style( 'freeio-login-style', get_template_directory_uri() . '/css/login-style.css', array(), '1.0' );
}
add_action( 'login_enqueue_scripts', 'freeio_login_enqueue_styles', 10 );
/**
 * Enqueue scripts.
 *
 * @since Freeio 1.0
 */
function freeio_enqueue_scripts() {
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	// bootstrap
	wp_enqueue_script( 'bootstrap-bundle', get_template_directory_uri() . '/js/bootstrap.bundle.min.js', array( 'jquery' ), '5.0.2', true );
	// slick
	wp_enqueue_script( 'slick', get_template_directory_uri() . '/js/slick.min.js', array( 'jquery' ), '1.8.0', true );
	// countdown
	wp_register_script( 'countdown', get_template_directory_uri() . '/js/countdown.js', array( 'jquery' ), '20150315', true );
	wp_localize_script( 'countdown', 'freeio_countdown_opts', array(
		'days' => esc_html__('Days', 'freeio'),
		'hours' => esc_html__('Hrs', 'freeio'),
		'mins' => esc_html__('Mins', 'freeio'),
		'secs' => esc_html__('Secs', 'freeio'),
	));
	
	// popup
	wp_enqueue_script( 'jquery-magnific-popup', get_template_directory_uri() . '/js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
	// unviel
	wp_enqueue_script( 'jquery-unveil', get_template_directory_uri() . '/js/jquery.unveil.js', array( 'jquery' ), '1.1.0', true );
	
	// perfect scrollbar
	wp_enqueue_script( 'perfect-scrollbar', get_template_directory_uri() . '/js/perfect-scrollbar.jquery.min.js', array( 'jquery' ), '1.5.0', true );
	
	if ( freeio_get_config('keep_header') ) {
		wp_enqueue_script( 'sticky', get_template_directory_uri() . '/js/sticky.min.js', array( 'jquery', 'elementor-waypoints' ), '4.0.1', true );
	}

	// mobile menu script
	wp_enqueue_script( 'sliding-menu', get_template_directory_uri() . '/js/sliding-menu.min.js', array( 'jquery' ), '0.3.0', true );

	// main script
	wp_register_script( 'freeio-functions', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150330', true );
	wp_localize_script( 'freeio-functions', 'freeio_opts', array(
		'ajaxurl' => esc_url(admin_url( 'admin-ajax.php' )),
		'previous' => esc_html__('Previous', 'freeio'),
		'next' => esc_html__('Next', 'freeio'),
		'menu_back_text' => esc_html__('Back', 'freeio')
	));
	wp_enqueue_script( 'freeio-functions' );
	
	wp_add_inline_script( 'freeio-functions', "(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);" );
}
add_action( 'wp_enqueue_scripts', 'freeio_enqueue_scripts', 1 );

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @since Freeio 1.0
 *
 * @param string $html Search form HTML.
 * @return string Modified search form HTML.
 */
function freeio_search_form_modify( $html ) {
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'freeio_search_form_modify' );


function freeio_get_config($name, $default = '') {
	global $freeio_theme_options;
	
	if ( empty($freeio_theme_options) ) {
		$freeio_theme_options = get_option('freeio_theme_options');
	}

    if ( isset($freeio_theme_options[$name]) ) {
        return $freeio_theme_options[$name];
    }
    return $default;
}

function freeio_set_exporter_ocdi_settings_option_keys($option_keys) {
	return array(
		'freeio_theme_options',
		'elementor_disable_color_schemes',
		'elementor_disable_typography_schemes',
		'elementor_allow_tracking',
		'elementor_cpt_support',
		'wp_freeio_settings',
		'wp_freeio__job__fields_data',
		'wp_freeio__employer__fields_data',
		'wp_freeio__freelancer__fields_data',
		'wp_freeio__service__fields_data',
		'wp_freeio__project__fields_data',
	);
}
add_filter( 'apus_exporter_ocdi_settings_option_keys', 'freeio_set_exporter_ocdi_settings_option_keys' );

function freeio_disable_one_click_import() {
	return false;
}
add_filter('apus_frammework_enable_one_click_import', 'freeio_disable_one_click_import');

function freeio_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Default', 'freeio' ),
		'id'            => 'sidebar-default',
		'description'   => esc_html__( 'Add widgets here to appear in your Sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	
	register_sidebar( array(
		'name'          => esc_html__( 'Services filter sidebar', 'freeio' ),
		'id'            => 'services-filter-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Service single sidebar', 'freeio' ),
		'id'            => 'service-single-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Projects filter sidebar', 'freeio' ),
		'id'            => 'projects-filter-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );
	
	register_sidebar( array(
		'name'          => esc_html__( 'Project single sidebar', 'freeio' ),
		'id'            => 'project-single-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Jobs filter sidebar', 'freeio' ),
		'id'            => 'jobs-filter-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Freelancers filter sidebar', 'freeio' ),
		'id'            => 'freelancers-filter-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Freelancer single sidebar', 'freeio' ),
		'id'            => 'freelancer-single-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Employers filter sidebar', 'freeio' ),
		'id'            => 'employers-filter-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Employer single sidebar', 'freeio' ),
		'id'            => 'employer-single-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'User Profile sidebar', 'freeio' ),
		'id'            => 'user-profile-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Blog sidebar', 'freeio' ),
		'id'            => 'blog-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Shop sidebar', 'freeio' ),
		'id'            => 'shop-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'freeio' ),
		'before_widget' => '<aside class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title"><span>',
		'after_title'   => '</span></h2>',
	) );

}
add_action( 'widgets_init', 'freeio_widgets_init' );

function freeio_get_load_plugins() {
	$plugins[] = array(
		'name'                     => esc_html__( 'Apus Framework For Themes', 'freeio' ),
        'slug'                     => 'apus-frame',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/apus-frame.zip'
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'Revolution Slider', 'freeio' ),
        'slug'                     => 'revslider',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/revslider.zip'
	);
	
	$plugins[] = array(
		'name'                     => esc_html__( 'Elementor Page Builder', 'freeio' ),
	    'slug'                     => 'elementor',
	    'required'                 => true,
	);
	
	$plugins[] = array(
		'name'                     => esc_html__( 'Cmb2', 'freeio' ),
	    'slug'                     => 'cmb2',
	    'required'                 => true,
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'MailChimp for WordPress', 'freeio' ),
	    'slug'                     => 'mailchimp-for-wp',
	    'required'                 =>  true
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'Contact Form 7', 'freeio' ),
	    'slug'                     => 'contact-form-7',
	    'required'                 => true,
	);

	// woocommerce plugins
	$plugins[] = array(
		'name'                     => esc_html__( 'Woocommerce', 'freeio' ),
	    'slug'                     => 'woocommerce',
	    'required'                 => true,
	);
	
	// Listing plugins
	$plugins[] = array(
		'name'                     => esc_html__( 'WP Freeio', 'freeio' ),
        'slug'                     => 'wp-freeio',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/wp-freeio.zip'
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'WP Freeio - WooCommerce Paid Listings', 'freeio' ),
        'slug'                     => 'wp-freeio-wc-paid-listings',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/wp-freeio-wc-paid-listings.zip'
	);

	$plugins[] = array(
		'name'                     => esc_html__( 'WP Private Message', 'freeio' ),
        'slug'                     => 'wp-private-message',
        'required'                 => true ,
        'source'				   => get_template_directory() . '/inc/plugins/wp-private-message.zip'
	);
	
	$plugins[] = array(
        'name'                  => esc_html__( 'One Click Demo Import', 'freeio' ),
        'slug'                  => 'one-click-demo-import',
        'required'              => false,
    );

	$plugins[] = array(
        'name'                  => esc_html__( 'SVG Support', 'freeio' ),
        'slug'                  => 'svg-support',
        'required'              => false,
        'force_activation'      => false,
        'force_deactivation'    => false,
    );

	tgmpa( $plugins );
}

require get_template_directory() . '/inc/plugins/class-tgm-plugin-activation.php';
get_template_part( '/inc/functions-helper' );
get_template_part( '/inc/functions-frontend' );

/**
 * Implement the Custom Header feature.
 *
 */
get_template_part( '/inc/custom-header' );
get_template_part( '/inc/classes/megamenu' );
get_template_part( '/inc/classes/mobilemenu' );

/**
 * Custom template tags for this theme.
 *
 */
get_template_part( '/inc/template-tags' );

/**
 * Customizer additions.
 *
 */
get_template_part( '/inc/customizer/font/custom-controls' );
get_template_part( '/inc/customizer/customizer-custom-control' );
get_template_part( '/inc/customizer/customizer' );


if( freeio_is_cmb2_activated() ) {
	get_template_part( '/inc/vendors/cmb2/page' );
}

if( freeio_is_woocommerce_activated() ) {
	get_template_part( '/inc/vendors/woocommerce/functions' );
	get_template_part( '/inc/vendors/woocommerce/customizer' );
}

if( freeio_is_freeio_activated() ) {
	get_template_part( '/inc/vendors/wp-freeio/customizer-project' );
	get_template_part( '/inc/vendors/wp-freeio/customizer-service' );
	get_template_part( '/inc/vendors/wp-freeio/customizer-job' );
	get_template_part( '/inc/vendors/wp-freeio/customizer-employer' );
	get_template_part( '/inc/vendors/wp-freeio/customizer-freelancer' );
	get_template_part( '/inc/vendors/wp-freeio/functions' );

	get_template_part( '/inc/vendors/wp-freeio/functions-job-display' );
	get_template_part( '/inc/vendors/wp-freeio/functions-employer' );
	get_template_part( '/inc/vendors/wp-freeio/functions-employer-display' );
	get_template_part( '/inc/vendors/wp-freeio/functions-freelancer' );
	get_template_part( '/inc/vendors/wp-freeio/functions-freelancer-display' );
	get_template_part( '/inc/vendors/wp-freeio/functions-service' );
	get_template_part( '/inc/vendors/wp-freeio/functions-service-display' );
	get_template_part( '/inc/vendors/wp-freeio/functions-project' );
	get_template_part( '/inc/vendors/wp-freeio/functions-project-display' );
}

if ( freeio_is_freeio_wc_paid_listings_activated() ) {
	get_template_part( '/inc/vendors/wp-freeio-wc-paid-listings/functions' );
}

function freeio_register_load_widget() {
	get_template_part( '/inc/widgets/custom_menu' );
	get_template_part( '/inc/widgets/recent_post' );
	get_template_part( '/inc/widgets/search' );
	
	get_template_part( '/inc/widgets/elementor-template' );
	
	if ( freeio_is_freeio_activated() ) {
		
		get_template_part( '/inc/widgets/service-price' );
		get_template_part( '/inc/widgets/service-author' );
		get_template_part( '/inc/widgets/service-tags' );
		get_template_part( '/inc/widgets/project-price' );
		get_template_part( '/inc/widgets/project-author' );
		get_template_part( '/inc/widgets/freelancer-info' );
		get_template_part( '/inc/widgets/freelancer-tags' );
		get_template_part( '/inc/widgets/employer-info' );

		get_template_part( '/inc/widgets/user-short-profile' );
		
	}
}
add_action( 'widgets_init', 'freeio_register_load_widget' );

if ( freeio_is_wp_private_message() ) {
	get_template_part( '/inc/vendors/wp-private-message/functions' );
}

get_template_part( '/inc/vendors/elementor/functions' );
get_template_part( '/inc/vendors/one-click-demo-import/functions' );


/**
 * Custom Styles
 *
 */
get_template_part( '/inc/custom-styles' );
// functions.php

// functions.php
// Handle saving phone and address on user registration
function handle_freelancer_registration( $user_id ) {
    if ( isset($_POST['phone']) ) {
        update_user_meta($user_id, 'phone', sanitize_text_field($_POST['phone']));
    }

    if ( isset($_POST['address']) ) {
        update_user_meta($user_id, 'address', sanitize_text_field($_POST['address']));
    }
}
add_action('user_register', 'handle_freelancer_registration');

// Log current user data to console on user login
add_action('wp_loaded', 'log_current_user_data');
function log_current_user_data() {
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $phone = get_user_meta($current_user->ID, 'phone', true);
        $address = get_user_meta($current_user->ID, 'address', true);
        $role_title = '';
        // Get the freelancer ID associated with the user
        $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($current_user->ID);

        if ($freelancer_id) {
            // Get the title (name) of the freelancer
            $user_name = get_the_title($freelancer_id);
        } else {
            $user_name = ''; // Handle if freelancer ID is not found
        }

        $user_data = array(
            'ID' => $current_user->ID,
            'Username' => $current_user->user_login,
            'Name' => $user_name,
            'Email' => $current_user->user_email,
            'Phone' => $phone,
            'Address' => $address,
        );

        // Output to browser console
        echo '<script>';
        echo 'console.log(' . json_encode($user_data) . ');';
        echo '</script>';
    }
}

// Add phone number and address fields to user profile
// function custom_register_user_meta_fields() {
//     // Phone Number
//     register_meta('user', 'phone', array(
//         'type' => 'string',
//         'description' => 'User Phone Number',
//         'single' => true,
//         'show_in_rest' => true, // Allows REST API access
//     ));

//     // Address
//     register_meta('user', 'address', array(
//         'type' => 'string',
//         'description' => 'User Address',
//         'single' => true,
//         'show_in_rest' => true, // Allows REST API access
//     ));
// }
// add_action('init', 'custom_register_user_meta_fields');
// // Add this code to your theme's functions.php file


// Enqueue scripts and styles
function enqueue_custom_scripts() {
    // Enqueue custom.js file
    wp_enqueue_script('custom-script', get_template_directory_uri() . '/custom.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');


//dasch Icons import
function enqueue_dashicons() {
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'enqueue_dashicons');


// // Generate a serial number for freelance (driver) users
// function generate_driver_serial_number() {
//     $random_number = rand(100000, 999999); // Generate a random 6-digit number
//     return 'Driver' . $random_number;
// }


// // Modify your registration logic
// add_action('user_register', 'custom_user_registration', 10, 1);
// function get_or_create_driver_serial_number($user_id) {
//     $serial_number = get_user_meta($user_id, 'driver_serial_number', true);
//     if (empty($serial_number)) {
//         $serial_number = generate_driver_serial_number();
//         update_user_meta($user_id, 'driver_serial_number', $serial_number);
//     }
//     return $serial_number;
// }
// =======
// Add this in your theme's functions.php or custom plugin file






