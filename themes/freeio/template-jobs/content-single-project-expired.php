<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

$icon = freeio_get_config('project_expired_icon_img');

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('job-single-expired'); ?>>


	<div class="job-content-area container">
		<!-- Main content -->
		<div class="content-job-detail text-center">
			<?php if ( !empty($icon) ) { ?>
				<div class="top-image">
					<img src="<?php echo esc_url($icon); ?>">
				</div>
			<?php } ?>
			<h1>
				<?php
				$title = freeio_get_config('project_expired_title');
				if ( !empty($title) ) {
					echo esc_html($title);
				} else {
					esc_html_e('We\'re Sorry Opps! Project Expired', 'freeio');
				}
				?>
			</h1>
		   	<div class="content">
		   		<?php
				$description = freeio_get_config('project_expired_description');
				if ( !empty($description) ) {
					echo esc_html($description);
				} else {
					esc_html_e('Unable to access the link. Project has been expired. Please contact the admin or who shared the link with you.', 'freeio');
				}
				?>
		   	</div>

		   	<div class="return mt-4">
				<a class="btn-theme btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__('Go To Home Page', 'freeio') ?></a>
			</div>
		</div>
	</div>

</article><!-- #post-## -->
