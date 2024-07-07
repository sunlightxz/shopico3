<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post;

?>
<?php do_action( 'wp_freeio_before_project_content', $post->ID ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('map-item'); ?> <?php freeio_project_item_map_meta($post); ?>>
    <div class="project-item project-list position-relative">
        <div class="d-md-flex">
            <?php echo freeio_project_display_favorite_btn($post->ID); ?>
            <div class="flex-shrink-0 logo position-relative">
                <?php freeio_project_display_employer_logo($post); ?>
            </div>
            <div class="information-right d-lg-flex">
                <div class="inner-middle">
                    <div class="d-flex">
                        <?php the_title( sprintf( '<h2 class="project-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                        <span class="flex-shrink-0">
                            <?php freeio_project_display_featured_icon($post); ?>
                        </span>
                    </div>
                    <div class="project-metas d-flex align-items-center flex-wrap">
                        <?php freeio_project_display_short_location($post, 'icon'); ?>
                        <?php freeio_project_display_postdate($post, 'icon'); ?>
                        <?php freeio_project_display_proposals_count($post, 'icon'); ?>
                    </div>
                    <?php
                        $post_content = get_the_content($post);
                        if ( !empty($post_content) ) {
                            echo '<div class="project-excerpt d-none d-sm-block">';
                            echo freeio_substring( $post_content, 15, '...' );
                            echo '</div>';
                        }
                    ?>
                    <?php freeio_project_display_skills_version2($post); ?>
                </div>
                <div class="inner-right d-flex justify-content-center flex-column">
                    <?php freeio_project_display_price($post); ?>
                    <a href="<?php the_permalink(); ?>#project-proposal-form-wrapper" class="btn btn-theme-rgba10 w-100 radius-sm"><?php esc_html_e('Send Proposal', 'freeio'); ?> <i class="flaticon-right-up next"></i></a>
                </div>
            </div>
        </div>
    </div>
</article><!-- #post-## -->
<?php do_action( 'wp_freeio_after_project_content', $post->ID ); ?>