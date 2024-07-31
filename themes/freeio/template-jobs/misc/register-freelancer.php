<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="register-form-wrapper">
    <div class="container-form">

        <?php if (sizeof($form_obj->errors)) : ?>
            <ul class="alert alert-danger errors">
                <?php foreach ($form_obj->errors as $message) { ?>
                    <div class="message_line danger">
                        <?php echo wp_kses_post($message); ?>
                    </div>
                <?php } ?>
            </ul>
        <?php endif; ?>

        <?php if (sizeof($form_obj->success_msg)) : ?>
            <ul class="alert alert-info success">
                <?php foreach ($form_obj->success_msg as $message) { ?>
                    <div class="message_line info">
                        <?php echo wp_kses_post($message); ?>
                    </div>
                <?php } ?>
            </ul>
        <?php endif; ?>

        <?php
        $html_output = '';
        $html_output .= '<div class="form-group">
        <label><input type="radio" checked name="driver_type" value="society" required> '. __('Society:', 'wp-freeio') . '</label>&nbsp;&nbsp;
        <label><input type="radio" name="driver_type" value="entrepreneur"> '. __('Entrepreneur:', 'wp-freeio') . '</label>&nbsp;&nbsp;
        <label><input type="radio" name="driver_type" value="other"> '. __('Other:', 'wp-freeio') . '</label>
        </div>';

         // Full Name or First & Last Name fields
         $html_output .= '<div class="form-group" id="name_fields">
         <div id="full_name_field" style="display: block;">
             <label for="_freelancer_name">' . __('Full Name:', 'wp-freeio') . '</label>
             <input type="text" name="_freelancer_name" id="_freelancer_name" class="form-control" placeholder="' . esc_attr(__('Enter full name', 'wp-freeio')) . '" required>
         </div>
         <div id="first_last_name_fields" style="display: none;">
             <label for="_freelancer_first_name">' . __('First Name:', 'wp-freeio') . '</label>
             <input type="text" name="_freelancer_first_name" id="_freelancer_first_name" class="form-control" placeholder="' . esc_attr(__('Enter first name', 'wp-freeio')) . '" required>
             <label for="_freelancer_last_name">' . __('Last Name:', 'wp-freeio') . '</label>
             <input type="text" name="_freelancer_last_name" id="_freelancer_last_name" class="form-control" placeholder="' . esc_attr(__('Enter last name', 'wp-freeio')) . '" required>
         </div>
     </div>';

     // Hidden input to store the combined name value
     $html_output .= '<div id="hidden_name_container" style="display: none;">
         <input type="text" id="_freelancer_username" name="_freelancer_username" value="">
     </div>';

        // Email field
        $html_output .= '<div class="form-group">
            <label for="_freelancer_email">' . __('Email:', 'wp-freeio') . '</label>
            <input type="email" name="_freelancer_email" id="_freelancer_email" class="form-control" placeholder="' . esc_attr(__('Enter your email', 'wp-freeio')) . '" required>
        </div>';

        // Password field with toggle show/hide functionality
        $html_output .= '<div class="form-group cmb2-wrap">
            <label for="hide_show_password">' . __('Password:', 'wp-freeio') . '</label>
            <span class="show_hide_password_wrapper">
                <input type="password" class="form-control" name="_freelancer_password" id="hide_show_password" value="" data-lpignore="1" autocomplete="off" data-hash="gl70hk4g8ss0" placeholder="' . esc_attr(__('Password', 'wp-freeio')) . '" required>
                <a class="toggle-password" title="' . esc_attr(__('Show', 'wp-freeio')) . '"><span class="dashicons dashicons-hidden"></span></a>
            </span>
        </div>';

        // Confirm Password field
        $html_output .= '<div class="form-group cmb2-wrap">
            <label for="_freelancer_confirmpassword">' . __('Confirm Password:', 'wp-freeio') . '</label>
            <span class="show_hide_password_wrapper">
            <input type="password" class="form-control" name="_freelancer_confirmpassword" id="hide_show_password_confirm" value="" data-lpignore="1" autocomplete="off" data-hash="gl70hk4g8ss0" placeholder="' . esc_attr(__('Confirm Password', 'wp-freeio')) . '" required>
            <a class="toggle-password" title="' . esc_attr(__('Show', 'wp-freeio')) . '"><span class="dashicons dashicons-hidden"></span></a>
        </span>       
         </div>';

        // Phone number field
        $html_output .= '<div class="form-group">
            <label for="_freelancer_phone">' . __('Whatsapp Phone Number:', 'wp-freeio') . '</label>
            <input type="tel" name="_freelancer_phone" id="_freelancer_phone" class="form-control" placeholder="' . esc_attr(__('Enter your whatsapp phone Number', 'wp-freeio')) . '" required>
        </div>';

        if (WP_Freeio_Recaptcha::is_recaptcha_enabled()) {
            $html_output .= '<div id="recaptcha-register-freelancer" class="ga-recaptcha margin-bottom-25" data-sitekey="' . esc_attr(wp_freeio_get_option('recaptcha_site_key')) . '"></div>';
        }

        // Address city and county select fields
        $html_output .= '
        <div class="form-group">
            <label for="city_select">' . esc_html__('City', 'wp-freeio') . '</label>
            <select id="city_select" class="form-control" required>
                <option value="">' . esc_html__('Select City', 'wp-freeio') . '</option>
                <option value="rabat">' . esc_html__('Rabat', 'wp-freeio') . '</option>
                <option value="casa">' . esc_html__('Casablanca', 'wp-freeio') . '</option>
                <option value="marrakech">' . esc_html__('Marrakech', 'wp-freeio') . '</option>
            </select>
        </div>
        <div class="form-group">
            <label for="county_select">' . esc_html__('County', 'wp-freeio') . '</label>
            <select id="county_select" name="_freelancer_address" class="form-control" required>
                <option value="">' . esc_html__('Select County', 'wp-freeio') . '</option>
            </select>
            <input type="hidden" name="_freelancer_address" id="_freelancer_address" value="">
        </div>';

        // Terms and Conditions
        $page_id = wp_freeio_get_option('terms_conditions_page_id');
        $page_id = WP_Freeio_Mixes::get_lang_post_id($page_id);
        if (!empty($page_id)) {
            $page_url = get_permalink($page_id);
            $html_output .= '
            <div class="form-group">
                <label for="register-terms-and-conditions">
                    <input type="checkbox" name="terms_and_conditions" value="on" id="register-terms-and-conditions" required>
                    ' . sprintf(__('You accept our <a href="%s">Terms and Conditions and Privacy Policy</a>', 'freeio'), esc_url($page_url)) . '
                </label>
            </div>';
        }

        echo cmb2_get_metabox_form($metaboxes_form, $post_id, array(
            'form_format' => '<form action="" class="cmb-form %1$s" method="post" id="%1$s_' . rand(0000, 9999) . '" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="' . $form_obj->get_form_name() . '" value="' . $form_obj->get_form_name() . '"><input type="hidden" name="object_id" value="%2$s">%3$s' . $html_output . '<button type="submit" name="submit-cmb-register-freelancer" class="btn btn-theme w-100">%4$s<i class="flaticon-right-up next"></i></button></form>',
            'save_button' => $submit_button_text,
        ));
        ?>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var driverTypeRadios = document.querySelectorAll('input[name="driver_type"]');
        var fullNameField = document.getElementById('full_name_field');
        var firstLastNameFields = document.getElementById('first_last_name_fields');
        var hiddenNameInput = document.getElementById('_freelancer_username');
        var nameInput = document.getElementById('_freelancer_name');
        var firstNameInput = document.getElementById('_freelancer_first_name');
        var lastNameInput = document.getElementById('_freelancer_last_name');

        driverTypeRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.value === 'society') {
                    fullNameField.style.display = 'block';
                    firstLastNameFields.style.display = 'none';
                    hiddenNameInput.value = nameInput.value;
                } else {
                    fullNameField.style.display = 'none';
                    firstLastNameFields.style.display = 'block';
                    var firstName = firstNameInput.value || '';
                    var lastName = lastNameInput.value || '';
                    hiddenNameInput.value = firstName + ' ' + lastName;
                }
            });
        });

        // Update hidden input value on input change
        nameInput.addEventListener('input', function() {
            if (document.querySelector('input[name="driver_type"]:checked').value === 'society') {
                hiddenNameInput.value = this.value;
            }
        });

        firstNameInput.addEventListener('input', function() {
            if (document.querySelector('input[name="driver_type"]:checked').value !== 'society') {
                hiddenNameInput.value = this.value + ' ' + (lastNameInput.value || '');
            }
        });

        lastNameInput.addEventListener('input', function() {
            if (document.querySelector('input[name="driver_type"]:checked').value !== 'society') {
                hiddenNameInput.value = (firstNameInput.value || '') + ' ' + this.value;
            }
        });

        const citySelect = document.getElementById('city_select');
        const countySelect = document.getElementById('county_select');
        const addressInput = document.getElementById('_freelancer_address');

        const counties = {
            'rabat': ['County 1', 'County 2', 'County 3'],
            'casa': ['County A', 'County B', 'County C'],
            'marrakech': ['County X', 'County Y', 'County Z']
        };

        citySelect.addEventListener('change', function() {
            const selectedCity = this.value;
            countySelect.innerHTML = '<option value=""><?php esc_html_e('Select County', 'wp-freeio'); ?></option>';

            if (counties[selectedCity]) {
                counties[selectedCity].forEach(function(county) {
                    const option = document.createElement('option');
                    option.value = county;
                    option.textContent = county;
                    countySelect.appendChild(option);
                });
            }

            // Update address field with selected city
            updateAddress();
        });

        countySelect.addEventListener('change', function() {
            // Update address field with selected county
            updateAddress();
        });

        function updateAddress() {
            const selectedCity = citySelect.value;
            const selectedCounty = countySelect.value;

            if (selectedCity && selectedCounty) {
                addressInput.value = selectedCity + ', ' + selectedCounty;
            } else {
                addressInput.value = '';
            }
        }

        // Toggle password visibility
        var togglePassword = document.querySelectorAll('.toggle-password');

        togglePassword.forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                var passwordInput = this.previousElementSibling;
                var icon = this.querySelector('span');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordInput.style.marginBottom = '20px'; // Add margin-bottom
                    icon.classList.remove('dashicons-hidden');
                    icon.classList.add('dashicons-visibility');
                } else {
                    passwordInput.type = 'password';
                    passwordInput.style.marginBottom = '20'; // Remove margin-bottom
                    icon.classList.remove('dashicons-visibility');
                    icon.classList.add('dashicons-hidden');
                }
            });
        });

    });
</script>
