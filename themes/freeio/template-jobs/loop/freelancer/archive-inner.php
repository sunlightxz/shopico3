<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//remove_action( 'wp_freeio_before_freelancer_archive', array( 'WP_Freeio_Freelancer', 'display_freelancers_count_results' ), 10 );
remove_action( 'wp_freeio_before_freelancer_archive', array( 'WP_Freeio_Freelancer', 'display_freelancers_alert_form' ), 20 );

add_action( 'wp_freeio_before_freelancer_archive', 'freeio_freelancer_display_filter_btn', 20 );


$display_mode = freeio_get_freelancers_display_mode();
$inner_style = freeio_get_freelancers_inner_style();
?>
<div class="freelancers-listing-wrapper main-items-wrapper layout-type-<?php echo esc_attr($display_mode); ?>" data-display_mode="<?php echo esc_attr($display_mode); ?>" data-settings="">
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

		<div class="freelancers-wrapper items-wrapper">
			<?php if ( $display_mode == 'grid' ) {
				$columns = freeio_get_freelancers_columns();
				$bcol = $columns ? 12/$columns : 4;
			?>
				<div class="row">
					<?php while ( $freelancers->have_posts() ) : $freelancers->the_post(); ?>
						<div class="col-sm-6 <?php echo esc_attr( ($columns > 3)?'col-lg-4':'' ); ?> col-xl-<?php echo esc_attr($bcol); ?> col-12">
							<?php echo WP_Freeio_Template_Loader::get_template_part( 'freelancers-styles/inner-'.$inner_style ); ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php } else { ?>
				<div class="row">
					<?php while ( $freelancers->have_posts() ) : $freelancers->the_post(); ?>
						<div class="col-12">
							<?php echo WP_Freeio_Template_Loader::get_template_part( 'freelancers-styles/inner-'.$inner_style ); ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php } ?>
		</div>

		<?php
		/**
		 * wp_freeio_after_loop_freelancer
		 */
		do_action( 'wp_freeio_after_loop_freelancer', $freelancers );

		wp_reset_postdata();
	?>

	<?php } else { ?>
		<div class="not-found"><?php esc_html_e('No freelancer found.', 'freeio'); ?></div>
	<?php } ?>

	<?php
	/**
	 * wp_freeio_after_freelancer_archive
	 */
	do_action( 'wp_freeio_after_freelancer_archive', $freelancers );
	?>
</div>