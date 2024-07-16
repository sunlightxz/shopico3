<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_style( 'dashicons' );

?>
<div class="profile-form-wrapper box-dashboard-wrapper">
	<h3 class="title"><?php esc_html_e('Edit Profile','freeio') ?></h3>
	
	<?php
	
		if ( ! empty( $_SESSION['messages'] ) ) : ?>
		<div class="inner-list">
			<ul class="messages">
				<?php foreach ( $_SESSION['messages'] as $message ) { ?>
					<?php
					$status = !empty( $message[0] ) ? $message[0] : 'success';
					if ( !empty( $message[1] ) ) {
					?>
					<li class="message_line text-<?php echo esc_attr( $status ) ?>">
						<?php echo trim( $message[1] ); ?>
					</li>
				<?php
					}
				}
				unset( $_SESSION['messages'] );
				?>
			</ul>
		</div>
	<?php endif; ?>

	<?php
		echo cmb2_get_metabox_form( $metaboxes_form, $post_id, array(
			'form_format' => '<form action="' . esc_url(WP_Freeio_Mixes::get_full_current_url()) . '" class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s
			<div class="submit-button-wrapper"><button type="submit" name="submit-cmb-profile" value="%4$s" class="btn btn-theme btn-inverse">%4$s <i class="flaticon-right-up next"></i></button></div></form>',
			'save_button' => esc_html__( 'Save Profile', 'freeio' ),
		) );
	?>
</div>