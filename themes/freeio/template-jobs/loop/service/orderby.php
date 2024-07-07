<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$orderby_options = apply_filters( 'wp-freeio-services-orderby', array(
	'menu_order' => esc_html__('Sort by (Default)', 'freeio'),
	'newest' => esc_html__('Newest', 'freeio'),
	'oldest' => esc_html__('Oldest', 'freeio'),
	'random' => esc_html__('Random', 'freeio'),
));
$orderby = isset( $_GET['filter-orderby'] ) ? wp_unslash( $_GET['filter-orderby'] ) : 'menu_order';
if ( !WP_Freeio_Mixes::is_ajax_request() ) {
	freeio_load_select2();
}
?>
<div class="services-ordering-wrapper">
	<form class="services-ordering jobs-ordering" method="get" action="<?php echo WP_Freeio_Mixes::get_services_page_url(); ?>">
		<select name="filter-orderby" class="orderby" data-placeholder="<?php esc_attr_e('Sort by', 'freeio'); ?>">
			<?php foreach ( $orderby_options as $id => $name ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>
		</select>
		<input type="hidden" name="paged" value="1" />
		<?php WP_Freeio_Mixes::query_string_form_fields( null, array( 'filter-orderby', 'submit', 'paged' ) ); ?>
	</form>
</div>
