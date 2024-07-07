<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;


$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);


$project_success = freeio_freelancer_get_project_success($post);
$total_services = freeio_freelancer_get_total_services($post);
$completed_services = freeio_freelancer_get_completed_service($post);
$inqueue_service = freeio_freelancer_get_inqueue_service($post);
?>
<div class="freelancer-detail-detail service-detail-detail">
    <ul class="list-service-detail column-4 d-flex align-items-center flex-wrap">
        
        <li>
            <div class="icon">
                <i class="flaticon-target"></i>
            </div>
            <div class="details">
                <div class="text"><?php esc_html_e('Project Success', 'freeio'); ?></div>
                <div class="value"><?php echo number_format($project_success); ?></div>
            </div>
        </li>

        <li>
            <div class="icon">
                <i class="flaticon-goal"></i>
            </div>
            <div class="details">
                <div class="text"><?php esc_html_e('Total Service', 'freeio'); ?></div>
                <div class="value"><?php echo number_format($total_services); ?></div>
            </div>
        </li>

        <li>
            <div class="icon">
                <i class="flaticon-target"></i>
            </div>
            <div class="details">
                <div class="text"><?php esc_html_e('Completed Service', 'freeio'); ?></div>
                <div class="value"><?php echo number_format($completed_services); ?></div>
            </div>
        </li>

        <li>
            <div class="icon">
                <i class="flaticon-file-1"></i>
            </div>
            <div class="details">
                <div class="text"><?php esc_html_e('In Queue service', 'freeio'); ?></div>
                <div class="value"><?php echo number_format($inqueue_service); ?></div>
            </div>
        </li>

        <?php do_action('wp-freeio-single-freelancer-details', $post); ?>

    </ul>

    
</div>