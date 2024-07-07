<?php
/**
 * Favorite
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Favorite {
	
	public static function init() {
		// Ajax endpoints.
		// add_service_favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_add_service_favorite',  array(__CLASS__,'process_add_service_favorite') );

		// remove service favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_service_favorite',  array(__CLASS__,'process_remove_service_favorite') );


		// add_project_favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_add_project_favorite',  array(__CLASS__,'process_add_project_favorite') );

		// remove project favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_project_favorite',  array(__CLASS__,'process_remove_project_favorite') );

		// add_job_favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_add_job_favorite',  array(__CLASS__,'process_add_job_favorite') );

		// remove job favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_job_favorite',  array(__CLASS__,'process_remove_job_favorite') );

		// add_employer_favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_add_employer_favorite',  array(__CLASS__,'process_add_employer_favorite') );

		// remove employer favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_employer_favorite',  array(__CLASS__,'process_remove_employer_favorite') );

		// add_freelancer_favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_add_freelancer_favorite',  array(__CLASS__,'process_add_freelancer_favorite') );

		// remove freelancer favorite
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_freelancer_favorite',  array(__CLASS__,'process_remove_freelancer_favorite') );
	}

	public static function process_add_service_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-add-service-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to add favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$service_id = !empty($_POST['service_id']) ? $_POST['service_id'] : '';
		$post = get_post($service_id);

		if ( !$post || empty($post->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Service did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-add-service-favorite', $_POST);

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_service', true);

		if ( !empty($favorite) && is_array($favorite) ) {
			if ( !in_array($service_id, $favorite) ) {
				$favorite[] = $service_id;
			}
		} else {
			$favorite = array( $service_id );
		}
		$result = update_user_meta( $user_id, '_favorite_service', $favorite );

		if ( $result ) {
	        $return = array(
	        	'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-remove-service-favorite-nonce' ),
	        	'msg' => esc_html__('Add favorite successfully.', 'wp-freeio')
	        );
	        $return = apply_filters('wp-freeio-add-service-favorite-return', $return, $service_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Add favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function process_remove_service_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-remove-service-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to remove favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$service_id = !empty($_POST['service_id']) ? $_POST['service_id'] : '';

		if ( empty($service_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Service did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-remove-service-favorite', $_POST);

		$user_id = get_current_user_id();

		$result = true;
		$favorite = get_user_meta($user_id, '_favorite_service', true);
		if ( !empty($favorite) && is_array($favorite) ) {
			if ( in_array($service_id, $favorite) ) {
				$key = array_search( $service_id, $favorite );
				unset($favorite[$key]);
				$result = update_user_meta( $user_id, '_favorite_service', $favorite );
			}
		}

		if ( $result ) {
	        $return = array(
	        	'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-add-service-favorite-nonce' ),
	        	'msg' => esc_html__('Remove service from favorite successfully.', 'wp-freeio')
	        );
	        $return = apply_filters('wp-freeio-remove-service-favorite-return', $return, $service_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Remove service from favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function check_service_added_favorite($service_id) {
		if ( empty($service_id) || !is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_service', true);

		if ( !empty($favorite) && is_array($favorite) && in_array($service_id, $favorite) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_service_favorites() {
        $user_id = get_current_user_id();
        $data = get_user_meta($user_id, '_favorite_service', true);
        return $data;
    }

    public static function display_service_favorite_btn($service_id, $args = array()) {
		$args = wp_parse_args( $args, array(
			'show_icon' => true,
			'show_text' => false,
			'echo' => true,
			'tooltip' => true,
			'added_classes' => 'btn-added-service-favorite',
			'added_text' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_tooltip_title' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_icon_class' => 'flaticon-like',
			'add_classes' => 'btn-add-service-favorite',
			'add_text' => esc_html__('Add Favorite', 'wp-freeio'),
			'add_icon_class' => 'flaticon-like',
			'add_tooltip_title' => esc_html__('Add Favorite', 'wp-freeio'),
		));

		if ( self::check_service_added_favorite($service_id) ) {
			$classes = $args['added_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-remove-service-favorite-nonce' );
			$text = $args['added_text'];
			$icon_class = $args['added_icon_class'];
			$tooltip_title = $args['added_tooltip_title'];
		} else {
			$classes = $args['add_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-add-service-favorite-nonce' );
			$text = $args['add_text'];
			$icon_class = $args['add_icon_class'];
			$tooltip_title = $args['add_tooltip_title'];
		}
		ob_start();
		?>
		<a href="javascript:void(0)" class="<?php echo esc_attr($classes); ?>" data-service_id="<?php echo esc_attr($service_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
			<?php if ($args['tooltip']) { ?>
                data-bs-toggle="tooltip"
                title="<?php echo esc_attr($tooltip_title); ?>"
            <?php } ?>>
			<?php if ( $args['show_icon'] ) { ?>
				<i class="<?php echo esc_attr($icon_class); ?>"></i>
			<?php } ?>
			<?php if ( $args['show_text'] ) { ?>
				<span><?php echo esc_html($text); ?></span>
			<?php } ?>
		</a>
		<?php
		$output = ob_get_clean();
	    if ( $args['echo'] ) {
	    	echo trim($output);
	    } else {
	    	return $output;
	    }
	}


	// Project
    public static function process_add_project_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-add-project-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to add favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$project_id = !empty($_POST['project_id']) ? $_POST['project_id'] : '';
		$post = get_post($project_id);

		if ( !$post || empty($post->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Project did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-add-project-favorite', $_POST);

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_project', true);

		if ( !empty($favorite) && is_array($favorite) ) {
			if ( !in_array($project_id, $favorite) ) {
				$favorite[] = $project_id;
			}
		} else {
			$favorite = array( $project_id );
		}
		$result = update_user_meta( $user_id, '_favorite_project', $favorite );

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-remove-project-favorite-nonce' ), 'msg' => esc_html__('Add favorite successfully.', 'wp-freeio') );
	        $return = apply_filters('wp-freeio-add-project-favorite-return', $return, $project_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Add favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function process_remove_project_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-remove-project-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to remove favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$project_id = !empty($_POST['project_id']) ? $_POST['project_id'] : '';

		if ( empty($project_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Project did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-remove-project-favorite', $_POST);

		$user_id = get_current_user_id();

		$result = true;
		$favorite = get_user_meta($user_id, '_favorite_project', true);
		if ( !empty($favorite) && is_array($favorite) ) {
			if ( in_array($project_id, $favorite) ) {
				$key = array_search( $project_id, $favorite );
				unset($favorite[$key]);
				$result = update_user_meta( $user_id, '_favorite_project', $favorite );
			}
		}

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-add-project-favorite-nonce' ), 'msg' => esc_html__('Remove project from favorite successfully.', 'wp-freeio') );
	        $return = apply_filters('wp-freeio-remove-project-favorite-return', $return, $project_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Remove project from favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function check_project_added_favorite($project_id) {
		if ( empty($project_id) || !is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_project', true);

		if ( !empty($favorite) && is_array($favorite) && in_array($project_id, $favorite) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_project_favorites() {
        $user_id = get_current_user_id();
        $data = get_user_meta($user_id, '_favorite_project', true);
        return $data;
    }

    public static function display_project_favorite_btn($project_id, $args = array()) {
	    $args = wp_parse_args( $args, array(
			'show_icon' => true,
			'show_text' => false,
			'echo' => true,
			'tooltip' => true,
			'added_classes' => 'btn-added-project-favorite',
			'added_text' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_tooltip_title' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_icon_class' => 'flaticon-like',
			'add_classes' => 'btn-add-project-favorite',
			'add_text' => esc_html__('Add Favorite', 'wp-freeio'),
			'add_icon_class' => 'flaticon-like',
			'add_tooltip_title' => esc_html__('Add Favorite', 'wp-freeio'),
		));

		if ( self::check_project_added_favorite($project_id) ) {
			$classes = $args['added_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-remove-project-favorite-nonce' );
			$text = $args['added_text'];
			$icon_class = $args['added_icon_class'];
			$tooltip_title = $args['added_tooltip_title'];
		} else {
			$classes = $args['add_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-add-project-favorite-nonce' );
			$text = $args['add_text'];
			$icon_class = $args['add_icon_class'];
			$tooltip_title = $args['add_tooltip_title'];
		}
		ob_start();
		?>
		<a href="javascript:void(0)" class="<?php echo esc_attr($classes); ?>" data-project_id="<?php echo esc_attr($project_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
			<?php if ($args['tooltip']) { ?>
                data-bs-toggle="tooltip"
                title="<?php echo esc_attr($tooltip_title); ?>"
            <?php } ?>>
			<?php if ( $args['show_icon'] ) { ?>
				<i class="<?php echo esc_attr($icon_class); ?>"></i>
			<?php } ?>
			<?php if ( $args['show_text'] ) { ?>
				<span><?php echo esc_html($text); ?></span>
			<?php } ?>
		</a>
		<?php
		$output = ob_get_clean();
	    if ( $args['echo'] ) {
	    	echo trim($output);
	    } else {
	    	return $output;
	    }
	}

	// Job
    public static function process_add_job_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-add-job-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to add favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$job_id = !empty($_POST['job_id']) ? $_POST['job_id'] : '';
		$post = get_post($job_id);

		if ( !$post || empty($post->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Job did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-add-job-favorite', $_POST);

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_job', true);

		if ( !empty($favorite) && is_array($favorite) ) {
			if ( !in_array($job_id, $favorite) ) {
				$favorite[] = $job_id;
			}
		} else {
			$favorite = array( $job_id );
		}
		$result = update_user_meta( $user_id, '_favorite_job', $favorite );

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-remove-job-favorite-nonce' ), 'msg' => esc_html__('Add favorite successfully.', 'wp-freeio') );
	        $return = apply_filters('wp-freeio-add-job-favorite-return', $return, $job_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Add favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function process_remove_job_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-remove-job-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to remove favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$job_id = !empty($_POST['job_id']) ? $_POST['job_id'] : '';

		if ( empty($job_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Job did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-remove-job-favorite', $_POST);

		$user_id = get_current_user_id();

		$result = true;
		$favorite = get_user_meta($user_id, '_favorite_job', true);
		if ( !empty($favorite) && is_array($favorite) ) {
			if ( in_array($job_id, $favorite) ) {
				$key = array_search( $job_id, $favorite );
				unset($favorite[$key]);
				$result = update_user_meta( $user_id, '_favorite_job', $favorite );
			}
		}

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-add-job-favorite-nonce' ), 'msg' => esc_html__('Remove job from favorite successfully.', 'wp-freeio') );
	        $return = apply_filters('wp-freeio-remove-job-favorite-return', $return, $job_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Remove job from favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function check_job_added_favorite($job_id) {
		if ( empty($job_id) || !is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_job', true);

		if ( !empty($favorite) && is_array($favorite) && in_array($job_id, $favorite) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_job_favorites() {
        $user_id = get_current_user_id();
        $data = get_user_meta($user_id, '_favorite_job', true);
        return $data;
    }

    public static function display_job_favorite_btn($job_id, $args = array()) {
		$args = wp_parse_args( $args, array(
			'show_icon' => true,
			'show_text' => false,
			'echo' => true,
			'tooltip' => true,
			'added_classes' => 'btn-added-job-favorite',
			'added_text' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_tooltip_title' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_icon_class' => 'flaticon-like',
			'add_classes' => 'btn-add-job-favorite',
			'add_text' => esc_html__('Add Favorite', 'wp-freeio'),
			'add_icon_class' => 'flaticon-like',
			'add_tooltip_title' => esc_html__('Add Favorite', 'wp-freeio'),
		));

		if ( self::check_job_added_favorite($job_id) ) {
			$classes = $args['added_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-remove-job-favorite-nonce' );
			$text = $args['added_text'];
			$icon_class = $args['added_icon_class'];
			$tooltip_title = $args['added_tooltip_title'];
		} else {
			$classes = $args['add_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-add-job-favorite-nonce' );
			$text = $args['add_text'];
			$icon_class = $args['add_icon_class'];
			$tooltip_title = $args['add_tooltip_title'];
		}
		ob_start();
		?>
		<a href="javascript:void(0)" class="<?php echo esc_attr($classes); ?>" data-job_id="<?php echo esc_attr($job_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
			<?php if ($args['tooltip']) { ?>
                data-toggle="tooltip"
                title="<?php echo esc_attr($tooltip_title); ?>"
            <?php } ?>>
			<?php if ( $args['show_icon'] ) { ?>
				<i class="<?php echo esc_attr($icon_class); ?>"></i>
			<?php } ?>
			<?php if ( $args['show_text'] ) { ?>
				<span><?php echo esc_html($text); ?></span>
			<?php } ?>
		</a>
		<?php
		$output = ob_get_clean();
	    if ( $args['echo'] ) {
	    	echo trim($output);
	    } else {
	    	return $output;
	    }
	}

	// Employer
    public static function process_add_employer_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-add-employer-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to add favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$employer_id = !empty($_POST['employer_id']) ? $_POST['employer_id'] : '';
		$post = get_post($employer_id);

		if ( !$post || empty($post->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Employer did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-add-employer-favorite', $_POST);

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_employer', true);

		if ( !empty($favorite) && is_array($favorite) ) {
			if ( !in_array($employer_id, $favorite) ) {
				$favorite[] = $employer_id;
			}
		} else {
			$favorite = array( $employer_id );
		}
		$result = update_user_meta( $user_id, '_favorite_employer', $favorite );

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-remove-employer-favorite-nonce' ), 'msg' => esc_html__('Add favorite successfully.', 'wp-freeio') );
	        $return = apply_filters('wp-freeio-add-employer-favorite-return', $return, $employer_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Add favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function process_remove_employer_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-remove-employer-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to remove favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$employer_id = !empty($_POST['employer_id']) ? $_POST['employer_id'] : '';

		if ( empty($employer_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Employer did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-remove-employer-favorite', $_POST);

		$user_id = get_current_user_id();

		$result = true;
		$favorite = get_user_meta($user_id, '_favorite_employer', true);
		if ( !empty($favorite) && is_array($favorite) ) {
			if ( in_array($employer_id, $favorite) ) {
				$key = array_search( $employer_id, $favorite );
				unset($favorite[$key]);
				$result = update_user_meta( $user_id, '_favorite_employer', $favorite );
			}
		}

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-add-employer-favorite-nonce' ), 'msg' => esc_html__('Remove employer from favorite successfully.', 'wp-freeio') );
	        $return = apply_filters('wp-freeio-remove-employer-favorite-return', $return, $employer_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Remove employer from favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function check_employer_added_favorite($employer_id) {
		if ( empty($employer_id) || !is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_employer', true);

		if ( !empty($favorite) && is_array($favorite) && in_array($employer_id, $favorite) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_employer_favorites() {
        $user_id = get_current_user_id();
        $data = get_user_meta($user_id, '_favorite_employer', true);
        return $data;
    }

    public static function display_employer_favorite_btn($employer_id, $args = array()) {
		$args = wp_parse_args( $args, array(
			'show_icon' => true,
			'show_text' => false,
			'echo' => true,
			'tooltip' => true,
			'added_classes' => 'btn-added-employer-favorite',
			'added_text' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_tooltip_title' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_icon_class' => 'flaticon-like',
			'add_classes' => 'btn-add-employer-favorite',
			'add_text' => esc_html__('Add Favorite', 'wp-freeio'),
			'add_icon_class' => 'flaticon-like',
			'add_tooltip_title' => esc_html__('Add Favorite', 'wp-freeio'),
		));

		if ( self::check_employer_added_favorite($employer_id) ) {
			$classes = $args['added_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-remove-employer-favorite-nonce' );
			$text = $args['added_text'];
			$icon_class = $args['added_icon_class'];
			$tooltip_title = $args['added_tooltip_title'];
		} else {
			$classes = $args['add_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-add-employer-favorite-nonce' );
			$text = $args['add_text'];
			$icon_class = $args['add_icon_class'];
			$tooltip_title = $args['add_tooltip_title'];
		}
		ob_start();
		?>
		<a href="javascript:void(0)" class="<?php echo esc_attr($classes); ?>" data-employer_id="<?php echo esc_attr($employer_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
			<?php if ($args['tooltip']) { ?>
                data-toggle="tooltip"
                title="<?php echo esc_attr($tooltip_title); ?>"
            <?php } ?>>
			<?php if ( $args['show_icon'] ) { ?>
				<i class="<?php echo esc_attr($icon_class); ?>"></i>
			<?php } ?>
			<?php if ( $args['show_text'] ) { ?>
				<span><?php echo esc_html($text); ?></span>
			<?php } ?>
		</a>
		<?php
		$output = ob_get_clean();
	    if ( $args['echo'] ) {
	    	echo trim($output);
	    } else {
	    	return $output;
	    }
	}

	// Freelancer
    public static function process_add_freelancer_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-add-freelancer-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to add favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$freelancer_id = !empty($_POST['freelancer_id']) ? $_POST['freelancer_id'] : '';
		$post = get_post($freelancer_id);

		if ( !$post || empty($post->ID) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Freelancer did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-add-freelancer-favorite', $_POST);

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_freelancer', true);

		if ( !empty($favorite) && is_array($favorite) ) {
			if ( !in_array($freelancer_id, $favorite) ) {
				$favorite[] = $freelancer_id;
			}
		} else {
			$favorite = array( $freelancer_id );
		}
		$result = update_user_meta( $user_id, '_favorite_freelancer', $favorite );

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-remove-freelancer-favorite-nonce' ), 'msg' => esc_html__('Add favorite successfully.', 'wp-freeio') );
	        $return = apply_filters('wp-freeio-add-freelancer-favorite-return', $return, $freelancer_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Add favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function process_remove_freelancer_favorite() {
		$return = array();
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-remove-freelancer-favorite-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		if ( !is_user_logged_in() ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Please login to remove favorite.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$freelancer_id = !empty($_POST['freelancer_id']) ? $_POST['freelancer_id'] : '';

		if ( empty($freelancer_id) ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Freelancer did not exists.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-process-remove-freelancer-favorite', $_POST);

		$user_id = get_current_user_id();

		$result = true;
		$favorite = get_user_meta($user_id, '_favorite_freelancer', true);
		if ( !empty($favorite) && is_array($favorite) ) {
			if ( in_array($freelancer_id, $favorite) ) {
				$key = array_search( $freelancer_id, $favorite );
				unset($favorite[$key]);
				$result = update_user_meta( $user_id, '_favorite_freelancer', $favorite );
			}
		}

		if ( $result ) {
	        $return = array( 'status' => true, 'nonce' => wp_create_nonce( 'wp-freeio-add-freelancer-favorite-nonce' ), 'msg' => esc_html__('Remove freelancer from favorite successfully.', 'wp-freeio') );
	        $return = apply_filters('wp-freeio-remove-freelancer-favorite-return', $return, $freelancer_id);
		   	echo wp_json_encode($return);
		   	exit;
	    } else {
			$return = array( 'status' => false, 'msg' => esc_html__('Remove freelancer from favorite error.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function check_freelancer_added_favorite($freelancer_id) {
		if ( empty($freelancer_id) || !is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();

		$favorite = get_user_meta($user_id, '_favorite_freelancer', true);

		if ( !empty($favorite) && is_array($favorite) && in_array($freelancer_id, $favorite) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_freelancer_favorites() {
        $user_id = get_current_user_id();
        $data = get_user_meta($user_id, '_favorite_freelancer', true);
        return $data;
    }

    public static function display_freelancer_favorite_btn($freelancer_id, $args = array()) {
		$args = wp_parse_args( $args, array(
			'show_icon' => true,
			'show_text' => false,
			'echo' => true,
			'tooltip' => true,
			'added_classes' => 'btn-added-freelancer-favorite',
			'added_text' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_tooltip_title' => esc_html__('Remove Favorite', 'wp-freeio'),
			'added_icon_class' => 'flaticon-like',
			'add_classes' => 'btn-add-freelancer-favorite',
			'add_text' => esc_html__('Add Favorite', 'wp-freeio'),
			'add_icon_class' => 'flaticon-like',
			'add_tooltip_title' => esc_html__('Add Favorite', 'wp-freeio'),
		));

		if ( self::check_freelancer_added_favorite($freelancer_id) ) {
			$classes = $args['added_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-remove-freelancer-favorite-nonce' );
			$text = $args['added_text'];
			$icon_class = $args['added_icon_class'];
			$tooltip_title = $args['added_tooltip_title'];
		} else {
			$classes = $args['add_classes'];
			$nonce = wp_create_nonce( 'wp-freeio-add-freelancer-favorite-nonce' );
			$text = $args['add_text'];
			$icon_class = $args['add_icon_class'];
			$tooltip_title = $args['add_tooltip_title'];
		}
		ob_start();
		?>
		<a href="javascript:void(0)" class="<?php echo esc_attr($classes); ?>" data-freelancer_id="<?php echo esc_attr($freelancer_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"
			<?php if ($args['tooltip']) { ?>
                data-toggle="tooltip"
                title="<?php echo esc_attr($tooltip_title); ?>"
            <?php } ?>>
			<?php if ( $args['show_icon'] ) { ?>
				<i class="<?php echo esc_attr($icon_class); ?>"></i>
			<?php } ?>
			<?php if ( $args['show_text'] ) { ?>
				<span><?php echo esc_html($text); ?></span>
			<?php } ?>
		</a>
		<?php
		$output = ob_get_clean();
	    if ( $args['echo'] ) {
	    	echo trim($output);
	    } else {
	    	return $output;
	    }
	}
}
WP_Freeio_Favorite::init();