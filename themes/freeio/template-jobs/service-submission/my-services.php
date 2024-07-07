<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
freeio_load_select2();

$my_services_page_id = wp_freeio_get_option('my_services_page_id');
$my_services_url = get_permalink( $my_services_page_id );

?>

<div class="box-dashboard-wrapper my-service">
	<h3 class="title"><?php echo esc_html__('My Service','freeio') ?></h3>
	<div class="inner-list">
		<div class="search-orderby-wrapper d-sm-flex align-items-center">
			<div class="search-my-jobs-form search-applicants-form widget_search">
				<form action="" method="get">
					<input type="text" placeholder="<?php esc_attr_e( 'Search ...', 'freeio' ); ?>" class="form-control" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
					<button class="search-submit btn btn-search" name="submit">
						<i class="flaticon-loupe"></i>
					</button>
					<input type="hidden" name="paged" value="1" />
				</form>
			</div>
			<div class="sort-my-jobs-form sortby-form ms-auto">
				<?php
					$orderby_options = apply_filters( 'wp_freeio_my_services_orderby', array(
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
				'post_type'     => 'service',
				'post_status'   => apply_filters('wp-freeio-my-services-post-statuses', array( 'publish', 'expired', 'pending', 'pending_payment', 'pending_approve', 'draft', 'preview' )),
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
			$services = new WP_Query($query_vars);
			
			if ( $services->have_posts() ) :
			?>
			<div class="table-responsive">
				<table class="job-table">
					<thead>
						<tr>
							<th class="job-title-td"><?php esc_html_e('Title', 'freeio'); ?></th>
							<th class="job-date"><?php esc_html_e('Expired', 'freeio'); ?></th>
							<th class="job-cost-type"><?php esc_html_e('Cost/Type', 'freeio'); ?></th>
							<th class="job-status"><?php esc_html_e('Status', 'freeio'); ?></th>
							<th class="job-status"><?php esc_html_e('In Queue', 'freeio'); ?></th>
							<th class="job-actions"><?php esc_html_e('Actions', 'freeio'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php while ( $services->have_posts() ) : $services->the_post(); global $post; ?>
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
										<?php freeio_service_display_featured_icon($post); ?>
									</div>
									<div class="listing-metas d-flex flex-wrap align-items-start">
										<?php freeio_service_display_category($post, 'icon'); ?>
										<?php freeio_service_display_short_location($post, 'icon'); ?>
										<?php freeio_service_display_postdate($post, 'icon'); ?>
									</div>
									
								</div>
							</td>

							<td>
								<div class="job-table-info-content-date-expiry">
									<div class="expiry-date">
										<span class="text-danger">
										<?php
											$expires = get_post_meta( $post->ID, WP_FREEIO_SERVICE_PREFIX.'expiry_date', true);
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
								<div class="job-table-info-content-cost-type">
									<?php 
										$salary = WP_Freeio_Service::get_price_html($post->ID);
									?>
									<?php if( !empty($salary) ) { ?>
										<div class="price-wrapper">
											<?php freeio_service_display_price($post); ?>
										</div>
										<div class="price-type"><?php echo freeio_service_display_meta($post, 'delivery_time'); ?></div>
									<?php } else { ?>
										<?php echo '--'; ?>
									<?php } ?>
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

							<td>
								<?php
								$query_vars = array(
									'post_type'     => 'service_order',
									'posts_per_page' => 1,
									'post_status'   => apply_filters('wp-freeio-my-services-employer-post-statuses', array( 'publish', 'hired', 'completed', 'cancelled' )),
									'meta_query' => array(
							            array(
							                'key'     => WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id',
							                'value'   => intval( $post->ID ),
							                'compare' => '=',
							            ),
							        ),
							        'fields' => 'ids'
								);
								$service_orders = new WP_Query($query_vars);
								if ( $service_orders->posts ) {
									$view_inqueue_url = add_query_arg( 'action', 'view-inqueue', remove_query_arg( 'action', $my_services_url ) );
									$view_inqueue_url = add_query_arg( 'service_id', $post->ID, remove_query_arg( 'service_id', $view_inqueue_url ) );
								?>
									<a href="<?php echo esc_url($view_inqueue_url); ?>" class="btn btn-theme btn-sm"><?php echo sprintf(esc_html__('View in Queue (%s)', 'freeio'), $service_orders->found_posts); ?></a>
								<?php } else {
									echo '--';
								} ?>
							</td>
							<td class="job-table-actions nowrap">
								<div class="action-button">
									<?php
									$my_services_url = add_query_arg( 'service_id', $post->ID, remove_query_arg( 'service_id', $my_services_url ) );
									switch ( $post->post_status ) {
										case 'publish' :
											$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_services_url ) );
											
											?>

											<a data-toggle="tooltip" class="edit-btn btn-action-icon edit job-table-action" href="<?php echo esc_url($edit_url); ?>" title="<?php esc_attr_e('Edit', 'freeio'); ?>">
												<i class="flaticon-pencil"></i>
											</a>
											<?php
											break;
										case 'expired' :
											$relist_url = add_query_arg( 'action', 'relist', remove_query_arg( 'action', $my_services_url ) );
											?>
											<a data-toggle="tooltip" href="<?php echo esc_url($relist_url); ?>" class="btn-action-icon edit job-table-action" title="<?php esc_attr_e('Relist', 'freeio'); ?>">
												<i class="fa fa-registered"></i>
											</a>
											<?php
											break;
										case 'pending_payment':
											$order_id = get_post_meta($post->ID, WP_FREEIO_SERVICE_PREFIX.'order_id', true);
											if ( $order_id ) {
												$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_services_url ) );
												?>
												<a data-toggle="tooltip" class="edit-btn btn-action-icon edit job-table-action" href="<?php echo esc_url($edit_url); ?>" title="<?php esc_attr_e('Edit', 'freeio'); ?>">
													<i class="flaticon-pencil"></i>
												</a>
												<?php
											} else {
												$continue_url = add_query_arg( 'action', 'continue', remove_query_arg( 'action', $my_services_url ) );
												?>
												<a data-toggle="tooltip" href="<?php echo esc_url($continue_url); ?>" class="edit-btn btn-action-icon edit job-table-action" title="<?php esc_attr_e('Continue', 'freeio'); ?>">
													<i class="flaticon-right"></i>
												</a>
												<?php
											}
										break;
										case 'pending_approve':
										case 'pending' :
											$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $my_services_url ) );
											?>
											<a data-toggle="tooltip" class="edit-btn btn-action-icon edit job-table-action" href="<?php echo esc_url($edit_url); ?>" title="<?php esc_attr_e('Edit', 'freeio'); ?>">
												<i class="flaticon-pencil"></i>
											</a>
											<?php
										break;
										case 'draft' :
										case 'preview' :
											$continue_url = add_query_arg( 'action', 'continue', remove_query_arg( 'action', $my_services_url ) );
											?>
											<a data-toggle="tooltip" href="<?php echo esc_url($continue_url); ?>" class="edit-btn btn-action-icon edit job-table-action" title="<?php esc_attr_e('Continue', 'freeio'); ?>">
												<i class="flaticon-right"></i>
											</a>
											<?php
											break;
									}
									?>


									<a data-toggle="tooltip" class="remove-btn btn-action-icon deleted job-table-action service-button-delete" href="javascript:void(0)" data-service_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-delete-service-nonce' )); ?>" title="<?php esc_attr_e('Remove', 'freeio'); ?>">
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
					'wp_query' => $services,
					'max_num_pages' => $services->max_num_pages,
					'prev_text'     => '<i class="ti-angle-left"></i>',
					'next_text'     => '<i class="ti-angle-right"></i>',
				));

				wp_reset_postdata();
			?>
		<?php else : ?>
			<div class="alert alert-warning">
				<p><?php esc_html_e( 'You don\'t have any services, yet. Start by creating new one.', 'freeio' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>