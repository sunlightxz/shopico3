<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$project_inner_style = !empty($settings['project_item_style']) ? $settings['project_item_style'] : 'grid';

$columns = !empty($settings['columns']) ? $settings['columns'] : 3;
$columns_tablet = !empty($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
$columns_mobile = !empty($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;

$mdcol = 12/$columns;
$smcol = 12/$columns_tablet;
$xscol = 12/$columns_mobile;

$total = $projects->found_posts;
$per_page = $projects->query_vars['posts_per_page'];
$current = max( 1, $projects->get( 'paged', 1 ) );
$last  = min( $total, $per_page * $current );


?>
<div class="results-count">
	<span class="last"><?php echo esc_html($last); ?></span>
</div>

<div class="items-wrapper">
	<?php while ( $projects->have_posts() ) : $projects->the_post(); ?>
		<div class="item-project col-xl-<?php echo esc_attr($mdcol); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr( $xscol ); ?>">
			<?php echo WP_Freeio_Template_Loader::get_template_part( 'projects-styles/inner-'.$project_inner_style ); ?>
		</div>
	<?php endwhile; ?>

</div>

<div class="apus-pagination-next-link"><?php next_posts_link( '&nbsp;', $projects->max_num_pages ); ?></div>