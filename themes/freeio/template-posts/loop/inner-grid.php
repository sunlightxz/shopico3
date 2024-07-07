<?php 
global $post;
$thumbsize = !isset($args['thumbsize']) ? '495x375' : $args['thumbsize'];
$thumb = freeio_display_post_thumb($thumbsize);
?>
<article <?php post_class('post post-layout post-grid'); ?>>
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
        <div class="date">
            <?php the_time( get_option('date_format', 'd M, Y') ); ?>
        </div>
        <?php if (get_the_title()) { ?>
            <h4 class="entry-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h4>
        <?php } ?>
        <div class="description"><?php echo freeio_substring( get_the_excerpt(), 8, '' ); ?></div>
    </div>
</article>