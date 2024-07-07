<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Banners extends Widget_Base {

	public function get_name() {
        return 'apus_element_banners';
    }

	public function get_title() {
        return esc_html__( 'Apus Banners', 'freeio' );
    }

	public function get_categories() {
        return [ 'freeio-elements' ];
    }

	protected function register_controls() {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'list_title', [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Banner Title' , 'freeio' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'img_src',
            [
                'name' => 'image',
                'label' => esc_html__( 'Banner Image', 'freeio' ),
                'type' => Controls_Manager::MEDIA,
                'placeholder'   => esc_html__( 'Upload Banner Image', 'freeio' ),
            ]
        );

        $repeater->add_control(
            'link',
            [
                'label' => esc_html__( 'Link', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => esc_html__( 'Enter your banner link here', 'freeio' ),
            ]
        );

        $this->add_control(
            'banners',
            [
                'label' => esc_html__( 'Banners', 'freeio' ),
                'type' => Controls_Manager::REPEATER,
                'placeholder' => esc_html__( 'Enter your banners here', 'freeio' ),
                'fields' => $repeater->get_controls(),
            ]
        );
        
        $this->add_control(
            'layout',
            [
                'label' => esc_html__( 'Layout', 'freeio' ),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'grid' => esc_html__('Grid', 'freeio'),
                    'carousel' => esc_html__('Carousel', 'freeio'),
                ),
                'default' => 'carousel'
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'freeio' ),
                'type' => Controls_Manager::TEXT,
                'input_type' => 'number',
                'placeholder' => esc_html__( 'Enter your column number here', 'freeio' ),
                'default' => 4
            ]
        );

   		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'freeio' ),
                'type'          => Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'freeio' ),
            ]
        );

        $this->end_controls_section();

    }

	protected function render() {

        $settings = $this->get_settings();

        extract( $settings );

        if ( !empty($banners) ) {
            if ( empty($columns) ) {
                $columns = 4;
            }
            $bcol = 12/$columns;

            ?>
            <div class="widget-banners <?php echo esc_attr($el_class); ?>">
                <?php if ( $layout == 'grid' ) { ?>
                    <div class="row">
                        <?php foreach ($banners as $banner) { ?>
                            <?php
                                $img_src = ( isset( $banner['img_src']['id'] ) && $banner['img_src']['id'] != 0 ) ? wp_get_attachment_url( $banner['img_src']['id'] ) : '';
                                if ( $img_src ) {
                            ?>
                                    <div class="col-xs-6 col-md-<?php echo esc_attr($bcol); ?>">
                                        <div class="banner-item">
                                            <?php if ( !empty($banner['link']) ) { ?>
                                                <a class="banner-item-link" href="<?php echo esc_url($banner['link']); ?>" <?php echo (!empty($banner['title']) ? 'title="'.esc_attr($banner['title']).'"' : ''); ?>>
                                            <?php } ?>
                                                <?php if( !empty($banner['list_title']) ) { ?>
                                                    <h4 class="title"><?php echo trim($banner['list_title']); ?> </h4>
                                                <?php } ?>
                                                <img src="<?php echo esc_url($img_src); ?>" <?php echo (!empty($banner['list_title']) ? 'alt="'.esc_attr($banner['title']).'"' : 'alt="'.esc_attr__('Image', 'freeio').'"'); ?>>
                                            <?php if ( !empty($banner['link']) ) { ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div class="slick-carousel <?php echo esc_attr( ($columns >= count($banners)) ? 'hidden-dots':'' ); ?>" data-items="<?php echo esc_attr($columns); ?>" data-medium="3" data-small="2" data-pagination="true" data-nav="false">
                        <?php foreach ($banners as $banner) { ?>
                            <?php
                                $img_src = ( isset( $banner['img_src']['id'] ) && $banner['img_src']['id'] != 0 ) ? wp_get_attachment_url( $banner['img_src']['id'] ) : '';
                                if ( $img_src ) {
                            ?>  
                                <div class="item">
                                    <div class="banner-item">
                                        <?php if ( !empty($banner['link']) ) { ?>
                                            <a class="banner-item-link" href="<?php echo esc_url($banner['link']); ?>" <?php echo (!empty($banner['title']) ? 'title="'.esc_attr($banner['title']).'"' : ''); ?>>
                                        <?php } ?>
                                            <?php if( !empty($banner['list_title']) ) { ?>
                                                <h4 class="title"><?php echo trim($banner['list_title']); ?> </h4>
                                            <?php } ?>
                                            <img src="<?php echo esc_url($img_src); ?>" <?php echo (!empty($banner['list_title']) ? 'alt="'.esc_attr($banner['title']).'"' : 'alt="'.esc_attr__('Image', 'freeio').'"'); ?>>

                                        <?php if ( !empty($banner['link']) ) { ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Banners );
} else {
    Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Banners );
}