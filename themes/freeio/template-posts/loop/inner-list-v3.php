<?php 
global $post;
$thumbsize = !isset($thumbsize) ? freeio_get_config( 'blog_item_thumbsize', 'full' ) : $thumbsize;
$thumb = freeio_display_post_thumb($thumbsize);
?>
<article <?php post_class('post post-layout post-list-item-v3'); ?>>
    <div class="d-flex align-items-center">
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
            <div class="date">
                <?php the_time( get_option('date_format', 'd M, Y') ); ?>
            </div>
            <?php if (get_the_title()) { ?>
                <h4 class="entry-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h4>
            <?php } ?>
            <div class="description d-none d-md-block"><?php echo freeio_substring( get_the_excerpt(),10, '' ); ?></div>
        </div>
    </div>
</article>