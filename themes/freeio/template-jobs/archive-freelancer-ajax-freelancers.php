<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$display_mode = freeio_get_freelancers_display_mode();
$inner_style = freeio_get_freelancers_inner_style();


$total = $freelancers->found_posts;
$per_page = $freelancers->query_vars['posts_per_page'];
$current = max( 1, $freelancers->get( 'paged', 1 ) );
$last  = min( $total, $per_page * $current );

$pre_page  = max( 0, ($freelancers->get( 'paged', 1 ) - 1 ) );
$i =  $per_page * $pre_page;
?>
<div class="results-count">
	<span class="last"><?php echo esc_html($last); ?></span>
</div>

<div class="items-wrapper">
	<?php if ( $display_mode == 'grid' ) {
		$columns = freeio_get_freelancers_columns();
		$bcol = $columns ? 12/$columns : 4;
	?>
			<?php while ( $freelancers->have_posts() ) : $freelancers->the_post(); ?>
				<div class="col-sm-6 col-md-<?php echo esc_attr($bcol); ?> col-xs-12 <?php echo esc_attr(($i%$columns == 0)?'md-clearfix lg-clearfix':''); ?> <?php echo esc_attr(($i%2 == 0)?'sm-clearfix':''); ?>">
					<?php echo WP_Freeio_Template_Loader::get_template_part( 'freelancers-styles/inner-'.$inner_style ); ?>
				</div>
			<?php $i++; endwhile; ?>
	<?php } else { ?>
		<?php while ( $freelancers->have_posts() ) : $freelancers->the_post(); ?>
			<div class="col-xs-12">
				<?php echo WP_Freeio_Template_Loader::get_template_part( 'freelancers-styles/inner-'.$inner_style ); ?>
			</div>
		<?php endwhile; ?>
	<?php } ?>
</div>

<div class="apus-pagination-next-link"><?php next_posts_link( '&nbsp;', $freelancers->max_num_pages ); ?></div>