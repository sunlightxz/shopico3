<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$args = array(
	'services' => $services,
	'settings' => $settings,
);

$total = $services->found_posts;
$per_page = $services->query_vars['posts_per_page'];
$current = max( 1, $services->get( 'paged', 1 ) );

$page_args = array(
	'services' => $services,
	'settings' => $pagination_settings,
);

$filters = WP_Freeio_Abstract_Filter::get_filters();
?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/service/results-filters', array('filters' => $filters)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/service/results-count', array('total' => $total, 'per_page' => $per_page, 'current' => $current)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/service/orderby', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/service/archive-inner-elementor', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/service/pagination-elementor', $page_args ); ?>