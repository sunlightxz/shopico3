<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'freelancers' => $freelancers,
	'settings' => $settings,
);

$total = $freelancers->found_posts;
$per_page = $freelancers->query_vars['posts_per_page'];
$current = max( 1, $freelancers->get( 'paged', 1 ) );

$page_args = array(
	'freelancers' => $freelancers,
	'settings' => $pagination_settings,
);

$filters = WP_Freeio_Abstract_Filter::get_filters();
?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/results-filters', array('filters' => $filters)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/results-count', array('total' => $total, 'per_page' => $per_page, 'current' => $current)); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/orderby', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/archive-inner-elementor', $args); ?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/pagination-elementor', $page_args ); ?>
