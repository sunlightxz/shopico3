<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$jobs_display_mode = freeio_get_jobs_display_mode();
$job_inner_style = freeio_get_jobs_inner_style();

$args = array(
	'jobs' => $jobs,
	'job_inner_style' => $job_inner_style,
	'jobs_display_mode' => $jobs_display_mode,
);

echo WP_Freeio_Template_Loader::get_template_part('loop/job/archive-inner', $args);

echo WP_Freeio_Template_Loader::get_template_part('loop/job/pagination', array('jobs' => $jobs));
