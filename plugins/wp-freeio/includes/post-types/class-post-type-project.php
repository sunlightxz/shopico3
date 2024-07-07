<?php
/**
 * Post Type: Project Listing
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Post_Type_Project {
	public static $prefix = WP_FREEIO_PROJECT_PREFIX;
	public static function init() {
	  	add_action( 'init', array( __CLASS__, 'register_post_type' ) );
	  	add_action( 'admin_menu', array( __CLASS__, 'add_pending_count_to_menu' ) );
	  	
	  	add_filter( 'cmb2_admin_init', array( __CLASS__, 'metaboxes' ) );

	  	add_filter( 'wp_insert_post_data', array( __CLASS__, 'fix_post_name' ), 10, 2 );
	  	
	  	add_filter( 'manage_edit-project_columns', array( __CLASS__, 'custom_columns' ) );
		add_action( 'manage_project_posts_custom_column', array( __CLASS__, 'custom_columns_manage' ) );
		add_action('restrict_manage_posts', array( __CLASS__, 'filter_project_by_type' ));
		add_action('parse_query', array( __CLASS__, 'filter_project_by_type_in_query' ));

		add_action('save_post', array( __CLASS__, 'save_post' ), 10, 2 );

		add_action( 'pending_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'pending_payment_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'preview_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'draft_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'auto-draft_to_publish', array( __CLASS__, 'set_expiry_date' ) );
		add_action( 'expired_to_publish', array( __CLASS__, 'set_expiry_date' ) );

		add_action( 'wp_freeio_check_for_expired_projects', array('WP_Freeio_Project', 'check_for_expired_projects') );
		add_action( 'wp_freeio_delete_old_previews', array('WP_Freeio_Project', 'delete_old_previews') );

		add_action( 'wp_freeio_email_daily_notices', array( 'WP_Freeio_Project', 'send_admin_expiring_notice' ) );
		add_action( 'wp_freeio_email_daily_notices', array( 'WP_Freeio_Project', 'send_employer_expiring_notice' ) );
		add_action( 'template_redirect', array( 'WP_Freeio_Project', 'track_project_view' ), 20 );

		add_action( "cmb2_save_field_".self::$prefix."expiry_date", array( __CLASS__, 'save_expiry_date' ), 10, 3 );
		
		// Ajax endpoints.
		add_action( 'wpfi_ajax_wp_freeio_ajax_remove_project',  array(__CLASS__,'process_remove_project') );


		// compatible handlers.
		add_action( 'wp_ajax_wp_freeio_ajax_remove_project',  array(__CLASS__,'process_remove_project') );
	}

	public static function register_post_type() {
		$singular = __( 'Project', 'wp-freeio' );
		$plural   = __( 'Projects', 'wp-freeio' );

		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new'               => sprintf(__( 'Add New %s', 'wp-freeio' ), $singular),
			'add_new_item'          => sprintf(__( 'Add New %s', 'wp-freeio' ), $singular),
			'edit_item'             => sprintf(__( 'Edit %s', 'wp-freeio' ), $singular),
			'new_item'              => sprintf(__( 'New %s', 'wp-freeio' ), $singular),
			'all_items'             => sprintf(__( 'All %s', 'wp-freeio' ), $plural),
			'view_item'             => sprintf(__( 'View %s', 'wp-freeio' ), $singular),
			'search_items'          => sprintf(__( 'Search %s', 'wp-freeio' ), $singular),
			'not_found'             => sprintf(__( 'No %s found', 'wp-freeio' ), $plural),
			'not_found_in_trash'    => sprintf(__( 'No %s found in Trash', 'wp-freeio' ), $plural),
			'parent_item_colon'     => '',
			'menu_name'             => $plural,
		);
		$has_archive = true;
		$project_archive = get_option('wp_freeio_project_archive_slug');
		if ( $project_archive ) {
			$has_archive = $project_archive;
		}
		$project_rewrite_slug = get_option('wp_freeio_project_base_slug');
		if ( empty($project_rewrite_slug) ) {
			$project_rewrite_slug = _x( 'project', 'Project slug - resave permalinks after changing this', 'wp-freeio' );
		}
		$rewrite = array(
			'slug'       => $project_rewrite_slug,
			'with_front' => false
		);
		register_post_type( 'project',
			array(
				'labels'            => $labels,
				'supports'          => array( 'title', 'editor', 'thumbnail', 'comments', 'author' ),
				'public'            => true,
				'has_archive'       => $has_archive,
				'rewrite'           => $rewrite,
				'menu_position'     => 50,
				'categories'        => array(),
				'menu_icon'         => 'dashicons-code-standards',
				'show_in_rest'		=> false,
			)
		);
	}

	/**
	 * Adds pending count to WP admin menu label
	 *
	 * @access public
	 * @return void
	 */
	public static function add_pending_count_to_menu() {
		global $menu;
		$menu_item_index = null;

		foreach( $menu as $index => $menu_item ) {
			if ( ! empty( $menu_item[5] ) && $menu_item[5] == 'menu-posts-project' ) {
				$menu_item_index = $index;
				break;
			}
		}

		if ( $menu_item_index ) {
			$count = WP_Freeio_Cache_Helper::get_listings_count();

			if ( $count > 0 ) {
				$menu_title = $menu[ $menu_item_index ][0];
				$menu_title = sprintf('%s <span class="awaiting-mod"><span class="pending-count">%s</span></span>', $menu_title, number_format_i18n($count) );
				$menu[ $menu_item_index ][0] = $menu_title;
			}
		}
	}

	public static function fix_post_name( $data, $postarr ) {
		if ( 'project' === $data['post_type']
			&& 'pending' === $data['post_status']
			&& ! current_user_can( 'publish_posts' )
			&& isset( $postarr['post_name'] )
		) {
			$data['post_name'] = $postarr['post_name'];
		}
		return $data;
	}

	public static function save_expiry_date($updated, $action, $obj) {
		if ( $action != 'disabled' ) {
			$key = self::$prefix.'expiry_date';
			$data_to_save = $obj->data_to_save;
			$post_id = !empty($data_to_save['post_ID']) ? $data_to_save['post_ID'] : '';
			$expiry_date = isset($data_to_save[$key]) ? $data_to_save[$key] : '';
			if ( empty( $expiry_date ) ) {
				if ( wp_freeio_get_option( 'submission_project_duration' ) ) {
					$expires = WP_Freeio_Project::calculate_project_expiry( $post_id );
					update_post_meta( $post_id, $key, $expires );
				} else {
					delete_post_meta( $post_id, $key );
				}
			} else {
				update_post_meta( $post_id, self::$prefix.'expiry_date', date( 'Y-m-d', strtotime( sanitize_text_field( $expiry_date ) ) ) );
			}

		}
	}

	public static function save_post($post_id, $post) {
		if ( $post->post_type === 'project' ) {
			$post_args = array();
			if ( !empty($_POST[self::$prefix . 'employer_posted_by']) ) {
				$post_args['post_author'] = $_POST[self::$prefix . 'posted_by'];
			}
			if ( !empty($_POST[self::$prefix . 'employer_posted_by']) ) {
				$employer_id = $_POST[self::$prefix . 'employer_posted_by'];
				$author_id = WP_Freeio_User::get_user_by_employer_id($employer_id);
				$post_args['post_author'] = $author_id;
			}

			if ( !empty($_POST[self::$prefix . 'featured']) ) {
				$post_args['menu_order'] = -1;
			} else {
				$post_args['menu_order'] = 0;
			}

			$expiry_date = get_post_meta( $post_id, self::$prefix.'expiry_date', true );
			$today_date = date( 'Y-m-d', current_time( 'timestamp' ) );
			$is_project_expired = $expiry_date && $today_date > $expiry_date;

			if ( $is_project_expired && ! WP_Freeio_Project::is_project_status_changing( null, 'draft' ) ) {

				if ( !empty($_POST) ) {
					if ( WP_Freeio_Project::is_project_status_changing( 'expired', 'publish' ) ) {
						if ( empty($_POST[self::$prefix.'expiry_date']) || strtotime( $_POST[self::$prefix.'expiry_date'] ) < current_time( 'timestamp' ) ) {
							$expires = WP_Freeio_Project::calculate_project_expiry( $post_id );
							update_post_meta( $post_id, self::$prefix.'expiry_date', WP_Freeio_Project::calculate_project_expiry( $post_id ) );
							if ( isset( $_POST[self::$prefix.'expiry_date'] ) ) {
								$_POST[self::$prefix.'expiry_date'] = $expires;
							}
						}
					} else {
						$post_args['post_status'] = 'expired';
					}
				}
			}
			if ( !empty($post_args) ) {
				$post_args['ID'] = $post_id;

				remove_action('save_post', array( __CLASS__, 'save_post' ), 10, 2 );
				wp_update_post( $post_args );
				add_action('save_post', array( __CLASS__, 'save_post' ), 10, 2 );
			}

			delete_transient( 'wp_freeio_filter_counts' );
			delete_transient( 'wp-freeio-get-filter-employers' );
			
			clean_post_cache( $post_id );
		}
	}

	public static function set_expiry_date( $post ) {

		if ( $post->post_type === 'project' ) {

			// See if it is already set.
			if ( metadata_exists( 'post', $post->ID, self::$prefix.'expiry_date' ) ) {
				$expires = get_post_meta( $post->ID, self::$prefix.'expiry_date', true );

				// if ( $expires && strtotime( $expires ) < current_time( 'timestamp' ) ) {
				// 	update_post_meta( $post->ID, self::$prefix.'expiry_date', '' );
				// }
			}

			// See if the user has set the expiry manually.
			if ( ! empty( $_POST[self::$prefix.'expiry_date'] ) ) {
				update_post_meta( $post->ID, self::$prefix.'expiry_date', date( 'Y-m-d', strtotime( sanitize_text_field( $_POST[self::$prefix.'expiry_date'] ) ) ) );
			} elseif ( ! isset( $expires ) ) {
				// No manual setting? Lets generate a date if there isn't already one.
				$expires = WP_Freeio_Project::calculate_project_expiry( $post->ID );
				update_post_meta( $post->ID, self::$prefix.'expiry_date', $expires );

				// In case we are saving a post, ensure post data is updated so the field is not overridden.
				if ( isset( $_POST[self::$prefix.'expiry_date'] ) ) {
					$_POST[self::$prefix.'expiry_date'] = $expires;
				}
			}
		}
	}

	public static function submission_validate( $data ) {
		$error = array();
		if ( empty($data['post_author']) ) {
			$error[] = array( 'danger', __( 'Please login to submit project', 'wp-freeio' ) );
		}
		if ( empty($data['post_title']) ) {
			$error[] = array( 'danger', __( 'Title is required.', 'wp-freeio' ) );
		}
		if ( empty($data['post_content']) ) {
			$error[] = array( 'danger', __( 'Description is required.', 'wp-freeio' ) );
		}
		return $error;
	}

	public static function process_remove_project() {
		if ( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'wp-freeio-delete-project-nonce' )  ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Your nonce did not verify.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		if ( ! is_user_logged_in() ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('Please login to remove this project', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$project_id = empty( $_POST['project_id'] ) ? false : intval( $_POST['project_id'] );
		if ( !$project_id ) {
			$return = array( 'status' => false, 'msg' => esc_html__('Project not found', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
		$user_id = WP_Freeio_User::get_user_id();
		$is_allowed = WP_Freeio_Mixes::is_allowed_to_remove_project( $user_id, $project_id );

		if ( ! $is_allowed ) {
	        $return = array( 'status' => false, 'msg' => esc_html__('You can not remove this project.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}

		do_action('wp-freeio-before-process-remove-project');

		if ( wp_delete_post( $project_id ) ) {
			$return = array( 'status' => true, 'msg' => esc_html__('Project has been successfully removed.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		} else {
			$return = array( 'status' => false, 'msg' => esc_html__('An error occured when removing an item.', 'wp-freeio') );
		   	echo wp_json_encode($return);
		   	exit;
		}
	}

	public static function metaboxes() {
		global $pagenow;
		if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) {
			do_action('wp-freeio-project-fields-admin');
		}
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
		if ( isset($columns['author']) ) {
			unset($columns['author']);
		}
		$c_fields = array();
		foreach ($columns as $key => $column) {
			if ( $key == 'title' ) {
				$c_fields['employer-logo'] = __( 'Logo', 'wp-freeio' );
			}
			$c_fields[$key] = $column;
		}
		$fields = array_merge($c_fields, array(
			'type' 				=> __( 'Type', 'wp-freeio' ),
			'location' 			=> __( 'Location', 'wp-freeio' ),
			'posted' 			=> __( 'Posted', 'wp-freeio' ),
			'expires' 			=> __( 'Expires', 'wp-freeio' ),
			'category' 			=> __( 'Category', 'wp-freeio' ),
			'featured' 			=> __( 'Featured', 'wp-freeio' ),
			'filled' 			=> __( 'Filled', 'wp-freeio' ),
			'project_status' 		=> __( 'Status', 'wp-freeio' ),
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
			case 'employer-logo':
				$obj_project_meta = WP_Freeio_Project_Meta::get_instance($post->ID);
				if ( $obj_project_meta->check_post_meta_exist('logo') && ($logo_url = $obj_project_meta->get_post_meta( 'logo' )) ) {
					$logo_id = WP_Freeio_Project::get_post_meta($post->ID, 'logo_id', true);
        			if ( $logo_id ) {
	        			echo wp_get_attachment_image( $logo_id, 'thumbnail', '', array( 'class' => 'attachment-thumbnail attachment-thumbnail-small' ) );
	        		} else {
	        			?>
	        			<img class="attachment-thumbnail attachment-thumbnail-small" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_the_title($employer_id)); ?>">
	        			<?php
	        		}
				} else {
					$author_id = WP_Freeio_Project::get_author_id($post->ID);
					if ( $author_id ) {
						$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
						if ( has_post_thumbnail($employer_id) ) {
							echo get_the_post_thumbnail( $employer_id, 'thumbnail', array(
								'class' => 'attachment-thumbnail attachment-thumbnail-small',
							) );
						} else {
							echo '-';
						}
					}
				}
				break;
			case 'type':
				$terms = get_the_terms( $post->ID, 'project_type' );
				if ( is_array( $terms ) ) {
					$project_type = array_shift( $terms );
					$color_value = get_term_meta( $project_type->term_id, '_color', true );
					$style = '';
					if ( $color_value ) {
						$style = 'style="background-color: '.$color_value.'; color: #fff;"';
					}
					echo sprintf( '<a href="?post_type=project&project_type=%s" class="project-type-bg" '.$style.'>%s</a>', $project_type->slug, $project_type->name );
				} else {
					echo '-';
				}
				break;
			case 'location':
				$terms = get_the_terms( $post->ID, 'location' );
				if ( ! empty( $terms ) ) {
					$i = 1;
					foreach ($terms as $term) {

						if( $i < count($terms) ) {
							echo sprintf( '<a href="?post_type=project&location=%s">%s</a>, ', $term->slug, $term->name );
						} else{
							echo sprintf( '<a href="?post_type=project&location=%s">%s</a>', $term->slug, $term->name );
						}
						$i++;
			    	}
				} else {
					echo '-';
				}
				break;
			
			case 'posted':
				$author_id = WP_Freeio_Project::get_author_id($post->ID);
				echo '<strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ) . '</strong><span><br>';
				echo ( empty( $author_id ) ? esc_html__( 'by a guest', 'wp-freeio' ) : sprintf( esc_html__( 'by %s', 'wp-freeio' ), '<a href="' . esc_url( add_query_arg( 'author', $author_id ) ) . '">' . esc_html( get_the_author_meta('display_name', $author_id) ) . '</a>' ) ) . '</span>';
				break;
			case 'expires':
				$expires = get_post_meta( $post->ID, self::$prefix.'expiry_date', true);
				if ( $expires ) {
					echo '<strong>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expires ) ) ) . '</strong>';
				} else {
					echo '&ndash;';
				}
				break;
			case 'category':
				$terms = get_the_terms( $post->ID, 'project_category' );
				if ( ! empty( $terms ) ) {
					$i = 1;
					foreach ($terms as $term) {

						if( $i < count($terms) ) {
							echo sprintf( '<a href="?post_type=project&project_category=%s">%s</a>, ', $term->slug, $term->name );
						} else{
							echo sprintf( '<a href="?post_type=project&project_category=%s">%s</a>', $term->slug, $term->name );
						}
						$i++;
			    	}
				} else {
					echo '-';
				}
				break;
			case 'featured':
				$featured = get_post_meta( $post->ID, self::$prefix . 'featured', true );

				if ( ! empty( $featured ) ) {
					echo '<div class="dashicons dashicons-star-filled"></div>';
				} else {
					echo '<div class="dashicons dashicons-star-empty"></div>';
				}
				break;
			case 'filled':
				$urgent = get_post_meta( $post->ID, self::$prefix . 'filled', true );

				if ( ! empty( $urgent ) ) {
					echo '&#10004;';
				} else {
					echo '&ndash;';
				}
				break;
			case 'project_status':
				$status   = $post->post_status;
				$statuses = WP_Freeio_Project::project_statuses();

				$status_text = $status;
				if ( !empty($statuses[$status]) ) {
					$status_text = $statuses[$status];
				}
				echo sprintf( '<a href="?post_type=project&post_status=%s">%s</a>', esc_attr( $post->post_status ), '<span class="status-' . esc_attr( $post->post_status ) . '">' . esc_html( $status_text ) . '</span>' );
				break;
		}
	}

	public static function filter_project_by_type() {
		global $typenow;
		if ($typenow == 'project') {
			$selected = isset($_GET['project_type']) ? $_GET['project_type'] : '';
			$terms = get_terms( 'project_type', array('hide_empty' => false,) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
				?>
				<select name="project_type">
					<option value=""><?php esc_html_e('All types', 'wp-freeio'); ?></option>
				<?php
				foreach ($terms as $term) {
					?>
					<option value="<?php echo esc_attr($term->slug); ?>" <?php echo trim($term->slug == $selected ? ' selected="selected"' : '') ; ?>><?php echo esc_html($term->name); ?></option>
					<?php
				}
				?>
				</select>
				<?php
			}
			// categories
			$selected = isset($_GET['project_category']) ? $_GET['project_category'] : '';
			$terms = get_terms( 'project_category', array('hide_empty' => false,) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
				?>
				<select name="project_category">
					<option value=""><?php esc_html_e('All categories', 'wp-freeio'); ?></option>
				<?php
				foreach ($terms as $term) {
					?>
					<option value="<?php echo esc_attr($term->slug); ?>" <?php echo trim($term->slug == $selected ? ' selected="selected"' : '') ; ?>><?php echo esc_html($term->name); ?></option>
					<?php
				}
				?>
				</select>
				<?php
			}
		}
	}

	public static function filter_project_by_type_in_query($query) {
		global $pagenow;

		$type_id = isset($_GET['project_type']) ? $_GET['project_type'] : '';
		$category_id = isset($_GET['project_category']) ? $_GET['project_category'] : '';
		$location_id = isset($_GET['location']) ? $_GET['location'] : '';
		$post_author = isset($_GET['post_author']) ? $_GET['post_author'] : '';
		$q_vars    = &$query->query_vars;

		if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == 'project' ) {
			if ( !empty($post_author) ) {
				$q_vars['author'] = $post_author;
			}
		}
		
	}
}
WP_Freeio_Post_Type_Project::init();


