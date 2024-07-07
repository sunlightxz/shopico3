<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_query, $freeio_services;




if ( get_query_var( 'paged' ) ) {
    $paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}

$query_args = array(
	'post_type' => 'service',
    'post_status' => 'publish',
    'post_per_page' => wp_freeio_get_option('number_services_per_page', 10),
    'paged' => $paged,
);

$params = array();
$taxs = ['category', 'location', 'tag'];
foreach ($taxs as $tax) {
	if ( is_tax('service_'.$tax) ) {
		$term = $wp_query->queried_object;
		if ( isset( $term->term_id) ) {
			$params['filter-'.$tax] = $term->term_id;
		}
	}
}
if ( WP_Freeio_Service_Filter::has_filter() ) {
	$params = array_merge($params, $_GET);
}

$freeio_services = WP_Freeio_Query::get_posts($query_args, $params);

if ( isset( $_REQUEST['load_type'] ) && WP_Freeio_Mixes::is_ajax_request() ) {
	$args = array(
		'services' => $freeio_services,
		'settings' => !empty( $_REQUEST['settings'] ) ? $_REQUEST['settings'] : array(),
		'pagination_settings' => !empty( $_REQUEST['pagination_settings'] ) ? $_REQUEST['pagination_settings'] : array()
	);
	if ( 'items' !== $_REQUEST['load_type'] ) {
        echo WP_Freeio_Template_Loader::get_template_part('archive-service-elementor-ajax-full', $args);
	} else {
		echo WP_Freeio_Template_Loader::get_template_part('archive-service-elementor-ajax-services', $args);
	}

} else {
	get_header();

	?>
		<section id="main-container" class="inner ">
			<?php do_action('freeio_service_archive_content'); ?>
		</section>
	<?php

	get_footer();
}