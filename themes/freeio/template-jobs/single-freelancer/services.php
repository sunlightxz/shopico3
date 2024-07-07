<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$limit = apply_filters('wp_freeio_freelancer_limit_services', 3);
$columns = 3;
$columns_tablet = 2;
$columns_mobile = 1;


$user_id = WP_Freeio_User::get_user_by_freelancer_id($post->ID);
$args = array(
    'post_type' => 'service',
    'post_status' => 'publish',
    'post_per_page' => $limit,
    'author' => $user_id
);
$services = WP_Freeio_Query::get_posts( $args );

if ( $services->have_posts() ):
    $services_url = WP_Freeio_Mixes::get_services_page_url();
    $services_url = add_query_arg( 'filter-author', $user_id, remove_query_arg( 'filter-author', $services_url ) );
?>
    <div class="widget-open-services">
        <div class="top-info-widget d-sm-flex align-items-center">
            <h4 class="title">
                <?php esc_html_e( 'Services', 'freeio' ); ?>
            </h4>
            <div class="ms-auto">
                <a href="<?php echo esc_url($services_url); ?>" class="text-theme view_all">
                    <?php esc_html_e('Browse Full List', 'freeio'); ?> <i class="flaticon-right-up next"></i>
                </a>
            </div>
        </div>
        <div class="widget-content">
            <div class="slick-carousel" data-carousel="slick"
                data-items="<?php echo esc_attr($columns); ?>"
                data-large="<?php echo esc_attr( $columns_tablet ); ?>"
                data-small="<?php echo esc_attr($columns_mobile); ?>"
                data-medium="2"
                
                data-slidestoscroll="<?php echo esc_attr($columns); ?>"
                data-slidestoscroll_smallmedium="<?php echo esc_attr( $columns_tablet ); ?>"
                data-slidestoscroll_extrasmall="<?php echo esc_attr($columns_mobile); ?>"
                data-pagination="false" data-nav="true" data-rows="1" data-infinite="false" data-autoplay="false">
                <?php
                    while ( $services->have_posts() ) : $services->the_post();
                        echo '<div class="item">';
                            echo WP_Freeio_Template_Loader::get_template_part( 'services-styles/inner-grid' ); 
                        echo '</div>';
                    endwhile;
                ?>
            </div>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
<?php endif; ?>