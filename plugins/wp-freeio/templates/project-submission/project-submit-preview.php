<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="project-submission-preview-form-wrapper">
	<?php if ( sizeof($form_obj->errors) ) : ?>
		<ul class="messages">
			<?php foreach ( $form_obj->errors as $message ) { ?>
				<li class="message_line danger">
					<?php echo wp_kses_post( $message ); ?>
				</li>
			<?php
			}
			?>
		</ul>
	<?php endif; ?>
	<form action="<?php echo esc_url($form_obj->get_form_action());?>" class="cmb-form" method="post" enctype="multipart/form-data" encoding="multipart/form-data">
		<input type="hidden" name="<?php echo esc_attr($form_obj->get_form_name()); ?>" value="<?php echo esc_attr($form_obj->get_form_name()); ?>">
		<input type="hidden" name="project_id" value="<?php echo esc_attr($project_id); ?>">
		<input type="hidden" name="submit_step" value="<?php echo esc_attr($step); ?>">
		<input type="hidden" name="object_id" value="<?php echo esc_attr($project_id); ?>">
		<?php wp_nonce_field('wp-freeio-project-submit-preview-nonce', 'security-project-submit-preview'); ?>

		<button class="button btn" name="continue-submit-project"><?php esc_html_e('Submit Project', 'wp-freeio'); ?></button>
		<button class="button btn" name="continue-edit-project"><?php esc_html_e('Edit Project', 'wp-freeio'); ?></button>

	</form>

	<?php echo WP_Freeio_Template_Loader::get_template_part( 'content-single-project' ); ?>
</div>
