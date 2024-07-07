<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
$views = WP_Freeio_Employer::get_post_meta($post->ID, 'views_count', true );
$user_id = WP_Freeio_User::get_user_by_employer_id($post->ID);
$jobs = WP_Freeio_Query::get_posts(array(
    'post_type' => 'job_listing',
    'post_status' => 'publish',
    'author' => $user_id,
    'fields' => 'ids'
));
$count_jobs = $jobs->post_count;

$address = WP_Freeio_Employer::get_post_meta($post->ID, 'address', true );
$categories = get_the_terms( $post->ID, 'employer_category' );
$founded_date = WP_Freeio_Employer::get_post_meta($post->ID, 'founded_date', true );

?>
<div class="employer-detail-detail">
    <h4><?php esc_html_e('Company Information', 'wp-freeio'); ?></h4>
    <ul class="list">
        

        <?php if ( $views ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-eye"></i>
                </div>
                <div class="details">
                    <div class="text"><?php esc_html_e('Views', 'wp-freeio'); ?></div>
                    <div class="value"><?php echo wp_kses_post($views); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $count_jobs ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-label"></i>
                </div>
                <div class="details">
                    <div class="text"><?php esc_html_e('Post jobs', 'wp-freeio'); ?></div>
                    <div class="value"><?php echo wp_kses_post($count_jobs); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $address ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-paper-plane"></i>
                </div>
                <div class="details">
                    <div class="text"><?php esc_html_e('Location', 'wp-freeio'); ?></div>
                    <div class="value"><?php echo wp_kses_post($address); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $categories ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-2-squares"></i>
                </div>
                <div class="details">
                    <div class="text"><?php esc_html_e('Categories', 'wp-freeio'); ?></div>
                    <div class="value">
                        <?php foreach ($categories as $term) { ?>
                            <a href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $founded_date ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-timeline"></i>
                </div>
                <div class="details">
                    <div class="text"><?php esc_html_e('Since', 'wp-freeio'); ?></div>
                    <div class="value"><?php echo wp_kses_post($founded_date); ?></div>
                </div>
            </li>
        <?php } ?>
    </ul>
</div>