<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="employers-listing-wrapper">
	<?php
	/**
	 * wp_freeio_before_employer_archive
	 */
	do_action( 'wp_freeio_before_employer_archive', $employers );
	?>

	<?php
	if ( !empty($employers) && !empty($employers->posts) ) {

		/**
		 * wp_freeio_before_loop_employer
		 */
		do_action( 'wp_freeio_before_loop_employer', $employers );
		?>

		<div class="employers-wrapper">
			<?php while ( $employers->have_posts() ) : $employers->the_post(); ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'employers-styles/inner-grid' ); ?>
			<?php endwhile;?>
		</div>

		<?php
		/**
		 * wp_freeio_after_loop_employer
		 */
		do_action( 'wp_freeio_after_loop_employer', $employers );

		WP_Freeio_Mixes::custom_pagination( array(
			'max_num_pages' => $employers->max_num_pages,
			'prev_text'          => __( 'Previous page', 'wp-freeio' ),
			'next_text'          => __( 'Next page', 'wp-freeio' ),
			'wp_query' => $employers
		));

		wp_reset_postdata();
	?>

	<?php } else { ?>
		<div class="not-found"><?php esc_html_e('No employer found.', 'wp-freeio'); ?></div>
	<?php } ?>

	<?php
	/**
	 * wp_freeio_after_employer_archive
	 */
	do_action( 'wp_freeio_after_employer_archive', $employers );
	?>
</div>