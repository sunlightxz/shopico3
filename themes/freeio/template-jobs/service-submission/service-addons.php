<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
freeio_load_select2();

$service_addons_url = get_permalink();

?>

<div class="box-dashboard-wrapper my-job-employer">
	<h3 class="widget-title"><?php echo esc_html__('Manage Services Addons','freeio') ?></h3>
	<div class="inner-list">
		<div class="search-orderby-wrapper d-sm-flex align-items-center">
			<div class="search-my-jobs-form search-applicants-form widget_search">
				<form action="" method="get">
					<input type="text" placeholder="<?php esc_attr_e( 'Search ...', 'freeio' ); ?>" class="form-control" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
					<button class="search-submit btn btn-sm btn-search" name="submit">
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
				'post_type'     => 'service_addon',
				'post_status'   => apply_filters('wp-freeio-my-services-addon-post-statuses', array( 'publish', 'expired', 'pending', 'pending_payment', 'pending_approve', 'draft', 'preview' )),
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
							<th class="job-title"><?php esc_html_e('Title', 'freeio'); ?></th>
							<th class="job-cost-type"><?php esc_html_e('Cost', 'freeio'); ?></th>
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
									</div>
									<div class="job-metas">
										<?php echo wpautop($post->post_content); ?>
									</div>
									
								</div>
							</td>

							<td>
								<div class="job-table-info-content-cost-type">
									<span class="price-wrapper">
										<?php
											$price = get_post_meta($post->ID, WP_FREEIO_SERVICE_ADDON_PREFIX . 'price', true);
											echo WP_Freeio_Price::format_price($price);
										?>
									</span>
								</div>
							</td>

							<td class="job-table-actions nowrap">
								<div class="action-button">
									<?php
									$service_addons_url = add_query_arg( 'service_addon_id', $post->ID, remove_query_arg( 'service_addon_id', $service_addons_url ) );
									switch ( $post->post_status ) {
										case 'publish' :
											$edit_url = add_query_arg( 'action', 'edit', remove_query_arg( 'action', $service_addons_url ) );
											?>
											<a data-toggle="tooltip" class="edit-btn btn-action-icon edit job-table-action" href="<?php echo esc_url($edit_url); ?>" title="<?php esc_attr_e('Edit', 'freeio'); ?>">
												<i class="ti-pencil-alt"></i>
											</a>
											<?php
											break;
									}
									?>

									<a data-toggle="tooltip" class="remove-btn btn-action-icon deleted job-table-action service-addon-button-delete" href="javascript:void(0)" data-service_addon_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-delete-service-addon-nonce' )); ?>" title="<?php esc_attr_e('Remove', 'freeio'); ?>">
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
				<p><?php esc_html_e( 'You don\'t have any service addons, yet. Start by creating new one.', 'freeio' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
	<h3 class="widget-title"><?php echo esc_html__('Add New Addons','freeio') ?></h3>
	<div class="inner-list">
		<form id="submit-service-addon-form" class="submit-service-addon-form form-theme" method="post" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			$title = $description = $price = '';
			if ( isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['service_addon_id']) ) {
				$service_addon_id = sanitize_text_field($_GET['service_addon_id']);
				$addon_post = get_post($service_addon_id);
				if ( $addon_post ) {
					$title = $addon_post->post_title;	
					$description = $addon_post->post_content;	
					$price = get_post_meta($service_addon_id, WP_FREEIO_SERVICE_ADDON_PREFIX . 'price', true);	
				}
				
			}
			?>
			<div class="form-group">
				<label for="service-addon-title"><?php esc_html_e('Title','freeio'); ?></label>
				<input autocomplete="off" id="service-addon-title" type="text" name="title" class="form-control" placeholder="<?php esc_attr_e('Title','freeio'); ?>" required value="<?php echo esc_attr($title); ?>">
			</div>
			<div class="form-group">
				<label for="service-addon-description"><?php esc_html_e('Description','freeio'); ?></label>
				<textarea autocomplete="off" id="service-addon-description" name="description" class="form-control"><?php echo trim($description); ?></textarea>
			</div>
			<div class="form-group">
				<label for="service-addon-price"><?php esc_html_e('Price','freeio'); ?></label>
				<input autocomplete="off" id="service-addon-price" type="number" min="0" pattern="\d*" name="price" class="form-control" placeholder="100" required value="<?php echo esc_attr($price); ?>">
			</div>

			<?php wp_nonce_field('wp-freeio-submit-service-addon-nonce', 'submit_service_addon_nonce_security'); ?>

			<?php if ( !empty($service_addon_id) ) { ?>
				<input type="hidden" name="service_addon_id" value="<?php echo esc_attr($service_addon_id); ?>">
			<?php } ?>

			<div class="clearfix">
				<button type="submit" class="btn btn-theme btn-inverse" name="submit"><?php echo esc_html__('Submit','freeio'); ?><i class="flaticon-right-up next"></i></button>
			</div>
		</form>
	</div>
</div>