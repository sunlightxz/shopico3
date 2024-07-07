<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

$icon = freeio_get_config('freelancer_expired_icon_img');

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('freelancer-single-expired'); ?>>


	<div class="freelancer-content-area container">
		<!-- Main content -->
		<div class="content-single-freelancer text-center">
			<?php if ( !empty($icon['id']) ) { ?>
				<div class="top-image">
					<?php echo freeio_get_attachment_thumbnail($icon['id'], 'full'); ?>
				</div>
			<?php } ?>
			<h1>
				<?php
				$title = freeio_get_config('freelancer_expired_title');
				if ( !empty($title) ) {
					echo esc_html($title);
				} else {
					esc_html_e('We\'re Sorry Opps! Freelancer Expired', 'freeio');
				}
				?>
			</h1>
		   	<div class="content">
		   		<?php
				$description = freeio_get_config('freelancer_expired_description');
				if ( !empty($description) ) {
					echo esc_html($description);
				} else {
					esc_html_e('Unable to access the link. Freelancer has been expired. Please contact the admin or who shared the link with you.', 'freeio');
				}
				?>
		   	</div>

		   	<div class="return margin-top-30">
				<a class="btn-theme btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__('Go To Home Page', 'freeio') ?></a>
			</div>
		</div>
	</div>

</article><!-- #post-## -->
