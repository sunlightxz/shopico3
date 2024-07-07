<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;


$categories = get_the_terms( $post->ID, 'freelancer_category' );
$address = WP_Freeio_Freelancer::get_post_meta( $post->ID, 'address', true );
?>
<div class="freelancer-detail-header">
    <?php if ( has_post_thumbnail() ) { ?>
        <div class="freelancer-thumbnail">
            <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
        </div>
    <?php } ?>
    <div class="freelancer-information">
        
        <?php the_title( '<h1 class="entry-title freelancer-title">', '</h1>' ); ?>
        <?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) { ?>
            <?php foreach ($categories as $term) { ?>
                <a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
            <?php } ?>
        <?php } ?>

        <?php if ( $address ) { ?>
            <div class="freelancer-address"><?php echo $address; ?></div>
        <?php } ?>
        
    </div>

    <div class="freelancer-detail-buttons">
        <?php WP_Freeio_Freelancer::display_shortlist_btn($post->ID); ?>
        <?php WP_Freeio_Freelancer::display_download_cv_btn($post->ID); ?>
    </div>
</div>