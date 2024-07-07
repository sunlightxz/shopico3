<?php
/**
 * Rest API
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Rest_API {
	
	public static function init() {
		add_action( 'rest_api_init', array(__CLASS__,'register_meta_fields'));
	}
	
	public static function register_meta_fields(){

		register_rest_field( 'job_listing', 'metas', array(
		 	'get_callback' => array(__CLASS__, 'get_job_metas_for_api'),
		 	'schema' => null,
		));

		register_rest_field( 'freelancer', 'metas', array(
		 	'get_callback' => array(__CLASS__, 'get_freelancer_metas_for_api'),
		 	'schema' => null,
		));

		register_rest_field( 'employer', 'metas', array(
		 	'get_callback' => array(__CLASS__, 'get_employer_metas_for_api'),
		 	'schema' => null,
		));

		register_rest_field( 'service', 'metas', array(
		 	'get_callback' => array(__CLASS__, 'get_service_metas_for_api'),
		 	'schema' => null,
		));

		register_rest_field( 'project', 'metas', array(
		 	'get_callback' => array(__CLASS__, 'get_project_metas_for_api'),
		 	'schema' => null,
		));
	}

	public static function get_job_metas_for_api($object) {
		$prefix = WP_FREEIO_JOB_LISTING_PREFIX;

		$post_id = $object['id'];

		$meta_obj = WP_Freeio_Job_Listing_Meta::get_instance($post_id);
		$fields = $meta_obj->get_post_metas();
		if ( isset($fields[$prefix.'title']) ) {
			unset($fields[$prefix.'title']);
		}
		if ( isset($fields[$prefix.'description']) ) {
			unset($fields[$prefix.'description']);
		}
		if ( isset($fields[$prefix.'max_salary']) ) {
			unset($fields[$prefix.'max_salary']);
		}
		$return = array();
		foreach ($fields as $key => $field) {
			if ( $field['type'] != 'title' ) {
				switch ($key) {
					case $prefix.'category':
						$terms = get_the_terms( $post_id, 'job_listing_category' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'type':
						$terms = get_the_terms( $post_id, 'job_listing_type' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'tag':
						$terms = get_the_terms( $post_id, 'job_listing_tag' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'location':
						$terms = get_the_terms( $post_id, 'location' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'salary':
						$return[$key] = WP_Freeio_Job_Listing::get_salary_html($post_id, false);
						break;
					case $prefix.'map_location':
						$values = [];
						$values['address'] = WP_Freeio_Job_Listing::get_post_meta($post_id, 'map_location_address');
						$values['latitude'] = WP_Freeio_Job_Listing::get_post_meta($post_id, 'map_location_latitude');
						$values['longitude'] = WP_Freeio_Job_Listing::get_post_meta($post_id, 'map_location_longitude');
						$return[$key] = $values;
						break;
					default:
						$return[$key] = $meta_obj->get_custom_post_meta($key);
						break;
				}
			}
		}

		$author_id = get_post_field('post_author', $post_id);
		if ( WP_Freeio_User::is_employer($author_id) ) {
			$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
			if ( empty($return[$prefix.'logo']) ) {
				if ( has_post_thumbnail($employer_id) ) {
					$return[$prefix.'logo'] = get_the_post_thumbnail_url($employer_id, 'thumbnail');
				}
			}
			$return[$prefix.'employer_name'] = get_the_title($employer_id);
			$return[$prefix.'employer_url'] = get_permalink($employer_id);
		}
		

		return $return;
	}

	public static function get_freelancer_metas_for_api($object) {
		$prefix = WP_FREEIO_FREELANCER_PREFIX;

		$post_id = $object['id'];

		$meta_obj = WP_Freeio_Freelancer_Meta::get_instance($post_id);
		$fields = $meta_obj->get_post_metas();
		if ( isset($fields[$prefix.'title']) ) {
			unset($fields[$prefix.'title']);
		}
		if ( isset($fields[$prefix.'description']) ) {
			unset($fields[$prefix.'description']);
		}
		if ( isset($fields[$prefix.'featured_image']) ) {
			unset($fields[$prefix.'featured_image']);
		}
		$return = array();
		foreach ($fields as $key => $field) {
			if ( $field['type'] != 'title' ) {
				switch ($key) {
					case $prefix.'category':
						$terms = get_the_terms( $post_id, 'freelancer_category' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'tag':
						$terms = get_the_terms( $post_id, 'freelancer_tag' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'location':
						$terms = get_the_terms( $post_id, 'location' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'salary':
						$return[$key] = WP_Freeio_Freelancer::get_salary_html($post_id, false);
						break;
					case $prefix.'map_location':
						$values = [];
						$values['address'] = WP_Freeio_Freelancer::get_post_meta($post_id, 'map_location_address');
						$values['latitude'] = WP_Freeio_Freelancer::get_post_meta($post_id, 'map_location_latitude');
						$values['longitude'] = WP_Freeio_Freelancer::get_post_meta($post_id, 'map_location_longitude');
						$return[$key] = $values;
						break;
					default:
						$return[$key] = $meta_obj->get_custom_post_meta($key);
						break;
				}
			}
		}
		if ( has_post_thumbnail($post_id) ) {
			$return[$prefix.'logo'] = get_the_post_thumbnail_url($post_id, 'thumbnail');
		}
		return $return;
	}

	public static function get_employer_metas_for_api($object) {
		$prefix = WP_FREEIO_EMPLOYER_PREFIX;

		$post_id = $object['id'];

		$meta_obj = WP_Freeio_Employer_Meta::get_instance($post_id);
		$fields = $meta_obj->get_post_metas();
		if ( isset($fields[$prefix.'title']) ) {
			unset($fields[$prefix.'title']);
		}
		if ( isset($fields[$prefix.'description']) ) {
			unset($fields[$prefix.'description']);
		}
		$return = array();
		foreach ($fields as $key => $field) {
			if ( $field['type'] != 'title' ) {
				switch ($key) {
					case $prefix.'category':
						$terms = get_the_terms( $post_id, 'employer_category' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'location':
						$terms = get_the_terms( $post_id, 'location' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'map_location':
						$values = [];
						$values['address'] = WP_Freeio_Employer::get_post_meta($post_id, 'map_location_address');
						$values['latitude'] = WP_Freeio_Employer::get_post_meta($post_id, 'map_location_latitude');
						$values['longitude'] = WP_Freeio_Employer::get_post_meta($post_id, 'map_location_longitude');
						$return[$key] = $values;
						break;
					default:
						$return[$key] = $meta_obj->get_custom_post_meta($key);
						break;
				}
			}
		}

		if ( has_post_thumbnail($post_id) ) {
			$return[$prefix.'logo'] = get_the_post_thumbnail_url($post_id, 'thumbnail');
		}

		return $return;
	}

	public static function get_service_metas_for_api($object) {
		$prefix = WP_FREEIO_SERVICE_PREFIX;

		$post_id = $object['id'];

		$meta_obj = WP_Freeio_Service_Meta::get_instance($post_id);
		$fields = $meta_obj->get_post_metas();
		if ( isset($fields[$prefix.'title']) ) {
			unset($fields[$prefix.'title']);
		}
		if ( isset($fields[$prefix.'description']) ) {
			unset($fields[$prefix.'description']);
		}
		$return = array();
		foreach ($fields as $key => $field) {
			if ( $field['type'] != 'title' ) {
				switch ($key) {
					case $prefix.'category':
						$terms = get_the_terms( $post_id, 'service_category' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'tag':
						$terms = get_the_terms( $post_id, 'service_tag' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'location':
						$terms = get_the_terms( $post_id, 'location' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'price':
						$return[$key] = WP_Freeio_Service::get_price_html($post_id, false);
						break;
					case $prefix.'map_location':
						$values = [];
						$values['address'] = WP_Freeio_Service::get_post_meta($post_id, 'map_location_address');
						$values['latitude'] = WP_Freeio_Service::get_post_meta($post_id, 'map_location_latitude');
						$values['longitude'] = WP_Freeio_Service::get_post_meta($post_id, 'map_location_longitude');
						$return[$key] = $values;
						break;
					default:
						$return[$key] = $meta_obj->get_custom_post_meta($key);
						break;
				}
			}
		}

		if ( has_post_thumbnail($post_id) ) {
			$return[$prefix.'featured_image'] = get_the_post_thumbnail_url($post_id, 'full');
		}

		return $return;
	}

	public static function get_project_metas_for_api($object) {
		$prefix = WP_FREEIO_PROJECT_PREFIX;

		$post_id = $object['id'];

		$meta_obj = WP_Freeio_Project_Meta::get_instance($post_id);
		$fields = $meta_obj->get_post_metas();
		if ( isset($fields[$prefix.'title']) ) {
			unset($fields[$prefix.'title']);
		}
		if ( isset($fields[$prefix.'description']) ) {
			unset($fields[$prefix.'description']);
		}
		$return = array();
		foreach ($fields as $key => $field) {
			if ( $field['type'] != 'title' ) {
				switch ($key) {
					case $prefix.'category':
						$terms = get_the_terms( $post_id, 'project_category' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'project_skill':
						$terms = get_the_terms( $post_id, 'project_skill' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'project_duration':
						$terms = get_the_terms( $post_id, 'project_duration' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'project_experience':
						$terms = get_the_terms( $post_id, 'project_experience' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'freelancer_type':
						$terms = get_the_terms( $post_id, 'freelancer_type' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'project_language':
						$terms = get_the_terms( $post_id, 'project_language' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'project_level':
						$terms = get_the_terms( $post_id, 'project_level' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'location':
						$terms = get_the_terms( $post_id, 'location' );
						$values = [];
						if ( $terms ) {
							foreach ($terms as $term) {
								$values[$term->term_id] = $term->name;
							}
						}
						$return[$key] = $values;
						break;
					case $prefix.'price':
						$return[$key] = WP_Freeio_Project::get_price_html($post_id, false);
						break;
					case $prefix.'map_location':
						$values = [];
						$values['address'] = WP_Freeio_Project::get_post_meta($post_id, 'map_location_address');
						$values['latitude'] = WP_Freeio_Project::get_post_meta($post_id, 'map_location_latitude');
						$values['longitude'] = WP_Freeio_Project::get_post_meta($post_id, 'map_location_longitude');
						$return[$key] = $values;
						break;
					default:
						$return[$key] = $meta_obj->get_custom_post_meta($key);
						break;
				}
			}
		}

		if ( has_post_thumbnail($post_id) ) {
			$return[$prefix.'featured_image'] = get_the_post_thumbnail_url($post_id, 'full');
		}

		return $return;
	}
}

WP_Freeio_Rest_API::init();