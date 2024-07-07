<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="freelancers-listing-wrapper">
	<?php
	/**
	 * wp_freeio_before_freelancer_archive
	 */
	do_action( 'wp_freeio_before_freelancer_archive', $freelancers );
	?>
	<?php
	if ( !empty($freelancers) && !empty($freelancers->posts) ) {

		/**
		 * wp_freeio_before_loop_freelancer
		 */
		do_action( 'wp_freeio_before_loop_freelancer', $freelancers );
		?>

		<div class="freelancers-wrapper">
			<?php while ( $freelancers->have_posts() ) : $freelancers->the_post(); ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'freelancers-styles/inner-grid' ); ?>
			<?php endwhile;?>
		</div>

		<?php
		/**
		 * wp_freeio_after_loop_freelancer
		 */
		do_action( 'wp_freeio_after_loop_freelancer', $freelancers );

		WP_Freeio_Mixes::custom_pagination( array(
			'max_num_pages' => $freelancers->max_num_pages,
			'prev_text'          => __( 'Previous page', 'wp-freeio' ),
			'next_text'          => __( 'Next page', 'wp-freeio' ),
			'wp_query' => $freelancers
		));

		wp_reset_postdata();
	?>

	<?php } else { ?>
		<div class="not-found"><?php esc_html_e('No freelancer found.', 'wp-freeio'); ?></div>
	<?php } ?>

	<?php
	/**
	 * wp_freeio_after_freelancer_archive
	 */
	do_action( 'wp_freeio_after_freelancer_archive', $freelancers );
	?>
</div>