<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();
$verification_sent = 0;
$args = array(
    'post_type' => 'verification',
    'author'    =>  $user_id,
    'fields' => 'ids',
    'post_status' => array('publish', 'pending')
);

$query = new WP_Query( $args );
if( !empty( $query ) ){
   $verification_sent = $query->found_posts;
}

if( $verification_sent > 0 ) {
	?>
	<div class="box-dashboard-wrapper max-650">
		<h3 class="widget-title"><?php echo esc_html__('Verification Details','freeio') ?></h3>
		<div class="inner-list">
			<div class="mb-3">
				<?php esc_html_e('You have already sent the verification document. Please revoke verification to send again.', 'freeio'); ?>
			</div>
			<button type="button" name="revoke_verification	" class="btn btn-theme btn-inverse revoke-verification"><?php esc_html_e( 'Revoke Verification', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
		</div>
	</div>
	<?php
} else {
	$rand = rand(0000,9999);
?>
	<div class="box-dashboard-wrapper max-650">
		<h3 class="widget-title"><?php echo esc_html__('Verification Details','freeio') ?></h3>
		<div class="inner-list">
			<form method="post" action="" id="verification-identity-form-<?php echo esc_attr($rand); ?>" class="verification-identity-form form-theme" enctype="multipart/form-data">
				<div class="form-group">
					<label for="your-name"><?php echo esc_html__( 'Your Name', 'freeio' ); ?></label>
					<input id="your-name" class="form-control" type="text" name="name" required="required">
				</div><!-- /.form-control -->

				<div class="form-group">
					<label for="contact_number"><?php echo esc_html__( 'Contact Number', 'freeio' ); ?></label>
					<input id="contact_number" class="form-control" type="text" name="contact_number" required="required">
				</div><!-- /.form-control -->

				<div class="form-group">
					<label for="verification_number"><?php echo esc_html__( 'CNIC / Passport / NIN / SSN', 'freeio' ); ?></label>
					<input id="verification_number" class="form-control" type="text" name="verification_number" required="required">
				</div><!-- /.form-control -->

				<div class="form-group">
					<label for="document"><?php echo esc_html__( 'Upload Document', 'freeio' ); ?></label>
					<input id="document" class="form-control" type="file" name="document[]" required="required">
				</div><!-- /.form-control -->

				<div class="form-group">
					<label for="document"><?php echo esc_html__( 'Address', 'freeio' ); ?></label>
					<textarea name="address" class="form-control" required="required"></textarea>
				</div><!-- /.form-control -->

				<button type="submit" name="submit_verification" class="button btn btn-theme btn-inverse"><?php esc_html_e( 'Submit Verification', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
			</form>
		</div>
	</div>
<?php }