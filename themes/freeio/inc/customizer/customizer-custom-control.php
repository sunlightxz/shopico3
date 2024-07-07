<?php

if ( class_exists( 'WP_Customize_Control' ) ) {
    class Freeio_WP_Customize_Radio_Image_Control extends WP_Customize_Control {
        public $type = 'freeio_radio_image';
        
        /**
         * Enqueue our scripts and styles
         */
        public function enqueue() {
            wp_enqueue_style( 'freeio-customizer', get_template_directory_uri() . '/inc/customizer/css/customizer.css', array(), '1.1', 'all' );
        }

        public function render_content() {

            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            </label>
            <?php if ( !empty($this->description) ) { ?>
                <span class="description customize-control-description"><?php echo trim($this->description); ?></span>
            <?php } ?>
            <?php if ( !empty($this->choices) ) { ?>
                <div class="options image_radio_button_control">
                    <?php foreach ($this->choices as $key => $option) { ?>
                        <div class="item radio-button-label">
                            <label>
                                <input type="radio" value="<?php echo esc_attr($key); ?>" <?php $this->link(); ?> <?php checked($this->value(), $key, true); ?> name="_customize-<?php echo esc_attr($this->type); ?>-<?php echo esc_attr($this->id); ?>">
                                <?php if ( $option['img'] ) { ?>
                                    <img src="<?php echo esc_url($option['img']); ?>" alt="<?php echo esc_attr($option['title']); ?>">
                                <?php } ?>
                                <span class="title"><?php echo esc_html($option['title']); ?></span>
                            </label>
                        </div>
                    <?php } ?>
                </div>

                <?php
            }
        }


    }


    class Freeio_WP_Customize_Heading_Control extends WP_Customize_Control {
        public $type = 'freeio_heading';
 
        public function render_content() {

            ?>
            <h4 class="customize-heading-custom">
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            </h4>
            <?php if ( !empty($this->description) ) { ?>
                <span class="description customize-control-description"><?php echo trim($this->description); ?></span>
            <?php } ?>
            <hr>
            <?php
        }
    }
}