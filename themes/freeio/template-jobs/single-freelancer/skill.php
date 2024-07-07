<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

if ( $meta_obj->check_post_meta_exist('skill') && ($skill = $meta_obj->get_post_meta( 'skill' )) ) {
    $show_title = !empty($show_title) ? $show_title : true;
?>
    <div id="job-freelancer-skill" class="freelancer-detail-skill freelancer_resume_skill">
        <?php if ( $show_title ) { ?>
            <h4 class="title"><?php esc_html_e('Skills', 'freeio'); ?></h4>
        <?php } ?>
        <div class="progress-levels">
            <?php $i=1; foreach ($skill as $item) {
                $delay = $i*100;
            ?>
                <div class="valuation-item wow animated" data-wow-delay="<?php echo esc_attr($delay); ?>ms" data-wow-duration="1500ms">

                    <?php if ( !empty($item['title']) ) { ?>
                        <h5 class="valuation-title"><?php echo esc_html($item['title']); ?></h5>
                    <?php } ?>
                    
                    <?php if ( !empty($item['percentage']) ) { ?>
                        <div class="progress-item d-flex align-items-center">
                            <div class="progress"><div class="progress-bar" role="progressbar" aria-valuenow="<?php echo esc_attr($item['percentage']); ?>" aria-valuemin="0" style="width: <?php echo trim($item['percentage']); ?>%;"></div></div><div class="value"><?php echo esc_html($item['percentage']); ?>%</div>
                        </div>
                    <?php } ?>
                </div>
            <?php $i++; } ?>
        </div>
    </div>
<?php }