<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>
<?php do_action( 'wp_freeio_before_freelancer_detail', get_the_ID() ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('freelancer-single'); ?>>
	
	<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/header' ); ?>

	<div class="freelancer-content-area container">
		<!-- Main content -->
		<div class="row content-single-freelancer content-service-detail">
			<div class="col-12 list-content-freelancer list-content-service col-lg-<?php echo esc_attr( is_active_sidebar( 'freelancer-single-sidebar' ) ? 8 : 12); ?>">
				<div class="content-main-service content-main-freelancer">
					<?php do_action( 'wp_freeio_before_job_content', get_the_ID() ); ?>
					
					<?php
					if ( freeio_get_config('show_freelancer_detail', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/details' );
					}
					?>
					
					<?php
					if ( freeio_get_config('show_freelancer_description', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/description' );
					}
					?>

					<?php if ( freeio_get_config('show_freelancer_gallery', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/gallery' );
					} ?>

					<?php if ( freeio_get_config('show_freelancer_video', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/video' );
					} ?>

					<?php if ( freeio_get_config('show_freelancer_education', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/education' );
					} ?>

					<?php if ( freeio_get_config('show_freelancer_experience', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/experience' );
					} ?>
					
					<?php if ( freeio_get_config('show_freelancer_award', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/award' );
					} ?>

					<?php if ( freeio_get_config('show_freelancer_skill', true) ) {
						echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/skill' );
					} ?>

					<?php
						if ( freeio_get_config('show_freelancer_services', true) ) {
							echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/services' );
						}
					?>

					<?php if ( is_active_sidebar( 'freelancer-single-sidebar' ) ): ?>
						<div class="sidebar-wrapper sidebar-service col-lg-4 col-12 d-block d-lg-none">
							<aside class="sidebar sidebar-listing-detail sidebar-right">
					   			<?php dynamic_sidebar( 'freelancer-single-sidebar' ); ?>
					   		</aside>
					   	</div>
				   	<?php endif; ?>

					<?php if ( freeio_check_post_review($post) ) : ?>
						<!-- Review -->
						<?php comments_template(); ?>
					<?php endif; ?>

					<?php do_action( 'wp_freeio_after_freelancer_content', get_the_ID() ); ?>
				</div>
			</div>
			<?php if ( is_active_sidebar( 'freelancer-single-sidebar' ) ): ?>
				<div class="sidebar-wrapper sidebar-service col-lg-4 col-12 d-none d-lg-block">
					<aside class="sidebar sidebar-listing-detail sidebar-right sticky-top">
			   			<?php dynamic_sidebar( 'freelancer-single-sidebar' ); ?>
			   		</aside>
			   	</div>
		   	<?php endif; ?>

		</div>

		<?php
			if ( freeio_get_config('show_freelancer_related', true) ) {
				echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/related' );
			}
		?>
	</div>
</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_job_detail', get_the_ID() ); ?>