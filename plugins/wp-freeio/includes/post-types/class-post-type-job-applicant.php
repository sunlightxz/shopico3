<?php
/**
 * Post Type: Job Applicant
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Job_Applicant {
	
	public static $prefix = WP_FREEIO_APPLICANT_PREFIX;

	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );

	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );

	  	add_filter( 'manage_edit-job_applicant_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_job_applicant_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
	}

	public static function register_post_type() {
		$singular = __( 'Job Applicant', 'wp-freeio' );
		$plural   = __( 'Job Applicants', 'wp-freeio' );

		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new'               => sprintf(__( 'Add New %s', 'wp-freeio' ), $singular),
			'add_new_item'          => sprintf(__( 'Add New %s', 'wp-freeio' ), $singular),
			'edit_item'             => sprintf(__( 'Edit %s', 'wp-freeio' ), $singular),
			'new_item'              => sprintf(__( 'New %s', 'wp-freeio' ), $singular),
			'all_items'             => $plural,
			'view_item'             => sprintf(__( 'View %s', 'wp-freeio' ), $singular),
			'search_items'          => sprintf(__( 'Search %s', 'wp-freeio' ), $singular),
			'not_found'             => sprintf(__( 'No %s found', 'wp-freeio' ), $plural),
			'not_found_in_trash'    => sprintf(__( 'No %s found in Trash', 'wp-freeio' ), $plural),
			'parent_item_colon'     => '',
			'menu_name'             => $plural,
		);

		register_post_type( 'job_applicant',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title' ),
				'public'            => true,
		        'has_archive'       => false,
		        'publicly_queryable' => false,
				'show_in_rest'		=> true,
				'show_in_menu'		=> 'edit.php?post_type=job_listing',
				'capabilities' => array(
				    'create_posts' => false,
				),
				'map_meta_cap' => true,
			)
		);
	}

	/**
	 * Defines custom fields
	 *
	 * @access public
	 * @param array $metaboxes
	 * @return array
	 */
	public static function fields( array $metaboxes ) {
		

		$metaboxes[ self::$prefix . 'general' ] = array(
			'id'                        => self::$prefix . 'general',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( 'job_applicant' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => array(
				array(
					'name'              => __( 'Status', 'wp-freeio' ),
					'id'                => self::$prefix . 'app_status',
					'type'              => 'select',
					'options'			=> array(
						'' => esc_html__('Pending', 'wp-freeio'),
						'approved' => esc_html__('Approved', 'wp-freeio'),
						'rejected' => esc_html__('Rejected', 'wp-freeio'),
					)
				),
				array(
					'name'              => __( 'Job ID', 'wp-freeio' ),
					'id'                => self::$prefix . 'job_id',
					'type'              => 'text',
				),
				array(
					'name'              => __( 'Job Name', 'wp-freeio' ),
					'id'                => self::$prefix . 'job_name',
					'type'              => 'text',
				),
				array(
					'name'              => __( 'Message', 'wp-freeio' ),
					'id'                => self::$prefix . 'message',
					'type'              => 'textarea',
				),
				array(
					'name'              => __( 'CV ID', 'wp-freeio' ),
					'id'                => self::$prefix . 'cv_file_id',
					'type'              => 'text',
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
	public static function custom_columns($columns) {
		if ( isset($columns['comments']) ) {
			unset($columns['comments']);
		}
		if ( isset($columns['date']) ) {
			unset($columns['date']);
		}
		$fields = array_merge($columns, array(
			'thumbnail' 		=> __( 'Thumbnail', 'wp-freeio' ),
			'title' 			=> __( 'Title', 'wp-freeio' ),
			'job_title' 		=> __( 'Job Title', 'wp-freeio' ),
			'freelancer' 		=> __( 'View Profile', 'wp-freeio' ),
			'author' 			=> __( 'Author', 'wp-freeio' ),
			'status' 			=> __( 'Status', 'wp-freeio' ),
		));
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
		switch ( $column ) {
			case 'thumbnail':
				$freelancer_id = get_post_meta( get_the_ID(), WP_FREEIO_APPLICANT_PREFIX . 'freelancer_id', true );
				if ( has_post_thumbnail($freelancer_id) ) {
					echo get_the_post_thumbnail( $freelancer_id, 'thumbnail', array(
						'class' => 'attachment-thumbnail attachment-thumbnail-small logo-thumnail',
					) );
				} else {
					echo '-';
				}
				break;
			case 'job_title':
				$job_id = get_post_meta( get_the_ID(), WP_FREEIO_APPLICANT_PREFIX . 'job_id', true );
				?>
				<a href="<?php echo esc_url(get_permalink($job_id)); ?>" target="_blank"><?php echo get_the_title($job_id); ?></a>
				<?php
				break;
			case 'freelancer':
				$freelancer_id = get_post_meta( get_the_ID(), WP_FREEIO_APPLICANT_PREFIX . 'freelancer_id', true );
				?>
				<a href="<?php echo esc_url(get_permalink($freelancer_id)); ?>" target="_blank"><?php esc_html_e('View profile', 'wp-freeio'); ?></a>
				<?php
				break;
			case 'status':
				$app_status = WP_Freeio_Applicant::get_post_meta(get_the_ID(), 'app_status', true);

                if ( $app_status == 'approved' ) {
                    echo '<div class="application-status-label approved" style="background: #007cba;color: #fff;border-radius: 3px;padding: 5px 10px; display: inline-block;">'.esc_html__('Approved', 'wp-freeio').'</div>';
                } elseif ( $app_status == 'rejected' ) {
                    echo '<div class="application-status-label rejected" style="background: #ca4a1f;color: #fff;border-radius: 3px;padding: 5px 10px;display: inline-block;">'.esc_html__('Rejected', 'wp-freeio').'</div>';
                } else {
                    echo '<div class="application-status-label pending" style="background: #39b54a;color: #fff;border-radius: 3px;padding: 5px 10px;display: inline-block;">'.esc_html__('Pending', 'wp-freeio').'</div>';
                }
				break;

		}
	}

}
WP_Freeio_Post_Type_Job_Applicant::init();