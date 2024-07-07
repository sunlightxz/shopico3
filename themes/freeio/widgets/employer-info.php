<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
extract( $args );

global $post;
if ( empty($post->post_type) || $post->post_type != 'employer' ) {
    return;
}

extract( $args );
extract( $instance );

echo trim($before_widget);
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . $after_title;
}

$meta_obj = WP_Freeio_Employer_Meta::get_instance($post->ID);

$founded_date = freeio_employer_display_meta($post, 'founded_date');



$email = freeio_employer_display_email($post, false, false);
$phone = freeio_employer_display_phone($post, false, false, false);

$website = freeio_employer_display_meta($post, 'website');

$company_size = freeio_employer_display_company_size($post, '', false);
$category = freeio_employer_display_category($post, '', false);
$location = freeio_employer_display_short_location($post, '', false);
?>
<div class="employer-info-detail ">
    <ul class="list-employer-info">

        <?php if ( $category ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-category"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('category')); ?></div>
                    <div class="value"><?php echo trim($category); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $company_size ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-factory"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('company_size')); ?></div>
                    <div class="value"><?php echo trim($company_size); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $founded_date ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-calendar"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('founded_date')); ?></div>
                    <div class="value"><?php echo trim($founded_date); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $email ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-mail"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('email')); ?></div>
                    <div class="value"><?php echo trim($email); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $phone ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-call"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('phone')); ?></div>
                    <div class="value"><?php echo trim($phone); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $location ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-place"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('location')); ?></div>
                    <div class="value"><?php echo trim($location); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php do_action('wp-freeio-single-employer-details', $post); ?>

    </ul>
    <?php if ( WP_Freeio_Employer::check_restrict_view_contact_info($post) ) { ?>
        <a href="#apus-contact-form-wrapper" class="btn btn-theme btn-inverse btn-service-contact-form btn-show-popup w-100"><?php esc_html_e('Contact Me', 'freeio'); ?> <i class="flaticon-right-up next"></i></a>

        <div id="apus-contact-form-wrapper" class="apus-contact-form mfp-hide" data-effect="fadeIn">
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
                <button class="button btn btn-theme btn-block" name="contact-form"><?php echo esc_html__( 'Send Message', 'freeio' ); ?></button>
            </form>
        </div>
    <?php } ?>
</div>
<?php echo trim($after_widget);