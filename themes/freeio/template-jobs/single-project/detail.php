<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);

$duration = freeio_project_display_tax($post, 'duration', false);
$level = freeio_project_display_tax($post, 'level', false);
$language = freeio_project_display_tax($post, 'language', false);

?>
<div class="project-detail-detail">
    <ul class="list-service-detail d-flex align-items-center flex-wrap">
        <?php if ( $obj_meta->check_post_meta_exist('location_type') && ($location_type = $obj_meta->get_post_meta('location_type')) ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-place"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($obj_meta->get_post_meta_title('location_type')); ?></div>
                    <div class="value">
                        <?php
                            if ( is_array($location_type) ) {
                                echo implode(', ', $location_type);
                            } else {
                                echo esc_html($location_type);
                            }
                        ?>         
                    </div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $obj_meta->check_post_meta_exist('project_type') && ($project_type = $obj_meta->get_post_meta('project_type')) ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-dollar"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($obj_meta->get_post_meta_title('project_type')); ?></div>
                    <div class="value">
                        <?php
                            $project_types = WP_Freeio_Mixes::get_default_project_types();
                            if ( is_array($project_type) ) {
                                $i = 1; foreach ($project_type as $value) {
                                    echo (!empty($project_types[$value]) ? $project_types[$value] : $value).($i < count($project_type) ? ', ' : '' );
                                    $i++;
                                }
                            } else {
                                echo !empty($project_types[$project_type]) ? $project_types[$project_type] : $project_type;
                            }
                        ?>         
                    </div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $duration ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-fifteen"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($obj_meta->get_post_meta_title('project_duration')); ?></div>
                    <div class="value"><?php echo trim($duration); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $level ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-like-1"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($obj_meta->get_post_meta_title('project_level')); ?></div>
                    <div class="value"><?php echo trim($level); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $language ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-translator"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($obj_meta->get_post_meta_title('project_language')); ?></div>
                    <div class="value"><?php echo trim($language); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $obj_meta->check_post_meta_exist('english_level') && ($english_level = $obj_meta->get_post_meta('english_level')) ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-goal"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($obj_meta->get_post_meta_title('english_level')); ?></div>
                    <div class="value">
                        <?php
                            if ( is_array($english_level) ) {
                                echo implode(', ', $english_level);
                            } else {
                                echo esc_html($english_level);
                            }
                        ?>         
                    </div>
                </div>
            </li>
        <?php } ?>


        <?php do_action('wp-freeio-single-project-details', $post); ?>
    </ul>
</div>