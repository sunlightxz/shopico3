<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_freeio_before_employer_detail', $post->ID ); ?>

<article id="post-<?php echo esc_attr($post->ID); ?>" <?php post_class('employer-single '); ?>>
	<!-- heading -->
	<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-employer/header' ); ?>

	<div class="employer-content-area <?php echo apply_filters('freeio_employer_content_class', 'container');?>">
		<!-- Main content -->
		<div class="row content-single-employer content-service-detail">
			<div class="col-12 list-content-employer list-content-service col-lg-<?php echo esc_attr( is_active_sidebar( 'employer-single-sidebar' ) ? 8 : 12); ?>">
				<div class="content-main-service content-main-employer">
					<?php do_action( 'wp_freeio_before_employer_content', $post->ID ); ?>

					<!-- employer description -->
					<?php
					if ( freeio_get_config('show_employer_description', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-employer/description' );
					}
					?>
					
					<?php if ( freeio_get_config('show_employer_gallery', true) ) { ?>
						<!-- profile photos -->
						<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-employer/gallery' ); ?>
					<?php } ?>

					<?php if ( freeio_get_config('show_employer_video', true) ) { ?>
						<!-- Video -->
						<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-employer/video' ); ?>
					<?php } ?>
					
					<?php if ( freeio_get_config('show_employer_projects', true) ) { ?>
						<!-- employer related -->
						<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-employer/projects' ); ?>
					<?php } ?>

					<?php if ( freeio_get_config('show_employer_open_jobs', true) ) { ?>
						<!-- employer related -->
						<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-employer/open-jobs' ); ?>
					<?php } ?>

					<?php if ( freeio_get_config('show_employer_members', true) ) { ?>
						<!-- team member -->
						<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-employer/members' ); ?>
					<?php } ?>

					<?php if ( is_active_sidebar( 'employer-single-sidebar' ) ): ?>
						<div class="sidebar-wrapper sidebar-service col-lg-4 col-12 d-block d-lg-none">
							<aside class="sidebar sidebar-listing-detail sidebar-right">
						   		<?php dynamic_sidebar( 'employer-single-sidebar' ); ?>
						   	</aside>
					   	</div>
				   	<?php endif; ?>
				
					<?php if ( freeio_check_post_review($post) ) : ?>
						<!-- Review -->
						<?php comments_template(); ?>
					<?php endif; ?>

					<?php do_action( 'wp_freeio_after_employer_content', $post->ID ); ?>
				</div>
			</div>
			<?php if ( is_active_sidebar( 'employer-single-sidebar' ) ): ?>
				<div class="sidebar-wrapper sidebar-service col-lg-4 col-12 d-none d-lg-block">
					<aside class="sidebar sidebar-right sidebar-listing-detail sticky-top">
				   		<?php dynamic_sidebar( 'employer-single-sidebar' ); ?>
				   	</aside>
			   	</div>
		   	<?php endif; ?>
		</div>
	</div>

</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_job_detail', $post->ID ); ?>