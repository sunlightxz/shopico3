<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'wp_freeio_before_job_archive', array( 'WP_Freeio_Job_Listing', 'display_job_feed' ), 22 );
remove_action( 'wp_freeio_before_job_archive', array( 'WP_Freeio_Job_Listing', 'display_jobs_alert_form' ), 20 );

add_action( 'wp_freeio_before_job_archive', 'freeio_job_display_filter_btn', 19 );

$display_mode = freeio_get_jobs_display_mode();
$inner_style = freeio_get_jobs_inner_style();
?>
<div class="jobs-listing-wrapper main-items-wrapper" data-display_mode="<?php echo esc_attr($jobs_display_mode); ?>" data-settings="">
	<?php
	/**
	 * wp_freeio_before_job_archive
	 */
	do_action( 'wp_freeio_before_job_archive', $jobs );
	?>

	<?php if ( !empty($jobs) && !empty($jobs->posts) ) : ?>
		<?php
		/**
		 * wp_freeio_before_loop_job
		 */
		do_action( 'wp_freeio_before_loop_job', $jobs );
		?>
		
		<div class="jobs-wrapper items-wrapper">

			<?php if ( $display_mode == 'grid' ) {
				$columns = freeio_get_jobs_columns();
				$bcol = $columns ? 12/$columns : 4;
			?>
				<div class="row">
					<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
						<div class="col-md-6 col-xl-<?php echo esc_attr($bcol); ?> col-12">
							<?php echo WP_Freeio_Template_Loader::get_template_part( 'jobs-styles/inner-'.$inner_style ); ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php } else { ?>
				<div class="row">
					<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
						<div class="col-12">
							<?php echo WP_Freeio_Template_Loader::get_template_part( 'jobs-styles/inner-'.$inner_style ); ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php } ?>

		</div>

		<?php
		/**
		 * wp_freeio_after_loop_job
		 */
		do_action( 'wp_freeio_after_loop_job', $jobs );
		
		wp_reset_postdata();
		?>

	<?php else : ?>
		<div class="not-found"><?php esc_html_e('No job found.', 'freeio'); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * wp_freeio_after_job_archive
	 */
	do_action( 'wp_freeio_after_job_archive', $jobs );
	?>
</div>