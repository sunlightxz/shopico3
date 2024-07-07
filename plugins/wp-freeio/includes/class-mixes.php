<?php
/**
 * Mixes
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Mixes {
	
	public static function init() {
		add_action( 'login_form', array( __CLASS__, 'social_login_before' ), 1 );
		add_action( 'login_form', array( __CLASS__, 'social_login_after' ), 30 );

		add_action( 'register_form_after', array( __CLASS__, 'social_login_before' ), 1 );
		add_action( 'register_form_after', array( __CLASS__, 'social_login_after' ), 30 );

		add_filter( 'wp_freeio_filter_distance_type', array( __CLASS__, 'set_distance_type' ), 10 );

		add_filter( 'wp_freeio_cmb2_field_taxonomy_location_number', array( __CLASS__, 'set_location_number' ), 10 );
		add_filter( 'wp_freeio_cmb2_field_taxonomy_location_field_name_1', array( __CLASS__, 'set_first_location_label' ), 10 );
		add_filter( 'wp_freeio_cmb2_field_taxonomy_location_field_name_2', array( __CLASS__, 'set_second_location_label' ), 10 );
		add_filter( 'wp_freeio_cmb2_field_taxonomy_location_field_name_3', array( __CLASS__, 'set_third_location_label' ), 10 );
		add_filter( 'wp_freeio_cmb2_field_taxonomy_location_field_name_4', array( __CLASS__, 'set_fourth_location_label' ), 10 );
	}

	/**
	 * Formats number by currency settings
	 *
	 * @access public
	 * @param $price
	 * @return bool|string
	 */
	public static function format_number( $price, $decimals = false, $money_decimals = 0  ) {
		if ( empty( $price ) || ! is_numeric( $price ) ) {
			return 0;
		}
		if ( !$decimals ) {
            $money_decimals = wp_freeio_get_option('money_decimals');
        }
		$money_thousands_separator = wp_freeio_get_option('money_thousands_separator');
		$money_dec_point = wp_freeio_get_option('money_dec_point');

		$price_parts_dot = explode( '.', $price );
		$price_parts_col = explode( ',', $price );

		if ( count( $price_parts_dot ) > 1 || count( $price_parts_col ) > 1 ) {
			$decimals = ! empty( $money_decimals ) ? $money_decimals : '0';
		} else {
			$decimals = 0;
		}

		$dec_point = ! empty( $money_dec_point ) ? $money_dec_point : '.';
		$thousands_separator = ! empty( $money_thousands_separator ) ? $money_thousands_separator : '';

		$price = number_format( $price, $decimals, $dec_point, $thousands_separator );

		return $price;
	}

	public static function is_allowed_to_remove( $user_id, $item_id ) {
		$author_id = get_post_field( 'post_author', $item_id );
		
		if ( ! empty( $author_id ) ) {
			return $author_id == $user_id ;
		}

		return false;
	}
	
	public static function is_allowed_to_remove_service( $user_id, $item_id ) {
		$author_id = WP_Freeio_Service::get_author_id($item_id);
		
		if ( ! empty( $author_id ) ) {
			return $author_id == $user_id ;
		}

		return false;
	}

	public static function is_allowed_to_remove_project( $user_id, $item_id ) {
		$author_id = WP_Freeio_Project::get_author_id($item_id);
		
		if ( ! empty( $author_id ) ) {
			return $author_id == $user_id ;
		}

		return false;
	}

	public static function is_allowed_to_remove_proposal( $user_id, $item_id ) {
		$item = get_post( $item_id );
		$author_id = get_post_field('post_author', $item_id);
		
		if ( $user_id == $author_id ) {
			return true;
		} else {
			$project_id = get_post_meta( $proposal_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true );
			$project_employer_id = get_post_field('post_author', $project_id);
			if ( $user_id == $project_employer_id ) {
				return true;
			}
		}

		return false;
	}

	public static function redirect($redirect_url) {
		if ( ! $redirect_url ) {
			$redirect_url = home_url( '/' );
		}

		wp_redirect( $redirect_url );
		exit();
	}

	public static function sort_array_by_priority( $a, $b ) {
		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}

		return ( $a['priority'] < $b['priority'] ) ? - 1 : 1;
	}
	
	public static function get_the_level($id, $type = 'property_location') {
	  	return count( get_ancestors($id, $type) );
	}

	public static function get_default_salary_types() {
		return apply_filters( 'wp-freeio-get-default-salary-types', array(
			'monthly' => __( 'Monthly', 'wp-freeio' ),
			'weekly' => __( 'Weekly', 'wp-freeio' ),
			'daily' => __( 'Daily', 'wp-freeio' ),
			'hourly' => __( 'Hourly', 'wp-freeio' ),
			'yearly' => __( 'Yearly', 'wp-freeio' ),
		));
	}

	public static function get_default_service_price_types() {
		return apply_filters( 'wp-freeio-get-default-service-price-types', array(
			'price' => __( 'Price', 'wp-freeio' ),
			'package' => __( 'Package', 'wp-freeio' ),
		));
	}

	public static function get_default_project_types() {
		return apply_filters( 'wp-freeio-get-default-project-types', array(
			'fixed' => __( 'Fixed project', 'wp-freeio' ),
			'hourly' => __( 'Hourly Based Project', 'wp-freeio' ),
		));
	}

	public static function get_default_apply_types() {
		return apply_filters( 'wp-freeio-get-default-apply-types', array(
			'internal' => __( 'Internal', 'wp-freeio' ),
            'external' => __( 'External URL', 'wp-freeio' ),
            'with_email' => __( 'By Email', 'wp-freeio' ),
            'call' => __( 'Call To Apply', 'wp-freeio' ),
		));
	}

	public static function get_default_withdraw_payout_methods() {
		return apply_filters( 'wp-freeio-payout-methods', array(
			'paypal' => __( 'Paypal', 'wp-freeio' ),
			'bacs' => __( 'Bank Transfer', 'wp-freeio' ),
			'payoneer' => __( 'Payoneer', 'wp-freeio' ),
		));
	}

	public static function get_default_bank_transfer_fields() {
		return apply_filters( 'wp-freeio-bank-transfer-fields', array(
			'bank_account_name' => __( 'Bank Account Name', 'wp-freeio' ),
			'bank_account_number' => __( 'Bank Account Number', 'wp-freeio' ),
			'bank_name' => __( 'Bank Name', 'wp-freeio' ),
			'bank_routing_number' => __( 'Bank Routing Number', 'wp-freeio' ),
			'bank_iban' => __( 'Bank IBAN', 'wp-freeio' ),
			'bank_bic_swift' => __( 'Bank BIC/SWIFT', 'wp-freeio' ),
		));
	}
	
	public static function get_default_comissions_tiers_range() {
		return apply_filters( 'wp-freeio-comissions-tiers-range', array(
			'1-500' => esc_html__('$0 - $500', 'wp-freeio'),
			'501-1000' => esc_html__('$501 - $1000', 'wp-freeio'),
			'1001-2000' => esc_html__('$1001 - $2000', 'wp-freeio'),
			'2001-3000' => esc_html__('$2001 - $3000', 'wp-freeio'),
			'3001-4000' => esc_html__('$3001 - $4000', 'wp-freeio'),
			'4001-5000' => esc_html__('$4001 - $5000', 'wp-freeio'),
			'5001-10000' => esc_html__('$5001 - $10,000', 'wp-freeio'),
			'10001-20000' => esc_html__('$10,001 - $20,000', 'wp-freeio'),
			'20001-30000' => esc_html__('$20,001 - $30,000', 'wp-freeio'),
			'30001-40000' => esc_html__('$30,001 - $40,000', 'wp-freeio'),
			'40001-50000' => esc_html__('$40,001 - $50,000', 'wp-freeio'),
			'50001-60000' => esc_html__('$50,001 - $60,000', 'wp-freeio'),
			'60001-70000' => esc_html__('$60,001 - $70,000', 'wp-freeio'),
			'70001-80000' => esc_html__('$70,001 - $80,000', 'wp-freeio'),
			'80001-90000' => esc_html__('$80,001 - $90,000', 'wp-freeio'),
			'90001-100000' => esc_html__('$90,001 - $100,000', 'wp-freeio'),
			'100001' => esc_html__('Greater or equal to $100,000', 'wp-freeio'),
		));
	}

	public static function get_image_mime_types() {
		return apply_filters( 'wp-freeio-get-image-mime-types', array(
			'jpg'         => 'image/jpeg',
			'jpeg'        => 'image/jpeg',
			'jpe'         => 'image/jpeg',
			'gif'         => 'image/gif',
			'png'         => 'image/png',
			'bmp'         => 'image/bmp',
			'tif|tiff'    => 'image/tiff',
			'ico'         => 'image/x-icon',
		));
	}

	public static function get_cv_mime_types() {
		return apply_filters( 'wp-freeio-get-cv-mime-types', array(
			'txt'         => 'text/plain',
			'doc'         => 'application/msword',
			'docx'        => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xlsx'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xls'         => 'application/vnd.ms-excel',
			'pdf'         => 'application/pdf',
		));
	}

	public static function get_socials_network() {
		return apply_filters( 'wp-freeio-get-socials-network', array(
			'facebook' => array(
				'title' => esc_html__('Facebook', 'wp-freeio'),
				'icon' => 'fab fa-facebook-f',
			),
			'twitter' => array(
				'title' => esc_html__('Twitter', 'wp-freeio'),
				'icon' => 'fab fa-x-twitter',
			),
			'linkedin' => array(
				'title' => esc_html__('Linkedin', 'wp-freeio'),
				'icon' => 'fab fa-linkedin-in',
			),
			'dribbble' => array(
				'title' => esc_html__('Dribbble', 'wp-freeio'),
				'icon' => 'fab fa-dribbble',
			),
			'tumblr' => array(
				'title' => esc_html__('Tumblr', 'wp-freeio'),
				'icon' => 'fab fa-tumblr',
			),
			'pinterest' => array(
				'title' => esc_html__('Pinterest', 'wp-freeio'),
				'icon' => 'fab fa-pinterest',
			),
			'instagram' => array(
				'title' => esc_html__('Instagram', 'wp-freeio'),
				'icon' => 'fab fa-instagram',
			),
			'youtube' => array(
				'title' => esc_html__('Youtube', 'wp-freeio'),
				'icon' => 'fab fa-youtube',
			),
			'tiktok' => array(
				'title' => esc_html__('Tiktok', 'wp-freeio'),
				'icon' => 'fab fa-tiktok',
			),
			'telegram' => array(
				'title' => esc_html__('Telegram', 'wp-freeio'),
				'icon' => 'fab fa-telegram',
			),
			'discord' => array(
				'title' => esc_html__('Discord', 'wp-freeio'),
				'icon' => 'fab fa-discord',
			),
		));
	}

	public static function get_jobs_page_url() {
		if ( is_post_type_archive('job_listing') ) {
			$url = get_post_type_archive_link( 'job_listing' );
		} elseif (is_tax()) {
			$url = '';
			$taxs = ['type', 'category', 'location', 'tag'];
			foreach ($taxs as $tax) {
				if ( is_tax('job_listing_'.$tax) ) {
					global $wp_query;
					$term = $wp_query->queried_object;
					if ( isset( $term->slug) ) {
						$url = get_term_link($term, 'job_listing_'.$tax);
					}
				}
			}
		} else {
			global $post;
			if ( is_page() && is_object($post) && (basename( get_page_template() ) == 'page-jobs.php' || basename( get_page_template() ) == 'page-jobs-elementor.php') ) {
				$url = get_permalink($post->ID);
			} else {
				$jobs_page_id = wp_freeio_get_option('jobs_page_id');
				$jobs_page_id = self::get_lang_post_id( $jobs_page_id, 'page');
				if ( $jobs_page_id ) {
					$url = get_permalink($jobs_page_id);
				} else {
					$url = get_post_type_archive_link( 'job_listing' );
				}
			}
		}
		return apply_filters( 'wp-freeio-get-jobs-page-url', $url );
	}

	public static function get_services_page_url() {
		if ( is_post_type_archive('service') ) {
			$url = get_post_type_archive_link( 'service' );
		} elseif (is_tax()) {
			$url = '';
			$taxs = ['category', 'location', 'tag'];
			foreach ($taxs as $tax) {
				if ( is_tax('service_'.$tax) ) {
					global $wp_query;
					$term = $wp_query->queried_object;
					if ( isset( $term->slug) ) {
						$url = get_term_link($term, 'service_'.$tax);
					}
				}
			}
		} else {
			global $post;
			if ( is_page() && is_object($post) && (basename( get_page_template() ) == 'page-services.php' || basename( get_page_template() ) == 'page-services-elementor.php') ) {
				$url = get_permalink($post->ID);
			} else {
				$services_page_id = wp_freeio_get_option('services_page_id');
				$services_page_id = self::get_lang_post_id( $services_page_id, 'page');
				if ( $services_page_id ) {
					$url = get_permalink($services_page_id);
				} else {
					$url = get_post_type_archive_link( 'service' );
				}
			}
		}
		return apply_filters( 'wp-freeio-get-services-page-url', $url );
	}

	public static function get_projects_page_url() {
		if ( is_post_type_archive('project') ) {
			$url = get_post_type_archive_link( 'project' );
		} elseif (is_tax()) {
			$url = '';
			$taxs = ['category', 'location', 'skill', 'duration', 'experience', 'freelancer_type', 'language', 'level'];
			foreach ($taxs as $tax) {
				if ( is_tax('project_'.$tax) ) {
					global $wp_query;
					$term = $wp_query->queried_object;
					if ( isset( $term->slug) ) {
						$url = get_term_link($term, 'project_'.$tax);
					}
				}
			}
		} else {
			global $post;
			if ( is_page() && is_object($post) && (basename( get_page_template() ) == 'page-projects.php' || basename( get_page_template() ) == 'page-projects-elementor.php') ) {
				$url = get_permalink($post->ID);
			} else {
				$projects_page_id = wp_freeio_get_option('projects_page_id');
				$projects_page_id = self::get_lang_post_id( $projects_page_id, 'page');
				if ( $projects_page_id ) {
					$url = get_permalink($projects_page_id);
				} else {
					$url = get_post_type_archive_link( 'project' );
				}
			}
		}
		return apply_filters( 'wp-freeio-get-projects-page-url', $url );
	}

	public static function get_employers_page_url() {
		if ( is_post_type_archive('employer') ) {
			$url = get_post_type_archive_link( 'employer' );
		} elseif (is_tax()) {
			$url = '';
			$taxs = ['category', 'location'];
			foreach ($taxs as $tax) {
				if ( is_tax('employer_'.$tax) ) {
					global $wp_query;
					$term = $wp_query->queried_object;
					if ( isset( $term->slug) ) {
						$url = get_term_link($term, 'employer_'.$tax);
					}
				}
			}
		} else {
			global $post;
			if ( is_page() && is_object($post) && (basename( get_page_template() ) == 'page-employers.php' || basename( get_page_template() ) == 'page-employers-elementor.php') ) {
				$url = get_permalink($post->ID);
			} else {
				$employers_page_id = wp_freeio_get_option('employers_page_id');
				$employers_page_id = self::get_lang_post_id( $employers_page_id, 'page');
				if ( $employers_page_id ) {
					$url = get_permalink($employers_page_id);
				} else {
					$url = get_post_type_archive_link( 'employer' );
				}
			}
		}
		return apply_filters( 'wp-freeio-get-employers-page-url', $url );
	}

	public static function get_freelancers_page_url() {
		if ( is_post_type_archive('freelancer') ) {
			$url = get_post_type_archive_link( 'freelancer' );
		} elseif (is_tax()) {
			$url = '';
			$taxs = ['category', 'location'];
			foreach ($taxs as $tax) {
				if ( is_tax('freelancer_'.$tax) ) {
					global $wp_query;
					$term = $wp_query->queried_object;
					if ( isset( $term->slug) ) {
						$url = get_term_link($term, 'freelancer_'.$tax);
					}
				}
			}
		} else {
			global $post;
			if ( is_page() && is_object($post) && (basename( get_page_template() ) == 'page-freelancers.php' || basename( get_page_template() ) == 'page-freelancers-elementor.php') ) {
				$url = get_permalink($post->ID);
			} else {
				$freelancers_page_id = wp_freeio_get_option('freelancers_page_id');
				$freelancers_page_id = self::get_lang_post_id( $freelancers_page_id, 'page');
				if ( $freelancers_page_id ) {
					$url = get_permalink($freelancers_page_id);
				} else {
					$url = get_post_type_archive_link( 'freelancer' );
				}
			}
		}
		return apply_filters( 'wp-freeio-get-freelancers-page-url', $url );
	}

	public static function get_lang_post_id($post_id, $post_type = 'page') {
	    return apply_filters( 'wp-freeio-post-id', $post_id, $post_type);
	}

	public static function custom_pagination( $args = array() ) {
    	global $wp_rewrite;
        
        $args = wp_parse_args( $args, array(
			'prev_text' => '<i class="ti-angle-left"></i>'.esc_html__('Prev', 'wp-freeio'),
			'next_text' => esc_html__('Next','wp-freeio').'<i class="ti-angle-right"></i>',
			'max_num_pages' => 10,
			'echo' => true,
			'class' => '',
		));

        if ( !empty($args['wp_query']) ) {
        	$wp_query = $args['wp_query'];
        } else {
        	global $wp_query;
        }

        if ( $wp_query->max_num_pages < 2 ) {
			return;
		}

    	$pages = $args['max_num_pages'];

    	$current = !empty($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 1 ? $wp_query->query_vars['paged'] : 1;
        if ( empty($pages) ) {
            global $wp_query;
            $pages = $wp_query->max_num_pages;
            if ( !$pages ) {
                $pages = 1;
            }
        }
        $pagination = array(
            'base' => @add_query_arg('paged','%#%'),
            'format' => '',
            'total' => $pages,
            'current' => $current,
            'prev_text' => $args['prev_text'],
            'next_text' => $args['next_text'],
            'type' => 'array'
        );

		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		$add_args = array();
		if ( !empty($query_args) ) {
			foreach ($query_args as $key => $value) {
				if ( is_array($value) ) {
					$add_args[$key] = array_map( 'urlencode', $value );
				} else {
					$add_args[$key] = $value;
				}
			}
		}

		$pagination['base'] = $pagenum_link;
		$pagination['format'] = $format;
		$pagination['add_args'] = $add_args;
        

        $sq = '';
        if ( isset($_GET['s']) ) {
            $cq = $_GET['s'];
            $sq = str_replace(" ", "+", $cq);
        }
        
        if ( !empty($wp_query->query_vars['s']) ) {
            $pagination['add_args'] = array( 's' => $sq);
        }
        $pagination = apply_filters( 'wp-freeio-custom-pagination', $pagination );

        $paginations = paginate_links( $pagination );
        $output = '';
        if ( !empty($paginations) ) {
            $output .= '<ul class="pagination '.esc_attr( $args["class"] ).'">';
                foreach ($paginations as $key => $pg) {
                    $output .= '<li>'. $pg .'</li>';
                }
            $output .= '</ul>';
        }
    	
        if ( $args["echo"] ) {
        	echo wp_kses_post($output);
        } else {
        	return $output;
        }
    }

    public static function custom_pagination2( $args = array() ) {
    	global $wp_rewrite;
        
        $args = wp_parse_args( $args, array(
			'prev_text' => '<i class="ti-angle-left"></i>'.esc_html__('Prev', 'wp-freeio'),
			'next_text' => esc_html__('Next','wp-freeio').'<i class="ti-angle-right"></i>',
			'echo' => true,
			'class' => '',
			'per_page' => '',
			'max_num_pages' => '',
			'current' => '',
		));

        if ( $args['max_num_pages'] < 2 ) {
			return;
		}

    	$pages = $args['max_num_pages'];

    	$current = !empty($args['current']) && $args['current'] > 1 ? $args['current'] : 1;
        
        $pagination = array(
            'base' => @add_query_arg('paged','%#%'),
            'format' => '',
            'total' => $pages,
            'current' => $current,
            'prev_text' => $args['prev_text'],
            'next_text' => $args['next_text'],
            'type' => 'array'
        );

		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts    = explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
		$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

		$format  = $wp_rewrite->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $wp_rewrite->using_permalinks() ? user_trailingslashit( $wp_rewrite->pagination_base . '/%#%', 'paged' ) : '?paged=%#%';

		$add_args = array();
		if ( !empty($query_args) ) {
			foreach ($query_args as $key => $value) {
				if ( is_array($value) ) {
					$add_args[$key] = array_map( 'urlencode', $value );
				} else {
					$add_args[$key] = $value;
				}
			}
		}

		$pagination['base'] = $pagenum_link;
		$pagination['format'] = $format;
		$pagination['add_args'] = $add_args;
        
        $sq = '';
        if ( isset($_GET['s']) ) {
            $cq = $_GET['s'];
            $sq = str_replace(" ", "+", $cq);
        }
        
        $pagination = apply_filters( 'wp-freeio-custom-pagination2', $pagination );

        $paginations = paginate_links( $pagination );
        $output = '';
        if ( !empty($paginations) ) {
            $output .= '<ul class="pagination '.esc_attr( $args["class"] ).'">';
                foreach ($paginations as $key => $pg) {
                    $output .= '<li>'. $pg .'</li>';
                }
            $output .= '</ul>';
        }
    	
        if ( $args["echo"] ) {
        	echo wp_kses_post($output);
        } else {
        	return $output;
        }
    }

    public static function query_string_form_fields( $values = null, $exclude = array(), $current_key = '', $return = false ) {
		if ( is_null( $values ) ) {
			$values = $_GET; // WPCS: input var ok, CSRF ok.
		} elseif ( is_string( $values ) ) {
			$url_parts = wp_parse_url( $values );
			$values    = array();

			if ( ! empty( $url_parts['query'] ) ) {
				parse_str( $url_parts['query'], $values );
			}
		}
		$html = '';

		foreach ( $values as $key => $value ) {
			if ( in_array( $key, $exclude, true ) ) {
				continue;
			}
			if ( $current_key ) {
				$key = $current_key . '[' . $key . ']';
			}
			if ( is_array( $value ) ) {
				$html .= self::query_string_form_fields( $value, $exclude, $key, true );
			} else {
				$html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '" />';
			}
		}

		if ( $return ) {
			return $html;
		}

		echo $html; // WPCS: XSS ok.
	}

	public static function is_ajax_request() {
	    if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
	        return true;
	    }
	    return false;
	}

	public static function get_full_current_url() {
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
		    $link = "https"; 
		} else {
		    $link = "http"; 
		}
		  
		// Here append the common URL characters. 
		$link .= "://"; 
		  
		// Append the host(domain name, ip) to the URL. 
		$link .= $_SERVER['HTTP_HOST']; 
		  
		// Append the requested resource location to the URL 
		$link .= $_SERVER['REQUEST_URI']; 
		      
		// Print the link 
		return $link; 
	}

	public static function check_social_login_enable() {
		$facebook = WP_Freeio_Social_Facebook::get_instance();
		$google = WP_Freeio_Social_Google::get_instance();
		$linkedin = WP_Freeio_Social_Linkedin::get_instance();
		$twitter = WP_Freeio_Social_Twitter::get_instance();
		if ( $facebook->is_facebook_login_enabled() || $google->is_google_login_enabled() || $linkedin->is_linkedin_login_enabled() || $twitter->is_twitter_login_enabled() ) {
			return true;
		}
		return false;
	}

	public static function social_login_before(){
		if ( self::check_social_login_enable() ) {
	        echo '<div class="wrapper-social-login"><div class="line-header"><span>'.esc_html__('or', 'wp-freeio').'</span></div><div class="inner-social">';
	    }
    }
	
	public static function social_login_after(){
		if ( self::check_social_login_enable() ) {
	        echo '</div></div>';
	    }
    }

    public static function set_distance_type($distance_unit) {
    	$unit = wp_freeio_get_option('distance_unit', 'miles');
    	if ( in_array($unit, array('miles', 'km')) ) {
    		$distance_unit = $unit;
    	}
    	return $distance_unit;
    }

    public static function random_key($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $return = '';
        for ($i = 0; $i < $length; $i++) {
            $return .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $return;
    }

    public static function set_location_number($nb) {
    	$nb_fields = wp_freeio_get_option('location_nb_fields', 1);
    	return $nb_fields;
    }

    public static function set_first_location_label($label) {
    	return wp_freeio_get_option('location_1_field_label', $label);
    }

    public static function set_second_location_label($label) {
    	return wp_freeio_get_option('location_2_field_label', $label);
    }

    public static function set_third_location_label($label) {
    	return wp_freeio_get_option('location_3_field_label', $label);
    }

    public static function set_fourth_location_label($label) {
    	return wp_freeio_get_option('location_4_field_label', $label);
    }

    public static function get_post_id_by_meta_value($meta_key, $meta_value) {
	    global $wpdb;
	    if ($meta_key != '' && $meta_value != '') {
	        $post_query = "SELECT postmeta.meta_id FROM $wpdb->postmeta AS postmeta";
	        $post_query .= " WHERE meta_key='{$meta_key}' AND meta_value='{$meta_value}'";
	        $post_query .= " LIMIT 1";
	        $results = $wpdb->get_col($post_query);
	        if (isset($results[0])) {
	            return $results[0];
	        }
	    }
	    return 0;
	}

	public static function required_add_label($obj) {
        if ( !empty($obj['name']) ) {
            return $obj['name'].' <span class="required">*</span>';
        }
        return '';
    }

    public static function process_commission_fee($price = '', $args = array()) {
    	if ( $price > 0 ) {
			if (!empty($args['type']) && $args['type'] === 'fixed' ) {
				$fixed_amount = !empty($args['fixed_amount']) ? $args['fixed_amount'] : 0;
				
				if ( $price < $fixed_amount ) {
					$fixed_amount = $price;
				}
				$admin_shares = $fixed_amount;
				$freelancer_shares = $price - $admin_shares;
				
			} elseif(!empty($args['type']) && $args['type'] === 'percentage' ) {
				$percentage	= !empty($args['percentage']) ? $args['percentage'] : 0;
				
				$admin_shares = $price/100 * $percentage;
				$freelancer_shares = $price - $admin_shares;
			} elseif(!empty($args['type']) && $args['type'] === 'comissions_tiers' ){
				$comissions_tiers	= !empty( $args['comissions_tiers'] ) ? $args['comissions_tiers'] : array();
				
				$admin_shares 		= 0;
				$freelancer_shares 	= $price;
				
				if(!empty($comissions_tiers)){
					foreach($comissions_tiers as $key => $item){
						$range	= !empty( $item['range'] ) ? explode('-',$item['range']) : 0;
						$amount	= !empty( $item['amount']) ? $item['amount'] : 0;
						
						$start =  !empty($range[0]) ? $range[0] : 0;
						$end =  !empty($range[1]) ? $range[1] : 0;

						if ( !empty($start) && !empty($end) ) {
							if ( $price >= $start && $price <= $end ) {
								if (!empty($item['type']) && $item['type'] === 'fixed') {
									$admin_shares 		= $amount;
									$freelancer_shares 	= $price - $admin_shares;
								} elseif(!empty($item['type']) && $item['type'] === 'percentage') {
									$admin_shares = $price/100 * $amount;
									$freelancer_shares = $price - $admin_shares;
								}
								break;
							}
						} elseif( !empty($start) && empty($end) ) {
							if ( $price >= $start ) {
								if (!empty($item['type']) && $item['type'] === 'fixed' ) {
									$admin_shares 		= $amount;
									$freelancer_shares 	= $price - $admin_shares;
								} elseif (!empty($item['type']) && $item['type'] === 'percentage' ) {
									$admin_shares 		= $price/100 * $amount;
									$freelancer_shares 	= $price - $admin_shares;
								}
								break;
							}
						}
					}
				}
			} else {
				$admin_shares = 0.0;
				$freelancer_shares = $price;
			}
		} else {
			$admin_shares = 0.0;
			$freelancer_shares = $price;
		}

		$args['admin_shares'] = !empty($admin_shares) && $admin_shares > 0 ? number_format($admin_shares,2,'.', '') : 0.0;
		$args['freelancer_shares'] = !empty($freelancer_shares) && $freelancer_shares > 0 ? number_format($freelancer_shares,2,'.', '') : 0.0;
		
		return $args;
	}

	public static function employer_hiring_payment_setting($price = '', $args = array()) {
		if (!empty($args['type']) && $args['type'] === 'fixed' ) {
			$commission	= !empty($args['fixed_amount']) ? $args['fixed_amount'] : 0;
			$price = $price + $commission;
		} elseif(!empty($args['type']) && $args['type'] === 'percentage' ) {
			$percentage = !empty($args['percentage']) ? $args['percentage'] : 0;
			$commission = $price/100 * $percentage;
			$price = $price + $commission;
		} else {
			$price = $price;
		}
		
		$args['commission_amount'] = !empty( $commission )  ? $commission : 0.0;
		$args['total_amount'] = $price;

		return $args;
	}
}

WP_Freeio_Mixes::init();
