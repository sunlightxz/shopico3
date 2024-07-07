<?php
    $bcol = floor( 12 / $args['columns'] );
	$count = 1;
?>
<div class="layout-blog">
    <div class="row">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="col-lg-<?php echo esc_attr($bcol); ?> col-12 col-sm-6">
                <?php get_template_part( 'template-posts/loop/inner-grid-v1' ); ?>
            </div>
        <?php $count++; endwhile; ?>
    </div>
</div>