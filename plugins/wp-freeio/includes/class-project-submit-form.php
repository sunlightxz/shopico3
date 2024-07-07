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

class WP_Freeio_Project_Submit_Form extends WP_Freeio_Project_Abstract_Form {
	public $form_name = 'wp_freeio_project_submit_form';
	

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

		$this->project_id = ! empty( $_REQUEST['project_id'] ) ? absint( $_REQUEST['project_id'] ) : 0;

		if ( ! WP_Freeio_User::is_employer_can_edit_project( $this->project_id ) ) {
			$this->project_id = 0;
		}
		do_action('wp_freeio_submit_project_construct', $this);
		add_filter( 'cmb2_meta_boxes', array( $this, 'fields_front' ) );
		
	}

	public function get_steps() {
		$this->steps = apply_filters( 'wp_freeio_submit_project_steps', array(
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
		
		if ( ! isset( $_POST['submit-cmb-project'] ) || empty( $_POST[WP_FREEIO_PROJECT_PREFIX.'post_type'] ) || 'project' !== $_POST[WP_FREEIO_PROJECT_PREFIX.'post_type'] ) {
			return;
		}
		
		$cmb = cmb2_get_metabox( WP_FREEIO_PROJECT_PREFIX . 'front' );
		if ( ! isset( $_POST[ $cmb->nonce() ] ) || ! wp_verify_nonce( $_POST[ $cmb->nonce() ], $cmb->nonce() ) ) {
			return;
		}
		// Setup and sanitize data
		if ( isset( $_POST[ WP_FREEIO_PROJECT_PREFIX . 'title' ] ) ) {
			$post_id = $this->project_id;

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
			
			$employer_user_id = WP_Freeio_User::get_user_id();
			$data = array(
				'post_title'     => sanitize_text_field( $_POST[ WP_FREEIO_PROJECT_PREFIX . 'title' ] ),
				'post_author'    => $employer_user_id,
				'post_status'    => $post_status,
				'post_type'      => 'project',
				'post_date'      => $post_date,
				'post_content'   => wp_kses_post( $_POST[ WP_FREEIO_PROJECT_PREFIX . 'description' ] ),
			);

			$new_post = true;
			if ( !empty( $post_id ) ) {
				$data['ID'] = $post_id;
				$new_post = false;
			} else {
				if ( apply_filters('wp-freeio-update-slug-submit-project', true) ) {
					$project_slug = array();

					// Prepend with employer.
					$employer_slug = wp_freeio_get_option('submission_project_slug_employer', 'on');
					if ( $employer_slug == 'on' ) {
						$employer_slug = true;
					} else {
						$employer_slug = false;
					}
					$employer_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);
					$employer_name = get_the_title($employer_id);
					if ( apply_filters( 'wp-freeio-submit-project-form-prefix-post-name-with-company', $employer_slug ) && !empty( $employer_name ) ) {
						$project_slug[] = $employer_name;
					}

					// Prepend category.
					$category_slug = wp_freeio_get_option('submission_project_slug_category', 'on');
					if ( $category_slug == 'on' ) {
						$category_slug = true;
					} else {
						$category_slug = false;
					}
					if ( apply_filters( 'wp-freeio-submit-project-form-prefix-post-name-with-category', $category_slug ) && !empty($_POST[WP_FREEIO_PROJECT_PREFIX.'category']) ) {
						$slugs = $_POST[WP_FREEIO_PROJECT_PREFIX.'category'];
						if ( is_array($slugs) ) {
							foreach ($slugs as $slug) {
								if ( is_numeric($slug) ) {
									$term = get_term($slug, 'project_category');
									if ( $term && $term->slug ) {
										$project_slug[] = $term->slug;
									}
								} else {
									$project_slug[] = $slug;
								}
							}
						} else {
							if ( is_numeric($slugs) ) {
								$term = get_term($slugs, 'project_category');
								if ( $term && $term->slug ) {
									$project_slug[] = $term->slug;
								}
							} else {
								$project_slug[] = $slugs;
							}
						}
					}

					$project_slug[] = $data['post_title'];
					$data['post_name'] = sanitize_title( implode( '-', $project_slug ) );
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
				$_POST['object_id'] = $post_id; // object_id in POST contains page ID instead of project ID

				$employer_id = WP_Freeio_User::get_employer_by_user_id($employer_user_id);
				$author_id = update_post_meta($post_id, WP_FREEIO_PROJECT_PREFIX . 'employer_posted_by', $employer_id);

				$cmb->save_fields( $post_id, 'post', $_POST );

				// Create featured image
				$featured_image = get_post_meta( $post_id, WP_FREEIO_PROJECT_PREFIX . 'featured_image', true );
				if ( ! empty( $_POST[ 'current_' . WP_FREEIO_PROJECT_PREFIX . 'featured_image' ] ) ) {
					$img_id = get_post_meta( $post_id, WP_FREEIO_PROJECT_PREFIX . 'featured_image_img', true );
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
						update_post_meta( $post_id, WP_FREEIO_PROJECT_PREFIX . 'featured_image', null );
						delete_post_thumbnail( $post_id );
					}
				} else {
					update_post_meta( $post_id, WP_FREEIO_PROJECT_PREFIX . 'featured_image', null );
					delete_post_thumbnail( $post_id );
				}

				do_action( 'wp-freeio-process-submission-after-save', $post_id );

				if ( $new_post ) {
					setcookie( 'project_add_new_update', 'new' );
				} else {
					setcookie( 'project_add_new_update', 'update' );
				}
				$this->project_id = $post_id;
				$this->step ++;

			} else {
				if( $new_post ) {
					$this->errors[] = __( 'Can not create project', 'wp-freeio' );
				} else {
					$this->errors[] = __( 'Can not update project', 'wp-freeio' );
				}
			}
		}

		return;
	}

	public function submission_validate( $data ) {
		$error = array();
		if ( empty($data['post_author']) ) {
			$error[] = __( 'Please login to submit project', 'wp-freeio' );
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

		if ( $this->project_id ) {
			$post              = get_post( $this->project_id ); // WPCS: override ok.
			$post->post_status = 'preview';

			setup_postdata( $post );

			do_action('wp-freeio-before-preview-project', $post);

			echo WP_Freeio_Template_Loader::get_template_part( 'project-submission/project-submit-preview', array(
				'post_id' => $this->project_id,
				'project_id'         => $this->project_id,
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

		if ( !isset( $_POST['security-project-submit-preview'] ) || ! wp_verify_nonce( $_POST['security-project-submit-preview'], 'wp-freeio-project-submit-preview-nonce' )  ) {
			$this->errors[] = esc_html__('Your nonce did not verify.', 'wp-freeio');
			return;
		}

		if ( isset( $_POST['continue-edit-project'] ) ) {
			$this->step --;
		} elseif ( isset( $_POST['continue-submit-project'] ) ) {
			$project = get_post( $this->project_id );

			if ( in_array( $project->post_status, array( 'preview', 'expired' ), true ) ) {
				// Reset expiry.
				delete_post_meta( $project->ID, WP_FREEIO_PROJECT_PREFIX.'expiry_date' );

				// Update project listing.
				$review_before = wp_freeio_get_option( 'submission_project_requires_approval' );
				$post_status = 'publish';
				if ( $review_before == 'on' ) {
					$post_status = 'pending';
				}

				$employer_user_id = WP_Freeio_User::get_user_id();
				$update_project                  = array();
				$update_project['ID']            = $project->ID;
				$update_project['post_status']   = apply_filters( 'wp_freeio_submit_project_post_status', $post_status, $project );
				$update_project['post_date']     = current_time( 'mysql' );
				$update_project['post_date_gmt'] = current_time( 'mysql', 1 );
				$update_project['post_author']   = $employer_user_id;

				wp_update_post( $update_project );
			}

			$this->step ++;
		}
	}

	public function done_output() {
		$project = get_post( $this->project_id );
		
		echo WP_Freeio_Template_Loader::get_template_part( 'project-submission/project-submit-done', array(
			'post_id' => $this->project_id,
			'project'	  => $project,
		) );
	}

	public function done_handler() {
		do_action( 'wp_freeio_project_submit_done', $this->project_id );
		
		if ( ! empty( $_COOKIE['project_add_new_update'] ) ) {
			$project_add_new_update = $_COOKIE['project_add_new_update'];

			if ( wp_freeio_get_option('admin_notice_add_new_project') ) {
				$project = get_post($this->project_id);
				$email_from = get_option( 'admin_email', false );
				
				$headers = sprintf( "From: %s <%s>\r\n Content-type: text/html", get_bloginfo('name'), $email_from );
				$email_to = get_option( 'admin_email', false );
				$subject = WP_Freeio_Email::render_email_vars(array('project' => $project), 'admin_notice_add_new_project', 'subject');
				$content = WP_Freeio_Email::render_email_vars(array('project' => $project), 'admin_notice_add_new_project', 'content');
				
				WP_Freeio_Email::wp_mail( $email_to, $subject, $content, $headers );
			}
			
			setcookie( 'project_add_new_update', '', time() - HOUR_IN_SECONDS );
		}
	}
}

function wp_freeio_project_submit_form() {
	if ( ! empty( $_POST['wp_freeio_project_submit_form'] ) ) {
		WP_Freeio_Project_Submit_Form::get_instance();
	}
}

add_action( 'init', 'wp_freeio_project_submit_form' );