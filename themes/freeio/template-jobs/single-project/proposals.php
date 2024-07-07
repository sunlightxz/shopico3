<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $post;

$obj_meta = WP_Freeio_Project_Meta::get_instance($post->ID);
?>

<div class="project-detail-proposals-wrapper">
    <div class="project-detail-proposals">
        <?php echo freeio_project_display_list_proposals('', $post->ID); ?>
    </div>
    <h3 class="title"><?php esc_html_e('Send Your Proposal', 'freeio'); ?></h3>
    <div id="project-proposal-form-wrapper" class="project-detail-proposals-form">
        <form id="project-proposal-form-<?php echo esc_attr($post->ID); ?>" class="project-proposal-form form-theme" method="post">
            
            <div class="row">
                <div class="col-12 col-sm-6"><div class="form-group">
                    <?php if ( $obj_meta->check_post_meta_exist('project_type') && $obj_meta->get_post_meta( 'project_type' ) == 'fixed' ) { ?>
                        <label for="proposed-amount-id"><?php esc_html_e('Your Total Price','freeio'); ?></label>
                    <?php } else { ?>
                        <label for="proposed-amount-id"><?php esc_html_e('Your Hourly Price','freeio'); ?></label>
                    <?php } ?>
                        <input id="proposed-amount-id" autocomplete="off" type="number" name="proposed_amount" class="form-control" placeholder="<?php esc_attr_e('Price','freeio'); ?>" required>
                    </div></div>
                <div class="col-12 col-sm-6"><div class="form-group">
                        <label for="estimated-time-id"><?php esc_html_e('Estimated Hours','freeio'); ?></label>
                        <input id="estimated-time-id" autocomplete="off" type="number" name="estimeted_time" class="form-control" placeholder="4" pattern="\d*" min="0" required>
                    </div></div>
            </div>

            <div class="form-group">
                <label for="description-id"><?php esc_html_e('Cover Letter','freeio'); ?></label>
                <textarea id="description-id" class="form-control" name="description" required></textarea>
            </div>

            <input type="hidden" name="project_id" value="<?php echo esc_attr($post->ID); ?>">
            <button type="submit" class="btn btn-theme btn-inverse"><?php esc_html_e('Submit a Proposal', 'freeio'); ?> <i class="flaticon-right-up next"></i></button>
        </form>
    </div>
</div>