<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$author_id = $post->post_author;
$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);
$freelancer_obj = get_post($freelancer_id);
?>
<article <?php post_class('map-item'); ?>>
    <div class="d-sm-flex">
        <div class="inner-left d-flex flex-grow-1">
            <div class="position-relative proposal-top flex-shrink-0">
                <?php freeio_freelancer_display_logo($freelancer_obj); ?>
            </div>
            <div class="service-information flex-grow-1">
                <?php
                $title = freeio_freelancer_name($freelancer_obj);
                ?>
                <h2 class="freelancer-title">
                    <a href="<?php echo esc_url(get_permalink($freelancer_obj)); ?>">
                        <?php echo get_the_title($freelancer_obj); ?>
                    </a>
                </h2>

                <div class="freelancer-metas d-flex align-items-center flex-wrap">
                    <?php freeio_freelancer_display_rating_reviews($freelancer_obj); ?>
                    <?php freeio_freelancer_display_short_location($freelancer_obj, 'icon'); ?>

                    <?php $post_date = sprintf(esc_html__('%s ago', 'freeio'), human_time_diff(get_the_time('U'), current_time('timestamp')) ); ?>
                    <div class="post-date with-icon"><i class="flaticon-30-days"></i> <?php echo trim($post_date); ?></div>
                </div>

                <div class="info-description">
                    <?php echo trim($post->post_content); ?>
                </div>
            </div>
        </div>
        <div class="amount ms-auto">
            <?php
            $estimeted_time = get_post_meta( $post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'estimeted_time', true );
            $proposed_amount = get_post_meta( $post->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'amount', true );
            ?>
            <div class="price-wrapper">
                <?php echo WP_Freeio_Price::format_price($proposed_amount); ?>
            </div>
            <div class="time"><?php echo sprintf(esc_html__('in %d hours', 'freeio'), $estimeted_time); ?></div>
        </div>
    </div>
</article><!-- #post-## -->