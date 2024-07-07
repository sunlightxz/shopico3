<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
extract( $args );

global $post;
if ( empty($post->post_type) || $post->post_type != 'freelancer' ) {
    return;
}

extract( $args );
extract( $instance );

echo trim($before_widget);
$title = apply_filters('widget_title', $instance['title']);

if ( $title ) {
    echo trim($before_title)  . trim( $title ) . $after_title;
}

$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post->ID);

$salary = WP_Freeio_Freelancer::get_salary_html($post->ID);

$gender = freeio_freelancer_display_meta($post, 'gender');
$age = freeio_freelancer_display_meta($post, 'age');
$qualification = freeio_freelancer_display_meta($post, 'qualification');
$languages = freeio_freelancer_display_meta($post, 'english_level');
$type = freeio_freelancer_display_meta($post, 'freelancer_type');

$email = freeio_freelancer_display_email($post, false, false);
$phone = freeio_freelancer_display_phone($post, false, true);

$website = freeio_freelancer_display_meta($post, 'website');

$location = freeio_freelancer_display_short_location($post, '', false);
?>
<div class="freelancer-info-detail ">
    <?php if ( $salary ) { ?>
        <div class="freelancer-salary-wrapper">
            <?php echo trim($salary); ?>
        </div>
    <?php } ?>
    <ul class="list-freelancer-info">


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

        <?php if ( $qualification ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-ruler"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('qualification')); ?></div>
                    <div class="value"><?php echo trim($qualification); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $type ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-document"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('freelancer_type')); ?></div>
                    <div class="value"><?php echo trim($type); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $languages ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-translator"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('english_level')); ?></div>
                    <div class="value"><?php echo trim($languages); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $gender ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-mars"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('gender')); ?></div>
                    <div class="value"><?php echo trim($gender); ?></div>
                </div>
            </li>
        <?php } ?>

        <?php if ( $age ) { ?>
            <li>
                <div class="icon">
                    <i class="flaticon-sandclock"></i>
                </div>
                <div class="details">
                    <div class="text"><?php echo trim($meta_obj->get_post_meta_title('age')); ?></div>
                    <div class="value"><?php echo trim($age); ?></div>
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

        <?php do_action('wp-freeio-single-freelancer-details', $post); ?>

        <?php
        if ( $meta_obj->check_post_meta_exist('socials') && ($socials = $meta_obj->get_post_meta( 'socials' )) ) {
            $all_socials = WP_Freeio_Mixes::get_socials_network();
            
            ?>

                <?php if ( $socials ) {
                    ob_start();
                    foreach ($socials as $social) { ?>
                        <?php if ( !empty($social['url']) && !empty($social['network']) ) {
                            $icon_class = !empty( $all_socials[$social['network']]['icon'] ) ? $all_socials[$social['network']]['icon'] : '';
                        ?>
                            <a href="<?php echo esc_html($social['url']); ?>">
                                <i class="<?php echo esc_attr($icon_class); ?>"></i>
                            </a>
                        <?php } ?>
                <?php }
                    $socials_html = ob_get_clean();
                    $socials_html = trim($socials_html);
                    if ( $socials_html ) {
                    ?>
                    <li>
                        <div class="icon">
                            <i class="flaticon-like-1"></i>
                        </div>
                        <div class="details">
                            <div class="text"><?php echo trim($meta_obj->get_post_meta_title('socials')); ?></div>
                            <div class="value">
                                <div class="apus-social-share ali-right">
                                    <?php echo trim($socials_html); ?>
                                </div>
                            </div>
                        </div>
                        
                    </li>
                    <?php } ?>
                <?php } ?>

        <?php } ?>

    </ul>
    

    <?php if ( WP_Freeio_Freelancer::check_restrict_view_contact_info($post) ) { ?>
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