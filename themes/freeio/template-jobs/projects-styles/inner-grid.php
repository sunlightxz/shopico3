<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

?>

<?php do_action( 'wp_freeio_before_project_content', $post->ID ); ?>

<article <?php post_class('map-item'); ?> <?php freeio_project_item_map_meta($post); ?>>
    <div class="project-item position-relative">
        <?php freeio_project_display_image($post); ?>
        <?php freeio_project_display_featured_icon($post,'text'); ?>

        <?php echo freeio_project_display_favorite_btn($post->ID); ?>

        <div class="project-information">
            <?php freeio_project_display_author($post); ?>
            <?php the_title( sprintf( '<h2 class="project-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
            <?php freeio_project_display_short_location($post, 'no-icon'); ?>
            <div class="info-bottom">
                <?php freeio_project_display_price($post, 'text'); ?>
            </div>
    	</div>
    </div>
</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_project_content', $post->ID ); ?>