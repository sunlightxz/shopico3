<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$form      = WP_Freeio_Service_Submit_Form::get_instance();
$service_id    = $form->get_service_id();
$step      = $form->get_step();
$form_name = $form->get_form_name();

$user_id = get_current_user_id();
$user_packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_packages_by_user($user_id, true, 'service_package');
$packages = WP_Freeio_Wc_Paid_Listings_Service_Submit_Form::get_products();

?>
<form method="post" id="service_package_selection">
	<?php if ( WP_Freeio_User::is_freelancer_can_add_submission($user_id) || WP_Freeio_User::is_freelancer_can_edit_service( $service_id ) ) { ?>
		<div class="service_service_packages_title">
			<input type="hidden" name="service_id" value="<?php echo esc_attr( $service_id ); ?>" />

			<input type="hidden" name="<?php echo esc_attr($form_name); ?>" value="<?php echo esc_attr($form_name); ?>">
			<input type="hidden" name="submit_step" value="<?php echo esc_attr($step); ?>">
			<input type="hidden" name="object_id" value="<?php echo esc_attr($service_id); ?>">

			<?php wp_nonce_field('wp-freeio-service-submit-package-nonce', 'security-service-submit-package'); ?>

			<h2><?php esc_html_e( 'Choose a package', 'wp-freeio-wc-paid-listings' ); ?></h2>
		</div>
		<div class="service_listing_types">
			<?php if ( sizeof($form->errors) ) : ?>
				<div class="box-white-dashboard">
					<ul class="messages errors">
						<?php foreach ( $form->errors as $message ) { ?>
							<li class="message_line danger">
								<?php echo trim( $message ); ?>
							</li>
						<?php
						}
						?>
					</ul>
				</div>
			<?php endif; ?>

			<?php echo WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('service-user-packages', array('user_packages' => $user_packages) ); ?>
			<?php echo WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('service-packages', array('packages' => $packages) ); ?>
		</div>
	<?php } else { ?>
		<div class="text-warning">
			<?php esc_html_e('Sorry, you can\'t post a service.', 'wp-freeio-wc-paid-listings'); ?>
		</div>
	<?php } ?>
</form>