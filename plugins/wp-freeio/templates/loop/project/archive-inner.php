<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


?>
<div class="projects-listing-wrapper">
	<?php
	/**
	 * wp_freeio_before_project_archive
	 */
	do_action( 'wp_freeio_before_project_archive', $projects );
	?>

	<?php if ( $projects->have_posts() ) : ?>
		<?php
		/**
		 * wp_freeio_before_loop_project
		 */
		do_action( 'wp_freeio_before_loop_project', $projects );
		?>

		<div class="projects-wrapper">
			<?php while ( $projects->have_posts() ) : $projects->the_post(); ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'projects-styles/inner-list' ); ?>
			<?php endwhile; ?>
		</div>

		<?php
		/**
		 * wp_freeio_after_loop_project
		 */
		do_action( 'wp_freeio_after_loop_project', $projects );

		WP_Freeio_Mixes::custom_pagination( array(
			'max_num_pages' => $projects->max_num_pages,
			'prev_text'     => esc_html__( 'Previous page', 'wp-freeio' ),
			'next_text'     => esc_html__( 'Next page', 'wp-freeio' ),
			'wp_query' => $projects
		));
		?>

	<?php else : ?>
		<div class="not-found"><?php esc_html_e('No project found.', 'wp-freeio'); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * wp_freeio_before_project_archive
	 */
	do_action( 'wp_freeio_before_project_archive', $projects );
	?>
</div>