<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty($userdata) ) {
    return;
}
?>

<?php do_action( 'wp_freeio_before_employee_content', $userdata ); ?>

<article class="employee-team-wrapper">
    <div class="employee-team">
        <div class="employee-thumbnail">
            <?php echo get_avatar( $userdata->ID, 'thumbnail' ); ?>
        </div>
        <div class="employee-information">
        	<h2 class="entry-title employee-title">
                <?php echo $userdata->display_name; ?>
            </h2>
    	</div>

        <a href="javascript:void(0);" class="btn btn-employer-remove-employee" data-employee_id="<?php echo esc_attr($userdata->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-employer-remove-employee-nonce' )); ?>"><?php esc_html_e('Remove', 'wp-freeio'); ?></a>
    </div>
</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_employee_content', $userdata ); ?>