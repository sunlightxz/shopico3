<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$job_inner_style = !empty($settings['job_item_style']) ? $settings['job_item_style'] : 'grid';

$columns = !empty($settings['columns']) ? $settings['columns'] : 3;
$columns_tablet_extra = !empty($settings['columns_tablet_extra']) ? $settings['columns_tablet_extra'] : $columns;
$columns_tablet = !empty($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
$columns_mobile = !empty($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;

$mdcol = 12/$columns;
$columns_tablet_extra = 12/$columns_tablet_extra;
$smcol = 12/$columns_tablet;
$xscol = 12/$columns_mobile;

?>
<div class="jobs-listing-wrapper main-items-wrapper" data-settings="<?php echo esc_attr(json_encode($settings)); ?>">
	
	<?php if ( !empty($jobs) && !empty($jobs->posts) ) : ?>
		
		<div class="jobs-wrapper items-wrapper">

			<div class="row">
				<?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
					<div class="item-job col-xl-<?php echo esc_attr($mdcol); ?> col-tablet-extra-<?php echo esc_attr($columns_tablet_extra); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr( $xscol ); ?>">
						<?php echo WP_Freeio_Template_Loader::get_template_part( 'jobs-styles/inner-'.$job_inner_style ); ?>
					</div>
				<?php endwhile; ?>
			</div>
		</div>

		<?php
		wp_reset_postdata();
		?>

	<?php else : ?>
		<div class="not-found"><?php esc_html_e('No job found.', 'freeio'); ?></div>
	<?php endif; ?>

</div>