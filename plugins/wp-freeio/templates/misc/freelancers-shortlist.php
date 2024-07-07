<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_enqueue_script('wpfi-select2');
wp_enqueue_style('wpfi-select2');
?>

<div class="search-orderby-wrapper">
	<div class="search-freelancers-shortlist-form">
		<form action="" method="get">
			<div class="form-group">
				<input type="text" name="search" value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>">
			</div>
			<div class="submit-wrapper">
				<button class="search-submit" name="submit">
					<?php esc_html_e( 'Search ...', 'wp-freeio' ); ?>
				</button>
			</div>
			<input type="hidden" name="paged" value="1" />
		</form>
	</div>
	<div class="sort-freelancers-shortlist-form sortby-form">
		<?php
			$orderby_options = apply_filters( 'wp_freeio_my_jobs_orderby', array(
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
			<form class="freelancers-shortlist-ordering" method="get">
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
if ( !empty($freelancer_ids) && is_array($freelancer_ids) ) {
	if ( get_query_var( 'paged' ) ) {
	    $paged = get_query_var( 'paged' );
	} elseif ( get_query_var( 'page' ) ) {
	    $paged = get_query_var( 'page' );
	} else {
	    $paged = 1;
	}
	$query_vars = array(
		'post_type'         => 'freelancer',
		'posts_per_page'    => get_option('posts_per_page'),
		'paged'    			=> $paged,
		'post_status'       => 'publish',
		'post__in'       	=> $freelancer_ids,
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

	$freelancers = new WP_Query($query_vars);
	if ( $freelancers->have_posts() ) {
		while ( $freelancers->have_posts() ) : $freelancers->the_post();
			global $post;

			$categories = get_the_terms( $post->ID, 'freelancer_category' );
			$address = get_post_meta( $post->ID, WP_FREEIO_FREELANCER_PREFIX . 'address', true );
			$salary = WP_Freeio_Freelancer::get_salary_html($post->ID);

			?>

			<?php do_action( 'wp_freeio_before_freelancer_content', $post->ID ); ?>

			<article <?php post_class('freelancer-shortlist-wrapper'); ?>>

				<?php if ( has_post_thumbnail() ) { ?>
			        <div class="freelancer-thumbnail">
			            <?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?>
			        </div>
			    <?php } ?>
			    <div class="freelancer-information">
			    	
					<?php the_title( sprintf( '<h2 class="entry-title freelancer-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

			        <?php if ( $categories ) { ?>
			            <?php foreach ($categories as $term) { ?>
			                <a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
			            <?php } ?>
			        <?php } ?>
			        <!-- rating -->

			        <?php if ( $address ) { ?>
			            <div class="freelancer-location">
			                <?php esc_html_e('Location', 'wp-freeio'); ?>
			                <?php echo $address; ?>
			            </div>
			        <?php } ?>

			        <?php if ( $salary ) { ?>
			            <div class="freelancer-salary">
			                <?php esc_html_e('Salary', 'wp-freeio'); ?>
			                <?php echo $salary; ?>
			            </div>
			        <?php } ?>
				</div>

			    <a href="<?php the_permalink(); ?>" class="btn button"><?php esc_html_e('View Profile', 'wp-freeio'); ?></a>

			    <a href="javascript:void(0)" class="btn-remove-freelancer-shortlist" data-freelancer_id="<?php echo esc_attr($post->ID); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp-freeio-remove-freelancer-shortlist-nonce' )); ?>"><?php esc_html_e('Remove', 'wp-freeio'); ?></a>
			</article><!-- #post-## -->

			<?php do_action( 'wp_freeio_after_freelancer_content', $post->ID );
		endwhile;
		
		WP_Freeio_Mixes::custom_pagination( array(
			'max_num_pages' => $freelancers->max_num_pages,
			'prev_text'     => esc_html__( 'Previous page', 'wp-freeio' ),
			'next_text'     => esc_html__( 'Next page', 'wp-freeio' ),
			'wp_query' => $freelancers
		));

		wp_reset_postdata();
	}
?>

<?php } else { ?>
	<div class="not-found"><?php esc_html_e('Don\'t have any freelancers in shortlist', 'wp-freeio'); ?></div>
<?php } ?>