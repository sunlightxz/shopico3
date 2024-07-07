<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;
?>

<?php do_action( 'wp_freeio_before_job_detail', get_the_ID() ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<!-- heading -->
	<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/header' ); ?>

	<!-- Main content -->
	<div class="row">
		<div class="col-sm-9">

			<?php do_action( 'wp_freeio_before_job_content', get_the_ID() ); ?>
			<!-- job description -->
			<div class="job-detail-description">
				<h3><?php esc_html_e('About Me', 'wp-freeio'); ?></h3>
				<div class="inner">
					<?php the_content(); ?>
				</div>
			</div>

			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/education' ); ?>

			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/experience' ); ?>

			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/portfolios' ); ?>

			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/skill' ); ?>

			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/award' ); ?>

			<?php if ( (comments_open() || get_comments_number()) && WP_Freeio_Freelancer::check_restrict_review($post) ) : ?>
				<!-- Review -->
				<?php comments_template(); ?>
			<?php endif; ?>

			<?php do_action( 'wp_freeio_after_job_content', get_the_ID() ); ?>
		</div>
		<div class="col-sm-3">
			<?php do_action( 'wp_freeio_before_job_sidebar', get_the_ID() ); ?>
			<!-- job detail -->
			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/detail' ); ?>
			
			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/cv_attachments' ); ?>

			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/map-location' ); ?>
			
			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/contact-form' ); ?>

			<?php do_action( 'wp_freeio_after_job_sidebar', get_the_ID() ); ?>
		</div>
	</div>

</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_job_detail', get_the_ID() ); ?>