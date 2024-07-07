<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_freeio_before_job_detail', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('job-single'); ?>>

	<!-- heading -->
	<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-job_listing/header' ); ?>

	<div class="job-content-area <?php echo apply_filters('freeio_job_content_class', 'container');?>">
		<!-- Main content -->
		<div class="content-job-detail">
			<div class="row">
				<div class="list-content-job col-12 content-main-service">
					<?php do_action( 'wp_freeio_before_job_content', $post->ID ); ?>
					
					<?php
					if ( freeio_get_config('show_job_detail', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-job_listing/detail' );
					}
					?>

					<!-- job description -->
					<?php
					if ( freeio_get_config('show_job_description', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-job_listing/description' );
					}
					?>
					
					<!-- job social -->
					<?php if ( freeio_get_config('show_job_social_share', false) ) { ?>
						<div class="sharebox-job">
		        			<?php get_template_part( 'template-parts/sharebox-job' );  ?>
		        		</div>
		            <?php } ?>

					<?php
					if ( freeio_get_config('show_job_photos', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-job_listing/photos' );
					}
					?>

					<?php
					if ( freeio_get_config('show_job_video', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-job_listing/video' );
					}
					?>

		            <!-- job related -->
		            <?php
			            if ( freeio_get_config('show_job_related', true) ) {
			            	echo WP_Freeio_Template_Loader::get_template_part( 'single-job_listing/related' );
			            }
		            ?>

					<?php do_action( 'wp_freeio_after_job_content', $post->ID ); ?>
				</div>
			</div>
		</div>
	</div>

</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_job_detail', $post->ID ); ?>