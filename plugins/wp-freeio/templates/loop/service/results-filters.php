<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !empty($filters) ) {
	?>
	<div class="results-filter-wrapper">
		<h3 class="title"><?php esc_html_e('Your Selected', 'wp-freeio'); ?></h3>
		<div class="inner">
			<ul class="results-filter">
				<?php foreach ($filters as $key => $value) { ?>
					<?php WP_Freeio_Service_Filter::display_filter_value($key, $value, $filters); ?>
				<?php } ?>
			</ul>
			<a href="<?php echo esc_url(WP_Freeio_Mixes::get_services_page_url()); ?>"><?php esc_html_e('Clear all', 'wp-freeio'); ?></a>
		</div>
	</div>
<?php }