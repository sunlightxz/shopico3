<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_script('wpfi-select2');
wp_enqueue_style('wpfi-select2');
?>
<div class="search-orderby-wrapper flex-middle-sm">
	<div class="search-jobs-applied-form widget-search">
		<form action="" method="get">
			<div class="input-group">
				<input type="text" placeholder="<?php echo esc_html__( 'Search ...', 'wp-freeio' ); ?>" class="form-control" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
				<span class="input-group-btn">
					<button class="search-submit btn btn-sm btn-search" name="submit">
						<i class="flaticon-magnifying-glass"></i>
					</button>
				</span>
			</div>
			<input type="hidden" name="paged" value="1" />
		</form>
	</div>
	<div class="sort-jobs-applied-form sortby-form">
		<?php
			$orderby_options = apply_filters( 'wp_freeio_my_jobs_orderby', array(
				'menu_order'	=> esc_html__( 'Default', 'wp-freeio' ),
				'newest' 		=> esc_html__( 'Newest', 'wp-freeio' ),
				'oldest'     	=> esc_html__( 'Oldest', 'wp-freeio' ),
			) );

			$orderby = isset( $_GET['orderby'] ) ? wp_unslash( $_GET['orderby'] ) : 'newest'; 
		?>

		<div class="orderby-wrapper flex-middle">
			<span class="text-sort">
				<?php echo esc_html__('Sort by: ','wp-freeio'); ?>
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
if ( !empty($applicants) && !empty($applicants->posts) ) {

	foreach ($applicants->posts as $applicant_id) {
		$job_id = get_post_meta($applicant_id, WP_FREEIO_APPLICANT_PREFIX.'job_id', true);
		$post = get_post($job_id);


		$author_id = WP_Freeio_Job_Listing::get_author_id($post->ID);
		$employer_id = WP_Freeio_User::get_employer_by_user_id($author_id);
		$types = get_the_terms( $post->ID, 'job_listing_type' );
		$address = get_post_meta( $post->ID, WP_FREEIO_JOB_LISTING_PREFIX . 'address', true );
		$salary = WP_Freeio_Job_Listing::get_salary_html($post->ID);

		?>

		<?php do_action( 'wp_freeio_before_job_content', $post->ID ); ?>

		<article <?php post_class('job-applied-wrapper'); ?>>

			<?php if ( has_post_thumbnail($employer_id) ) { ?>
		        <div class="employer-thumbnail">
		            <?php echo get_the_post_thumbnail( $employer_id, 'thumbnail' ); ?>
		        </div>
		    <?php } ?>
		    <div class="job-information">
		    	<?php if ( $types ) { ?>
		            <?php foreach ($types as $term) { ?>
		                <a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
		            <?php } ?>
		        <?php } ?>
		        <h2 class="entry-title job-title">
		        	<a href="<?php echo esc_url(get_permalink($job_id)); ?>" rel="bookmark"><?php echo get_the_title($job_id); ?></a>
		        </h2>

		        <div class="job-date-author">
		            <?php echo sprintf( __(' posted %s ago', 'wp-freeio'), human_time_diff(get_the_time('U', $job_id), current_time('timestamp')) ); ?> 
		            <?php
		            if ( $employer_id ) {
		                echo sprintf( __('by %s', 'wp-freeio'), get_the_title($employer_id) );
		            }
		            ?>
		        </div>
		        <div class="job-metas">
		            <?php if ( $address ) { ?>
		                <div class="job-location"><?php echo $address; ?></div>
		            <?php } ?>
		            <?php if ( $salary ) { ?>
		                <div class="job-salary"><?php echo $salary; ?></div>
		            <?php } ?>
		        </div>

			</div>

			<a href="javascript:void(0)" class="btn-remove-job-applied" data-applicant_id="<?php echo esc_attr($applicant_id); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-remove-applied-nonce' )); ?>"><?php esc_html_e('Remove', 'wp-freeio'); ?></a>

		</article><!-- #post-## -->

		<?php do_action( 'wp_freeio_after_job_content', $post->ID );

	}

	WP_Freeio_Mixes::custom_pagination( array(
		'max_num_pages' => $applicants->max_num_pages,
		'prev_text'     => esc_html__( 'Previous page', 'wp-freeio' ),
		'next_text'     => esc_html__( 'Next page', 'wp-freeio' ),
		'wp_query' => $applicants
	));
?>

<?php } else { ?>
	<div class="not-found"><?php esc_html_e('No apply found.', 'wp-freeio'); ?></div>
<?php } ?>