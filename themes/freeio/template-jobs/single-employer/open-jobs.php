<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$limit = freeio_get_config('employer_number_open_jobs', 3);

$user_id = WP_Freeio_User::get_user_by_employer_id($post->ID);
$args = array(
    'post_type' => 'job_listing',
    'post_status' => 'publish',
    'post_per_page' => $limit,
    'author' => $user_id
);
$jobs = WP_Freeio_Query::get_posts( $args );

if( $jobs->have_posts() ):
    $jobs_url = WP_Freeio_Mixes::get_jobs_page_url();
    $jobs_url = add_query_arg( 'filter-author', $user_id, remove_query_arg( 'filter-author', $jobs_url ) );
?>
    <div class="widget-open-jobs">
        <div class="top-info-widget d-sm-flex align-items-end">
            <h4 class="title">
                <?php esc_html_e( 'Open Position', 'freeio' ); ?>
            </h4>
            <div class="ms-auto">
                <a href="<?php echo esc_url($jobs_url); ?>" class="text-theme view_all">
                    <?php esc_html_e('Browse Full List', 'freeio'); ?> <i class="flaticon-right-up next"></i>
                </a>
            </div>
        </div>
        <div class="widget-content">
            <?php
                while ( $jobs->have_posts() ) : $jobs->the_post();
                    ?>
                    <?php echo WP_Freeio_Template_Loader::get_template_part( 'jobs-styles/inner-list' ); ?>
                    <?php
                endwhile;
            ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
<?php endif; ?>