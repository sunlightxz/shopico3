<?php

if ( !function_exists('freeio_get_products') ) {
    function freeio_get_products( $args = array() ) {
        global $woocommerce, $wp_query;

        $args = wp_parse_args( $args, array(
            'categories' => array(),
            'product_type' => 'recent_product',
            'paged' => 1,
            'post_per_page' => -1,
            'orderby' => '',
            'order' => '',
            'includes' => array(),
            'excludes' => array(),
            'author' => '',
            'search' => '',
        ));
        extract($args);
        
        $query_args = array(
            'post_type' => 'product',
            'posts_per_page' => $post_per_page,
            'post_status' => 'publish',
            'paged' => $paged,
            'orderby'   => $orderby,
            'order' => $order
        );

        if ( isset( $query_args['orderby'] ) ) {
            if ( 'price' == $query_args['orderby'] ) {
                $query_args = array_merge( $query_args, array(
                    'meta_key'  => '_price',
                    'orderby'   => 'meta_value_num'
                ) );
            }
            if ( 'featured' == $query_args['orderby'] ) {
                $query_args = array_merge( $query_args, array(
                    'meta_key'  => '_featured',
                    'orderby'   => 'meta_value'
                ) );
            }
            if ( 'sku' == $query_args['orderby'] ) {
                $query_args = array_merge( $query_args, array(
                    'meta_key'  => '_sku',
                    'orderby'   => 'meta_value'
                ) );
            }
        }
        switch ($product_type) {
            case 'service_package':
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => array( 'service_package', 'service_package_subscription' )
                );
                break;
            case 'project_package':
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => array( 'project_package', 'project_package_subscription' )
                );
                break;
            case 'job_package':
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => array( 'job_package', 'job_package_subscription' )
                );
                break;
            case 'cv_package':
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => array( 'cv_package', 'cv_package_subscription' )
                );
                break;
            case 'contact_package':
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => array( 'contact_package', 'contact_package_subscription' )
                );
                break;
            case 'freelancer_package':
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => array( 'freelancer_package', 'freelancer_package_subscription' )
                );
                break;
            case 'resume_package':
                $query_args['tax_query'][] = array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => array( 'resume_package', 'resume_package_subscription' )
                );
            break;
        }

        if ( !empty($categories) && is_array($categories) ) {
            $query_args['tax_query'][] = array(
                'taxonomy'      => 'product_cat',
                'field'         => 'slug',
                'terms'         => implode(",", $categories ),
                'operator'      => 'IN'
            );
        }

        if (!empty($includes) && is_array($includes)) {
            $query_args['post__in'] = $includes;
        }
        
        if ( !empty($excludes) && is_array($excludes) ) {
            $query_args['post__not_in'] = $excludes;
        }

        if ( !empty($author) ) {
            $query_args['author'] = $author;
        }

        if ( !empty($search) ) {
            $query_args['search'] = "*{$search}*";
        }

        return new WP_Query($query_args);
    }
}


// hooks
function freeio_woocommerce_enqueue_styles() {
    if( is_rtl() ){
        wp_enqueue_style( 'freeio-woocommerce-rtl', get_template_directory_uri() .'/css/woocommerce.rtl.css' , 'freeio-woocommerce-front' , '1.0.0', 'all' );
    } else {
        wp_enqueue_style( 'freeio-woocommerce', get_template_directory_uri() .'/css/woocommerce.css' , 'freeio-woocommerce-front' , '1.0.0', 'all' );
    }
}
add_action( 'wp_enqueue_scripts', 'freeio_woocommerce_enqueue_styles', 99 );

function freeio_woocommerce_enqueue_scripts() {
    wp_register_script( 'freeio-woocommerce', get_template_directory_uri() . '/js/woocommerce.js', array( 'jquery', 'slick' ), '20150330', true );

    $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : site_url();
    $options = array(
        'ajaxurl' => esc_url(admin_url( 'admin-ajax.php' )),
        'view_more_text' => esc_html__('View More', 'freeio'),
        'view_less_text' => esc_html__('View Less', 'freeio'),
    );
    wp_localize_script( 'freeio-woocommerce', 'freeio_woo_options', $options );
    wp_enqueue_script( 'freeio-woocommerce' );
}
add_action( 'wp_enqueue_scripts', 'freeio_woocommerce_enqueue_scripts', 10 );

// cart
if ( !function_exists('freeio_woocommerce_header_add_to_cart_fragment') ) {
    function freeio_woocommerce_header_add_to_cart_fragment( $fragments ){
        global $woocommerce;
        $fragments['.cart .count'] =  ' <span class="count"> '. $woocommerce->cart->cart_contents_count .' </span> ';
        $fragments['.footer-mini-cart .count'] =  ' <span class="count"> '. $woocommerce->cart->cart_contents_count .' </span> ';
        return $fragments;
    }
}
add_filter('woocommerce_add_to_cart_fragments', 'freeio_woocommerce_header_add_to_cart_fragment' );

// breadcrumb for woocommerce page
if ( !function_exists('freeio_woocommerce_breadcrumb_defaults') ) {
    function freeio_woocommerce_breadcrumb_defaults( $args ) {
        $breadcrumb_img = freeio_get_config('woo_breadcrumb_image');
        $breadcrumb_color = freeio_get_config('woo_breadcrumb_color');
        $style = array();
        $has_bg = '';
        $show_breadcrumbs = freeio_get_config('show_product_breadcrumbs',1);
        
        if ( !$show_breadcrumbs ) {
            $style[] = 'display:none';
        }
        if( $breadcrumb_color  ){
            $style[] = 'background-color:'.$breadcrumb_color;
        }
        if ( !empty($breadcrumb_img) ) {
            $style[] = 'background-image:url(\''.esc_url($breadcrumb_img).'\')';
            $has_bg = 1;
        }

        $estyle = !empty($style)? ' style="'.implode(";", $style).'"':"";

        $full_width = apply_filters('freeio_woocommerce_content_class', 'container');
        $has_bg = $has_bg ? ' has_bg' :'';
        // check woo
        if(is_product()){
            $title = '';
        }else{
            $title = '<div class="breadscrumb-inner"><h2 class="bread-title">'.esc_html__( 'Shop', 'freeio' ).'</h2></div>';
        }

        $args['wrap_before'] = '<section id="apus-breadscrumb" class="apus-breadscrumb '.$has_bg.'"'.$estyle.'><div class="'.$full_width.'"><div class="wrapper-breads"><div class="wrapper-breads-inner">'.$title.'<ol class="apus-woocommerce-breadcrumb breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>';
        $args['wrap_after'] = '</ol></div></div></div></section>';

        return $args;
    }
}
add_filter( 'woocommerce_breadcrumb_defaults', 'freeio_woocommerce_breadcrumb_defaults' );
add_action( 'freeio_woo_template_main_before', 'woocommerce_breadcrumb', 30, 0 );

// display woocommerce modes
if ( !function_exists('freeio_woocommerce_display_modes') ) {
    function freeio_woocommerce_display_modes(){
        global $wp;
        $current_url = freeio_shop_page_link(true);

        $url_grid = add_query_arg( 'display_mode', 'grid', remove_query_arg( 'display_mode', $current_url ) );
        $url_list = add_query_arg( 'display_mode', 'list', remove_query_arg( 'display_mode', $current_url ) );

        $woo_mode = freeio_woocommerce_get_display_mode();

        echo '<div class="display-mode">';
        echo '<a href="'.  $url_grid  .'" class=" change-view '.($woo_mode == 'grid' ? 'active' : '').'"><i class="ti-layout-grid3"></i></a>';
        echo '<a href="'.  $url_list  .'" class=" change-view '.($woo_mode == 'list' ? 'active' : '').'"><i class="ti-view-list-alt"></i></a>';
        echo '</div>'; 
    }
}

if ( !function_exists('freeio_woocommerce_get_display_mode') ) {
    function freeio_woocommerce_get_display_mode() {
        $woo_mode = freeio_get_config('product_display_mode', 'grid');
        $args = array( 'grid', 'list' );
        if ( isset($_COOKIE['freeio_woo_mode']) && in_array($_COOKIE['freeio_woo_mode'], $args) ) {
            $woo_mode = $_COOKIE['freeio_woo_mode'];
        }
        return $woo_mode;
    }
}

if(!function_exists('freeio_shop_page_link')) {
    function freeio_shop_page_link($keep_query = false ) {
        if ( defined( 'SHOP_IS_ON_FRONT' ) ) {
            $link = home_url();
        } elseif ( is_post_type_archive( 'product' ) || is_page( wc_get_page_id('shop') ) ) {
            $link = get_post_type_archive_link( 'product' );
        } else {
            $link = get_term_link( get_query_var('term'), get_query_var('taxonomy') );
        }

        if( $keep_query ) {
            // Keep query string vars intact
            foreach ( $_GET as $key => $val ) {
                if ( 'orderby' === $key || 'submit' === $key ) {
                    continue;
                }
                $link = add_query_arg( $key, $val, $link );

            }
        }
        return $link;
    }
}


if(!function_exists('freeio_filter_before')){
    function freeio_filter_before(){
        echo '<div class="wrapper-fillter"><div class="apus-filter d-flex align-items-center">';
    }
}
if(!function_exists('freeio_filter_after')){
    function freeio_filter_after(){
        echo '</div></div>';
    }
}
add_action( 'woocommerce_before_shop_loop', 'freeio_filter_before' , 1 );
add_action( 'woocommerce_before_shop_loop', 'freeio_filter_after' , 40 );


// set display mode to cookie
if ( !function_exists('freeio_before_woocommerce_init') ) {
    function freeio_before_woocommerce_init() {
        if( isset($_GET['display_mode']) && ($_GET['display_mode']=='list' || $_GET['display_mode']=='grid') ){  
            setcookie( 'freeio_woo_mode', trim($_GET['display_mode']) , time()+3600*24*100,'/' );
            $_COOKIE['freeio_woo_mode'] = trim($_GET['display_mode']);
        }
    }
}
add_action( 'init', 'freeio_before_woocommerce_init' );

// Number of products per page
if ( !function_exists('freeio_woocommerce_shop_per_page') ) {
    function freeio_woocommerce_shop_per_page($number) {
        $value = freeio_get_config('number_products_per_page', 12);
        return intval( $value );

    }
}
add_filter( 'loop_shop_per_page', 'freeio_woocommerce_shop_per_page', 30 );

// Number of products per row
if ( !function_exists('freeio_woocommerce_shop_columns') ) {
    function freeio_woocommerce_shop_columns($number) {
        $value = freeio_get_config('product_columns', 4);
        if ( in_array( $value, array(1, 2, 3, 4, 5, 6) ) ) {
            $number = $value;
        }
        return $number;
    }
}
add_filter( 'loop_shop_columns', 'freeio_woocommerce_shop_columns' );

// swap effect
if ( !function_exists('freeio_swap_images') ) {
    function freeio_swap_images() {
        global $post, $product, $woocommerce;
        
        $thumb = 'woocommerce_thumbnail';
        $output = '';
        $class = "attachment-$thumb size-$thumb image-no-effect";
        if (has_post_thumbnail()) {
            $swap_image = freeio_get_config('enable_swap_image', true);
            if ( $swap_image ) {
                $attachment_ids = $product->get_gallery_image_ids();
                if ($attachment_ids && isset($attachment_ids[0])) {
                    $class = "attachment-$thumb size-$thumb image-hover";
                    $swap_class = "attachment-$thumb size-$thumb image-effect";
                    $output .= freeio_get_attachment_thumbnail( $attachment_ids[0], $thumb, false, array('class' => $swap_class), false);
                }
            }
            $output .= freeio_get_attachment_thumbnail( get_post_thumbnail_id(), $thumb , false, array('class' => $class), false);
        } else {
            $output .= '<img src="'.esc_url(wc_placeholder_img_src()).'" alt="'.esc_attr__('Placeholder' , 'freeio').'" class="'.$class.'"  />';
        }
        echo trim($output);
    }
}
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'freeio_swap_images', 10);

if ( !function_exists('freeio_product_image') ) {
    function freeio_product_image($thumb = 'shop_thumbnail') {
        $swap_image = (bool)freeio_get_config('enable_swap_image', true);
        ?>
        <a title="<?php echo esc_attr(get_the_title()); ?>" href="<?php the_permalink(); ?>" class="product-image">
            <?php freeio_product_get_image($thumb, $swap_image); ?>
        </a>
        <?php
    }
}
// get image
if ( !function_exists('freeio_product_get_image') ) {
    function freeio_product_get_image($thumb = 'woocommerce_thumbnail', $swap = true) {
        global $post, $product, $woocommerce;
        
        $output = '';
        $class = "attachment-$thumb size-$thumb image-no-effect";
        if (has_post_thumbnail()) {
            if ( $swap ) {
                $attachment_ids = $product->get_gallery_image_ids();
                if ($attachment_ids && isset($attachment_ids[0])) {
                    $class = "attachment-$thumb size-$thumb image-hover";
                    $swap_class = "attachment-$thumb size-$thumb image-effect";
                    $output .= freeio_get_attachment_thumbnail( $attachment_ids[0], $thumb , false, array('class' => $swap_class), false);
                }
            }
            $output .= freeio_get_attachment_thumbnail( get_post_thumbnail_id(), $thumb , false, array('class' => $class), false);
        } else {
            $image_sizes = get_option('shop_catalog_image_size');
            $placeholder_width = $image_sizes['width'];
            $placeholder_height = $image_sizes['height'];

            $output .= '<img src="'.esc_url(wc_placeholder_img_src()).'" alt="'.esc_attr__('Placeholder' , 'freeio').'" class="'.$class.'" width="'.$placeholder_width.'" height="'.$placeholder_height.'" />';
        }
        echo trim($output);
    }
}

// layout class for woo page
if ( !function_exists('freeio_woocommerce_content_class') ) {
    function freeio_woocommerce_content_class( $class ) {
        $page = 'archive';
        if ( is_singular( 'product' ) ) {
            $page = 'single';
        }
        if( freeio_get_config('product_'.$page.'_fullwidth') ) {
            return 'container-fluid';
        }
        return $class;
    }
}
add_filter( 'freeio_woocommerce_content_class', 'freeio_woocommerce_content_class' );

// get layout configs
if ( !function_exists('freeio_get_woocommerce_layout_configs') ) {
    function freeio_get_woocommerce_layout_configs() {
        $page = 'archive';
        if ( is_singular( 'product' ) ) {
            $page = 'single';
        }
        // lg and md for fullwidth
        $left = freeio_get_config('product_'.$page.'_left_sidebar');
        $right = freeio_get_config('product_'.$page.'_right_sidebar');

        switch ( freeio_get_config('product_'.$page.'_layout') ) {
            case 'left-main':
                if ( is_active_sidebar( $left ) ) {
                    $configs['left'] = array( 'sidebar' => $left, 'class' => 'sidebar-shop col-lg-3 col-12 '  );
                    $configs['main'] = array( 'class' => 'main-blog p-left col-lg-9 col-12' );
                }
                break;
            case 'main-right':
                if ( is_active_sidebar( $right ) ) {
                    $configs['right'] = array( 'sidebar' => $right,  'class' => 'sidebar-shop col-lg-3 col-12 ' ); 
                    $configs['main'] = array( 'class' => 'main-blog p-right col-lg-9 col-12' );
                }
                break;
            case 'main':
                $configs['main'] = array( 'class' => 'col-md-12 col-12' );
                break;
            default:
                if (is_active_sidebar( 'sidebar-default' ) && !is_shop() && !is_single() ) {
                    $configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'sidebar-shop col-lg-3 col-12' ); 
                    $configs['main'] = array( 'class' => 'main-blog p-right col-lg-9 col-12' );
                } else {
                    $configs['main'] = array( 'class' => 'col-md-12 col-12' );
                }
                break;
        }
        if ( empty($configs) ) {
            if (is_active_sidebar( 'sidebar-default' ) && !is_shop() && !is_single() ) {
                $configs['right'] = array( 'sidebar' => 'sidebar-default',  'class' => 'sidebar-shop col-lg-3 col-12' ); 
                $configs['main'] = array( 'class' => 'main-blog p-right col-lg-9 col-12' );
            } else {
                $configs['main'] = array( 'class' => 'col-12' );
            }
        }
        
        return $configs; 
    }
}

if ( !function_exists( 'freeio_product_review_tab' ) ) {
    function freeio_product_review_tab($tabs) {
        global $post;
        if ( !freeio_get_config('show_product_review_tab', true) && isset($tabs['reviews']) ) {
            unset( $tabs['reviews'] ); 
        }
        return $tabs;
    }
}
add_filter( 'woocommerce_product_tabs', 'freeio_product_review_tab', 90 );

function freeio_woo_after_shop_loop_before() {
    ?>
    <div class="apus-after-loop-shop clearfix">
    <?php
}
function freeio_woo_after_shop_loop_after() {
    ?>
    </div>
    <?php
}
add_action( 'woocommerce_after_shop_loop', 'freeio_woo_after_shop_loop_before', 1 );
add_action( 'woocommerce_after_shop_loop', 'freeio_woo_after_shop_loop_after', 99999 );
// remove </a> in add to cart
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );