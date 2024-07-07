<?php
/**
 * Settings
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Settings {

	/**
	 * Option key, and option page slug
	 * @var string
	 */
	private $key = 'wp_freeio_settings';

	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	protected $option_metabox = array();

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 * @since 1.0
	 */
	public function __construct() {
	
		add_action( 'admin_menu', array( $this, 'admin_menu' ) , 10 );

		add_action( 'admin_init', array( $this, 'init' ) );

		//Custom CMB2 Settings Fields
		add_action( 'cmb2_render_wp_freeio_title', 'wp_freeio_title_callback', 10, 5 );
		add_action( 'cmb2_render_wp_freeio_hidden', 'wp_freeio_hidden_callback', 10, 5 );

		add_action( "cmb2_save_options-page_fields", array( $this, 'settings_notices' ), 10, 3 );


		add_action( 'cmb2_render_api_keys', 'wp_freeio_api_keys_callback', 10, 5 );

		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-wp_freeio_properties_page_freelancer-settings", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	public function admin_menu() {
		//Settings

	 	add_menu_page(
			__( 'Freeio Settings', 'wp-freeio' ),
			__( 'Freeio Settings', 'wp-freeio' ),
			'manage_options',
			'freelancer-settings',
			array( $this, 'admin_page_display' ),
			'',
			49
		);

		add_submenu_page('freelancer-settings', __( 'Settings', 'wp-freeio' ), __( 'Settings', 'wp-freeio' ), 'manage_options', 'freelancer-settings' );
	}

	/**
	 * Register our setting to WP
	 * @since  1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Retrieve settings tabs
	 *
	 * @since 1.0
	 * @return array $tabs
	 */
	public function wp_freeio_get_settings_tabs() {
		$tabs             	  = array();
		$tabs['general']  	  = __( 'General', 'wp-freeio' );
		$tabs['pages']   = __( 'Pages', 'wp-freeio' );
		$tabs['job_submission']   = __( 'Submission', 'wp-freeio' );
		$tabs['jobs_settings']   = __( 'Jobs Settings', 'wp-freeio' );
		$tabs['services_settings']   = __( 'Services Settings', 'wp-freeio' );
		$tabs['projects_settings']   = __( 'Projects Settings', 'wp-freeio' );
		$tabs['employer_settings']   = __( 'Employer Settings', 'wp-freeio' );
		$tabs['freelancer_settings']   = __( 'Freelancer Settings', 'wp-freeio' );
		$tabs['payment_settings']   = __( 'Payment Settings', 'wp-freeio' );
	 	$tabs['api_settings'] = __( 'Socials API', 'wp-freeio' );
	 	$tabs['recaptcha_api_settings'] = __( 'ReCaptcha API', 'wp-freeio' );
	 	$tabs['email_notification'] = __( 'Email Notification', 'wp-freeio' );
	 	// $tabs['import_job_integrations'] = __( 'Import Job Integrations', 'wp-freeio' );

		return apply_filters( 'wp_freeio_settings_tabs', $tabs );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  1.0
	 */
	public function admin_page_display() {

		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->wp_freeio_get_settings_tabs() ) ? $_GET['tab'] : 'general';

		?>

		<div class="wrap wp_freeio_settings_page cmb2_options_page <?php echo esc_attr($this->key); ?> <?php echo esc_attr($active_tab); ?>">
			<h2></h2>

			<div class="nav-tab-wrapper">
				<?php
				foreach ( $this->wp_freeio_get_settings_tabs() as $tab_id => $tab_name ) {

					$tab_url = esc_url( add_query_arg( array(
						'settings-updated' => false,
						'tab'              => $tab_id
					) ) );

					$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

					echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );

					echo '</a>';
				}
				?>
			</div>
			
			<?php cmb2_metabox_form( $this->wp_freeio_settings( $active_tab ), $this->key ); ?>

		</div><!-- .wrap -->

		<?php
	}

	/**
	 * Define General Settings Metabox and field configurations.
	 *
	 * Filters are provided for each settings section to allow add-ons and other plugins to add their own settings
	 *
	 * @param $active_tab active tab settings; null returns full array
	 *
	 * @return array
	 */
	public function wp_freeio_settings( $active_tab ) {

		$pages = wp_freeio_cmb2_get_post_options( array(
			'post_type'   => 'page',
			'numberposts' => - 1
		) );
		$cv_mime_types = array();
		$mime_types = WP_Freeio_Mixes::get_cv_mime_types();
		foreach($mime_types as $key => $mine_type) {
			$cv_mime_types[$key] = $key;
		}

		$images_file_types = array();
		$mime_types = WP_Freeio_Mixes::get_image_mime_types();
		foreach($mime_types as $key => $mine_type) {
			$images_file_types[$key] = $key;
		}

		$wp_freeio_settings = array();

		$countries = array( '' => __('All Countries', 'wp-freeio') );
		$countries = array_merge( $countries, WP_Freeio_Indeed_API::indeed_api_countries() );

		// General
		$wp_freeio_settings['general'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'General Settings', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_general', array(
					
					array(
						'name' => __( 'File Types', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_general_settings_3',
						'before_row' => '<div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Images File Types', 'wp-freeio' ),
						'id'      => 'image_file_types',
						'type'    => 'multicheck_inline',
						'options' => $images_file_types,
						'default' => array('jpg', 'jpeg', 'jpe', 'png')
					),
					array(
						'name'    => __( 'CV File Types', 'wp-freeio' ),
						'id'      => 'cv_file_types',
						'type'    => 'multicheck_inline',
						'options' => $cv_mime_types,
						'default' => array('doc', 'pdf', 'docx')
					),
					array(
						'name' => __( 'Map API Settings', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_general_settings_4',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Map Service', 'wp-freeio' ),
						'id'      => 'map_service',
						'type'    => 'select',
						'options' => array(
							'mapbox' => __('Mapbox', 'wp-freeio'),
							'google-map' => __('Google Maps', 'wp-freeio'),
							'here' => __('Here Maps', 'wp-freeio'),
							'openstreetmap' => __('OpenStreetMap', 'wp-freeio'),
						),
					),
					array(
						'name'    => __( 'Google Map API', 'wp-freeio' ),
						'desc'    => __( 'Google requires an API key to retrieve location information for job listings. Acquire an API key from the <a href="https://developers.google.com/maps/documentation/geocoding/get-api-key">Google Maps API developer site.</a>', 'wp-freeio' ),
						'id'      => 'google_map_api_keys',
						'type'    => 'text',
						'default' => '',
						'attributes' => array(
							'data-conditional-id' => 'map_service',
							'data-conditional-value' => 'google-map',
						),
					),
					array(
						'name'    => __( 'Google Maps Style', 'wp-freeio' ),
						'desc' 	  => wp_kses(__('<a href="//snazzymaps.com/">Get custom style</a> and paste it below. If there is nothing added, we will fallback to the Google Maps service.', 'wp-freeio'), array('a' => array('href' => array()))),
						'id'      => 'google_map_style',
						'type'    => 'textarea',
						'default' => '',
						'attributes' => array(
							'data-conditional-id' => 'map_service',
							'data-conditional-value' => 'google-map',
						),
					),
					array(
						'name'    => __( 'Mapbox Token', 'wp-freeio' ),
						'desc' => wp_kses(__('<a href="//www.mapbox.com/help/create-api-access-token/">Get a FREE token</a> and paste it below. If there is nothing added, we will fallback to the Google Maps service.', 'wp-freeio'), array('a' => array('href' => array()))),
						'id'      => 'mapbox_token',
						'type'    => 'text',
						'default' => '',
						'attributes' => array(
							'data-conditional-id' => 'map_service',
							'data-conditional-value' => 'mapbox',
						),
					),
					array(
						'name'    => __( 'Mapbox Style', 'wp-freeio' ),
						'id'      => 'mapbox_style',
						'type'    => 'wp_freeio_image_select',
						'options' => array(
							'streets-v11' => array(
		                        'alt' => esc_html__('streets', 'wp-freeio'),
		                        'img' => WP_FREEIO_PLUGIN_URL . '/assets/images/streets.png'
		                    ),
		                    'light-v10' => array(
		                        'alt' => esc_html__('light', 'wp-freeio'),
		                        'img' => WP_FREEIO_PLUGIN_URL . '/assets/images/light.png'
		                    ),
		                    'dark-v10' => array(
		                        'alt' => esc_html__('dark', 'wp-freeio'),
		                        'img' => WP_FREEIO_PLUGIN_URL . '/assets/images/dark.png'
		                    ),
		                    'outdoors-v11' => array(
		                        'alt' => esc_html__('outdoors', 'wp-freeio'),
		                        'img' => WP_FREEIO_PLUGIN_URL . '/assets/images/outdoors.png'
		                    ),
		                    'satellite-v9' => array(
		                        'alt' => esc_html__('satellite', 'wp-freeio'),
		                        'img' => WP_FREEIO_PLUGIN_URL . '/assets/images/satellite.png'
		                    ),
		                ),
		                'default' => 'streets-v11',
		                'attributes' => array(
							'data-conditional-id' => 'map_service',
							'data-conditional-value' => 'mapbox',
						),
					),
					array(
						'name'    => __( 'Here Maps API Key', 'wp-freeio' ),
						'desc' => wp_kses(__('<a href="https://developer.here.com/tutorials/getting-here-credentials/">Get a API key</a> and paste it below. If there is nothing added, we will fallback to the Google Maps service.', 'wp-freeio'), array('a' => array('href' => array()))),
						'id'      => 'here_map_api_key',
						'type'    => 'text',
						'default' => '',
						'attributes' => array(
							'data-conditional-id' => 'map_service',
							'data-conditional-value' => 'here',
						),
					),
					array(
						'name'    => __( 'Here Maps Style', 'wp-freeio' ),
						'id'      => 'here_map_style',
						'type'    => 'select',
						'options' => array(
							'normal.day' => esc_html__('Normal Day', 'wp-freeio'),
							'normal.day.grey' => esc_html__('Normal Day Grey', 'wp-freeio'),
							'normal.day.transit' => esc_html__('Normal Day Transit', 'wp-freeio'),
							'normal.night' => esc_html__('Normal Night', 'wp-freeio'),
							'reduced.day' => esc_html__('Reduced Day', 'wp-freeio'),
							'reduced.night' => esc_html__('Reduced Night', 'wp-freeio'),
							'pedestrian.day' => esc_html__('Pedestrian Day', 'wp-freeio'),
						),
						'attributes' => array(
							'data-conditional-id' => 'map_service',
							'data-conditional-value' => 'here',
						),
					),
					array(
						'name'    => __( 'Geocoder Country', 'wp-freeio' ),
						'id'      => 'geocoder_country',
						'type'    => 'select',
						'options' => $countries
					),
					array(
						'name' => __( 'Default maps location', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_general_settings_default_maps_location',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Latitude', 'wp-freeio' ),
						'desc'    => __( 'Enter your latitude', 'wp-freeio' ),
						'id'      => 'default_maps_location_latitude',
						'type'    => 'text_small',
						'default' => '43.6568',
					),
					array(
						'name'    => __( 'Longitude', 'wp-freeio' ),
						'desc'    => __( 'Enter your longitude', 'wp-freeio' ),
						'id'      => 'default_maps_location_longitude',
						'type'    => 'text_small',
						'default' => '-79.4512',
					),
					array(
						'name'    => esc_html__( 'Map Pin', 'wp-freeio' ),
						'desc'    => esc_html__( 'Enter your map pin', 'wp-freeio' ),
						'id'      => 'default_maps_pin',
						'type'    => 'file',
						'options' => array(
							'url' => true,
						),
						'query_args' => array(
							'type' => array(
								'image/gif',
								'image/jpeg',
								'image/png',
							),
						),
					),
					array(
						'name' => __( 'Distance Settings', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_general_settings_distance',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Distance unit', 'wp-freeio' ),
						'id'      => 'distance_unit',
						'type'    => 'select',
						'options' => array(
							'km' => __('Kilometers', 'wp-freeio'),
							'miles' => __('Miles', 'wp-freeio'),
						),
						'default' => 'miles',
					),
					array(
						'name' => __( 'Location Settings', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_general_settings_location',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Location Multiple Fields', 'wp-freeio' ),
						'id'      => 'location_multiple_fields',
						'type'    => 'select',
						'options' => array(
							'yes' 	=> __( 'Yes', 'wp-freeio' ),
							'no'   => __( 'No', 'wp-freeio' ),
						),
						'default' => 'yes',
						'desc'    => __( 'You can set 4 fields for regions like: Country, State, City, District', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Number Fields', 'wp-freeio' ),
						'id'      => 'location_nb_fields',
						'type'    => 'select',
						'options' => array(
							'1' => __('1 Field', 'wp-freeio'),
							'2' => __('2 Fields', 'wp-freeio'),
							'3' => __('3 Fields', 'wp-freeio'),
							'4' => __('4 Fields', 'wp-freeio'),
						),
						'default' => '1',
						'desc'    => __( 'You can set 4 fields for regions like: Country, State, City, District', 'wp-freeio' ),
						'attributes' => array(
							'data-conditional-id' => 'location_multiple_fields',
							'data-conditional-value' => 'yes',
						),
					),
					array(
						'name'    => __( 'First Field Label', 'wp-freeio' ),
						'desc'    => __( 'Empty for translate multiple languages', 'wp-freeio' ),
						'id'      => 'location_1_field_label',
						'type'    => 'text',
						'attributes' 	    => array(
							'placeholder'  => 'Country',
							'data-conditional-id' => 'location_multiple_fields',
							'data-conditional-value' => 'yes',
						),
					),
					array(
						'name'    => __( 'Second Field Label', 'wp-freeio' ),
						'desc'    => __( 'Empty for translate multiple languages', 'wp-freeio' ),
						'id'      => 'location_2_field_label',
						'type'    => 'text',
						'attributes' 	    => array(
							'placeholder'  => 'State',
							'data-conditional-id' => 'location_multiple_fields',
							'data-conditional-value' => 'yes',
						)
					),
					array(
						'name'    => __( 'Third Field Label', 'wp-freeio' ),
						'desc'    => __( 'Empty for translate multiple languages', 'wp-freeio' ),
						'id'      => 'location_3_field_label',
						'type'    => 'text',
						'attributes' 	    => array(
							'placeholder'  => 'City',
							'data-conditional-id' => 'location_multiple_fields',
							'data-conditional-value' => 'yes',
						)
					),
					array(
						'name'    => __( 'Fourth Field Label', 'wp-freeio' ),
						'desc'    => __( 'Empty for translate multiple languages', 'wp-freeio' ),
						'id'      => 'location_4_field_label',
						'type'    => 'text',
						'attributes' 	    => array(
							'placeholder'  => 'District',
							'data-conditional-id' => 'location_multiple_fields',
							'data-conditional-value' => 'yes',
						),
						'after_row' => '</div></div></div>'
					)
				)
			)		 
		);
		
		// Pages
		$wp_freeio_settings['pages'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Pages', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_pages', array(
					array(
						'name' => __( 'General Settings', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_page_general_settings',
						'before_row' => '<div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Jobs Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the jobs listing page. The <code>[wp_freeio_jobs]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'jobs_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					
					array(
						'name'    => __( 'Services Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the services listing page. The <code>[wp_freeio_services]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'services_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					
					array(
						'name'    => __( 'Projects Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the projects listing page. The <code>[wp_freeio_projects]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'projects_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),

					array(
						'name'    => __( 'Employers Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the employers listing page. The <code>[wp_freeio_employers]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'employers_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),

					array(
						'name'    => __( 'Freelancers Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the freelancers listing page. The <code>[wp_freeio_freelancers]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'freelancers_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					
					array(
						'name'    => __( 'Login Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the job listings page. The <code>[wp_freeio_login]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'login_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Register Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the job listings page. The <code>[wp_freeio_register_employer]</code> <code>[wp_freeio_register_freelancer]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'register_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'After Login Page (Employer)', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the page after employer login.', 'wp-freeio' ),
						'id'      => 'after_login_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'After Login Page (Freelancer)', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the page after freelancer login.', 'wp-freeio' ),
						'id'      => 'after_login_page_id_freelancer',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'After Register Page (Employer)', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the page after employer register.', 'wp-freeio' ),
						'id'      => 'after_register_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'After Register Page (Freelancer)', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the page after freelancer register.', 'wp-freeio' ),
						'id'      => 'after_register_page_id_freelancer',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Approve User Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the job listings page. The <code>[wp_freeio_approve_user]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'approve_user_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Freelancer Dashboard Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the user dashboard. The <code>[wp_freeio_user_dashboard]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'user_dashboard_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Employer Dashboard Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the user dashboard. The <code>[wp_freeio_user_dashboard]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'employer_dashboard_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Edit Profile Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the user edit profile. The <code>[wp_freeio_change_profile]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'edit_profile_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Change Password Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the user edit profile. The <code>[wp_freeio_change_password]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'change_password_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'My Jobs Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the job listings page. The <code>[wp_freeio_my_jobs]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'my_jobs_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'My Projects Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the projects page. The <code>[wp_freeio_my_projects]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'my_projects_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'My Services Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the services page. The <code>[wp_freeio_my_services]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'my_services_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'My Proposals Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the services page. The <code>[wp_freeio_my_proposals]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'my_proposals_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'My Bought Services Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the services page. The <code>[wp_freeio_my_bought_services]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'my_bought_services_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'My Disputes Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the location of the services page. The <code>[wp_freeio_dispute]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'my_disputes_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Terms and Conditions Page', 'wp-freeio' ),
						'desc'    => __( 'This lets the plugin know the Terms and Conditions page.', 'wp-freeio' ),
						'id'      => 'terms_conditions_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page',
						'after_row' => '</div></div></div>'
					),
				), $pages
			)		 
		);
		
		// Job Submission
		$wp_freeio_settings['job_submission'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Job Submission', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_job_submission', array(
					
					// Service Submission
					array(
						'name' => __( 'Service Submission', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_service_submission_settings_1',
						'before_row' => '<div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Submit Service Form Page', 'wp-freeio' ),
						'desc'    => __( 'This is page to display form for submit service. The <code>[wp_freeio_submission_service]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'submit_service_form_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Moderate New Services', 'wp-freeio' ),
						'desc'    => __( 'Require admin approval of all new service submissions', 'wp-freeio' ),
						'id'      => 'submission_service_requires_approval',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
					),
					array(
						'name'    => __( 'Allow Published Edits', 'wp-freeio' ),
						'desc'    => __( 'Choose whether published service services can be edited and if edits require admin approval. When moderation is required, the original service services will be unpublished while edits await admin approval.', 'wp-freeio' ),
						'id'      => 'user_edit_published_submission_service',
						'type'    => 'select',
						'options' => array(
							'no' 	=> __( 'Users cannot edit', 'wp-freeio' ),
							'yes'   => __( 'Users can edit without admin approval', 'wp-freeio' ),
							'yes_moderated'   => __( 'Users can edit, but edits require admin approval', 'wp-freeio' ),
						),
						'default' => 'yes',
					),
					array(
						'name'            => __( 'Service Duration', 'wp-freeio' ),
						'desc'            => __( 'Services will display for the set number of days, then expire. Enter this field "0" if you don\'t want services to have an expiration date.', 'wp-freeio' ),
						'id'              => 'submission_service_duration',
						'type'            => 'text_small',
						'default'         => 30,
					),
					array(
						'name' => __( 'Service Slug Settings', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_service_submission_service_slug',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Freelancer Name', 'wp-freeio' ),
						'desc'    => __( 'Use freelancer name in service slug', 'wp-freeio' ),
						'id'      => 'submission_service_slug_freelancer',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
					),
					array(
						'name'    => __( 'Category', 'wp-freeio' ),
						'desc'    => __( 'Use category in service slug', 'wp-freeio' ),
						'id'      => 'submission_service_slug_category',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
					),
					// Project Submission
					array(
						'name' => __( 'Project Submission', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_project_submission_settings_1',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Submit Project Form Page', 'wp-freeio' ),
						'desc'    => __( 'This is page to display form for submit project. The <code>[wp_freeio_submission_project]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'submit_project_form_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Moderate New Projects', 'wp-freeio' ),
						'desc'    => __( 'Require admin approval of all new project submissions', 'wp-freeio' ),
						'id'      => 'submission_project_requires_approval',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
					),
					array(
						'name'    => __( 'Allow Published Edits', 'wp-freeio' ),
						'desc'    => __( 'Choose whether published project projects can be edited and if edits require admin approval. When moderation is required, the original project projects will be unpublished while edits await admin approval.', 'wp-freeio' ),
						'id'      => 'user_edit_published_submission_project',
						'type'    => 'select',
						'options' => array(
							'no' 	=> __( 'Users cannot edit', 'wp-freeio' ),
							'yes'   => __( 'Users can edit without admin approval', 'wp-freeio' ),
							'yes_moderated'   => __( 'Users can edit, but edits require admin approval', 'wp-freeio' ),
						),
						'default' => 'yes',
					),
					array(
						'name'            => __( 'Project Duration', 'wp-freeio' ),
						'desc'            => __( 'Projects will display for the set number of days, then expire. Enter this field "0" if you don\'t want projects to have an expiration date.', 'wp-freeio' ),
						'id'              => 'submission_project_duration',
						'type'            => 'text_small',
						'default'         => 30,
					),
					array(
						'name' => __( 'Project Slug Settings', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_project_submission_project_slug',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Employer Name', 'wp-freeio' ),
						'desc'    => __( 'Use employer name in project slug', 'wp-freeio' ),
						'id'      => 'submission_project_slug_employer',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
					),
					array(
						'name'    => __( 'Category', 'wp-freeio' ),
						'desc'    => __( 'Use category in project slug', 'wp-freeio' ),
						'id'      => 'submission_project_slug_category',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
					),
					// Job Submission
					array(
						'name' => __( 'Job Submission', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_job_submission_settings_1',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Submit Job Form Page', 'wp-freeio' ),
						'desc'    => __( 'This is page to display form for submit job. The <code>[wp_freeio_submission_job]</code> shortcode should be on this page.', 'wp-freeio' ),
						'id'      => 'submit_job_form_page_id',
						'type'    => 'select',
						'options' => $pages,
						'page-type' => 'page'
					),
					array(
						'name'    => __( 'Moderate New Listings', 'wp-freeio' ),
						'desc'    => __( 'Require admin approval of all new listing submissions', 'wp-freeio' ),
						'id'      => 'submission_requires_approval',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
					),
					array(
						'name'    => __( 'Allow Published Edits', 'wp-freeio' ),
						'desc'    => __( 'Choose whether published job listings can be edited and if edits require admin approval. When moderation is required, the original job listings will be unpublished while edits await admin approval.', 'wp-freeio' ),
						'id'      => 'user_edit_published_submission',
						'type'    => 'select',
						'options' => array(
							'no' 	=> __( 'Users cannot edit', 'wp-freeio' ),
							'yes'   => __( 'Users can edit without admin approval', 'wp-freeio' ),
							'yes_moderated'   => __( 'Users can edit, but edits require admin approval', 'wp-freeio' ),
						),
						'default' => 'yes',
					),
					array(
						'name'            => __( 'Listing Duration', 'wp-freeio' ),
						'desc'            => __( 'Listings will display for the set number of days, then expire. Enter this field "0" if you don\'t want listings to have an expiration date.', 'wp-freeio' ),
						'id'              => 'submission_duration',
						'type'            => 'text_small',
						'default'         => 30,
					),
					array(
						'name' => __( 'Job Slug Settings', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_job_submission_job_slug',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Employer Name', 'wp-freeio' ),
						'desc'    => __( 'Use employer name in job slug', 'wp-freeio' ),
						'id'      => 'submission_job_slug_employer',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
					),
					array(
						'name'    => __( 'Location', 'wp-freeio' ),
						'desc'    => __( 'Use location in job slug', 'wp-freeio' ),
						'id'      => 'submission_job_slug_location',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
					),
					array(
						'name'    => __( 'Job Type', 'wp-freeio' ),
						'desc'    => __( 'Use job type in job slug', 'wp-freeio' ),
						'id'      => 'submission_job_slug_type',
						'type'    => 'select',
						'options' => array(
							'on' 	=> __( 'Enable', 'wp-freeio' ),
							'off'   => __( 'Disable', 'wp-freeio' ),
						),
						'default' => 'on',
						'after_row' => '</div></div></div>'
					),
				), $pages
			)		 
		);
		
		// Jobs Settings
		$wp_freeio_settings['jobs_settings'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Jobs Settings', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_jobs_settings', array(
				
				array(
					'name' => __( 'General Settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_general_settings_1',
					'before_row' => '<div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">'
				),
				array(
					'name'    => __( 'Number jobs per page', 'wp-freeio' ),
					'desc'    => __( 'Number of jobs to display per page.', 'wp-freeio' ),
					'id'      => 'number_jobs_per_page',
					'type'    => 'text',
					'default' => '10',
				),
				

				array(
					'name' => __( 'Restrict Job settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_jobs_settings_restrict_job',
					'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">'
				),
				array(
					'name'    => __( 'Restrict Job Type', 'wp-freeio' ),
					'desc'    => __( 'Select a restrict type for restrict job', 'wp-freeio' ),
					'id'      => 'job_restrict_type',
					'type'    => 'select',
					'options' => array(
						'' => __( 'None', 'wp-freeio' ),
						'view' => __( 'View Job', 'wp-freeio' ),
					),
					'default' => ''
				),
				array(
					'name'    => __( 'Restrict Job Detail', 'wp-freeio' ),
					'desc'    => __( 'Restrict Jobs detail page for all users except jobs.', 'wp-freeio' ),
					'id'      => 'job_restrict_detail',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-job-detail', array(
						'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'register_freelancer' => __( 'Register Freelancers (All registered freelancers can view jobs.)', 'wp-freeio' ),
						'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
					)),
					'default' => 'all',
					'attributes' => array(
						'data-conditional-id' => 'job_restrict_type',
						'data-conditional-value' => 'view',
					),
				),
				array(
					'name'    => __( 'Restrict Job Listing', 'wp-freeio' ),
					'desc'    => __( 'Restrict Jobs Listing page for all users except jobs.', 'wp-freeio' ),
					'id'      => 'job_restrict_listing',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-job-listing', array(
						'all' => __( 'All Users (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'register_freelancer' => __( 'Register Freelancers (All registered freelancers can view jobs.)', 'wp-freeio' ),
						'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
					)),
					'default' => 'all',
					'after_row' => '</div></div></div>',
					'attributes' => array(
						'data-conditional-id' => 'job_restrict_type',
						'data-conditional-value' => 'view',
					),
				),

			), $pages )
		);

		// Services Settings

		$fields = array(
			array(
				'name' => __( 'General Settings', 'wp-freeio' ),
				'type' => 'wp_freeio_title',
				'id'   => 'wp_freeio_title_serivce_general_settings_1',
				'before_row' => '<div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
				'after_row' => '<div class="before-group-row-inner-content">'
			),
			array(
				'name'    => __( 'Number services per page', 'wp-freeio' ),
				'desc'    => __( 'Number of services to display per page.', 'wp-freeio' ),
				'id'      => 'number_services_per_page',
				'type'    => 'text',
				'default' => '10',
			),
			

			array(
				'name' => __( 'Restrict Service settings', 'wp-freeio' ),
				'type' => 'wp_freeio_title',
				'id'   => 'wp_freeio_title_services_settings_restrict_service',
				'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
				'after_row' => '<div class="before-group-row-inner-content">'
			),
			array(
				'name'    => __( 'Restrict Service Type', 'wp-freeio' ),
				'desc'    => __( 'Select a restrict type for restrict service', 'wp-freeio' ),
				'id'      => 'service_restrict_type',
				'type'    => 'select',
				'options' => array(
					'' => __( 'None', 'wp-freeio' ),
					'view' => __( 'View Service', 'wp-freeio' ),
				),
				'default' => ''
			),
			array(
				'name'    => __( 'Restrict Service Detail', 'wp-freeio' ),
				'desc'    => __( 'Restrict Services detail page for all users except services.', 'wp-freeio' ),
				'id'      => 'service_restrict_detail',
				'type'    => 'radio',
				'options' => apply_filters( 'wp-freeio-restrict-service-detail', array(
					'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
					'register_user' => __( 'All Register Users', 'wp-freeio' ),
					'register_freelancer' => __( 'Register Freelancers (All registered freelancers can view services.)', 'wp-freeio' ),
					'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
				)),
				'default' => 'all',
				'attributes' => array(
					'data-conditional-id' => 'service_restrict_type',
					'data-conditional-value' => 'view',
				),
			),
			array(
				'name'    => __( 'Restrict Service Listing', 'wp-freeio' ),
				'desc'    => __( 'Restrict Services Listing page for all users except services.', 'wp-freeio' ),
				'id'      => 'service_restrict_listing',
				'type'    => 'radio',
				'options' => apply_filters( 'wp-freeio-restrict-service-listing', array(
					'all' => __( 'All Users (Users, Guests)', 'wp-freeio' ),
					'register_user' => __( 'All Register Users', 'wp-freeio' ),
					'register_employer' => __( 'Register Employers (All registered employers can view services.)', 'wp-freeio' ),
					'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
				)),
				'default' => 'all',
				'attributes' => array(
					'data-conditional-id' => 'service_restrict_type',
					'data-conditional-value' => 'view',
				),
			),
			array(
				'name' => __( 'Service Review settings', 'wp-freeio' ),
				'type' => 'wp_freeio_title',
				'id'   => 'wp_freeio_title_service_settings_service_review',
				'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
				'after_row' => '<div class="before-group-row-inner-content">'
			),
			array(
				'name'    => __( 'Enabled Review', 'wp-freeio' ),
				'id'      => 'service_enable_review',
				'type'    => 'select',
				'options' => array(
					'enable' => __( 'Enable', 'wp-freeio' ),
					'disbale' => __( 'Disbale', 'wp-freeio' ),
				),
				'default' => 'enable',
			),
			array(
				'name'    => __( 'Restrict Review Form', 'wp-freeio' ),
				'id'      => 'services_restrict_review',
				'type'    => 'radio',
				'options' => apply_filters( 'wp-freeio-restrict-service-review', array(
					'all' => __( 'All (Users, Guests can see review services)', 'wp-freeio' ),
					'register_user' => __( 'All Register Users can see review services', 'wp-freeio' ),
					'register_employer' => __( 'Register Employers (All registered employers can see review services.)', 'wp-freeio' ),
					'employer_bought_service' => __( 'Employers bought service (All employers bought service can review services.)', 'wp-freeio' ),
					'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
				)),
				'default' => 'all',
				'after_row' => '</div></div></div>'
			),
		);
		
		if (class_exists('WooCommerce')) {
			$query_args = array(
			   	'post_type' => 'product',
			   	'post_status' => 'publish',
				'posts_per_page'   => -1,
				'order'            => 'asc',
				'orderby'          => 'menu_order',
				'tax_query' => array(
			        array(
			            'taxonomy' => 'product_type',
			            'field'    => 'slug',
			            'terms'    => array('simple'),
			        ),
			    ),
			);
			$products = new WP_Query($query_args);
			
			$product_opts = ['' => esc_html__('Choose a product', 'wp-freeio')];
			if ( !empty($products->posts) ) {
			    foreach ($products->posts as $p) {
			    	$product_opts[$p->ID] = $p->post_title;
			    }
			}

			$fields[] = array(
				'name' => __( 'WooCommerce Product Settings', 'wp-freeio' ),
				'type' => 'wp_freeio_title',
				'id'   => 'wp_freeio_title_service_woocommerce_product_settings',
				'before_row' => '<div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
				'after_row' => '<div class="before-group-row-inner-content">'
			);
			$fields[] = array(
				'name'    => __( 'WooCommerce Product', 'wp-freeio' ),
				'id'      => 'services_woocommerce_product_id',
				'type'    => 'select',
				'options' => $product_opts,
				'default' => 'all',
				'after_row' => '</div></div></div>'
			);
		}

		$wp_freeio_settings['services_settings'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Services Settings', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_services_settings', $fields, $pages )
		);
		
		// Projects Settings
		$fields = array(
			array(
				'name' => __( 'General Settings', 'wp-freeio' ),
				'type' => 'wp_freeio_title',
				'id'   => 'wp_freeio_title_project_general_settings_1',
				'before_row' => '<div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
				'after_row' => '<div class="before-group-row-inner-content">'
			),
			array(
				'name'    => __( 'Number projects per page', 'wp-freeio' ),
				'desc'    => __( 'Number of projects to display per page.', 'wp-freeio' ),
				'id'      => 'number_projects_per_page',
				'type'    => 'text',
				'default' => '10',
			),
			

			array(
				'name' => __( 'Restrict Project settings', 'wp-freeio' ),
				'type' => 'wp_freeio_title',
				'id'   => 'wp_freeio_title_projects_settings_restrict_project',
				'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
				'after_row' => '<div class="before-group-row-inner-content">'
			),
			array(
				'name'    => __( 'Restrict Project Type', 'wp-freeio' ),
				'desc'    => __( 'Select a restrict type for restrict project', 'wp-freeio' ),
				'id'      => 'project_restrict_type',
				'type'    => 'select',
				'options' => array(
					'' => __( 'None', 'wp-freeio' ),
					'view' => __( 'View Project', 'wp-freeio' ),
				),
				'default' => ''
			),
			array(
				'name'    => __( 'Restrict Project Detail', 'wp-freeio' ),
				'desc'    => __( 'Restrict Projects detail page for all users except projects.', 'wp-freeio' ),
				'id'      => 'project_restrict_detail',
				'type'    => 'radio',
				'options' => apply_filters( 'wp-freeio-restrict-project-detail', array(
					'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
					'register_user' => __( 'All Register Users', 'wp-freeio' ),
					'register_freelancer' => __( 'Register Freelancers (All registered freelancers can view projects.)', 'wp-freeio' ),
					'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
				)),
				'default' => 'all',
				'attributes' => array(
					'data-conditional-id' => 'project_restrict_type',
					'data-conditional-value' => 'view',
				),
			),
			array(
				'name'    => __( 'Restrict Project Listing', 'wp-freeio' ),
				'desc'    => __( 'Restrict Projects Listing page for all users except projects.', 'wp-freeio' ),
				'id'      => 'project_restrict_listing',
				'type'    => 'radio',
				'options' => apply_filters( 'wp-freeio-restrict-project-listing', array(
					'all' => __( 'All Users (Users, Guests)', 'wp-freeio' ),
					'register_user' => __( 'All Register Users', 'wp-freeio' ),
					'register_freelancer' => __( 'Register Freelancers (All registered freelancers can view projects.)', 'wp-freeio' ),
					'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
				)),
				'default' => 'all',
				'attributes' => array(
					'data-conditional-id' => 'project_restrict_type',
					'data-conditional-value' => 'view',
				),
			),
			array(
				'name' => __( 'Project Review settings', 'wp-freeio' ),
				'type' => 'wp_freeio_title',
				'id'   => 'wp_freeio_title_project_settings_project_review',
				'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
				'after_row' => '<div class="before-group-row-inner-content">'
			),
			array(
				'name'    => __( 'Restrict Review', 'wp-freeio' ),
				'id'      => 'projects_restrict_review',
				'type'    => 'radio',
				'options' => apply_filters( 'wp-freeio-restrict-project-review', array(
					'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
					'register_user' => __( 'All Register Users', 'wp-freeio' ),
					'register_freelancer' => __( 'Register Freelancers (All registered freelancers can see review projects.)', 'wp-freeio' ),
					'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
				)),
				'default' => 'all',
				'after_row' => '</div></div></div>'
			)
		);
		if (class_exists('WooCommerce')) {
			$fields[] = array(
				'name' => __( 'WooCommerce Product Settings', 'wp-freeio' ),
				'type' => 'wp_freeio_title',
				'id'   => 'wp_freeio_title_service_woocommerce_product_settings',
				'before_row' => '<div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
				'after_row' => '<div class="before-group-row-inner-content">'
			);
			$fields[] = array(
				'name'    => __( 'WooCommerce Product', 'wp-freeio' ),
				'id'      => 'projects_woocommerce_product_id',
				'type'    => 'select',
				'options' => $product_opts,
				'default' => 'all',
				'after_row' => '</div></div></div>'
			);
		}
		$wp_freeio_settings['projects_settings'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Projects Settings', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_projects_settings', $fields, $pages )
		);

		// Employer Settings
		$wp_freeio_settings['employer_settings'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Employer Settings', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_employer_settings', array(
				array(
					'name' => __( 'General Settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_employer_general_settings',
					'before_row' => '<div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">'
				),

				array(
					'name'    => __( 'Moderate New Employer', 'wp-freeio' ),
					'desc'    => __( 'Require admin approval of all new employers', 'wp-freeio' ),
					'id'      => 'employers_requires_approval',
					'type'    => 'select',
					'options' => array(
						'auto' 	=> __( 'Auto Approve', 'wp-freeio' ),
						'email_approve' => __( 'Email Approve', 'wp-freeio' ),
						'admin_approve' => __( 'Administrator Approve', 'wp-freeio' ),
					),
					'default' => 'auto',
				),
				
				array(
					'name'    => __( 'Number employers per page', 'wp-freeio' ),
					'desc'    => __( 'Number of employers to display per page.', 'wp-freeio' ),
					'id'      => 'number_employers_per_page',
					'type'    => 'text',
					'default' => '10',
				),

				array(
					'name' => __( 'Employer Slug', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_employer_submission_job_slug',
					'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">'
				),
				array(
					'name'    => __( 'Employer Slug', 'wp-freeio' ),
					'id'      => 'employer_slug_employer',
					'type'    => 'select',
					'options' => array(
						'' 	=> __( 'Default (Employer name)', 'wp-freeio' ),
						'number' 	=> __( 'Random Number', 'wp-freeio' ),
					),
					'default' => '',
				),

				array(
					'name' => __( 'Restrict Employer settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_employer_settings_restrict_employer',
					'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">'
				),
				array(
					'name'    => __( 'Restrict Type', 'wp-freeio' ),
					'desc'    => __( 'Select a restrict type for restrict employer', 'wp-freeio' ),
					'id'      => 'employer_restrict_type',
					'type'    => 'select',
					'options' => array(
						'' => __( 'None', 'wp-freeio' ),
						'view' => __( 'View Employer', 'wp-freeio' ),
						'view_contact_info' => __( 'View Employer Contact Info', 'wp-freeio' ),
					),
					'default' => ''
				),
				array(
					'name'    => __( 'Restrict Employer Detail', 'wp-freeio' ),
					'desc'    => __( 'Restrict Employers detail page for all users except employers.', 'wp-freeio' ),
					'id'      => 'employer_restrict_detail',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-employer-detail', array(
						'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'only_applicants' => __( 'Only Applicants (Freelancer can view only their own applicants employers.)', 'wp-freeio' ),
						'register_freelancer' => __( 'Register Freelancers (All registered freelancers can view employers.)', 'wp-freeio' ),
						'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
					)),
					'default' => 'all',
					'attributes' => array(
						'data-conditional-id' => 'employer_restrict_type',
						'data-conditional-value' => 'view',
					),
				),
				array(
					'name'    => __( 'Restrict Employer Listing', 'wp-freeio' ),
					'desc'    => __( 'Restrict Employers Listing page for all users except employers.', 'wp-freeio' ),
					'id'      => 'employer_restrict_listing',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-employer-listing', array(
						'all' => __( 'All Users (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'only_applicants' => __( 'Only Applicants (Freelancer can view only their own applicants employers.)', 'wp-freeio' ),
						'register_freelancer' => __( 'Register Freelancers (All registered freelancers can view employers.)', 'wp-freeio' ),
						'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
					)),
					'default' => 'all',
					'attributes' => array(
						'data-conditional-id' => 'employer_restrict_type',
						'data-conditional-value' => 'view',
					),
				),

				// restrict contact
				array(
					'name'    => __( 'Restrict View Contact Employer', 'wp-freeio' ),
					'desc'    => __( 'Restrict View Contact Employers detail page for all users except employers.', 'wp-freeio' ),
					'id'      => 'employer_restrict_contact_info',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-employer-view-contact', array(
						'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'only_applicants' => __( 'Only Applicants (Freelancer can see contact info only their own applicants employers.)', 'wp-freeio' ),
						'register_freelancer' => __( 'Register Freelancers (All registered employers can see contact info employers.)', 'wp-freeio' ),
						'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
					)),
					'default' => 'all',
					'attributes' => array(
						'data-conditional-id' => 'employer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),

				array(
					'name'    => __( 'Hide Employer Name', 'wp-freeio' ),
					'id'      => 'restrict_contact_employer_name',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'employer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),
				array(
					'name'    => __( 'Hide Employer Email', 'wp-freeio' ),
					'id'      => 'restrict_contact_employer_email',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'employer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),
				array(
					'name'    => __( 'Hide Employer Phone', 'wp-freeio' ),
					'id'      => 'restrict_contact_employer_phone',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'employer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),
				array(
					'name'    => __( 'Hide Employer Website', 'wp-freeio' ),
					'id'      => 'restrict_contact_employer_website',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'employer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),
				array(
					'name'    => __( 'Hide Employer Socials Media', 'wp-freeio' ),
					'id'      => 'restrict_contact_employer_social',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'employer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),

				array(
					'name' => __( 'Employer Review settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_employer_settings_employer_review',
					'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">'
				),
				array(
					'name'    => __( 'Restrict Review', 'wp-freeio' ),
					'id'      => 'employers_restrict_review',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-employer-review', array(
						'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'only_applicants' => __( 'Only Applicants (Freelancer can see contact info only their own applicants employers.)', 'wp-freeio' ),
						'register_freelancer' => __( 'Register Freelancers (All registered freelancers can review employers.)', 'wp-freeio' ),
						'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
					)),
					'default' => 'all',
				),

				array(
					'name' => __( 'Project Commission settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_employer_settings_project_commission_employer',
					'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">'
				),
				array(
					'name'    => __( 'Project commission fee', 'wp-freeio' ),
					'id'      => 'employers_project_commission_fee',
					'type'    => 'select',
					'options' => apply_filters( 'wp-freeio-commission-type', array(
						'none' => __( 'None', 'wp-freeio' ),
						'fixed' => __( 'Fixed amount', 'wp-freeio' ),
						'percentage' => __( 'Percentage', 'wp-freeio' ),
						'comissions_tiers' => __( 'Commission tiers', 'wp-freeio' ),
					)),
					'default' => 'none',
				),
				array(
					'name'    => __( 'Fixed amount', 'wp-freeio' ),
					'id'      => 'employers_project_commission_fixed_amount',
					'type'    => 'text',
					'default' => '10',
					'attributes' => array(
						'data-conditional-id' => 'employers_project_commission_fee',
						'data-conditional-value' => 'fixed',
						'type'              => 'number',
	                    'min'               => 0,
	                    'pattern'           => '\d*',
					),
				),
				array(
					'name'    => __( 'Percentage', 'wp-freeio' ),
					'id'      => 'employers_project_commission_percentage',
					'type'    => 'text',
					'default' => '20',
					'attributes' => array(
						'data-conditional-id' => 'employers_project_commission_fee',
						'data-conditional-value' => 'percentage',
						'type'              => 'number',
	                    'min'               => 0,
	                    'pattern'           => '\d*',
					),
				),
				array(
	                'name'              => __( 'Comissions tiers', 'wp-freeio' ),
	                'id'                => 'employers_project_comissions_tiers',
	                'type'              => 'group',
	                'options'           => array(
	                    'group_title'       => __( 'Tier {#}', 'wp-freeio' ),
	                    'add_button'        => __( 'Add Another Tier', 'wp-freeio' ),
	                    'remove_button'     => __( 'Remove Tier', 'wp-freeio' ),
	                    'sortable'          => false,
	                    'closed'         => true,
	                ),
	                'fields'            => array(
	                    array(
							'name'    => __( 'Type', 'wp-freeio' ),
							'id'      => 'type',
							'type'    => 'select',
							'options' => array(
								'fixed' => __( 'Fixed amount', 'wp-freeio' ),
								'percentage' => __( 'Percentage', 'wp-freeio' ),
							),
						),
						array(
							'name'    => __( 'Select range', 'wp-freeio' ),
							'id'      => 'range',
							'type'    => 'select',
							'options' => WP_Freeio_Mixes::get_default_comissions_tiers_range(),
						),
	                    array(
	                        'name'      => __( 'Amount', 'wp-freeio' ),
	                        'id'        => 'amount',
	                        'type'      => 'text',
	                        'default'      => '20',
	                        'attributes' => array(
								'type'              => 'number',
			                    'min'               => 0,
			                    'pattern'           => '\d*',
							),
	                    ),
	                ),
	            ),

				array(
					'name' => __( 'Service Commission settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_employer_settings_service_commission_employer',
					'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">'
				),
				array(
					'name'    => __( 'Service commission fee', 'wp-freeio' ),
					'id'      => 'employers_service_commission_fee',
					'type'    => 'select',
					'options' => apply_filters( 'wp-freeio-commission-type', array(
						'none' => __( 'None', 'wp-freeio' ),
						'fixed' => __( 'Fixed amount', 'wp-freeio' ),
						'percentage' => __( 'Percentage', 'wp-freeio' ),
						'comissions_tiers' => __( 'Commission tiers', 'wp-freeio' ),
					)),
					'default' => 'none',
				),
				array(
					'name'    => __( 'Fixed amount', 'wp-freeio' ),
					'id'      => 'employers_service_commission_fixed_amount',
					'type'    => 'text',
					'default' => '10',
					'attributes' => array(
						'data-conditional-id' => 'employers_service_commission_fee',
						'data-conditional-value' => 'fixed',
						'type'              => 'number',
	                    'min'               => 0,
	                    'pattern'           => '\d*',
					),
				),
				array(
					'name'    => __( 'Percentage', 'wp-freeio' ),
					'id'      => 'employers_service_commission_percentage',
					'type'    => 'text',
					'default' => '20',
					'attributes' => array(
						'data-conditional-id' => 'employers_service_commission_fee',
						'data-conditional-value' => 'percentage',
						'type'              => 'number',
	                    'min'               => 0,
	                    'pattern'           => '\d*',
					),
				),
				array(
	                'name'              => __( 'Comissions tiers', 'wp-freeio' ),
	                'id'                => 'employers_service_comissions_tiers',
	                'type'              => 'group',
	                'options'           => array(
	                    'group_title'       => __( 'Tier {#}', 'wp-freeio' ),
	                    'add_button'        => __( 'Add Another Tier', 'wp-freeio' ),
	                    'remove_button'     => __( 'Remove Tier', 'wp-freeio' ),
	                    'sortable'          => false,
	                    'closed'         => true,
	                ),
	                'fields'            => array(
	                    array(
							'name'    => __( 'Type', 'wp-freeio' ),
							'id'      => 'type',
							'type'    => 'select',
							'options' => array(
								'fixed' => __( 'Fixed amount', 'wp-freeio' ),
								'percentage' => __( 'Percentage', 'wp-freeio' ),
							),
						),
						array(
							'name'    => __( 'Select range', 'wp-freeio' ),
							'id'      => 'range',
							'type'    => 'select',
							'options' => WP_Freeio_Mixes::get_default_comissions_tiers_range(),
						),
	                    array(
	                        'name'      => __( 'Amount', 'wp-freeio' ),
	                        'id'        => 'amount',
	                        'type'      => 'text',
	                        'default'      => '20',
	                        'attributes' => array(
								'type'              => 'number',
			                    'min'               => 0,
			                    'pattern'           => '\d*',
							),
	                    ),
	                ),
	            ),

				// Employee Settings
				array(
					'name' => __( 'Employee settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_employer_settings_employee_settings',
					'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_reset_password" class="before-group-row before-group-row-21"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">'
				),
				array(
					'name'    => __( 'Employee View Dashboard', 'wp-freeio' ),
					'id'      => 'employee_view_dashboard',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
				),
				array(
					'name'    => __( 'Employee Submit Job', 'wp-freeio' ),
					'id'      => 'employee_submit_job',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
				),
				array(
					'name'    => __( 'Employee Edit Job', 'wp-freeio' ),
					'id'      => 'employee_edit_job',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
				),
				array(
					'name'    => __( 'Employee Edit Employer Profile', 'wp-freeio' ),
					'id'      => 'employee_edit_employer_profile',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
				),
				array(
					'name'    => __( 'Employee View My Jobs', 'wp-freeio' ),
					'id'      => 'employee_view_my_jobs',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
				),
				array(
					'name'    => __( 'Employee View Applications', 'wp-freeio' ),
					'id'      => 'employee_view_applications',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
				),
				array(
					'name'    => __( 'Employee View Shortlist Freelancer', 'wp-freeio' ),
					'id'      => 'employee_view_shortlist',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
				),
				array(
					'name'    => __( 'Employee View Freelancer Alerts', 'wp-freeio' ),
					'id'      => 'employee_view_freelancer_alert',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'after_row' => '</div></div></div>'
				),
			), $pages )
		);
		// Freelancer Settings
		$wp_freeio_settings['freelancer_settings'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Freelancer Settings', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_freelancer_settings', array(
				array(
					'name' => __( 'General settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_freelancer_settings_general_settings',
					'before_row' => '<div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">',
				),
				array(
					'name'    => __( 'Number freelancers per page', 'wp-freeio' ),
					'desc'    => __( 'Number of freelancers to display per page.', 'wp-freeio' ),
					'id'      => 'number_freelancers_per_page',
					'type'    => 'text',
					'default' => '10',
				),
				array(
					'name' => __( 'Register Freelancer settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_freelancer_settings_register_freelancer',
					'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">',
				),
				array(
					'name'    => __( 'Moderate New Freelancer', 'wp-freeio' ),
					'desc'    => __( 'Require admin approval of all new freelancers', 'wp-freeio' ),
					'id'      => 'freelancers_requires_approval',
					'type'    => 'select',
					'options' => array(
						'auto' 	=> __( 'Auto Approve', 'wp-freeio' ),
						'email_approve' => __( 'Email Approve', 'wp-freeio' ),
						'admin_approve' => __( 'Administrator Approve', 'wp-freeio' ),
					),
					'default' => 'auto',
				),
				array(
					'name'    => __( 'Moderate New Resume', 'wp-freeio' ),
					'desc'    => __( 'Require admin approval of all new resume', 'wp-freeio' ),
					'id'      => 'resumes_requires_approval',
					'type'    => 'select',
					'options' => array(
						'auto' 	=> __( 'Auto Approve', 'wp-freeio' ),
						'admin_approve' => __( 'Administrator Approve', 'wp-freeio' ),
					),
					'default' => 'auto',
				),
				array(
					'name'            => __( 'Resume Duration', 'wp-freeio' ),
					'desc'            => __( 'Resumes will display for the set number of days, then expire. Enter this field "0" if you don\'t want resumes to have an expiration date.', 'wp-freeio' ),
					'id'              => 'resume_duration',
					'type'            => 'text_small',
					'default'         => 30,
				),
				array(
					'name' => __( 'Freelancer Slug', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_freelancer_submission_job_slug',
					'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">',
				),
				array(
					'name'    => __( 'Freelancer Slug', 'wp-freeio' ),
					'id'      => 'freelancer_slug_freelancer',
					'type'    => 'select',
					'options' => array(
						'' 	=> __( 'Default (Freelancer name)', 'wp-freeio' ),
						'number' 	=> __( 'Random Number', 'wp-freeio' ),
					),
					'default' => '',
				),
				array(
					'name' => __( 'Freelancer Apply settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_freelancer_settings_freelancer_appy',
					'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">',
				),
				array(
					'name'    => __( 'Free Job Apply', 'wp-freeio' ),
					'desc'    => __( 'Allow freelancers to apply jobs absolutely package free.', 'wp-freeio' ),
					'id'      => 'freelancer_free_job_apply',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
				),
				array(
					'name'    => __( 'Freelancer packages Page', 'wp-freeio' ),
					'desc'    => __( 'Select Freelancer Packages Page. It will redirect freelancers at selected page to buy package.', 'wp-freeio' ),
					'id'      => 'freelancer_package_page_id',
					'type'    => 'select',
					'options' => $pages,
					'attributes' => array(
						'data-conditional-id' => 'freelancer_free_job_apply',
						'data-conditional-value' => 'off',
					),
				),
				array(
					'name'            => __( 'Apply Job With Complete Resume', 'wp-freeio' ),
					'desc'            => __( '% Freelancer can apply job with percent number resume complete.', 'wp-freeio' ),
					'id'              => 'apply_job_with_percent_resume',
					'type'            => 'text_small',
					'default'         => 70,
					'attributes' 	    => array(
						'type' 				=> 'number',
						'min'				=> 0,
						'max'				=> 100,
						'pattern' 			=> '\d*',
					)
				),
				array(
					'name'    => __( 'Apply job without login', 'wp-freeio' ),
					'desc'    => __( 'Allow freelancers to apply jobs without login.', 'wp-freeio' ),
					'id'      => 'freelancer_apply_job_without_login',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'off',
				),
				array(
					'name'    => __( 'CV required ?', 'wp-freeio' ),
					'desc'    => __( 'Required freelancer choose a CV.', 'wp-freeio' ),
					'id'      => 'freelancer_apply_job_cv_required',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
				),

				array(
					'name' => __( 'Restrict Freelancer settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_freelancer_settings_restrict_freelancer',
					'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">',
				),
				array(
					'name'    => __( 'Restrict Type', 'wp-freeio' ),
					'desc'    => __( 'Select a restrict type for restrict freelancer', 'wp-freeio' ),
					'id'      => 'freelancer_restrict_type',
					'type'    => 'select',
					'options' => array(
						'' => __( 'None', 'wp-freeio' ),
						'view' => __( 'View Freelancer', 'wp-freeio' ),
						'view_contact_info' => __( 'View Freelancer Contact Info', 'wp-freeio' ),
					),
					'default' => ''
				),
				array(
					'name'    => __( 'Restrict Freelancer Detail', 'wp-freeio' ),
					'desc'    => __( 'Restrict Freelancers detail page for all users except employers.', 'wp-freeio' ),
					'id'      => 'freelancer_restrict_detail',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-freelancer-detail', array(
						'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'only_applicants' => __( 'Only Applicants (Employer can view only their own applicants freelancers.)', 'wp-freeio' ),
						'register_employer' => __( 'Register Employers (All registered employers can view freelancers.)', 'wp-freeio' ),
					)),
					'default' => 'all',
					'attributes' => array(
						'data-conditional-id' => 'freelancer_restrict_type',
						'data-conditional-value' => 'view',
					),
				),
				array(
					'name'    => __( 'Restrict Freelancer Listing', 'wp-freeio' ),
					'desc'    => __( 'Restrict Freelancers Listing page for all users except employers.', 'wp-freeio' ),
					'id'      => 'freelancer_restrict_listing',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-freelancer-listing', array(
						'all' => __( 'All Users (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'only_applicants' => __( 'Only Applicants (Employer can view only their own applicants freelancers.)', 'wp-freeio' ),
						'register_employer' => __( 'Register Employers (All registered employers can view freelancers.)', 'wp-freeio' ),
					)),
					'default' => 'all',
					'attributes' => array(
						'data-conditional-id' => 'freelancer_restrict_type',
						'data-conditional-value' => 'view',
					),
				),

				// restrict contact
				array(
					'name'    => __( 'Restrict View Contact Freelancer', 'wp-freeio' ),
					'desc'    => __( 'Restrict View Contact Freelancers detail page for all users except employers.', 'wp-freeio' ),
					'id'      => 'freelancer_restrict_contact_info',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-freelancer-view-contact', array(
						'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'only_applicants' => __( 'Only Applicants (Employer can see contact info only their own applicants freelancers.)', 'wp-freeio' ),
						'register_employer' => __( 'Register Employers (All registered employers can see contact info freelancers.)', 'wp-freeio' ),
					)),
					'default' => 'all',
					'attributes' => array(
						'data-conditional-id' => 'freelancer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),

				array(
					'name'    => __( 'Hide Freelancer Name', 'wp-freeio' ),
					'id'      => 'restrict_contact_freelancer_name',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'freelancer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),
				array(
					'name'    => __( 'Hide Freelancer Email', 'wp-freeio' ),
					'id'      => 'restrict_contact_freelancer_email',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'freelancer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),
				array(
					'name'    => __( 'Hide Freelancer Phone', 'wp-freeio' ),
					'id'      => 'restrict_contact_freelancer_phone',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'freelancer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),
				array(
					'name'    => __( 'Hide Freelancer Socials Media', 'wp-freeio' ),
					'id'      => 'restrict_contact_freelancer_social',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'freelancer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),

				array(
					'name'    => __( 'Hide Freelancer Download CV', 'wp-freeio' ),
					'id'      => 'restrict_contact_freelancer_download_cv',
					'type'    => 'select',
					'options' => array(
						'on' 	=> __( 'Enable', 'wp-freeio' ),
						'off'   => __( 'Disable', 'wp-freeio' ),
					),
					'default' => 'on',
					'attributes' => array(
						'data-conditional-id' => 'freelancer_restrict_type',
						'data-conditional-value' => 'view_contact_info',
					),
				),

				array(
					'name' => __( 'Freelancer Review settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_freelancer_settings_freelancer_review',
					'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">',
				),
				array(
					'name'    => __( 'Restrict Review', 'wp-freeio' ),
					'id'      => 'freelancers_restrict_review',
					'type'    => 'radio',
					'options' => apply_filters( 'wp-freeio-restrict-freelancer-review', array(
						'all' => __( 'All (Users, Guests)', 'wp-freeio' ),
						'register_user' => __( 'All Register Users', 'wp-freeio' ),
						'only_applicants' => __( 'Only Applicants (Employer can see contact info only their own applicants freelancers.)', 'wp-freeio' ),
						'register_employer' => __( 'Register Employers (All registered employers can review freelancers.)', 'wp-freeio' ),
						'always_hidden' => __( 'Always Hidden', 'wp-freeio' ),
					)),
					'default' => 'all',
				),

				array(
					'name' => __( 'Project Commission settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_freelancer_settings_project_commission_freelancer',
					'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">',
				),
				array(
					'name'    => __( 'Project commission fee', 'wp-freeio' ),
					'id'      => 'freelancers_project_commission_fee',
					'type'    => 'select',
					'options' => apply_filters( 'wp-freeio-commission-type', array(
						'none' => __( 'None', 'wp-freeio' ),
						'fixed' => __( 'Fixed amount', 'wp-freeio' ),
						'percentage' => __( 'Percentage', 'wp-freeio' ),
						'comissions_tiers' => __( 'Comissions tiers', 'wp-freeio' ),
					)),
					'default' => 'none',
				),
				array(
					'name'    => __( 'Fixed amount', 'wp-freeio' ),
					'id'      => 'freelancers_project_commission_fixed_amount',
					'type'    => 'text',
					'default' => '10',
					'attributes' => array(
						'data-conditional-id' => 'freelancers_project_commission_fee',
						'data-conditional-value' => 'fixed',
						'type'              => 'number',
	                    'min'               => 0,
	                    'pattern'           => '\d*',
					),
				),
				array(
					'name'    => __( 'Percentage', 'wp-freeio' ),
					'id'      => 'freelancers_project_commission_percentage',
					'type'    => 'text',
					'default' => '20',
					'attributes' => array(
						'data-conditional-id' => 'freelancers_project_commission_fee',
						'data-conditional-value' => 'percentage',
						'type'              => 'number',
	                    'min'               => 0,
	                    'pattern'           => '\d*',
					),
				),
				array(
	                'name'              => __( 'Comissions tiers', 'wp-freeio' ),
	                'id'                => 'freelancers_project_comissions_tiers',
	                'type'              => 'group',
	                'options'           => array(
	                    'group_title'       => __( 'Tier {#}', 'wp-freeio' ),
	                    'add_button'        => __( 'Add Another Tier', 'wp-freeio' ),
	                    'remove_button'     => __( 'Remove Tier', 'wp-freeio' ),
	                    'sortable'          => false,
	                    'closed'         => true,
	                ),
	                'fields'            => array(
	                    array(
							'name'    => __( 'Type', 'wp-freeio' ),
							'id'      => 'type',
							'type'    => 'select',
							'options' => array(
								'fixed' => __( 'Fixed amount', 'wp-freeio' ),
								'percentage' => __( 'Percentage', 'wp-freeio' ),
							),
						),
						array(
							'name'    => __( 'Select range', 'wp-freeio' ),
							'id'      => 'range',
							'type'    => 'select',
							'options' => WP_Freeio_Mixes::get_default_comissions_tiers_range(),
						),
	                    array(
	                        'name'      => __( 'Amount', 'wp-freeio' ),
	                        'id'        => 'amount',
	                        'type'      => 'text',
	                        'default'      => '20',
	                        'attributes' => array(
								'type'              => 'number',
			                    'min'               => 0,
			                    'pattern'           => '\d*',
							),
	                    ),
	                ),
	            ),

				array(
					'name' => __( 'Service Commission settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_freelancer_settings_service_commission_freelancer',
					'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">',
				),
				array(
					'name'    => __( 'Service commission fee', 'wp-freeio' ),
					'id'      => 'freelancers_service_commission_fee',
					'type'    => 'select',
					'options' => apply_filters( 'wp-freeio-commission-type', array(
						'none' => __( 'None', 'wp-freeio' ),
						'fixed' => __( 'Fixed amount', 'wp-freeio' ),
						'percentage' => __( 'Percentage', 'wp-freeio' ),
						'comissions_tiers' => __( 'Comissions tiers', 'wp-freeio' ),
					)),
					'default' => 'none',
				),
				array(
					'name'    => __( 'Fixed amount', 'wp-freeio' ),
					'id'      => 'freelancers_service_commission_fixed_amount',
					'type'    => 'text',
					'default' => '10',
					'attributes' => array(
						'data-conditional-id' => 'freelancers_service_commission_fee',
						'data-conditional-value' => 'fixed',
						'type'              => 'number',
	                    'min'               => 0,
	                    'pattern'           => '\d*',
					),
				),
				array(
	                'name'              => __( 'Comissions tiers', 'wp-freeio' ),
	                'id'                => 'freelancers_service_comissions_tiers',
	                'type'              => 'group',
	                'options'           => array(
	                    'group_title'       => __( 'Tier {#}', 'wp-freeio' ),
	                    'add_button'        => __( 'Add Another Tier', 'wp-freeio' ),
	                    'remove_button'     => __( 'Remove Tier', 'wp-freeio' ),
	                    'sortable'          => false,
	                    'closed'         => true,
	                ),
	                'fields'            => array(
	                    array(
							'name'    => __( 'Type', 'wp-freeio' ),
							'id'      => 'type',
							'type'    => 'select',
							'options' => array(
								'fixed' => __( 'Fixed amount', 'wp-freeio' ),
								'percentage' => __( 'Percentage', 'wp-freeio' ),
							),
						),
						array(
							'name'    => __( 'Select range', 'wp-freeio' ),
							'id'      => 'range',
							'type'    => 'select',
							'options' => WP_Freeio_Mixes::get_default_comissions_tiers_range(),
						),
	                    array(
	                        'name'      => __( 'Amount', 'wp-freeio' ),
	                        'id'        => 'amount',
	                        'type'      => 'text',
	                        'default'      => '20',
	                        'attributes' => array(
								'type'              => 'number',
			                    'min'               => 0,
			                    'pattern'           => '\d*',
							),
	                    ),
	                ),
	            ),
				array(
					'name'    => __( 'Percentage', 'wp-freeio' ),
					'id'      => 'freelancers_service_commission_percentage',
					'type'    => 'text',
					'default' => '20',
					'attributes' => array(
						'data-conditional-id' => 'freelancers_service_commission_fee',
						'data-conditional-value' => 'percentage',
						'type'              => 'number',
	                    'min'               => 0,
	                    'pattern'           => '\d*',
					),
					'after_row' => '</div></div></div>'
				),
			), $pages )
		);
		
		// Payment Settings
		$wp_freeio_settings['payment_settings'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Payment Settings', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_payment_settings', array(
				
				array(
					'name' => __( 'Payout settings', 'wp-freeio' ),
					'type' => 'wp_freeio_title',
					'id'   => 'wp_freeio_title_payment_settings_title',
					'before_row' => '<div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
					'after_row' => '<div class="before-group-row-inner-content">',
				),

				array(
					'name'    => __( 'Minimum withdraw amount', 'wp-freeio' ),
					'desc'    => __( 'Add minimum amount to process wallet.', 'wp-freeio' ),
					'id'      => 'minimum_withdraw_amount',
					'type'    => 'text',
					'default' => '50',
				),

				array(
					'name'    => __( 'Payout method', 'wp-freeio' ),
					'id'      => 'withdraw_payout_methods',
					'type'    => 'multicheck',
					'options' => WP_Freeio_Mixes::get_default_withdraw_payout_methods(),
					'select_all_button' => true,
					'default' => array('paypal', 'bacs', 'payoneer'),
				),

				array(
					'name'    => __( 'Bank transfer fields', 'wp-freeio' ),
					'id'      => 'bank_transfer_fields',
					'type'    => 'multicheck',
					'options' => WP_Freeio_Mixes::get_default_bank_transfer_fields(),
					'select_all_button' => true,
					'default' => array('bank_account_name', 'bank_account_number', 'bank_name', 'bank_routing_number', 'bank_iban', 'bank_bic_swift'),
					'after_row' => '</div></div></div>'
				),
			))
		);
		// ReCaaptcha
		$wp_freeio_settings['api_settings'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Social API', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_api_settings', array(
					// Facebook
					array(
						'name' => __( 'Facebook API settings', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_api_settings_facebook_title',
						'before_row' => '<div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">',
						'desc' => sprintf(__('Callback URL is: %s', 'wp-freeio'), admin_url('admin-ajax.php?action=wp_freeio_facebook_login')),
					),
					array(
						'name'            => __( 'App ID', 'wp-freeio' ),
						'desc'            => __( 'Please enter App ID of your Facebook account.', 'wp-freeio' ),
						'id'              => 'facebook_api_app_id',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'App Secret', 'wp-freeio' ),
						'desc'            => __( 'Please enter App Secret of your Facebook account.', 'wp-freeio' ),
						'id'              => 'facebook_api_app_secret',
						'type'            => 'text',
					),
					array(
						'name'    => __( 'Enable Facebook Login', 'wp-freeio' ),
						'id'      => 'enable_facebook_login',
						'type'    => 'checkbox',
					),
					array(
						'name'    => __( 'Enable Facebook Apply', 'wp-freeio' ),
						'id'      => 'enable_facebook_apply',
						'type'    => 'checkbox',
					),

					// Linkedin
					array(
						'name' => __( 'Linkedin API settings', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_api_settings_linkedin_title',
						'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">',
						'desc' => sprintf(__('Callback URL is: %s', 'wp-freeio'), home_url('/')),
					),
					array(
						'name'    => __( 'Linkedin Login Type', 'wp-freeio' ),
						'id'      => 'linkedin_login_type',
						'type'    => 'radio',
						'options' => array(
							'' => 'OAuth',
							'openid' => 'OpenID',
						),
						'default' => '',
					),
					array(
						'name'            => __( 'Client ID', 'wp-freeio' ),
						'desc'            => __( 'Please enter Client ID of your linkedin app.', 'wp-freeio' ),
						'id'              => 'linkedin_api_client_id',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Client Secret', 'wp-freeio' ),
						'desc'            => __( 'Please enter Client Secret of your linkedin app.', 'wp-freeio' ),
						'id'              => 'linkedin_api_client_secret',
						'type'            => 'text',
					),
					array(
						'name'    => __( 'Enable Linkedin Login', 'wp-freeio' ),
						'id'      => 'enable_linkedin_login',
						'type'    => 'checkbox',
					),
					array(
						'name'    => __( 'Enable Linkedin Apply', 'wp-freeio' ),
						'id'      => 'enable_linkedin_apply',
						'type'    => 'checkbox',
					),

					// Twitter
					array(
						'name' => __( 'Twitter API settings', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_api_settings_twitter_title',
						'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">',
						'desc' => sprintf(__('Callback URL is: %s', 'wp-freeio'), home_url('/')),
					),
					array(
						'name'            => __( 'Consumer Key', 'wp-freeio' ),
						'desc'            => __( 'Set Consumer Key for twitter.', 'wp-freeio' ),
						'id'              => 'twitter_api_consumer_key',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Consumer Secret', 'wp-freeio' ),
						'desc'            => __( 'Set Consumer Secret for twitter.', 'wp-freeio' ),
						'id'              => 'twitter_api_consumer_secret',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Access Token', 'wp-freeio' ),
						'desc'            => __( 'Set Access Token for twitter.', 'wp-freeio' ),
						'id'              => 'twitter_api_access_token',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Token Secret', 'wp-freeio' ),
						'desc'            => __( 'Set Token Secret for twitter.', 'wp-freeio' ),
						'id'              => 'twitter_api_token_secret',
						'type'            => 'text',
					),
					array(
						'name'    => __( 'Enable Twitter Login', 'wp-freeio' ),
						'id'      => 'enable_twitter_login',
						'type'    => 'checkbox',
					),
					array(
						'name'    => __( 'Enable Twitter Apply', 'wp-freeio' ),
						'id'      => 'enable_twitter_apply',
						'type'    => 'checkbox',
					),

					// Google API
					array(
						'name' => __( 'Google API settings', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_api_settings_google_title',
						'before_row' => '</div></div></div> <div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">',
						'desc' => sprintf(__('Callback URL is: %s', 'wp-freeio'), home_url('/')),
					),
					array(
						'name'            => __( 'Client ID', 'wp-freeio' ),
						'desc'            => __( 'Please enter Client ID of your Google account.', 'wp-freeio' ),
						'id'              => 'google_api_client_id',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Client Secret', 'wp-freeio' ),
						'desc'            => __( 'Please enter Client secret of your Google account.', 'wp-freeio' ),
						'id'              => 'google_api_client_secret',
						'type'            => 'text',
					),
					array(
						'name'    => __( 'Enable Google Login', 'wp-freeio' ),
						'id'      => 'enable_google_login',
						'type'    => 'checkbox',
					),
					array(
						'name'    => __( 'Enable Google Apply', 'wp-freeio' ),
						'id'      => 'enable_google_apply',
						'type'    => 'checkbox',
						'after_row' => '</div></div></div>'
					),
				)
			)		 
		);

		// ReCaaptcha
		$wp_freeio_settings['recaptcha_api_settings'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'reCAPTCHA API', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_recaptcha_api_settings', array(
					
					// Google Recaptcha
					array(
						'name' => __( 'Google reCAPTCHA API (V2) Settings', 'wp-freeio' ),
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_api_settings_google_recaptcha',
						'before_row' => '<div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">',
						'desc' => __('The plugin use ReCaptcha v2', 'wp-freeio'),
					),
					array(
						'name'            => __( 'Site Key', 'wp-freeio' ),
						'desc'            => __( 'You can retrieve your site key from <a href="https://www.google.com/recaptcha/admin#list">Google\'s reCAPTCHA admin dashboard.</a>', 'wp-freeio' ),
						'id'              => 'recaptcha_site_key',
						'type'            => 'text',
					),
					array(
						'name'            => __( 'Secret Key', 'wp-freeio' ),
						'desc'            => __( 'You can retrieve your secret key from <a href="https://www.google.com/recaptcha/admin#list">Google\'s reCAPTCHA admin dashboard.</a>', 'wp-freeio' ),
						'id'              => 'recaptcha_secret_key',
						'type'            => 'text',
						'after_row' => '</div></div></div>'
					),
				)
			)		 
		);

		// Email Notification
		$wp_freeio_settings['email_notification'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Email Notification', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_email_notification', array(
					// Project
					array(
						'name' => __( 'Project', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_email_project',
						'before_row' => '<div id="heading-general-title" class="before-group-row before-group-row-0 active"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),

					array(
						'name'    => __( 'Admin Notice of New Project', 'wp-freeio' ),
						'id'      => 'admin_notice_add_new_project',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a new project is submitted on the frontend.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_add_new_project', 'subject') ),
						'id'      => 'admin_notice_add_new_project_subject',
						'type'    => 'text',
						'default' => 'New Project Found',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_add_new_project', 'content') ),
						'id'      => 'admin_notice_add_new_project_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_add_new_project'),
					),

					array(
						'name'    => __( 'Admin Notice of Updated Project', 'wp-freeio' ),
						'id'      => 'admin_notice_updated_project',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a project is updated on the frontend.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_updated_project', 'subject') ),
						'id'      => 'admin_notice_updated_project_subject',
						'type'    => 'text',
						'default' => 'A Project Updated',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_updated_project', 'content') ),
						'id'      => 'admin_notice_updated_project_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_updated_project'),
					),

					
					array(
						'name'    => __( 'Admin Notice of Expiring Project', 'wp-freeio' ),
						'id'      => 'admin_notice_expiring_project',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send notices to the site administrator before a project listing expires.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Notice Period', 'wp-freeio' ),
						'desc'    => __( 'days', 'wp-freeio' ),
						'id'      => 'admin_notice_expiring_project_days',
						'type'    => 'text_small',
						'default' => '1',
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_expiring_project', 'subject') ),
						'id'      => 'admin_notice_expiring_project_subject',
						'type'    => 'text',
						'default' => 'Project Expiring: {{project_title}}',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_expiring_project', 'content') ),
						'id'      => 'admin_notice_expiring_project_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_expiring_project'),
					),
					array(
						'name'    => __( 'Employer Notice of Expiring Project', 'wp-freeio' ),
						'id'      => 'employer_notice_expiring_project',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send notices to employers before a project listing expires.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Notice Period', 'wp-freeio' ),
						'desc'    => __( 'days', 'wp-freeio' ),
						'id'      => 'employer_notice_expiring_project_days',
						'type'    => 'text_small',
						'default' => '1',
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('employer_notice_expiring_project', 'subject') ),
						'id'      => 'employer_notice_expiring_project_subject',
						'type'    => 'text',
						'default' => 'Project Expiring: {{project_title}}',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('employer_notice_expiring_project', 'content') ),
						'id'      => 'employer_notice_expiring_project_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('employer_notice_expiring_project'),
					),
					
					// Send Proposal
					array(
						'name'    => __( 'Send Email to Employer When Project got a proposal', 'wp-freeio' ),
						'id'      => 'employer_notice_add_new_proposal',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site employers when a new proposal is submitted on the frontend.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('send_proposal_notice', 'subject') ),
						'id'      => 'send_proposal_notice_subject',
						'type'    => 'text',
						'default' => 'You Got a New Proposal',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('send_proposal_notice', 'content') ),
						'id'      => 'send_proposal_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('send_proposal_notice'),
					),
					// Hired Proposal Freelancer
					array(
						'name'    => __( 'Send Email to Freelancer When Project is Assigned', 'wp-freeio' ),
						'id'      => 'freelancer_notice_add_hired_proposal',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when a project is assigned to freelancer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_proposal_notice', 'subject') ),
						'id'      => 'hired_proposal_notice_subject',
						'type'    => 'text',
						'default' => 'Congratulations! You Got a New Project',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_proposal_notice', 'content') ),
						'id'      => 'hired_proposal_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('hired_proposal_notice'),
					),
					// Hired Proposal Employer
					array(
						'name'    => __( 'Send Email to Employer When Project is Assigned', 'wp-freeio' ),
						'id'      => 'employer_notice_add_hired_proposal',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when a project is assigned to Employer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_proposal_employer_notice', 'subject') ),
						'id'      => 'hired_proposal_employer_notice_subject',
						'type'    => 'text',
						'default' => 'You Have Assigned a Project',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_proposal_employer_notice', 'content') ),
						'id'      => 'hired_proposal_employer_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('hired_proposal_employer_notice'),
					),

					// Completed Project Freelancer
					array(
						'name'    => __( 'Send Email to Freelancer When Project is Completed', 'wp-freeio' ),
						'id'      => 'freelancer_notice_add_completed_project',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when a project is completed to Freelancer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('completed_project_notice', 'subject') ),
						'id'      => 'completed_project_notice_subject',
						'type'    => 'text',
						'default' => 'You Have Completed a Project',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('completed_project_notice', 'content') ),
						'id'      => 'completed_project_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('completed_project_notice'),
					),
					// Completed Project Employer
					array(
						'name'    => __( 'Send Email to Employer When Project is Completed', 'wp-freeio' ),
						'id'      => 'employer_notice_add_completed_project',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when a project is completed to Employer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('completed_project_employer_notice', 'subject') ),
						'id'      => 'completed_project_employer_notice_subject',
						'type'    => 'text',
						'default' => 'Your project has been marked as completed',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('completed_project_employer_notice', 'content') ),
						'id'      => 'completed_project_employer_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('completed_project_employer_notice'),
					),
					// Cancelled Project Freelancer
					array(
						'name'    => __( 'Send Email to Freelancer When Project is Cancelled', 'wp-freeio' ),
						'id'      => 'freelancer_notice_add_cancelled_project',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when a project is cancelled to Freelancer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('cancelled_project_notice', 'subject') ),
						'id'      => 'cancelled_project_notice_subject',
						'type'    => 'text',
						'default' => 'Project Canceled',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('cancelled_project_notice', 'content') ),
						'id'      => 'cancelled_project_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('cancelled_project_notice'),
					),
					// Cancelled Project Employer
					array(
						'name'    => __( 'Send Email to Employer When Project is Cancelled', 'wp-freeio' ),
						'id'      => 'employer_notice_add_cancelled_project',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when a project is cancelled to Employer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('cancelled_project_employer_notice', 'subject') ),
						'id'      => 'cancelled_project_employer_notice_subject',
						'type'    => 'text',
						'default' => 'Your project has been canceled',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('cancelled_project_employer_notice', 'content') ),
						'id'      => 'cancelled_project_employer_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('cancelled_project_employer_notice'),
					),
					// Hired Project Message
					array(
						'name'    => __( 'Send Email to user When project message is posted', 'wp-freeio' ),
						'id'      => 'user_notice_hired_project_message',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site freelancers when a completed project.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_project_message_notice', 'subject') ),
						'id'      => 'hired_project_message_notice_subject',
						'type'    => 'text',
						'default' => 'A new message',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_project_message_notice', 'content') ),
						'id'      => 'hired_project_message_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('hired_project_message_notice'),
					),


					// Invite Freelancer
					array(
						'name'    => __( 'Send Email to freelancer When a inviting apply project posted', 'wp-freeio' ),
						'id'      => 'user_notice_add_invite_freelancer',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site users when a Invite Freelancer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('invite_freelancer_notice', 'subject') ),
						'id'      => 'invite_freelancer_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Invite Freelancer: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('invite_freelancer_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('invite_freelancer_notice', 'content') ),
						'id'      => 'invite_freelancer_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('invite_freelancer_notice'),
					),


					// Service
					array(
						'name' => __( 'Service', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_email_service',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_email_service" class="before-group-row before-group-row-1"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Admin Notice of New Service', 'wp-freeio' ),
						'id'      => 'admin_notice_add_new_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a new service is submitted on the frontend.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_add_new_service', 'subject') ),
						'id'      => 'admin_notice_add_new_service_subject',
						'type'    => 'text',
						'default' => 'New Service Found',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_add_new_service', 'content') ),
						'id'      => 'admin_notice_add_new_service_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_add_new_service'),
					),

					array(
						'name'    => __( 'Admin Notice of Updated Service', 'wp-freeio' ),
						'id'      => 'admin_notice_updated_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a service is updated on the frontend.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_updated_service', 'subject') ),
						'id'      => 'admin_notice_updated_service_subject',
						'type'    => 'text',
						'default' => 'A Service Updated',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_updated_service', 'content') ),
						'id'      => 'admin_notice_updated_service_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_updated_service'),
					),

					
					array(
						'name'    => __( 'Admin Notice of Expiring Service', 'wp-freeio' ),
						'id'      => 'admin_notice_expiring_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send notices to the site administrator before a service listing expires.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Notice Period', 'wp-freeio' ),
						'desc'    => __( 'days', 'wp-freeio' ),
						'id'      => 'admin_notice_expiring_service_days',
						'type'    => 'text_small',
						'default' => '1',
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_expiring_service', 'subject') ),
						'id'      => 'admin_notice_expiring_service_subject',
						'type'    => 'text',
						'default' => 'Service Expiring: {{service_title}}',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_expiring_service', 'content') ),
						'id'      => 'admin_notice_expiring_service_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_expiring_service'),
					),
					array(
						'name'    => __( 'Freelancer Notice of Expiring Service', 'wp-freeio' ),
						'id'      => 'freelancer_notice_expiring_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send notices to freelancer before a service listing expires.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Notice Period', 'wp-freeio' ),
						'desc'    => __( 'days', 'wp-freeio' ),
						'id'      => 'freelancer_notice_expiring_service_days',
						'type'    => 'text_small',
						'default' => '1',
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('freelancer_notice_expiring_service', 'subject') ),
						'id'      => 'freelancer_notice_expiring_service_subject',
						'type'    => 'text',
						'default' => 'Service Expiring: {{service_title}}',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('freelancer_notice_expiring_service', 'content') ),
						'id'      => 'freelancer_notice_expiring_service_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('freelancer_notice_expiring_service'),
					),


					// Hired Service Freelancer
					array(
						'name'    => __( 'Send Email to Freelancer When Order is received', 'wp-freeio' ),
						'id'      => 'freelancer_notice_add_hired_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when a service receive an order.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_service_notice', 'subject') ),
						'id'      => 'hired_service_notice_subject',
						'type'    => 'text',
						'default' => 'Congratulations! You Got a New Order',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_service_notice', 'content') ),
						'id'      => 'hired_service_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('hired_service_notice'),
					),
					// Hired Service Employer
					array(
						'name'    => __( 'Send Email to Employer When Order is created', 'wp-freeio' ),
						'id'      => 'employer_notice_add_hired_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when an Employer purchase order.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_service_employer_notice', 'subject') ),
						'id'      => 'hired_service_employer_notice_subject',
						'type'    => 'text',
						'default' => 'You have placed an order',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_service_employer_notice', 'content') ),
						'id'      => 'hired_service_employer_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('hired_service_employer_notice'),
					),
					// Completed Service Freelancer
					array(
						'name'    => __( 'Send Email to Freelancer When order is completed', 'wp-freeio' ),
						'id'      => 'freelancer_notice_add_completed_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when order is completed to freelancer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('completed_service_notice', 'subject') ),
						'id'      => 'completed_service_notice_subject',
						'type'    => 'text',
						'default' => 'You have completed order',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('completed_service_notice', 'content') ),
						'id'      => 'completed_service_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('completed_service_notice'),
					),
					// Completed Service Employer
					array(
						'name'    => __( 'Send email to employer When order is completed', 'wp-freeio' ),
						'id'      => 'employer_notice_add_completed_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when order is completed to employer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('completed_service_employer_notice', 'subject') ),
						'id'      => 'completed_service_employer_notice_subject',
						'type'    => 'text',
						'default' => 'Your order has been marked as completed',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('completed_service_employer_notice', 'content') ),
						'id'      => 'completed_service_employer_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('completed_service_employer_notice'),
					),

					// Cancelled Service Freelancer
					array(
						'name'    => __( 'Send email to freelancer when order is canceled', 'wp-freeio' ),
						'id'      => 'freelancer_notice_add_cancelled_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email when order is canceled to freelancer.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('cancelled_service_notice', 'subject') ),
						'id'      => 'cancelled_service_notice_subject',
						'type'    => 'text',
						'default' => 'Order Canceled',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('cancelled_service_notice', 'content') ),
						'id'      => 'cancelled_service_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('cancelled_service_notice'),
					),
					// Cancelled Service Employer
					array(
						'name'    => __( 'Send email to employer when order is canceled', 'wp-freeio' ),
						'id'      => 'employer_notice_add_cancelled_service',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email to employer when a order is canceled.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('cancelled_service_employer_notice', 'subject') ),
						'id'      => 'cancelled_service_employer_notice_subject',
						'type'    => 'text',
						'default' => 'Your Order has been canceled',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('cancelled_service_employer_notice', 'content') ),
						'id'      => 'cancelled_service_employer_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('cancelled_service_employer_notice'),
					),

					// Hired Service Message
					array(
						'name'    => __( 'Send email to user when service message is sent', 'wp-freeio' ),
						'id'      => 'user_notice_hired_service_message',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send email to user when a order message is sent.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Service Message Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_service_message_notice', 'subject') ),
						'id'      => 'hired_service_message_notice_subject',
						'type'    => 'text',
						'default' => 'A new message',
					),
					array(
						'name'    => __( 'Service Message Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('hired_service_message_notice', 'content') ),
						'id'      => 'hired_service_message_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('hired_service_message_notice'),
					),



					// Job
					array(
						'name' => __( 'Job Listing', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_email_job_listing',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_email_job_listing" class="before-group-row before-group-row-2"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Admin Notice of New Job', 'wp-freeio' ),
						'id'      => 'admin_notice_add_new_job',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a new job is submitted on the frontend.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_add_new_job', 'subject') ),
						'id'      => 'admin_notice_add_new_job_subject',
						'type'    => 'text',
						'default' => 'New Job Found',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_add_new_job', 'content') ),
						'id'      => 'admin_notice_add_new_job_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_add_new_job'),
					),

					
					array(
						'name'    => __( 'Admin Notice of Updated Job', 'wp-freeio' ),
						'id'      => 'admin_notice_updated_job',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site administrator when a job is updated on the frontend.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_updated_job', 'subject') ),
						'id'      => 'admin_notice_updated_job_subject',
						'type'    => 'text',
						'default' => 'A Job Updated',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_updated_job', 'content') ),
						'id'      => 'admin_notice_updated_job_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_updated_job'),
					),

					
					array(
						'name'    => __( 'Admin Notice of Expiring Job', 'wp-freeio' ),
						'id'      => 'admin_notice_expiring_job',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send notices to the site administrator before a job listing expires.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Notice Period', 'wp-freeio' ),
						'desc'    => __( 'days', 'wp-freeio' ),
						'id'      => 'admin_notice_expiring_job_days',
						'type'    => 'text_small',
						'default' => '1',
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_expiring_job', 'subject') ),
						'id'      => 'admin_notice_expiring_job_subject',
						'type'    => 'text',
						'default' => 'Job Expiring: {{job_title}}',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_expiring_job', 'content') ),
						'id'      => 'admin_notice_expiring_job_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_expiring_job'),
					),

					
					array(
						'name'    => __( 'Employer Notice of Expiring Job', 'wp-freeio' ),
						'id'      => 'employer_notice_expiring_job',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send notices to employers before a job listing expires.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Notice Period', 'wp-freeio' ),
						'desc'    => __( 'days', 'wp-freeio' ),
						'id'      => 'employer_notice_expiring_job_days',
						'type'    => 'text_small',
						'default' => '1',
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('employer_notice_expiring_job', 'subject') ),
						'id'      => 'employer_notice_expiring_job_subject',
						'type'    => 'text',
						'default' => 'Job Expiring: {{job_title}}',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('employer_notice_expiring_job', 'content') ),
						'id'      => 'employer_notice_expiring_job_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('employer_notice_expiring_job'),
					),

					
					// Job Alert
					array(
						'name'    => __( 'Send a email for freelancer when job alert', 'wp-freeio' ),
						'id'      => 'freelancer_notice_job_alert',
						'type'    => 'title',
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('job_alert_notice', 'subject') ),
						'id'      => 'job_alert_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Job Alert: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('job_alert_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('job_alert_notice', 'content') ),
						'id'      => 'job_alert_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('job_alert_notice'),
					),

					// Email Apply
					array(
						'name'    => __( 'Send a email to employer when a new email apply sent', 'wp-freeio' ),
						'id'      => 'employer_notice_add_new_email_apply',
						'type'    => 'title',
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('email_apply_job_notice', 'subject') ),
						'id'      => 'email_apply_job_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Apply Job: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('email_apply_job_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('email_apply_job_notice', 'content') ),
						'id'      => 'email_apply_job_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('email_apply_job_notice'),
					),

					// Internal Apply
					array(
						'name'    => __( 'Send a email to employer when a new internal apply sent', 'wp-freeio' ),
						'id'      => 'employer_notice_add_new_internal_apply',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email when a new internal apply sent.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('internal_apply_job_notice', 'subject') ),
						'id'      => 'internal_apply_job_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Apply Job: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('internal_apply_job_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('internal_apply_job_notice', 'content') ),
						'id'      => 'internal_apply_job_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('internal_apply_job_notice'),
					),

					// Applied Thanks
					array(
						'name'    => __( 'Send a email to freelancer when a new application sent', 'wp-freeio' ),
						'id'      => 'freelancer_notice_add_thanks_apply',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a emai to freelancer when a new application sent.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('applied_job_thanks_notice', 'subject') ),
						'id'      => 'applied_job_thanks_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Thanks for applying the job: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('applied_job_thanks_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('applied_job_thanks_notice', 'content') ),
						'id'      => 'applied_job_thanks_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('applied_job_thanks_notice'),
					),

					// Create Meeting
					array(
						'name'    => __( 'Send a email to freelancer when a new meetings posted', 'wp-freeio' ),
						'id'      => 'user_notice_add_new_meeting',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site users when a new meetings.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('meeting_create', 'subject') ),
						'id'      => 'meeting_create_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Apply Job: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('meeting_create', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('meeting_create', 'content') ),
						'id'      => 'meeting_create_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('meeting_create'),
					),

					// Re-schedule Meeting
					array(
						'name'    => __( 'Send a email to user when a Re-schedule meetings posted', 'wp-freeio' ),
						'id'      => 'user_notice_add_reschedule_meeting',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Send a notice to the site users when a Re-schedule meetings.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('meeting_reschedule', 'subject') ),
						'id'      => 'meeting_reschedule_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Apply Job: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('meeting_reschedule', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('meeting_reschedule', 'content') ),
						'id'      => 'meeting_reschedule_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('meeting_reschedule'),
					),

					// Reject interview
					array(
						'name'    => __( 'Send a email to freelancer when his application rejected', 'wp-freeio' ),
						'id'      => 'user_notice_add_reject_interview',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to freelancer when his application rejected.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('reject_interview_notice', 'subject') ),
						'id'      => 'reject_interview_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Reject interview: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('reject_interview_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('reject_interview_notice', 'content') ),
						'id'      => 'reject_interview_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('reject_interview_notice'),
					),

					// Undo Reject interview
					array(
						'name'    => __( 'Send a email to freelancer when his application undo-rejected', 'wp-freeio' ),
						'id'      => 'user_notice_add_undo_reject_interview',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to freelancer when his application undo-rejected.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('undo_reject_interview_notice', 'subject') ),
						'id'      => 'undo_reject_interview_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Undo-Reject interview: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('undo_reject_interview_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('undo_reject_interview_notice', 'content') ),
						'id'      => 'undo_reject_interview_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('undo_reject_interview_notice'),
					),

					// Approve interview
					array(
						'name'    => __( 'Send a email to freelancer When his application Approve', 'wp-freeio' ),
						'id'      => 'user_notice_add_approve_interview',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to freelancer When his application Approve.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('approve_interview_notice', 'subject') ),
						'id'      => 'approve_interview_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Approve interview: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('approve_interview_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('approve_interview_notice', 'content') ),
						'id'      => 'approve_interview_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('approve_interview_notice'),
					),

					// Undo Approve interview
					array(
						'name'    => __( 'Send a email to freelancer When his application Undo-approve', 'wp-freeio' ),
						'id'      => 'user_notice_add_undo_approve_interview',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to freelancer When his application Undo-approve.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('undo_approve_interview_notice', 'subject') ),
						'id'      => 'undo_approve_interview_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Undo-approve interview: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('undo_approve_interview_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('undo_approve_interview_notice', 'content') ),
						'id'      => 'undo_approve_interview_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('undo_approve_interview_notice'),
					),

					// Freelancer Alert
					array(
						'name' => __( 'Freelancer Alert', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_freelancer_alert',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_freelancer_alert" class="before-group-row before-group-row-5"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('freelancer_alert_notice', 'subject') ),
						'id'      => 'freelancer_alert_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Freelancer Alert: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('freelancer_alert_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('freelancer_alert_notice', 'content') ),
						'id'      => 'freelancer_alert_notice_content',
						'type'    => 'wysiwyg',
						'default' =>  WP_Freeio_Email::get_email_default_content('freelancer_alert_notice'),
					),

					


					// contact form
					array(
						'name' => __( 'Contact Form', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_contact_form',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_contact_form" class="before-group-row before-group-row-12"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('contact_form_notice', 'subject') ),
						'id'      => 'contact_form_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Contact Form: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('contact_form_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('contact_form_notice', 'content') ),
						'id'      => 'contact_form_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('contact_form_notice'),
					),

					// Report
					array(
						'name' => __( 'Report', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_report',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_report" class="before-group-row before-group-row-12"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Send a email to administrator when user report post', 'wp-freeio' ),
						'id'      => 'admin_notice_user_report',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to administrator when user report post.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('report_notice', 'subject') ),
						'id'      => 'report_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Report: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('report_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('report_notice', 'content') ),
						'id'      => 'report_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('report_notice'),
					),
					// Withdraw
					array(
						'name' => __( 'Withdraw', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_withdraw',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_withdraw" class="before-group-row before-group-row-12"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Send a email to administrator when user withdraw the money', 'wp-freeio' ),
						'id'      => 'admin_notice_user_withdraw_money',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to administrator when user withdraw the money.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_freelancer_withdraw', 'subject') ),
						'id'      => 'admin_notice_freelancer_withdraw_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'A new withdraw: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_freelancer_withdraw', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_freelancer_withdraw', 'content') ),
						'id'      => 'admin_notice_freelancer_withdraw_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_freelancer_withdraw'),
					),
					// Dispute
					array(
						'name' => __( 'Dispute', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_dispute',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_created_dispute_notice" class="before-group-row before-group-row-17"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Send a email to user when he received a dispute', 'wp-freeio' ),
						'id'      => 'user_notice_add_new_dispute',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to user when he received a dispute.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('created_dispute_notice', 'subject') ),
						'id'      => 'created_dispute_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'New dispute: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('created_dispute_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('created_dispute_notice', 'content') ),
						'id'      => 'created_dispute_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('created_dispute_notice'),
					),

					array(
						'name'    => __( 'Send a email to administrator when he received a dispute', 'wp-freeio' ),
						'id'      => 'admin_notice_add_new_dispute',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to administrator when he received a dispute.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('created_dispute_admin_notice', 'subject') ),
						'id'      => 'created_dispute_admin_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'New dispute: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('created_dispute_admin_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('created_dispute_admin_notice', 'content') ),
						'id'      => 'created_dispute_admin_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('created_dispute_admin_notice'),
					),

					array(
						'name'    => __( 'Send a email to user when he received a dispute message', 'wp-freeio' ),
						'id'      => 'user_notice_add_new_dispute_message',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to user when he received a dispute message.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('dispute_message_notice', 'subject') ),
						'id'      => 'dispute_message_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'New dispute message: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('dispute_message_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('dispute_message_notice', 'content') ),
						'id'      => 'dispute_message_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('dispute_message_notice'),
					),
					// winner
					array(
						'name'    => __( 'Send a email to winner user when admin process', 'wp-freeio' ),
						'id'      => 'dispute_winner_notice',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to winner user when admin process.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('dispute_user_winner_notice', 'subject') ),
						'id'      => 'dispute_user_winner_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'New dispute: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('dispute_user_winner_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('dispute_user_winner_notice', 'content') ),
						'id'      => 'dispute_user_winner_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('dispute_user_winner_notice'),
					),
					// loser
					array(
						'name'    => __( 'Send a email to loser user when admin process', 'wp-freeio' ),
						'id'      => 'dispute_loser_notice',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to loser user when admin process.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('dispute_user_loser_notice', 'subject') ),
						'id'      => 'dispute_user_loser_notice_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'New dispute: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('dispute_user_loser_notice', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('dispute_user_loser_notice', 'content') ),
						'id'      => 'dispute_user_loser_notice_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('dispute_user_loser_notice'),
					),

					// Verification
					array(
						'name' => __( 'Verification', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_verification',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_created_verification_notice" class="before-group-row before-group-row-17"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Send a email to admin when user sent a verification', 'wp-freeio' ),
						'id'      => 'admin_notice_user_verification',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to admin when user sent a verification.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('created_verification_notice', 'subject') ),
						'id'      => 'admin_notice_user_verification_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'New verification: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_user_verification', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('admin_notice_user_verification', 'content') ),
						'id'      => 'admin_notice_user_verification_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('admin_notice_user_verification'),
					),

					array(
						'name'    => __( 'Send a email to user when his verification approved', 'wp-freeio' ),
						'id'      => 'user_notice_admin_approve_verification',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to user when he received a verification message.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('verification_message_notice', 'subject') ),
						'id'      => 'user_notice_admin_approve_verification_subject',
						'type'    => 'text',
						'default' => 'Your verification approved',
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_notice_admin_approve_verification', 'content') ),
						'id'      => 'user_notice_admin_approve_verification_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('user_notice_admin_approve_verification'),
					),

					// Approve new user register
					array(
						'name' => __( 'Users', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_user_register_auto_approve',
						'before_row' => '</div></div></div> <div id="heading-wp_freeio_title_user_register_auto_approve" class="before-group-row before-group-row-17"><div class="before-group-row-inner">',
						'after_row' => '<div class="before-group-row-inner-content">'
					),
					array(
						'name'    => __( 'Send a email to user when he registered user (auto approve)', 'wp-freeio' ),
						'id'      => 'user_notice_add_new_user_register',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to user when he registered user.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_auto_approve', 'subject') ),
						'id'      => 'user_register_auto_approve_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'New user register: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_auto_approve', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_auto_approve', 'content') ),
						'id'      => 'user_register_auto_approve_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('user_register_auto_approve'),
					),
					// Approve new user register
					array(
						'name' => __( 'Send a email to user|admin When a new user registered (Email Approve, Admin Approve)', 'wp-freeio' ),
						'desc' => '',
						'type' => 'title',
						'id'   => 'wp_freeio_title_user_register_need_approve',
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_need_approve', 'subject') ),
						'id'      => 'user_register_need_approve_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Approve new user register: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_need_approve', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_need_approve', 'content') ),
						'id'      => 'user_register_need_approve_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('user_register_need_approve'),
					),
					// Approved user register
					array(
						'name'    => __( 'Send a email to user When his account approved', 'wp-freeio' ),
						'id'      => 'user_notice_add_approved_user',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to sent a email to user When his account approved.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_approved', 'subject') ),
						'id'      => 'user_register_approved_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Your account approved: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_approved', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_approved', 'content') ),
						'id'      => 'user_register_approved_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('user_register_approved'),
					),
					// Denied user register
					array(
						'name'    => __( 'Send a email to user When his account denied.', 'wp-freeio' ),
						'id'      => 'user_notice_add_denied_user',
						'type'    => 'checkbox',
						'desc' 	=> __( 'Do you want to send a email to user When his account denied.', 'wp-freeio' ),
					),
					array(
						'name'    => __( 'Email Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_denied', 'subject') ),
						'id'      => 'user_register_denied_subject',
						'type'    => 'text',
						'default' => sprintf(__( 'Your account denied: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_denied', 'subject') ),
					),
					array(
						'name'    => __( 'Email Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_register_denied', 'content') ),
						'id'      => 'user_register_denied_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('user_register_denied'),
					),
					// Reset Password
					array(
						'name' => __( 'Reset Password Template', 'wp-freeio' ),
						'desc' => '',
						'type' => 'title',
						'id'   => 'wp_freeio_title_user_reset_password',
					),
					array(
						'name'    => __( 'Reset Password Subject', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email subject. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_reset_password', 'subject') ),
						'id'      => 'user_reset_password_subject',
						'type'    => 'text',
						'default' => 'Your new password',
					),
					array(
						'name'    => __( 'Reset Password Content', 'wp-freeio' ),
						'desc'    => sprintf(__( 'Enter email content. You can add variables: %s', 'wp-freeio' ), WP_Freeio_Email::display_email_vars('user_reset_password', 'content') ),
						'id'      => 'user_reset_password_content',
						'type'    => 'wysiwyg',
						'default' => WP_Freeio_Email::get_email_default_content('user_reset_password'),
						'after_row' => '</div></div></div>'
					),
				)
			)		 
		);

		// Indeed Jobs Import
		$wp_freeio_settings['import_job_integrations'] = array(
			'id'         => 'options_page',
			'wp_freeio_title' => __( 'Import Job Integrations', 'wp-freeio' ),
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->key ) ),
			'fields'     => apply_filters( 'wp_freeio_settings_import_job_integrations', array(
					array(
						'name' => __( 'Indeed Jobs Import', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_indeed_job_import',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name' => __( 'Enable Indeed Jobs Import', 'wp-freeio' ),
						'id'   => 'indeed_job_import_enable',
						'type' => 'checkbox',
					),
					array(
                        'name'    => __( 'Publisher Number', 'wp-freeio' ),
                        'id'      => 'indeed_job_import_number',
                        'type'    => 'text',
                        'desc' => wp_kses(__('Acquire an publisher ID from the <a href="https://www.indeed.com/publisher" target="_blank">https://www.indeed.com/publisher</a>', 'wp-freeio'), array('a' => array('href' => array(), 'target' => array())))
                    ),

					array(
						'name' => __( 'Ziprecruiter Jobs Import', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_ziprecruiter_job_import',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name' => __( 'Enable Ziprecruiter Jobs import', 'wp-freeio' ),
						'id'   => 'ziprecruiter_job_import_enable',
						'type' => 'checkbox',
					),
					array(
                        'name'    => __( 'Ziprecruiter API Key', 'wp-freeio' ),
                        'id'      => 'ziprecruiter_job_import_api',
                        'type'    => 'text',
                        'desc' => wp_kses(__('Acquire an API key from the <a href="https://www.ziprecruiter.com/zipsearch" target="_blank">https://www.ziprecruiter.com/zipsearch</a>', 'wp-freeio'), array('a' => array('href' => array(), 'target' => array())))
                    ),

					array(
						'name' => __( 'CareerJet Jobs Import', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_careerjet_job_import',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name' => __( 'Enable CareerJet Jobs import', 'wp-freeio' ),
						'id'   => 'careerjet_job_import_enable',
						'type' => 'checkbox',
					),
					array(
                        'name'    => __( 'CareerJet AFFID', 'wp-freeio' ),
                        'id'      => 'careerjet_job_import_api',
                        'type'    => 'text',
                        'desc' => wp_kses(__('Acquire an AFFID from the <a href="https://www.careerjet.com/contact-us" target="_blank">https://www.careerjet.com/contact-us</a>', 'wp-freeio'), array('a' => array('href' => array(), 'target' => array())))
                    ),

					array(
						'name' => __( 'CareerBuilder Jobs Import', 'wp-freeio' ),
						'desc' => '',
						'type' => 'wp_freeio_title',
						'id'   => 'wp_freeio_title_careerbuilder_job_import',
						'before_row' => '<hr>',
						'after_row'  => '<hr>'
					),
					array(
						'name' => __( 'Enable CareerBuilder Jobs import', 'wp-freeio' ),
						'id'   => 'careerbuilder_job_import_enable',
						'type' => 'checkbox',
					),
					array(
                        'name'    => __( 'CareerBuilder API Key', 'wp-freeio' ),
                        'id'      => 'careerbuilder_job_import_api',
                        'type'    => 'text',
                        'desc' => wp_kses(__('Acquire an AFFID from the <a href="https://developer.careerbuilder.com/" target="_blank">https://developer.careerbuilder.com/</a>', 'wp-freeio'), array('a' => array('href' => array(), 'target' => array())))
                    ),
				)
			)		 
		);
		//Return all settings array if necessary

		if ( $active_tab === null   ) {  
			return apply_filters( 'wp_freeio_registered_settings', $wp_freeio_settings );
		}

		// Add other tabs and settings fields as needed
		return apply_filters( 'wp_freeio_registered_'.$active_tab.'_settings', isset($wp_freeio_settings[ $active_tab ])?$wp_freeio_settings[ $active_tab ]:array() );

	}

	/**
	 * Show Settings Notices
	 *
	 * @param $object_id
	 * @param $updated
	 * @param $cmb
	 */
	public function settings_notices( $object_id, $updated, $cmb ) {

		//Sanity check
		if ( $object_id !== $this->key ) {
			return;
		}

		if ( did_action( 'cmb2_save_options-page_fields' ) === 1 ) {
			settings_errors( 'wp_freeio-notices' );
		}

		add_settings_error( 'wp_freeio-notices', 'global-settings-updated', __( 'Settings updated.', 'wp-freeio' ), 'updated' );

	}


	/**
	 * Public getter method for retrieving protected/private variables
	 *
	 * @since  1.0
	 *
	 * @param  string $field Field to retrieve
	 *
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {

		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'fields', 'wp_freeio_title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		if ( 'option_metabox' === $field ) {
			return $this->option_metabox();
		}

		throw new Exception( 'Invalid property: ' . $field );
	}


}

// Get it started
$WP_Freeio_Settings = new WP_Freeio_Settings();

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 *
 * @param  string $key Options array key
 *
 * @return mixed        Option value
 */
function wp_freeio_get_option( $key = '', $default = false ) {
	global $wp_freeio_options;

	$wp_freeio_options = wp_freeio_get_settings();
	
	$value = isset( $wp_freeio_options[ $key ] ) ? $wp_freeio_options[ $key ] : $default;
	$value = apply_filters( 'wp_freeio_get_option', $value, $key, $default );

	return apply_filters( 'wp_freeio_get_option_' . $key, $value, $key, $default );
}



/**
 * Get Settings
 *
 * Retrieves all WP_Freeio plugin settings
 *
 * @since 1.0
 * @return array WP_Freeio settings
 */
function wp_freeio_get_settings() {
	return apply_filters( 'wp_freeio_get_settings', get_option( 'wp_freeio_settings' ) );
}


/**
 * WP_Freeio Title
 *
 * Renders custom section titles output; Really only an <hr> because CMB2's output is a bit funky
 *
 * @since 1.0
 *
 * @param       $field_object , $escaped_value, $object_id, $object_type, $field_type_object
 *
 * @return void
 */
function wp_freeio_title_callback( $field_object, $escaped_value, $object_id, $object_type, $field_type_object ) {

	$id                = $field_type_object->field->args['id'];
	$title             = $field_type_object->field->args['name'];
	$field_description = $field_type_object->field->args['desc'];
	if ( $field_description ) {
		echo '<div class="desc">'.$field_description.'</div>';
	}
}

function wp_freeio_hidden_callback( $field_object, $escaped_value, $object_id, $object_type, $field_type_object ) {
	$id                = $field_type_object->field->args['id'];
	$title             = $field_type_object->field->args['name'];
	$field_description = $field_type_object->field->args['desc'];
	echo '<input type="hidden" name="'.$id.'" value="'.$escaped_value.'">';
	if ( $field_type_object->field->args['human_value'] ) {
		echo '<strong>'.$field_type_object->field->args['human_value'].'</strong>';
	}
	if ( $field_description ) {
		echo '<div class="desc">'.$field_description.'</div>';
	}
}

/**
 * Gets a number of posts and displays them as options
 *
 * @param  array $query_args Optional. Overrides defaults.
 * @param  bool  $force      Force the pages to be loaded even if not on settings
 *
 * @see: https://github.com/WebDevStudios/CMB2/wiki/Adding-your-own-field-types
 * @return array An array of options that matches the CMB2 options array
 */
function wp_freeio_cmb2_get_post_options( $query_args, $force = false ) {

	$post_options = array( '' => '' ); // Blank option

	if ( ( ! isset( $_GET['page'] ) || 'freelancer-settings' != $_GET['page'] ) && ! $force ) {
		return $post_options;
	}

	$args = wp_parse_args( $query_args, array(
		'post_type'   => 'page',
		'numberposts' => 10,
	) );

	$posts = get_posts( $args );

	if ( $posts ) {
		foreach ( $posts as $post ) {

			$post_options[ $post->ID ] = $post->post_title;

		}
	}

	return $post_options;
}


/**
 * Modify CMB2 Default Form Output
 *
 * @param string @args
 *
 * @since 1.0
 */

add_filter( 'cmb2_get_metabox_form_format', 'wp_freeio_modify_cmb2_form_output', 10, 3 );

function wp_freeio_modify_cmb2_form_output( $form_format, $object_id, $cmb ) {

	//only modify the wp_freeio settings form
	if ( 'wp_freeio_settings' == $object_id && 'options_page' == $cmb->cmb_id ) {

		return '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="wp_freeio-submit-wrap"><input type="submit" name="submit-cmb" value="' . __( 'Save Settings', 'wp-freeio' ) . '" class="button-primary"></div></form>';
	}

	return $form_format;

}
