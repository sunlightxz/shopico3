<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'wp_freeio_before_employer_archive', array( 'WP_Freeio_Employer', 'display_employers_results_count_orderby_start' ), 5 );
add_action( 'wp_freeio_before_employer_archive', array( 'WP_Freeio_Employer', 'display_employers_results_count_orderby_start' ), 14 );

add_action( 'wp_freeio_before_employer_archive', 'freeio_employer_display_filter_btn', 14 );

$display_mode = freeio_get_employers_display_mode();
$inner_style = freeio_get_employers_inner_style();
$columns = freeio_get_employers_columns();
$bcol = $columns ? 12/$columns : 4;
?>
<div class="employers-listing-wrapper main-items-wrapper layout-type-<?php echo esc_attr($display_mode); ?>" data-display_mode="<?php echo esc_attr($display_mode); ?>" data-settings="">
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

		<div class="employers-wrapper items-wrapper">
			<div class="row">
				<?php while ( $employers->have_posts() ) : $employers->the_post(); ?>
					<div class="col-sm-6 <?php echo esc_attr( ($columns > 3)?'col-lg-4':'' ); ?> col-xl-<?php echo esc_attr($bcol); ?> col-12">
						<?php echo WP_Freeio_Template_Loader::get_template_part( 'employers-styles/inner-'.$inner_style ); ?>
					</div>
				<?php endwhile; ?>
			</div>
		</div>

		<?php
		/**
		 * wp_freeio_after_loop_employer
		 */
		do_action( 'wp_freeio_after_loop_employer', $employers );

		wp_reset_postdata();
	?>

	<?php } else { ?>
		<div class="not-found"><?php esc_html_e('No employer found.', 'freeio'); ?></div>
	<?php } ?>

	<?php
	/**
	 * wp_freeio_after_employer_archive
	 */
	do_action( 'wp_freeio_after_employer_archive', $employers );
	?>
</div>