<?php
/**
 * Abstract Form
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Freeio_Abstract_Register_Form {
    public $form_name = '';
    public $post_type = '';
    public $prefix = '';
    public $errors = array();
    public $success_msg = array();
    private $selected_driver_type;

    // Global variable to store selected driver type

    public function __construct($myvalue) {
        // Initialize selected driver type based on POST data
      //  $this->selected_driver_type = $myvalue;
     //   error_log('Constructor: $myvalue = ' . $this->selected_driver_type);

        add_filter( 'cmb2_meta_boxes', array( $this, 'fields_front' ) );
    }

    public function get_form_action() {
        return '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function get_form_name() {
        return $this->form_name;
    }

    public function add_error( $error ) {
        $this->errors[] = $error;
    }

	public function fields_front($metaboxes) {

		$fields = apply_filters( 'wp-freeio-register-'.$this->post_type.'-fields', array(
			array(
				'id'          => 'post_type',
				'type'        => 'hidden',
				'default'     => $this->post_type,
				'priority'    => 0,
			),
	
			array(
				'name'       => __( 'Email', 'wp-freeio' ),
				'id'         => $this->prefix . 'email',
				'type'       => 'text',
				'priority'   => 0,
				'label_cb'   => array( 'WP_Freeio_Mixes', 'required_add_label' ),
				'attributes' => array(
					'placeholder' => esc_html__('Email', 'wp-freeio'),
					'required'    => true
				)
			),
            array(
                'name'              => __( 'Password', 'wp-freeio' ),
                'id'                => $this->prefix . 'password',
                'type'              => 'hide_show_password',
                'priority'          => 0,
                'attributes' => array(
                    'placeholder' => esc_html__('Password', 'wp-freeio'),
                    'required' => true
                )
            ),
            array(
                'name'              => __( 'Confirm Password', 'wp-freeio' ),
                'id'                => $this->prefix . 'confirmpassword',
                'type'              => 'hide_show_password',
                'priority'          => 0,
                'attributes' => array(
                    'placeholder' => esc_html__('Confirm Password', 'wp-freeio'),
                    'required' => true
                )
            ),
            // Add Phone Number field
            array(
                'name'              => __( 'WhatsApp Phone Number', 'wp-freeio' ),
                'id'                => $this->prefix . 'phone',
                'type'              => 'text',
                'priority'          => 0,
                'attributes' => array(
                    'placeholder' => esc_html__('Phone Number', 'wp-freeio'),
                    'required' => true
                )
            ),
            // Add Address field
            array(
                'name'              => __( 'Address', 'wp-freeio' ),
                'id'                => $this->prefix . 'address',
                'type'              => 'text',
                'priority'          => 0,
                'attributes' => array(
                    'placeholder' => esc_html__('Address', 'wp-freeio'),
                    'required' => true
                )
            ),
        ));

        $metaboxes[ $this->prefix . 'register_fields' ] = array(
            'id'                        => $this->prefix . 'register_fields',
            'title'                     => __( 'General Options', 'wp-freeio' ),
            'object_types'              => array( $this->post_type ),
            'context'                   => 'normal',
            'priority'                  => 'high',
            'show_names'                => true,
            'fields'                    => $fields
        );

        return $metaboxes;
    }

    public function form_output() {
        $metaboxes = apply_filters( 'cmb2_meta_boxes', array() );
        if ( ! isset( $metaboxes[ $this->prefix . 'register_fields' ] ) ) {
            return __( 'A metabox with the specified \'metabox_id\' doesn\'t exist.', 'wp-freeio' );
        }
        $metaboxes_form = $metaboxes[ $this->prefix . 'register_fields' ];

        wp_enqueue_script('wpfi-select2');
        wp_enqueue_style('wpfi-select2');

        return WP_Freeio_Template_Loader::get_template_part( 'misc/register-'.$this->post_type, array(
            'post_id' => WP_Freeio_Mixes::random_key(),
            'metaboxes_form' => $metaboxes_form,
            'form_obj'       => $this,
            'submit_button_text' => apply_filters( 'wp_freeio_register_'.$this->post_type.'_form_submit_button_text', __( 'Register now', 'wp-freeio' ) ),
            'selected_driver_type' => $this->selected_driver_type, // Pass selected driver type to the template
        ) );
    }
}
?>


<script>
	console.log($myvalue)
</script>