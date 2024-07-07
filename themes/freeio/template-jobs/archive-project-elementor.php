<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_query, $freeio_projects;




if ( get_query_var( 'paged' ) ) {
    $paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}

$query_args = array(
	'post_type' => 'project',
    'post_status' => 'publish',
    'post_per_page' => wp_freeio_get_option('number_projects_per_page', 10),
    'paged' => $paged,
);

$params = array();
$taxs = ['category', 'location', 'tag', 'experience', 'freelancer_type'];
foreach ($taxs as $tax) {
	if ( is_tax('project_'.$tax) ) {
		$term = $wp_query->queried_object;
		if ( isset( $term->term_id) ) {
			$params['filter-'.$tax] = $term->term_id;
		}
	}
}
// no prefix
$taxs = ['project_skill', 'project_level', 'project_language', 'project_duration', 'project_experience'];
foreach ($taxs as $tax) {
	if ( is_tax($tax) ) {
		$term = $wp_query->queried_object;
		if ( isset( $term->term_id) ) {
			$params['filter-'.$tax] = $term->term_id;
		}
	}
}

if ( WP_Freeio_Project_Filter::has_filter() ) {
	$params = array_merge($params, $_GET);
}

$freeio_projects = WP_Freeio_Query::get_posts($query_args, $params);

if ( isset( $_REQUEST['load_type'] ) && WP_Freeio_Mixes::is_ajax_request() ) {
	$args = array(
		'projects' => $freeio_projects,
		'settings' => !empty( $_REQUEST['settings'] ) ? $_REQUEST['settings'] : array(),
		'pagination_settings' => !empty( $_REQUEST['pagination_settings'] ) ? $_REQUEST['pagination_settings'] : array()
	);
	if ( 'items' !== $_REQUEST['load_type'] ) {
        echo WP_Freeio_Template_Loader::get_template_part('archive-project-elementor-ajax-full', $args);
	} else {
		echo WP_Freeio_Template_Loader::get_template_part('archive-project-elementor-ajax-projects', $args);
	}

} else {
	get_header();

	?>
		<section id="main-container" class="inner ">
			<?php do_action('freeio_project_archive_content'); ?>
		</section>
	<?php

	get_footer();
}