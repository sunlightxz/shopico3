<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="services-pagination-wrapper">
	<?php
		WP_Freeio_Mixes::custom_pagination( array(
			'max_num_pages' => $services->max_num_pages,
			'prev_text'     => esc_html__( 'Previous page', 'wp-freeio' ),
			'next_text'     => esc_html__( 'Next page', 'wp-freeio' ),
			'wp_query' => $services
		));
	?>
</div>
