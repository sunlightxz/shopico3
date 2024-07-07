<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $post;

?>
<?php do_action( 'wp_freeio_before_project_content', $post->ID ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('map-item'); ?> <?php freeio_project_item_map_meta($post); ?>>
    <div class="project-item project-grid-v2">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 logo position-relative">
                <?php freeio_project_display_employer_logo($post); ?>
            </div>
            <div class="project-metas d-flex align-items-center flex-wrap flex-grow-1">
                <?php freeio_project_display_short_location($post, 'icon'); ?>
                <?php freeio_project_display_views($post); ?>
                <?php freeio_project_display_proposals_count($post, 'icon'); ?>
            </div>
        </div>
        <div class="info">
            <?php the_title( sprintf( '<h2 class="project-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
            <?php
                $post_content = get_the_content($post);
                if ( !empty($post_content) ) {
                    echo '<div class="project-excerpt">';
                    echo freeio_substring( $post_content, 11, '...' );
                    echo '</div>';
                }
            ?>
            <?php freeio_project_display_skills_version2($post,'no-title',true,2); ?>
            <?php freeio_project_display_price($post); ?>
            <a href="<?php the_permalink(); ?>#project-proposal-form-wrapper" class="btn btn-theme btn-outline w-100"><?php esc_html_e('Send Proposal', 'freeio'); ?> <i class="flaticon-right-up next"></i></a>
        </div>
    </div>
</article><!-- #post-## -->
<?php do_action( 'wp_freeio_after_project_content', $post->ID ); ?>