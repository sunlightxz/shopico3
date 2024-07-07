<?php


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Freeio_Elementor_User_Packages extends Elementor\Widget_Base {

    public function get_name() {
        return 'apus_element_user_packages';
    }

    public function get_title() {
        return esc_html__( 'Apus User Packages', 'freeio' );
    }
    
    public function get_categories() {
        return [ 'freeio-elements' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Content', 'freeio' ),
                'tab' => Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'freeio' ),
                'type' => Elementor\Controls_Manager::TEXT,
                'default' => '',
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
        ?>
        <div class="box-dashboard-wrapper">
            <?php if ($title!=''): ?>
                <h2 class="title">
                    <?php echo esc_attr( $title ); ?>
                </h2>
            <?php endif; ?>
            <div class="inner-list">

                <?php if ( ! is_user_logged_in() ) {
                    ?>
                    <div class="box-list-2">
                        <div class="text-warning"><?php  esc_html_e( 'Please login as "Employer" to see this page.', 'freeio' ); ?></div>
                    </div>  
                    <?php
                } else {
                    $packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_packages_by_user( get_current_user_id(), false, 'all' );
                    if ( !empty($packages) ) {
                    ?>
                        <div class="widget m-0 <?php echo esc_attr($el_class); ?>">
                            <div class="table-responsive">
                                <table class="job-table">
                                    <thead>
                                        <tr>
                                            <th><?php esc_html_e('#', 'freeio'); ?></th>
                                            <th><?php esc_html_e('ID', 'freeio'); ?></th>
                                            <th><?php esc_html_e('Package', 'freeio'); ?></th>
                                            <th><?php esc_html_e('Package Type', 'freeio'); ?></th>
                                            <th><?php esc_html_e('Package Info', 'freeio'); ?></th>
                                            <th><?php esc_html_e('Status', 'freeio'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach ($packages as $package) {
                                            $prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
                                            $package_type = get_post_meta($package->ID, $prefix. 'package_type', true);
                                            $package_types = WP_Freeio_Wc_Paid_Listings_Post_Type_Packages::package_types();
                                            $subscription_type = get_post_meta($package->ID, $prefix. 'subscription_type', true);
                                        ?>
                                            <tr>
                                                <td><?php echo trim($i); ?></td>
                                                <td><?php echo trim($package->ID); ?></td>
                                                <td class="name-package text-theme"><?php echo trim($package->post_title); ?></td>
                                                <td>
                                                    <?php
                                                        if ( !empty($package_types[$package_type]) ) {
                                                            echo esc_html($package_types[$package_type]);
                                                        } else {
                                                            echo '--';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="package-info-wrapper">
                                                    <?php
                                                        switch ($package_type) {
                                                            case 'cv_package':
                                                                $freelancer_ids = get_post_meta($package->ID, $prefix. 'cv_viewed_count', true);
                                                                if ( !empty($freelancer_ids) ) {
                                                                    $freelancer_ids = explode(',', $freelancer_ids);
                                                                    $cv_viewed_count = count( $freelancer_ids );
                                                                } else {
                                                                    $cv_viewed_count = 0;
                                                                }
                                                                $cv_package_expiry_time = get_post_meta($package->ID, $prefix. 'cv_package_expiry_time', true);
                                                                $cv_number_of_cv = get_post_meta($package->ID, $prefix. 'cv_number_of_cv', true);
                                                                ?>
                                                                <ul class="lists-info">
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('CV Count:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($cv_viewed_count); ?></span>
                                                                    </li>
                                                                    <?php if ( $subscription_type !== 'listing' ) { ?>
                                                                        <li>
                                                                            <span class="title-inner"><?php esc_html_e('Expiry Time:', 'freeio'); ?></span>
                                                                            <span class="value"><?php echo sprintf(_n('%d Day', '%d Days', intval($cv_package_expiry_time), 'freeio'), intval($cv_package_expiry_time)); ?></span>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('CV Limit:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($cv_number_of_cv); ?></span>
                                                                    </li>
                                                                </ul>
                                                                <?php
                                                                break;
                                                            case 'contact_package':
                                                                $freelancer_ids = get_post_meta($package->ID, $prefix. 'contact_viewed_count', true);
                                                                if ( !empty($freelancer_ids) ) {
                                                                    $freelancer_ids = explode(',', $freelancer_ids);
                                                                    $contact_viewed_count = count( $freelancer_ids );
                                                                } else {
                                                                    $contact_viewed_count = 0;
                                                                }
                                                                $contact_package_expiry_time = get_post_meta($package->ID, $prefix. 'contact_package_expiry_time', true);
                                                                $contact_number_of_cv = get_post_meta($package->ID, $prefix. 'contact_number_of_cv', true);
                                                                ?>
                                                                <ul class="lists-info">
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('CV Count:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($contact_viewed_count); ?></span>
                                                                    </li>
                                                                    <?php if ( $subscription_type !== 'listing' ) { ?>
                                                                        <li>
                                                                            <span class="title-inner"><?php esc_html_e('Expiry Time:', 'freeio'); ?></span>
                                                                            <span class="value"><?php echo sprintf(_n('%d Day', '%d Days', intval($contact_package_expiry_time), 'freeio'), intval($contact_package_expiry_time)); ?></span>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('CV Limit:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($contact_number_of_cv); ?></span>
                                                                    </li>
                                                                </ul>
                                                                <?php
                                                                break;
                                                            case 'freelancer_package':
                                                                $freelancer_ids = get_post_meta($package->ID, $prefix. 'freelancer_applied_count', true);
                                                                if ( !empty($freelancer_ids) ) {
                                                                    $freelancer_ids = explode(',', $freelancer_ids);
                                                                    $freelancer_applied_count = count( $freelancer_ids );
                                                                } else {
                                                                    $freelancer_applied_count = 0;
                                                                }
                                                                $freelancer_package_expiry_time = get_post_meta($package->ID, $prefix. 'freelancer_package_expiry_time', true);
                                                                $freelancer_number_of_applications = get_post_meta($package->ID, $prefix. 'freelancer_number_of_applications', true);
                                                                ?>
                                                                <ul class="lists-info">
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Applications Count:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($freelancer_applied_count); ?></span>
                                                                    </li>
                                                                    <?php if ( $subscription_type !== 'listing' ) { ?>
                                                                        <li>
                                                                            <span class="title-inner"><?php esc_html_e('Expiry Time:', 'freeio'); ?></span>
                                                                            <span class="value"><?php echo sprintf(_n('%d Day', '%d Days', intval($freelancer_package_expiry_time), 'freeio'), intval($freelancer_package_expiry_time)); ?></span>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Applications Limit:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($freelancer_number_of_applications); ?></span>
                                                                    </li>
                                                                </ul>
                                                                <?php
                                                                break;
                                                            case 'resume_package':
                                                                $urgent_resumes = get_post_meta($package->ID, $prefix. 'urgent_resumes', true);
                                                                $featured_resumes = get_post_meta($package->ID, $prefix. 'feature_resumes', true);
                                                                $resumes_duration = get_post_meta($package->ID, $prefix. 'resumes_duration', true);
                                                                ?>
                                                                <ul class="lists-info">
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Urgent:', 'freeio'); ?></span>
                                                                        <span class="value">
                                                                            <?php
                                                                                if ( $urgent_resumes == 'on' ) {
                                                                                    esc_html_e('Yes', 'freeio');
                                                                                } else {
                                                                                    esc_html_e('No', 'freeio');
                                                                                }
                                                                            ?>
                                                                        </span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Featured:', 'freeio'); ?></span>
                                                                        <span class="value">
                                                                            <?php
                                                                                if ( $featured_resumes == 'on' ) {
                                                                                    esc_html_e('Yes', 'freeio');
                                                                                } else {
                                                                                    esc_html_e('No', 'freeio');
                                                                                }
                                                                            ?>
                                                                        </span>
                                                                    </li>
                                                                    <?php if ( $subscription_type !== 'listing' ) { ?>
                                                                        <li>
                                                                            <span class="title-inner"><?php esc_html_e('Resume Duration:', 'freeio'); ?></span>
                                                                            <span class="value"><?php echo intval($resumes_duration); ?></span>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                                <?php
                                                                break;
                                                            case 'service_package':
                                                                $urgent_services = get_post_meta($package->ID, $prefix. 'urgent_services', true);
                                                                $feature_services = get_post_meta($package->ID, $prefix. 'feature_services', true);
                                                                $package_count = get_post_meta($package->ID, $prefix. 'package_count', true);
                                                                $service_limit = get_post_meta($package->ID, $prefix. 'service_limit', true);
                                                                $service_duration = get_post_meta($package->ID, $prefix. 'service_duration', true);
                                                                ?>
                                                                <ul class="lists-info">
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Urgent:', 'freeio'); ?></span>
                                                                        <span class="value">
                                                                            <?php
                                                                                if ( $urgent_services == 'on' ) {
                                                                                    esc_html_e('Yes', 'freeio');
                                                                                } else {
                                                                                    esc_html_e('No', 'freeio');
                                                                                }
                                                                            ?>
                                                                        </span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Featured:', 'freeio'); ?></span>
                                                                        <span class="value">
                                                                            <?php
                                                                                if ( $feature_services == 'on' ) {
                                                                                    esc_html_e('Yes', 'freeio');
                                                                                } else {
                                                                                    esc_html_e('No', 'freeio');
                                                                                }
                                                                            ?>
                                                                        </span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Posted:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($package_count); ?></span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Limit Posts:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($service_limit); ?></span>
                                                                    </li>
                                                                    <?php if ( $subscription_type !== 'listing' ) { ?>
                                                                        <li>
                                                                            <span class="title-inner"><?php esc_html_e('Service Duration:', 'freeio'); ?></span>
                                                                            <span class="value"><?php echo intval($service_duration); ?></span>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                                <?php
                                                                break;
                                                            case 'project_package':
                                                                $urgent_projects = get_post_meta($package->ID, $prefix. 'urgent_projects', true);
                                                                $feature_projects = get_post_meta($package->ID, $prefix. 'feature_projects', true);
                                                                $package_count = get_post_meta($package->ID, $prefix. 'package_count', true);
                                                                $project_limit = get_post_meta($package->ID, $prefix. 'project_limit', true);
                                                                $project_duration = get_post_meta($package->ID, $prefix. 'project_duration', true);
                                                                ?>
                                                                <ul class="lists-info">
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Urgent:', 'freeio'); ?></span>
                                                                        <span class="value">
                                                                            <?php
                                                                                if ( $urgent_projects == 'on' ) {
                                                                                    esc_html_e('Yes', 'freeio');
                                                                                } else {
                                                                                    esc_html_e('No', 'freeio');
                                                                                }
                                                                            ?>
                                                                        </span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Featured:', 'freeio'); ?></span>
                                                                        <span class="value">
                                                                            <?php
                                                                                if ( $feature_projects == 'on' ) {
                                                                                    esc_html_e('Yes', 'freeio');
                                                                                } else {
                                                                                    esc_html_e('No', 'freeio');
                                                                                }
                                                                            ?>
                                                                        </span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Posted:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($package_count); ?></span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Limit Posts:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($project_limit); ?></span>
                                                                    </li>
                                                                    <?php if ( $subscription_type !== 'listing' ) { ?>
                                                                        <li>
                                                                            <span class="title-inner"><?php esc_html_e('Project Duration:', 'freeio'); ?></span>
                                                                            <span class="value"><?php echo intval($project_duration); ?></span>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                                <?php
                                                                break;
                                                            case 'job_package':
                                                            default:
                                                                $urgent_jobs = get_post_meta($package->ID, $prefix. 'urgent_jobs', true);
                                                                $feature_jobs = get_post_meta($package->ID, $prefix. 'feature_jobs', true);
                                                                $package_count = get_post_meta($package->ID, $prefix. 'package_count', true);
                                                                $job_limit = get_post_meta($package->ID, $prefix. 'job_limit', true);
                                                                $job_duration = get_post_meta($package->ID, $prefix. 'job_duration', true);
                                                                ?>
                                                                <ul class="lists-info">
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Urgent:', 'freeio'); ?></span>
                                                                        <span class="value">
                                                                            <?php
                                                                                if ( $urgent_jobs == 'on' ) {
                                                                                    esc_html_e('Yes', 'freeio');
                                                                                } else {
                                                                                    esc_html_e('No', 'freeio');
                                                                                }
                                                                            ?>
                                                                        </span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Featured:', 'freeio'); ?></span>
                                                                        <span class="value">
                                                                            <?php
                                                                                if ( $feature_jobs == 'on' ) {
                                                                                    esc_html_e('Yes', 'freeio');
                                                                                } else {
                                                                                    esc_html_e('No', 'freeio');
                                                                                }
                                                                            ?>
                                                                        </span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Posted:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($package_count); ?></span>
                                                                    </li>
                                                                    <li>
                                                                        <span class="title-inner"><?php esc_html_e('Limit Posts:', 'freeio'); ?></span>
                                                                        <span class="value"><?php echo intval($job_limit); ?></span>
                                                                    </li>
                                                                    <?php if ( $subscription_type !== 'listing' ) { ?>
                                                                        <li>
                                                                            <span class="title-inner"><?php esc_html_e('Job Duration:', 'freeio'); ?></span>
                                                                            <span class="value"><?php echo intval($job_duration); ?></span>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                                <?php
                                                                break;
                                                        }
                                                    ?>
                                                    </div>
                                                </td>
                                                <td>

                                                    <?php
                                                        $valid = false;
                                                        $user_id = get_current_user_id();
                                                        switch ($package_type) {
                                                            case 'cv_package':
                                                                $valid = WP_Freeio_Wc_Paid_Listings_Mixes::cv_package_is_valid($user_id, $package->ID);
                                                                break;
                                                            case 'contact_package':
                                                                $valid = WP_Freeio_Wc_Paid_Listings_Mixes::contact_package_is_valid($user_id, $package->ID);
                                                                break;
                                                            case 'freelancer_package':
                                                                $valid = WP_Freeio_Wc_Paid_Listings_Mixes::freelancer_package_is_valid($user_id, $package->ID);
                                                                break;
                                                            case 'resume_package':
                                                                $valid = WP_Freeio_Wc_Paid_Listings_Mixes::resume_package_is_valid($user_id, $package->ID);
                                                                break;
                                                            case 'service_package':
                                                                $valid = WP_Freeio_Wc_Paid_Listings_Mixes::service_package_is_valid($user_id, $package->ID);
                                                                break;
                                                            case 'project_package':
                                                                $valid = WP_Freeio_Wc_Paid_Listings_Mixes::project_package_is_valid($user_id, $package->ID);
                                                                break;
                                                            case 'job_package':
                                                            default:
                                                                $valid = WP_Freeio_Wc_Paid_Listings_Mixes::package_is_valid($user_id, $package->ID);
                                                                break;
                                                        }
                                                        if ( !$valid ) {
                                                            echo '<span class="action finish">'.esc_html__('Finished', 'freeio').'</span>';
                                                        } else {
                                                            echo '<span class="action active">'.esc_html__('Active', 'freeio').'</span>';
                                                        }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="not-found"><?php esc_html_e('Don\'t have any packages', 'freeio'); ?></div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?php }

}

if ( version_compare(ELEMENTOR_VERSION, '3.5.0', '<') ) {
    Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Freeio_Elementor_User_Packages );
} else {
    Elementor\Plugin::instance()->widgets_manager->register( new Freeio_Elementor_User_Packages );
}