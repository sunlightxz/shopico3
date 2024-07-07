<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post, $preview_post;
$preview_post = $post;

add_filter('freeio_is_service_single', '__return_true' );
?>
<div class="job-submission-preview-form-wrapper">
	<?php if ( sizeof($form_obj->errors) ) : ?>
		<ul class="messages">
			<?php foreach ( $form_obj->errors as $message ) { ?>
				<li class="message_line danger">
					<?php echo trim( $message ); ?>
				</li>
			<?php
			}
			?>
		</ul>
	<?php endif; ?>
	<form action="<?php echo esc_url($form_obj->get_form_action());?>" class="cmb-form" method="post" enctype="multipart/form-data" encoding="multipart/form-data">
		<input type="hidden" name="<?php echo esc_attr($form_obj->get_form_name()); ?>" value="<?php echo esc_attr($form_obj->get_form_name()); ?>">
		<input type="hidden" name="service_id" value="<?php echo esc_attr($service_id); ?>">
		<input type="hidden" name="submit_step" value="<?php echo esc_attr($step); ?>">
		<input type="hidden" name="object_id" value="<?php echo esc_attr($service_id); ?>">
		<?php wp_nonce_field('wp-freeio-service-submit-preview-nonce', 'security-service-submit-preview'); ?>

		<div class="action-preview">
			<button class="button btn btn-second" name="continue-submit-service"><?php esc_html_e('Submit Service', 'freeio'); ?></button>
			<button class="button btn btn-theme" name="continue-edit-service"><?php esc_html_e('Edit Service', 'freeio'); ?></button>
		</div>
		
	</form>
	
	<div class="single-listing-wrapper service" <?php freeio_service_item_map_meta($post); ?>>
		<?php
			echo WP_Freeio_Template_Loader::get_template_part( 'content-single-service' );
		?>
	</div>
</div>