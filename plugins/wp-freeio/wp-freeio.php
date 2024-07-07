<?php
/**
 * Plugin Name: WP Freeio
 * Plugin URI: http://apusthemes.com/wp-freeio/
 * Description: Powerful plugin to create a freelancer on your website.
 * Version: 1.2.11
 * Author: Habq
 * Author URI: http://apusthemes.com/
 * Requires at least: 3.8
 * Tested up to: 6.0
 *
 * Text Domain: wp-freeio
 * Domain Path: /languages/
 *
 * @package wp-freeio
 * @category Plugins
 * @author Habq
 */
if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

if ( !class_exists("WP_Freeio") ) {
	
	final class WP_Freeio {

		private static $instance;

		public static function getInstance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Freeio ) ) {
				self::$instance = new WP_Freeio;
				self::$instance->setup_constants();

				self::$instance->load_textdomain();
				self::$instance->plugin_update();


				add_action( 'activated_plugin', array( self::$instance, 'plugin_order' ) );
				add_action( 'tgmpa_register', array( self::$instance, 'register_plugins' ) );
				add_action( 'widgets_init', array( self::$instance, 'register_widgets' ) );

				self::$instance->libraries();
				self::$instance->includes();
			}

			return self::$instance;
		}

		/**
		 *
		 */
		public function setup_constants(){
			define( 'WP_FREEIO_PLUGIN_VERSION', '1.2.11' );

			define( 'WP_FREEIO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			define( 'WP_FREEIO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

			define('WP_FREEIO_JOB_LISTING_PREFIX', '_job_');
			define('WP_FREEIO_PROJECT_PREFIX', '_project_');
			define('WP_FREEIO_SERVICE_PREFIX', '_service_');

			define('WP_FREEIO_EMPLOYER_PREFIX', '_employer_');
			define('WP_FREEIO_FREELANCER_PREFIX', '_freelancer_');
			define('WP_FREEIO_APPLICANT_PREFIX', '_applicant_');
			define('WP_FREEIO_EARNING_PREFIX', '_earning_');
			define('WP_FREEIO_PROJECT_PROPOSAL_PREFIX', '_project_proposal_');
			define('WP_FREEIO_WITHDRAW_PREFIX', '_withdraw_');
			define('WP_FREEIO_REPORT_PREFIX', '_report_');
			define('WP_FREEIO_VERIFICATION_PREFIX', '_verification_');
			define('WP_FREEIO_DISPUTE_PREFIX', '_dispute_');

			define('WP_FREEIO_JOB_ALERT_PREFIX', '_job_alert_');
			define('WP_FREEIO_SERVICE_ALERT_PREFIX', '_service_alert_');

			define('WP_FREEIO_SERVICE_ORDER_PREFIX', '_service_order_');
			define('WP_FREEIO_SERVICE_ADDON_PREFIX', '_service_addon_');
			define('WP_FREEIO_FREELANCER_ALERT_PREFIX', '_freelancer_alert_');
			define('WP_FREEIO_MEETING_PREFIX', '_meeting_');
		}

		public function includes() {
			global $wp_freeio_options;
			// Admin Settings
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/admin/class-settings.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/admin/class-permalink-settings.php';

			$wp_freeio_options = wp_freeio_get_settings();
			
			// post type
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-service.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-job_listing.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-project.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-employer.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-freelancer.php';
			

			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-job-applicant.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-job-alert.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-freelancer-alert.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-service-addon.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-service-order.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-meeting.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-earnings.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-project-proposal.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-withdraw.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-report.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-dispute.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/post-types/class-post-type-verification.php';
			
			// custom fields
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/custom-fields/class-fields-manager.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/custom-fields/class-custom-fields-html.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/custom-fields/class-custom-fields.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/custom-fields/class-custom-fields-display.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/custom-fields/class-custom-fields-register.php';

			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-job-meta.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-employer-meta.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-freelancer-meta.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-service-meta.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-project-meta.php';

			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-abstract-register-form.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-employer-register-form.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-freelancer-register-form.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-freelancer-register-apply-form.php';


			// taxonomies
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-job-type.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-job-category.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-job-tag.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-location.php';
			// employer taxonomies
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-employer-category.php';
			// require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-employer-location.php';
			// freelancer taxonomies
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-freelancer-category.php';
			// require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-freelancer-location.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-freelancer-tag.php';

			// service taxonomies
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-service-category.php';
			// require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-service-location.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-service-tag.php';

			// project taxonomies
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-project-category.php';
			// require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-project-location.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-project-skill.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-project-duration.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-project-experience.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-project-freelancer-type.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-project-language.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/taxonomies/class-taxonomy-project-level.php';

			//
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-scripts.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-template-loader.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-job_listing.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-project.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-service.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-employer.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-freelancer.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-applicant.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-job-rss-feed.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-price.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-query.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-shortcodes.php';

			// submit job
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-job-abstract-form.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-job-submit-form.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-job-edit-form.php';
			
			// submit service
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-service-abstract-form.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-service-submit-form.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-service-edit-form.php';

			// submit project
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-project-abstract-form.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-project-submit-form.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-project-edit-form.php';

			//
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-user.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-image.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-recaptcha.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-email.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-abstract-filter.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-job-filter.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-employer-filter.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-freelancer-filter.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-service-filter.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-project-filter.php';

			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-review.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-job-alert.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-freelancer-alert.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-service-alert.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-geocode.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-favorite.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-woocommerce.php';
			
			// meeting
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/meetings/class-meeting.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/meetings/class-meeting-zoom.php';

			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-user-notification.php';


			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-ajax.php';

			// social login
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/socials/class-social-facebook.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/socials/class-social-google.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/socials/class-social-linkedin.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/socials/class-social-twitter.php';

			// import indeed jobs
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/import-jobs-integration/class-import-jobs-integration.php';

			// mpdf
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-mpdf.php';

			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-mixes.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-cache-helper.php';

			// 3rd-party
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/3rd-party/class-wpml.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/3rd-party/class-polylang.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/3rd-party/class-all-in-one-seo-pack.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/3rd-party/class-jetpack.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/3rd-party/class-yoast.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/3rd-party/class-all-import.php';

			// google structured data
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-structured-data.php';

			//
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/class-rest-api.php';

			add_action('init', array( __CLASS__, 'register_post_statuses' ) );
			add_action('admin_footer-post.php', array( __CLASS__, 'append_statuses'));
		}

		public function plugin_update() {
	        require_once WP_FREEIO_PLUGIN_DIR . 'libraries/plugin-update-checker/plugin-update-checker.php';
	        Puc_v4_Factory::buildUpdateChecker(
	            'https://www.apusthemes.com/themeplugins/wp-freeio.json',
	            __FILE__,
	            'wp-freeio'
	        );
	    }

	    public static function post_statuses(){
	    	$statuses = array(
				'hired' 		=> esc_html__('Hired','wp-freeio'),
				'completed' 	=> esc_html__('Completed','wp-freeio'),
				'cancelled' 	=> esc_html__('Cancelled','wp-freeio'),
			);
			return apply_filters('wp-freeio-get-proposal-statuses', $statuses);
	    }

		public static function append_statuses(){
			global $post;
			$selected = '';
			$statuses = self::post_statuses();

			if( $post->post_type == 'project' || $post->post_type == 'services-orders' ) {
				ob_start();
			?>
			<script>
				jQuery(document).ready(function($){
				<?php 
				foreach ( $statuses as $key => $value ) {                     
					if( $post->post_status == $key ){
						$selected = 'selected';
					} else {
						$selected = '';
					}
					?>
					jQuery("#post-status-select select#post_status").append("<option value='<?php echo esc_attr( $key ); ?>' <?php if( $post->post_status == $key ){ ?> selected='selected' <?php } ?>><?php echo esc_attr( $value ); ?></option>");
					<?php if( $post->post_status == $key ){ ?>
						jQuery("#post-status-display").append("<?php echo esc_attr( $value ); ?>");
					<?php } ?>
					<?php if( $post->post_status == 'hired' || $post->post_status == 'completed' ){ ?>
						 	jQuery("#publish").val("Update");
							jQuery("#publish").attr("name","save");
							jQuery("#original_publish").val("Update");
					<?php } ?>
					<?php } ?>          
				});
			</script>
			<?php 
				echo ob_get_clean();
			 }
		}

		public static function register_post_statuses() {
			
			
			register_post_status(
				'hired',
				array(
					'label'                     => _x( 'Hired', 'post status', 'wp-freeio' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Hired <span class="count">(%s)</span>', 'Hired <span class="count">(%s)</span>', 'wp-freeio' ),
				)
			);
			register_post_status(
				'completed',
				array(
					'label'                     => _x( 'Completed', 'post status', 'wp-freeio' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'wp-freeio' ),
				)
			);
			register_post_status(
				'cancelled',
				array(
					'label'                     => _x( 'Cancelled', 'post status', 'wp-freeio' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'wp-freeio' ),
				)
			);
			register_post_status(
				'expired',
				array(
					'label'                     => _x( 'Expired', 'post status', 'wp-freeio' ),
					'public'                    => true,
					'protected'                 => true,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'wp-freeio' ),
				)
			);
			register_post_status(
				'pending_approve',
				array(
					'label'                     => _x( 'Pending Approve', 'post status', 'wp-freeio' ),
					'public'                    => false,
					'protected'                 => true,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Pending Approve <span class="count">(%s)</span>', 'Pending Approve <span class="count">(%s)</span>', 'wp-freeio' ),
				)
			);
			register_post_status(
				'preview',
				array(
					'label'                     => _x( 'Preview', 'post status', 'wp-freeio' ),
					'public'                    => false,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Preview <span class="count">(%s)</span>', 'Preview <span class="count">(%s)</span>', 'wp-freeio' ),
				)
			);
			register_post_status(
				'pending_payment',
				array(
					'label'                     => _x( 'Pending Payment', 'post status', 'wp-freeio' ),
					'public'                    => false,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'wp-freeio' ),
				)
			);
			register_post_status(
				'denied',
				array(
					'label'                     => _x( 'Denied', 'post status', 'wp-freeio' ),
					'public'                    => false,
					'exclude_from_search'       => true,
					'show_in_admin_all_list'    => false,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( 'Denied <span class="count">(%s)</span>', 'Denied <span class="count">(%s)</span>', 'wp-freeio' ),
				)
			);
		}
		public static function register_widgets() {
			// widgets
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/widgets/class-widget-job-filter.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/widgets/class-widget-employer-filter.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/widgets/class-widget-freelancer-filter.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/widgets/class-widget-job-alert-form.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'includes/widgets/class-widget-freelancer-alert-form.php';
		}
		/**
		 * Loads third party libraries
		 *
		 * @access public
		 * @return void
		 */
		public static function libraries() {
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2-conditionals/cmb2-conditionals.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_map/cmb-field-map.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_tags/cmb2-field-type-tags.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_file/cmb2-field-type-file.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_attached_user/cmb2-field-type-attached_user.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_profile_url/cmb2-field-type-profile_url.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_image_select/cmb2-field-type-image-select.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb_field_select2/cmb-field-select2.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb_field_taxonomy_select2/cmb-field-taxonomy-select2.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb_field_taxonomy_select2_search/cmb-field-taxonomy-select2-search.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_ajax_search/cmb2-field-ajax-search.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb_field_taxonomy_location/cmb-field-taxonomy-location.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb_field_taxonomy_location_search/cmb-field-taxonomy-location-search.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_message_list/cmb2-field-type-message-list.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2-hide-show-password-field/cmb2-hide-show-password.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_rate_exchange/cmb2-field-type-rate_exchange.php';

			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_datepicker/cmb2-field-type-datepicker.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_datepicker2/cmb2-field-type-datepicker2.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_addons/cmb2-field-type-addons.php';
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2_field_payout_details/cmb2-field-type-payout-details.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/cmb2/cmb2-tabs/plugin.php';
			
			require_once WP_FREEIO_PLUGIN_DIR . 'libraries/class-tgm-plugin-activation.php';
		}

		/**
	     * Loads this plugin first
	     *
	     * @access public
	     * @return void
	     */
	    public static function plugin_order() {
		    $wp_path_to_this_file = preg_replace( '/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR.'/$2', __FILE__ );
		    $this_plugin = plugin_basename( trim( $wp_path_to_this_file ) );
		    $active_plugins = get_option( 'active_plugins' );
		    $this_plugin_key = array_search( $this_plugin, $active_plugins );
			if ( $this_plugin_key ) {
				array_splice( $active_plugins, $this_plugin_key, 1 );
				array_unshift( $active_plugins, $this_plugin );
			    update_option( 'active_plugins', $active_plugins );
		    }
	    }

		/**
		 * Install plugins
		 *
		 * @access public
		 * @return void
		 */
		public static function register_plugins() {
			$plugins = array(
	            array(
		            'name'      => 'CMB2',
		            'slug'      => 'cmb2',
		            'required'  => true,
	            )
			);

			tgmpa( $plugins );
		}

		public static function maybe_schedule_cron_jobs() {
			if ( ! wp_next_scheduled( 'wp_freeio_check_for_expired_jobs' ) ) {
				wp_schedule_event( time(), 'hourly', 'wp_freeio_check_for_expired_jobs' );
			}
			if ( ! wp_next_scheduled( 'wp_freeio_delete_old_previews' ) ) {
				wp_schedule_event( time(), 'daily', 'wp_freeio_delete_old_previews' );
			}
			if ( ! wp_next_scheduled( 'wp_freeio_email_daily_notices' ) ) {
				wp_schedule_event( time(), 'daily', 'wp_freeio_email_daily_notices' );
			}
		}

		/**
		 * Unschedule cron jobs. This is run on plugin deactivation.
		 */
		public static function unschedule_cron_jobs() {
			wp_clear_scheduled_hook( 'wp_freeio_check_for_expired_jobs' );
			wp_clear_scheduled_hook( 'wp_freeio_delete_old_previews' );
			wp_clear_scheduled_hook( 'wp_freeio_email_daily_notices' );
		}

		/**
		 *
		 */
		public function load_textdomain() {
			// Set filter for WP_Freeio's languages directory
			$lang_dir = WP_FREEIO_PLUGIN_DIR . 'languages/';
			$lang_dir = apply_filters( 'wp_freeio_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-freeio' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'wp-freeio', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/wp-freeio/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/wp-freeio folder
				load_textdomain( 'wp-freeio', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/wp-freeio/languages/ folder
				load_textdomain( 'wp-freeio', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'wp-freeio', false, $lang_dir );
			}
		}
	}
}

register_activation_hook( __FILE__, array( 'WP_Freeio', 'maybe_schedule_cron_jobs' ) );
register_deactivation_hook( __FILE__, array( 'WP_Freeio', 'unschedule_cron_jobs' ) );

function WP_Freeio() {
	return WP_Freeio::getInstance();
}

add_action( 'plugins_loaded', 'WP_Freeio' );
