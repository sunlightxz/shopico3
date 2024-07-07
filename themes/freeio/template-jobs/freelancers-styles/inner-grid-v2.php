<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

?>
<?php do_action( 'wp_freeio_before_freelancer_content', $post->ID ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('map-item freelancer-card'); ?> <?php freeio_freelancer_item_map_meta($post); ?>>
    <div class="freelancer-item position-relative v2">
        <?php freeio_freelancer_display_logo($post, true, '343x396'); ?>
        <div class="inner d-flex">
            <div class="left-inner flex-grow-1">
                <?php
                $title = freeio_freelancer_name($post);
                ?>
                <h2 class="freelancer-title">
                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                        <?php echo trim($title) ?>
                    </a>
                </h2>

                <?php freeio_freelancer_display_job_title($post); ?>
            </div>
            <div class="ms-auto">
                <?php freeio_freelancer_display_rating_reviews($post); ?>
            </div>
        </div>
    </div>
</article><!-- #post-## -->
<?php do_action( 'wp_freeio_after_freelancer_content', $post->ID ); ?>