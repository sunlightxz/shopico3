<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$relate_count = freeio_get_config('number_freelancer_related', 4);
$columns = freeio_get_config('related_freelancer_columns', 4);
$columns_tablet = 3;
$columns_mobile = 1;

$tax_query = array();
$terms = get_the_terms( $post->ID, 'freelancer_category' );
if ($terms) {
    $termids = array();
    foreach($terms as $term) {
        $termids[] = $term->term_id;
    }
    $tax_query[] = array(
        'taxonomy' => 'freelancer_category',
        'field' => 'id',
        'terms' => $termids,
        'operator' => 'IN'
    );
}
if ( empty($tax_query) ) {
    return;
}
$args = array(
    'post_type' => 'freelancer',
    'posts_per_page' => $relate_count,
    'post__not_in' => array( get_the_ID() ),
    'tax_query' => $tax_query
);
$relates = new WP_Query( $args );
if( $relates->have_posts() ):
?>
<div class="wrapper-services-related">
    <div class="related-freelancers related-posts">
        <h4 class="title">
            <?php esc_html_e( 'Related Freelancers', 'freeio' ); ?>
        </h4>
        <div class="widget-content">
            <div class="slick-carousel" data-carousel="slick"
                data-items="<?php echo esc_attr($columns); ?>"
                data-large="<?php echo esc_attr( $columns_tablet ); ?>"
                data-medium="2"
                data-small="<?php echo esc_attr($columns_mobile); ?>"
                data-smallest="<?php echo esc_attr($columns_mobile); ?>"
                
                data-slidestoscroll="<?php echo esc_attr($columns); ?>"
                data-slidestoscroll_medium="<?php echo esc_attr( $columns_tablet ); ?>"
                data-slidestoscroll_small="<?php echo esc_attr($columns_mobile); ?>"
                data-pagination="false" data-nav="true" data-rows="1" data-infinite="false" data-autoplay="false">
                <?php
                    while ( $relates->have_posts() ) : $relates->the_post();
                        echo WP_Freeio_Template_Loader::get_template_part( 'freelancers-styles/inner-grid' );
                    endwhile;
                ?>
            </div>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
</div>
<?php endif; ?>