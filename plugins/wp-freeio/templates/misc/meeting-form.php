<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

$wp_scripts = wp_scripts();
$jquery_version = $wp_scripts->registered['jquery-ui-core']->ver;
wp_enqueue_style('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', false, '1.0.0', false);


wp_enqueue_script( 'jquery-ui-core' );
wp_enqueue_script( 'jquery-ui-datepicker' );
wp_enqueue_script( 'wpfi-select2' );
wp_enqueue_style( 'wpfi-select2' );


$user_id = WP_Freeio_User::get_user_id();
$employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);

$zoom_email = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_email');
$zoom_client_id = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_client_id');
$zoom_client_secret = WP_Freeio_Employer::get_post_meta($employer_id, 'zoom_client_secret');
$access_token = WP_Freeio_Meeting_Zoom::user_zoom_access_token($user_id);

$rand_id = WP_Freeio_Mixes::random_key();

$datepicker_date_format = str_replace(
    array( 'd', 'j', 'l', 'z', /* Day. */ 'F', 'M', 'n', 'm', /* Month. */ 'Y', 'y', /* Year. */ ),
    array( 'dd', 'd', 'DD', 'o', 'MM', 'M', 'm', 'mm', 'yy', 'y', ),
    get_option( 'date_format' )
);
?>
<div id="job-apply-create-meeting-form-wrapper-<?php echo esc_attr($post->ID); ?>" class="job-apply-email-form-wrapper mfp-hide">
	<div class="inner">
		<h2 class="widget-title"><span><?php esc_html_e('Create Meeting', 'wp-freeio'); ?></span></h2>

		<form id="job-apply-create-meeting-form-<?php echo esc_attr($post->ID); ?>" class="create-meeting-form" method="post" autocomplete="off">
			<div class="form-group">
				<label><?php esc_html_e('Date', 'wp-freeio'); ?></label>
				<input type="text" class="form-control style2 datetimepicker-date" name="date_display" autocomplete="false" placeholder="<?php echo esc_attr(date_i18n(get_option('date_format'), strtotime('now'))); ?>" required="required" data-date_format="<?php echo esc_attr($datepicker_date_format); ?>" data-id="#datetimepicker-date-id<?php echo esc_attr($rand_id); ?>">

				<input id="datetimepicker-date-id<?php echo esc_attr($rand_id); ?>" type="hidden" class="form-control" name="date" required="required">
			</div>
			<div class="form-group">
				<label><?php esc_html_e('Time', 'wp-freeio'); ?></label>
				<select class="select-time-hour form-control style2" name="time" placeholder="<?php echo esc_attr(date_i18n(get_option('time_format'), strtotime('now'))); ?>">

					<?php foreach (range(0, 86399, 900) as $time) {
						$value = gmdate( 'H:i', $time);
					?>
						<option value="<?php echo esc_attr( $value ) ?>"><?php echo esc_html( gmdate( get_option( 'time_format' ), $time ) ) ?></option>
					<?php }
						$value = gmdate( 'H:i', 86399);
					?>
					<option value="<?php echo esc_attr( $value ) ?>"><?php echo esc_html( gmdate( get_option( 'time_format' ), 86399 ) ) ?></option>
				</select>
			</div>
			<div class="form-group">
				<label><?php esc_html_e('Time Duration', 'wp-freeio'); ?></label>
				<input type="text" class="form-control style2" name="time_duration" placeholder="<?php esc_attr_e('30', 'wp-freeio'); ?>" required="required">
			</div>

	     	<div class="form-group space-30">
	     		<label><?php esc_html_e('Message', 'wp-freeio'); ?></label>
	            <textarea class="form-control style2" name="message" placeholder="<?php esc_attr_e( 'Message', 'wp-freeio' ); ?>"></textarea>
	        </div>

	        <?php if ( !empty($zoom_email) && !empty($zoom_client_id) && !empty($zoom_client_secret) && !empty($access_token) ) { ?>
		        <div class="form-group">
					<label><?php esc_html_e('Zoom Meeting', 'wp-freeio'); ?></label>
					<input type="checkbox" name="zoom_meeting" value="1">
				</div>
			<?php } ?>
			
	        <!-- /.form-group -->

			<?php wp_nonce_field( 'wp-freeio-create-meeting-nonce', 'nonce' ); ?>
	      	<input type="hidden" name="action" value="wp_freeio_ajax_create_meeting">
	      	<input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
	        <button class="button btn btn-theme btn-block" name="create-meeting"><?php echo esc_html__( 'Create Meeting', 'wp-freeio' ); ?></button>
		</form>
	</div>
</div>
