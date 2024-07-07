<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

?>

<?php do_action( 'wp_freeio_before_employer_content', $post->ID ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('map-item'); ?> <?php freeio_employer_item_map_meta($post); ?>>
    <div class="employer-item position-relative">
        <?php echo freeio_employer_display_favorite_btn($post->ID); ?>
        <div class="position-relative">
            <div class="d-flex align-items-center">
                <span class="flex-shrink-0"><?php freeio_employer_display_logo($post); ?></span>
                <?php
                $title = freeio_employer_name($post);
                ?>
                <h2 class="employer-title">
                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                        <?php echo trim($title); ?>
                    </a>
                </h2>
                <span class="flex-shrink-0"><?php freeio_employer_display_featured_icon($post, 'icon'); ?></span>
            </div>
        </div>
        <?php freeio_employer_display_rating_reviews($post); ?>
        <div class="employer-metas d-flex flex-wrap">
            <?php freeio_employer_display_short_location($post); ?>
            <?php freeio_employer_display_open_position($post, true); ?>
        </div>
    </div>
</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_employer_content', $post->ID ); ?>