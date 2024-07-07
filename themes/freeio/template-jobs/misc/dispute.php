<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$user_id = get_current_user_id();

$rand = rand(0000,9999);

$dispute_page_url = get_permalink();
?>
<div class="box-dashboard-wrapper">
	<div class="d-flex">
		<h3 class="widget-title"><?php echo esc_html__('Dispute','freeio') ?></h3>
		<div class="ms-auto">
			<a href="#create-dispute-form-wrapper-<?php echo esc_attr($user_id); ?>-<?php echo esc_attr($rand); ?>" class="btn btn-theme btn-sm btn-inverse btn-show-popup"><?php echo esc_attr_e('Create Dispute', 'freeio'); ?></a>
		</div>
	</div>

	<div id="create-dispute-form-wrapper-<?php echo esc_attr($user_id); ?>-<?php echo esc_attr($rand); ?>" class="view-proposal-description-wrapper mfp-hide">
		<a href="javascript:void(0);" class="close-magnific-popup ali-right"><i class="ti-close"></i></a>
		<form method="post" action="" id="create-dispute-form-<?php echo esc_attr($rand); ?>" class="create-dispute-form form-theme" enctype="multipart/form-data">

			<?php
			if ( WP_Freeio_User::is_freelancer($user_id) ) {
				$dispute_args = array(
					'posts_per_page' => -1,
					'post_type' => 'project_proposal',
					'orderby' => 'ID',
					'order' => 'DESC',
					'author' => $user_id,
					'post_status' => array('publish', 'cancelled', 'hired'),
					'suppress_filters'  => false,
				);

				$dispute_service_args = array(
					'posts_per_page' => -1,
					'post_type' => 'service_order',
					'orderby' => 'ID',
					'order' => 'DESC',
					'post_status' => array('cancelled', 'hired'),
					'suppress_filters' => false,
					'meta_query' => array(
						array(
							'key' 			=> WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author',
							'value' 			=> $user_id,
							'compare' 		=> '='
						),
					)
				);
			} else {
				$dispute_args = array(
					'posts_per_page' => -1,
					'post_type' => 'project_proposal',
					'orderby' => 'ID',
					'order' => 'DESC',
					'post_status' => array('publish', 'cancelled', 'hired'),
					'suppress_filters'  => false,
					'meta_query'		=> array(
						array(
							'key' 			=> WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'employer_id',
							'value' 			=> $user_id,
							'compare' 		=> '='
						),
					)
				);

				$dispute_service_args = array(
					'posts_per_page' => -1,
					'post_type' => 'service_order',
					'orderby' => 'ID',
					'order' => 'DESC',
					'post_status' => array('cancelled', 'hired'),
					'suppress_filters' => false,
					'author' => $user_id,
				);
			}

			$dispute_query = get_posts($dispute_args);
			$dispute_service_query = get_posts($dispute_service_args);

			if( !empty($dispute_query) && !empty( $dispute_service_query ) ){
				$dispute_query	= array_merge($dispute_query,$dispute_service_query);
			} else if( empty($dispute_query) && !empty( $dispute_service_query ) ){
				$dispute_query	= $dispute_service_query;
			} else if( !empty($dispute_query) && empty( $dispute_service_query ) ){
				$dispute_query	= $dispute_query;
			} else{
				$dispute_query	= array();
			}

			?>
			<div class="form-group">
				<label for="dispute_project"><?php echo esc_html__( 'Select project/service', 'freeio' ); ?></label>
				<select name="dispute_project" class="form-control" required>
					<option value=""><?php esc_html_e('Choose a Service/Project', 'freeio'); ?></option>
					<?php foreach ($dispute_query as $item) {
						$post_title	= $item->post_title;
						$post_type	= $item->post_type;
						if ( WP_Freeio_User::is_freelancer($user_id) ) {
							if ( !empty($post_type) && $post_type === 'service_order' ) {
								$post_author = get_post_field( 'post_author', $item->ID );
								$employer_user_id = WP_Freeio_User::get_employer_by_user_id($post_author);
								$post_title = $post_title.' ('.get_the_title($employer_user_id).')';
							} else {
								$post_author = get_post_meta( $item->ID, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'employer_id', true );
								$employer_user_id = WP_Freeio_User::get_employer_by_user_id($post_author);
								$post_title = $post_title.' ('.get_the_title($employer_user_id).')';
							}
						} else {
							if ( !empty($post_type) && $post_type === 'service_order' ) {
								$post_author = get_post_meta( $item->ID, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author', true );
								$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($post_author);
								$post_title = $post_title.' ('.get_the_title($freelancer_user_id).')';
							} else {
								$post_author = get_post_field( 'post_author', $item->ID );
								$freelancer_user_id = WP_Freeio_User::get_freelancer_by_user_id($post_author);
								$post_title = $post_title.' ('.get_the_title($freelancer_user_id).')';
							}
						}
					?>
						<option value="<?php echo esc_attr( $item->ID ); ?>"><?php echo esc_attr($post_title); ?></option>
					<?php } ?>
				</select>
			</div><!-- /.form-control -->

			<div class="form-group">
				<label for="dispute_title"><?php echo esc_html__( 'Tell us what your dispute is about', 'freeio' ); ?></label>
				<input id="dispute_title" class="form-control" type="text" name="dispute_title" required="required">
			</div><!-- /.form-control -->

			<div class="form-group">
				<label for="dispute-description"><?php echo esc_html__( 'Description', 'freeio' ); ?></label>
				<textarea id="dispute-description" name="description" class="form-control" required="required"></textarea>
			</div><!-- /.form-control -->

			<button type="submit" name="submit_dispute" class="button btn btn-theme w-100 btn-inverse"><?php esc_html_e( 'Send Dispute', 'freeio' ); ?><i class="flaticon-right-up next"></i></button>
		</form>
	</div>

	<!-- List Disputes -->
	<div class="inner-list">
		<?php
		$user_id = WP_Freeio_User::get_user_id();
		$paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
		$query_vars = array(
			'post_type'     => 'dispute',
			'post_status'   => array( 'publish', 'completed', 'pending' ),
			'paged'         => $paged,
			'orderby'		=> 'date',
			'order'			=> 'DESC',
			'meta_query' => array(
				array(
					'relation' => 'OR',
             	array(
                 'key'     => WP_FREEIO_DISPUTE_PREFIX.'sender',
                 'value'   => intval( $user_id ),
                 'compare' => '=',
             	),
             	array(
                 'key'     => WP_FREEIO_DISPUTE_PREFIX.'receipt',
                 'value'   => intval( $user_id ),
                 'compare' => '=',
             	),
          	)
         ),
		);
		$query = new WP_Query($query_vars);
		
		if ( $query->have_posts() ) :
		?>
			<div class="table-responsive">
				<table class="job-table">
					<thead>
						<tr>
							<th class="job-title-td"><?php esc_html_e('Title', 'freeio'); ?></th>
							<th class="job-date"><?php esc_html_e('Project/Service', 'freeio'); ?></th>
							<th class="job-date"><?php esc_html_e('User Name', 'freeio'); ?></th>
							<th class="job-date"><?php esc_html_e('Created', 'freeio'); ?></th>
							<th class="job-status"><?php esc_html_e('Status', 'freeio'); ?></th>
							<th class="job-actions"><?php esc_html_e('Actions', 'freeio'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php while ( $query->have_posts() ) : $query->the_post(); global $post; ?>
						<?php
						$dispute_page_url = add_query_arg( 'dispute_id', $post->ID, remove_query_arg( 'dispute_id', $dispute_page_url ) );
						$view_detail_url = add_query_arg( 'action', 'view-detail', remove_query_arg( 'action', $dispute_page_url ) );
						?>
						<tr class="my-item-wrapper">
							<td class="job-table-info">
								
								<div class="job-table-info-content">
									<div class="title-wrapper">
										<h3 class="job-table-info-content-title">
											<a href="<?php echo esc_url($view_detail_url); ?>" title="<?php esc_attr_e('View detail', 'freeio'); ?>">
												<?php the_title(); ?>
											</a>
										</h3>
									</div>
								</div>
							</td>

							<td>
								<div class="job-table-info-content-project">
									<?php
									$post_id = get_post_meta($post->ID, WP_FREEIO_DISPUTE_PREFIX.'post_id', true);
									$post_type = get_post_type($post_id);
									if ( $post_type == 'project_proposal' ) {
										$project_id = get_post_meta($post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'project_id', true);
										$project = get_post($project_id);
										if ( $project ) {
									?>
											<h5><a href="<?php echo esc_url(get_permalink($project)); ?>"><?php echo get_the_title($project_id); ?></a></h5>
										<?php } ?>
									<?php } elseif ( $post_type == 'service_order' ) {
											$service_id = get_post_meta($post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_id', true);
											$service = get_post($service_id);
											if ( $service ) {
										?>
												<h5><a href="<?php echo esc_url(get_permalink($service)); ?>"><?php echo get_the_title($service_id); ?></a></h5>
											<?php } ?>

									<?php } ?>
								</div>
							</td>

							<td>
								<div class="job-table-info-content-project">
									<?php
									if ( WP_Freeio_User::is_freelancer($user_id) ) {
										if ( !empty($post_type) && $post_type === 'service_order' ) {
											$post_author = get_post_field( 'post_author', $post_id );
											$post_user_id = WP_Freeio_User::get_employer_by_user_id($post_author);
										} else {
											$post_author = get_post_meta( $post_id, WP_FREEIO_PROJECT_PROPOSAL_PREFIX.'employer_id', true );
											$post_user_id = WP_Freeio_User::get_employer_by_user_id($post_author);
										}
									} else {
										if ( !empty($post_type) && $post_type === 'service_order' ) {
											$post_author = get_post_meta( $post_id, WP_FREEIO_SERVICE_ORDER_PREFIX.'service_author', true );
											$post_user_id = WP_Freeio_User::get_freelancer_by_user_id($post_author);
										} else {
											$post_author = get_post_field( 'post_author', $post_id );
											$post_user_id = WP_Freeio_User::get_freelancer_by_user_id($post_author);
										}
									}
									?>
									<a href="<?php echo get_permalink($post_user_id); ?>"><?php echo get_the_title($post_user_id); ?></a>
								</div>
							</td>

							<td>
								<div class="job-table-info-content-date">
									<div class="created">
										<?php the_time( get_option('date_format') ); ?>
									</div>
								</div>
							</td>

							<td class="job-table-status nowrap">
								<?php
				        		$resolved = get_post_meta($post->ID, WP_FREEIO_DISPUTE_PREFIX.'resolved', true);
					    		if ( !$resolved ) {
					    			$classes = 'bg-pending';
					    		} else {
					    			$classes = 'bg-success';
					    		}
								?>
								<div class="job-table-actions-inner">
									<div class="badge <?php echo esc_attr($classes);?>">
										<?php
											if ( $resolved ) {
												esc_html_e('Resolved', 'freeio');
											} else {
												esc_html_e('Ongoing', 'freeio');
											}
										?>
									</div>
								</div>
							</td>

							<td class="job-table-actions nowrap">
								<div class="action-button">
									<a class="btn btn-sm btn-theme" href="<?php echo esc_url($view_detail_url); ?>" title="<?php esc_attr_e('View detail', 'freeio'); ?>">
										<?php esc_html_e('View detail', 'freeio'); ?>
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
					'wp_query' => $query,
					'max_num_pages' => $query->max_num_pages,
					'prev_text'     => '<i class="ti-angle-left"></i>',
					'next_text'     => '<i class="ti-angle-right"></i>',
				));

				wp_reset_postdata();
			?>
		<?php else : ?>
			<div class="alert alert-warning">
				<p><?php esc_html_e( 'You don\'t have any dispute.', 'freeio' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>