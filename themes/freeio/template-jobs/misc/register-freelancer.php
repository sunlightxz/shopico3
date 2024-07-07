<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="register-form-wrapper">
    <div class="container-form">

        <?php if ( sizeof($form_obj->errors) ) : ?>
            <ul class="alert alert-danger errors">
                <?php foreach ( $form_obj->errors as $message ) { ?>
                    <div class="message_line danger">
                        <?php echo wp_kses_post( $message ); ?>
                    </div>
                <?php } ?>
            </ul>
        <?php endif; ?>

        <?php if ( sizeof($form_obj->success_msg) ) : ?>
            <ul class="alert alert-info success">
                <?php foreach ( $form_obj->success_msg as $message ) { ?>
                    <div class="message_line info">
                        <?php echo wp_kses_post( $message ); ?>
                    </div>
                <?php } ?>
            </ul>
        <?php endif; ?>

        <div class="form-group">
            <label><input type="radio" name="driver_type" value="society" required> Society</label>&nbsp;&nbsp;
            <label><input type="radio" name="driver_type" value="entrepreneur"> Entrepreneur</label>&nbsp;&nbsp;
            <label><input type="radio" name="driver_type" value="other"> Other</label>
        </div>

        <div class="form-group" id="username_or_society_name">
            <label for="username_or_society_name_field"><?php echo __('Username:', 'wp-freeio'); ?></label>
            <input type="text" name="username_or_society_name" id="username_or_society_name_field" class="form-control" required>
        </div>
        
        <?php
            $html_output = '';

            if ( WP_Freeio_Recaptcha::is_recaptcha_enabled() ) {
                $html_output .= '<div id="recaptcha-register-freelancer" class="ga-recaptcha margin-bottom-25" data-sitekey="'.esc_attr(wp_freeio_get_option( 'recaptcha_site_key' )).'"></div>';
            }


            // Terms and Conditions
            $page_id = wp_freeio_get_option('terms_conditions_page_id');
            $page_id = WP_Freeio_Mixes::get_lang_post_id($page_id);
            if ( !empty($page_id) ) {
                $page_url = get_permalink($page_id);
                $html_output .= '
                <div class="form-group">
                    <label for="register-terms-and-conditions">
                        <input type="checkbox" name="terms_and_conditions" value="on" id="register-terms-and-conditions" required>
                        '.sprintf(__('You accept our <a href="%s">Terms and Conditions and Privacy Policy</a>', 'freeio'), esc_url($page_url)).'
                    </label>
                </div>';
            }

            echo cmb2_get_metabox_form( $metaboxes_form, $post_id, array(
                'form_format' => '<form action="" class="cmb-form %1$s" method="post" id="%1$s_'.rand(0000,9999).'" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="'.$form_obj->get_form_name().'" value="'.$form_obj->get_form_name().'"><input type="hidden" name="object_id" value="%2$s">%3$s'.$html_output.'<button type="submit" name="submit-cmb-register-freelancer" class="btn btn-theme w-100">%4$s<i class="flaticon-right-up next"></i></button></form>',
                'save_button' => $submit_button_text,
            ) );
        ?>
    </div>
</div>

<script>
    // JavaScript to toggle visibility of Username or Society Name field
    document.addEventListener('DOMContentLoaded', function() {
        var driverTypeRadios = document.querySelectorAll('input[name="driver_type"]');
        var usernameOrSocietyNameField = document.getElementById('username_or_society_name_field');
        var usernameOrSocietyNameLabel = document.querySelector('#username_or_society_name label');

        driverTypeRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.value === 'society') {
                    usernameOrSocietyNameLabel.textContent = '<?php echo esc_js(__('Society Name:', 'wp-freeio')); ?>';
                    usernameOrSocietyNameField.setAttribute('placeholder', '<?php echo esc_attr(__('Enter society name', 'wp-freeio')); ?>');
                } else {
                    usernameOrSocietyNameLabel.textContent = '<?php echo esc_js(__('Username:', 'wp-freeio')); ?>';
                    usernameOrSocietyNameField.setAttribute('placeholder', '<?php echo esc_attr(__('Enter username', 'wp-freeio')); ?>');
                }
            });
        });
    });
</script>