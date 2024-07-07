<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
$job_id = get_post_meta($post->ID, WP_FREEIO_APPLICANT_PREFIX.'job_id', true);
$freelancer_id = get_post_meta( $post->ID, WP_FREEIO_APPLICANT_PREFIX.'freelancer_id', true );
$freelancer = get_post($freelancer_id);

$freelancer_url = get_permalink($freelancer_id);
$freelancer_url = add_query_arg( 'applicant_id', $post->ID, $freelancer_url );
$freelancer_url = add_query_arg( 'freelancer_id', $freelancer_id, $freelancer_url );
$freelancer_url = add_query_arg( 'action', 'view-profile', $freelancer_url );

$rating_avg = WP_Freeio_Review::get_ratings_average($freelancer_id);

$viewed = get_post_meta( $post->ID, WP_FREEIO_APPLICANT_PREFIX.'viewed', true );
$classes = $viewed ? 'viewed' : '';
?>

<?php do_action( 'wp_freeio_before_applicant_content', $post->ID ); ?>

<article <?php post_class('applicants-job job-applicant-wrapper clearfix '.$classes); ?>>

    <div class="job-item job-list d-md-flex">
        <div class="freelancer-info flex-grow-1">
            <div class="d-md-flex">
                <?php if ( has_post_thumbnail($freelancer_id) ) { ?>
                    <div class="employer-logo-wrapper position-relative flex-shrink-0">
                        <div class="employer-logo">
                            <a href="<?php echo esc_url( $freelancer_url ); ?>" rel="bookmark">
                                <?php echo get_the_post_thumbnail( $freelancer_id, 'thumbnail' ); ?>
                            </a>
                        </div>
                    </div>
                <?php } ?>
                <div class="job-information-right flex-grow-1">
                    <div class="title-wrapper d-sm-flex align-items-center mb-2">
                        <h2 class="employer-title">
                            <a href="<?php echo esc_url( $freelancer_url ); ?>" rel="bookmark"><?php the_title(); ?></a>
                        </h2>

                        <?php
                            $app_status = WP_Freeio_Applicant::get_post_meta($post->ID, 'app_status', true);
                            if ( $app_status == 'approved' ) {
                                echo '<span class="badge bg-success approved">'.esc_html__('Approved', 'freeio').'</span>';
                            } elseif ( $app_status == 'rejected' ) {
                                echo '<span class="badge bg-cancelled rejected">'.esc_html__('Rejected', 'freeio').'</span>';
                            } else {
                                echo '<span class="label badge bg-pending pending">'.esc_html__('Pending', 'freeio').'</span>';
                            }
                        ?>
                    </div>
                    <h4 class="job-title">
                        <a href="<?php echo get_permalink($job_id); ?>"><?php echo get_the_title($job_id); ?></a>
                    </h4>
                    <div class="listing-metas d-flex flex-wrap align-items-start">
                        <?php freeio_freelancer_display_short_location($freelancer, 'icon'); ?>
                        <?php freeio_freelancer_display_salary($freelancer, 'icon'); ?>
                        <div class="date">
                            <i class="flaticon-30-days"></i>
                            <?php esc_html_e('Applied date : ', 'freeio'); ?>
                            <?php the_time( get_option('date_format', 'd M, Y') ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ms-auto">
            <div class="applicant-action-button action-button">
                <a data-toggle="tooltip" href="javascript:void(0);" class="btn-undo-reject-job-applied btn-action-icon reject" data-applicant_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-undo-reject-applied-nonce' )); ?>" title="<?php esc_attr_e('Undo Rejected', 'freeio'); ?>"><i class="ti-loop"></i></a>

                <a data-toggle="tooltip" title="<?php esc_attr_e('Remove', 'freeio'); ?>" href="javascript:void(0);" class="btn-action-icon btn-remove-job-applied remove" data-applicant_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-remove-applied-nonce' )); ?>"><i class="flaticon-delete"></i></a>
            </div>
        </div> 
    </div>
</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_applicant_content', $post->ID );