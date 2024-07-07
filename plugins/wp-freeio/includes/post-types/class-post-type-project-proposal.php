<?php
/**
 * Post Type: Project Proposal
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Project_Proposal {
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_filter( 'cmb2_meta_boxes', array( __CLASS__, 'fields' ) );
	  	
	  	add_filter( 'manage_edit-project_proposal_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_project_proposal_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );

	  	// Ajax endpoints.
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_proposal',  array(__CLASS__, 'process_remove_proposal') );


		// compatible handlers.
		add_action( 'wp_ajax_wp_freeio_ajax_remove_proposal',  array(__CLASS__, 'process_remove_proposal') );
	}

	public static function register_post_type() {
		$singular = __( 'Proposal', 'wp-freeio' );
		$plural   = __( 'Proposals', 'wp-freeio' );

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
		
		register_post_type( 'project_proposal',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor' ),
				'public'            => true,
		        'has_archive'       => false,
		        'publicly_queryable' => false,
				'show_in_rest'		=> true,
				'show_in_menu'		=> 'edit.php?post_type=project',
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
		
		$fields = array();
		
		

		$fields[] = array(
			'name'              => __( 'Project type', 'wp-freeio' ),
			'id'                => WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'project_type',
			'type'              => 'select',
			'options'           => WP_Freeio_Mixes::get_default_project_types(),
		);

		$fields[] = array(
			'name'              => __( 'Project ID', 'wp-freeio' ),
			'id'                => WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'project_id',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Amount', 'wp-freeio' ),
			'id'                => WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'amount',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Estimeted time', 'wp-freeio' ),
			'id'                => WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'estimeted_time',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Status', 'wp-freeio' ),
			'id'                => WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'status',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Admin amount', 'wp-freeio' ),
			'id'                => WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'admin_amount',
			'type'              => 'text',
		);

		$fields[] = array(
			'name'              => __( 'Freelancer amount', 'wp-freeio' ),
			'id'                => WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'freelancer_amount',
			'type'              => 'text',
		);

		$metaboxes[ WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'general' ] = array(
			'id'                        => WP_FREEIO_PROJECT_PROPOSAL_PREFIX . 'general',
			'title'                     => __( 'General Options', 'wp-freeio' ),
			'object_types'              => array( 'project_proposal' ),
			'context'                   => 'normal',
			'priority'                  => 'high',
			'show_names'                => true,
			'show_in_rest'				=> true,
			'fields'                    => $fields
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
			'title' 			=> __( 'Title', 'wp-freeio' ),
			'date' 				=> esc_html__( 'Date', 'wp-freeio' ),
			'author' 			=> esc_html__( 'Author', 'wp-freeio' ),
			'status' 			=> esc_html__( 'Status', 'wp-freeio' ),
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
		global $post;
		switch ( $column ) {
			case 'status':
				$status   = $post->post_status;
				$status_label = get_post_status($post);
				
				echo sprintf( '<a href="?post_type=project&post_status=%s">%s</a>', esc_attr( $post->post_status ), '<span class="status-' . esc_attr( $post->post_status ) . '">' . esc_html( $status_label ) . '</span>' );
				break;
			break;
		}
	}

	public static function process_remove_proposal() {
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-delete-proposal-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to remove this proposal', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$proposal_id = empty( $_POST['proposal_id'] ) ? false : intval( $_POST['proposal_id'] );
		if ( !$proposal_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Proposal not found', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$user_id = WP_Freeio_User::get_user_id();
		$is_allowed = WP_Freeio_Mixes::is_allowed_to_remove_proposal( $user_id, $proposal_id );

		if ( ! $is_allowed ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('You can not remove this proposal.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-before-process-remove-proposal');

		if ( wp_delete_post( $proposal_id ) ) {
			$return = array( 'status' => true, 'msg' => esc_html__('Proposal has been successfully removed.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('An error occured when removing an item.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}
}
WP_Freeio_Post_Type_Project_Proposal::init();