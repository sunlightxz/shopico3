<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_query;

if ( get_query_var( 'paged' ) ) {
    $paged = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
    $paged = get_query_var( 'page' );
} else {
    $paged = 1;
}

$query_args = array(
	'post_type' => 'freelancer',
    'post_status' => 'publish',
    'post_per_page' => wp_freeio_get_option('number_freelancers_per_page', 10),
    'paged' => $paged,
);

$params = array();
$taxs = ['category', 'location', 'tag'];
foreach ($taxs as $tax) {
	if ( is_tax('freelancer_'.$tax) ) {
		$term = $wp_query->queried_object;
		if ( isset( $term->term_id) ) {
			$params['filter-'.$tax] = $term->term_id;
		}
	}
}
if ( WP_Freeio_Job_Filter::has_filter() ) {
	$params = array_merge($params, $_GET);
}

$freelancers = WP_Freeio_Query::get_posts($query_args, $params);

if ( isset( $_REQUEST['load_type'] ) && WP_Freeio_Mixes::is_ajax_request() ) {
	if ( 'items' !== $_REQUEST['load_type'] ) {
        echo WP_Freeio_Template_Loader::get_template_part('archive-freelancer-ajax-full', array('freelancers' => $freelancers));
	} else {
		echo WP_Freeio_Template_Loader::get_template_part('archive-freelancer-ajax-freelancers', array('freelancers' => $freelancers));
	}

} else {
	get_header();
	
	$layout_type = 'default';
	$filter_sidebar = 'freelancers-filter-sidebar';

	if ( $layout_type == 'half-map' ) {

	?>
		<section id="main-container" class="inner">
			<div class="mobile-groups-button d-block d-lg-none clearfix text-center">
				<button class=" btn btn-sm btn-theme btn-view-map" type="button"><i class="fa fa-map" aria-hidden="true"></i> <?php esc_html_e( 'Map View', 'freeio' ); ?></button>
				<button class=" btn btn-sm btn-theme btn-view-listing d-none d-lg-block" type="button"><i class="fa fa-list" aria-hidden="true"></i> <?php esc_html_e( 'Listing View', 'freeio' ); ?></button>
			</div>
			<div class="row m-0 layout-type-<?php echo esc_attr($layout_type); ?>">
				<div id="main-content" class="col-12 col-lg-6 col-xl-5 p-0">
					<div class="inner-left">
						<?php if ( is_active_sidebar( $filter_sidebar ) ): ?>
							<div class="filter-sidebar offcanvas-filter-sidebar">
								<div class="offcanvas-filter-sidebar-header d-flex align-items-center">
							        <div class="title"><?php echo esc_html__('All Filters','freeio'); ?></div>
							        <span class="close-filter-sidebar ms-auto d-flex align-items-center justify-content-center"><i class="ti-close"></i></span>
							    </div>
								<div class="filter-scroll">
						   			<?php dynamic_sidebar( $filter_sidebar ); ?>
						   		</div>
					   		</div>
				   			<div class="over-dark"></div>
					   	<?php endif; ?>
					   	<div class="content-listing">
							<?php
								echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/archive-inner', array('freelancers' => $freelancers));
								echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/pagination', array('freelancers' => $freelancers));
							?>
						</div>
					</div>
				</div><!-- .content-area -->
				<div class="col-12 col-lg-6 col-xl-7 p-0">
					<div id="jobs-google-maps" class="d-none d-lg-block fix-map type-freelancer">
					</div>
				</div>
			</div>
		</section>
	<?php
	} else {
		$sidebar_configs = freeio_get_freelancers_layout_configs();
		$layout_sidebar = freeio_get_freelancers_layout_sidebar();

		$top_content = freeio_get_freelancers_show_top_content();
		?>
			<section id="main-container" class="page-job-board inner layout-type-<?php echo esc_attr($layout_type); ?> <?php echo esc_attr($top_content ? 'has-filter-top':''); ?>">

				<?php freeio_render_breadcrumbs_simple(); ?>

				<?php if ( $top_content ) { ?>
					<div class="freelancers-top-content-wrapper">
				   		<?php freeio_display_top_content( $top_content ); ?>
				   	</div>
				<?php } ?>

				<?php if ( $layout_sidebar == 'main' && is_active_sidebar( $filter_sidebar ) && freeio_get_freelancers_show_offcanvas_filter() ) { ?>
				   	<div class="filter-sidebar offcanvas-filter-sidebar">
				   		<div class="offcanvas-filter-sidebar-header d-flex align-items-center">
					        <div class="title"><?php echo esc_html__('All Filters','freeio'); ?></div>
					        <span class="close-filter-sidebar ms-auto d-flex align-items-center justify-content-center"><i class="ti-close"></i></span>
					    </div>
						<div class="filter-scroll">
				   			<?php dynamic_sidebar( $filter_sidebar ); ?>
				   		</div>
			   		</div>
		   			<div class="over-dark"></div>
				<?php } ?>
				
				<section class="layout-freelancer-sidebar main-content <?php echo apply_filters('freeio_freelancer_content_class', 'container');?> inner">
					<?php freeio_before_content( $sidebar_configs ); ?>
					<div class="row">
						<?php freeio_display_sidebar_left( $sidebar_configs ); ?>

						<div id="main-content" class="col-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
							<main id="main" class="site-main layout-type-<?php echo esc_attr($layout_type); ?>" role="main">
								<?php
									echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/archive-inner', array('freelancers' => $freelancers));

									echo WP_Freeio_Template_Loader::get_template_part('loop/freelancer/pagination', array('freelancers' => $freelancers));
								?>
							</main><!-- .site-main -->
						</div><!-- .content-area -->
						
						<?php freeio_display_sidebar_right( $sidebar_configs ); ?>
					</div>
				</section>
			</section>
		<?php
	}
	get_footer();
}