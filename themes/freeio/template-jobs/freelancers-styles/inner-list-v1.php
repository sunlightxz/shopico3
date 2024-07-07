<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

?>
<?php do_action( 'wp_freeio_before_freelancer_content', $post->ID ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('map-item freelancer-card'); ?> <?php freeio_freelancer_item_map_meta($post); ?>>
    <div class="freelancer-item freelancer-item-list-v1">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 logo position-relative">
                <?php freeio_freelancer_display_logo($post); ?>
            </div>
            <div class="information-right flex-grow-1">
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
                <div class="info">
                    <?php freeio_freelancer_display_rating_reviews($post); ?>
                    <?php freeio_freelancer_display_short_location($post, 'icon'); ?>
                    <?php freeio_freelancer_display_salary($post, 'icon'); ?>
                </div>
            </div>
            <div class="freelancer-link ms-auto d-none d-xl-block">
                <a href="<?php the_permalink(); ?>" class="btn btn-theme btn-outline"><?php esc_html_e('View Profile', 'freeio'); ?> <i class="next flaticon-right-up"></i></a>
            </div>
        </div>
        <?php
            $post_content = get_the_content('', false, $post);
            if ( !empty($post_content) ) {
                echo '<div class="excerpt d-block d-lg-none">';
                echo freeio_substring( $post_content, 20, '' );
                echo '</div>';
            }
        ?>
        <?php
            $post_content = get_the_content($post);
            if ( !empty($post_content) ) {
                echo '<div class="excerpt d-none d-lg-block">';
                echo freeio_substring( $post_content, 35, '' );
                echo '</div>';
            }
        ?>
        <?php freeio_freelancer_display_tags($post); ?>
    </div>
</article><!-- #post-## -->
<?php do_action( 'wp_freeio_after_freelancer_content', $post->ID ); ?>