<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
extract( $args );

extract( $args );
extract( $instance );
echo trim($before_widget);
$title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';

if ( $title ) {
    echo trim($before_title)  .trim( $title ) . $after_title;
}
?>
<div class="widget_search">
    <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
		<input type="text" placeholder="<?php esc_attr_e( 'Search...', 'freeio' ); ?>" name="s" class="apus-search form-control"/>
		<?php if ( isset($post_type) && $post_type ): ?>
			<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" class="post_type" />
		<?php endif; ?>
		<button class="btn btn-search" type="submit"><i class="flaticon-loupe"></i></button>
	</form>
</div>
<?php echo trim($after_widget);