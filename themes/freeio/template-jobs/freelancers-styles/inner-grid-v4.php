<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

?>
<?php do_action( 'wp_freeio_before_freelancer_content', $post->ID ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('map-item freelancer-card'); ?> <?php freeio_freelancer_item_map_meta($post); ?>>
    <div class="freelancer-item position-relative v4">
        <div class="d-flex align-items-center">
            <?php freeio_freelancer_display_logo($post, true, '90x90'); ?>
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
        </div>
        <div class="info">
            <?php freeio_freelancer_display_rating_reviews($post); ?>
            <?php freeio_freelancer_display_salary($post,'icon'); ?>
        </div>
        <?php
            $post_content = get_the_content('', false, $post);
            if ( !empty($post_content) ) {
                echo '<div class="excerpt">';
                echo freeio_substring( $post_content, 10, '' );
                echo '</div>';
            }
        ?>
        <?php freeio_freelancer_display_tags_version2($post,'no-title',true,2); ?>
        <div class="freelancer-link">
            <a href="<?php the_permalink(); ?>" class="btn btn-theme btn-outline w-100"><?php esc_html_e('View Profile', 'freeio'); ?> <i class="next flaticon-right-up"></i></a>
        </div>
    </div>
</article><!-- #post-## -->
<?php do_action( 'wp_freeio_after_freelancer_content', $post->ID ); ?>