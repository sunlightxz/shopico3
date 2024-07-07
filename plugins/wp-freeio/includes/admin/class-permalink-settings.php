<?php
/**
 * Permalink Settings
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Freeio_Permalink_Settings {
	
	public static function init() {
		add_action('admin_init', array( __CLASS__, 'setup_fields') );
		add_action('admin_init', array( __CLASS__, 'settings_save') );
	}

	public static function setup_fields() {
		add_settings_field(
			'wp_freeio_job_base_slug',
			__( 'Job base', 'wp-freeio' ),
			array( __CLASS__, 'job_base_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_job_category_slug',
			__( 'Job category base', 'wp-freeio' ),
			array( __CLASS__, 'job_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_job_type_slug',
			__( 'Job type base', 'wp-freeio' ),
			array( __CLASS__, 'job_type_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_location_slug',
			__( 'Location base', 'wp-freeio' ),
			array( __CLASS__, 'location_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_job_tag_slug',
			__( 'Job tag base', 'wp-freeio' ),
			array( __CLASS__, 'job_tag_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_job_archive_slug',
			__( 'Job archive base', 'wp-freeio' ),
			array( __CLASS__, 'job_archive_slug_input' ),
			'permalink',
			'optional'
		);

		// employer
		add_settings_field(
			'wp_freeio_employer_base_slug',
			__( 'Employer base', 'wp-freeio' ),
			array( __CLASS__, 'employer_base_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_employer_category_slug',
			__( 'Employer category base', 'wp-freeio' ),
			array( __CLASS__, 'employer_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_employer_archive_slug',
			__( 'Employer archive base', 'wp-freeio' ),
			array( __CLASS__, 'employer_archive_slug_input' ),
			'permalink',
			'optional'
		);

		// freelancer
		add_settings_field(
			'wp_freeio_freelancer_base_slug',
			__( 'Freelancer base', 'wp-freeio' ),
			array( __CLASS__, 'freelancer_base_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_freelancer_category_slug',
			__( 'Freelancer category base', 'wp-freeio' ),
			array( __CLASS__, 'freelancer_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_freelancer_tag_slug',
			__( 'Freelancer tag base', 'wp-freeio' ),
			array( __CLASS__, 'freelancer_tag_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_freelancer_archive_slug',
			__( 'Freelancer archive base', 'wp-freeio' ),
			array( __CLASS__, 'freelancer_archive_slug_input' ),
			'permalink',
			'optional'
		);

		// service
		add_settings_field(
			'wp_freeio_service_base_slug',
			__( 'Service base', 'wp-freeio' ),
			array( __CLASS__, 'service_base_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_service_category_slug',
			__( 'Service category base', 'wp-freeio' ),
			array( __CLASS__, 'service_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_service_tag_slug',
			__( 'Service tag base', 'wp-freeio' ),
			array( __CLASS__, 'service_tag_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_service_archive_slug',
			__( 'Service archive base', 'wp-freeio' ),
			array( __CLASS__, 'service_archive_slug_input' ),
			'permalink',
			'optional'
		);

		// project
		add_settings_field(
			'wp_freeio_project_base_slug',
			__( 'Project base', 'wp-freeio' ),
			array( __CLASS__, 'project_base_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_project_category_slug',
			__( 'Project category base', 'wp-freeio' ),
			array( __CLASS__, 'project_category_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_project_skill_slug',
			__( 'Project skill base', 'wp-freeio' ),
			array( __CLASS__, 'project_skill_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_project_duration_slug',
			__( 'Project duration base', 'wp-freeio' ),
			array( __CLASS__, 'project_duration_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_project_experience_slug',
			__( 'Project experience base', 'wp-freeio' ),
			array( __CLASS__, 'project_experience_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_project_freelancer_type_slug',
			__( 'Project freelancer type base', 'wp-freeio' ),
			array( __CLASS__, 'project_freelancer_type_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_project_language_slug',
			__( 'Project language base', 'wp-freeio' ),
			array( __CLASS__, 'project_language_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_project_level_slug',
			__( 'Project level base', 'wp-freeio' ),
			array( __CLASS__, 'project_level_slug_input' ),
			'permalink',
			'optional'
		);
		add_settings_field(
			'wp_freeio_project_archive_slug',
			__( 'Project archive base', 'wp-freeio' ),
			array( __CLASS__, 'project_archive_slug_input' ),
			'permalink',
			'optional'
		);
	}

	public static function job_base_slug_input() {
		$value = get_option('wp_freeio_job_base_slug');
		?>
		<input name="wp_freeio_job_base_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'job', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function job_category_slug_input() {
		$value = get_option('wp_freeio_job_category_slug');
		?>
		<input name="wp_freeio_job_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'job-category', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function job_type_slug_input() {
		$value = get_option('wp_freeio_job_type_slug');
		?>
		<input name="wp_freeio_job_type_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'job-type', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function location_slug_input() {
		$value = get_option('wp_freeio_location_slug');
		?>
		<input name="wp_freeio_location_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'job-location', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function job_tag_slug_input() {
		$value = get_option('wp_freeio_job_tag_slug');
		?>
		<input name="wp_freeio_job_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'job-tag', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function job_archive_slug_input() {
		$value = get_option('wp_freeio_job_archive_slug');
		?>
		<input name="wp_freeio_job_archive_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'jobs', 'wp-freeio' ); ?>" />
		<?php
	}

	// employer
	public static function employer_base_slug_input() {
		$value = get_option('wp_freeio_employer_base_slug');
		?>
		<input name="wp_freeio_employer_base_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'employer', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function employer_category_slug_input() {
		$value = get_option('wp_freeio_employer_category_slug');
		?>
		<input name="wp_freeio_employer_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'employer-category', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function employer_archive_slug_input() {
		$value = get_option('wp_freeio_employer_archive_slug');
		?>
		<input name="wp_freeio_employer_archive_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'employers', 'wp-freeio' ); ?>" />
		<?php
	}

	// freelancer
	public static function freelancer_base_slug_input() {
		$value = get_option('wp_freeio_freelancer_base_slug');
		?>
		<input name="wp_freeio_freelancer_base_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'freelancer', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function freelancer_category_slug_input() {
		$value = get_option('wp_freeio_freelancer_category_slug');
		?>
		<input name="wp_freeio_freelancer_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'freelancer-category', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function freelancer_tag_slug_input() {
		$value = get_option('wp_freeio_freelancer_tag_slug');
		?>
		<input name="wp_freeio_freelancer_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'freelancer-tag', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function freelancer_archive_slug_input() {
		$value = get_option('wp_freeio_freelancer_archive_slug');
		?>
		<input name="wp_freeio_freelancer_archive_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'freelancers', 'wp-freeio' ); ?>" />
		<?php
	}

	// service
	public static function service_base_slug_input() {
		$value = get_option('wp_freeio_service_base_slug');
		?>
		<input name="wp_freeio_service_base_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'service', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function service_category_slug_input() {
		$value = get_option('wp_freeio_service_category_slug');
		?>
		<input name="wp_freeio_service_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'service-category', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function service_tag_slug_input() {
		$value = get_option('wp_freeio_service_tag_slug');
		?>
		<input name="wp_freeio_service_tag_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'service-tag', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function service_archive_slug_input() {
		$value = get_option('wp_freeio_service_archive_slug');
		?>
		<input name="wp_freeio_service_archive_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'services', 'wp-freeio' ); ?>" />
		<?php
	}

	// project
	public static function project_base_slug_input() {
		$value = get_option('wp_freeio_project_base_slug');
		?>
		<input name="wp_freeio_project_base_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'project', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function project_category_slug_input() {
		$value = get_option('wp_freeio_project_category_slug');
		?>
		<input name="wp_freeio_project_category_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'project-category', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function project_skill_slug_input() {
		$value = get_option('wp_freeio_project_skill_slug');
		?>
		<input name="wp_freeio_project_skill_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'project-skill', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function project_duration_slug_input() {
		$value = get_option('wp_freeio_project_duration_slug');
		?>
		<input name="wp_freeio_project_duration_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'project-duration', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function project_experience_slug_input() {
		$value = get_option('wp_freeio_project_experience_slug');
		?>
		<input name="wp_freeio_project_experience_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'project-experience', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function project_freelancer_type_slug_input() {
		$value = get_option('wp_freeio_project_freelancer_type_slug');
		?>
		<input name="wp_freeio_project_freelancer_type_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'project-freelancer-type', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function project_language_slug_input() {
		$value = get_option('wp_freeio_project_language_slug');
		?>
		<input name="wp_freeio_project_language_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'project-language', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function project_level_slug_input() {
		$value = get_option('wp_freeio_project_level_slug');
		?>
		<input name="wp_freeio_project_level_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'project-level', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function project_archive_slug_input() {
		$value = get_option('wp_freeio_project_archive_slug');
		?>
		<input name="wp_freeio_project_archive_slug" type="text" class="regular-text code" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php esc_attr_e( 'projects', 'wp-freeio' ); ?>" />
		<?php
	}

	public static function settings_save() {
		if ( ! is_admin() ) {
			return;
		}

		if ( isset( $_POST['permalink_structure'] ) ) {
			if ( function_exists( 'switch_to_locale' ) ) {
				switch_to_locale( get_locale() );
			}
			if ( isset($_POST['wp_freeio_job_base_slug']) ) {
				update_option( 'wp_freeio_job_base_slug', sanitize_title_with_dashes($_POST['wp_freeio_job_base_slug']) );
			}
			if ( isset($_POST['wp_freeio_job_category_slug']) ) {
				update_option( 'wp_freeio_job_category_slug', sanitize_title_with_dashes($_POST['wp_freeio_job_category_slug']) );
			}
			if ( isset($_POST['wp_freeio_job_type_slug']) ) {
				update_option( 'wp_freeio_job_type_slug', sanitize_title_with_dashes($_POST['wp_freeio_job_type_slug']) );
			}
			if ( isset($_POST['wp_freeio_location_slug']) ) {
				update_option( 'wp_freeio_location_slug', sanitize_title_with_dashes($_POST['wp_freeio_location_slug']) );
			}
			if ( isset($_POST['wp_freeio_job_tag_slug']) ) {
				update_option( 'wp_freeio_job_tag_slug', sanitize_title_with_dashes($_POST['wp_freeio_job_tag_slug']) );
			}
			if ( isset($_POST['wp_freeio_job_archive_slug']) ) {
				update_option( 'wp_freeio_job_archive_slug', sanitize_title_with_dashes($_POST['wp_freeio_job_archive_slug']) );
			}

			// employer
			if ( isset($_POST['wp_freeio_employer_base_slug']) ) {
				update_option( 'wp_freeio_employer_base_slug', sanitize_title_with_dashes($_POST['wp_freeio_employer_base_slug']) );
			}
			if ( isset($_POST['wp_freeio_employer_category_slug']) ) {
				update_option( 'wp_freeio_employer_category_slug', sanitize_title_with_dashes($_POST['wp_freeio_employer_category_slug']) );
			}
			if ( isset($_POST['wp_freeio_employer_archive_slug']) ) {
				update_option( 'wp_freeio_employer_archive_slug', sanitize_title_with_dashes($_POST['wp_freeio_employer_archive_slug']) );
			}

			// freelancer
			if ( isset($_POST['wp_freeio_freelancer_base_slug']) ) {
				update_option( 'wp_freeio_freelancer_base_slug', sanitize_title_with_dashes($_POST['wp_freeio_freelancer_base_slug']) );
			}
			if ( isset($_POST['wp_freeio_freelancer_category_slug']) ) {
				update_option( 'wp_freeio_freelancer_category_slug', sanitize_title_with_dashes($_POST['wp_freeio_freelancer_category_slug']) );
			}
			if ( isset($_POST['wp_freeio_freelancer_tag_slug']) ) {
				update_option( 'wp_freeio_freelancer_tag_slug', sanitize_title_with_dashes($_POST['wp_freeio_freelancer_tag_slug']) );
			}
			if ( isset($_POST['wp_freeio_freelancer_archive_slug']) ) {
				update_option( 'wp_freeio_freelancer_archive_slug', sanitize_title_with_dashes($_POST['wp_freeio_freelancer_archive_slug']) );
			}

			// service
			if ( isset($_POST['wp_freeio_service_base_slug']) ) {
				update_option( 'wp_freeio_service_base_slug', sanitize_title_with_dashes($_POST['wp_freeio_service_base_slug']) );
			}
			if ( isset($_POST['wp_freeio_service_category_slug']) ) {
				update_option( 'wp_freeio_service_category_slug', sanitize_title_with_dashes($_POST['wp_freeio_service_category_slug']) );
			}
			if ( isset($_POST['wp_freeio_service_tag_slug']) ) {
				update_option( 'wp_freeio_service_tag_slug', sanitize_title_with_dashes($_POST['wp_freeio_service_tag_slug']) );
			}
			if ( isset($_POST['wp_freeio_service_archive_slug']) ) {
				update_option( 'wp_freeio_service_archive_slug', sanitize_title_with_dashes($_POST['wp_freeio_service_archive_slug']) );
			}

			// project
			if ( isset($_POST['wp_freeio_project_base_slug']) ) {
				update_option( 'wp_freeio_project_base_slug', sanitize_title_with_dashes($_POST['wp_freeio_project_base_slug']) );
			}
			if ( isset($_POST['wp_freeio_project_category_slug']) ) {
				update_option( 'wp_freeio_project_category_slug', sanitize_title_with_dashes($_POST['wp_freeio_project_category_slug']) );
			}
			if ( isset($_POST['wp_freeio_project_skill_slug']) ) {
				update_option( 'wp_freeio_project_skill_slug', sanitize_title_with_dashes($_POST['wp_freeio_project_skill_slug']) );
			}
			if ( isset($_POST['wp_freeio_project_duration_slug']) ) {
				update_option( 'wp_freeio_project_duration_slug', sanitize_title_with_dashes($_POST['wp_freeio_project_duration_slug']) );
			}
			if ( isset($_POST['wp_freeio_project_experience_slug']) ) {
				update_option( 'wp_freeio_project_experience_slug', sanitize_title_with_dashes($_POST['wp_freeio_project_experience_slug']) );
			}
			if ( isset($_POST['wp_freeio_project_freelancer_type_slug']) ) {
				update_option( 'wp_freeio_project_freelancer_type_slug', sanitize_title_with_dashes($_POST['wp_freeio_project_freelancer_type_slug']) );
			}
			if ( isset($_POST['wp_freeio_project_language_slug']) ) {
				update_option( 'wp_freeio_project_language_slug', sanitize_title_with_dashes($_POST['wp_freeio_project_language_slug']) );
			}
			if ( isset($_POST['wp_freeio_project_level_slug']) ) {
				update_option( 'wp_freeio_project_level_slug', sanitize_title_with_dashes($_POST['wp_freeio_project_level_slug']) );
			}
			if ( isset($_POST['wp_freeio_project_archive_slug']) ) {
				update_option( 'wp_freeio_project_archive_slug', sanitize_title_with_dashes($_POST['wp_freeio_project_archive_slug']) );
			}
		}
	}
}

WP_Freeio_Permalink_Settings::init();
