<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
freeio_load_select2();
?>
<div class="box-dashboard-wrapper">
	<h3 class="title"><?php echo esc_html__('Applied Jobs','freeio') ?></h3>
	<div class="inner-list">
		<div class="search-orderby-wrapper d-sm-flex align-items-center">
			<div class="search-jobs-applied-form search-applicants-form widget_search">
				<form action="" method="get">
					<input type="text" placeholder="<?php esc_attr_e( 'Search ...', 'freeio' ); ?>" class="form-control" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
					<button class="search-submit btn btn-search" name="submit">
						<i class="flaticon-loupe"></i>
					</button>
					<input type="hidden" name="paged" value="1" />
				</form>
			</div>
			<div class="sort-jobs-applied-form sortby-form ms-auto">
				<?php
					$orderby_options = apply_filters( 'wp_freeio_my_jobs_orderby', array(
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
		<?php if ( !empty($applicants) && !empty($applicants->posts) ) { ?>
			<div class="table-responsive">
			<table class="job-table">
				<thead>
					<tr>
						<th class="job-title-td"><?php esc_html_e('Job Title', 'freeio'); ?></th>
						<th class="job-date"><?php esc_html_e('Date Applied', 'freeio'); ?></th>
						<th class="job-status"><?php esc_html_e('Status', 'freeio'); ?></th>
						<th class="job-actions"><?php esc_html_e('Actions', 'freeio'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($applicants->posts as $applicant_id) {
						$job_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX.'job_id', true);
						$post = get_post($job_id);


						$author_id = freeio_get_post_author($post->ID);
						$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
						$types = get_the_terms( $post->ID, 'job_listing_type' );
						$category = get_the_terms( $post->ID, 'job_listing_category' );
						$location = get_the_terms( $post->ID, 'location' );
						$salary = WP_Freeio_Job_Listing::get_salary_html($post->ID);
						?>

						<tr class="job-applied-wrapper">
							<td>
								<div class="job-applied">
									<div class="d-md-flex align-items-center">
										<div class="logo-job position-relative flex-shrink-0">
											<?php freeio_job_display_employer_logo($post, true, true); ?>
										</div>
									    <div class="job-information-right flex-grow-1">
									        <h2 class="job-title">
									        	<a href="<?php echo esc_url(get_permalink($job_id)); ?>" rel="bookmark"><?php echo get_the_title($job_id); ?></a>
									        </h2>
						                     <div class="listing-metas d-flex align-items-start flex-wrap">
									        	<?php if ( $category ) { ?>
									        		<div class="category-job">
									        			<i class="flaticon-category"></i>
											            <?php foreach ($category as $term) { ?>
											                <a href="<?php echo get_term_link($term); ?>"><?php echo trim($term->name); ?></a>
											                <?php break; ?>
											            <?php } ?>
										            </div>
										        <?php } ?>
									            <?php if ( $location ) { ?>
									        		<div class="location-job">
									        			<i class="flaticon-place"></i>
											            <?php foreach ($location as $term) { ?>
											                <a href="<?php echo get_term_link($term); ?>"><?php echo trim($term->name); ?></a>
											                <?php break; ?>
											            <?php } ?>
										            </div>
										        <?php } ?>
									        </div>
									    </div>
									</div>
							    </div>
							</td>
							<td>
								<?php echo get_the_time(get_option('date_format'), $applicant_id); ?>
							</td>
							<td>
								<?php
			                        $app_status = WP_Freeio_Applicant::get_post_meta($applicant_id, 'app_status', true);

			                        if ( $app_status == 'rejected' ) {
										echo '<span class="job-table-status badge bg-cancelled">'.esc_html__('Rejected', 'freeio').'</span>';
									} elseif ( $app_status == 'approved' ) {
										echo '<span class="job-table-status badge bg-success">'.esc_html__('Approved', 'freeio').'</span>';
									} else {
										echo '<span class="job-table-status badge bg-pending">'.esc_html__('Pending', 'freeio').'</span>';
									}
			                    ?>
							</td>
							<td>
								<div class="action-button">
									<a href="javascript:void(0)" class="btn-remove-job-applied btn-action-icon deleted" data-applicant_id="<?php echo esc_attr($applicant_id); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-remove-applied-nonce' )); ?>" data-toggle="tooltip" title="<?php esc_attr_e('Remove', 'freeio'); ?>"><i class="flaticon-delete"></i></a>
									<a class="btn-action-icon" href="<?php echo esc_url(get_permalink($job_id)); ?>" data-toggle="tooltip" title="<?php esc_attr_e('View Job', 'freeio'); ?>"><i class="ti-eye"></i></a>
								</div>
							</td>
						</tr>

						<?php
					} ?>
				</tbody>
			</table>
			</div>

			<?php WP_Freeio_Mixes::custom_pagination( array(
				'wp_query' => $applicants,
				'max_num_pages' => $applicants->max_num_pages,
				'prev_text'     => '<i class="ti-angle-left"></i>',
				'next_text'     => '<i class="ti-angle-right"></i>',
			));
		?>

		<?php } else { ?>
			<div class="not-found"><?php esc_html_e('No application found.', 'freeio'); ?></div>
		<?php } ?>
	</div>
</div>