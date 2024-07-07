<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$relate_count = freeio_get_config('number_service_related', 3);
$columns = freeio_get_config('related_service_columns', 4);
$columns_tablet = $columns;
$columns_mobile = 1;

$tax_query = array();
$terms = get_the_terms( $post->ID, 'service_category' );
if ($terms) {
    $termids = array();
    foreach($terms as $term) {
        $termids[] = $term->term_id;
    }
    $tax_query[] = array(
        'taxonomy' => 'service_category',
        'field' => 'id',
        'terms' => $termids,
        'operator' => 'IN'
    );
}
if ( empty($tax_query) ) {
    return;
}
$args = array(
    'post_type' => 'service',
    'post_status' => 'publish',
    'posts_per_page' => $relate_count,
    'post__not_in' => array( get_the_ID() ),
    'tax_query' => array_merge(array( 'relation' => 'AND' ), $tax_query)
);
$relates = new WP_Query( $args );
if( $relates->have_posts() ):
?>
<div class="wrapper-services-related">
    <div class="related-posts">
        <h4 class="title">
            <?php esc_html_e( 'Related Services', 'freeio' ); ?>
        </h4>
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
                    while ( $relates->have_posts() ) : $relates->the_post(); ?>
                        <div class="item">
                            <?php echo WP_Freeio_Template_Loader::get_template_part( 'services-styles/inner-grid' ); ?>
                        </div>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</div>
<?php endif;