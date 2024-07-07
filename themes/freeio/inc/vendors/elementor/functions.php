<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( ! class_exists( 'Freeio_Elementor_Extensions' ) ) {
    final class Freeio_Elementor_Extensions {

        private static $_instance = null;

        
        public function __construct() {
            add_action( 'elementor/elements/categories_registered', array( $this, 'add_widget_categories' ) );
            add_action( 'init', array( $this, 'elementor_widgets' ),  100 );
            add_filter( 'freeio_generate_post_builder', array( $this, 'render_post_builder' ), 10, 2 );

            add_action( 'elementor/controls/controls_registered', array( $this, 'modify_controls' ), 10, 1 );
            add_action('elementor/editor/before_enqueue_styles', array( $this, 'style' ) );

            add_filter( 'elementor/icons_manager/additional_tabs', array( $this, 'custom_icons' ) );

            add_filter( 'elementor/controls/animations/additional_animations', array( $this, 'additional_animations' ), 10 );
        }

        public static function instance () {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        public function add_widget_categories( $elements_manager ) {
            $elements_manager->add_category(
                'freeio-elements',
                [
                    'title' => esc_html__( 'Freeio Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-header-elements',
                [
                    'title' => esc_html__( 'Freeio Header Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-service-detail-elements',
                [
                    'title' => esc_html__( 'Service Detail Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-service-archive-elements',
                [
                    'title' => esc_html__( 'Service Archive Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-project-detail-elements',
                [
                    'title' => esc_html__( 'Project Detail Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-project-archive-elements',
                [
                    'title' => esc_html__( 'Project Archive Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-job-detail-elements',
                [
                    'title' => esc_html__( 'Job Detail Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-job-archive-elements',
                [
                    'title' => esc_html__( 'Job Archive Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-employer-detail-elements',
                [
                    'title' => esc_html__( 'Employer Detail Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-employer-archive-elements',
                [
                    'title' => esc_html__( 'Employer Archive Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-freelancer-detail-elements',
                [
                    'title' => esc_html__( 'Freelancer Detail Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-freelancer-archive-elements',
                [
                    'title' => esc_html__( 'Freelancer Archive Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );

            $elements_manager->add_category(
                'freeio-dashboard-elements',
                [
                    'title' => esc_html__( 'Dashboard Elements', 'freeio' ),
                    'icon' => 'fa fa-shopping-bag',
                ]
            );
        }

        public function elementor_widgets() {
            // general elements
            get_template_part( 'inc/vendors/elementor/widgets/heading' );
            get_template_part( 'inc/vendors/elementor/widgets/posts' );
            get_template_part( 'inc/vendors/elementor/widgets/call_to_action' );
            get_template_part( 'inc/vendors/elementor/widgets/features_box' );
            get_template_part( 'inc/vendors/elementor/widgets/address_box' );
            get_template_part( 'inc/vendors/elementor/widgets/social_links' );
            get_template_part( 'inc/vendors/elementor/widgets/testimonials' );
            get_template_part( 'inc/vendors/elementor/widgets/brands' );
            get_template_part( 'inc/vendors/elementor/widgets/popup_video' );
            get_template_part( 'inc/vendors/elementor/widgets/banner' );
            get_template_part( 'inc/vendors/elementor/widgets/banners' );
            get_template_part( 'inc/vendors/elementor/widgets/banner_account' );
            get_template_part( 'inc/vendors/elementor/widgets/countdown' );
            get_template_part( 'inc/vendors/elementor/widgets/nav_menu' );
            get_template_part( 'inc/vendors/elementor/widgets/filter_button_template' );
            get_template_part( 'inc/vendors/elementor/widgets/team' );
            get_template_part( 'inc/vendors/elementor/widgets/achievements' );
            get_template_part( 'inc/vendors/elementor/widgets/list_icon' );

            get_template_part( 'inc/vendors/elementor/widgets/breadcrumbs' );
            get_template_part( 'inc/vendors/elementor/widgets/page-title' );
            get_template_part( 'inc/vendors/elementor/widgets/elementor-template' );

            // header elements
            get_template_part( 'inc/vendors/elementor/header-widgets/logo' );
            get_template_part( 'inc/vendors/elementor/header-widgets/primary_menu' );
            get_template_part( 'inc/vendors/elementor/header-widgets/nav_bar' );
            get_template_part( 'inc/vendors/elementor/header-widgets/vertical_menu' );
            get_template_part( 'inc/vendors/elementor/header-widgets/header-notice' );
            

            if ( freeio_is_mailchimp_activated() ) {
                get_template_part( 'inc/vendors/elementor/widgets/mailchimp' );
            }
            
            if ( freeio_is_revslider_activated() ) {
                get_template_part( 'inc/vendors/elementor/widgets/revslider' );
            }

            if ( freeio_is_freeio_activated() ) {
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/services' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/services_tabs' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/services_categories' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/services_locations' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/projects' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/projects_tabs' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/projects_categories' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/jobs' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/jobs_tabs' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employers' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancers' );

                // search form
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/search_form' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service_search_form' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project_search_form' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job_search_form' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job_alert_form' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer_search_form' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer_search_form' );
                
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/user_info' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/user_short_profile' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/register_form_tabs' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/submit-btn' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/currencies' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/header-notification' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/favorite_tabs' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/transactions' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/contact-form' );

                // service
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/archive-listing-items' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/archive-maps' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/archive-pagination' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/archive-results-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/archive-sortby' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/archive-results-filter' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-description' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-overview' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-price' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-related' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-reviews' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-single-field' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-title' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-faq' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-favorite-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-report-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-share-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-author' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-header-bottom' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-gallery' );

                // project
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/archive-listing-items' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/archive-maps' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/archive-pagination' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/archive-results-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/archive-sortby' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/archive-results-filter' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-attachments' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-author' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-description' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-overview' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-faq' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-favorite-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-header-bottom' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-price' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-proposals' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-related' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-report-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-share-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-single-field' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-skill' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-title' );
                
                
                // job
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/archive-listing-items' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/archive-maps' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/archive-pagination' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/archive-results-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/archive-sortby' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/archive-results-filter' );
                
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-apply-btn' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-description' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-employer-title' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-favorite-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-gallery' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-logo' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-overview' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-related' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-report-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-salary' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-share-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-single-field' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-title' );
                
                
                // freelancer
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/archive-listing-items' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/archive-maps' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/archive-pagination' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/archive-results-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/archive-sortby' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/archive-results-filter' );

                
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-award' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-description' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-detail' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-education' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-experience' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-favorite-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-gallery' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-header-bottom' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-logo' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-overview' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-related' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-report-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-reviews' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-salary' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-services' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-share-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-single-field' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-skill' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-tags' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-title' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-video' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-download-cv-button' );
                
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/dashboard-chart' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/dashboard-notification' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/dashboard-recent-service-orders' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/dashboard-review-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/dashboard-service-completed-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/dashboard-service-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/dashboard-service-inqueue-count' );

                // employer
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/archive-listing-items' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/archive-maps' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/archive-pagination' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/archive-results-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/archive-sortby' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/archive-results-filter' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-description' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-favorite-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-gallery' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-header-bottom' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-jobs' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-logo' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-overview' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-projects' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-related' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-report-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-reviews' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-share-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-single-field' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-tagline' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-title' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-video' );

                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/dashboard-project-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/dashboard-proposal-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/dashboard-review-count' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/dashboard-chart' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/dashboard-recent-proposals' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/dashboard-notification' );
            }

            if ( freeio_is_freeio_wc_paid_listings_activated() && freeio_is_woocommerce_activated() ) {
                get_template_part( 'inc/vendors/elementor/wc-paid-listings-widgets/packages' );
                get_template_part( 'inc/vendors/elementor/wc-paid-listings-widgets/user_packages' );
            }

            if ( freeio_is_wp_private_message() ) {
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/employer/detail-private-message-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/freelancer/detail-private-message-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/service/detail-private-message-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/project/detail-private-message-button' );
                get_template_part( 'inc/vendors/elementor/wp-freeio-widgets/job/detail-private-message-button' );
            }
        }

        public function style() {
            wp_enqueue_style('freeio-flaticon',  get_template_directory_uri() . '/css/flaticon.css');
            wp_enqueue_style('themify-icons',  get_template_directory_uri() . '/css/themify-icons.css');
        }

        public function modify_controls( $controls_registry ) {
            // Get existing icons
            $icons = $controls_registry->get_control( 'icon' )->get_settings( 'options' );
            
            $new_icons = array_merge(array(
                'ti-volume' => 'ti-volume', 'ti-user' => 'ti-user', 'ti-unlock' => 'ti-unlock', 'ti-unlink' => 'ti-unlink', 'ti-trash' => 'ti-trash', 'ti-thought' => 'ti-thought', 'ti-target' => 'ti-target', 'ti-tag' => 'ti-tag', 'ti-tablet' => 'ti-tablet', 'ti-star' => 'ti-star', 'ti-spray' => 'ti-spray', 'ti-signal' => 'ti-signal', 'ti-shopping-cart' => 'ti-shopping-cart', 'ti-shopping-cart-full' => 'ti-shopping-cart-full', 'ti-settings' => 'ti-settings', 'ti-search' => 'ti-search', 'ti-zoom-in' => 'ti-zoom-in', 'ti-zoom-out' => 'ti-zoom-out', 'ti-cut' => 'ti-cut', 'ti-ruler' => 'ti-ruler', 'ti-ruler-pencil' => 'ti-ruler-pencil', 'ti-ruler-alt' => 'ti-ruler-alt', 'ti-bookmark' => 'ti-bookmark', 'ti-bookmark-alt' => 'ti-bookmark-alt', 'ti-reload' => 'ti-reload', 'ti-plus' => 'ti-plus', 'ti-pin' => 'ti-pin', 'ti-pencil' => 'ti-pencil', 'ti-pencil-alt' => 'ti-pencil-alt', 'ti-paint-roller' => 'ti-paint-roller', 'ti-paint-bucket' => 'ti-paint-bucket', 'ti-na' => 'ti-na', 'ti-mobile' => 'ti-mobile', 'ti-minus' => 'ti-minus', 'ti-medall' => 'ti-medall', 'ti-medall-alt' => 'ti-medall-alt', 'ti-marker' => 'ti-marker', 'ti-marker-alt' => 'ti-marker-alt', 'ti-arrow-up' => 'ti-arrow-up', 'ti-arrow-right' => 'ti-arrow-right', 'ti-arrow-left' => 'ti-arrow-left', 'ti-arrow-down' => 'ti-arrow-down', 'ti-lock' => 'ti-lock', 'ti-location-arrow' => 'ti-location-arrow', 'ti-link' => 'ti-link', 'ti-layout' => 'ti-layout', 'ti-layers' => 'ti-layers', 'ti-layers-alt' => 'ti-layers-alt', 'ti-key' => 'ti-key', 'ti-import' => 'ti-import', 'ti-image' => 'ti-image', 'ti-heart' => 'ti-heart', 'ti-heart-broken' => 'ti-heart-broken', 'ti-hand-stop' => 'ti-hand-stop', 'ti-hand-open' => 'ti-hand-open', 'ti-hand-drag' => 'ti-hand-drag', 'ti-folder' => 'ti-folder', 'ti-flag' => 'ti-flag', 'ti-flag-alt' => 'ti-flag-alt', 'ti-flag-alt-2' => 'ti-flag-alt-2', 'ti-eye' => 'ti-eye', 'ti-export' => 'ti-export', 'ti-exchange-vertical' => 'ti-exchange-vertical', 'ti-desktop' => 'ti-desktop', 'ti-cup' => 'ti-cup', 'ti-crown' => 'ti-crown', 'ti-comments' => 'ti-comments', 'ti-comment' => 'ti-comment', 'ti-comment-alt' => 'ti-comment-alt', 'ti-close' => 'ti-close', 'ti-clip' => 'ti-clip', 'ti-angle-up' => 'ti-angle-up', 'ti-angle-right' => 'ti-angle-right', 'ti-angle-left' => 'ti-angle-left', 'ti-angle-down' => 'ti-angle-down', 'ti-check' => 'ti-check', 'ti-check-box' => 'ti-check-box', 'ti-camera' => 'ti-camera', 'ti-announcement' => 'ti-announcement', 'ti-brush' => 'ti-brush', 'ti-briefcase' => 'ti-briefcase', 'ti-bolt' => 'ti-bolt', 'ti-bolt-alt' => 'ti-bolt-alt', 'ti-blackboard' => 'ti-blackboard', 'ti-bag' => 'ti-bag', 'ti-move' => 'ti-move', 'ti-arrows-vertical' => 'ti-arrows-vertical', 'ti-arrows-horizontal' => 'ti-arrows-horizontal', 'ti-fullscreen' => 'ti-fullscreen', 'ti-arrow-top-right' => 'ti-arrow-top-right', 'ti-arrow-top-left' => 'ti-arrow-top-left', 'ti-arrow-circle-up' => 'ti-arrow-circle-up', 'ti-arrow-circle-right' => 'ti-arrow-circle-right', 'ti-arrow-circle-left' => 'ti-arrow-circle-left', 'ti-arrow-circle-down' => 'ti-arrow-circle-down', 'ti-angle-double-up' => 'ti-angle-double-up', 'ti-angle-double-right' => 'ti-angle-double-right', 'ti-angle-double-left' => 'ti-angle-double-left', 'ti-angle-double-down' => 'ti-angle-double-down', 'ti-zip' => 'ti-zip', 'ti-world' => 'ti-world', 'ti-wheelchair' => 'ti-wheelchair', 'ti-view-list' => 'ti-view-list', 'ti-view-list-alt' => 'ti-view-list-alt', 'ti-view-grid' => 'ti-view-grid', 'ti-uppercase' => 'ti-uppercase', 'ti-upload' => 'ti-upload', 'ti-underline' => 'ti-underline', 'ti-truck' => 'ti-truck', 'ti-timer' => 'ti-timer', 'ti-ticket' => 'ti-ticket', 'ti-thumb-up' => 'ti-thumb-up', 'ti-thumb-down' => 'ti-thumb-down', 'ti-text' => 'ti-text', 'ti-stats-up' => 'ti-stats-up', 'ti-stats-down' => 'ti-stats-down', 'ti-split-v' => 'ti-split-v', 'ti-split-h' => 'ti-split-h', 'ti-smallcap' => 'ti-smallcap', 'ti-shine' => 'ti-shine', 'ti-shift-right' => 'ti-shift-right', 'ti-shift-left' => 'ti-shift-left', 'ti-shield' => 'ti-shield', 'ti-notepad' => 'ti-notepad', 'ti-server' => 'ti-server', 'ti-quote-right' => 'ti-quote-right', 'ti-quote-left' => 'ti-quote-left', 'ti-pulse' => 'ti-pulse', 'ti-printer' => 'ti-printer', 'ti-power-off' => 'ti-power-off', 'ti-plug' => 'ti-plug', 'ti-pie-chart' => 'ti-pie-chart', 'ti-paragraph' => 'ti-paragraph', 'ti-panel' => 'ti-panel', 'ti-package' => 'ti-package', 'ti-music' => 'ti-music', 'ti-music-alt' => 'ti-music-alt', 'ti-mouse' => 'ti-mouse', 'ti-mouse-alt' => 'ti-mouse-alt', 'ti-money' => 'ti-money', 'ti-microphone' => 'ti-microphone', 'ti-menu' => 'ti-menu', 'ti-menu-alt' => 'ti-menu-alt', 'ti-map' => 'ti-map', 'ti-map-alt' => 'ti-map-alt', 'ti-loop' => 'ti-loop', 'ti-location-pin' => 'ti-location-pin', 'ti-list' => 'ti-list', 'ti-light-bulb' => 'ti-light-bulb', 'ti-talic' => 'ti-talic', 'ti-info' => 'ti-info', 'ti-infinite' => 'ti-infinite', 'ti-id-badge' => 'ti-id-badge', 'ti-hummer' => 'ti-hummer', 'ti-home' => 'ti-home', 'ti-help' => 'ti-help', 'ti-headphone' => 'ti-headphone', 'ti-harddrives' => 'ti-harddrives', 'ti-harddrive' => 'ti-harddrive', 'ti-gift' => 'ti-gift', 'ti-game' => 'ti-game', 'ti-filter' => 'ti-filter', 'ti-files' => 'ti-files', 'ti-file' => 'ti-file', 'ti-eraser' => 'ti-eraser', 'ti-envelope' => 'ti-envelope', 'ti-download' => 'ti-download', 'ti-direction' => 'ti-direction', 'ti-direction-alt' => 'ti-direction-alt', 'ti-dashboard' => 'ti-dashboard', 'ti-control-stop' => 'ti-control-stop', 'ti-control-shuffle' => 'ti-control-shuffle', 'ti-control-play' => 'ti-control-play', 'ti-control-pause' => 'ti-control-pause', 'ti-control-forward' => 'ti-control-forward', 'ti-control-backward' => 'ti-control-backward', 'ti-cloud' => 'ti-cloud', 'ti-cloud-up' => 'ti-cloud-up', 'ti-cloud-down' => 'ti-cloud-down', 'ti-clipboard' => 'ti-clipboard', 'ti-car' => 'ti-car', 'ti-calendar' => 'ti-calendar', 'ti-book' => 'ti-book', 'ti-bell' => 'ti-bell', 'ti-basketball' => 'ti-basketball', 'ti-bar-chart' => 'ti-bar-chart', 'ti-bar-chart-alt' => 'ti-bar-chart-alt', 'ti-back-right' => 'ti-back-right', 'ti-back-left' => 'ti-back-left', 'ti-arrows-corner' => 'ti-arrows-corner', 'ti-archive' => 'ti-archive', 'ti-anchor' => 'ti-anchor', 'ti-align-right' => 'ti-align-right', 'ti-align-left' => 'ti-align-left', 'ti-align-justify' => 'ti-align-justify', 'ti-align-center' => 'ti-align-center', 'ti-alert' => 'ti-alert', 'ti-alarm-clock' => 'ti-alarm-clock', 'ti-agenda' => 'ti-agenda', 'ti-write' => 'ti-write', 'ti-window' => 'ti-window', 'ti-widgetized' => 'ti-widgetized', 'ti-widget' => 'ti-widget', 'ti-widget-alt' => 'ti-widget-alt', 'ti-wallet' => 'ti-wallet', 'ti-video-clapper' => 'ti-video-clapper', 'ti-video-camera' => 'ti-video-camera', 'ti-vector' => 'ti-vector', 'ti-themify-logo' => 'ti-themify-logo', 'ti-themify-favicon' => 'ti-themify-favicon', 'ti-themify-favicon-alt' => 'ti-themify-favicon-alt', 'ti-support' => 'ti-support', 'ti-stamp' => 'ti-stamp', 'ti-split-v-alt' => 'ti-split-v-alt', 'ti-slice' => 'ti-slice', 'ti-shortcode' => 'ti-shortcode', 'ti-shift-right-alt' => 'ti-shift-right-alt', 'ti-shift-left-alt' => 'ti-shift-left-alt', 'ti-ruler-alt-2' => 'ti-ruler-alt-2', 'ti-receipt' => 'ti-receipt', 'ti-pin2' => 'ti-pin2', 'ti-pin-alt' => 'ti-pin-alt', 'ti-pencil-alt2' => 'ti-pencil-alt2', 'ti-palette' => 'ti-palette', 'ti-more' => 'ti-more', 'ti-more-alt' => 'ti-more-alt', 'ti-microphone-alt' => 'ti-microphone-alt', 'ti-magnet' => 'ti-magnet', 'ti-line-double' => 'ti-line-double', 'ti-line-dotted' => 'ti-line-dotted', 'ti-line-dashed' => 'ti-line-dashed', 'ti-layout-width-full' => 'ti-layout-width-full', 'ti-layout-width-default' => 'ti-layout-width-default', 'ti-layout-width-default-alt' => 'ti-layout-width-default-alt', 'ti-layout-tab' => 'ti-layout-tab', 'ti-layout-tab-window' => 'ti-layout-tab-window', 'ti-layout-tab-v' => 'ti-layout-tab-v', 'ti-layout-tab-min' => 'ti-layout-tab-min', 'ti-layout-slider' => 'ti-layout-slider', 'ti-layout-slider-alt' => 'ti-layout-slider-alt', 'ti-layout-sidebar-right' => 'ti-layout-sidebar-right', 'ti-layout-sidebar-none' => 'ti-layout-sidebar-none', 'ti-layout-sidebar-left' => 'ti-layout-sidebar-left', 'ti-layout-placeholder' => 'ti-layout-placeholder', 'ti-layout-menu' => 'ti-layout-menu', 'ti-layout-menu-v' => 'ti-layout-menu-v', 'ti-layout-menu-separated' => 'ti-layout-menu-separated', 'ti-layout-menu-full' => 'ti-layout-menu-full', 'ti-layout-media-right-alt' => 'ti-layout-media-right-alt', 'ti-layout-media-right' => 'ti-layout-media-right', 'ti-layout-media-overlay' => 'ti-layout-media-overlay', 'ti-layout-media-overlay-alt' => 'ti-layout-media-overlay-alt', 'ti-layout-media-overlay-alt-2' => 'ti-layout-media-overlay-alt-2', 'ti-layout-media-left-alt' => 'ti-layout-media-left-alt', 'ti-layout-media-left' => 'ti-layout-media-left', 'ti-layout-media-center-alt' => 'ti-layout-media-center-alt', 'ti-layout-media-center' => 'ti-layout-media-center', 'ti-layout-list-thumb' => 'ti-layout-list-thumb', 'ti-layout-list-thumb-alt' => 'ti-layout-list-thumb-alt', 'ti-layout-list-post' => 'ti-layout-list-post', 'ti-layout-list-large-image' => 'ti-layout-list-large-image', 'ti-layout-line-solid' => 'ti-layout-line-solid', 'ti-layout-grid4' => 'ti-layout-grid4', 'ti-layout-grid3' => 'ti-layout-grid3', 'ti-layout-grid2' => 'ti-layout-grid2', 'ti-layout-grid2-thumb' => 'ti-layout-grid2-thumb', 'ti-layout-cta-right' => 'ti-layout-cta-right', 'ti-layout-cta-left' => 'ti-layout-cta-left', 'ti-layout-cta-center' => 'ti-layout-cta-center', 'ti-layout-cta-btn-right' => 'ti-layout-cta-btn-right', 'ti-layout-cta-btn-left' => 'ti-layout-cta-btn-left', 'ti-layout-column4' => 'ti-layout-column4', 'ti-layout-column3' => 'ti-layout-column3', 'ti-layout-column2' => 'ti-layout-column2', 'ti-layout-accordion-separated' => 'ti-layout-accordion-separated', 'ti-layout-accordion-merged' => 'ti-layout-accordion-merged', 'ti-layout-accordion-list' => 'ti-layout-accordion-list', 'ti-ink-pen' => 'ti-ink-pen', 'ti-info-alt' => 'ti-info-alt', 'ti-help-alt' => 'ti-help-alt', 'ti-headphone-alt' => 'ti-headphone-alt', 'ti-hand-point-up' => 'ti-hand-point-up', 'ti-hand-point-right' => 'ti-hand-point-right', 'ti-hand-point-left' => 'ti-hand-point-left', 'ti-hand-point-down' => 'ti-hand-point-down', 'ti-gallery' => 'ti-gallery', 'ti-face-smile' => 'ti-face-smile', 'ti-face-sad' => 'ti-face-sad', 'ti-credit-card' => 'ti-credit-card', 'ti-control-skip-forward' => 'ti-control-skip-forward', 'ti-control-skip-backward' => 'ti-control-skip-backward', 'ti-control-record' => 'ti-control-record', 'ti-control-eject' => 'ti-control-eject', 'ti-comments-smiley' => 'ti-comments-smiley', 'ti-brush-alt' => 'ti-brush-alt', 'ti-youtube' => 'ti-youtube', 'ti-vimeo' => 'ti-vimeo', 'ti-twitter' => 'ti-twitter', 'ti-time' => 'ti-time', 'ti-tumblr' => 'ti-tumblr', 'ti-skype' => 'ti-skype', 'ti-share' => 'ti-share', 'ti-share-alt' => 'ti-share-alt', 'ti-rocket' => 'ti-rocket', 'ti-pinterest' => 'ti-pinterest', 'ti-new-window' => 'ti-new-window', 'ti-microsoft' => 'ti-microsoft', 'ti-list-ol' => 'ti-list-ol', 'ti-linkedin' => 'ti-linkedin', 'ti-layout-sidebar-2' => 'ti-layout-sidebar-2', 'ti-layout-grid4-alt' => 'ti-layout-grid4-alt', 'ti-layout-grid3-alt' => 'ti-layout-grid3-alt', 'ti-layout-grid2-alt' => 'ti-layout-grid2-alt', 'ti-layout-column4-alt' => 'ti-layout-column4-alt', 'ti-layout-column3-alt' => 'ti-layout-column3-alt', 'ti-layout-column2-alt' => 'ti-layout-column2-alt', 'ti-instagram' => 'ti-instagram', 'ti-google' => 'ti-google', 'ti-github' => 'ti-github', 'ti-flickr' => 'ti-flickr', 'ti-facebook' => 'ti-facebook', 'ti-dropbox' => 'ti-dropbox', 'ti-dribbble' => 'ti-dribbble', 'ti-apple' => 'ti-apple', 'ti-android' => 'ti-android', 'ti-save' => 'ti-save', 'ti-save-alt' => 'ti-save-alt', 'ti-yahoo' => 'ti-yahoo', 'ti-wordpress' => 'ti-wordpress', 'ti-vimeo-alt' => 'ti-vimeo-alt', 'ti-twitter-alt' => 'ti-twitter-alt', 'ti-tumblr-alt' => 'ti-tumblr-alt', 'ti-trello' => 'ti-trello', 'ti-stack-overflow' => 'ti-stack-overflow', 'ti-soundcloud' => 'ti-soundcloud', 'ti-sharethis' => 'ti-sharethis', 'ti-sharethis-alt' => 'ti-sharethis-alt', 'ti-reddit' => 'ti-reddit', 'ti-pinterest-alt' => 'ti-pinterest-alt', 'ti-microsoft-alt' => 'ti-microsoft-alt', 'ti-linux' => 'ti-linux', 'ti-jsfiddle' => 'ti-jsfiddle', 'ti-joomla' => 'ti-joomla', 'ti-html5' => 'ti-html5', 'ti-flickr-alt' => 'ti-flickr-alt', 'ti-email' => 'ti-email', 'ti-drupal' => 'ti-drupal', 'ti-dropbox-alt' => 'ti-dropbox-alt', 'ti-css3' => 'ti-css3', 'ti-rss' => 'ti-rss', 'ti-rss-alt' => 'ti-rss-alt',
                    'flaticon-loupe' => 'flaticon-loupe', 'flaticon-menu' => 'flaticon-menu', 'flaticon-down-filled-triangular-arrow' => 'flaticon-down-filled-triangular-arrow', 'flaticon-next' => 'flaticon-next', 'flaticon-left-arrow' => 'flaticon-left-arrow', 'flaticon-right' => 'flaticon-right', 'flaticon-right-arrow-1' => 'flaticon-right-arrow-1', 'flaticon-right-up' => 'flaticon-right-up', 'flaticon-left' => 'flaticon-left', 'flaticon-security' => 'flaticon-security', 'flaticon-badge' => 'flaticon-badge', 'flaticon-review' => 'flaticon-review', 'flaticon-rocket' => 'flaticon-rocket', 'flaticon-button' => 'flaticon-button', 'flaticon-developer' => 'flaticon-developer', 'flaticon-web-design' => 'flaticon-web-design', 'flaticon-cv' => 'flaticon-cv', 'flaticon-secure' => 'flaticon-secure', 'flaticon-customer-service' => 'flaticon-customer-service', 'flaticon-money' => 'flaticon-money', 'flaticon-tick' => 'flaticon-tick', 'flaticon-web-design-1' => 'flaticon-web-design-1', 'flaticon-digital-marketing' => 'flaticon-digital-marketing', 'flaticon-translator' => 'flaticon-translator', 'flaticon-microphone' => 'flaticon-microphone', 'flaticon-video-file' => 'flaticon-video-file', 'flaticon-ruler' => 'flaticon-ruler', 'flaticon-goal' => 'flaticon-goal', 'flaticon-star' => 'flaticon-star', 'flaticon-working' => 'flaticon-working', 'flaticon-star-2' => 'flaticon-star-2', 'flaticon-file' => 'flaticon-file', 'flaticon-rocket-1' => 'flaticon-rocket-1', 'flaticon-share' => 'flaticon-share', 'flaticon-website' => 'flaticon-website', 'flaticon-file-1' => 'flaticon-file-1', 'flaticon-calendar' => 'flaticon-calendar', 'flaticon-tracking' => 'flaticon-tracking', 'flaticon-sand-clock' => 'flaticon-sand-clock', 'flaticon-recycle' => 'flaticon-recycle', 'flaticon-place' => 'flaticon-place', 'flaticon-30-days' => 'flaticon-30-days', 'flaticon-target' => 'flaticon-target', 'flaticon-fifteen' => 'flaticon-fifteen', 'flaticon-mars' => 'flaticon-mars', 'flaticon-sliders' => 'flaticon-sliders', 'flaticon-pay-day' => 'flaticon-pay-day', 'flaticon-notification' => 'flaticon-notification', 'flaticon-mail' => 'flaticon-mail', 'flaticon-home' => 'flaticon-home', 'flaticon-photo' => 'flaticon-photo', 'flaticon-logout' => 'flaticon-logout', 'flaticon-delete' => 'flaticon-delete', 'flaticon-share-1' => 'flaticon-share-1', 'flaticon-send' => 'flaticon-send', 'flaticon-refresh' => 'flaticon-refresh', 'flaticon-add' => 'flaticon-add', 'flaticon-notification-1' => 'flaticon-notification-1', 'flaticon-images' => 'flaticon-images', 'flaticon-briefcase' => 'flaticon-briefcase', 'flaticon-presentation' => 'flaticon-presentation', 'flaticon-content' => 'flaticon-content', 'flaticon-search' => 'flaticon-search', 'flaticon-document' => 'flaticon-document', 'flaticon-chat' => 'flaticon-chat', 'flaticon-receipt' => 'flaticon-receipt', 'flaticon-dollar' => 'flaticon-dollar', 'flaticon-web' => 'flaticon-web', 'flaticon-like' => 'flaticon-like', 'flaticon-contract' => 'flaticon-contract', 'flaticon-success' => 'flaticon-success', 'flaticon-sandclock' => 'flaticon-sandclock', 'flaticon-review-1' => 'flaticon-review-1', 'flaticon-like-1' => 'flaticon-like-1', 'flaticon-page' => 'flaticon-page', 'flaticon-call' => 'flaticon-call', 'flaticon-factory' => 'flaticon-factory', 'flaticon-category' => 'flaticon-category', 'flaticon-pencil' => 'flaticon-pencil', 'flaticon-flag' => 'flaticon-flag', 'flaticon-income' => 'flaticon-income', 'flaticon-withdraw' => 'flaticon-withdraw', 'flaticon-price-tag' => 'flaticon-price-tag', 'flaticon-rocket-2' => 'flaticon-rocket-2', 'flaticon-search-1' => 'flaticon-search-1', 'flaticon-wallet' => 'flaticon-wallet', 'flaticon-antivirus' => 'flaticon-antivirus', 'flaticon-web-development' => 'flaticon-web-development', 'flaticon-mobile' => 'flaticon-mobile', 'flaticon-question' => 'flaticon-question', 'flaticon-house' => 'flaticon-house', 'flaticon-home-1' => 'flaticon-home-1'

            ), $icons);

            // Then we set a new list of icons as the options of the icon control
            $controls_registry->get_control( 'icon' )->set_settings( 'options', $new_icons );
        }

        public function custom_icons($icons_args = array()) {
            $flaticon_icons = array(
                'flaticon-loupe', 'flaticon-menu', 'flaticon-down-filled-triangular-arrow', 'flaticon-next', 'flaticon-left-arrow', 'flaticon-right', 'flaticon-right-arrow-1', 'flaticon-right-up', 'flaticon-left', 'flaticon-security', 'flaticon-badge', 'flaticon-review', 'flaticon-rocket', 'flaticon-button', 'flaticon-developer', 'flaticon-web-design', 'flaticon-cv', 'flaticon-secure', 'flaticon-customer-service', 'flaticon-money', 'flaticon-tick', 'flaticon-web-design-1', 'flaticon-digital-marketing', 'flaticon-translator', 'flaticon-microphone', 'flaticon-video-file', 'flaticon-ruler', 'flaticon-goal', 'flaticon-star', 'flaticon-working', 'flaticon-star-2', 'flaticon-file', 'flaticon-rocket-1', 'flaticon-share', 'flaticon-website', 'flaticon-file-1', 'flaticon-calendar', 'flaticon-tracking', 'flaticon-sand-clock', 'flaticon-recycle', 'flaticon-place', 'flaticon-30-days', 'flaticon-target', 'flaticon-fifteen', 'flaticon-mars', 'flaticon-sliders', 'flaticon-pay-day', 'flaticon-notification', 'flaticon-mail', 'flaticon-home', 'flaticon-photo', 'flaticon-logout', 'flaticon-delete', 'flaticon-share-1', 'flaticon-send', 'flaticon-refresh', 'flaticon-add', 'flaticon-notification-1', 'flaticon-images', 'flaticon-briefcase', 'flaticon-presentation', 'flaticon-content', 'flaticon-search', 'flaticon-document', 'flaticon-chat', 'flaticon-receipt', 'flaticon-dollar', 'flaticon-web', 'flaticon-like', 'flaticon-contract', 'flaticon-success', 'flaticon-sandclock', 'flaticon-review-1', 'flaticon-like-1', 'flaticon-page', 'flaticon-call', 'flaticon-factory', 'flaticon-category', 'flaticon-pencil', 'flaticon-flag', 'flaticon-income', 'flaticon-withdraw', 'flaticon-price-tag', 'flaticon-rocket-2', 'flaticon-search-1', 'flaticon-wallet', 'flaticon-antivirus', 'flaticon-web-development', 'flaticon-mobile', 'flaticon-question', 'flaticon-house', 'flaticon-home-1'
            );

            $icons_args['apus-flaticon-icon'] = array(
                'name'          => 'apus-flaticon-icon',
                'label'         => esc_html__( 'Flaticon Icon', 'freeio' ),
                'labelIcon'     => 'fas fa-user',
                'prefix'        => '',
                'displayPrefix' => '',
                'url'           => get_template_directory_uri() . '/css/flaticon.css',
                'icons'         => $flaticon_icons,
                'ver'           => FREEIO_THEME_VERSION,
            );

            $themify_icons = array(
                'ti-volume', 'ti-user', 'ti-unlock', 'ti-unlink', 'ti-trash', 'ti-thought', 'ti-target', 'ti-tag', 'ti-tablet', 'ti-star', 'ti-spray', 'ti-signal', 'ti-shopping-cart', 'ti-shopping-cart-full', 'ti-settings', 'ti-search', 'ti-zoom-in', 'ti-zoom-out', 'ti-cut', 'ti-ruler', 'ti-ruler-pencil', 'ti-ruler-alt', 'ti-bookmark', 'ti-bookmark-alt', 'ti-reload', 'ti-plus', 'ti-pin', 'ti-pencil', 'ti-pencil-alt', 'ti-paint-roller', 'ti-paint-bucket', 'ti-na', 'ti-mobile', 'ti-minus', 'ti-medall', 'ti-medall-alt', 'ti-marker', 'ti-marker-alt', 'ti-arrow-up', 'ti-arrow-right', 'ti-arrow-left', 'ti-arrow-down', 'ti-lock', 'ti-location-arrow', 'ti-link', 'ti-layout', 'ti-layers', 'ti-layers-alt', 'ti-key', 'ti-import', 'ti-image', 'ti-heart', 'ti-heart-broken', 'ti-hand-stop', 'ti-hand-open', 'ti-hand-drag', 'ti-folder', 'ti-flag', 'ti-flag-alt', 'ti-flag-alt-2', 'ti-eye', 'ti-export', 'ti-exchange-vertical', 'ti-desktop', 'ti-cup', 'ti-crown', 'ti-comments', 'ti-comment', 'ti-comment-alt', 'ti-close', 'ti-clip', 'ti-angle-up', 'ti-angle-right', 'ti-angle-left', 'ti-angle-down', 'ti-check', 'ti-check-box', 'ti-camera', 'ti-announcement', 'ti-brush', 'ti-briefcase', 'ti-bolt', 'ti-bolt-alt', 'ti-blackboard', 'ti-bag', 'ti-move', 'ti-arrows-vertical', 'ti-arrows-horizontal', 'ti-fullscreen', 'ti-arrow-top-right', 'ti-arrow-top-left', 'ti-arrow-circle-up', 'ti-arrow-circle-right', 'ti-arrow-circle-left', 'ti-arrow-circle-down', 'ti-angle-double-up', 'ti-angle-double-right', 'ti-angle-double-left', 'ti-angle-double-down', 'ti-zip', 'ti-world', 'ti-wheelchair', 'ti-view-list', 'ti-view-list-alt', 'ti-view-grid', 'ti-uppercase', 'ti-upload', 'ti-underline', 'ti-truck', 'ti-timer', 'ti-ticket', 'ti-thumb-up', 'ti-thumb-down', 'ti-text', 'ti-stats-up', 'ti-stats-down', 'ti-split-v', 'ti-split-h', 'ti-smallcap', 'ti-shine', 'ti-shift-right', 'ti-shift-left', 'ti-shield', 'ti-notepad', 'ti-server', 'ti-quote-right', 'ti-quote-left', 'ti-pulse', 'ti-printer', 'ti-power-off', 'ti-plug', 'ti-pie-chart', 'ti-paragraph', 'ti-panel', 'ti-package', 'ti-music', 'ti-music-alt', 'ti-mouse', 'ti-mouse-alt', 'ti-money', 'ti-microphone', 'ti-menu', 'ti-menu-alt', 'ti-map', 'ti-map-alt', 'ti-loop', 'ti-location-pin', 'ti-list', 'ti-light-bulb', 'ti-talic', 'ti-info', 'ti-infinite', 'ti-id-badge', 'ti-hummer', 'ti-home', 'ti-help', 'ti-headphone', 'ti-harddrives', 'ti-harddrive', 'ti-gift', 'ti-game', 'ti-filter', 'ti-files', 'ti-file', 'ti-eraser', 'ti-envelope', 'ti-download', 'ti-direction', 'ti-direction-alt', 'ti-dashboard', 'ti-control-stop', 'ti-control-shuffle', 'ti-control-play', 'ti-control-pause', 'ti-control-forward', 'ti-control-backward', 'ti-cloud', 'ti-cloud-up', 'ti-cloud-down', 'ti-clipboard', 'ti-car', 'ti-calendar', 'ti-book', 'ti-bell', 'ti-basketball', 'ti-bar-chart', 'ti-bar-chart-alt', 'ti-back-right', 'ti-back-left', 'ti-arrows-corner', 'ti-archive', 'ti-anchor', 'ti-align-right', 'ti-align-left', 'ti-align-justify', 'ti-align-center', 'ti-alert', 'ti-alarm-clock', 'ti-agenda', 'ti-write', 'ti-window', 'ti-widgetized', 'ti-widget', 'ti-widget-alt', 'ti-wallet', 'ti-video-clapper', 'ti-video-camera', 'ti-vector', 'ti-themify-logo', 'ti-themify-favicon', 'ti-themify-favicon-alt', 'ti-support', 'ti-stamp', 'ti-split-v-alt', 'ti-slice', 'ti-shortcode', 'ti-shift-right-alt', 'ti-shift-left-alt', 'ti-ruler-alt-2', 'ti-receipt', 'ti-pin2', 'ti-pin-alt', 'ti-pencil-alt2', 'ti-palette', 'ti-more', 'ti-more-alt', 'ti-microphone-alt', 'ti-magnet', 'ti-line-double', 'ti-line-dotted', 'ti-line-dashed', 'ti-layout-width-full', 'ti-layout-width-default', 'ti-layout-width-default-alt', 'ti-layout-tab', 'ti-layout-tab-window', 'ti-layout-tab-v', 'ti-layout-tab-min', 'ti-layout-slider', 'ti-layout-slider-alt', 'ti-layout-sidebar-right', 'ti-layout-sidebar-none', 'ti-layout-sidebar-left', 'ti-layout-placeholder', 'ti-layout-menu', 'ti-layout-menu-v', 'ti-layout-menu-separated', 'ti-layout-menu-full', 'ti-layout-media-right-alt', 'ti-layout-media-right', 'ti-layout-media-overlay', 'ti-layout-media-overlay-alt', 'ti-layout-media-overlay-alt-2', 'ti-layout-media-left-alt', 'ti-layout-media-left', 'ti-layout-media-center-alt', 'ti-layout-media-center', 'ti-layout-list-thumb', 'ti-layout-list-thumb-alt', 'ti-layout-list-post', 'ti-layout-list-large-image', 'ti-layout-line-solid', 'ti-layout-grid4', 'ti-layout-grid3', 'ti-layout-grid2', 'ti-layout-grid2-thumb', 'ti-layout-cta-right', 'ti-layout-cta-left', 'ti-layout-cta-center', 'ti-layout-cta-btn-right', 'ti-layout-cta-btn-left', 'ti-layout-column4', 'ti-layout-column3', 'ti-layout-column2', 'ti-layout-accordion-separated', 'ti-layout-accordion-merged', 'ti-layout-accordion-list', 'ti-ink-pen', 'ti-info-alt', 'ti-help-alt', 'ti-headphone-alt', 'ti-hand-point-up', 'ti-hand-point-right', 'ti-hand-point-left', 'ti-hand-point-down', 'ti-gallery', 'ti-face-smile', 'ti-face-sad', 'ti-credit-card', 'ti-control-skip-forward', 'ti-control-skip-backward', 'ti-control-record', 'ti-control-eject', 'ti-comments-smiley', 'ti-brush-alt', 'ti-youtube', 'ti-vimeo', 'ti-twitter', 'ti-time', 'ti-tumblr', 'ti-skype', 'ti-share', 'ti-share-alt', 'ti-rocket', 'ti-pinterest', 'ti-new-window', 'ti-microsoft', 'ti-list-ol', 'ti-linkedin', 'ti-layout-sidebar-2', 'ti-layout-grid4-alt', 'ti-layout-grid3-alt', 'ti-layout-grid2-alt', 'ti-layout-column4-alt', 'ti-layout-column3-alt', 'ti-layout-column2-alt', 'ti-instagram', 'ti-google', 'ti-github', 'ti-flickr', 'ti-facebook', 'ti-dropbox', 'ti-dribbble', 'ti-apple', 'ti-android', 'ti-save', 'ti-save-alt', 'ti-yahoo', 'ti-wordpress', 'ti-vimeo-alt', 'ti-twitter-alt', 'ti-tumblr-alt', 'ti-trello', 'ti-stack-overflow', 'ti-soundcloud', 'ti-sharethis', 'ti-sharethis-alt', 'ti-reddit', 'ti-pinterest-alt', 'ti-microsoft-alt', 'ti-linux', 'ti-jsfiddle', 'ti-joomla', 'ti-html5', 'ti-flickr-alt', 'ti-email', 'ti-drupal', 'ti-dropbox-alt', 'ti-css3', 'ti-rss', 'ti-rss-alt'
            );

            $icons_args['apus-themify-icon'] = array(
                'name'          => 'apus-themify-icon',
                'label'         => esc_html__( 'Themify Icon', 'freeio' ),
                'labelIcon'     => 'fas fa-user',
                'prefix'        => '',
                'displayPrefix' => '',
                'url'           => get_template_directory_uri() . '/css/themify-icons.css',
                'icons'         => $themify_icons,
                'ver'           => FREEIO_THEME_VERSION,
            );

            return $icons_args;
        }

        public function additional_animations($animations = array()) {
            $additional_animations = array(
                'ApusTheme' => [
                    'scale' => esc_html__('Scale', 'freeio'),
                    'fancy' => esc_html__('Fancy', 'freeio'),
                    'slide-up' => esc_html__('Slide Up', 'freeio'),
                    'slide-left' => esc_html__('Slide Left', 'freeio'),
                    'slide-right' => esc_html__('Slide Right', 'freeio'),
                    'slide-down' => esc_html__('Slide Down', 'freeio'),
                ],
            );
            return array_merge( $animations, $additional_animations );
        }

        public function render_page_content($post_id) {
            if ( class_exists( 'Elementor\Core\Files\CSS\Post' ) ) {
                $css_file = new Elementor\Core\Files\CSS\Post( $post_id );
                $css_file->enqueue();
            }

            return Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id );
        }

        public function render_post_builder($html, $post) {
            if ( !empty($post) && !empty($post->ID) ) {
                return $this->render_page_content($post->ID);
            }
            return $html;
        }
    }
}

if ( did_action( 'elementor/loaded' ) ) {
    // Finally initialize code
    Freeio_Elementor_Extensions::instance();
}