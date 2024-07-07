<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'wp_freeio_before_job_detail', get_the_ID() ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('freelancer-single-v1'); ?>>
	<!-- heading -->
	<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/header' ); ?>

	<!-- Main content -->
	<div class="row">
		<div class="col-sm-12">

			<?php do_action( 'wp_freeio_before_job_content', get_the_ID() ); ?>
			
			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/detail' ); ?>

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

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<!-- Review -->
				<?php comments_template(); ?>
			<?php endif; ?>

			<?php do_action( 'wp_freeio_after_job_content', get_the_ID() ); ?>
		</div>
	</div>

</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_job_detail', get_the_ID() ); ?>