<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post;

?>

<?php do_action( 'wp_freeio_before_job_content', $post->ID ); ?>

<article <?php post_class('map-item'); ?> <?php freeio_job_item_map_meta($post); ?>>
    <div class="job-item position-relative job-list">
        <?php echo freeio_job_display_favorite_btn($post->ID); ?>
        <div class="d-md-flex">
            <div class="employer-logo-wrapper position-relative flex-shrink-0">
                <?php freeio_job_display_employer_logo($post); ?>
                
            </div>
            <div class="job-information-right flex-grow-1">
                <div class="d-flex">
                    <?php the_title( sprintf( '<h2 class="job-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                    <span class="flex-shrink-0"><?php freeio_job_display_featured_icon($post,'icon'); ?></span>
                </div>
                <?php freeio_job_display_employer_title($post); ?>
                <div class="job-metas">
                    <?php freeio_job_display_salary($post); ?>
                    <?php freeio_job_display_job_first_category($post); ?>
                    <?php freeio_job_display_job_type($post); ?>
                    <?php freeio_job_display_short_location($post); ?>
                </div>
            </div>
        </div>
    </div>
</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_job_content', $post->ID ); ?>