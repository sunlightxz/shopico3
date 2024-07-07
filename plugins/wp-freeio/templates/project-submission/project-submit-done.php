<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="submission-form-wrapper">
	<?php
		do_action( 'wp_freeio_project_submit_done_content_after', sanitize_title( $project->post_status ), $project );

		switch ( $project->post_status ) :
			case 'publish' :
				echo wp_kses_post(sprintf(__( 'Project listed successfully. To view your listing <a href="%s">click here</a>.', 'wp-freeio' ), get_permalink( $project->ID ) ));
			break;
			case 'pending' :
				echo wp_kses_post(sprintf(esc_html__( 'Project submitted successfully. Your listing will be visible once approved.', 'wp-freeio' ), get_permalink( $project->ID )));
			break;
			default :
				do_action( 'wp_freeio_project_submit_done_content_' . str_replace( '-', '_', sanitize_title( $project->post_status ) ), $project );
			break;
		endswitch;

		do_action( 'wp_freeio_project_submit_done_content_after', sanitize_title( $project->post_status ), $project );
	?>
</div>
