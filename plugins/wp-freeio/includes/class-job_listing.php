<?php
/**
 * Job Listing
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Job_Listing {
	
	public static function init() {
		// loop
		add_action( 'wp_freeio_before_job_archive', array( __CLASS__, 'display_jobs_results_filters' ), 5 );
		add_action( 'wp_freeio_before_job_archive', array( __CLASS__, 'display_jobs_count_results' ), 10 );

		add_action( 'wp_freeio_before_job_archive', array( __CLASS__, 'display_jobs_alert_orderby_start' ), 15 );
		add_action( 'wp_freeio_before_job_archive', array( __CLASS__, 'display_jobs_alert_form' ), 20 );
		add_action( 'wp_freeio_before_job_archive', array( __CLASS__, 'display_job_feed' ), 22 );
		add_action( 'wp_freeio_before_job_archive', array( __CLASS__, 'display_jobs_orderby' ), 25 );
		add_action( 'wp_freeio_before_job_archive', array( __CLASS__, 'display_jobs_alert_orderby_end' ), 100 );


		// restrict
		add_filter( 'wp-freeio-job_listing-query-args', array( __CLASS__, 'job_restrict_listing_query_args'), 100, 2 );
		add_filter( 'wp-freeio-job_listing-filter-query', array( __CLASS__, 'job_restrict_listing_query'), 100, 2 );
	}

	public static function get_author_id($post_id) {
		$employer_id = self::get_post_meta($post_id, 'employer_posted_by', true);

		if ( !empty($employer_id) ) {
			$user_id = WP_Freeio_User::get_user_by_employer_id($employer_id);
		} else {
			$user_id = get_post_field( 'post_author', $post_id );
		}
		return $user_id;
	}

	public static function get_post_meta($post_id, $key, $single = true) {
		return get_post_meta($post_id, WP_FREEIO_JOB_LISTING_PREFIX.$key, $single);
	}

	// add product viewed
	public static function track_job_view() {
	    if ( ! is_singular( 'job_listing' ) ) {
	        return;
	    }

	    global $post;

	    // views count
	    $viewed_count = intval(get_post_meta($post->ID, '_viewed_count', true));
	    $viewed_count++;
	    update_post_meta($post->ID, '_viewed_count', $viewed_count);

	    // view days
	    $today = date('Y-m-d', time());
	    $views_by_date = get_post_meta($post->ID, '_views_by_date', true);

	    if( $views_by_date != '' || is_array($views_by_date) ) {
	        if (!isset($views_by_date[$today])) {
	            if ( count($views_by_date) > 60 ) {
	                array_shift($views_by_date);
	            }
	            $views_by_date[$today] = 1;
	        } else {
	            $views_by_date[$today] = intval($views_by_date[$today]) + 1;
	        }
	    } else {
	        $views_by_date = array();
	        $views_by_date[$today] = 1;
	    }
	    update_post_meta($post->ID, '_views_by_date', $views_by_date);
	    update_post_meta($post->ID, '_recently_viewed', $today);

	    // 

	    if ( empty( $_COOKIE['wp_freeio_recently_viewed'] ) ) {
	        $viewed_products = array();
	    } else {
	        $viewed_products = (array) explode( '|', $_COOKIE['wp_freeio_recently_viewed'] );
	    }

	    if ( ! in_array( $post->ID, $viewed_products ) ) {
	        $viewed_products[] = $post->ID;
	    }

	    if ( sizeof( $viewed_products ) > 15 ) {
	        array_shift( $viewed_products );
	    }

	    // Store for session only
	    setcookie( 'wp_freeio_recently_viewed', implode( '|', $viewed_products ) );

	}

	public static function send_admin_expiring_notice() {
		global $wpdb;

		if ( !wp_freeio_get_option('admin_notice_expiring_job') ) {
			return;
		}
		$days_notice = wp_freeio_get_option('admin_notice_expiring_job_days');

		$job_ids = self::get_expiring_jobs($days_notice);

		if ( $job_ids ) {
			foreach ( $job_ids as $job_id ) {
				// send email here.
				$job = get_post($job_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$email_to = get_option( 'admin_email', false );
				$subject = WP_Freeio_Email::render_email_vars(array('job' => $job), 'admin_notice_expiring_job', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('job' => $job), 'admin_notice_expiring_job', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}
		}
	}

	public static function send_employer_expiring_notice() {
		global $wpdb;

		if ( !wp_freeio_get_option('employer_notice_expiring_job') ) {
			return;
		}
		$days_notice = wp_freeio_get_option('employer_notice_expiring_job_days');

		$job_ids = self::get_expiring_jobs($days_notice);

		if ( $job_ids ) {
			foreach ( $job_ids as $job_id ) {
				// send email here.
				$job = get_post($job_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$author_id = WP_Freeio_Job_Listing::get_author_id($job->ID);

				if ( WP_Freeio_User::is_employer($author_id) ) {
					$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
					$email_to = WP_Freeio_Employer::get_post_meta($employer_id, 'email');
				}
				if ( empty($email_to) ) {
					$email_to = get_the_author_meta( 'user_email', $author_id );
				}
				
				$subject = WP_Freeio_Email::render_email_vars(array('job' => $job), 'employer_notice_expiring_job', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('job' => $job), 'employer_notice_expiring_job', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
				
			}
		}
	}

	public static function get_expiring_jobs($days_notice) {
		global $wpdb;
		$prefix = WP_FREEIO_JOB_LISTING_PREFIX;

		$notice_before_ts = current_time( 'timestamp' ) + ( DAY_IN_SECONDS * $days_notice );
		$job_ids          = $wpdb->get_col( $wpdb->prepare(
			"
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = %s
			AND postmeta.meta_value = %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'job_listing'
			",
			$prefix.'expiry_date',
			date( 'Y-m-d', $notice_before_ts )
		) );

		return $job_ids;
	}

	public static function check_for_expired_jobs() {
		global $wpdb;

		$prefix = WP_FREEIO_JOB_LISTING_PREFIX;
		
		// Change status to expired.
		$job_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
				LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
				WHERE postmeta.meta_key = %s
				AND postmeta.meta_value > 0
				AND postmeta.meta_value < %s
				AND posts.post_status = 'publish'
				AND posts.post_type = 'job_listing'",
				$prefix.'expiry_date',
				date( 'Y-m-d', current_time( 'timestamp' ) )
			)
		);

		if ( $job_ids ) {
			foreach ( $job_ids as $job_id ) {
				$job_data                = array();
				$job_data['ID']          = $job_id;
				$job_data['post_status'] = 'expired';
				wp_update_post( $job_data );
			}
		}

		// Delete old expired jobs.
		if ( apply_filters( 'wp_freeio_delete_expired_jobs', false ) ) {
			$job_ids = $wpdb->get_col(
				$wpdb->prepare( "
					SELECT posts.ID FROM {$wpdb->posts} as posts
					WHERE posts.post_type = 'job_listing'
					AND posts.post_modified < %s
					AND posts.post_status = 'expired'",
					date( 'Y-m-d', strtotime( '-' . apply_filters( 'wp_freeio_delete_expired_jobs_days', 30 ) . ' days', current_time( 'timestamp' ) ) )
				)
			);

			if ( $job_ids ) {
				foreach ( $job_ids as $job_id ) {
					wp_trash_post( $job_id );
				}
			}
		}
	}

	/**
	 * Deletes old previewed jobs after 30 days to keep the DB clean.
	 */
	public static function delete_old_previews() {
		global $wpdb;

		// Delete old expired jobs.
		$job_ids = $wpdb->get_col(
			$wpdb->prepare( "
				SELECT posts.ID FROM {$wpdb->posts} as posts
				WHERE posts.post_type = 'job_listing'
				AND posts.post_modified < %s
				AND posts.post_status = 'preview'",
				date( 'Y-m-d', strtotime( '-' . apply_filters( 'wp_freeio_delete_old_previews_jobs_days', 30 ) . ' days', current_time( 'timestamp' ) ) )
			)
		);

		if ( $job_ids ) {
			foreach ( $job_ids as $job_id ) {
				wp_delete_post( $job_id, true );
			}
		}
	}

	public static function job_statuses() {
		return apply_filters(
			'wp_freeio_job_listing_statuses',
			array(
				'draft'           => _x( 'Draft', 'post status', 'wp-freeio' ),
				'expired'         => _x( 'Expired', 'post status', 'wp-freeio' ),
				'preview'         => _x( 'Preview', 'post status', 'wp-freeio' ),
				'pending'         => _x( 'Pending approval', 'post status', 'wp-freeio' ),
				'pending_approve' => _x( 'Pending approval', 'post status', 'wp-freeio' ),
				'pending_payment' => _x( 'Pending payment', 'post status', 'wp-freeio' ),
				'publish'         => _x( 'Active', 'post status', 'wp-freeio' ),
			)
		);
	}

	public static function is_job_status_changing( $from_status, $to_status ) {
		return isset( $_POST['post_status'] ) && isset( $_POST['original_post_status'] ) && $_POST['original_post_status'] !== $_POST['post_status'] && ( null === $from_status || $from_status === $_POST['original_post_status'] ) && $to_status === $_POST['post_status'];
	}

	public static function calculate_job_expiry( $job_id ) {
		$duration = absint( wp_freeio_get_option( 'submission_duration' ) );
		$duration = apply_filters( 'wp-freeio-calculate-job-expiry', $duration, $job_id);

		if ( $duration ) {
			return date( 'Y-m-d', strtotime( "+{$duration} days", current_time( 'timestamp' ) ) );
		}

		return '';
	}

	public static function get_company_name( $post ) {
		$author_id = WP_Freeio_Job_Listing::get_author_id($post->ID);
		$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
		$ouput = '';
		if ( $employer_id ) {
			$ouput = get_the_title($employer_id);
		}
		return apply_filters('wp-freeio-get-company-name', $ouput, $post);
	}

	public static function get_company_name_html( $post ) {
		$author_id = WP_Freeio_Job_Listing::get_author_id($post->ID);
		$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
		$ouput = '';
		if ( $employer_id ) {
			$ouput = sprintf(wp_kses(__('<a href="%s" class="employer text-theme">%s</a>', 'wp-freeio'), array( 'a' => array('class' => array(), 'href' => array()) ) ), get_permalink($employer_id), get_the_title($employer_id) );
		}
		return apply_filters('wp-freeio-get-company-name-html', $ouput, $post);
	}

	public static function get_salary_html( $post_id = null, $html = true ) {
		$min_salary = self::get_min_salary_html($post_id, $html);
		$max_salary = self::get_max_salary_html($post_id, $html);
		$price_html = '';
		if ( $min_salary ) {
			$price_html = $min_salary;
		}
		if ( $max_salary ) {
			$price_html .= (!empty($price_html) ? ' - ' : '').$max_salary;
		}
		if ( $price_html ) {
			$salary_type = self::get_post_meta( $post_id, 'salary_type', true );

			$salary_type_html = '';
			switch ($salary_type) {
				case 'yearly':
					$salary_type_html = esc_html__(' per year', 'wp-freeio');
					break;
				case 'monthly':
					$salary_type_html = esc_html__(' per month', 'wp-freeio');
					break;
				case 'weekly':
					$salary_type_html = esc_html__(' per week', 'wp-freeio');
					break;
				case 'daily':
					$salary_type_html = esc_html__(' per day', 'wp-freeio');
					break;
				case 'hourly':
					$salary_type_html = esc_html__(' per hour', 'wp-freeio');
					break;
				default:
					$types = WP_Freeio_Mixes::get_default_salary_types();
					if ( !empty($types[$salary_type]) ) {
						$salary_type_html = ' / '.$types[$salary_type];
					}
					break;
			}
			$salary_type_html = apply_filters( 'wp-freeio-get-salary-type-html', $salary_type_html, $salary_type, $post_id );
			$price_html = $price_html.$salary_type_html;
		}
		return apply_filters( 'wp-freeio-get-salary-html', $price_html, $post_id );
	}

	public static function get_min_salary_html( $post_id = null, $html = true ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($post_id);
		if ( !$meta_obj->check_post_meta_exist('salary') ) {
			return false;
		}
		$price = $meta_obj->get_post_meta( 'salary' );

		if ( $price == '0' ) {
			$price = 0;
		} elseif ( empty( $price ) || ! is_numeric( $price ) ) {
			return false;
		}

		if ( !$html ) {
			$price = WP_Freeio_Price::format_price_without_html( $price );
		} else {
			$price = WP_Freeio_Price::format_price( $price );
		}

		return apply_filters( 'wp-freeio-get-min-salary-html', $price, $post_id, $html );
	}

	public static function get_max_salary_html( $post_id = null, $html = true ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($post_id);
		if ( !$meta_obj->check_post_meta_exist('max_salary') ) {
			return false;
		}
		$price = $meta_obj->get_post_meta( 'max_salary' );

		if ( $price == '0' ) {
			$price = 0;
		} elseif ( empty( $price ) || ! is_numeric( $price ) ) {
			return false;
		}
		if ( !$html ) {
			$price = WP_Freeio_Price::format_price_without_html( $price );
		} else {
			$price = WP_Freeio_Price::format_price( $price );
		}

		return apply_filters( 'wp-freeio-get-max-salary-html', $price, $post_id, $html );
	}
	
	public static function is_featured( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$featured = self::get_post_meta( $post_id, 'featured', true );
		$return = $featured ? true : false;
		return apply_filters( 'wp-freeio-job-listing-is-featured', $return, $post_id );
	}

	public static function is_urgent( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$urgent = self::get_post_meta( $post_id, 'urgent', true );
		$return = $urgent ? true : false;
		return apply_filters( 'wp-freeio-job-listing-is-urgent', $return, $post_id );
	}

	public static function is_filled( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$filled = self::get_post_meta( $post_id, 'filled', true );
		$return = $filled ? true : false;
		return apply_filters( 'wp-freeio-job-listing-is-filled', $return, $post_id );
	}

	public static function count_applicants( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$job_ids = array(0);
		
		$job_ida = apply_filters( 'wp-freeio-translations-post-ids', $post_id );
		if ( !empty($job_ida) && is_array($job_ida) ) {
			$job_ids = array_merge($job_ids, $job_ida );
		} else {
			$job_ids = array_merge($job_ids, array($post_id) );
		}
		
		$query_args = array(
			'post_type'         => 'job_applicant',
			'fields' 			=> 'ids',
			'posts_per_page'    => 1,
			'post_status'       => 'publish',
			'meta_query'       	=> array(
				array(
					'key' => WP_FREEIO_APPLICANT_PREFIX.'job_id',
					'value'     => $job_ids,
					'compare'   => 'IN',
				)
			)
		);
		$applicants = new WP_Query( $query_args );
		
		return $applicants->found_posts;
	}

	public static function get_job_taxs( $post_id = null, $tax = 'job_listing_category' ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$types = get_the_terms( $post_id, $tax );
		return $types;
	}

	public static function get_job_types_html( $post_id = null ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}
		$output = '';
		$types = self::get_job_taxs( $post_id, 'job_listing_type' );
		if ( $types ) {
            foreach ($types as $term) {
                $output .= '<a href="'.get_term_link($term).'">'.wp_kses_post($term->name).'</a>';
            }
        }
		return apply_filters( 'wp-freeio-get-job-types-html', $output, $post_id );
	}

	public static function check_can_apply_social( $post_id = null ) {
		$apply_type = self::get_post_meta( $post_id, 'apply_type', true );
		$application_deadline_date = self::get_post_meta( $post_id, 'application_deadline_date', true );
		if ( empty($application_deadline_date) || strtotime($application_deadline_date) >= strtotime('today') ) {
			if ( $apply_type == 'internal' && !is_user_logged_in() ) {
				return true;
			}
		}
		return false;
	}

	public static function display_apply_job_btn( $post_id = null ) {
		$apply_type = self::get_post_meta( $post_id, 'apply_type', true );
		$application_deadline_date = self::get_post_meta( $post_id, 'application_deadline_date', true );
		
		if ( empty($application_deadline_date) || date('Y-m-d', strtotime($application_deadline_date)) >= date('Y-m-d', strtotime('now')) ) {
			if ( $application_deadline_date ) {
				$deadline_date = strtotime($application_deadline_date);
				?>
				<div class="deadline-time"><?php echo sprintf(__('Application ends: <strong>%s</strong>', 'wp-freeio'), date_i18n(get_option('date_format'), $deadline_date)); ?></div>
				<?php
			}
			
			$filled = self::get_post_meta($post_id, 'filled');
			$filled_class = $filled ? 'filled' : '';

			if ( $apply_type == 'external' ) {
				$apply_url = self::get_post_meta( $post_id, 'apply_url', true );
				if ( !empty($apply_url) ) {
					$apply_without_login = wp_freeio_get_option('freelancer_apply_job_without_login', 'off');
					if ( is_user_logged_in() || $apply_without_login == 'on' ) {
						?>
						<a href="<?php echo esc_url($apply_url); ?>" target="_blank" rel="nofollow, noindex" class="btn btn-apply btn-apply-job-external <?php echo esc_attr($filled_class); ?>"><?php esc_html_e('Apply Now', 'wp-freeio'); ?><i class="next flaticon-right-up"></i></a>
						<?php
					} else {
						?>
						<a href="javascript:void(0);" class="btn btn-apply btn-apply-job-internal-required <?php echo esc_attr($filled_class); ?>"><?php esc_html_e('Apply Now', 'wp-freeio'); ?><i class="next flaticon-right-up"></i></a>
						<?php
						echo WP_Freeio_Template_Loader::get_template_part('single-job_listing/apply-internal-required');
					}
				}
			} elseif ( $apply_type == 'with_email' ) {
				$apply_without_login = wp_freeio_get_option('freelancer_apply_job_without_login', 'off');
				if ( is_user_logged_in() || $apply_without_login == 'on' ) {
				?>
					<a href="#job-apply-email-form-wrapper-<?php echo esc_attr($post_id); ?>" class="btn btn-apply btn-apply-job-email <?php echo esc_attr($filled_class); ?>" data-job_id="<?php echo esc_attr($post_id); ?>"><?php esc_html_e('Apply Now', 'wp-freeio'); ?><i class="next flaticon-right-up"></i></a>
					<!-- email apply form here -->
					<?php
					echo WP_Freeio_Template_Loader::get_template_part('single-job_listing/apply-email-form');
				} else {
					?>
					<a href="javascript:void(0);" class="btn btn-apply btn-apply-job-internal-required <?php echo esc_attr($filled_class); ?>"><?php esc_html_e('Apply Now', 'wp-freeio'); ?><i class="next flaticon-right-up"></i></a>
					<?php
					echo WP_Freeio_Template_Loader::get_template_part('single-job_listing/apply-internal-required');
				}
			}  elseif ( $apply_type == 'call' ) {
				$apply_without_login = wp_freeio_get_option('freelancer_apply_job_without_login', 'off');
				if ( is_user_logged_in() || $apply_without_login == 'on' ) {
				?>
					<a href="#job-apply-call-form-wrapper-<?php echo esc_attr($post_id); ?>" class="btn btn-apply btn-apply-job-call <?php echo esc_attr($filled_class); ?>" data-job_id="<?php echo esc_attr($post_id); ?>"><?php esc_html_e('Apply Now', 'wp-freeio'); ?><i class="next flaticon-right-up"></i></a>
					<!-- email apply form here -->
					<?php
					echo WP_Freeio_Template_Loader::get_template_part('single-job_listing/apply-call-form');
				} else {
					?>
					<a href="javascript:void(0);" class="btn btn-apply btn-apply-job-internal-required <?php echo esc_attr($filled_class); ?>"><?php esc_html_e('Apply Now', 'wp-freeio'); ?><i class="next flaticon-right-up"></i></a>
					<?php
					echo WP_Freeio_Template_Loader::get_template_part('single-job_listing/apply-internal-required');
				}
			} else {
				if ( !is_user_logged_in() || !WP_Freeio_User::is_freelancer() ) {
					$apply_without_login = wp_freeio_get_option('freelancer_apply_job_without_login', 'off');
					if ( $apply_without_login == 'on' ) {
						?>
						<a href="#job-apply-internal-without-login-form-wrapper-<?php echo esc_attr($post_id); ?>" class="btn btn-apply btn-apply-job-internal-without-login <?php echo esc_attr($filled_class); ?>"><?php esc_html_e('Apply Now', 'wp-freeio'); ?><i class="next flaticon-right-up"></i></a>
						<?php
						echo WP_Freeio_Template_Loader::get_template_part('single-job_listing/apply-internal-without-login');
					} else {
						?>
						<a href="javascript:void(0);" class="btn btn-apply btn-apply-job-internal-required <?php echo esc_attr($filled_class); ?>"><?php esc_html_e('Apply Now', 'wp-freeio'); ?><i class="next flaticon-right-up"></i></a>
						<?php
						echo WP_Freeio_Template_Loader::get_template_part('single-job_listing/apply-internal-required');
					}
				} else {
					$rand = WP_Freeio_Mixes::random_key();
					$class = 'btn-apply-job-internal';
					$text = esc_html__('Apply Now', 'wp-freeio').'<i class="next flaticon-right-up"></i>';
					$url = '#job-apply-internal-form-wrapper-'.esc_attr($rand);

					$user_id = WP_Freeio_User::get_user_id();
					$check_applied = WP_Freeio_Freelancer::check_applied($user_id, $post_id);
					if ( $check_applied ) {
						$class = 'btn-applied-job-internal';
						$text = esc_html__('Applied', 'wp-freeio');
						$url = 'javascript:void(0);';
					}
					?>
					<a href="<?php echo trim($url); ?>" class="btn btn-apply <?php echo esc_attr($class); ?> <?php echo esc_attr($filled_class); ?>" data-job_id="<?php echo esc_attr($post_id); ?>"><?php echo trim($text); ?></a>
					<?php
					if ( !$check_applied ) {
						echo WP_Freeio_Template_Loader::get_template_part('single-job_listing/apply-internal-form', array('rand' => $rand));
					}
				}
			}
			
		} else {
			?>
			<div class="deadline-closed"><?php esc_html_e('Application deadline closed.', 'wp-freeio'); ?></div>
			<?php
		}
	}

	public static function display_jobs_results_filters() {
		$filters = WP_Freeio_Abstract_Filter::get_filters();

		echo WP_Freeio_Template_Loader::get_template_part('loop/job/results-filters', array('filters' => $filters));
	}

	public static function display_jobs_count_results($wp_query) {
		$total = $wp_query->found_posts;
		$per_page = $wp_query->query_vars['posts_per_page'];
		$current = max( 1, $wp_query->get( 'paged', 1 ) );
		$args = array(
			'total' => $total,
			'per_page' => $per_page,
			'current' => $current,
		);

		echo WP_Freeio_Template_Loader::get_template_part('loop/job/results-count', $args);
	}

	public static function display_jobs_alert_form() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/job/jobs-alert-form');
	}

	public static function display_jobs_orderby() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/job/orderby');
	}

	public static function display_jobs_alert_orderby_start() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/job/alert-orderby-start');
	}

	public static function display_jobs_alert_orderby_end() {
		echo WP_Freeio_Template_Loader::get_template_part('loop/job/alert-orderby-end');
	}

	public static function job_feed_url($values = null, $exclude = array(), $current_key = '', $page_rss_url = '', $return = false) {
		if ( empty($page_rss_url) ) {
			$page_rss_url = home_url('/') . '?feed=job_listing_feed';
		}
		if ( is_null( $values ) ) {
			$values = $_GET; // WPCS: input var ok, CSRF ok.
		} elseif ( is_string( $values ) ) {
			$url_parts = wp_parse_url( $values );
			$values    = array();

			if ( ! empty( $url_parts['query'] ) ) {
				parse_str( $url_parts['query'], $values );
			}
		}
		foreach ( $values as $key => $value ) {
			if ( in_array( $key, $exclude, true ) ) {
				continue;
			}
			if ( $current_key ) {
				$key = $current_key . '[' . $key . ']';
			}
			if ( is_array( $value ) ) {
				$page_rss_url = self::job_feed_url( $value, $exclude, $key, $page_rss_url, true );
			} else {
				$page_rss_url = add_query_arg($key, wp_unslash( $value ), $page_rss_url);
			}
		}

		if ( $return ) {
			return $page_rss_url;
		}

		echo $page_rss_url;
	}

	public static function display_job_feed(){
		echo WP_Freeio_Template_Loader::get_template_part('loop/job/jobs-rss-btn');
	}


	// check view
	public static function check_view_job_detail() {
		global $post;
		$restrict_type = wp_freeio_get_option('job_restrict_type', '');
		$view = wp_freeio_get_option('job_restrict_detail', 'all');
		
		$return = true;
		if ( $restrict_type == 'view' ) {
			$author_id = WP_Freeio_Job_Listing::get_author_id($post->ID);
			$user_id = WP_Freeio_User::get_user_id();
			if ( $user_id == $author_id ) {
				$return = true;
			} else {
				switch ($view) {
					case 'always_hidden':
						$return = false;
						break;
					case 'register_user':
						$return = false;
						if ( is_user_logged_in() ) {
							$return = true;
						}
						break;
					case 'register_freelancer':
						$return = false;
						if ( is_user_logged_in() ) {
							if ( WP_Freeio_User::is_freelancer($user_id) ) {
								$return = true;
							}
						}
						break;
					default:
						$return = true;
						break;
				}
			}
		}
		return apply_filters('wp-freeio-check-view-job-detail', $return, $post);
	}

	public static function job_restrict_listing_query($query, $filter_params) {
		$query_vars = $query->query_vars;
		$query_vars = self::job_restrict_listing_query_args($query_vars, $filter_params);
		$query->query_vars = $query_vars;
		
		return apply_filters('wp-freeio-check-view-job-listing-query', $query);
	}

	public static function job_restrict_listing_query_args($query_args, $filter_params) {

		$restrict_type = wp_freeio_get_option('job_restrict_type', '');
		
		if ( $restrict_type == 'view' ) {
			$view = wp_freeio_get_option('job_restrict_listing', 'all');
			
			$user_id = WP_Freeio_User::get_user_id();
			switch ($view) {
				case 'always_hidden':
					$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
					$meta_query[] = array(
						'key'       => 'job_restrict_listing',
						'value'     => 'always_hidden',
						'compare'   => '==',
					);
					$query_args['meta_query'] = $meta_query;
					break;
				case 'register_user':
					if ( !is_user_logged_in() ) {
						$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
						$meta_query[] = array(
							'key'       => 'job_restrict_listing',
							'value'     => 'register_user',
							'compare'   => '==',
						);
						$query_args['meta_query'] = $meta_query;
					}
					break;
				case 'register_freelancer':
					$return = false;
					if ( is_user_logged_in() ) {
						if ( WP_Freeio_User::is_freelancer($user_id) ) {
							$return = true;
						}
					}
					if ( !$return ) {
						$meta_query = !empty($query_args['meta_query']) ? $query_args['meta_query'] : array();
						$meta_query[] = array(
							'key'       => 'job_restrict_listing',
							'value'     => 'register_freelancer',
							'compare'   => '==',
						);
						$query_args['meta_query'] = $meta_query;
					}
					break;
			}
		}
		return apply_filters('wp-freeio-check-view-job_listing-listing-query-args', $query_args);
	}

}
WP_Freeio_Job_Listing::init();