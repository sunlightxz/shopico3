<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_style( 'dashicons' );
?>
<div class="job-submission-form-wrapper box-dashboard-wrapper">
	<?php if ( $form_obj->get_form_name() == 'wp_freeio_job_edit_form' ) { ?>
		<h3 class="title"><?php esc_html_e('Edit Service','freeio') ?></h3>
	<?php } else { ?>
		<h3 class="title"><?php esc_html_e('Post a New Service','freeio') ?></h3>
	<?php } ?>
	
	<?php if ( sizeof($form_obj->errors) ) : ?>
		<div class="inner-list">
		<div class="alert alert-danger">
			<ul class="messages errors">
				<?php foreach ( $form_obj->errors as $message ) { ?>
					<li class="message_line danger">
						<?php echo trim( $message ); ?>
					</li>
				<?php
				}
				?>
			</ul>
		</div>
		</div>
	<?php endif; ?>

	<?php if ( sizeof($form_obj->success_msg) ) : ?>
		<div class="inner-list">
		<div class="alert alert-success">
			<ul class="messages success">
				<?php foreach ( $form_obj->success_msg as $message ) { ?>
					<li class="message_line info">
						<?php echo trim( $message ); ?>
					</li>
				<?php
				}
				?>
			</ul>
		</div>
		</div>
	<?php endif; ?>

	<?php
		echo cmb2_get_metabox_form( $metaboxes_form, $post_id, array(
			'form_format' => '<form action="' . $form_obj->get_form_action() . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="service_id" value="'.$service_id.'"><input type="hidden" name="'.$form_obj->get_form_name().'" value="'.$form_obj->get_form_name().'"><input type="hidden" name="submit_step" value="'.$step.'"><input type="hidden" name="object_id" value="%2$s">%3$s
			<div class="submit-button-wrapper">
					<button type="submit" name="submit-cmb-service" value="%4$s" class="btn btn-theme">%4$s <i class="flaticon-right-up next"></i></button>
				</div>
			</form>',
			'save_button' => $submit_button_text,
		) );
	?>
</div>