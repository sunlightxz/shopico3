<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


global $post, $preview_post;
if ( $preview_post ) {
    $post = $preview_post;
}
if ( empty($post->post_type) || $post->post_type !== 'service' ) {
    return;
}
extract( $args );

$author_id = freeio_get_service_post_author($post->ID);
$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($author_id);
if ( !$freelancer_id ) {
    return;
}
$freelancer_obj = get_post($freelancer_id);

extract( $args );
extract( $instance );


echo trim($before_widget);
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . $after_title;
}

?>
    <div class="widget-service-author">
        <div class="service-author-heading d-flex align-items-center">
            <div class="service-author-image flex-shrink-0">
                <a href="<?php echo esc_url( get_permalink($freelancer_id) ); ?>">
                    <?php freeio_freelancer_display_logo($freelancer_obj, false); ?>
                </a>
            </div>
            <div class="service-author-right flex-grow-1">
                <h5><a href="<?php echo esc_url( get_permalink($freelancer_id) ); ?>">
                    <?php echo freeio_freelancer_name($freelancer_obj); ?>
                </a></h5>
                <!-- job -->
                <?php freeio_freelancer_display_job_title($freelancer_obj); ?>
                <?php freeio_freelancer_display_rating_reviews($freelancer_obj); ?>
            </div>
        </div>

        <div class="metas">
            <?php freeio_freelancer_display_short_location($freelancer_obj, 'title'); ?>
            <?php freeio_freelancer_display_salary($freelancer_obj, 'title'); ?>
        </div>

        <?php if ( WP_Freeio_Freelancer::check_restrict_view_contact_info($freelancer_obj) ) { ?>
            <a href="#apus-contact-form-wrapper" class="btn btn-theme btn-outline w-100 btn-service-contact-form btn-show-popup"><?php esc_html_e('Contact Me', 'freeio'); ?> <i class="flaticon-right-up next"></i></a>
        <?php } ?>
    </div>
    <?php if ( WP_Freeio_Freelancer::check_restrict_view_contact_info($freelancer_obj) ) { ?>
        <div id="apus-contact-form-wrapper" class="apus-contact-form mfp-hide apus-popup-form" data-effect="fadeIn">
            
            <a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>

            <form method="post" action="?" class="contact-form-wrapper">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <input type="text" class="form-control" name="subject" placeholder="<?php esc_attr_e( 'Subject', 'freeio' ); ?>" required="required">
                        </div><!-- /.form-group -->
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="<?php esc_attr_e( 'E-mail', 'freeio' ); ?>" required="required">
                        </div><!-- /.form-group -->
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <input type="text" class="form-control style2" name="phone" placeholder="<?php esc_attr_e( 'Phone', 'freeio' ); ?>" required="required">
                        </div><!-- /.form-group -->
                    </div>
                </div>
                <div class="form-group">
                    <textarea class="form-control" name="message" placeholder="<?php esc_attr_e( 'Message', 'freeio' ); ?>" required="required"></textarea>
                </div><!-- /.form-group -->

                <?php if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) { ?>
                    <div id="recaptcha-contact-form" class="ga-recaptcha" data-sitekey="<?php echo esc_attr(wp_freeio_get_option( 'recaptcha_site_key' )); ?>"></div>
                <?php } ?>

                <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID); ?>">
                <button class="button btn btn-theme btn-outline w-100" name="contact-form"><?php echo esc_html__( 'Send Message', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
            </form>
        </div>
    <?php } ?>
<?php

echo trim($after_widget);