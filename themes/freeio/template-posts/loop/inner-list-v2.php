<?php 
global $post;
$thumbsize = !isset($args['thumbsize']) ? freeio_get_config( 'blog_item_thumbsize', 'full' ) : $args['thumbsize'];
$thumb = freeio_display_post_thumb($thumbsize);
?>
<article <?php post_class('post post-layout post-list-item-v2'); ?>>
    <div class="d-md-flex align-items-center">
        <?php
            if ( !empty($thumb) ) {
                ?>
                <div class="top-image flex-shrink-0">
                    <?php
                        echo trim($thumb);
                    ?>
                 </div>
                <?php
            }
        ?>
        <div class="col-content flex-grow-1">
            <?php freeio_post_categories_first($post); ?>
            <?php if (get_the_title()) { ?>
                <h4 class="entry-title">
                    <?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
                        <div class="stick-icon text-theme"><i class="ti-pin2"></i></div>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h4>
            <?php } ?>
            <div class="description d-none d-sm-block"><?php echo freeio_substring( get_the_excerpt(),25, '...' ); ?></div>
            <div class="description d-block d-sm-none"><?php echo freeio_substring( get_the_excerpt(),12, '...' ); ?></div>
            <div class="author">
                <a class="d-flex align-items-center" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                    <div class="left-inner flex-shrink-0">
                        <span class="avatar-img">
                            <?php echo freeio_get_avatar( get_the_author_meta( 'ID' ),60 ); ?>
                        </span>
                    </div>
                    <div class="inner flex-grow-1">
                        <div class="name-author">
                            <?php echo get_the_author(); ?>
                        </div>
                        <div class="date">
                            <?php the_time( get_option('date_format', 'd M, Y') ); ?>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</article>