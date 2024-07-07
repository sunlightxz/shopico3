<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;
?>
<?php do_action( 'wp_freeio_before_project_content', $post->ID ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('map-item project-favorite-wrapper'); ?> <?php freeio_project_item_map_meta($post); ?>>
    <div class="project-item project-list v1 position-relative">
        <div class="d-md-flex">
            <a href="javascript:void(0);" class="btn-remove-project-favorite" data-project_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-remove-project-favorite-nonce' )); ?>"><i class="flaticon-delete"></i></a>

            <div class="flex-shrink-0 logo position-relative">
                <?php freeio_project_display_employer_logo($post); ?>
            </div>
            <div class="information-right">
                <div class="inner-middle">
                    <div class="d-flex">
                        <?php the_title( sprintf( '<h2 class="project-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                        <span class="flex-shrink-0">
                            <?php freeio_project_display_featured_icon($post); ?>
                        </span>
                    </div>
                    <?php freeio_project_display_employer_title($post); ?>
                    <div class="project-metas d-flex align-items-center flex-wrap">
                        <?php freeio_project_display_short_location($post, 'icon'); ?>
                        <?php freeio_project_display_postdate($post, 'icon'); ?>
                        <?php freeio_project_display_proposals_count($post, 'icon'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article><!-- #post-## -->
<?php do_action( 'wp_freeio_after_project_content', $post->ID ); ?>