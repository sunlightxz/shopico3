<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);
$show_title = !isset($show_title) ? true : $show_title;
if ( $obj_meta->check_post_meta_exist('attachments') && ($attachments = $obj_meta->get_post_meta('attachments')) ) {
?>
<div class="project-detail-attachments">
    <?php if($show_title) { ?>
        <h3 class="title-detail-sm"><?php echo trim($obj_meta->get_post_meta_title('attachments')); ?></h3>
    <?php } ?>
    <?php
    $download_base_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_file');
    if ( is_array($attachments) ) { ?>
        <div id="project-cv" class="project-cv">
        <?php foreach ($attachments as $id => $cv_url) {
            $file_info = pathinfo($cv_url);
            if ( $file_info ) {
                $check_download = freeio_check_freelancer_can_download_project_attachment($id);
                $classes = 'cannot-download-cv-btn';
                $download_url = 'javascript:void(0);';
                if ( $check_download ) {
                    $download_url = add_query_arg(array('file_id' => $id), $download_base_url);
                    $classes = '';
                }
            ?>
                <a href="<?php echo trim($download_url); ?>" class="project-detail-cv <?php echo esc_attr($classes); ?>" data-msg="<?php esc_attr_e('Please login as freelancer account to download attachment', 'freeio'); ?>">
                    <span class="icon_type">
                        <i class="flaticon-file"></i>
                    </span>
                    <?php if ( !empty($file_info['filename']) ) { ?>
                        <div class="filename"><?php echo esc_html($file_info['filename']); ?></div>
                    <?php } ?>
                    <?php if ( !empty($file_info['extension']) ) { ?>
                        <div class="extension"><?php echo esc_html($file_info['extension']); ?></div>
                    <?php } ?>
                </a>
            <?php }
        }?>
        </div>
    <?php 
    }
    ?>
</div>
<?php }