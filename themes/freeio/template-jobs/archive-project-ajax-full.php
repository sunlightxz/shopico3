<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$projects_display_mode = freeio_get_projects_display_mode();
$project_inner_style = freeio_get_projects_inner_style();

$args = array(
	'projects' => $projects,
	'project_inner_style' => $project_inner_style,
	'projects_display_mode' => $projects_display_mode,
);
?>

<?php
	echo WP_Freeio_Template_Loader::get_template_part('loop/project/archive-inner', $args);
?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/project/pagination', array('projects' => $projects) ); ?>