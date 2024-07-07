<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php do_action( 'wp_freeio_before_job_detail', get_the_ID() ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('freelancer-single-v1'); ?>>
	<!-- heading -->
	<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/header-pdf' ); ?>

	<!-- Main content -->
	<div class="row">
		<div class="col-sm-12">

			<?php do_action( 'wp_freeio_before_job_content', get_the_ID() ); ?>
			
			
			<!-- job description -->
			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/description' ); ?>
			
			<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/detail' ); ?>

			<?php if ( freeio_get_config('show_freelancer_education', true) ) { ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/education', array('source_pdf' => true) ); ?>
			<?php } ?>

			<?php if ( freeio_get_config('show_freelancer_experience', true) ) { ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/experience', array('source_pdf' => true) ); ?>
			<?php } ?>

			<?php if ( freeio_get_config('show_freelancer_portfolios', true) ) { ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/portfolios-pdf' ); ?>
			<?php } ?>

			<?php if ( freeio_get_config('show_freelancer_skill', true) ) { ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/skill' ); ?>
			<?php } ?>

			<?php if ( freeio_get_config('show_freelancer_award', true) ) { ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'single-freelancer/award', array('source_pdf' => true) ); ?>
			<?php } ?>
			
			<?php do_action( 'wp_freeio_after_job_content', get_the_ID() ); ?>
		</div>
	</div>

</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_job_detail', get_the_ID() ); ?>