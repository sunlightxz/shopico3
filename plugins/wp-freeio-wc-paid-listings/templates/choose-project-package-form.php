<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$form      = WP_Freeio_Project_Submit_Form::get_instance();
$project_id    = $form->get_project_id();
$step      = $form->get_step();
$form_name = $form->get_form_name();

$user_id = get_current_user_id();
$user_packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_packages_by_user($user_id, true, 'project_package');
$packages = WP_Freeio_Wc_Paid_Listings_Project_Submit_Form::get_products();

?>
<form method="post" id="project_package_selection">
	<?php if ( WP_Freeio_User::is_employer_can_add_submission($user_id) || WP_Freeio_User::is_employer_can_edit_project( $project_id ) ) { ?>
		<div class="project_project_packages_title">
			<input type="hidden" name="project_id" value="<?php echo esc_attr( $project_id ); ?>" />

			<input type="hidden" name="<?php echo esc_attr($form_name); ?>" value="<?php echo esc_attr($form_name); ?>">
			<input type="hidden" name="submit_step" value="<?php echo esc_attr($step); ?>">
			<input type="hidden" name="object_id" value="<?php echo esc_attr($project_id); ?>">

			<?php wp_nonce_field('wp-freeio-project-submit-package-nonce', 'security-project-submit-package'); ?>

			<h2><?php esc_html_e( 'Choose a package', 'wp-freeio-wc-paid-listings' ); ?></h2>
		</div>
		<div class="project_listing_types">
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

			<?php echo WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('project-user-packages', array('user_packages' => $user_packages) ); ?>
			<?php echo WP_Freeio_Wc_Paid_Listings_Template_Loader::get_template_part('project-packages', array('packages' => $packages) ); ?>
		</div>
	<?php } else { ?>
		<div class="text-warning">
			<?php esc_html_e('Sorry, you can\'t post a project.', 'wp-freeio-wc-paid-listings'); ?>
		</div>
	<?php } ?>
</form>