<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="jobs-pagination-wrapper main-pagination-wrapper">
	<?php
		$pagination_type = freeio_get_jobs_pagination();
		if ( $pagination_type == 'loadmore' || $pagination_type == 'infinite' ) {
			$next_link = get_next_posts_link( '&nbsp;', $jobs->max_num_pages );
			if ( $next_link ) {
		?>
				<div class="ajax-pagination <?php echo trim($pagination_type == 'loadmore' ? 'loadmore-action' : 'infinite-action'); ?>">
					<div class="apus-pagination-next-link hidden"><?php echo trim($next_link); ?></div>
					<a href="#" class="apus-loadmore-btn"><?php esc_html_e( 'Load more', 'freeio' ); ?></a>
					<span href="#" class="apus-allproducts"><?php esc_html_e( 'All jobs loaded.', 'freeio' ); ?></span>
				</div>
		<?php
			}
		} else {
			WP_Freeio_Mixes::custom_pagination( array(
				'wp_query' => $jobs,
				'max_num_pages' => $jobs->max_num_pages,
				'prev_text'     => '<i class="ti-angle-left"></i>',
				'next_text'     => '<i class="ti-angle-right"></i>',
			));
		}
	?>
</div>