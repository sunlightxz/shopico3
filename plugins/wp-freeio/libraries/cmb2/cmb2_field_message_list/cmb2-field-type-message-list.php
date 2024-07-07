<?php
/**
 * CMB2 Addons
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_CMB2_Field_Message_List {

	public function __construct() {
		add_filter( 'cmb2_render_wpfr_message_list', array( $this, 'render_field' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wpfr_message_list', array( $this, 'sanitize_field' ), 10, 4 );	
	}

	/**
	 * Render field
	 */
	public function render_field( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$this->setup_admin_scripts();
		if ( $field_object_id ) {

			?>
			<div id="messages-list" class="messages-list">
				<?php
					$messages = get_post_meta($field_object_id, WP_FREEIO_DISPUTE_PREFIX . 'messages', true);;
				    $messages = !empty($messages) ? $messages : array();
				    if ( !empty($messages) ) {
				    	echo $this->display_list_messages($messages, $field_object_id, 'project');
				    }
			    ?>
			</div>
			<?php
		}
	}

	public function display_list_messages ($messages, $post_id, $type) {
		ob_start();
	    $user_id = get_post_meta( $post_id, WP_FREEIO_DISPUTE_PREFIX . 'sender', true );
	    ?>
	    <div class="proposals-message-content">
	    	<ul class="list-replies">
				<?php
				$messages = array_reverse($messages);
			    foreach ($messages as $key => $message) {
			    	?>
			    	<li class="<?php echo esc_attr($message['user_id'] == $user_id ? 'yourself-reply' : 'user-reply'); ?> author-id-<?php echo esc_attr($message['user_id']); ?>">
						<?php if ( $message['user_id'] != $user_id ) { ?>
							<div class="avatar">
								<?php freeio_user_avarta( $message['user_id'] ); ?>
							</div>
						<?php } ?>
						<div class="reply-content">
							<!-- date -->
							<div class="post-date">
								<?php
	                                $time = $message['time'];
	                                echo human_time_diff( $time, current_time( 'timestamp' ) ).' '.esc_html__( 'ago', 'freeio' );
	                            ?>
							</div>
							<div class="post-content">
								<?php echo wpautop($message['message']); ?>

								<?php
								if ( !empty($message['attachment_ids']) ) {
									$download_base_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_proposal_attachment');
									?>
									<div class="attachments">
										<?php
										foreach ($message['attachment_ids'] as $id => $cv_url) {
								            $file_info = pathinfo($cv_url);
								            if ( $file_info ) {
								            	$download_url = add_query_arg(array('file_id' => $id, 'post_id' => $post_id, 'type' => $type), $download_base_url);
								            ?>
								            	<div class="item">
									                <a href="<?php echo esc_url($download_url); ?>" class="d-inline-block type-file-message">
									                    <span class="icon_type pre d-inline-block text-theme">
									                        <i class="flaticon-file"></i>
									                    </span>
									                    <?php if ( !empty($file_info['basename']) ) { ?>
									                        <span class="filename"><?php echo esc_html($file_info['basename']); ?></span>
									                    <?php } ?>
									                </a>
								                </div>
								            <?php }
								        } ?>
									</div>
									<?php
								}
								?>
							</div>
						</div>
					</li>
					<?php
			    }
			    ?>
			</ul>
	    </div>
	    <?php
	    $return = ob_get_clean();
	    return $return;
	}

	public function sanitize_field( $override_value, $value, $object_id, $field_args ) {
		return $value;
	}

	public function setup_admin_scripts() {
		wp_enqueue_style( 'wpfr-message-list', plugins_url( 'css/style.css', __FILE__ ), array(), '1.0' );

	}
}

$cmb2_message_list = new WP_Freeio_CMB2_Field_Message_List();