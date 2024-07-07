<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'employers' => $employers,
	'settings' => $settings,
);

$total = $employers->found_posts;
$per_page = $employers->query_vars['posts_per_page'];
$current = max( 1, $employers->get( 'paged', 1 ) );

$page_args = array(
	'employers' => $employers,
	'settings' => $pagination_settings,
);

$filters = WP_Freeio_Abstract_Filter::get_filters();
?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/employer/results-filters', array('filters' => $filters)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/employer/results-count', array('total' => $total, 'per_page' => $per_page, 'current' => $current)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/employer/orderby', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/employer/archive-inner-elementor', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/employer/pagination-elementor', $page_args ); ?>
