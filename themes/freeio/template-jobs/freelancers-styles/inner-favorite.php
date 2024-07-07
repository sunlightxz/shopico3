<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

?>
<?php do_action( 'wp_freeio_before_freelancer_content', $post->ID ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('map-item freelancer-card freelancer-favorite-wrapper'); ?> <?php freeio_freelancer_item_map_meta($post); ?>>
    <div class="freelancer-item position-relative">
        <a href="javascript:void(0);" class="btn-remove-freelancer-favorite" data-freelancer_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-remove-freelancer-favorite-nonce' )); ?>"><i class="flaticon-delete"></i></a>
        <div class="d-flex">
            <div class="flex-shrink-0">
                <?php freeio_freelancer_display_logo($post); ?>
            </div>
            <div class="information-right d-lg-flex">
                <div class="inner-middle">
                    <?php
                    $title = freeio_freelancer_name($post);
                    ?>
                    <div class="d-flex freelancer-title-wrapper">
                        <h2 class="freelancer-title">
                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                <?php echo trim($title) ?>
                            </a>
                        </h2>
                        <span class="flex-shrink-0"><?php freeio_freelancer_display_featured_icon($post,'icon'); ?></span>
                    </div>
                    <?php freeio_freelancer_display_job_title($post); ?>
                    <?php freeio_freelancer_display_rating_reviews($post); ?>

                    <?php freeio_freelancer_display_tags_version2($post); ?>
                </div>
                <div class="inner-right">
                    <div class="freelancer-metas d-flex align-items-center">
                        <?php freeio_freelancer_display_short_location($post, 'title'); ?>
                        <?php freeio_freelancer_display_salary($post, 'title'); ?>
                    </div>

                    <div class="freelancer-link">
                        <a href="<?php the_permalink(); ?>" class="btn btn-theme-rgba10 w-100 radius-sm"><?php esc_html_e('View Profile', 'freeio'); ?> <i class="next flaticon-right-up"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article><!-- #post-## -->
<?php do_action( 'wp_freeio_after_freelancer_content', $post->ID ); ?>