<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php
	echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/archive-inner', array('freelancers' => $freelancers));

	echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/pagination', array('freelancers' => $freelancers));
?>