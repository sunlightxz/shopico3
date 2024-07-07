<?php
    $bcol = floor( 12 / $args['columns'] );
?>
<div class="layout-blog">
    <div class="row">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-lg-<?php echo esc_attr($bcol); ?> col-12 col-sm-6">
                <?php get_template_part( 'template-posts/loop/inner-grid', null, array('thumbsize' => $args['thumbsize']) ); ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>