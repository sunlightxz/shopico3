<?php

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

if ( !is_user_logged_in() || !WP_Freeio_User::is_freelancer() ) {
?>
	<div class="job-apply-internal-required-wrapper" style="display: none;">
		<div class="msg-inner"><?php esc_html_e('Please login as "Freelancer" to apply', 'wp-freeio'); ?></div>
	</div>
<?php }