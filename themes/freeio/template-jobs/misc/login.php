<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_style( 'dashicons' );
$popup = isset($popup) ? $popup : false;
$rand = rand(0000, 9999);
?>
<div class="box-account">
	<div class="login-form-wrapper">
		<div id="login-form-wrapper<?php echo esc_attr($rand); ?>" class="form-container">			
			<?php if ( defined('FREEIO_DEMO_MODE') && FREEIO_DEMO_MODE ) { ?>
				<div class="sign-in-demo-notice">
					Username: <strong>freelancer</strong> or <strong>employer</strong><br>
					Password: <strong>demo</strong>
				</div>
			<?php } ?>
			
			<form class="login-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="post">
				<?php if ( isset($_SESSION['register_msg']) ) { ?>
					<div class="alert <?php echo esc_attr($_SESSION['register_msg']['error'] ? 'alert-warning' : 'alert-info'); ?>">
						<?php echo trim($_SESSION['register_msg']['msg']); ?>
					</div>
				<?php
					unset($_SESSION['register_msg']);
				}
				?>
				<div class="form-group">
					<label><?php esc_attr_e('Email','freeio'); ?></label>
					<input autocomplete="off" type="text" name="username" class="form-control" id="username_or_email" placeholder="<?php esc_attr_e('Email','freeio'); ?>">
				</div>
				<div class="form-group">
					<label><?php esc_attr_e('Password','freeio'); ?></label>
					<span class="show_hide_password">
						<input name="password" type="password" class="password required form-control" id="login_password" placeholder="<?php esc_attr_e('Password','freeio'); ?>">
						<a class="toggle-password" title="<?php esc_attr_e('Show', 'freeio'); ?>"><span class="dashicons dashicons-hidden"></span></a>
					</span>
				</div>

				<div class="row form-group info">
					<div class="col-6">
						<label for="user-remember-field<?php echo esc_attr($rand); ?>" class="remember">
							<input type="checkbox" name="remember" id="user-remember-field<?php echo esc_attr($rand); ?>" value="true"> <?php echo esc_html__('Keep me signed in','freeio'); ?>
						</label>
					</div>
					<div class="col-6 link-right">
						<a class="back-link" href="#forgot-password-form-wrapper<?php echo esc_attr($rand); ?>" title="<?php esc_attr_e('Forgotten password','freeio'); ?>"><?php echo esc_html__("Forgotten password?",'freeio'); ?></a>
					</div>
				</div>
				
				<div class="form-group info">
					<div id="recaptcha-login-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_freeio_get_option( 'recaptcha_site_key' )); ?>"></div>
				</div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-theme w-100" name="submit"><?php echo esc_html__('Login','freeio'); ?><i class="flaticon-right-up next"></i></button>
				</div>
				<?php
					do_action('login_form');
					wp_nonce_field('ajax-login-nonce', 'security_login');
				?>
				<?php if ( $popup ) { ?>
					<div class="register-info">
						<?php esc_html_e('Don\'t you have an account?', 'freeio'); ?>
						<a class="apus-user-register" href="#apus_register_form">
		                    <?php esc_html_e('Register', 'freeio'); ?>
		                </a>
		            </div>
		        <?php } ?>
			</form>
		</div>
		<!-- reset form -->
		<div id="forgot-password-form-wrapper<?php echo esc_attr($rand); ?>" class="form-container forgotpassword-form-wrapper">
			<div class="top-info-user">
				<h3 class="title"><?php echo esc_html__('Reset Password', 'freeio'); ?></h3>
				<div class="des-forgot"><?php echo esc_html__('Please Enter Username or Email','freeio') ?></div>
			</div>
			<form name="forgotpasswordform" class="forgotpassword-form" action="<?php echo esc_url( site_url('wp-login.php?action=lostpassword', 'login_post') ); ?>" method="post">
				<div class="lostpassword-fields">
					<div class="form-group">
						<input type="text" name="user_login" class="user_login form-control" id="lostpassword_username" placeholder="<?php esc_attr_e('Username or E-mail','freeio'); ?>">
					</div>
					<?php
						do_action('lostpassword_form');
						wp_nonce_field('ajax-lostpassword-nonce', 'security_lostpassword');
					?>

		            <div id="recaptcha-forgotpasswordform" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_freeio_get_option( 'recaptcha_site_key' )); ?>"></div>
			      	
					<div class="form-group">
						<button type="submit" class="btn btn-theme w-100" name="wp-submit"><?php echo esc_html__('Get New Password','freeio'); ?><i class="flaticon-right-up next"></i></button>
					</div>
				</div>
				<div class="lostpassword-link"><a href="#login-form-wrapper<?php echo esc_attr($rand); ?>" class="back-link"><?php echo esc_html__('Back To Login', 'freeio'); ?></a></div>
			</form>
		</div>
	</div>
</div>