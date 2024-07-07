<?php

class Freeio_Widget_User_Short_Profile extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'apus_user_short_profile',
            esc_html__('Apus User Short Profile', 'freeio'),
            array( 'description' => esc_html__( 'Show User Short Profile in sidebar', 'freeio' ), )
        );
        $this->widgetName = 'user_short_profile';
    }
    
    public function widget( $args, $instance ) {
        get_template_part('widgets/user-short-profile', '', array('args' => $args, 'instance' => $instance));
    }
    
    public function form( $instance ) {
        $defaults = array(
            'title' => '',
            'nav_menu_employer' => '',
            'nav_menu_freelancer' => '',
            'nav_menu_employee' => '',
        );
        $instance = wp_parse_args((array) $instance, $defaults);

        $custom_menus = array( '' => esc_html__('Choose a menu', 'freeio') );
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
        if ( is_array( $menus ) && ! empty( $menus ) ) {
            foreach ( $menus as $single_menu ) {
                if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->slug ) ) {
                    $custom_menus[ $single_menu->slug ] = $single_menu->name;
                }
            }
        }
        // Widget admin form
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:', 'freeio' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('nav_menu_employer')); ?>">
                <?php echo esc_html__('Employer Menu:', 'freeio' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('nav_menu_employer')); ?>" name="<?php echo esc_attr($this->get_field_name('nav_menu_employer')); ?>">
                <?php foreach ( $custom_menus as $key => $value ) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['nav_menu_employer'],$key); ?> ><?php echo esc_html( $value ); ?></option>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('nav_menu_freelancer')); ?>">
                <?php echo esc_html__('Freelancer Menu:', 'freeio' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('nav_menu_freelancer')); ?>" name="<?php echo esc_attr($this->get_field_name('nav_menu_freelancer')); ?>">
                <?php foreach ( $custom_menus as $key => $value ) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['nav_menu_freelancer'],$key); ?> ><?php echo esc_html( $value ); ?></option>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('nav_menu_employee')); ?>">
                <?php echo esc_html__('Employee Menu:', 'freeio' ); ?>
            </label>
            <br>
            <select id="<?php echo esc_attr($this->get_field_id('nav_menu_employee')); ?>" name="<?php echo esc_attr($this->get_field_name('nav_menu_employee')); ?>">
                <?php foreach ( $custom_menus as $key => $value ) { ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected($instance['nav_menu_employee'],$key); ?> ><?php echo esc_html( $value ); ?></option>
                <?php } ?>
            </select>
        </p>
<?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';
        $instance['nav_menu_employer'] = ( ! empty( $new_instance['nav_menu_employer'] ) ) ? strip_tags( $new_instance['nav_menu_employer'] ) : '';
        $instance['nav_menu_employee'] = ( ! empty( $new_instance['nav_menu_employee'] ) ) ? strip_tags( $new_instance['nav_menu_employee'] ) : '';
        $instance['nav_menu_freelancer'] = ( ! empty( $new_instance['nav_menu_freelancer'] ) ) ? strip_tags( $new_instance['nav_menu_freelancer'] ) : '';
        return $instance;
    }
}

call_user_func( implode('_', array('register', 'widget') ), 'Freeio_Widget_User_Short_Profile' );
