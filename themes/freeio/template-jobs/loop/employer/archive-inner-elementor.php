<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$employer_inner_style = !empty($settings['employer_item_style']) ? $settings['employer_item_style'] : 'grid';

$columns = !empty($settings['columns']) ? $settings['columns'] : 3;
$columns_tablet_extra = !empty($settings['columns_tablet_extra']) ? $settings['columns_tablet_extra'] : $columns;
$columns_tablet = !empty($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
$columns_mobile = !empty($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;

$mdcol = 12/$columns;
$columns_tablet_extra = 12/$columns_tablet_extra;
$smcol = 12/$columns_tablet;
$xscol = 12/$columns_mobile;
?>
<div class="employers-listing-wrapper main-items-wrapper" data-settings="<?php echo esc_attr(json_encode($settings)); ?>">
	
	<?php
	if ( !empty($employers) && !empty($employers->posts) ) {

		?>

		<div class="employers-wrapper items-wrapper">
			<div class="row">
				<?php while ( $employers->have_posts() ) : $employers->the_post(); ?>
					<div class="item-employer col-xl-<?php echo esc_attr($mdcol); ?> col-tablet-extra-<?php echo esc_attr($columns_tablet_extra); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr( $xscol ); ?>">
						<?php echo WP_Freeio_Template_Loader::get_template_part( 'employers-styles/inner-'.$employer_inner_style ); ?>
					</div>
				<?php endwhile; ?>
			</div>
		</div>

		<?php
		wp_reset_postdata();
	?>

	<?php } else { ?>
		<div class="not-found"><?php esc_html_e('No employer found.', 'freeio'); ?></div>
	<?php } ?>

</div>