<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


?>
<div class="services-listing-wrapper">
	<?php
	/**
	 * wp_freeio_before_service_archive
	 */
	do_action( 'wp_freeio_before_service_archive', $services );
	?>

	<?php if ( $services->have_posts() ) : ?>
		<?php
		/**
		 * wp_freeio_before_loop_service
		 */
		do_action( 'wp_freeio_before_loop_service', $services );
		?>

		<div class="services-wrapper">
			<?php while ( $services->have_posts() ) : $services->the_post(); ?>
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'services-styles/inner-list' ); ?>
			<?php endwhile; ?>
		</div>

		<?php
		/**
		 * wp_freeio_after_loop_service
		 */
		do_action( 'wp_freeio_after_loop_service', $services );

		WP_Freeio_Mixes::custom_pagination( array(
			'max_num_pages' => $services->max_num_pages,
			'prev_text'     => esc_html__( 'Previous page', 'wp-freeio' ),
			'next_text'     => esc_html__( 'Next page', 'wp-freeio' ),
			'wp_query' => $services
		));
		?>

	<?php else : ?>
		<div class="not-found"><?php esc_html_e('No service found.', 'wp-freeio'); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * wp_freeio_before_service_archive
	 */
	do_action( 'wp_freeio_before_service_archive', $services );
	?>
</div>