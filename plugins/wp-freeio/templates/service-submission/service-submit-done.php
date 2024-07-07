<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="submission-form-wrapper">
	<?php
		do_action( 'wp_freeio_service_submit_done_content_after', sanitize_title( $service->post_status ), $service );

		switch ( $service->post_status ) :
			case 'publish' :
				echo wp_kses_post(sprintf(__( 'Service listed successfully. To view your listing <a href="%s">click here</a>.', 'wp-freeio' ), get_permalink( $service->ID ) ));
			break;
			case 'pending' :
				echo wp_kses_post(sprintf(esc_html__( 'Service submitted successfully. Your listing will be visible once approved.', 'wp-freeio' ), get_permalink( $service->ID )));
			break;
			default :
				do_action( 'wp_freeio_service_submit_done_content_' . str_replace( '-', '_', sanitize_title( $service->post_status ) ), $service );
			break;
		endswitch;

		do_action( 'wp_freeio_service_submit_done_content_after', sanitize_title( $service->post_status ), $service );
	?>
</div>
