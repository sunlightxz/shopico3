<?php
$post_format = get_post_format();
global $post;
?>
<div class="entry-content-detail header-info-blog">
    <div class="header-info-blog-inner <?php echo esc_attr( (has_post_thumbnail()) ? '':'position-static' ); ?>">
        <?php if (get_the_title()) { ?>
            <h1 class="detail-title">
                <?php the_title(); ?>
            </h1>
        <?php } ?>
        <div class="d-flex align-items-center top-detail-info flex-wrap">
            <div class="author">
                <a class="d-flex align-items-center" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                    <span class="avatar-img">
                        <?php echo freeio_get_avatar( get_the_author_meta( 'ID' ),40 ); ?>
                    </span>
                    <?php echo get_the_author(); ?>
                </a>
            </div>
            <?php freeio_post_categories($post); ?>
            <div class="date">
                <?php the_time( get_option('date_format', 'd M, Y') ); ?>
            </div>
        </div>
    </div>
    <?php if(has_post_thumbnail()) { ?>
        <div class="entry-thumb">
            <?php
                $thumb = freeio_post_thumbnail();
                echo trim($thumb);
            ?>
        </div>
    <?php } ?>
</div>