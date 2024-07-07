<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'employers' => $employers,
);
?>

<?php
	echo WP_Freeio_Template_Loader::get_template_part('loop/employer/archive-inner', $args);
?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/employer/pagination', array('employers' => $employers) ); ?>
