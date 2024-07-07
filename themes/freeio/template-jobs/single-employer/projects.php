<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$limit = freeio_get_config('employer_number_projects', 3);

$user_id = WP_Freeio_User::get_user_by_employer_id($post->ID);
$args = array(
    'post_type' => 'project',
    'post_status' => 'publish',
    'post_per_page' => $limit,
    'author' => $user_id
);
$projects = WP_Freeio_Query::get_posts( $args );

if( $projects->have_posts() ):
    $projects_url = WP_Freeio_Mixes::get_projects_page_url();
    $projects_url = add_query_arg( 'filter-author', $user_id, remove_query_arg( 'filter-author', $projects_url ) );
?>
    <div class="widget-open-projects">
        <div class="top-info-widget d-sm-flex">
            <h4 class="title">
                <?php esc_html_e( 'Projects', 'freeio' ); ?>
            </h4>
            <div class="ms-auto">
                <a href="<?php echo esc_url($projects_url); ?>" class="text-theme view_all">
                    <?php esc_html_e('Browse Full List', 'freeio'); ?> <i class="flaticon-right-up next"></i>
                </a>
            </div>
        </div>
        <div class="widget-content">
            <?php
                while ( $projects->have_posts() ) : $projects->the_post();
                    echo WP_Freeio_Template_Loader::get_template_part( 'projects-styles/inner-list' );
                endwhile;
            ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
<?php endif; ?>