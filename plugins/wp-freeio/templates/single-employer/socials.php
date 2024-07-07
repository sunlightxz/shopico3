<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

if ( !WP_Freeio_Employer::check_restrict_view_contact_info($post) && wp_freeio_get_option('restrict_contact_employer_social', 'on') == 'on' ) {
    return;
}

$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);

if ( $meta_obj->check_post_meta_exist('socials') && ($socials = $meta_obj->get_post_meta( 'socials' )) ) {
    $all_socials = WP_Freeio_Mixes::get_socials_network();
    ob_start();
    ?>
    <?php foreach ($socials as $social) {
        if ( !empty($social['network']) && !empty($social['url']) ) {
            $icon_class = !empty( $all_socials[$social['network']]['icon'] ) ? $all_socials[$social['network']]['icon'] : '';
    ?>
        <a href="<?php echo esc_url($social['url']); ?>"><i class="<?php echo esc_attr($icon_class); ?>"></i></a>
        <?php } ?>
    <?php }
    $output = ob_get_clean();
    $output = trim($output);
    if ( !empty($output) ) {
    ?>

        <div class="social-job-detail">
            <span class="title">
                <?php esc_html_e('Social Profiles:', 'wp-freeio'); ?>
            </span>
            <?php echo wp_kses_post($output); ?>
        </div>
    <?php
    }
}
?>