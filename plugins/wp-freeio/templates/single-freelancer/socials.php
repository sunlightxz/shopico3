<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$emp_post_meta = WP_Freeio_Employer_Meta::get_instance($post->ID);
$socials = $emp_post_meta->get_post_meta('socials');

if ( !empty($socials) ) {
    ?>
    <div class="freelancer-detail-socials">
        <div class="label">
            <?php esc_html_e('Social Profiles:', 'wp-freeio'); ?>
        </div>
        <ul class="list">
            <?php foreach ($socials as $social) {
                if ( !empty($social['network']) && !empty($social['url']) ) {
            ?>
                <li><a href="<?php echo esc_url($social['url']); ?>"><i class="fa fa-<?php echo esc_attr($social['network']); ?>"></a></li>
            <?php } ?>
            <?php } ?>
        </ul>
    </div>
    <?php
}
?>