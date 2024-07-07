<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'wp_freeio_before_service_archive', array( 'WP_Freeio_Service', 'display_service_feed' ), 22 );

add_action( 'wp_freeio_before_service_archive', 'freeio_service_display_filter_btn', 19 );

?>
<div class="services-listing-wrapper main-items-wrapper" data-display_mode="<?php echo esc_attr($services_display_mode); ?>" data-settings="">
	<?php
	/**
	 * wp_freeio_before_service_archive
	 */
	do_action( 'wp_freeio_before_service_archive', $services );
	?>

	<?php if ( !empty($services) && !empty($services->posts) ) : ?>
		<?php
		/**
		 * wp_freeio_before_loop_service
		 */
		do_action( 'wp_freeio_before_loop_service', $services );
		?>
		
		<div class="services-wrapper items-wrapper">
			<?php 
				$columns = freeio_get_services_columns();
				$bcol = $columns ? 12/$columns : 4;
			?>
			<div class="row items-wrapper-<?php echo esc_attr($service_inner_style); ?>">
				<?php while ( $services->have_posts() ) : $services->the_post(); ?>
					<div class="item-service <?php echo esc_attr($columns > 1 ? 'col-md-6' : 'col-md-12'); ?> <?php echo esc_attr($columns > 2 ? 'col-lg-4' : ''); ?> col-xl-<?php echo esc_attr($bcol); ?> col-12">
						<?php echo WP_Freeio_Template_Loader::get_template_part( 'services-styles/inner-'.$service_inner_style ); ?>
					</div>
				<?php endwhile; ?>
			</div>
		</div>

		<?php
		/**
		 * wp_freeio_after_loop_service
		 */
		do_action( 'wp_freeio_after_loop_service', $services );
		
		wp_reset_postdata();
		?>

	<?php else : ?>
		<div class="not-found"><?php esc_html_e('No service found.', 'freeio'); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * wp_freeio_after_service_archive
	 */
	do_action( 'wp_freeio_after_service_archive', $services );
	?>
</div>