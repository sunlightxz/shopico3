<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Employer_Gallery extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_employer_gallery';
	}

	public function get_title() {
		return esc_html__( 'Employer Details:: Gallery', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-employer-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Settings', 'freeio' ),
			]
		);

		$this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $this->add_group_control(
            Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
                'default' => 'full',
                'separator' => 'none',
                'condition' => [
                    'layout_type' => 'v1',
                ],
            ]
        );

		$this->add_control(
            'el_class',
            [
                'label'         => esc_html__( 'Extra class name', 'freeio' ),
                'type'          => Elementor\Controls_Manager::TEXT,
                'placeholder'   => esc_html__( 'If you wish to style particular content element differently, please add a class name to this field and refer to it in your custom CSS file.', 'freeio' ),
            ]
        );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        extract( $settings );
        if ( freeio_is_employer_single_page() ) {
        	global $post;
			$post_id = get_the_ID();
		} else {
			$args = array(
				'limit' => 1,
				'fields' => 'ids',
			);
			$employers = freeio_get_employers($args);
			if ( !empty($employers->posts) ) {
				$post_id = $employers->posts[0];
				$post = get_post($post_id);
				setup_postdata( $GLOBALS['post'] =& $post );
			}
		}

		if ( !empty($post) ) {
			?>
			<div class="detail-gallery <?php echo esc_attr($el_class); ?>">
				<?php
					$args = array('elementor' => true);
					if ( $image_size == 'custom' ) {
		                
		                if ( $image_custom_dimension['width'] && $image_custom_dimension['height'] ) {
		                    $imagesize = $image_custom_dimension['width'].'x'.$image_custom_dimension['height'];
		                } else {
		                    $imagesize = 'full';
		                }
		            } else {
		                $imagesize = $image_size;
		            }
		            $args['gallery_size'] = $imagesize;


		            if ( $thumbnail_size == 'custom' ) {
		                
		                if ( $thumbnail_custom_dimension['width'] && $thumbnail_custom_dimension['height'] ) {
		                    $thumbnailsize = $thumbnail_custom_dimension['width'].'x'.$thumbnail_custom_dimension['height'];
		                } else {
		                    $thumbnailsize = 'full';
		                }
		            } else {
		                $thumbnailsize = $thumbnail_size;
		            }
		            $args['gallery_second_size'] = $thumbnailsize;
		            echo WP_Freeio_Template_Loader::get_template_part( 'single-employer/gallery', $args );
				?>
			</div>
			<?php
			if ( !freeio_is_employer_single_page() ) {
				wp_reset_postdata();
			}
	    }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Employer_Gallery );
