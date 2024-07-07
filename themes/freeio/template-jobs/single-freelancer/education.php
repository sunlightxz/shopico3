<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

if ( $meta_obj->check_post_meta_exist('education') && ($education = $meta_obj->get_post_meta( 'education' )) ) {
    $source_pdf = isset($source_pdf) ? $source_pdf : false;
    $show_title = !empty($show_title) ? true : $show_title;
?>
    <div id="job-freelancer-education" class="freelancer-detail-education my_resume_eduarea">
        <?php if ( $show_title ) { ?>
            <h4 class="title"><?php esc_html_e('Education', 'freeio'); ?></h4>
        <?php } ?>
        <?php foreach ($education as $item) { ?>

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
                <div class="edu_center">
                    <?php if ( !empty($item['academy']) ) { ?>
                        <span class="university"><?php echo esc_html($item['academy']); ?></span>
                    <?php } ?>
                </div>
                <?php if ( !empty($item['description']) ) { ?>
                    <p class="mb0"><?php echo esc_html($item['description']); ?></p>
                <?php } ?>
            </div>
            
        <?php } ?>
    </div>
<?php }