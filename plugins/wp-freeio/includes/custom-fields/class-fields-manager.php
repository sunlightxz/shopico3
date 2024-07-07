<?php
/**
 * Fields Manager
 *
 * @package    wp-freeio
 * @author     Habq
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
 
class WP_Freeio_Fields_Manager {

	public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_page' ), 1 );
        add_action( 'init', array(__CLASS__, 'init_hook'), 10 );
	}

    public static function register_page() {
        add_submenu_page( 'edit.php?post_type=job_listing', __( 'Fields Manager', 'wp-freeio' ), __( 'Fields Manager', 'wp-freeio' ), 'manage_options', 'job_listing-manager-fields-manager', array( __CLASS__, 'output_job_fields' ), 9 );

        add_submenu_page( 'edit.php?post_type=employer', __( 'Fields Manager', 'wp-freeio' ), __( 'Fields Manager', 'wp-freeio' ), 'manage_options', 'employer-manager-fields-manager', array( __CLASS__, 'output_employer_fields' ), 9 );

        add_submenu_page( 'edit.php?post_type=freelancer', __( 'Fields Manager', 'wp-freeio' ), __( 'Fields Manager', 'wp-freeio' ), 'manage_options', 'freelancer-manager-fields-manager', array( __CLASS__, 'output_freelancer_fields' ), 9 );

        add_submenu_page( 'edit.php?post_type=service', __( 'Fields Manager', 'wp-freeio' ), __( 'Fields Manager', 'wp-freeio' ), 'manage_options', 'service-manager-fields-manager', array( __CLASS__, 'output_service_fields' ), 9 );

        add_submenu_page( 'edit.php?post_type=project', __( 'Fields Manager', 'wp-freeio' ), __( 'Fields Manager', 'wp-freeio' ), 'manage_options', 'project-manager-fields-manager', array( __CLASS__, 'output_project_fields' ), 9 );
    }

    public static function init_hook() {
        // Ajax endpoints.
        add_action( 'wpfi_ajax_wp_freeio_custom_field_html', array( __CLASS__, 'custom_field_html' ) );

        add_action( 'wpfi_ajax_wp_freeio_custom_field_available_html', array( __CLASS__, 'custom_field_available_html' ) );

        // compatible handlers.
        // custom fields
        add_action( 'wp_ajax_wp_freeio_custom_field_html', array( __CLASS__, 'custom_field_html' ) );
        add_action( 'wp_ajax_nopriv_wp_freeio_custom_field_html', array( __CLASS__, 'custom_field_html' ) );

        add_action( 'wp_ajax_wp_freeio_custom_field_available_html', array( __CLASS__, 'custom_field_available_html' ) );
        add_action( 'wp_ajax_nopriv_wp_freeio_custom_field_available_html', array( __CLASS__, 'custom_field_available_html' ) );

        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'scripts' ), 1 );
    }

    public static function scripts() {
        wp_enqueue_style('wp-freeio-custom-field-css', WP_FREEIO_PLUGIN_URL . 'assets/admin/style.css');
        
        // icon
        if ( !empty($_GET['page']) && ($_GET['page'] == 'job_listing-manager-fields-manager' || $_GET['page'] == 'employer-manager-fields-manager' || $_GET['page'] == 'freelancer-manager-fields-manager' || $_GET['page'] == 'service-manager-fields-manager' || $_GET['page'] == 'project-manager-fields-manager') ) {
            wp_enqueue_style('jquery-fonticonpicker', WP_FREEIO_PLUGIN_URL. 'assets/admin/jquery.fonticonpicker.min.css', array(), '1.0');
            wp_enqueue_style('jquery-fonticonpicker-bootstrap', WP_FREEIO_PLUGIN_URL. 'assets/admin/jquery.fonticonpicker.bootstrap.min.css', array(), '1.0');
            wp_enqueue_script('jquery-fonticonpicker', WP_FREEIO_PLUGIN_URL. 'assets/admin/jquery.fonticonpicker.min.js', array(), '1.0', true);

            
            wp_register_script('wp-freeio-custom-field', WP_FREEIO_PLUGIN_URL.'assets/admin/functions.js', array('jquery', 'wp-color-picker'), '', true);

            $args = array(
                'plugin_url' => WP_FREEIO_PLUGIN_URL,
                'ajax_url' => admin_url('admin-ajax.php'),
            );
            wp_localize_script('wp-freeio-custom-field', 'wp_freeio_customfield_common_vars', $args);
            wp_enqueue_script('wp-freeio-custom-field');

            wp_enqueue_script('jquery-ui-sortable');
        }
    }

    public static function output_html($prefix = WP_FREEIO_JOB_LISTING_PREFIX) {
        
        self::save($prefix);
        $rand_id = rand(123, 9878787);
        $default_fields = self::get_all_field_types();
        $post_type = '';
        if ( $prefix == WP_FREEIO_JOB_LISTING_PREFIX ) {
            $available_fields = self::get_all_types_job_listing_fields_available();
            $required_types = self::get_all_types_job_listing_fields_required();
            $post_type = 'job_listing';
        } elseif ( $prefix == WP_FREEIO_EMPLOYER_PREFIX ) {
            $available_fields = self::get_all_types_employer_fields_available();
            $required_types = self::get_all_types_employer_fields_required();
            $post_type = 'employer';
        } elseif ( $prefix == WP_FREEIO_FREELANCER_PREFIX ) {
            $available_fields = self::get_all_types_freelancer_fields_available();
            $required_types = self::get_all_types_freelancer_fields_required();
            $post_type = 'freelancer';
        } elseif ( $prefix == WP_FREEIO_SERVICE_PREFIX ) {
            $available_fields = self::get_all_types_service_fields_available();
            $required_types = self::get_all_types_service_fields_required();
            $post_type = 'service';
        } elseif ( $prefix == WP_FREEIO_PROJECT_PREFIX ) {
            $available_fields = self::get_all_types_project_fields_available();
            $required_types = self::get_all_types_project_fields_required();
            $post_type = 'project';
        }

        $custom_all_fields_saved_data = self::get_custom_fields_data($prefix);

        ?>
        <h1><?php echo esc_html__('Fields manager', 'wp-freeio'); ?></h1>

        <form class="job_listing-manager-options" method="post" action="" data-prefix="<?php echo esc_attr($prefix); ?>">
            
            <button type="submit" class="button button-primary" name="updateWPJBFieldManager"><?php esc_html_e('Update', 'wp-freeio'); ?></button>
            
            <div class="custom-fields-wrapper clearfix">
                            
                <div class="wp-freeio-custom-field-form" id="wp-freeio-custom-field-form-<?php echo esc_attr($rand_id); ?>">
                    <div class="box-wrapper">
                        <h3 class="title"><?php echo esc_html('List of Fields', 'wp-freeio'); ?></h3>
                        <ul id="foo<?php echo esc_attr($rand_id); ?>" class="block__list block__list_words"> 
                            <?php

                            $count_node = 1000;
                            $output = '';
                            $all_fields_name_count = 0;
                            $disabled_fields = array();

                            if (is_array($custom_all_fields_saved_data) && sizeof($custom_all_fields_saved_data) > 0) {
                                $field_names_counter = 0;
                                $types = self::get_all_field_type_keys();
                                foreach ($custom_all_fields_saved_data as $key => $custom_field_saved_data) {
                                    $all_fields_name_count++;
                                    
                                    $li_rand_id = rand(454, 999999);

                                    $output .= '<li class="custom-field-class-' . $li_rand_id . '">';

                                    $fieldtype = $custom_field_saved_data['type'];

                                    $delete = true;
                                    $drfield_values = self::get_field_id($fieldtype, $required_types);
                                    $dvfield_values = self::get_field_id($fieldtype, $available_fields);

                                    if ( !empty($drfield_values) ) {
                                        $count_node ++;
                                        
                                        $delete = false;
                                        $field_values = wp_parse_args( $custom_field_saved_data, $drfield_values);
                                        if ( in_array( $fieldtype, array( $prefix.'title', $prefix.'expiry_date', $prefix.'featured', $prefix.'urgent', $prefix.'verified', $prefix.'filled', $prefix.'posted_by', $prefix.'attached_user' ) ) ) {
                                            $output .= apply_filters('wp_freeio_custom_field_available_simple_html', $fieldtype, $count_node, $field_values, $prefix);
                                        } elseif ( in_array( $fieldtype, array( $prefix.'description' ) ) ) {
                                            $output .= apply_filters('wp_freeio_custom_field_available_description_html', $fieldtype, $count_node, $field_values, $prefix);
                                        } else {
                                            $output .= apply_filters('wp_freeio_custom_field_available_'.$fieldtype.'_html', $fieldtype, $count_node, $field_values, $prefix);
                                        }
                                    } elseif ( !empty($dvfield_values) ) {
                                        $count_node ++;
                                        $field_values = wp_parse_args( $custom_field_saved_data, $dvfield_values);

                                        $dtypes = apply_filters( 'wp_freeio_list_simple_type', array( $prefix.'featured', $prefix.'urgent', $prefix.'verified', $prefix.'address', $prefix.'salary', $prefix.'max_salary', $prefix.'min_rate', $prefix.'max_rate', $prefix.'application_deadline_date', $prefix.'apply_url', $prefix.'apply_email', $prefix.'video', $prefix.'profile_url', $prefix.'email', $prefix.'founded_date', $prefix.'website', $prefix.'phone', $prefix.'video_url', $prefix.'socials', $prefix.'team_members', $prefix.'employees', $prefix.'show_profile', $prefix.'tagline', WP_FREEIO_FREELANCER_PREFIX.'experience', $prefix.'education', $prefix.'award', $prefix.'skill', $prefix.'tag', $prefix.'company_size', $prefix.'faq', $prefix.'addons', $prefix.'price', $prefix.'max_price', $prefix.'map_location', $prefix.'firstname', $prefix.'lastname', $prefix.'job_title', $prefix.'estimated_hours', $prefix.'price_type', $prefix.'price_packages' ) );

                                        if ( in_array( $fieldtype, $dtypes) ) {
                                            $output .= apply_filters('wp_freeio_custom_field_available_simple_html', $fieldtype, $count_node, $field_values, $prefix);
                                        } elseif ( in_array( $fieldtype, array( $prefix.'category', $prefix.'type', $prefix.'project_skill', $prefix.'project_duration', $prefix.'project_experience', WP_FREEIO_PROJECT_PREFIX.'freelancer_type', $prefix.'project_language', $prefix.'project_level' ) ) ) {
                                            $output .= apply_filters('wp_freeio_custom_field_available_tax_html', $fieldtype, $count_node, $field_values, $prefix);
                                        } elseif ( in_array($fieldtype, array( $prefix.'featured_image', $prefix.'logo', $prefix.'gallery', $prefix.'attachments', $prefix.'cover_photo', $prefix.'profile_photos', $prefix.'cv_attachment', $prefix.'photos' ) )) {
                                            $output .= apply_filters('wp_freeio_custom_field_available_files_html', $fieldtype, $count_node, $field_values, $prefix);
                                        }  elseif ( in_array($fieldtype, array( $prefix.'experience_time', $prefix.'experience', $prefix.'gender', WP_FREEIO_FREELANCER_PREFIX.'freelancer_type', $prefix . 'english_level', $prefix.'industry', $prefix.'qualification', $prefix.'career_level', $prefix.'age', $prefix.'languages', $prefix . 'response_time', $prefix . 'delivery_time', $prefix . 'location_type') )) {
                                            $output .= apply_filters( 'wp_freeio_custom_field_available_select_option_html', $fieldtype, $count_node, $field_values, $prefix );
                                        } elseif ( in_array( $fieldtype, array( $prefix.'salary_type') ) ) {
                                            $output .= apply_filters('wp_freeio_custom_field_available_salary_type_html', $fieldtype, $count_node, $field_values, $prefix);
                                        }  elseif ( in_array($fieldtype, array( $prefix.'project_type' ) )) {
                                            $output .= apply_filters( 'wp_freeio_custom_field_available_project_type_html',  $fieldtype, $count_node, $field_values, $prefix );
                                        } elseif ( in_array( $fieldtype, array( $prefix.'apply_type') ) ) {
                                            $output .= apply_filters('wp_freeio_custom_field_available_apply_type_html', $fieldtype, $count_node, $field_values, $prefix);
                                        } elseif ( in_array($fieldtype, array( $prefix.'location' ) )) {
                                            $output .= apply_filters( 'wp_freeio_custom_field_available_location_html', $fieldtype, $count_node, $field_values, $prefix );
                                        } else {
                                            $output .= apply_filters('wp_freeio_custom_field_available_'.$fieldtype.'_html', $fieldtype, $count_node, $field_values, $prefix);
                                        }
                                        $disabled_fields[] = $fieldtype;
                                    } elseif ( in_array($fieldtype, $types) ) {
                                        $count_node ++;
                                        if ( in_array( $fieldtype, array('text', 'textarea', 'wysiwyg', 'number', 'url', 'email', 'checkbox') ) ) {
                                            $output .= apply_filters('wp_freeio_custom_field_text_html', $fieldtype, $count_node, $custom_field_saved_data, $prefix);
                                        } elseif ( in_array( $fieldtype, array('select', 'multiselect', 'radio') ) ) {
                                            $output .= apply_filters('wp_freeio_custom_field_opts_html', $fieldtype, $count_node, $custom_field_saved_data, $prefix);
                                        } else {
                                            $output .= apply_filters('wp_freeio_custom_field_'.$fieldtype.'_html', $fieldtype, $count_node, $custom_field_saved_data, $prefix);
                                        }
                                    }

                                    $output .= apply_filters('wp_freeio_custom_field_actions_html', $li_rand_id, $count_node, $fieldtype, $delete);
                                    $output .= '</li>';
                                }
                            } else {
                                foreach ($required_types as $field_values) {
                                    $count_node ++;
                                    $li_rand_id = rand(454, 999999);
                                    $output .= '<li class="custom-field-class-' . $li_rand_id . '">';
                                    $output .= apply_filters('wp_freeio_custom_field_available_simple_html', $field_values['id'], $count_node, $field_values, $prefix);

                                    $output .= apply_filters('wp_freeio_custom_field_actions_html', $li_rand_id, $count_node, $field_values['id'], false);
                                    $output .= '</li>';
                                }
                            }
                            echo force_balance_tags($output);
                            ?>
                        </ul>

                        <button type="submit" class="button button-primary" name="updateWPJBFieldManager"><?php esc_html_e('Update', 'wp-freeio'); ?></button>

                        <div class="input-field-types">
                            <h3><?php esc_html_e('Create a custom field', 'wp-freeio'); ?></h3>
                            <div class="input-field-types-wrapper">
                                <select name="field-types" class="wp-freeio-field-types">
                                    <?php foreach ($default_fields as $group) { ?>
                                        <optgroup label="<?php echo esc_attr($group['title']); ?>">
                                            <?php foreach ($group['fields'] as $value => $label) { ?>
                                                <option value="<?php echo esc_attr($value); ?>"><?php echo $label; ?></option>
                                            <?php } ?>
                                        </optgroup>
                                    <?php } ?>
                                </select>
                                <button type="button" class="button btn-add-field" data-randid="<?php echo esc_attr($rand_id); ?>"><?php esc_html_e('Create', 'wp-freeio'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wp-freeio-form-field-list wp-freeio-list">
                    <h3 class="title"><?php esc_html_e('Available Fields', 'wp-freeio'); ?></h3>
                    <?php if ( !empty($available_fields) ) { ?>
                        <ul>
                            <?php foreach ($available_fields as $field) { ?>
                                <li class="<?php echo esc_attr($field['id']); ?> <?php echo esc_attr(in_array($field['id'], $disabled_fields) ? 'disabled' : ''); ?>">
                                    <a class="wp-freeio-custom-field-add-available-field" data-fieldtype="<?php echo esc_attr($field['id']); ?>" data-randid="<?php echo esc_attr($rand_id); ?>" href="javascript:void(0);" data-fieldlabel="<?php echo esc_attr($field['name']); ?>">
                                        <span class="icon-wrapper">
                                            <i class="dashicons dashicons-plus"></i>
                                        </span>
                                        <?php echo esc_html($field['name']); ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
                <div class="clearfix" style="clear: both;"></div>
            </div>

            <script>
                var global_custom_field_counter = <?php echo intval($all_fields_name_count); ?>;
                jQuery(document).ready(function () {
                    
                    jQuery('#foo<?php echo esc_attr($rand_id); ?>').sortable({
                        group: "words",
                        animation: 150,
                        handle: ".field-intro",
                        cancel: ".form-group-wrapper"
                    });
                });
            </script>
        </form>
        <?php
    }

    public static function output_job_fields() {
        self::output_html(WP_FREEIO_JOB_LISTING_PREFIX);
    }

    public static function output_employer_fields() {
        self::output_html(WP_FREEIO_EMPLOYER_PREFIX);
    }

    public static function output_freelancer_fields() {
        self::output_html(WP_FREEIO_FREELANCER_PREFIX);
    }

    public static function output_service_fields() {
        self::output_html(WP_FREEIO_SERVICE_PREFIX);
    }

    public static function output_project_fields() {
        self::output_html(WP_FREEIO_PROJECT_PREFIX);
    }

    public static function save($prefix) {
        if ( isset( $_POST['updateWPJBFieldManager'] ) ) {

            $custom_field_final_array = $counts = array();
            $field_index = 0;
            if ( !empty($_POST['wp-freeio-custom-fields-type']) ) {
                foreach ($_POST['wp-freeio-custom-fields-type'] as $field_type) {
                    $custom_fields_id = isset($_POST['wp-freeio-custom-fields-id'][$field_index]) ? $_POST['wp-freeio-custom-fields-id'][$field_index] : '';
                    $counter = 0;
                    if ( isset($counts[$field_type]) ) {
                        $counter = $counts[$field_type];
                    }
                    $custom_field_final_array[] = self::custom_field_ready_array($counter, $field_type, $custom_fields_id);
                    $counter++;
                    $counts[$field_type] = $counter;
                    $field_index++;
                }
            }
            $option_key = self::get_custom_fields_key($prefix);

            update_option($option_key, $custom_field_final_array);
            
        }
    }

    public static function custom_field_ready_array($array_counter = 0, $field_type = '', $custom_fields_id = '') {
        $custom_field_element_array = array();
        $custom_field_element_array['type'] = $field_type;
        if ( !empty($_POST["wp-freeio-custom-fields-{$field_type}"]) ) {
            foreach ($_POST["wp-freeio-custom-fields-{$field_type}"] as $field => $value) {
                if ( isset($value[$custom_fields_id]) ) {
                    $custom_field_element_array[$field] = $value[$custom_fields_id];
                } elseif ( isset($value[$array_counter]) ) {
                    $custom_field_element_array[$field] = $value[$array_counter];
                }
            }
        }
        return $custom_field_element_array;
    }

    public static function get_custom_fields_data($prefix) {
        $option_key = self::get_custom_fields_key($prefix);
        return apply_filters( 'wp-freeio-get-custom-fields-data', get_option($option_key, array()), $prefix );
    }

    public static function get_custom_fields_key($prefix) {
        return apply_filters( 'wp-freeio-get-custom-fields-key', 'wp_freeio_'.$prefix.'_fields_data', $prefix );
    }

    public static function get_field_id($id, $fields) {
        if ( !empty($fields) && is_array($fields) ) {
            foreach ($fields as $field) {
                if ( $field['id'] == $id ) {
                    return $field;
                }
            }
        }
        return array();
    }

    public static function get_all_field_types() {
        $fields = apply_filters( 'wp_freeio_get_default_field_types', array(
            array(
                'title' => esc_html__('Direct Input', 'wp-freeio'),
                'fields' => array(
                    'text' => esc_html__('Text', 'wp-freeio'),
                    'textarea' => esc_html__('Textarea', 'wp-freeio'),
                    'wysiwyg' => esc_html__('WP Editor', 'wp-freeio'),
                    'date' => esc_html__('Date', 'wp-freeio'),
                    'number' => esc_html__('Number', 'wp-freeio'),
                    'url' => esc_html__('Url', 'wp-freeio'),
                    'email' => esc_html__('Email', 'wp-freeio'),
                )
            ),
            array(
                'title' => esc_html__('Choices', 'wp-freeio'),
                'fields' => array(
                    'select' => esc_html__('Select', 'wp-freeio'),
                    'multiselect' => esc_html__('Multiselect', 'wp-freeio'),
                    'checkbox' => esc_html__('Checkbox', 'wp-freeio'),
                    'radio' => esc_html__('Radio Buttons', 'wp-freeio'),
                )
            ),
            array(
                'title' => esc_html__('Form UI', 'wp-freeio'),
                'fields' => array(
                    'heading' => esc_html__('Heading', 'wp-freeio')
                )
            ),
            array(
                'title' => esc_html__('Others', 'wp-freeio'),
                'fields' => array(
                    'file' => esc_html__('File', 'wp-freeio')
                )
            ),
        ));
        
        return $fields;
    }

    public static function get_all_field_type_keys() {
        $fields = self::get_all_field_types();
        $return = array();
        foreach ($fields as $group) {
            foreach ($group['fields'] as $key => $value) {
                $return[] = $key;
            }
        }

        return apply_filters( 'wp_freeio_get_all_field_types', $return );
    }

    public static function get_all_types_job_listing_fields_required() {
        $datepicker_date_format = str_replace(
            array( 'd', 'j', 'l', 'z', /* Day. */ 'F', 'M', 'n', 'm', /* Month. */ 'Y', 'y', /* Year. */ ),
            array( 'dd', 'd', 'DD', 'o', 'MM', 'M', 'm', 'mm', 'yy', 'y', ),
            get_option( 'date_format' )
        );

        $prefix = WP_FREEIO_JOB_LISTING_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Job Title', 'wp-freeio' ),
                'id'                => $prefix . 'title',
                'type'              => 'text',
                'disable_check' => true,
                'required' => true,
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Job Description', 'wp-freeio' ),
                'id'                => $prefix . 'description',
                'type'              => 'textarea',
                'options'           => array(
                    'media_buttons' => false,
                    'textarea_rows' => 8,
                    'wpautop' => true,
                    'tinymce'       => array(
                        'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
                        'paste_as_text'                 => true,
                        'paste_auto_cleanup_on_paste'   => true,
                        'paste_remove_spans'            => true,
                        'paste_remove_styles'           => true,
                        'paste_remove_styles_if_webkit' => true,
                        'paste_strip_class_attributes'  => true,
                    ),
                ),
                'disable_check' => true,
                'required' => true,
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Expiry Date', 'wp-freeio' ),
                'id'                => $prefix . 'expiry_date',
                'type'              => 'wpfi_datepicker',
                'date_format'       => 'Y-m-d',
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
                'attributes' => array(
                    'data-datepicker' => json_encode(array(
                        'dateFormat' => $datepicker_date_format,
                        'altField' => '#'.$prefix . 'expiry_date',
                        'altFormat' => 'yy-mm-dd',
                    ))
                )
            ),
            array(
                'name'              => __( 'Featured Job', 'wp-freeio' ),
                'id'                => $prefix . 'featured',
                'type'              => 'checkbox',
                'description'       => __( 'Featured properties will be sticky during searches, and can be styled differently.', 'wp-freeio' ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_checkbox'),
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
            ),
            array(
                'name'              => __( 'Urgent Job', 'wp-freeio' ),
                'id'                => $prefix . 'urgent',
                'type'              => 'checkbox',
                'description'       => __( 'Urgent jobs will be sticky during searches, and can be styled differently.', 'wp-freeio' ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_checkbox'),
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
            ),
            array(
                'name'              => __( 'Filled', 'wp-freeio' ),
                'id'                => $prefix . 'filled',
                'type'              => 'checkbox',
                'description'       => __( 'Filled listings will no longer accept applications.', 'wp-freeio' ),
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
            ),
        );
        return apply_filters( 'wp-freeio-job_listing-type-required-fields', $fields );
    }

    public static function get_all_types_job_listing_fields_available() {

        $datepicker_date_format = str_replace(
            array( 'd', 'j', 'l', 'z', /* Day. */ 'F', 'M', 'n', 'm', /* Month. */ 'Y', 'y', /* Year. */ ),
            array( 'dd', 'd', 'DD', 'o', 'MM', 'M', 'm', 'mm', 'yy', 'y', ),
            get_option( 'date_format' )
        );

        $prefix = WP_FREEIO_JOB_LISTING_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Banner Image', 'wp-freeio' ),
                'id'                => $prefix . 'featured_image',
                'type'              => 'wp_freeio_file',
                'ajax'              => true,
                'multiple_files'    => false,
                'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'jpg|jpeg|jpe', 'png' ),
            ),
            array(
                'name'              => __( 'Logo Image', 'wp-freeio' ),
                'id'                => $prefix . 'logo',
                'type'              => 'wp_freeio_file',
                'file_multiple'     => false,
                'ajax'              => true,
                'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'png' ),
            ),
            array(
                'name'              => __( 'Application Deadline Date', 'wp-freeio' ),
                'id'                => $prefix . 'application_deadline_date',
                'type'              => 'wpfi_datepicker',
                'attributes' => array(
                    'data-datepicker' => json_encode(array(
                        'dateFormat' => $datepicker_date_format,
                        'altField' => '#'.$prefix . 'application_deadline_date',
                        'altFormat' => 'yy-mm-dd',
                    ))
                )
            ),
            array(
                'name'              => __( 'Job Apply Type', 'wp-freeio' ),
                'id'                => $prefix . 'apply_type',
                'type'              => 'select',
                'options'           => WP_Freeio_Mixes::get_default_apply_types()
            ),
            array(
                'name'              => __( 'External URL for Apply Job', 'wp-freeio' ),
                'id'                => $prefix . 'apply_url',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Job Apply Email', 'wp-freeio' ),
                'id'                => $prefix . 'apply_email',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Phone Number', 'wp-freeio' ),
                'id'                => $prefix . 'phone',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Min. Salary', 'wp-freeio' ),
                'id'                => $prefix . 'salary',
                'type'              => 'text',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_job_salary'),
            ),
            array(
                'name'              => __( 'Max. Salary', 'wp-freeio' ),
                'id'                => $prefix . 'max_salary',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Salary Type', 'wp-freeio' ),
                'id'                => $prefix . 'salary_type',
                'type'              => 'select',
                'options'           => WP_Freeio_Mixes::get_default_salary_types(),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_radio_list'),
            ),
            array(
                'name'              => __( 'Location', 'wp-freeio' ),
                'id'                => $prefix . 'location',
                'type'              => 'wpjb_taxonomy_location',
                'taxonomy'          => 'location',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_location_select'),
                'show_compare'      => true
            ),
            array(
                'name'              => __( 'Friendly Address', 'wp-freeio' ),
                'id'                => $prefix . 'address',
                'type'              => 'text',
            ),
            array(
                'id'                => $prefix . 'map_location',
                'name'              => __( 'Maps Location', 'wp-freeio' ),
                'type'              => 'pw_map',
                'sanitization_cb'   => 'pw_map_sanitise',
                'split_values'      => true,
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_location'),
            ),
            

            // Taxonomies
            array(
                'name'              => __( 'Type', 'wp-freeio' ),
                'id'                => $prefix . 'type',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'job_listing_type',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_select'),
            ),
            array(
                'name'              => __( 'Category', 'wp-freeio' ),
                'id'                => $prefix . 'category',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'job_listing_category',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_select'),
            ),
            array(
                'name'              => __( 'Tag', 'wp-freeio' ),
                'id'                => $prefix . 'tag',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'job_listing_tag',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            
            // custom
            array(
                'name'              => __( 'Experience', 'wp-freeio' ),
                'id'                => $prefix . 'experience',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => 'Fresh
1 Year
2 Year
3 Year
4 Year
5 Year'
            ),
            array(
                'name'              => __( 'Gender', 'wp-freeio' ),
                'id'                => $prefix . 'gender',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => 'Both
Female
Male'
            ),
            array(
                'name'              => __( 'Industry', 'wp-freeio' ),
                'id'                => $prefix . 'industry',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => 'Development
Management
Finance
Html & Css
Seo
Banking
Designer Graphics'
            ),
            array(
                'name'              => __( 'Qualification', 'wp-freeio' ),
                'id'                => $prefix . 'qualification',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => 'Certificate
Associate Degree
Bachelor Degree
Master’s Degree
Doctorate Degree'
            ),
            array(
                'name'              => __( 'Career Level', 'wp-freeio' ),
                'id'                => $prefix . 'career_level',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => 'Manager
Officer
Student
Executive
Others'
            ),
            array(
                'name'              => __( 'Photos', 'wp-freeio' ),
                'id'                => $prefix . 'photos',
                'type'              => 'file_list',
                'file_multiple'     => true,
                'ajax'              => true,
                'multiple_files'    => true,
                'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'png' ),
                'query_args' => array( 'type' => 'image' ), // Only images attachment
                'text' => array(
                    'add_upload_files_text' => __( 'Add or Upload Images', 'wp-freeio' ),
                ),
            ),
            array(
                'name'              => __( 'Introduction Video URL', 'wp-freeio' ),
                'id'                => $prefix . 'video_url',
                'type'              => 'text',
            ),
        );
        return apply_filters( 'wp-freeio-job_listing-type-available-fields', $fields );
    }

    public static function get_all_types_employer_fields_required() {
        $prefix = WP_FREEIO_EMPLOYER_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Employer name', 'wp-freeio' ),
                'id'                => $prefix . 'title',
                'type'              => 'text',
                'default'           => '',
                'attributes'        => array(
                    'required'          => 'required'
                ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input'),
            ),
            array(
                'name'              => __( 'Description', 'wp-freeio' ),
                'id'                => $prefix . 'description',
                'type'              => 'wysiwyg',
                'options' => array(
                    'media_buttons' => false,
                    'textarea_rows' => 8,
                    'wpautop' => true,
                    'tinymce'       => array(
                        'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
                        'paste_as_text'                 => true,
                        'paste_auto_cleanup_on_paste'   => true,
                        'paste_remove_spans'            => true,
                        'paste_remove_styles'           => true,
                        'paste_remove_styles_if_webkit' => true,
                        'paste_strip_class_attributes'  => true,
                    ),
                ),
            ),
            array(
                'name'              => __( 'Featured Employer', 'wp-freeio' ),
                'id'                => $prefix . 'featured',
                'type'              => 'checkbox',
                'description'       => __( 'Featured employer will be sticky during searches, and can be styled differently.', 'wp-freeio' ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_checkbox'),
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
            ),
            array(
                'name'              => __( 'Verified Employer', 'wp-freeio' ),
                'id'                => $prefix . 'verified',
                'type'              => 'checkbox',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_checkbox'),
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
            ),
            array(
                'name'              => __( 'Attached User', 'wp-freeio' ),
                'id'                => $prefix . 'attached_user',
                'type'              => 'wp_freeio_attached_user',
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
                'disable_check_register' => true,
            ),
        );
        return apply_filters( 'wp-freeio-employer-type-required-fields', $fields );
    }

    public static function get_all_types_employer_fields_available() {
        $socials = WP_Freeio_Mixes::get_socials_network();
        $opt_socials = [];
        foreach ($socials as $key => $value) {
            $opt_socials[$key] = $value['title'];
        }
        $prefix = WP_FREEIO_EMPLOYER_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Profile url', 'wp-freeio' ),
                'id'                => $prefix . 'profile_url',
                'type'              => 'wp_freeio_profile_url',
                'disable_check' => true,
                'show_in_submit_form' => 'yes',
                'show_in_admin_edit' => '',
                'disable_check_register' => true,
            ),
            array(
                'name'              => __( 'First Name', 'wp-freeio' ),
                'id'                => $prefix . 'firstname',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Last Name', 'wp-freeio' ),
                'id'                => $prefix . 'lastname',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Email', 'wp-freeio' ),
                'id'                => $prefix . 'email',
                'type'              => 'text',
                'disable_check_register' => true,
            ),
            array(
                'name'              => __( 'Founded Date', 'wp-freeio' ),
                'id'                => $prefix . 'founded_date',
                'type'              => 'text_small',
                'attributes'        => array(
                    'type'              => 'number',
                    'min'               => 0,
                    'pattern'           => '\d*',
                ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_found_date_range_slider'),
            ),
            array(
                'name'              => __( 'Website', 'wp-freeio' ),
                'id'                => $prefix . 'website',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Phone Number', 'wp-freeio' ),
                'id'                => $prefix . 'phone',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Logo Image', 'wp-freeio' ),
                'id'                => $prefix . 'featured_image',
                'type'              => 'wp_freeio_file',
                'file_multiple'         => false,
                'ajax'              => true,
                'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'png' ),
            ),
            array(
                'name'              => __( 'Gallery', 'wp-freeio' ),
                'id'                => $prefix . 'gallery',
                'type'              => 'file_list',
                'file_multiple'     => true,
                'ajax'              => true,
                'multiple_files'    => true,
                'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'png' ),
                'query_args' => array( 'type' => 'image' ), // Only images attachment
                'text' => array(
                    'add_upload_files_text' => __( 'Add or Upload Images', 'wp-freeio' ),
                ),
            ),
            array(
                'name'              => __( 'Video URL', 'wp-freeio' ),
                'id'                => $prefix . 'video_url',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Company Size', 'wp-freeio' ),
                'id'                => $prefix . 'company_size',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Socials', 'wp-freeio' ),
                'id'                => $prefix . 'socials',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Network {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another Network', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove Network', 'wp-freeio' ),
                    'sortable'          => false,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Network', 'wp-freeio' ),
                        'id'        => 'network',
                        'type'      => 'select',
                        'options'   => $opt_socials
                    ),
                    array(
                        'name'      => __( 'Url', 'wp-freeio' ),
                        'id'        => 'url',
                        'type'      => 'text',
                    ),
                ),
            ),
            array(
                'name'              => __( 'Friendly Address', 'wp-freeio' ),
                'id'                => $prefix . 'address',
                'type'              => 'text',
            ),
            array(
                'id'                => $prefix . 'map_location',
                'name'              => __( 'Maps Location', 'wp-freeio' ),
                'type'              => 'pw_map',
                'sanitization_cb'   => 'pw_map_sanitise',
                'split_values'      => true,
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_location'),
            ),
            array(
                'name'              => __( 'Members', 'wp-freeio' ),
                'id'                => $prefix . 'team_members',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Member {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another Member', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove Member', 'wp-freeio' ),
                    'sortable'          => true,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Name', 'wp-freeio' ),
                        'id'        => 'name',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Designation', 'wp-freeio' ),
                        'id'        => 'designation',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Experience', 'wp-freeio' ),
                        'id'        => 'experience',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Profile Image', 'wp-freeio' ),
                        'id'        => 'profile_image',
                        'type'      => 'file',
                        'options' => array(
                            'url' => false, // Hide the text input for the url
                        ),
                        'text'    => array(
                            'add_upload_file_text' => __( 'Add Image', 'wp-freeio' ),
                        ),
                        'query_args' => array(
                            'type' => array(
                                'image/gif',
                                'image/jpeg',
                                'image/png',
                            ),
                        ),
                        'file_multiple'         => false,
                        'ajax'              => true,
                        'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'png' ),
                    ),
                    array(
                        'name'              => __( 'Facebook URL', 'wp-freeio' ),
                        'id'                => 'facebook',
                        'type'              => 'text',
                    ),
                    array(
                        'name'              => __( 'Twitter URL', 'wp-freeio' ),
                        'id'                => 'twitter',
                        'type'              => 'text',
                    ),
                    array(
                        'name'              => __( 'Google Plus URL', 'wp-freeio' ),
                        'id'                => 'google_plus',
                        'type'              => 'text',
                    ),
                    array(
                        'name'              => __( 'Linkedin URL', 'wp-freeio' ),
                        'id'                => 'linkedin',
                        'type'              => 'text',
                    ),
                    array(
                        'name'              => __( 'Dribbble URL', 'wp-freeio' ),
                        'id'                => 'dribbble',
                        'type'              => 'text',
                    ),
                    array(
                        'name'              => __( 'description', 'wp-freeio' ),
                        'id'                => 'description',
                        'type'              => 'textarea',
                    ),
                )
            ),
            array(
                'name'          => __( 'Employees', 'wp-freeio' ),
                'id'            => $prefix . 'employees',
                'type'          => 'user_ajax_search',
                'multiple'      => true,
                'query_args'    => array(
                    'role'              => array( 'wp_freeio_employee' ),
                    'search_columns'    => array( 'user_login', 'user_email' ),
                    'meta_query'        => array(
                        'relation' => 'OR',
                        array(
                            'key'       => 'employee_employer_id',
                            'value'     => '',
                        ),
                        array(
                            'key'       => 'employee_employer_id',
                            'compare' => 'NOT EXISTS',
                        )
                    )
                ),
                'disable_check_register' => true,
            ),

            // taxonimies
            array(
                'name'              => __( 'Categories', 'wp-freeio' ),
                'id'                => $prefix . 'category',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'employer_category',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Location', 'wp-freeio' ),
                'id'                => $prefix . 'location',
                'type'              => 'wpjb_taxonomy_location',
                'taxonomy'          => 'location',
                'attributes'        => array(
                    'placeholder'   => __( 'Select %s', 'wp-freeio' ),
                ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Show my profile', 'wp-freeio' ),
                'id'                => $prefix . 'show_profile',
                'type'              => 'select',
                'options'           => array(
                    'show'  => __( 'Show', 'wp-freeio' ),
                    'hide'  => __( 'Hide', 'wp-freeio' ),
                ),
                'disable_check_register' => true,
            ),
        );
        return apply_filters( 'wp-freeio-employer-type-available-fields', $fields );
    }

    public static function get_all_types_freelancer_fields_required() {
        $datepicker_date_format = str_replace(
            array( 'd', 'j', 'l', 'z', /* Day. */ 'F', 'M', 'n', 'm', /* Month. */ 'Y', 'y', /* Year. */ ),
            array( 'dd', 'd', 'DD', 'o', 'MM', 'M', 'm', 'mm', 'yy', 'y', ),
            get_option( 'date_format' )
        );
        $prefix = WP_FREEIO_FREELANCER_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Full Name', 'wp-freeio' ),
                'id'                => $prefix . 'title',
                'type'              => 'text',
                'default'           => '',
                'attributes'        => array(
                    'required'          => 'required'
                ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input'),
            ),
            array(
                'name'              => __( 'Description', 'wp-freeio' ),
                'id'                => $prefix . 'description',
                'type'              => 'wysiwyg',
                'options' => array(
                    'media_buttons' => false,
                    'textarea_rows' => 8,
                    'wpautop' => true,
                    'tinymce'       => array(
                        'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
                        'paste_as_text'                 => true,
                        'paste_auto_cleanup_on_paste'   => true,
                        'paste_remove_spans'            => true,
                        'paste_remove_styles'           => true,
                        'paste_remove_styles_if_webkit' => true,
                        'paste_strip_class_attributes'  => true,
                    ),
                ),
            ),
            array(
                'name'              => __( 'Featured Freelancer', 'wp-freeio' ),
                'id'                => $prefix . 'featured',
                'type'              => 'checkbox',
                'description'       => __( 'Featured employer will be sticky during searches, and can be styled differently.', 'wp-freeio' ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_checkbox'),
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
            ),
            array(
                'name'              => __( 'Verified Freelancer', 'wp-freeio' ),
                'id'                => $prefix . 'verified',
                'type'              => 'checkbox',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_checkbox'),
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
            ),
            array(
                'name'              => __( 'Attached User', 'wp-freeio' ),
                'id'                => $prefix . 'attached_user',
                'type'              => 'wp_freeio_attached_user',
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
                'disable_check_register' => true,
            ),
            array(
                'name'              => __( 'Expiry Date', 'wp-freeio' ),
                'id'                => $prefix . 'expiry_date',
                'type'              => 'wpfi_datepicker',
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
                'disable_check_register' => true,
                'attributes' => array(
                    'data-datepicker' => json_encode(array(
                        'dateFormat' => $datepicker_date_format,
                        'altField' => '#'.$prefix . 'expiry_date',
                        'altFormat' => 'yy-mm-dd',
                    ))
                )
            ),
        );
        return apply_filters( 'wp-freeio-freelancer-type-required-fields', $fields );
    }

    public static function get_all_types_freelancer_fields_available() {
        $socials = WP_Freeio_Mixes::get_socials_network();
        $opt_socials = [];
        foreach ($socials as $key => $value) {
            $opt_socials[$key] = $value['title'];
        }
        
        $datepicker_date_format = str_replace(
            array( 'd', 'j', 'l', 'z', /* Day. */ 'F', 'M', 'n', 'm', /* Month. */ 'Y', 'y', /* Year. */ ),
            array( 'dd', 'd', 'DD', 'o', 'MM', 'M', 'm', 'mm', 'yy', 'y', ),
            get_option( 'date_format' )
        );

        $prefix = WP_FREEIO_FREELANCER_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Profile url', 'wp-freeio' ),
                'id'                => $prefix . 'profile_url',
                'type'              => 'wp_freeio_profile_url',
                'disable_check' => true,
                'show_in_submit_form' => 'yes',
                'show_in_admin_edit' => '',
                'disable_check_register' => true,
            ),
            array(
                'name'              => __( 'Featured Image', 'wp-freeio' ),
                'id'                => $prefix . 'featured_image',
                'type'              => 'wp_freeio_file',
                'multiple'          => false,
                'default'           => ! empty( $featured_image ) ? $featured_image : '',
                'ajax'              => true,
                'mime_types' => array( 'gif', 'jpeg', 'jpg', 'png' ),
            ),
            array(
                'name'              => __( 'First Name', 'wp-freeio' ),
                'id'                => $prefix . 'firstname',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Last Name', 'wp-freeio' ),
                'id'                => $prefix . 'lastname',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Email', 'wp-freeio' ),
                'id'                => $prefix . 'email',
                'type'              => 'text',
                'disable_check_register' => true,
            ),
            array(
                'name'              => __( 'Show my profile', 'wp-freeio' ),
                'id'                => $prefix . 'show_profile',
                'type'              => 'select',
                'options'           => array(
                    'show'  => __( 'Show', 'wp-freeio' ),
                    'hide'  => __( 'Hide', 'wp-freeio' ),
                ),
                'disable_check_register' => true,
            ),
            array(
                'name'              => __( 'Date of Birth', 'wp-freeio' ),
                'id'                => $prefix . 'founded_date',
                'type'              => 'wpfi_datepicker',
                // 'date_format' => get_option( 'date_format' ),
                'attributes'        => array(
                    'data-datepicker' => json_encode(array(
                        'yearRange' => '-100:+5',
                        'dateFormat' => $datepicker_date_format,
                        'altField' => '#'.$prefix . 'founded_date',
                        'altFormat' => 'yy-mm-dd',
                    ))
                ),
            ),
            array(
                'name'              => __( 'Phone Number', 'wp-freeio' ),
                'id'                => $prefix . 'phone',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Job Title', 'wp-freeio' ),
                'id'                => $prefix . 'job_title',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Minimum per hour rate', 'wp-freeio' ),
                'id'                => $prefix . 'min_rate',
                'type'              => 'text',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_freelancer_salary'),
            ),
            array(
                'name'              => __( 'Maximum per hour rate', 'wp-freeio' ),
                'id'                => $prefix . 'max_rate',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Gallery', 'wp-freeio' ),
                'id'                => $prefix . 'gallery',
                'type'              => 'file_list',
                'file_multiple'     => true,
                'ajax'              => true,
                'multiple_files'    => true,
                'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'png' ),
                'query_args' => array( 'type' => 'image' ), // Only images attachment
                'text' => array(
                    'add_upload_files_text' => __( 'Add or Upload Images', 'wp-freeio' ),
                ),
            ),
            array(
                'name'              => __( 'Resume Attachment', 'wp-freeio' ),
                'id'                => $prefix . 'cv_attachment',
                'type'              => 'file_list',
                'file_multiple'     => true,
                'ajax'              => true,
                'multiple_files'    => true,
                'mime_types'        => array( 'pdf', 'doc', 'docx' ),
                'description'       => __('Upload file .pdf, .doc, .docx', 'wp-freeio')
            ),
            array(
                'name'              => __( 'Introduction Video URL', 'wp-freeio' ),
                'id'                => $prefix . 'video_url',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Socials', 'wp-freeio' ),
                'id'                => $prefix . 'socials',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Network {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another Network', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove Network', 'wp-freeio' ),
                    'sortable'          => false,
                    'closed'         => true,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Network', 'wp-freeio' ),
                        'id'        => 'network',
                        'type'      => 'select',
                        'options'   => $opt_socials
                    ),
                    array(
                        'name'      => __( 'Url', 'wp-freeio' ),
                        'id'        => 'url',
                        'type'      => 'text',
                    ),
                ),
            ),
            array(
                'name'              => __( 'Friendly Address', 'wp-freeio' ),
                'id'                => $prefix . 'address',
                'type'              => 'text',
            ),
            array(
                'id'                => $prefix . 'map_location',
                'name'              => __( 'Maps Location', 'wp-freeio' ),
                'type'              => 'pw_map',
                'sanitization_cb'   => 'pw_map_sanitise',
                'split_values'      => true,
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_location'),
            ),
            array(
                'name'              => __( 'Education', 'wp-freeio' ),
                'id'                => $prefix . 'education',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Education {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another Education', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove Education', 'wp-freeio' ),
                    'sortable'          => false,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Title', 'wp-freeio' ),
                        'id'        => 'title',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Academy', 'wp-freeio' ),
                        'id'        => 'academy',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Year', 'wp-freeio' ),
                        'id'        => 'year',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Description', 'wp-freeio' ),
                        'id'        => 'description',
                        'type'      => 'textarea',
                    ),
                )
            ),
            array(
                'name'              => __( 'Experience', 'wp-freeio' ),
                'id'                => $prefix . 'experience',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Experience {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another Experience', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove Experience', 'wp-freeio' ),
                    'sortable'          => false,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Title', 'wp-freeio' ),
                        'id'        => 'title',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Start Date', 'wp-freeio' ),
                        'id'        => 'start_date',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'End Date', 'wp-freeio' ),
                        'id'        => 'end_date',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Company', 'wp-freeio' ),
                        'id'        => 'company',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Description', 'wp-freeio' ),
                        'id'        => 'description',
                        'type'      => 'textarea',
                    ),
                )
            ),
            array(
                'name'              => __( 'Award', 'wp-freeio' ),
                'id'                => $prefix . 'award',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Award {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another Award', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove Award', 'wp-freeio' ),
                    'sortable'          => false,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Title', 'wp-freeio' ),
                        'id'        => 'title',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Year', 'wp-freeio' ),
                        'id'        => 'year',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Description', 'wp-freeio' ),
                        'id'        => 'description',
                        'type'      => 'textarea',
                    ),
                )
            ),
            array(
                'name'              => __( 'Skill', 'wp-freeio' ),
                'id'                => $prefix . 'skill',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Skill {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another Skill', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove Skill', 'wp-freeio' ),
                    'sortable'          => false,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Title', 'wp-freeio' ),
                        'id'        => 'title',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Percentage', 'wp-freeio' ),
                        'id'        => 'percentage',
                        'type'      => 'text',
                        'attributes'        => array(
                            'type'              => 'number',
                            'min'               => 0,
                            'pattern'           => '\d*',
                        )
                    ),
                )
            ),

            // taxonomies
            array(
                'name'              => __( 'Categories', 'wp-freeio' ),
                'id'                => $prefix . 'category',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'freelancer_category',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Location', 'wp-freeio' ),
                'id'                => $prefix . 'location',
                'type'              => 'wpjb_taxonomy_location',
                'taxonomy'          => 'location',
                'attributes'        => array(
                    'placeholder'   => __( 'Select %s', 'wp-freeio' ),
                ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Tag', 'wp-freeio' ),
                'id'                => $prefix . 'tag',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'freelancer_tag',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),

            // custom
            array(
                'name'              => __( 'Freelancer type', 'wp-freeio' ),
                'id'                => $prefix . 'freelancer_type',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => 'Agency Freelancers
Independent Freelancers
New Rising Talent'
            ),
            array(
                'name'              => __( 'Gender', 'wp-freeio' ),
                'id'                => $prefix . 'gender',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => 'Both
Female
Male'
            ),
            array(
                'name'              => __( 'English Level', 'wp-freeio' ),
                'id'                => $prefix . 'english_level',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => 'Basic
Conversational
Fluent
Native Or Bilingual
Professional'
            ),
            array(
                'name'              => __( 'FAQ', 'wp-freeio' ),
                'id'                => $prefix . 'faq',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'FAQ {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another FAQ', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove FAQ', 'wp-freeio' ),
                    'sortable'          => false,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Question', 'wp-freeio' ),
                        'id'        => 'question',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Answer', 'wp-freeio' ),
                        'id'        => 'answer',
                        'type'      => 'textarea',
                    ),
                ),
            ),
        );
        return apply_filters( 'wp-freeio-freelancer-type-available-fields', $fields );
    }

    public static function get_all_types_service_fields_required() {
        $datepicker_date_format = str_replace(
            array( 'd', 'j', 'l', 'z', /* Day. */ 'F', 'M', 'n', 'm', /* Month. */ 'Y', 'y', /* Year. */ ),
            array( 'dd', 'd', 'DD', 'o', 'MM', 'M', 'm', 'mm', 'yy', 'y', ),
            get_option( 'date_format' )
        );
        $prefix = WP_FREEIO_SERVICE_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Title', 'wp-freeio' ),
                'id'                => $prefix . 'title',
                'type'              => 'text',
                'default'           => '',
                'attributes'        => array(
                    'required'          => 'required'
                ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input'),
            ),
            array(
                'name'              => __( 'Description', 'wp-freeio' ),
                'id'                => $prefix . 'description',
                'type'              => 'wysiwyg',
                'options' => array(
                    'media_buttons' => false,
                    'textarea_rows' => 8,
                    'wpautop' => true,
                    'tinymce'       => array(
                        'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
                        'paste_as_text'                 => true,
                        'paste_auto_cleanup_on_paste'   => true,
                        'paste_remove_spans'            => true,
                        'paste_remove_styles'           => true,
                        'paste_remove_styles_if_webkit' => true,
                        'paste_strip_class_attributes'  => true,
                    ),
                ),
            ),
            array(
                'name'              => __( 'Featured Service', 'wp-freeio' ),
                'id'                => $prefix . 'featured',
                'type'              => 'checkbox',
                'description'       => __( 'Featured employer will be sticky during searches, and can be styled differently.', 'wp-freeio' ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_checkbox'),
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
            ),
            array(
                'name'              => __( 'Expiry Date', 'wp-freeio' ),
                'id'                => $prefix . 'expiry_date',
                'type'              => 'wpfi_datepicker',
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
                'disable_check_register' => true,
                'attributes' => array(
                    'data-datepicker' => json_encode(array(
                        'dateFormat' => $datepicker_date_format,
                        'altField' => '#'.$prefix . 'expiry_date',
                        'altFormat' => 'yy-mm-dd',
                    ))
                )
            ),
        );
        return apply_filters( 'wp-freeio-service-type-required-fields', $fields );
    }

    public static function get_all_types_service_fields_available() {
        
        $prefix = WP_FREEIO_SERVICE_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Featured Image', 'wp-freeio' ),
                'id'                => $prefix . 'featured_image',
                'type'              => 'wp_freeio_file',
                'multiple'          => false,
                'default'           => ! empty( $featured_image ) ? $featured_image : '',
                'ajax'              => true,
                'mime_types' => array( 'gif', 'jpeg', 'jpg', 'png' ),
            ),
            array(
                'name'              => __( 'Price Type', 'wp-freeio' ),
                'id'                => $prefix . 'price_type',
                'type'              => 'select',
                'options'           => WP_Freeio_Mixes::get_default_service_price_types(),
                'default'           => 'price',
            ),
            array(
                'name'              => __( 'Packages', 'wp-freeio' ),
                'id'                => $prefix . 'price_packages',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'Package {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another Package', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove Package', 'wp-freeio' ),
                    'sortable'          => true,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Name', 'wp-freeio' ),
                        'id'        => 'name',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Description', 'wp-freeio' ),
                        'id'        => 'description',
                        'type'      => 'textarea',
                    ),
                    array(
                        'name'      => __( 'Delivery Time', 'wp-freeio' ),
                        'id'        => 'delivery_time',
                        'type'      => 'select',
                        'options'   => array()
                    ),
                    array(
                        'name'      => __( 'Revisions', 'wp-freeio' ),
                        'id'        => 'revisions',
                        'type'      => 'text',
                        'attributes'        => array(
                            'type'              => 'number',
                            'min'               => 0,
                            'pattern'           => '\d*',
                        ),
                    ),
                    array(
                        'name'      => __( 'More Features', 'wp-freeio' ),
                        'id'        => 'features',
                        'type'      => 'textarea',
                        'description' => esc_html__('Add each option in a new line.', 'wp-freeio')
                    ),
                    array(
                        'name'              => __( 'Price', 'wp-freeio' ),
                        'id'                => 'price',
                        'type'              => 'text',
                        'attributes'        => array(
                            'type'              => 'number',
                            'min'               => 0,
                            'pattern'           => '\d*',
                        ),
                    ),
                ),
            ),

            array(
                'name'              => __( 'Service Price', 'wp-freeio' ),
                'id'                => $prefix . 'price',
                'type'              => 'text',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_service_price'),
            ),
            array(
                'name'              => __( 'Gallery', 'wp-freeio' ),
                'id'                => $prefix . 'gallery',
                'type'              => 'file_list',
                'file_multiple'     => true,
                'ajax'              => true,
                'multiple_files'    => true,
                'mime_types'        => array( 'gif', 'jpeg', 'jpg', 'png' ),
                'query_args' => array( 'type' => 'image' ), // Only images attachment
                'text' => array(
                    'add_upload_files_text' => __( 'Add or Upload Images', 'wp-freeio' ),
                ),
            ),
            array(
                'name'              => __( 'Attachments', 'wp-freeio' ),
                'id'                => $prefix . 'attachments',
                'type'              => 'file_list',
                'file_multiple'     => true,
                'ajax'              => true,
                'multiple_files'    => true,
                'mime_types'        => array( 'pdf', 'doc', 'docx' ),
                'description'       => __('Upload file .pdf, .doc, .docx', 'wp-freeio')
            ),
            array(
                'name'              => __( 'Video URL', 'wp-freeio' ),
                'id'                => $prefix . 'video_url',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Friendly Address', 'wp-freeio' ),
                'id'                => $prefix . 'address',
                'type'              => 'text',
            ),
            array(
                'id'                => $prefix . 'map_location',
                'name'              => __( 'Maps Location', 'wp-freeio' ),
                'type'              => 'pw_map',
                'sanitization_cb'   => 'pw_map_sanitise',
                'split_values'      => true,
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_location'),
            ),
            // taxonomies
            array(
                'name'              => __( 'Categories', 'wp-freeio' ),
                'id'                => $prefix . 'category',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'service_category',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Location', 'wp-freeio' ),
                'id'                => $prefix . 'location',
                'type'              => 'wpjb_taxonomy_location',
                'taxonomy'          => 'location',
                'attributes'        => array(
                    'placeholder'   => __( 'Select %s', 'wp-freeio' ),
                ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Tag', 'wp-freeio' ),
                'id'                => $prefix . 'tag',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'service_tag',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),

            array(
                'name'              => __( 'Addons Service', 'wp-freeio' ),
                'id'                => $prefix . 'addons',
                'type'              => 'wpjb_addons',
            ),

            // custom
            array(
                'name'              => __( 'English level', 'wp-freeio' ),
                'id'                => $prefix . 'english_level',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => 'Basic
Conversational
Fluent
Native Or Bilingual
Professional'
            ),
            array(
                'name'              => __( 'Delivery Time', 'wp-freeio' ),
                'id'                => $prefix . 'delivery_time',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => '1 Day
2 Days
3 Days
4 Days
5 Days
6 Days
7 Days'
            ),
            array(
                'name'              => __( 'Response Time', 'wp-freeio' ),
                'id'                => $prefix . 'response_time',
                'type'              => 'select',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
                'options' => '1 Hour
2 Hours
3 Hours
4 Hours
5 Hours
6 Hours
7 Hours'
            ),
            array(
                'name'              => __( 'FAQ', 'wp-freeio' ),
                'id'                => $prefix . 'faq',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'FAQ {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another FAQ', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove FAQ', 'wp-freeio' ),
                    'sortable'          => false,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Question', 'wp-freeio' ),
                        'id'        => 'question',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Answer', 'wp-freeio' ),
                        'id'        => 'answer',
                        'type'      => 'textarea',
                    ),
                ),
            ),
        );
        return apply_filters( 'wp-freeio-service-type-available-fields', $fields );
    }

    public static function get_all_types_project_fields_required() {
        $datepicker_date_format = str_replace(
            array( 'd', 'j', 'l', 'z', /* Day. */ 'F', 'M', 'n', 'm', /* Month. */ 'Y', 'y', /* Year. */ ),
            array( 'dd', 'd', 'DD', 'o', 'MM', 'M', 'm', 'mm', 'yy', 'y', ),
            get_option( 'date_format' )
        );
        $prefix = WP_FREEIO_PROJECT_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Title', 'wp-freeio' ),
                'id'                => $prefix . 'title',
                'type'              => 'text',
                'default'           => '',
                'attributes'        => array(
                    'required'          => 'required'
                ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input'),
            ),
            array(
                'name'              => __( 'Description', 'wp-freeio' ),
                'id'                => $prefix . 'description',
                'type'              => 'wysiwyg',
                'options' => array(
                    'media_buttons' => false,
                    'textarea_rows' => 8,
                    'wpautop' => true,
                    'tinymce'       => array(
                        'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
                        'paste_as_text'                 => true,
                        'paste_auto_cleanup_on_paste'   => true,
                        'paste_remove_spans'            => true,
                        'paste_remove_styles'           => true,
                        'paste_remove_styles_if_webkit' => true,
                        'paste_strip_class_attributes'  => true,
                    ),
                ),
            ),
            array(
                'name'              => __( 'Featured Service', 'wp-freeio' ),
                'id'                => $prefix . 'featured',
                'type'              => 'checkbox',
                'description'       => __( 'Featured employer will be sticky during searches, and can be styled differently.', 'wp-freeio' ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_checkbox'),
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
            ),
            array(
                'name'              => __( 'Expiry Date', 'wp-freeio' ),
                'id'                => $prefix . 'expiry_date',
                'type'              => 'wpfi_datepicker',
                'disable_check' => true,
                'show_in_submit_form' => '',
                'show_in_admin_edit' => 'yes',
                'disable_check_register' => true,
                'attributes' => array(
                    'data-datepicker' => json_encode(array(
                        'dateFormat' => $datepicker_date_format,
                        'altField' => '#'.$prefix . 'expiry_date',
                        'altFormat' => 'yy-mm-dd',
                    ))
                )
            ),
        );
        return apply_filters( 'wp-freeio-project-type-required-fields', $fields );
    }

    public static function get_all_types_project_fields_available() {
        
        $prefix = WP_FREEIO_PROJECT_PREFIX;
        $fields = array(
            array(
                'name'              => __( 'Featured Image', 'wp-freeio' ),
                'id'                => $prefix . 'featured_image',
                'type'              => 'wp_freeio_file',
                'multiple'          => false,
                'default'           => ! empty( $featured_image ) ? $featured_image : '',
                'ajax'              => true,
                'mime_types' => array( 'gif', 'jpeg', 'jpg', 'png' ),
            ),
            array(
                'name'              => __( 'Project location type', 'wp-freeio' ),
                'id'                => $prefix . 'location_type',
                'type'              => 'select',
                'options' => 'Onsite
Partial Onsite
Remote',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
            ),
            array(
                'name'              => __( 'Project Type', 'wp-freeio' ),
                'id'                => $prefix . 'project_type',
                'type'              => 'select',
                'options'           => WP_Freeio_Mixes::get_default_project_types(),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
            ),
            array(
                'name'              => __( 'Minimum Price', 'wp-freeio' ),
                'id'                => $prefix . 'price',
                'type'              => 'text',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_project_price'),
            ),
            array(
                'name'              => __( 'Maximum Price', 'wp-freeio' ),
                'id'                => $prefix . 'max_price',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Estimated Hours', 'wp-freeio' ),
                'id'                => $prefix . 'estimated_hours',
                'type'              => 'text',
            ),
            array(
                'name'              => __( 'Attachments', 'wp-freeio' ),
                'id'                => $prefix . 'attachments',
                'type'              => 'file_list',
                'file_multiple'     => true,
                'ajax'              => true,
                'multiple_files'    => true,
                'mime_types'        => array( 'pdf', 'doc', 'docx' ),
                'description'       => __('Upload file .pdf, .doc, .docx', 'wp-freeio')
            ),
            array(
                'name'              => __( 'Friendly Address', 'wp-freeio' ),
                'id'                => $prefix . 'address',
                'type'              => 'text',
            ),
            array(
                'id'                => $prefix . 'map_location',
                'name'              => __( 'Maps Location', 'wp-freeio' ),
                'type'              => 'pw_map',
                'sanitization_cb'   => 'pw_map_sanitise',
                'split_values'      => true,
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_input_location'),
            ),
            // taxonomies
            array(
                'name'              => __( 'Categories', 'wp-freeio' ),
                'id'                => $prefix . 'category',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'project_category',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Location', 'wp-freeio' ),
                'id'                => $prefix . 'location',
                'type'              => 'wpjb_taxonomy_location',
                'taxonomy'          => 'location',
                'attributes'        => array(
                    'placeholder'   => __( 'Select %s', 'wp-freeio' ),
                ),
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Skill', 'wp-freeio' ),
                'id'                => $prefix . 'project_skill',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'project_skill',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Duration', 'wp-freeio' ),
                'id'                => $prefix . 'project_duration',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'project_duration',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Experience', 'wp-freeio' ),
                'id'                => $prefix . 'project_experience',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'project_experience',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Freelancer Type', 'wp-freeio' ),
                'id'                => $prefix . 'freelancer_type',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'project_freelancer_type',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Language', 'wp-freeio' ),
                'id'                => $prefix . 'project_language',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'project_language',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'Level', 'wp-freeio' ),
                'id'                => $prefix . 'project_level',
                'type'              => 'pw_taxonomy_multiselect',
                'taxonomy'          => 'project_level',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_taxonomy_hierarchical_check_list'),
            ),
            array(
                'name'              => __( 'English Level', 'wp-freeio' ),
                'id'                => $prefix . 'english_level',
                'type'              => 'select',
                'options' => 'Basic
Conversational
Fluent
Native Or Bilingual
Professional',
                'field_call_back' => array( 'WP_Freeio_Abstract_Filter', 'filter_field_select'),
            ),
            array(
                'name'              => __( 'FAQ', 'wp-freeio' ),
                'id'                => $prefix . 'faq',
                'type'              => 'group',
                'options'           => array(
                    'group_title'       => __( 'FAQ {#}', 'wp-freeio' ),
                    'add_button'        => __( 'Add Another FAQ', 'wp-freeio' ),
                    'remove_button'     => __( 'Remove FAQ', 'wp-freeio' ),
                    'sortable'          => false,
                    'closed'         => true,
                ),
                'fields'            => array(
                    array(
                        'name'      => __( 'Question', 'wp-freeio' ),
                        'id'        => 'question',
                        'type'      => 'text',
                    ),
                    array(
                        'name'      => __( 'Answer', 'wp-freeio' ),
                        'id'        => 'answer',
                        'type'      => 'textarea',
                    ),
                ),
            ),
        );
        return apply_filters( 'wp-freeio-project-type-available-fields', $fields );
    }

    public static function get_display_hooks($prefix = WP_FREEIO_JOB_LISTING_PREFIX) {
        $hooks = [];
        switch ($prefix) {
            case WP_FREEIO_JOB_LISTING_PREFIX:
                $hooks = array(
                    '' => esc_html__('Choose a position', 'wp-freeio'),
                    'wp-freeio-single-job-description' => esc_html__('Single Job - Description', 'wp-freeio'),
                    'wp-freeio-single-job-details' => esc_html__('Single Job - Details', 'wp-freeio'),
                );
                break;
            case WP_FREEIO_EMPLOYER_PREFIX:
                $hooks = array(
                    '' => esc_html__('Choose a position', 'wp-freeio'),
                    'wp-freeio-single-employer-description' => esc_html__('Single Employer - Description', 'wp-freeio'),
                    'wp-freeio-single-employer-details' => esc_html__('Single Employer - Details', 'wp-freeio'),
                );
                break;
            case WP_FREEIO_FREELANCER_PREFIX:
                $hooks = array(
                    '' => esc_html__('Choose a position', 'wp-freeio'),
                    'wp-freeio-single-freelancer-description' => esc_html__('Single Freelancer - Description', 'wp-freeio'),
                    'wp-freeio-single-freelancer-details' => esc_html__('Single Freelancer - Details', 'wp-freeio'),
                );
                break;
            case WP_FREEIO_SERVICE_PREFIX:
                $hooks = array(
                    '' => esc_html__('Choose a position', 'wp-freeio'),
                    'wp-freeio-single-service-description' => esc_html__('Single Service - Description', 'wp-freeio'),
                    'wp-freeio-single-service-details' => esc_html__('Single Service - Details', 'wp-freeio'),
                );
                break;
            case WP_FREEIO_PROJECT_PREFIX:
                $hooks = array(
                    '' => esc_html__('Choose a position', 'wp-freeio'),
                    'wp-freeio-single-project-description' => esc_html__('Single Project - Description', 'wp-freeio'),
                    'wp-freeio-single-project-details' => esc_html__('Single Project - Details', 'wp-freeio'),
                );
                break;
        }
        return apply_filters( 'wp-freeio-get-custom-fields-display-hooks', $hooks, $prefix );
    }

    public static function custom_field_html() {
        $fieldtype = $_POST['fieldtype'];
        $prefix = $_POST['prefix'];
        $global_custom_field_counter = $_REQUEST['global_custom_field_counter'];
        $li_rand_id = rand(454, 999999);
        $global_custom_field_counter = $global_custom_field_counter.$li_rand_id;
        
        $html = '<li class="custom-field-class-' . $li_rand_id . '">';
        $types = self::get_all_field_type_keys();
        if ( in_array($fieldtype, $types) ) {
            if ( in_array( $fieldtype, array('text', 'textarea', 'wysiwyg', 'number', 'url', 'email', 'checkbox') ) ) {
                $html .= apply_filters( 'wp_freeio_custom_field_text_html', $fieldtype, $global_custom_field_counter, '', $prefix );
            } elseif ( in_array( $fieldtype, array('select', 'multiselect', 'radio') ) ) {
                $html .= apply_filters( 'wp_freeio_custom_field_opts_html', $fieldtype, $global_custom_field_counter, '', $prefix );
            } else {
                $html .= apply_filters('wp_freeio_custom_field_'.$fieldtype.'_html', $fieldtype, $global_custom_field_counter, '', $prefix);
            }
        }
        // action btns
        $html .= apply_filters('wp_freeio_custom_field_actions_html', $li_rand_id, $global_custom_field_counter, $fieldtype);
        $html .= '</li>';
        echo json_encode( array('html' => $html) );
        wp_die();
    }

    public static function custom_field_available_html() {
        $prefix = $_REQUEST['prefix'];

        $fieldtype = $_POST['fieldtype'];
        $global_custom_field_counter = $_REQUEST['global_custom_field_counter'];
        $li_rand_id = rand(454, 999999);
        $html = '<li class="custom-field-class-' . $li_rand_id . '">';
        if ( $prefix == WP_FREEIO_JOB_LISTING_PREFIX ) {
            $types = self::get_all_types_job_listing_fields_available();
        } elseif ( $prefix == WP_FREEIO_EMPLOYER_PREFIX ) {
            $types = self::get_all_types_employer_fields_available();
        } elseif ( $prefix == WP_FREEIO_FREELANCER_PREFIX ) {
            $types = self::get_all_types_freelancer_fields_available();
        } elseif ( $prefix == WP_FREEIO_SERVICE_PREFIX ) {
            $types = self::get_all_types_service_fields_available();
        } elseif ( $prefix == WP_FREEIO_PROJECT_PREFIX ) {
            $types = self::get_all_types_project_fields_available();
        }

        $dfield_values = self::get_field_id($fieldtype, $types);
        if ( !empty($dfield_values) ) {

            $dtypes = apply_filters( 'wp_freeio_list_simple_type', array( $prefix.'featured', $prefix.'urgent', $prefix.'verified', $prefix.'address', $prefix.'salary', $prefix.'max_salary', $prefix.'min_rate', $prefix.'max_rate', $prefix.'application_deadline_date', $prefix.'apply_url', $prefix.'apply_email', $prefix.'video', $prefix.'profile_url', $prefix.'email', $prefix.'founded_date', $prefix.'website', $prefix.'phone', $prefix.'video_url', $prefix.'socials', $prefix.'team_members', $prefix.'employees', $prefix.'attached_user', $prefix.'show_profile', $prefix.'tagline', WP_FREEIO_FREELANCER_PREFIX.'experience', $prefix.'education', $prefix.'award', $prefix.'skill', $prefix.'tag', $prefix.'company_size', $prefix.'faq', $prefix.'addons', $prefix.'price', $prefix.'max_price', $prefix.'map_location', $prefix.'firstname', $prefix.'lastname', $prefix.'job_title', $prefix.'estimated_hours', $prefix.'price_type', $prefix.'price_packages' ) );

            if ( in_array( $fieldtype, $dtypes ) ) {
                $html .= apply_filters( 'wp_freeio_custom_field_available_simple_html', $fieldtype, $global_custom_field_counter, $dfield_values, $prefix );
            } elseif ( in_array( $fieldtype, array($prefix.'category', $prefix.'type', $prefix.'project_skill', $prefix.'project_duration', $prefix.'project_experience', WP_FREEIO_PROJECT_PREFIX.'freelancer_type', $prefix.'project_language', $prefix.'project_level') ) ) {
                $html .= apply_filters( 'wp_freeio_custom_field_available_tax_html', $fieldtype, $global_custom_field_counter, $dfield_values, $prefix );
            } elseif ( in_array( $fieldtype, array($prefix.'featured_image', $prefix.'logo', $prefix.'gallery', $prefix.'attachments', $prefix.'cover_photo', $prefix.'profile_photos', $prefix.'cv_attachment', $prefix.'photos') ) ) {
                $html .= apply_filters( 'wp_freeio_custom_field_available_file_html', $fieldtype, $global_custom_field_counter, $dfield_values, $prefix );
            } elseif ( in_array($fieldtype, array( $prefix.'experience_time', $prefix.'experience', $prefix.'gender', WP_FREEIO_FREELANCER_PREFIX.'freelancer_type', $prefix . 'english_level', $prefix.'industry', $prefix.'qualification', $prefix.'career_level', $prefix.'age', $prefix.'languages', $prefix . 'response_time', $prefix . 'delivery_time', $prefix . 'location_type') )) {
                $html .= apply_filters( 'wp_freeio_custom_field_available_select_option_html', $fieldtype, $global_custom_field_counter, $dfield_values, $prefix );
            } elseif ( in_array($fieldtype, array( $prefix.'salary_type') )) {
                $html .= apply_filters( 'wp_freeio_custom_field_available_salary_type_html', $fieldtype, $global_custom_field_counter, $dfield_values, $prefix );
            } elseif ( in_array($fieldtype, array( $prefix.'project_type' ) )) {
                $html .= apply_filters( 'wp_freeio_custom_field_available_project_type_html', $fieldtype, $global_custom_field_counter, $dfield_values, $prefix );
            } elseif ( in_array($fieldtype, array( $prefix.'apply_type' ) )) {
                $html .= apply_filters( 'wp_freeio_custom_field_available_apply_type_html', $fieldtype, $global_custom_field_counter, $dfield_values, $prefix );
            } elseif ( in_array($fieldtype, array( $prefix.'location' ) )) {
                $html .= apply_filters( 'wp_freeio_custom_field_available_location_html', $fieldtype, $global_custom_field_counter, $dfield_values, $prefix );
            } else {
                $html .= apply_filters( 'wp_freeio_custom_field_available_'.$fieldtype.'_html', $fieldtype, $global_custom_field_counter, $dfield_values, $prefix );
            }
        }

        // action btns
        $html .= apply_filters('wp_freeio_custom_field_actions_html', $li_rand_id, $global_custom_field_counter, $fieldtype);
        $html .= '</li>';
        echo json_encode(array('html' => $html));
        wp_die();
    }

}

WP_Freeio_Fields_Manager::init();


