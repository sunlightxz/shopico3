<?php
/**
 * Submit Form
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Service_Submit_Form extends WP_Freeio_Service_Abstract_Form {
	public $form_name = 'wp_freeio_service_submit_form';
	

	private static $_instance = null;

	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct() {

		add_action( 'wp', array( $this, 'process' ) );

		$this->get_steps();

		if ( !empty( $_REQUEST['submit_step'] ) ) {
			$step = is_numeric( $_REQUEST['submit_step'] ) ? max( absint( $_REQUEST['submit_step'] ), 0 ) : array_search( intval( $_REQUEST['submit_step'] ), array_keys( $this->steps ), true );
			$this->step = $step;
		}

		$this->service_id = ! empty( $_REQUEST['service_id'] ) ? absint( $_REQUEST['service_id'] ) : 0;

		if ( ! WP_Freeio_User::is_freelancer_can_edit_service( $this->service_id ) ) {
			$this->service_id = 0;
		}
		do_action('wp_freeio_submit_service_construct', $this);
		add_filter( 'cmb2_meta_boxes', array( $this, 'fields_front' ) );
		
	}

	public function get_steps() {
		$this->steps = apply_filters( 'wp_freeio_submit_service_steps', array(
			'submit'  => array(
				'view'     => array( $this, 'form_output' ),
				'handler'  => array( $this, 'submit_process' ),
				'priority' => 10,
			),
			'preview' => array(
				'view'     => array( $this, 'preview_output' ),
				'handler'  => array( $this, 'preview_process' ),
				'priority' => 20,
			),
			'done'    => array(
				'before_view' => array( $this, 'done_handler' ),
				'view'     => array( $this, 'done_output' ),
				'priority' => 30,
			)
		));

		uasort( $this->steps, array( 'WP_Freeio_Mixes', 'sort_array_by_priority' ) );

		return $this->steps;
	}

	public function submit_process() {
		
		if ( ! isset( $_POST['submit-cmb-service'] ) || empty( $_POST[WP_FREEIO_SERVICE_PREFIX.'post_type'] ) || 'service' !== $_POST[WP_FREEIO_SERVICE_PREFIX.'post_type'] ) {
			return;
		}
		
		$cmb = cmb2_get_metabox( WP_FREEIO_SERVICE_PREFIX . 'front' );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return;
		}
		// Setup and sanitize data
		if ( isset( $_POST[ WP_FREEIO_SERVICE_PREFIX . 'title' ] ) ) {
			$post_id = $this->service_id;

			$post_status = 'preview';
			if ( ! empty( $post_id ) ) {
				$old_post = get_post( $post_id );
				$post_date = $old_post->post_date;
				$old_post_status = get_post_status( $post_id );
				if ( $old_post_status === 'draft' || $old_post_status === 'expired' ) {
					$post_status = 'preview';
				} else {
					$post_status = $old_post_status;
				}
			} else {
				$post_date = '';
			}
			
			$freelancer_user_id = WP_Freeio_User::get_user_id();
			$data = array(
				'post_title'     => sanitize_text_field( $_POST[ WP_FREEIO_SERVICE_PREFIX . 'title' ] ),
				'post_author'    => $freelancer_user_id,
				'post_status'    => $post_status,
				'post_type'      => 'service',
				'post_date'      => $post_date,
				'post_content'   => wp_kses_post( $_POST[ WP_FREEIO_SERVICE_PREFIX . 'description' ] ),
			);

			$new_post = true;
			if ( !empty( $post_id ) ) {
				$data['ID'] = $post_id;
				$new_post = false;
			} else {
				if ( apply_filters('wp-freeio-update-slug-submit-service', true) ) {
					$service_slug = array();

					// Prepend with freelancer.
					$freelancer_slug = wp_freeio_get_option('submission_service_slug_freelancer', 'on');
					if ( $freelancer_slug == 'on' ) {
						$freelancer_slug = true;
					} else {
						$freelancer_slug = false;
					}
					$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
					$freelancer_name = get_the_title($freelancer_id);
					if ( apply_filters( 'wp-freeio-submit-service-form-prefix-post-name-with-company', $freelancer_slug ) && !empty( $freelancer_name ) ) {
						$service_slug[] = $freelancer_name;
					}

					// Prepend category.
					$category_slug = wp_freeio_get_option('submission_service_slug_category', 'on');
					if ( $category_slug == 'on' ) {
						$category_slug = true;
					} else {
						$category_slug = false;
					}
					if ( apply_filters( 'wp-freeio-submit-service-form-prefix-post-name-with-category', $category_slug ) && !empty($_POST[WP_FREEIO_SERVICE_PREFIX.'category']) ) {
						$slugs = $_POST[WP_FREEIO_SERVICE_PREFIX.'category'];
						if ( is_array($slugs) ) {
							foreach ($slugs as $slug) {
								if ( is_numeric($slug) ) {
									$term = get_term($slug, 'service_category');
									if ( $term && $term->slug ) {
										$service_slug[] = $term->slug;
									}
								} else {
									$service_slug[] = $slug;
								}
							}
						} else {
							if ( is_numeric($slugs) ) {
								$term = get_term($slugs, 'service_category');
								if ( $term && $term->slug ) {
									$service_slug[] = $term->slug;
								}
							} else {
								$service_slug[] = $slugs;
							}
						}
					}

					$service_slug[] = $data['post_title'];
					$data['post_name'] = sanitize_title( implode( '-', $service_slug ) );
				}
			}

			do_action( 'wp-freeio-process-submission-before-save', $post_id, $this );

			$data = apply_filters('wp-freeio-process-submission-data', $data, $post_id);
			
			$this->errors = $this->submission_validate($data);
			if ( sizeof($this->errors) ) {
				return;
			}

			$post_id = wp_insert_post( $data, true );

			if ( ! empty( $post_id ) ) {
				$_POST['object_id'] = $post_id; // object_id in POST contains page ID instead of service ID

				$freelancer_id = WP_Freeio_User::get_freelancer_by_user_id($freelancer_user_id);
				$author_id = update_post_meta($post_id, WP_FREEIO_SERVICE_PREFIX . 'freelancer_posted_by', $freelancer_id);

				$cmb->save_fields( $post_id, 'post', $_POST );

				// Create featured image
				$featured_image = get_post_meta( $post_id, WP_FREEIO_SERVICE_PREFIX . 'featured_image', true );
				if ( ! empty( $_POST[ 'current_' . WP_FREEIO_SERVICE_PREFIX . 'featured_image' ] ) ) {
					$img_id = get_post_meta( $post_id, WP_FREEIO_SERVICE_PREFIX . 'featured_image_img', true );
					if ( !empty($featured_image) ) {
						if ( is_array($featured_image) ) {
							$img_id = $featured_image[0];
						} elseif ( is_integer($featured_image) ) {
							$img_id = $featured_image;
						} else {
							$img_id = WP_Freeio_Image::get_attachment_id_from_url($featured_image);
						}
						set_post_thumbnail( $post_id, $img_id );
					} else {
						update_post_meta( $post_id, WP_FREEIO_SERVICE_PREFIX . 'featured_image', null );
						delete_post_thumbnail( $post_id );
					}
				} else {
					update_post_meta( $post_id, WP_FREEIO_SERVICE_PREFIX . 'featured_image', null );
					delete_post_thumbnail( $post_id );
				}

				do_action( 'wp-freeio-process-submission-after-save', $post_id );

				if ( $new_post ) {
					setcookie( 'service_add_new_update', 'new' );
				} else {
					setcookie( 'service_add_new_update', 'update' );
				}
				$this->service_id = $post_id;
				$this->step ++;

			} else {
				if( $new_post ) {
					$this->errors[] = __( 'Can not create service', 'wp-freeio' );
				} else {
					$this->errors[] = __( 'Can not update service', 'wp-freeio' );
				}
			}
		}

		return;
	}

	public function submission_validate( $data ) {
		$error = array();
		if ( empty($data['post_author']) ) {
			$error[] = __( 'Please login to submit service', 'wp-freeio' );
		}
		if ( empty($data['post_title']) ) {
			$error[] = __( 'Title is required.', 'wp-freeio' );
		}
		if ( empty($data['post_content']) ) {
			$error[] = __( 'Description is required.', 'wp-freeio' );
		}
		$error = apply_filters('wp-freeio-submission-validate', $error);
		return $error;
	}

	public function preview_output() {
		global $post;

		if ( $this->service_id ) {
			$post              = get_post( $this->service_id ); // WPCS: override ok.
			$post->post_status = 'preview';

			setup_postdata( $post );

			do_action('wp-freeio-before-preview-service', $post);

			echo WP_Freeio_Template_Loader::get_template_part( 'service-submission/service-submit-preview', array(
				'post_id' => $this->service_id,
				'service_id'         => $this->service_id,
				'step'           => $this->get_step(),
				'form_obj'           => $this,
			) );
			wp_reset_postdata();
		}
	}

	public function preview_process() {
		if ( ! $_POST ) {
			return;
		}

		if ( !isset( $_POST['security-service-submit-preview'] ) || ! wp_verify_nonce( $_POST['security-service-submit-preview'], 'wp-freeio-service-submit-preview-nonce' )  ) {
			$this->errors[] = esc_html__('Your nonce did not verify.', 'wp-freeio');
			return;
		}

		if ( isset( $_POST['continue-edit-service'] ) ) {
			$this->step --;
		} elseif ( isset( $_POST['continue-submit-service'] ) ) {
			$service = get_post( $this->service_id );

			if ( in_array( $service->post_status, array( 'preview', 'expired' ), true ) ) {
				// Reset expiry.
				delete_post_meta( $service->ID, WP_FREEIO_SERVICE_PREFIX.'expiry_date' );

				// Update service listing.
				$review_before = wp_freeio_get_option( 'submission_service_requires_approval' );
				$post_status = 'publish';
				if ( $review_before == 'on' ) {
					$post_status = 'pending';
				}

				$freelancer_user_id = WP_Freeio_User::get_user_id();
				$update_service                  = array();
				$update_service['ID']            = $service->ID;
				$update_service['post_status']   = apply_filters( 'wp_freeio_submit_service_post_status', $post_status, $service );
				$update_service['post_date']     = current_time( 'mysql' );
				$update_service['post_date_gmt'] = current_time( 'mysql', 1 );
				$update_service['post_author']   = $freelancer_user_id;

				wp_update_post( $update_service );
			}

			$this->step ++;
		}
	}

	public function done_output() {
		$service = get_post( $this->service_id );
		
		echo WP_Freeio_Template_Loader::get_template_part( 'service-submission/service-submit-done', array(
			'post_id' => $this->service_id,
			'service'	  => $service,
		) );
	}

	public function done_handler() {
		do_action( 'wp_freeio_service_submit_done', $this->service_id );
		
		if ( ! empty( $_COOKIE['service_add_new_update'] ) ) {
			$service_add_new_update = $_COOKIE['service_add_new_update'];

			if ( wp_freeio_get_option('admin_notice_add_new_service') ) {
				$service = get_post($this->service_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$email_to = get_option( 'admin_email', false );
				$subject = WP_Freeio_Email::render_email_vars(array('service' => $service), 'admin_notice_add_new_service', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('service' => $service), 'admin_notice_add_new_service', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}
			
			setcookie( 'service_add_new_update', '', time() - HOUR_IN_SECONDS );
		}
	}
}

function wp_freeio_service_submit_form() {
	if ( ! empty( $_POST['wp_freeio_service_submit_form'] ) ) {
		WP_Freeio_Service_Submit_Form::get_instance();
	}
}

add_action( 'init', 'wp_freeio_service_submit_form' );