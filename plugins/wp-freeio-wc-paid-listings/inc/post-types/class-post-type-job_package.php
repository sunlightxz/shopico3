<?php
/**
 * Package
 *
 * @package    wp-freeio-wc-paid-listings
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */
 
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class WP_Freeio_Wc_Paid_Listings_Post_Type_Packages {

  	public static function init() {
    	add_action( 'init', array( __CLASS__, 'register_post_type' ) );

    	add_action( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );

    	add_filter( 'manage_edit-job_package_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_job_package_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

		add_action('restrict_manage_posts', array( __CLASS__, 'filter_job_package_by_type' ));
  	}

  	public static function register_post_type() {
	    $labels = array(
			'name'                  => esc_html__( 'User Package', 'wp-freeio-wc-paid-listings' ),
			'singular_name'         => esc_html__( 'User Package', 'wp-freeio-wc-paid-listings' ),
			'add_new'               => esc_html__( 'Add New Package', 'wp-freeio-wc-paid-listings' ),
			'add_new_item'          => esc_html__( 'Add New Package', 'wp-freeio-wc-paid-listings' ),
			'edit_item'             => esc_html__( 'Edit Package', 'wp-freeio-wc-paid-listings' ),
			'new_item'              => esc_html__( 'New Package', 'wp-freeio-wc-paid-listings' ),
			'all_items'             => esc_html__( 'User Packages', 'wp-freeio-wc-paid-listings' ),
			'view_item'             => esc_html__( 'View Package', 'wp-freeio-wc-paid-listings' ),
			'search_items'          => esc_html__( 'Search Package', 'wp-freeio-wc-paid-listings' ),
			'not_found'             => esc_html__( 'No Packages found', 'wp-freeio-wc-paid-listings' ),
			'not_found_in_trash'    => esc_html__( 'No Packages found in Trash', 'wp-freeio-wc-paid-listings' ),
			'parent_item_colon'     => '',
			'menu_name'             => esc_html__( 'User Packages', 'wp-freeio-wc-paid-listings' ),
	    );

	    register_post_type( 'job_package',
	      	array(
		        'labels'            => apply_filters( 'wp_freeio_wc_paid_listings_postype_package_fields_labels' , $labels ),
		        'supports'          => array( 'title' ),
		        'public'            => true,
		        'has_archive'       => false,
		        'publicly_queryable' => false,
		        'show_in_menu'		=> 'edit.php?post_type=job_listing',
	      	)
	    );
  	}
	
	public static function package_types() {
		$types = array(
			'service_package' => __('Service Package', 'wp-freeio-wc-paid-listings'),
			'project_package' => __('Project Package', 'wp-freeio-wc-paid-listings'),
			'job_package' => __('Job Package', 'wp-freeio-wc-paid-listings'),
			'cv_package' => __('CV Package', 'wp-freeio-wc-paid-listings'),
			'contact_package' => __('Contact Package', 'wp-freeio-wc-paid-listings'),
			'freelancer_package' => __('Freelancer Package', 'wp-freeio-wc-paid-listings'),
			'resume_package' => __('Resume Package', 'wp-freeio-wc-paid-listings'),
		);
		// if ( class_exists( 'WC_Subscriptions' ) ) {
		// 	$types['service_package_subscription'] = __( 'Service Package Subscription', 'wp-freeio-wc-paid-listings' );
		// 	$types['project_package_subscription'] = __( 'Project Package Subscription', 'wp-freeio-wc-paid-listings' );
		// 	$types['job_package_subscription'] = __( 'Job Package Subscription', 'wp-freeio-wc-paid-listings' );
		// 	$types['cv_package_subscription'] = __( 'CV Package Subscription', 'wp-freeio-wc-paid-listings' );
		// 	$types['contact_package_subscription'] = __( 'Contact Package Subscription', 'wp-freeio-wc-paid-listings' );
		// 	$types['freelancer_package_subscription'] = __( 'Freelancer Package Subscription', 'wp-freeio-wc-paid-listings' );
		// 	$types['resume_package_subscription'] = __( 'Resume Package Subscription', 'wp-freeio-wc-paid-listings' );
		// }
		return apply_filters('wp-freeio-wc-paid-listings-package-types', $types);
	}

	public static function get_packages($type = 'job_package') {
		$packages = array( '' => __('Choose a package', 'wp-freeio-wc-paid-listings') );
		$product_packages = WP_Freeio_Wc_Paid_Listings_Mixes::get_job_package_products($type);
		if ( !empty($product_packages) ) {
			foreach ($product_packages as $product) {
				$packages[$product->ID] = $product->post_title;
			}
		}
		return $packages;
	}

  	public static function fields( array $metaboxes ) {
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;


		$package_types = array_merge(array('' => __('Choose package type', 'wp-freeio-wc-paid-listings')), self::package_types());
		$metaboxes[ $prefix . 'general' ] = array(
			'id'                        => $prefix . 'general',
			'title'                     => __( 'General Options', 'wp-freeio-wc-paid-listings' ),
			'object_types'              => array( 'job_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Order Id', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'order_id',
					'type'              => 'text',
				),
				array(
					'name'              => __( 'Employer/Freelancer user id', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'user_id',
					'type'              => 'text',
				),
				array(
					'name'              => __( 'Package Type', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'package_type',
					'type'              => 'select',
					'options'			=> $package_types
				),
			),
		);

		$packages = self::get_packages(array('service_package', 'service_package_subscription'));
		$metaboxes[ $prefix . 'service_package' ] = array(
			'id'                        => $prefix . 'service_package',
			'title'                     => __( 'Service Package Options', 'wp-freeio-wc-paid-listings' ),
			'object_types'              => array( 'job_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Package', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'product_id',
					'type'              => 'select',
					'options'			=> $packages
				),
				array(
					'name'              => __( 'Package Count', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'package_count',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					)
				),
				array(
					'name'              => __( 'Urgent Services', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'urgent_services',
					'type'              => 'checkbox',
					'desc'				=> __( 'Urgent this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Featured Services', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'feature_services',
					'type'              => 'checkbox',
					'desc'				=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Service duration', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'service_duration',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of days that the services will be active', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Services limit', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'service_limit',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of services a user can post with this package', 'wp-freeio-wc-paid-listings' ),
				),
			),
		);

		$packages = self::get_packages(array('project_package', 'project_package_subscription'));
		$metaboxes[ $prefix . 'project_package' ] = array(
			'id'                        => $prefix . 'project_package',
			'title'                     => __( 'Project Package Options', 'wp-freeio-wc-paid-listings' ),
			'object_types'              => array( 'job_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Package', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'product_id',
					'type'              => 'select',
					'options'			=> $packages
				),
				array(
					'name'              => __( 'Package Count', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'package_count',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					)
				),
				array(
					'name'              => __( 'Urgent Projects', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'urgent_projects',
					'type'              => 'checkbox',
					'desc'				=> __( 'Urgent this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Featured Projects', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'feature_projects',
					'type'              => 'checkbox',
					'desc'				=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Project duration', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'project_duration',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of days that the projects will be active', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Projects limit', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'project_limit',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of projects a user can post with this package', 'wp-freeio-wc-paid-listings' ),
				),
			),
		);

		$packages = self::get_packages(array('job_package', 'job_package_subscription'));
		$metaboxes[ $prefix . 'job_package' ] = array(
			'id'                        => $prefix . 'job_package',
			'title'                     => __( 'Job Package Options', 'wp-freeio-wc-paid-listings' ),
			'object_types'              => array( 'job_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Package', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'product_id',
					'type'              => 'select',
					'options'			=> $packages
				),
				array(
					'name'              => __( 'Package Count', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'package_count',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					)
				),
				array(
					'name'              => __( 'Urgent Jobs', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'urgent_jobs',
					'type'              => 'checkbox',
					'desc'				=> __( 'Urgent this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Featured Jobs', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'feature_jobs',
					'type'              => 'checkbox',
					'desc'				=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Job duration', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'job_duration',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of days that the jobs will be active', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Jobs limit', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'job_limit',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of jobs a user can post with this package', 'wp-freeio-wc-paid-listings' ),
				),
			),
		);

		$packages = self::get_packages(array('cv_package', 'cv_package_subscription'));
		$metaboxes[ $prefix . 'cv_package' ] = array(
			'id'                        => $prefix . 'cv_package',
			'title'                     => __( 'CV Package Options', 'wp-freeio-wc-paid-listings' ),
			'object_types'              => array( 'job_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Package', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'cv_product_id',
					'type'              => 'select',
					'options'			=> $packages
				),
				array(
					'name'              => __( 'CV viewed', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'cv_viewed_count',
					'type'              => 'text',
					'desc' 				=> __( 'Enter freelancer ids separate by ","', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Package Expiry Time (Days)', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'cv_package_expiry_time',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of days that the user package active. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Number of CV\'s', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'cv_number_of_cv',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of CV to view in this package. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				),
			),
		);

		$packages = self::get_packages(array('contact_package', 'contact_package_subscription'));
		$metaboxes[ $prefix . 'contact_package' ] = array(
			'id'                        => $prefix . 'contact_package',
			'title'                     => __( 'Contact Package Options', 'wp-freeio-wc-paid-listings' ),
			'object_types'              => array( 'job_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Package', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'contact_product_id',
					'type'              => 'select',
					'options'			=> $packages
				),
				array(
					'name'              => __( 'CV Contacts Sent', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'contact_viewed_count',
					'type'              => 'text',
					'desc' 				=> __( 'Enter freelancer ids separate by ","', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Package Expiry Time (Days)', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'contact_package_expiry_time',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of days that the user package active. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Number of CV\'s', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'contact_number_of_cv',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of CV to view in this package. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				),
			),
		);

		$packages = self::get_packages(array('freelancer_package', 'freelancer_package_subscription'));
		$metaboxes[ $prefix . 'freelancer_package' ] = array(
			'id'                        => $prefix . 'freelancer_package',
			'title'                     => __( 'Freelancer Package Options', 'wp-freeio-wc-paid-listings' ),
			'object_types'              => array( 'job_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Package', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'freelancer_product_id',
					'type'              => 'select',
					'options'			=> $packages
				),
				array(
					'name'              => __( 'Freelancer applications', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'freelancer_applied_count',
					'type'              => 'text',
					'desc' 				=> __( 'Enter applications ids separate by ","', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Package Expiry Time (Days)', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'freelancer_package_expiry_time',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of days that the user package active. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Number of applications', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'freelancer_number_of_applications',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of applications to freelancer apply. Leave this field blank for unlimited', 'wp-freeio-wc-paid-listings' ),
				),
			),
		);

		$packages = self::get_packages(array('resume_package', 'resume_package_subscription'));
		$metaboxes[ $prefix . 'resume_package' ] = array(
			'id'                        => $prefix . 'resume_package',
			'title'                     => __( 'Resume Package Options', 'wp-freeio-wc-paid-listings' ),
			'object_types'              => array( 'job_package' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Package', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'resume_product_id',
					'type'              => 'select',
					'options'			=> $packages
				),
				array(
					'name'              => __( 'Urgent Resumes', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'urgent_resumes',
					'type'              => 'checkbox',
					'desc'				=> __( 'Urgent this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Featured Resumes', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'featured_resumes',
					'type'              => 'checkbox',
					'desc'				=> __( 'Feature this listing - it will be styled differently and sticky.', 'wp-freeio-wc-paid-listings' ),
				),
				array(
					'name'              => __( 'Resume duration', 'wp-freeio-wc-paid-listings' ),
					'id'                => $prefix . 'resumes_duration',
					'type'              => 'text',
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'pattern' 			=> '\d*',
					),
					'desc'				=> __( 'The number of days that the jobs will be active', 'wp-freeio-wc-paid-listings' ),
				),
			),
		);
		return $metaboxes;
	}


	/**
	 * Custom admin columns for post type
	 *
	 * @access public
	 * @return array
	 */
	public static function custom_columns() {
		$fields = array(
			'cb' 				=> '<input type="checkbox" />',
			'title' 			=> __( 'Title', 'wp-freeio-wc-paid-listings' ),
			'package_type' 		=> __( 'Package Type', 'wp-freeio-wc-paid-listings' ),
			'author' 			=> __( 'Author', 'wp-freeio-wc-paid-listings' ),
			'date' 				=> __( 'Date', 'wp-freeio-wc-paid-listings' ),
		);
		return $fields;
	}

	/**
	 * Custom admin columns implementation
	 *
	 * @access public
	 * @param string $column
	 * @return array
	 */
	public static function custom_columns_manage( $column ) {
		global $post;
		$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
		switch ( $column ) {
			case 'package_type':
				$package_type = get_post_meta($post->ID, $prefix.'package_type', true );
				$package_types = self::package_types();
				if ( !empty($package_types[$package_type]) ) {
					echo $package_types[$package_type];
				} else {
					echo '-';
				}
				break;
		}
	}

	public static function filter_job_package_by_type() {
		global $typenow;
		if ( $typenow == 'job_package') {
			// categories
			$selected = isset($_GET['package_type']) ? $_GET['package_type'] : '';
			$package_types = self::package_types();
			if ( ! empty( $package_types ) ){
				?>
				<select name="package_type">
					<option value=""><?php esc_html_e('All package types', 'wp-freeio-wc-paid-listings'); ?></option>
					<?php
					foreach ($package_types as $key => $title) {
						?>
						<option value="<?php echo esc_attr($key); ?>" <?php selected($selected, $key); ?>><?php echo esc_html($title); ?></option>
						<?php
					}
				?>
				</select>
				<?php
			}
		}
	}

}

WP_Freeio_Wc_Paid_Listings_Post_Type_Packages::init();