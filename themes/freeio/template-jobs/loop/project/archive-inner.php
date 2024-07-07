<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'wp_freeio_before_project_archive', array( 'WP_Freeio_Project', 'display_project_feed' ), 22 );
add_action( 'wp_freeio_before_project_archive', 'freeio_project_display_filter_btn', 20 );
$display_mode = freeio_get_projects_display_mode();
$inner_style = freeio_get_projects_inner_style();
?>
<div class="projects-listing-wrapper main-items-wrapper" data-display_mode="<?php echo esc_attr($display_mode); ?>" data-settings="">
	<?php
	/**
	 * wp_freeio_before_project_archive
	 */
	do_action( 'wp_freeio_before_project_archive', $projects );
	?>

	<?php if ( !empty($projects) && !empty($projects->posts) ) : ?>
		<?php
		/**
		 * wp_freeio_before_loop_project
		 */
		do_action( 'wp_freeio_before_loop_project', $projects );
		?>
		
		<div class="projects-wrapper items-wrapper">

			<?php if ( $display_mode == 'grid' ) {
				$columns = freeio_get_projects_columns();
				$bcol = $columns ? 12/$columns : 4;
			?>
				<div class="row">
					<?php while ( $projects->have_posts() ) : $projects->the_post(); ?>
						<div class="col-sm-6 col-xl-<?php echo esc_attr($bcol); ?> col-12">
							<?php echo WP_Freeio_Template_Loader::get_template_part( 'projects-styles/inner-'.$inner_style ); ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php } else { ?>
				<div class="row">
					<?php while ( $projects->have_posts() ) : $projects->the_post(); ?>
						<div class="col-12">
							<?php echo WP_Freeio_Template_Loader::get_template_part( 'projects-styles/inner-'.$inner_style ); ?>
						</div>
					<?php endwhile; ?>
				</div>
			<?php } ?>

		</div>

		<?php
		/**
		 * wp_freeio_after_loop_project
		 */
		do_action( 'wp_freeio_after_loop_project', $projects );
		
		wp_reset_postdata();
		?>

	<?php else : ?>
		<div class="not-found"><?php esc_html_e('No project found.', 'freeio'); ?></div>
	<?php endif; ?>

	<?php
	/**
	 * wp_freeio_after_project_archive
	 */
	do_action( 'wp_freeio_after_project_archive', $projects );
	?>
</div>