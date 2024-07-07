<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = array(
	'freelancers' => $freelancers,
);
?>

<?php
	echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/archive-inner', $args);
?>

<?php echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/pagination', array('freelancers' => $freelancers) ); ?>
