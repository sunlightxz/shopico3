<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$relate_count = apply_filters('wp_freeio_number_job_releated', 3);

$tax_query = array();
$terms = WP_Freeio_Job_Listing::get_job_taxs( $post->ID, 'job_listing_type' );
if ($terms) {
    $termids = array();
    foreach($terms as $term) {
        $termids[] = $term->term_id;
    }
    $tax_query[] = array(
        'taxonomy' => 'job_listing_type',
        'field' => 'id',
        'terms' => $termids,
        'operator' => 'IN'
    );
}
$terms = WP_Freeio_Job_Listing::get_job_taxs( $post->ID, 'job_listing_category' );
if ($terms) {
    $termids = array();
    foreach($terms as $term) {
        $termids[] = $term->term_id;
    }
    $tax_query[] = array(
        'taxonomy' => 'job_listing_category',
        'field' => 'id',
        'terms' => $termids,
        'operator' => 'IN'
    );
}
if ( empty($tax_query) ) {
    return;
}
$args = array(
    'post_type' => 'job_listing',
    'posts_per_page' => $relate_count,
    'post__not_in' => array( get_the_ID() ),
    'tax_query' => array_merge(array( 'relation' => 'AND' ), $tax_query)
);
$relates = new WP_Query( $args );
if( $relates->have_posts() ):
?>
    <div class="widget releated-jobs">
        <h4 class="widget-title">
            <span><?php esc_html_e( 'Related Jobs', 'wp-freeio' ); ?></span>
        </h4>
        <div class="widget-content">
            <?php
                while ( $relates->have_posts() ) : $relates->the_post();
                    echo WP_Freeio_Template_Loader::get_template_part( 'jobs-styles/inner-list' );
                endwhile;
            ?>
            <?php wp_reset_postdata(); ?>
        </div>
    </div>
<?php endif; ?>