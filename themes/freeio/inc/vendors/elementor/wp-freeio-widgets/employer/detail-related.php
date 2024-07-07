<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Freeio_Elementor_Widget_Detail_Employer_Related extends Elementor\Widget_Base {

	public function get_name() {
		return 'apus_element_detail_employer_related';
	}

	public function get_title() {
		return esc_html__( 'Employer Details:: Related', 'freeio' );
	}

	public function get_categories() {
		return [ 'freeio-employer-detail-elements' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Settings', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

		$this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__( 'Enter your title here', 'freeio' ),
            ]
        );

		$this->add_control(
            'limit',
            [
                'label' => esc_html__( 'Limit', 'freeio' ),
                'type' => Elementor\Controls_Manager::NUMBER,
                'input_type' => 'number',
                'description' => esc_html__( 'Limit employers to display', 'freeio' ),
                'default' => 4
            ]
        );

		$this->add_control(
            'employer_item_style',
            [
                'label' => esc_html__( 'Employer Item Style', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'grid' => esc_html__('Grid', 'freeio'),
                    'grid-v1' => esc_html__('Grid 1', 'freeio'),
                    'grid-v2' => esc_html__('Grid 2', 'freeio'),
                    'grid-v3' => esc_html__('Grid 3', 'freeio'),
                    'list' => esc_html__('List', 'freeio'),
                ),
                'default' => 'grid'
            ]
        );

		$this->add_control(
            'layout_type',
            [
                'label' => esc_html__( 'Layout', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    'grid' => esc_html__('Grid', 'freeio'),
                    'carousel' => esc_html__('Carousel', 'freeio'),
                ),
                'default' => 'grid'
            ]
        );

        $columns = range( 1, 12 );
        $columns = array_combine( $columns, $columns );

        $this->add_responsive_control(
            'columns',
            [
                'label' => esc_html__( 'Columns', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => $columns,
                'frontend_available' => true,
                'default' => 3,
            ]
        );

        $this->add_responsive_control(
            'slides_to_scroll',
            [
                'label' => esc_html__( 'Slides to Scroll', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'description' => esc_html__( 'Set how many slides are scrolled per swipe.', 'freeio' ),
                'options' => $columns,
                'condition' => [
                    'columns!' => '1',
                    'layout_type' => 'carousel',
                ],
                'frontend_available' => true,
                'default' => 1,
            ]
        );

        $this->add_control(
            'rows',
            [
                'label' => esc_html__( 'Rows', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'input_type' => 'number',
                'placeholder' => esc_html__( 'Enter your rows number here', 'freeio' ),
                'default' => 1,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'show_nav',
            [
                'label'         => esc_html__( 'Show Navigation', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'         => esc_html__( 'Show Pagination', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Show', 'freeio' ),
                'label_off'     => esc_html__( 'Hide', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );


        $this->add_control(
            'slider_autoplay',
            [
                'label'         => esc_html__( 'Autoplay', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'freeio' ),
                'label_off'     => esc_html__( 'No', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'infinite_loop',
            [
                'label'         => esc_html__( 'Infinite Loop', 'freeio' ),
                'type'          => Elementor\Controls_Manager::SWITCHER,
                'label_on'      => esc_html__( 'Yes', 'freeio' ),
                'label_off'     => esc_html__( 'No', 'freeio' ),
                'return_value'  => true,
                'default'       => true,
                'condition' => [
                    'layout_type' => 'carousel',
                ],
            ]
        );

        $this->add_control(
            'position_nav',
            [
                'label' => esc_html__( 'Position Nav', 'freeio' ),
                'type' => Elementor\Controls_Manager::SELECT,
                'options' => array(
                    '' => esc_html__('Default', 'freeio'),
                    'nav-bottom' => esc_html__('Bottom', 'freeio'),
                ),
                'default' => '',
                'condition' => [
                    'layout_type' => 'carousel',
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

		$this->start_controls_section(
            'section_box_style',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_STYLE,
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
			}
		}
		if ( !empty($post) ) {
	        ?>
			<div class="employer-detail-related <?php echo esc_attr($el_class); ?>">
				<?php
				$tax_query = array();
                $terms = get_the_terms( $post->ID, 'employer_category' );
                if ($terms) {
                    $termids = array();
                    foreach($terms as $term) {
                        $termids[] = $term->term_id;
                    }
                    $tax_query[] = array(
                        'taxonomy' => 'employer_category',
                        'field' => 'id',
                        'terms' => $termids,
                        'operator' => 'IN'
                    );
                }
                if ( empty($tax_query) ) {
                    return;
                }
                $args = array(
                    'post_type' => 'employer',
                    'posts_per_page' => $limit,
                    'post__not_in' => array( get_the_ID() ),
                    'tax_query' => $tax_query
                );
                $loop = new WP_Query( $args );
                
				if( $loop->have_posts() ):
				        $columns = !empty($columns) ? $columns : 3;
                        $columns_tablet_extra = !empty($settings['columns_tablet_extra']) ? $settings['columns_tablet_extra'] : $columns;
			            $columns_tablet = !empty($columns_tablet) ? $columns_tablet : 2;
			            $columns_mobile = !empty($columns_mobile) ? $columns_mobile : 1;
			            
			            $slides_to_scroll = !empty($slides_to_scroll) ? $slides_to_scroll : 1;
			            $slides_to_scroll_tablet = !empty($slides_to_scroll_tablet) ? $slides_to_scroll_tablet : $columns_tablet;
			            $slides_to_scroll_mobile = !empty($slides_to_scroll_mobile) ? $slides_to_scroll_mobile : $columns_mobile;
		            ?>
		            <div class="widget-employers <?php echo esc_attr($layout_type.' item-'.$employer_item_style); ?> <?php echo esc_attr($el_class); ?>">
		                
	                    <?php if ( $title ) { ?>
                            <h4 class="title-related-employers"><?php echo esc_html($title); ?></h4>
                        <?php } ?>

		                <div class="widget-content">
		                    <?php if ( $layout_type == 'carousel' ): ?>
		                        <div class="slick-carousel <?php echo esc_attr($columns < $loop->post_count?'':'hidden-dots'); ?> <?php echo esc_attr($position_nav); ?>"
		                            data-items="<?php echo esc_attr($columns); ?>"
			                        data-large="<?php echo esc_attr( $columns_tablet_extra ); ?>"
			                        data-medium="<?php echo esc_attr( $columns_tablet ); ?>"
			                        data-small="<?php echo esc_attr($columns_mobile); ?>"
			                        data-smallest="<?php echo esc_attr($columns_mobile); ?>"

			                        data-slidestoscroll="<?php echo esc_attr($slides_to_scroll); ?>"
			                        data-slidestoscroll_large="<?php echo esc_attr( $slides_to_scroll_tablet ); ?>"
			                        data-slidestoscroll_medium="<?php echo esc_attr( $slides_to_scroll_tablet ); ?>"
			                        data-slidestoscroll_small="<?php echo esc_attr($slides_to_scroll_mobile); ?>"
			                        data-slidestoscroll_smallest="<?php echo esc_attr($slides_to_scroll_mobile); ?>"

		                            data-pagination="<?php echo esc_attr( $show_pagination ? 'true' : 'false' ); ?>" data-nav="<?php echo esc_attr( $show_nav ? 'true' : 'false' ); ?>" data-rows="<?php echo esc_attr( $rows ); ?>" data-infinite="<?php echo esc_attr( $infinite_loop ? 'true' : 'false' ); ?>" data-autoplay="<?php echo esc_attr( $slider_autoplay ? 'true' : 'false' ); ?>">
		                            <?php while ( $loop->have_posts() ): $loop->the_post(); ?>
		                                <div class="item">
		                                    <?php echo WP_Freeio_Template_Loader::get_template_part( 'employers-styles/inner-'. $employer_item_style ); ?>
		                                </div>
		                            <?php endwhile; ?>
		                        </div>
		                    <?php else: ?>
		                        <?php
		                            $mdcol = 12/$columns;
                                    $columns_tablet_extra = 12/$columns_tablet_extra;
		                            $smcol = 12/$columns_tablet;
		                            $xscol = 12/$columns_mobile;
		                        ?>
		                        <div class="row">
		                            <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
		                                <div class="col-xl-<?php echo esc_attr($mdcol); ?> col-tablet-extra-<?php echo esc_attr($columns_tablet_extra); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr( $xscol ); ?> list-item">
		                                    <?php echo WP_Freeio_Template_Loader::get_template_part( 'employers-styles/inner-'. $employer_item_style ); ?>
		                                </div>
		                            <?php endwhile; ?>
		                        </div>
		                    <?php endif; ?>
		                    <?php wp_reset_postdata(); ?>
		                </div>
		            </div>
				<?php endif; ?>
			</div>
			<?php
	    }
	}

}

Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Widget_Detail_Employer_Related );
