<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_script('wpfi-select2');
wp_enqueue_style('wpfi-select2');

$my_projects_page_id = wp_freeio_get_option('my_projects_page_id');
$my_projects_url = get_permalink( $my_projects_page_id );
?>


<div class="search-orderby-wrapper">
	<div class="search-my-projects-form">
		<form action="" method="get">
			<div class="form-group">
				<input type="text" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
			</div>
			<div class="submit-wrapper">
				<button class="search-submit" name="submit">
					<?php esc_html_e( 'Search ...', 'wp-freeio' ); ?>
				</button>
			</div>
		</form>
	</div>
	<div class="sort-my-projects-form sortby-form">
		<?php
			$orderby_options = apply_filters( 'wp_freeio_my_projects_orderby', array(
				'menu_order'	=> esc_html__( 'Default', 'wp-freeio' ),
				'newest' 		=> esc_html__( 'Newest', 'wp-freeio' ),
				'oldest'     	=> esc_html__( 'Oldest', 'wp-freeio' ),
			) );

			$orderby = isset( $_GET['orderby'] ) ? wp_unslash( $_GET['orderby'] ) : 'newest'; 
		?>

		<div class="orderby-wrapper">
			<span>
				<?php echo esc_html__('Sort by: ','wp-freeio'); ?>
			</span>
			<form class="my-projects-ordering" method="get">
				<select name="orderby" class="orderby">
					<?php foreach ( $orderby_options as $id => $name ) : ?>
						<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" name="paged" value="1" />
				<?php WP_Freeio_Mixes::query_string_form_fields( null, array( 'orderby', 'submit', 'paged' ) ); ?>
			</form>
		</div>
	</div>
</div>

<?php
	$user_id = WP_Freeio_User::get_user_id();
	$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
	$query_vars = array(
		'post_type'     => 'project',
		'post_status'   => apply_filters('wp-freeio-my-projects-post-statuses', array( 'publish', 'expired', 'pending', 'pending_approve', 'draft', 'preview' )),
		'paged'         => $paged,
		'author'        => $user_id,
		'orderby'		=> 'date',
		'order'			=> 'DESC',
	);
	if ( isset($_GET['search']) ) {
		$query_vars['s'] = $_GET['search'];
	}
	if ( isset($_GET['orderby']) ) {
		switch ($_GET['orderby']) {
			case 'menu_order':
				$query_vars['orderby'] = array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
					'ID'         => 'DESC',
				);
				break;
			case 'newest':
				$query_vars['orderby'] = 'date';
				$query_vars['order'] = 'DESC';
				break;
			case 'oldest':
				$query_vars['orderby'] = 'date';
				$query_vars['order'] = 'ASC';
				break;
		}
	}

	$projects = new WP_Query($query_vars);
	if ( $projects->have_posts() ) : ?>
	<table class="project-table">
		<thead>
			<tr>
				<th class="project-title"><?php esc_html_e('Project Title', 'wp-freeio'); ?></th>
				<th class="project-status"><?php esc_html_e('Status', 'wp-freeio'); ?></th>
				<th class="project-actions"></th>
			</tr>
		</thead>
		<tbody>
		<?php while ( $projects->have_posts() ) : $projects->the_post(); global $post; ?>
			<tr>
				<td class="project-table-info">
					
					<div class="project-table-info-content">
						<div class="project-table-info-content-title">
							<?php if ( $post->post_status == 'publish' ) { ?>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<?php } else { ?>
								<?php the_title(); ?>
							<?php } ?>


							<?php $is_urgent = get_post_meta( $post->ID, WP_FREEIO_PROJECT_PREFIX . 'urgent', true ); ?>
							<?php if ( $is_urgent ) : ?>
								<span class="urgent-lable"><?php esc_html_e( 'Urgent', 'wp-freeio' ); ?></span>
							<?php endif; ?>

							<?php $is_featured = get_post_meta( $post->ID, WP_FREEIO_PROJECT_PREFIX . 'featured', true ); ?>
							<?php if ( $is_featured ) : ?>
								<span class="featured-lable"><?php esc_html_e( 'Featured', 'wp-freeio' ); ?></span>
							<?php endif; ?>

						</div>

						
						<div class="project-table-info-content-date-expiry">
							<div class="project-table-info-content-date">
								<?php esc_html_e('Created: ', 'wp-freeio'); ?>
								<span><?php the_time( get_option('date_format') ); ?></span>
							</div>
							<div class="project-table-info-content-expiry">
								<?php
									$expires = get_post_meta( $post->ID, WP_FREEIO_PROJECT_PREFIX.'expiry_date', true);
									if ( $expires ) {
										echo '<span>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expires ) ) ) . '</span>';
									} else {
										echo '--';
									}
								?>
							</div>
						</div>
					</div>
				</td>

				<td class="project-table-status min-width nowrap">
					<div class="project-table-actions-inner <?php echo esc_attr($post->post_status); ?>">
						<?php echo get_post_status(); ?>
					</div>
				</td>

				<td class="project-table-actions min-width nowrap">
					<a class="view-btn" href="<?php the_permalink(); ?>" title="<?php esc_attr_e('View', 'wp-freeio'); ?>"><?php esc_html_e('View', 'wp-freeio'); ?></a>

					<?php
					$my_projects_url = add_query_arg( 'project_id', $post->ID, remove_query_arg( 'project_id', $my_projects_url ) );
					switch ( $post->post_status ) {
						case 'publish' :
							$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_projects_url ) );
							?>
							<a class="view-btn btn-action-icon view btn-action-sm" href="<?php the_permalink(); ?>" title="<?php esc_attr_e('View', 'wp-freeio'); ?>">
								<?php esc_html_e('View', 'wp-freeio'); ?>
							</a>

							<?php
							$filled = WP_Freeio_Project::get_post_meta($post->ID, 'filled');
							if ( $filled ) {
								$classes = 'mark_not_filled';
								$title = esc_html__('Mark not filled', 'wp-freeio');
								$nonce = wp_create_nonce( 'wp-freeio-mark-not-filled-nonce' );
							} else {
								$classes = 'mark_filled';
								$title = esc_html__('Mark filled', 'wp-freeio');
								$nonce = wp_create_nonce( 'wp-freeio-mark-filled-nonce' );
							}
							?>
							<a class="fill-btn btn-action-icon btn-action-sm <?php echo esc_attr($classes); ?>" href="javascript:void(0);" title="<?php echo esc_attr($title); ?>" data-project_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><?php echo esc_attr($title); ?></a>

							<?php
							$edit_able = wp_freeio_get_option('user_edit_published_submission');
							if ( $edit_able !== 'no' ) {
								?>
								<a href="<?php echo esc_url($edit_url); ?>" class="edit-btn btn-action-icon edit btn-action-sm project-table-action" title="<?php esc_attr_e('Edit', 'wp-freeio'); ?>">
									<?php esc_html_e('Edit', 'wp-freeio'); ?>
								</a>
								<?php
							}
							break;
						case 'expired' :
							$relist_url = add_query_arg( 'action', 'relist', remove_query_arg( 'action', $my_projects_url ) );
							?>
							<a href="<?php echo esc_url($relist_url); ?>" class="btn-action-icon view btn-action-sm project-table-action" title="<?php esc_attr_e('Relist', 'wp-freeio'); ?>">
								<?php esc_html_e('Relist', 'wp-freeio'); ?>
							</a>
							<?php
							break;
						case 'pending_payment' :
						case 'pending_approve':
						case 'pending' :
							$edit_able = wp_freeio_get_option('user_edit_published_submission');
							if ( $edit_able !== 'no' ) {
								$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_projects_url ) );
								?>
								<a href="<?php echo esc_url($edit_url); ?>" class="edit-btn btn-action-icon edit btn-action-sm project-table-action" title="<?php esc_attr_e('Edit', 'wp-freeio'); ?>">
									<?php esc_html_e('Edit', 'wp-freeio'); ?>
								</a>
								<?php
							}
						break;
						case 'draft' :
						case 'preview' :
							$continue_url = add_query_arg( 'action', 'continue', remove_query_arg( 'action', $my_projects_url ) );
							?>
							<a href="<?php echo esc_url($continue_url); ?>" class="edit-btn btn-action-icon relist btn-action-sm project-table-action" title="<?php esc_attr_e('Continue', 'wp-freeio'); ?>">
								<?php esc_html_e('Continue', 'wp-freeio'); ?>
							</a>
							<?php
							break;
					}
					?>

					<a class="remove-btn" href="javascript:void(0)" class="project-table-action project-button-delete" data-project_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-delete-project-nonce' )); ?>" title="<?php esc_attr_e('Remove', 'wp-freeio'); ?>">
						<?php esc_html_e( 'Remove', 'wp-freeio' ); ?>
					</a>

				</td>
			</tr>
		<?php endwhile; ?>
		</tbody>
	</table>

	<?php
		WP_Freeio_Mixes::custom_pagination( array(
			'max_num_pages' => $projects->max_num_pages,
			'prev_text'     => '<i class="ti-angle-left"></i>',
			'next_text'     => '<i class="ti-angle-right"></i>',
			'wp_query' => $projects
		));
		
		wp_reset_postdata();
	?>
<?php else : ?>
	<div class="alert alert-warning">
		<p><?php esc_html_e( 'You don\'t have any projects, yet. Start by creating new one.', 'wp-freeio' ); ?></p>
	</div>
<?php endif; ?>
