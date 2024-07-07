<?php
/**
 *
 * Search form.
 * @since 1.0.0
 * @version 1.0.0
 *
 */
?>
<div class="widget_search">
	<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
		<input type="text" placeholder="<?php esc_attr_e( 'Search...', 'freeio' ); ?>" name="s" class="form-control"/>
		<input type="hidden" name="post_type" value="post" class="post_type" />
        <button class="btn btn-search" type="submit"><i class="flaticon-loupe"></i></button>
	</form>
</div>