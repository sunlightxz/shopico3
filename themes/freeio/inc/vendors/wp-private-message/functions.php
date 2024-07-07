<?php

remove_action( 'widgets_init', array( WP_Private_Message::getInstance(), 'register_widgets' ) );

add_action( 'freeio_after_contact_form', 'freeio_private_message_form', 10, 2 );
function freeio_private_message_form($post, $user_id, $btn_text = '') {
	?>
	<div class="send-private-wrapper">
		<a href="#send-private-message-wrapper-<?php echo esc_attr($post->ID); ?>" class="btn-show-popup send-private-message-btn btn btn-theme btn-sm">
			<?php
			if ( !empty($btn_text) ){
				echo trim($btn_text);
			} else {
				esc_html_e('Message', 'freeio');
			}
			?>
				
			</a>
	</div>
	<div id="send-private-message-wrapper-<?php echo esc_attr($post->ID); ?>" class="send-private-message-wrapper mfp-hide" data-effect="fadeIn">
		<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>
		<h3 class="title"><?php echo esc_html__('Send message', 'freeio'); ?></h3>
		<?php
		if ( is_user_logged_in() ) {
			?>
			<form id="send-message-form" class="send-message-form" action="?" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'freeio' ); ?>" required="required">
                </div><!-- /.form-group -->
                <div class="form-group">
                    <textarea class="form-control message" name="message" placeholder="<?php esc_attr_e( 'Enter text here...', 'freeio' ); ?>" required="required"></textarea>
                </div><!-- /.form-group -->

                <?php wp_nonce_field( 'wp-private-message-send-message', 'wp-private-message-send-message-nonce' ); ?>
              	<input type="hidden" name="recipient" value="<?php echo esc_attr($user_id); ?>">
              	<input type="hidden" name="action" value="wp_private_message_send_message">
                <button class="button btn btn-theme btn-block send-message-btn"><?php echo esc_html__( 'Send Message', 'freeio' ); ?></button>
        	</form>
			<?php
		} else {
			$login_url = '';
			if ( function_exists('wp_freeio_get_option') ) {
				$page_id = wp_freeio_get_option('login_register_page_id');
				$page_id = WP_Freeio_Mixes::get_lang_post_id($page_id);
				$login_url = get_permalink( $page_id );
			}
			?>
			<a href="<?php echo esc_url($login_url); ?>" class="login"><?php esc_html_e('Please login to send a private message', 'freeio'); ?></a>
			<?php
		}
		?>
	</div>
	<?php
}

function freeio_private_message_form_btn($post, $user_id) {
	?>

	<a data-toggle="tooltip" class="btn btn-theme btn-sm send-private-message-btn" href="#send-private-message-wrapper-<?php echo esc_attr($post->ID); ?>" data-freelancer_id="<?php echo esc_attr($post->ID); ?>" title="<?php esc_attr_e('Send message', 'freeio'); ?>"><i class="flaticon-mail"></i></a>

	<div id="send-private-message-wrapper-<?php echo esc_attr($post->ID); ?>" class="send-private-message-wrapper mfp-hide" data-effect="fadeIn">
		<h3 class="title"><?php echo esc_html__('Send message', 'freeio'); ?></h3>
		<?php
		if ( is_user_logged_in() ) {
			?>
			<form id="send-message-form" class="send-message-form" action="?" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'freeio' ); ?>" required="required">
                </div><!-- /.form-group -->
                <div class="form-group">
                    <textarea class="form-control message" name="message" placeholder="<?php esc_attr_e( 'Enter text here...', 'freeio' ); ?>" required="required"></textarea>
                </div><!-- /.form-group -->

                <?php wp_nonce_field( 'wp-private-message-send-message', 'wp-private-message-send-message-nonce' ); ?>
              	<input type="hidden" name="recipient" value="<?php echo esc_attr($user_id); ?>">
              	<input type="hidden" name="action" value="wp_private_message_send_message">
                <button class="button btn btn-theme btn-block send-message-btn"><?php echo esc_html__( 'Send Message', 'freeio' ); ?></button>
        	</form>
			<?php
		} else {
			$login_url = '';
			if ( function_exists('wp_freeio_get_option') ) {
				$page_id = wp_freeio_get_option('login_register_page_id');
				$page_id = WP_Freeio_Mixes::get_lang_post_id($page_id);
				$login_url = get_permalink( $page_id );
			}
			?>
			<a href="<?php echo esc_url($login_url); ?>" class="login"><?php esc_html_e('Please login to send a private message', 'freeio'); ?></a>
			<?php
		}
		?>
	</div>
	<?php
}

function freeio_private_message_user_id($user_id = 0) {
	if ( method_exists('WP_Freeio_User', 'get_user_id') ) {
        $user_id = WP_Freeio_User::get_user_id();
    } else {
    	$user_id = get_current_user_id();
    }
    return $user_id;
}
add_filter('wp-private-message-get-current-user-id', 'freeio_private_message_user_id');

function freeio_private_message_user_display_name($display_name, $user_id) {
	if ( class_exists('WP_Freeio_User') && (WP_Freeio_User::is_employer($user_id) || WP_Freeio_User::is_freelancer($user_id)) ) {
	    if ( WP_Freeio_User::is_employer($user_id) ) {
	        $employer_id = WP_Freeio_User::get_employer_by_user_id($user_id);
	        $display_name = get_the_title($employer_id);
	    } else {
	        $freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($user_id);
	        $display_name = get_the_title($freelancer_id);
	    }
	}
    return $display_name;
}
add_filter('freeio-private-message-user-display-name', 'freeio_private_message_user_display_name', 10, 2);