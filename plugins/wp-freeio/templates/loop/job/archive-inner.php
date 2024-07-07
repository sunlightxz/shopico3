<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


?>
<div class="jobs-listing-wrapper">
	<?php
	/**
	 * wp_freeio_before_job_archive
	 */
	do_action( 'wp_freeio_before_job_archive', $jobs );
	?>

	<?php if ( $jobs->have_posts() ) : ?>
		<?php
		/**
		 * wp_freeio_before_loop_job
		 */
		do_action( 'wp_freeio_before_loop_job', $jobs );
		?>

		<div class="jobs-wrapper">
			<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'jobs-styles/inner-list' ); ?>
			<?php endwhile; ?>
		</div>

		<?php
		/**
		 * wp_freeio_after_loop_job
		 */
		do_action( 'wp_freeio_after_loop_job', $jobs );

		WP_Freeio_Mixes::custom_pagination( array(
			'max_num_pages' => $jobs->max_num_pages,
			'prev_text'     => esc_html__( 'Previous page', 'wp-freeio' ),
			'next_text'     => esc_html__( 'Next page', 'wp-freeio' ),
			'wp_query' => $jobs
		));
		?>

	<?php else : ?>
		<div class="not-found"><?php esc_html_e('No job found.', 'wp-freeio'); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * wp_freeio_before_job_archive
	 */
	do_action( 'wp_freeio_before_job_archive', $jobs );
	?>
</div>