<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

if ( $meta_obj->check_post_meta_exist('award') && ($award = $meta_obj->get_post_meta( 'award' )) ) {
    $source_pdf = isset($source_pdf) ? $source_pdf : false;
    $show_title = !empty($show_title) ? true : $show_title;
?>
    <div id="job-freelancer-award" class="my_resume_eduarea color-award">
        <?php if ( $show_title ) { ?>
            <h4 class="title"><?php esc_html_e('Awards', 'freeio'); ?></h4>
        <?php } ?>
        <?php foreach ($award as $item) { ?>

            <div class="content">
                <div class="circle">
                    <?php if ( !empty($item['title']) ) {
                        echo mb_substr(trim($item['title']), 0, 1);
                    } ?>
                </div>
                <div class="top-info">
                    <?php if ( !empty($item['year']) ) { ?>
                        <span class="year"><?php echo esc_html($item['year']); ?></span>
                    <?php } ?>
                    <?php if ( !empty($item['title']) ) { ?>
                        <span class="edu_stats"><?php echo esc_html($item['title']); ?></span>
                    <?php } ?>
                </div>
                <?php if ( !empty($item['description']) ) { ?>
                    <div class="mb0"><?php echo esc_html($item['description']); ?></div>
                <?php } ?>
            </div>

        <?php } ?>
    </div>
<?php }