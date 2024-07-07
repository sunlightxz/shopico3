<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php
	echo WP_Freeio_Template_Loader::get_template_part('loop/employer/archive-inner', array('employers' => $employers));

	echo WP_Freeio_Template_Loader::get_template_part('loop/employer/pagination', array('employers' => $employers));
?>