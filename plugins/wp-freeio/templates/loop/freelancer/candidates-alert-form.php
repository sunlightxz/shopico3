<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$email_frequency_default = WP_Freeio_Job_Alert::get_email_frequency();
?>
<div class="freelancer-alert-form-btn">
	<a href="javascript:void(0);" class="btn btn-theme btn-freelancer-alert"><?php esc_html_e('Get Freelancer Alerts', 'wp-freeio'); ?></a>
</div>
<div class="freelancer-alert-form-wrapper hidden">
	<form method="get" action="" class="freelancer-alert-form">
		<div class="form-group">
		    <label for="freelancer_alert_title"><?php esc_html_e('Title', 'wp-freeio'); ?></label>

		    <input type="text" name="name" class="form-control" id="freelancer_alert_title">
		</div><!-- /.form-group -->

		<div class="form-group">
		    <label for="freelancer_alert_email_frequency"><?php esc_html_e('Email Frequency', 'wp-freeio'); ?></label>
		    <div class="wrapper-select">
			    <select name="email_frequency" class="form-control" id="freelancer_alert_email_frequency">
			        <?php if ( !empty($email_frequency_default) ) { ?>
			            <?php foreach ($email_frequency_default as $key => $value) {
			                if ( !empty($value['label']) && !empty($value['days']) ) {
			            ?>
			                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($value['label']); ?></option>

			                <?php } ?>
			            <?php } ?>
			        <?php } ?>
			    </select>
		    </div>
		</div><!-- /.form-group -->

		<?php
			do_action('wp-freeio-add-freelancer-alert-form');

			wp_nonce_field('wp-freeio-add-freelancer-alert-nonce', 'nonce');
		?>

		<div class="form-group">
			<button class="button"><?php esc_html_e('Save Freelancer Alert', 'wp-freeio'); ?></button>
		</div><!-- /.form-group -->

	</form>
</div>