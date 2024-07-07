<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Superio
 * @since Superio 1.0
 */
/*
*Template Name: Projects Elementor Template
*/

global $freeio_projects;

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

		<section id="main-container" class="inner">
			
			<?php
			// Start the loop.
			while ( have_posts() ) : the_post();
				
				// Include the page content template.
				the_content();

			// End the loop.
			endwhile;
			?>

		</section>


	<?php

	get_footer();
}