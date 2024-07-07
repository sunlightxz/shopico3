<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$service_inner_style = freeio_get_services_inner_style();


$total = $services->found_posts;
$per_page = $services->query_vars['posts_per_page'];
$current = max( 1, $services->get( 'paged', 1 ) );
$last  = min( $total, $per_page * $current );

$pre_page  = max( 0, ($services->get( 'paged', 1 ) - 1 ) );
$i =  $per_page * $pre_page;
?>
<div class="results-count">
	<span class="last"><?php echo esc_html($last); ?></span>
</div>

<div class="items-wrapper">
	<?php
		$columns = freeio_get_services_columns();
		$bcol = $columns ? 12/$columns : 4;
	?>
	<?php while ( $services->have_posts() ) : $services->the_post(); ?>
		<div class="item-service <?php echo esc_attr($columns > 1 ? 'col-sm-6' : 'col-sm-12'); ?> col-md-<?php echo esc_attr($bcol); ?> col-12">
			<?php echo WP_Freeio_Template_Loader::get_template_part( 'services-styles/inner-'.$service_inner_style ); ?>
		</div>
	<?php $i++; endwhile; ?>

</div>

<div class="apus-pagination-next-link"><?php next_posts_link( '&nbsp;', $services->max_num_pages ); ?></div>