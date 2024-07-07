<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="register-form-wrapper">
  	<div class="container-form">

		<ul class="role-tabs">
			<li class="active"><?php esc_html_e('Freelancer', 'wp-freeio'); ?></li>
			<li><?php esc_html_e('Employer', 'wp-freeio'); ?></li>
		</ul>

      	<form name="registerForm" method="post" class="register-form register-form-freelancer">
			<?php do_action('register_freelancer_form_fields_before'); ?>

			<input type="radio" name="role" value="wp_freeio_freelancer" checked="checked" class="hidden">

			<div class="form-group">
				<label for="register-username"><?php esc_html_e('Username', 'wp-freeio'); ?></label>
				<sup class="required-field">*</sup>
				<input type="text" class="form-control" name="username" id="register-username" placeholder="<?php esc_attr_e('Enter Username','wp-freeio'); ?>">
			</div>
			<div class="form-group">
				<label for="register-email"><?php esc_html_e('Email', 'wp-freeio'); ?></label>
				<sup class="required-field">*</sup>
				<input type="text" class="form-control" name="email" id="register-email" placeholder="<?php esc_attr_e('Enter Email','wp-freeio'); ?>">
			</div>
		
			<div class="form-group">
				<label for="password"><?php esc_html_e('Password', 'wp-freeio'); ?></label>
				<sup class="required-field">*</sup>
				<input type="password" class="form-control" name="password" id="password" placeholder="<?php esc_attr_e('Enter Password','wp-freeio'); ?>">
			</div>
			<div class="form-group">
				<label for="confirmpassword"><?php esc_html_e('Confirm Password', 'wp-freeio'); ?></label>
				<sup class="required-field">*</sup>
				<input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="<?php esc_attr_e('Enter Password','wp-freeio'); ?>">
			</div>


			<?php do_action('register_freelancer_form_fields_after'); ?>


			<?php wp_nonce_field('ajax-register-nonce', 'security_register'); ?>

			<?php if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) { ?>
	            <div id="recaptcha-contact-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_freeio_get_option( 'recaptcha_site_key' )); ?>"></div>
	      	<?php } ?>
	      	
			<?php
			$page_id = wp_freeio_get_option('terms_conditions_page_id');
			if ( !empty($page_id) ) {
				$page_url = $page_id ? get_permalink($page_id) : home_url('/');
			?>
				<div class="form-group">
					<label for="register-terms-and-conditions">
						<input type="checkbox" name="terms_and_conditions" value="on" id="register-terms-and-conditions" required>
						<?php
							echo sprintf(__('You accept our <a href="%s">Terms and Conditions and Privacy Policy</a>', 'wp-freeio'), esc_url($page_url));
						?>
					</label>
				</div>
			<?php } ?>

			<div class="form-group">
				<button type="submit" class="btn btn-second btn-block" name="submitRegister">
					<?php echo esc_html__('Register now', 'wp-freeio'); ?>
				</button>
			</div>

			<?php do_action('register_form'); ?>
      	</form>

      	
    </div>

</div>
