<?php 
global $post;
$thumbsize = !isset($args['thumbsize']) ? '495x375' : $args['thumbsize'];
$thumb = freeio_display_post_thumb($thumbsize);
?>
<article <?php post_class('post post-layout post-grid v2'); ?>>
    <?php
        if ( !empty($thumb) ) {
            ?>
            <div class="top-image">
                <?php
                    echo trim($thumb);
                ?>
             </div>
            <?php
        }
    ?>
    <div class="col-content">
        <div class="d-flex align-items-center">
            <div class="author flex-shrink-0">
                <a class="d-flex align-items-center" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                    <span class="avatar-img">
                        <?php echo freeio_get_avatar( get_the_author_meta( 'ID' ),56 ); ?>
                    </span>
                </a>
            </div>
            <div class="inner flex-grow-1">
                <?php if (get_the_title()) { ?>
                    <h4 class="entry-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h4>
                <?php } ?>
                <a class="author-name" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                    <?php echo esc_html__('by','freeio') ?> <?php echo get_the_author(); ?>
                </a>
            </div>
        </div>
    </div>
</article>