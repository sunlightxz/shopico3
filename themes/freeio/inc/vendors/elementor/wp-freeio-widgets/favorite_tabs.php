<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_Freeio_Favorite_Tabs extends Elementor\Widget_Base {

	public function get_name() {
        return 'apus_element_freeio_favorite_tabs';
    }

	public function get_title() {
        return esc_html__( 'Apus Favorite Tabs', 'freeio' );
    }
    
	public function get_categories() {
        return [ 'freeio-elements' ];
    }

    public function get_tab_options() {
        $options = [
            'service' => esc_html__('Service', 'freeio'),
            'project' => esc_html__('Project', 'freeio'),
            'job' => esc_html__('Job', 'freeio'),
            'employer' => esc_html__('Employer', 'freeio'),
            'freelancer' => esc_html__('Freelancer', 'freeio'),
        ];
        return $options;
    }

	protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Favorite', 'freeio' ),
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

        $options = $this->get_tab_options();

        foreach ($options as $key => $label) {
            $this->add_control(
                'show_'.$key,
                [
                    'label'         => sprintf(esc_html__( 'Show %s Favorite Tab', 'freeio' ), $label),
                    'type'          => Elementor\Controls_Manager::SWITCHER,
                    'label_on'      => esc_html__( 'Show', 'freeio' ),
                    'label_off'     => esc_html__( 'Hide', 'freeio' ),
                    'return_value'  => true,
                    'default'       => true,
                ]
            );
        }

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
        $_id = freeio_random_key();

        $options = $this->get_tab_options();
        ?>
        <div class="widget-favorite-tabs box-dashboard-wrapper <?php echo esc_attr($el_class); ?>">
            <?php if ( $title ) { ?>
                <h2 class="widget-title"><?php echo trim($title); ?></h2>
            <?php } ?>
            <div class="inner-list">
                <ul role="tablist" class="nav nav-tabs categories-blog-list">
                    <?php $tab_count = 0; foreach ($options as $key => $title):
                        if ( $settings['show_'.$key] ) {
                            ?>
                            <li>
                                <a href="#tab-<?php echo esc_attr($key);?>-<?php echo esc_attr($_id);?>-<?php echo esc_attr($tab_count); ?>" data-bs-toggle="tab" class="<?php echo esc_attr($tab_count == 0 ? 'active' : '');?>">
                                    <?php echo trim($title); ?>
                                </a>
                            </li>
                            <?php
                        }
                    $tab_count++; endforeach; ?>
                </ul>
                <div class="tab-content">
                    <?php $tab_count = 0; foreach ($options as $key => $title):
                        if ( $settings['show_'.$key] ) {
                    ?>
                        <div id="tab-<?php echo esc_attr($key);?>-<?php echo esc_attr($_id);?>-<?php echo esc_attr($tab_count); ?>" class="tab-pane <?php echo esc_attr($tab_count == 0 ? 'active' : ''); ?>">
                            <?php
                            $args = array(
                                'limit' => -1,
                            );
                            if ( $key == 'service' ) {
                                $service_ids = WP_Freeio_Favorite::get_service_favorites();
                                $service_ids = !empty($service_ids) ? $service_ids : array(0);
                                $args['post__in'] = $service_ids;
                                $loop = freeio_get_services($args);
                            
                            
                                if ( $loop->have_posts() ) {
                                    $columns = 4;
                                    $columns_tablet = 2;
                                    $columns_mobile = 1;
                                    $mdcol = 12/$columns;
                                    $smcol = 12/$columns_tablet;
                                    $xscol = 12/$columns_mobile;
                                    ?>
                                    <div class="row">
                                        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                            <div class="col-xl-<?php echo esc_attr($mdcol); ?> col-lg-<?php echo esc_attr($smcol); ?> col-md-<?php echo esc_attr($smcol); ?> col-<?php echo esc_attr( $xscol ); ?> list-item">
                                                <?php get_template_part( 'template-jobs/services-styles/inner', 'favorite' ); ?>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <?php wp_reset_postdata();
                                } else {
                                    ?>
                                    <div class="not-found"><?php esc_html_e('No service favorite found', 'freeio'); ?></div>
                                    <?php
                                }
                            } elseif ( $key == 'project' ) {
                                $project_ids = WP_Freeio_Favorite::get_project_favorites();
                                $project_ids = !empty($project_ids) ? $project_ids : array(0);
                                $args['post__in'] = $project_ids;
                                $loop = freeio_get_projects($args);
                            
                            
                                if ( $loop->have_posts() ) {
                                    $columns = 2;
                                    $columns_tablet = 1;
                                    $columns_mobile = 1;
                                    $mdcol = 12/$columns;
                                    $smcol = 12/$columns_tablet;
                                    $xscol = 12/$columns_mobile;
                                    ?>
                                    <div class="row">
                                        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                            <div class="col-xl-<?php echo esc_attr($mdcol); ?> col-sm-<?php echo esc_attr($smcol); ?> col-xs-<?php echo esc_attr( $xscol ); ?> list-item">
                                                <?php get_template_part( 'template-jobs/projects-styles/inner', 'favorite' ); ?>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <?php wp_reset_postdata();
                                } else {
                                    ?>
                                    <div class="not-found"><?php esc_html_e('No project favorite found', 'freeio'); ?></div>
                                    <?php
                                }
                            } elseif ( $key == 'job' ) {
                                $job_ids = WP_Freeio_Favorite::get_job_favorites();
                                $job_ids = !empty($job_ids) ? $job_ids : array(0);
                                $args['post__in'] = $job_ids;
                                $loop = freeio_get_jobs($args);
                                
                                if ( $loop->have_posts() ) {
                                    ?>
                                    <div class="row">
                                        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                            <div class="col-xl-6 col-12 list-item">
                                                <?php get_template_part( 'template-jobs/jobs-styles/inner', 'favorite' ); ?>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <?php wp_reset_postdata();
                                } else {
                                    ?>
                                    <div class="not-found"><?php esc_html_e('No job favorite found', 'freeio'); ?></div>
                                    <?php
                                }
                            } elseif ( $key == 'employer' ) {
                                $employer_ids = WP_Freeio_Favorite::get_employer_favorites();
                                $employer_ids = !empty($employer_ids) ? $employer_ids : array(0);
                                $args['post__in'] = $employer_ids;
                                $loop = freeio_get_employers($args);
                                
                                if ( $loop->have_posts() ) {
                                    ?>
                                    <div class="row">
                                        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                            <div class="col-xl-3 col-md-6 col-12 list-item">
                                                <?php get_template_part( 'template-jobs/employers-styles/inner', 'favorite' ); ?>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <?php wp_reset_postdata();
                                } else {
                                    ?>
                                    <div class="not-found"><?php esc_html_e('No employer favorite found', 'freeio'); ?></div>
                                    <?php
                                }
                            } elseif ( $key == 'freelancer' ) {
                                $freelancer_ids = WP_Freeio_Favorite::get_freelancer_favorites();
                                $freelancer_ids = !empty($freelancer_ids) ? $freelancer_ids : array(0);
                                $args['post__in'] = $freelancer_ids;
                                $loop = freeio_get_freelancers($args);
                                
                                if ( $loop->have_posts() ) {
                                    ?>
                                    <div class="row">
                                        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                                            <div class="col-12 list-item">
                                                <?php get_template_part( 'template-jobs/freelancers-styles/inner', 'favorite' ); ?>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <?php wp_reset_postdata();
                                } else {
                                    ?>
                                    <div class="not-found"><?php esc_html_e('No freelancer favorite found', 'freeio'); ?></div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php $tab_count++; } ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }
}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_Freeio_Favorite_Tabs );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_Freeio_Favorite_Tabs );
}