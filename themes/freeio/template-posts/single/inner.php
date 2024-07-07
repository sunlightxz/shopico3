<?php
$post_format = get_post_format();
global $post;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="inner">
        <div class="entry-content-detail <?php echo esc_attr((!has_post_thumbnail())?'not-img-featured':'' ); ?>">

            <div class="single-info info-bottom">
                <div class="entry-description clearfix">
                    <?php
                        the_content();
                    ?>
                </div><!-- /entry-content -->
                <?php
                wp_link_pages( array(
                    'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'freeio' ) . '</span>',
                    'after'       => '</div>',
                    'link_before' => '<span>',
                    'link_after'  => '</span>',
                    'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'freeio' ) . ' </span>%',
                    'separator'   => '',
                ) );
                ?>
                <?php  
                    $posttags = get_the_tags();
                ?>
                <?php if( !empty($posttags) || freeio_get_config('show_blog_social_share', false) ){ ?>
                    <div class="tag-social d-md-flex align-items-center">
                        <?php if( freeio_get_config('show_blog_social_share', false) ) { ?>
                            <?php get_template_part( 'template-parts/sharebox' ); ?>
                        <?php } ?>
                        <?php if(!empty($posttags)){ ?>
                            <div class="<?php echo esc_attr( (freeio_get_config('show_blog_social_share', false))?'ms-auto':'' ); ?>">
                                <?php freeio_post_tags(); ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
    <?php get_template_part( 'template-parts/author-bio' ); ?>
    <?php
        //Previous/next post navigation.
        the_post_navigation( array(
            'next_text' => 
                '<div class="inner">'.
                '<div class="navi">' . esc_html__( 'Next Post', 'freeio' ) . ' <i class="ti-angle-right"></i></div>'.
                '<span class="title-direct">%title</span></div>',
            'prev_text' => 
                '<div class="inner">'.
                '<div class="navi"><i class="ti-angle-left"></i> ' . esc_html__( 'Previous Post', 'freeio' ) . '</div>'.
                '<span class="title-direct">%title</span></div>',
        ) );
    ?>
</article>