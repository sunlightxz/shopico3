<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);

if ( !$obj_meta->check_post_meta_exist('project_skill') ) {
    return;
}
$skills = freeio_project_display_skills($post, 'no-title', false);

if ( empty($skills) ) {
    return;
}

?>
<div class="project-detail-skills">
    <h3 class="title"><?php esc_html_e('Skills Required', 'freeio'); ?></h3>
    <?php echo trim($skills); ?>
</div>