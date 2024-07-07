<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$services_display_mode = freeio_get_services_display_mode();
$service_inner_style = freeio_get_services_inner_style();

$args = array(
	'services' => $services,
	'service_inner_style' => $service_inner_style,
	'services_display_mode' => $services_display_mode,
);

echo WP_Freeio_Template_Loader::get_template_part('loop/service/archive-inner', $args);

echo WP_Freeio_Template_Loader::get_template_part('loop/service/pagination', array('services' => $services));
