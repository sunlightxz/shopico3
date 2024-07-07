<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo WP_Freeio_Template_Loader::get_template_part('loop/service/archive-inner', array('services' => $services));
