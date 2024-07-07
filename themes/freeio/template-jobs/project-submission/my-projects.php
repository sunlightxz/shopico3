<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
freeio_load_select2();

$my_projects_page_id = wp_freeio_get_option('my_projects_page_id');
$my_projects_url = get_permalink( $my_projects_page_id );

?>

<div class="box-dashboard-wrapper my-job-employer">
	<h3 class="widget-title"><?php echo esc_html__('Manage Projects','freeio') ?></h3>
	<div class="inner-list">
		<div class="search-orderby-wrapper d-sm-flex align-items-center">
			<div class="search-my-jobs-form search-applicants-form widget_search">
				<form action="" method="get">
					<input type="text" placeholder="<?php esc_attr_e( 'Search ...', 'freeio' ); ?>" class="form-control" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
					<input type="hidden" name="paged" value="1" />
					<button class="search-submit btn btn-search" name="submit">
						<i class="flaticon-loupe"></i>
					</button>
				</form>
			</div>
			<div class="sort-my-jobs-form sortby-form ms-auto">
				<?php
					$orderby_options = apply_filters( 'wp_freeio_my_projects_orderby', array(
						'menu_order'	=> esc_html__( 'Default', 'freeio' ),
						'newest' 		=> esc_html__( 'Newest', 'freeio' ),
						'oldest'     	=> esc_html__( 'Oldest', 'freeio' ),
					) );

					$orderby = isset( $_GET['orderby'] ) ? wp_unslash( $_GET['orderby'] ) : 'newest'; 
				?>

				<div class="orderby-wrapper d-flex align-items-center">
					<span class="text-sort">
						<?php echo esc_html__('Sort by: ','freeio'); ?>
					</span>
					<form class="my-jobs-ordering" method="get">
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
				'post_status'   => apply_filters('wp-freeio-my-projects-post-statuses', array( 'publish','hired','completed','cancelled', 'expired', 'pending', 'pending_payment', 'pending_approve', 'draft', 'preview' )),
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
			
			if ( $projects->have_posts() ) :
			?>
			<div class="table-responsive">
				<table class="job-table">
					<thead>
						<tr>
							<th class="job-title-td"><?php esc_html_e('Title', 'freeio'); ?></th>
							<th class="job-date"><?php esc_html_e('Expired', 'freeio'); ?></th>
							<th class="job-cost-type"><?php esc_html_e('Cost/Type', 'freeio'); ?></th>
							<th class="job-status"><?php esc_html_e('Status', 'freeio'); ?></th>
							<th class="job-proposals"><?php esc_html_e('Actions', 'freeio'); ?></th>
							<th class="job-actions"></th>
						</tr>
					</thead>
					<tbody>
					<?php while ( $projects->have_posts() ) : $projects->the_post(); global $post;
						$my_projects_url = add_query_arg( 'project_id', $post->ID, remove_query_arg( 'project_id', $my_projects_url ) );
					?>
						<tr class="my-item-wrapper">
							<td class="job-table-info">
								
								<div class="job-table-info-content">
									<div class="title-wrapper">
										<h3 class="job-table-info-content-title">
											<?php if ( $post->post_status == 'publish' ) { ?>
												<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
											<?php } else { ?>
												<?php the_title(); ?>
											<?php } ?>
										</h3>
										<?php freeio_project_display_featured_icon($post); ?>
									</div>
									<?php freeio_project_display_proposals_count($post); ?>
									<div class="listing-metas d-flex flex-wrap align-items-start">
										<?php freeio_project_display_category($post, 'icon'); ?>
										<?php freeio_project_display_short_location($post, 'icon'); ?>
										<?php freeio_project_display_postdate($post, 'icon'); ?>
									</div>
									
								</div>
							</td>

							<td>
								<div class="job-table-info-content-date-expiry">
									<div class="expiry-date">
										<span class="text-danger">
										<?php
											$expires = get_post_meta( $post->ID, WP_FREEIO_PROJECT_PREFIX.'expiry_date', true);
											if ( $expires ) {
												echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expires ) ) );
											} else {
												echo '--';
											}
										?>
										</span>
									</div>
								</div>
							</td>

							<td>
								<div class="price-wrapper">
									<?php freeio_project_display_price($post); ?>
								</div>
							</td>

							<td class="job-table-status">
								<?php
				        		$post_status = get_post_status_object( $post->post_status );
				        		if ( $post->post_status == 'pending' ) {
				        			$classes = 'bg-pending';
				        		} elseif( $post->post_status == 'cancelled' ) {
				        			$classes = 'bg-cancelled';
				        		} else {
				        			$classes = 'bg-success';
				        		}
								?>
								<div class="badge <?php echo esc_attr($classes);?>">
									<?php
										if ( !empty($post_status->label) ) {
											echo esc_html($post_status->label);
										} else {
											echo esc_html($post_status->post_status);
										}
									?>
								</div>
							</td>
							<td class="job-table-proposals">
								<?php $view_proposal_url = add_query_arg( 'action', 'view-proposals', remove_query_arg( 'action', $my_projects_url ) ); ?>
								<a href="<?php echo esc_url($view_proposal_url); ?>" class="btn btn-theme btn-sm"><?php esc_html_e('View Proposals', 'freeio'); ?></a>
							</td>
							<td class="job-table-actions nowrap">
								<div class="action-button">
									<?php
									
									switch ( $post->post_status ) {
										case 'publish' :
											$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_projects_url ) );
											
											?>

											<a data-toggle="tooltip" class="edit-btn btn-action-icon edit job-table-action" href="<?php echo esc_url($edit_url); ?>" title="<?php esc_attr_e('Edit', 'freeio'); ?>">
												<i class="ti-pencil-alt"></i>
											</a>
											<?php
											break;
										case 'expired' :
											$relist_url = add_query_arg( 'action', 'relist', remove_query_arg( 'action', $my_projects_url ) );
											?>
											<a data-toggle="tooltip" href="<?php echo esc_url($relist_url); ?>" class="btn-action-icon edit job-table-action" title="<?php esc_attr_e('Relist', 'freeio'); ?>">
												<i class="fa fa-registered"></i>
											</a>
											<?php
											break;
										case 'pending_payment':
											$order_id = get_post_meta($post->ID, WP_FREEIO_PROJECT_PREFIX.'order_id', true);
											if ( $order_id ) {
												$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_projects_url ) );
												?>
												<a data-toggle="tooltip" class="edit-btn btn-action-icon edit job-table-action" href="<?php echo esc_url($edit_url); ?>" title="<?php esc_attr_e('Edit', 'freeio'); ?>">
													<i class="ti-pencil-alt"></i>
												</a>
												<?php
											} else {
												$continue_url = add_query_arg( 'action', 'continue', remove_query_arg( 'action', $my_projects_url ) );
												?>
												<a data-toggle="tooltip" href="<?php echo esc_url($continue_url); ?>" class="edit-btn btn-action-icon edit job-table-action" title="<?php esc_attr_e('Continue', 'freeio'); ?>">
													<i class="flaticon-right"></i>
												</a>
												<?php
											}
										break;
										case 'pending_approve':
										case 'pending' :
											$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_projects_url ) );
											?>
											<a data-toggle="tooltip" class="edit-btn btn-action-icon edit job-table-action" href="<?php echo esc_url($edit_url); ?>" title="<?php esc_attr_e('Edit', 'freeio'); ?>">
												<i class="ti-pencil-alt"></i>
											</a>
											<?php
										break;
										case 'draft' :
										case 'preview' :
											$continue_url = add_query_arg( 'action', 'continue', remove_query_arg( 'action', $my_projects_url ) );
											?>
											<a data-toggle="tooltip" href="<?php echo esc_url($continue_url); ?>" class="edit-btn btn-action-icon edit job-table-action" title="<?php esc_attr_e('Continue', 'freeio'); ?>">
												<i class="flaticon-right"></i>
											</a>
											<?php
											break;
									}
									?>


									<a data-toggle="tooltip" class="remove-btn btn-action-icon deleted job-table-action project-button-delete" href="javascript:void(0)" data-project_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-delete-project-nonce' )); ?>" title="<?php esc_attr_e('Remove', 'freeio'); ?>">
										<i class="flaticon-delete"></i>
									</a>
								</div>
							</td>
						</tr>
					<?php endwhile; ?>
					</tbody>
				</table>
			</div>
			<?php
				WP_Freeio_Mixes::custom_pagination( array(
					'wp_query' => $projects,
					'max_num_pages' => $projects->max_num_pages,
					'prev_text'     => '<i class="ti-angle-left"></i>',
					'next_text'     => '<i class="ti-angle-right"></i>',
				));

				wp_reset_postdata();
			?>
		<?php else : ?>
			<div class="alert alert-warning">
				<p><?php esc_html_e( 'You don\'t have any projects, yet. Start by creating new one.', 'freeio' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>