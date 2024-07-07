<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

?>
<?php do_action( 'wp_freeio_before_freelancer_content', $post->ID ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('map-item freelancer-card'); ?> <?php freeio_freelancer_item_map_meta($post); ?>>
    <div class="freelancer-item position-relative">
        <?php freeio_freelancer_display_featured_icon($post,'text'); ?>
        <?php freeio_freelancer_display_logo($post); ?>
        <?php echo freeio_freelancer_display_favorite_btn($post->ID); ?>
        <div class="inner-bottom">
            <div class="text-center">
                <?php
                $title = freeio_freelancer_name($post);
                ?>
                <h2 class="freelancer-title">
                    <a href="<?php the_permalink(); ?>" rel="bookmark">
                        <?php echo trim($title) ?>
                    </a>
                </h2>

                <?php freeio_freelancer_display_job_title($post); ?>
                <?php freeio_freelancer_display_rating_reviews($post); ?>

                <?php freeio_freelancer_display_tags_version2($post,'no-title',true,2); ?>
            </div>
            <div class="freelancer-metas d-flex align-items-center">
                <?php freeio_freelancer_display_short_location($post, 'title'); ?>
                <?php freeio_freelancer_display_salary($post, 'title'); ?>
            </div>

            <div class="freelancer-link">
    	        <a href="<?php the_permalink(); ?>" class="btn btn-theme-rgba10 w-100 radius-sm"><?php esc_html_e('View Profile', 'freeio'); ?> <i class="next flaticon-right-up"></i></a>
        	</div>
        </div>
    </div>
</article><!-- #post-## -->
<?php do_action( 'wp_freeio_after_freelancer_content', $post->ID ); ?>