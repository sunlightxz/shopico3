<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'jobs' => $jobs,
	'settings' => $settings,
);

$total = $jobs->found_posts;
$per_page = $jobs->query_vars['posts_per_page'];
$current = max( 1, $jobs->get( 'paged', 1 ) );

$page_args = array(
	'jobs' => $jobs,
	'settings' => $pagination_settings,
);

$filters = WP_Freeio_Abstract_Filter::get_filters();
?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/job/results-filters', array('filters' => $filters)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/job/results-count', array('total' => $total, 'per_page' => $per_page, 'current' => $current)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/job/orderby', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/job/archive-inner-elementor', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/job/pagination-elementor', $page_args ); ?>