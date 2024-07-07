<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$args = array(
	'projects' => $projects,
	'settings' => $settings,
);

$total = $projects->found_posts;
$per_page = $projects->query_vars['posts_per_page'];
$current = max( 1, $projects->get( 'paged', 1 ) );

$page_args = array(
	'projects' => $projects,
	'settings' => $pagination_settings,
);

$filters = WP_Freeio_Abstract_Filter::get_filters();
?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/project/results-filters', array('filters' => $filters)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/project/results-count', array('total' => $total, 'per_page' => $per_page, 'current' => $current)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/project/orderby', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/project/archive-inner-elementor', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/project/pagination-elementor', $page_args ); ?>