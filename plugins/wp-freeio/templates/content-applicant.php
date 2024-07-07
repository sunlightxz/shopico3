<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$freelancer_id = get_post_meta( $post->ID, WP_FREEIO_APPLICANT_PREFIX.'freelancer_id', true );

$freelancer_url = get_permalink($freelancer_id);
$freelancer_url = add_query_arg( 'applicant_id', $post->ID, $freelancer_url );
$freelancer_url = add_query_arg( 'freelancer_id', $freelancer_id, $freelancer_url );
$freelancer_url = add_query_arg( 'action', 'view-profile', $freelancer_url );

$cv_file_id = get_post_meta( $post->ID, WP_FREEIO_APPLICANT_PREFIX.'cv_file_id', true );

$download_base_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_cv');
if ( $cv_file_id ) {
    $download_url = add_query_arg(array('file_id' => $cv_file_id), $download_base_url);
} else {
    $cv_attachments = $meta_obj->get_post_meta('cv_attachment');
    if ( !empty($cv_attachments) ) {
        foreach ($cv_attachments as $id => $cv_url) {
            $download_url = add_query_arg(array('file_id' => $id), $download_base_url);
            break;
        }
    }
}
if ( !empty($download_url) ) {
    $download_base_url = WP_Freeio_Ajax::get_endpoint('wp_freeio_ajax_download_resume_cv');
    $download_url = add_query_arg(array('post_id' => $freelancer_id), $download_base_url);
}




$viewed = get_post_meta( $post->ID, WP_FREEIO_APPLICANT_PREFIX.'viewed', true );
$classes = $viewed ? 'viewed' : '';
?>

<?php do_action( 'wp_freeio_before_applicant_content', $post->ID ); ?>

<article <?php post_class('job-applicant-wrapper '.$classes); ?>>

    <?php if ( has_post_thumbnail($freelancer_id) ) { ?>
        <div class="applicant-thumbnail">
            <a href="<?php echo esc_url( $freelancer_url ); ?>" rel="bookmark">
                <?php echo get_the_post_thumbnail( $freelancer_id, 'thumbnail' ); ?>
            </a>
        </div>
    <?php } ?>
    <div class="applicant-information">
        <h2 class="entry-title applicant-title">
            <a href="<?php echo esc_url( $freelancer_url ); ?>" rel="bookmark"><?php the_title(); ?></a>
        </h2>

        <div class="applicant-date">
            <?php the_time( get_option('date_format', 'd M, Y') ); ?>
        </div>

        <a href="<?php echo esc_url( $freelancer_url ); ?>" rel="bookmark"><?php esc_html_e('View Profile', 'wp-freeio'); ?></a>

        <div class="applicant-action-button">
            <a href="javascript:void(0);" class="btn-actions"><?php esc_html_e('Actions', 'wp-freeio'); ?></a>
            <div class="all-actions-wrapper">
                <?php
                if ( $download_url ) {
                ?>
                    <a href="<?php echo esc_url($download_url); ?>"><?php esc_html_e('Download CV', 'wp-freeio'); ?></a>
                <?php } ?>

                <?php WP_Freeio_Freelancer::display_shortlist_link($freelancer_id); ?>

                <?php
                    $app_status = WP_Freeio_Applicant::get_post_meta($post->ID, 'app_status', true);
                    if ( $app_status == 'rejected' ) {
                        $text = esc_html__('Rejected', 'wp-freeio');
                    } else {
                        $text = esc_html__('Reject', 'wp-freeio');
                    }
                ?>
                <a href="javascript:void(0);" class="btn-reject-job-applied" data-applicant_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-reject-applied-nonce' )); ?>"><?php echo wp_kses_post($text); ?></a>

                <a href="javascript:void(0);" class="btn-remove-job-applied" data-applicant_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-remove-applied-nonce' )); ?>"><?php esc_html_e('Remove', 'wp-freeio'); ?></a>
            </div>
        </div>
    </div>
</article><!-- #post-## -->

<?php do_action( 'wp_freeio_after_applicant_content', $post->ID );