<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty($user_id) ) {
	return;
}
?>
<div class="box-dashboard-wrapper">
	<h3 class="widget-title"><?php echo esc_html__('Delete Profile','freeio') ?></h3>
	<div class="inner-list">
		<div class="widget-delete">
			<div class="conf-messages"><?php esc_html_e('Are you sure! You want to delete your profile.', 'freeio'); ?></div>
			<div class="undone-messages"><?php esc_html_e('This can\'t be undone!', 'freeio'); ?></div>

			<form method="post" action="" class="delete-profile-form form-theme">

				<div class="form-group">
					<label><?php esc_html_e( 'Please enter your login Password to confirm:', 'freeio' ); ?></label>
					<input id="delete-profile-password" class="form-control" type="password" name="password" required="required" placeholder="<?php esc_attr_e('Password', 'freeio'); ?>">
				</div><!-- /.form-control -->

				<?php
					do_action('wp-freeio-delete-profile-form-fields');
					wp_nonce_field('wp-freeio-delete-profile-nonce', 'nonce');
				?>

				<button type="submit" class="btn btn-danger btn-inverse delete-profile-btn"><?php esc_html_e( 'Delete Profile', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
			</form>
		</div>
	</div>
</div>